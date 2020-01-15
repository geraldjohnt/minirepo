<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Artisan;
use App\Admin;
use App\Staff;
use App\Transformers\UserTransformer;
use Hash;
use Dingo\Api\Exception\ResourceException;

class AdminController extends ApiController
{
    /**
     * Update
     *
     * @param UpdateAdminRequest $request
     * @return admin
     */
    public function _update(Requests\UpdateAdminRequest $request)
    {
        try {
            $data = $request->all();
            $pic_url = '';

            if($request->hasFile('avatar')) {
                $a_request = [
                    'file_key' => 'avatar',
                    'file_path' => \Config::get('proto.uploads.avatar'),
                    'file_prefix' => 'avatar',
                    'current_file_url' => ''
                ];

                if($this->user->pic_url) {
                    $a_request = array_merge($a_request, ['current_file_url' => $this->user->pic_url]);
                }

                $request->merge($a_request);
                $a_file = $this->uploadFile($request, 'pic_url');

                $data = empty($a_file) ?
                    array_except($data,['avatar','role']) :
                    array_merge(array_except($data,['avatar','role']), ['pic_url' => $a_file['pic_url']]);
            } else {
                $data = array_except($data,['avatar','role', 'pic_url']);
            }

            if(!empty($data['current_password'])) {
                if(!Hash::check($data['current_password'], $this->user->password)) {
                    $error = [
                        'current_password' => 'Invalid Password.'
                    ];
                    throw new ResourceException('Invalid Inputs', $error);
                }

                $data = [
                    'password' => bcrypt($this->request->password)
                ];
                $this->request->merge($data);
            }

            $this->user->update($data);
            $user = $this->transformItem($this->user, new UserTransformer);

            return $this->response->array(compact('user'))->setStatusCode(200);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'AdminController:_update',
                    'messages' => $e
                )
            ]);
        }
    }
}
