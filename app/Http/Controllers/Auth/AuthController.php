<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Transformers\UserTransformer;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use League\Fractal;
use Artisan;

class AuthController extends Controller
{
    /**
     * Authenticate User
     *
     * @param Request $request
     * @return array
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if(! $token = JWTAuth::attempt($credentials))
            {
                return $this->response->errorUnauthorized('Unable to sign in with those credentials.');
            }
        } catch (JWTException $ex) {
            return $this->response->error('Something went wrong!', 500);
        }

        try {
            $user = JWTAuth::toUser($token);
            if($user->isActive()):
                $user->lastLogin();
                $role = $user->getRole();
                return compact('token','role','user');
            else:
                return $this->response->errorUnauthorized('Disabled user, unable to sign in.');
            endif;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'AuthController:authenticate',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Authenticate Admin
     *
     * @param Request $request
     * @return response
     */
    public function authenticateAdmin(Request $request)
    {
        try {
            $auth = $this->authenticate($request);

            if($auth['user']->isAdmin()) {
                $user = $this->transformItem($auth['user'], new UserTransformer);
                return $this->response->array(
                    array_merge( array_only($auth,['token']), compact('user') )
                )->setStatusCode(200);
            }

            return $this->response->errorUnauthorized('Unable to sign in with those credentials.');
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'AuthController:authenticateAdmin',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Authenticate Staff
     *
     * @param Request $request
     * @return response
     */
    public function authenticateStaff(Request $request)
    {
        try {
            $auth = $this->authenticate($request);

            if($auth['user']->isStaff()) {
                $user = $this->transformItem($auth['user'], new UserTransformer);
                return $this->response->array(
                    array_merge( array_only($auth,['token']), compact('user') )
                )->setStatusCode(200);
            }

            return $this->response->errorUnauthorized('Unable to sign in with those credentials.');
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'AuthController:authenticateStaff',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Authenticate Admin or Staff
     *
     * @param Request $request
     * @return response
     */
    public function authenticateAdminOrStaff(Request $request)
    {
        try {
            $auth = $this->authenticate($request);
            $user = $this->transformItem($auth['user'], new UserTransformer);

            return $this->response->array(
                    array_merge( array_except($auth,['user']), compact('user') )
            )->setStatusCode(200);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'AuthController:authenticateAdminOrStaff',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Get all users
     *
     * @param  void
     * @return array Users
     */
    public function index(Request $request)
    {
        try {
            $per_page = $request->input('per_page');
            $per_page = !empty($per_page) ? $per_page : \Config::get('proto.defaults.pagination');
            $result = $this->transformPaginatedCollection(User::paginate($per_page), new UserTransformer, 'users');

            return $this->response->array($result)->setStatusCode(200);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'AuthController:index',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Get user
     *
     * @param  void
     * @return User
     */
    public function getUser()
    {
        try {
            $user = JWTAuth::parseToken()->toUser();
            
            if(!$user) {
                return $this->response->errorNotFound('User not found');
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $ex) {
            return $this->response->errorUnauthorized('Token is invalid');
        } catch ( \Tymon\JWTAuth\Exceptions\TokenExpiredException $ex) {
            return $this->response->errorUnauthorized('Token has expired');
        } catch ( \Tymon\JWTAuth\Exceptions\TokenBlackListedException $ex) {
            return $this->response->errorUnauthorized('Token is blacklisted');
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'AuthController:getUser',
                    'messages' => $e
                )
            ]);
        }

        return $user;
    }

    /**
     * Get user
     *
     * @param  void
     * @return User
     */
    public function show(Request $request)
    {
        try {
            $data = $request->only('role');
            $user = $this->getUser();
            $role = $user->getRole();
            $user = $this->transformItem($user, new UserTransformer);
            $admin = $this->getUser()->admin;
            if (!is_null($data['role']) && $data['role'] != $role ) {
                return $this->response->errorUnauthorized("Invalid Credentials");
            }

            // return $this->response->array(compact('role','user'))->setStatusCode(200);
            return $this->response->array(compact('role','user','admin'))->setStatusCode(200);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'AuthController:show',
                    'messages' => $e
                )
            ]);
        }
    }


    /**
     * Refereshes the token
     *
     * @param  void
     * @return JW
     */
    public function getToken()
    {
        $token = JWTAuth::getToken();

        if(!$token) {
            return $this->response->errorUnauthorized("Token is invalid");
        }

        try {
            $refreshedToken = JWTAuth::refresh($token);
        } catch (JWTException $ex) {
            $this->response->error('Something went wrong');
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'AuthController:getToken',
                    'messages' => $e
                )
            ]);
        }

        return $this->response->array(compact('refreshedToken'));
    }
}
