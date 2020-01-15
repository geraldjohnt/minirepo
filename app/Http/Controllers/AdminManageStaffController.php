<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Artisan;
use App\User;
use App\Staff;
use App\Http\Requests;
use App\Transformers;
use App\Http\Requests\StaffRequest;
use App\Transformers\StaffTransformer;

class AdminManageStaffController extends ApiController
{
    
    /**
     * @param Request
     * @return void
     */
    public function __construct(StaffRequest $request)
    {
        try {
            parent::__construct($request);
            $this->model = new Staff;
            $this->transformer = new StaffTransformer;
            $this->key = 'staff';
            $this->request = $request;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'AdminManageStaffController:_construct',
                    'messages' => $e
                )
            ]);
        }
    }
    
    public function getLogin(){
        try {
            return Staff::getAllStaffNegotation();
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'AdminManageStaffController:getLogin',
                    'messages' => $e
                )
            ]);
        }
    }
    
    public function getProcess(){
        try {
            $staff_id = $this->request->staff;

            $staff = Staff::find($staff_id);
            $neg_del = $staff->negotations()->delete();
            $doc_del = $staff->documents()->delete();
            $fol_del = $staff->folders()->delete();
            $no_del = $staff->notices()->delete();
            $user_del = $staff->user()->delete();
            $st_del = DB::table('staff')->where('id', $staff_id)->delete();
            return ['id' => $staff_id, 'delete' => ['user' => $user_del, 'negotiations' => $neg_del, 'documents' => $doc_del, 'folders' => $fol_del, 'notices' => $no_del, 'staff' => $st_del]];
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'AdminManageStaffController:getProcess',
                    'messages' => $e
                )
            ]);
        }
    }
    
    public function setStatus(){
        try {
            //DB::enableQueryLog();
            $staff_id = $this->request->staff_id;
            $status = $this->request->status;
    //        $stat = DB::table('users')
    //            ->where('id', $staff_id)
    //            ->update(['status' => $status]);
    //
            $staff = Staff::find($staff_id);
            if($staff):
                $staff->user->status = $status;
                $stat = $staff->user->save();
            else:
                $stat = "User for ". $staff_id. " not found";
            endif;

            //return ['id' => $staff_id, 'enabled' => $status, 'return' => $stat, 'query' => DB::getQueryLog()];
            return ['id' => $staff_id, 'enabled' => $status, 'return' => $stat];
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'AdminManageStaffController:setStatus',
                    'messages' => $e
                )
            ]);
        }
    }
    
    /**
     * Add an item
     *
     * @param void
     * @return model item
     */
    public function store()
    {
        try {
            /**
             * Check if email already exists and that they are not active
             */

            $user = DB::table('users')
                        ->where('email', $this->request->email)
                        //->where('deleted_at', NULL)
                        ->count();

            if ($user > 0) {
                return $this->response->array(['message' => 'Email already exists.'])->setStatusCode(404);
            }

            $this->model = new User;

            $this->request->offsetUnset('pic_url');
            //TODO: to be decided to change default staff password
            $data = [
                'return_created' => 1,
                'password' => bcrypt($this->request->password),
                'role' => \Config::get('proto.role.staff')
            ];

            $data['files_attached'][] = [
                'file_key' => 'avatar',
                'file_path' => \Config::get('proto.uploads.avatar'),
                'file_prefix' => 'avatar',
                'field_key' => 'pic_url'
            ];

            $this->request->merge($data);
            $result = parent::store();

            if($result->staff) {
                $data = $this->request->only(['title']);
                $result->staff->update($data);
            }

            $trialUser = User::where('email', $this->request->email)->first();
            $trialUser->registered_date = date_create('now', timezone_open('Asia/Tokyo'))->format('Y-m-d H:i:s');
            $trialOn = User::where('email', $this->request->email)->first();
            $trialOn->trial_start = date_create('now', timezone_open('Asia/Tokyo'))->format('Y-m-d H:i:s');
            $trialUser->trial == 0 ? $trialUser->save() : $trialOn->save();


            return $this->response->created();
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'AdminManageStaffController:store',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Add an item
     *
     * @param void
     * @return model item
     */
    public function update($id)
    {
        try {
            $staff = $this->model->findOrFail($id);
            $user_id = $staff->user_id;

            $this->model = new User;
            $this->request->offsetUnset('pic_url');
            $this->request->offsetUnset('role');
            //TODO: to be decided to change default staff password
            //$data = [
            //    'return_updated' => 1,
            //    'password' => bcrypt($this->request->password)
            //];
	
            $data['return_updated'] = 1;
            if ($this->request->password) {
                $data['password'] = bcrypt($this->request->password);
            }
            $data['trial'] = $this->request->trial == 'true' ? 1 : 0;
            $data['status'] = $this->request->active == 'true' ? 1 : 0;
            $data['files_attached'][] = [
                'file_key' => 'avatar',
                'file_path' => \Config::get('proto.uploads.avatar'),
                'file_prefix' => 'avatar',
                'field_key' => 'pic_url'
            ];

            $PreviousTrial = Staff::findOrFail($id)->user->trial;

            $this->request->merge($data);
            parent::update($user_id);

            $staff_data = $this->request->only(['title']);
            $staff->update($staff_data);


            $trialStaff = Staff::findorFail($id);            
            if(isset($trialStaff->user->registered_date)){
            } else if($PreviousTrial == 1 && $trialStaff->user->trial == 0){
                $trialStaff->user->registered_date = date_create('now', timezone_open('Asia/Tokyo'))->format('Y-m-d H:i:s');
                $trialStaff->user->save();
            }

            $result = $this->transformItem($staff, $this->transformer);
            return $this->response->array([$this->key => $result])->setStatusCode(200);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'AdminManageStaffController:update',
                    'messages' => $e
                )
            ]);
        }
    }
}
