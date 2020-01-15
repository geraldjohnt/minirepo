<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Artisan;
use Password;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Dingo\Api\Exception\ResourceException;
use App\Http\Controllers\EmailController;

class PasswordController extends Controller
{
    use ResetsPasswords;

    protected $mail;

    /**
     * @param Request
     * @return void
     */
    public function __construct(EmailController $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Forgot Password
     *
     * @param Request $request
     * @return response
     */
    public function forgotPass(Request $request)
    {
        try {
            $user = Password::broker()->getUser(
                $request->only('email')
            );

            if (is_null($user)) {
                return $this->response->errorUnauthorized('正しいメールアドレスを入力してください');
            }

            $token = Hash::make($user->id);
            $user->password_reset_token = $token;
            $user->save();

            $this->mail->sendResetPassLink($user, base64_encode($token));

            return $this->response->array([
                'message' => 'パスワードの再設定ページへのリンクを登録メールアドレスに送信しました'
            ])->setStatusCode(200);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'PasswordController:forgotPass',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Reset Password
     *
     * @param Request $request
     * @return response
     */
    public function resetPass(Request $request)
    {
        try {
            $data = $request->all();
            if (isset($data['token'])) {
                $user = User::where('password_reset_token', base64_decode($data['token']))->first();
                if (is_null($user)) {
                    return $this->response->errorUnauthorized('このリンクは既に無効です');
                }

                if(isset($data['action']) && $data['action'] == 'validate') {
                    return $this->response->array(['valid' => true])->setStatusCode(200);
                } else {
                    $validator = Validator::make($request->all(),[
                                        'password' => 'required|confirmed|min:6|max:99'
                                    ],[
                                        'password.required' => '入力必須項目です',
                                        'password.confirmed' => '入力内容が異なっています。正しいパスワードを入力してください',
                                        'password.min' => 'パスワードは6字以上で入力してください。',
                                        'password.max' => 'パスワードは99字以内で入力してください。'
                                    ]);
                    if ($validator->fails()) {
                        throw new ResourceException('入力内容に不備があります', $validator->getMessageBag());
                    } else {
                        $this->resetPassword($user, $data['password']);
                        $user->remember_token = null;
                        $user->password_reset_token = null;
                        $user->save();

                        return $this->response->array(['message' => 'パスワードの再設定が完了しました'])->setStatusCode(200);
                    }
                }
            }

            return $this->response->errorUnauthorized('このリンクは既に無効です');
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'PasswordController:resetPass',
                    'messages' => $e
                )
            ]);
        }
    }
}
