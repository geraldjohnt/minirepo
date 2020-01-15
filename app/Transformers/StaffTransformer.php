<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Staff;
use Storage;

class StaffTransformer extends TransformerAbstract
{

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(Staff $staff)
    {   
       $firstname = !empty($staff->user->first_name)?$staff->user->first_name : null;
       $lastname = !empty($staff->user->last_name)?$staff->user->last_name : null;
        return [
            'staff_id'          => $staff->id,
            'title'             => $staff->title,
            'in_meeting'        => $staff->in_meeting,
            'client_peer_id'    => $staff->client_peer_id,
            'profile'      => [
                'first_name'    => !empty($staff->user->firstname)?$staff->user->first_name : null,
                'last_name'     => !empty($staff->user->last_name)?$staff->user->last_name : null,
                'email'         => !empty($staff->user->email)? $staff->user->email : null,
                'company'       => !empty($staff->user->company)?$staff->user->company : null,
                'department'    => !empty($staff->user->department)?$staff->user->department:null,
                'post'          => !empty($staff->user->post)?$staff->user->post:null,
                'pic_url'       => empty($staff->user->pic_url) ? '' : env('APP_URL').Storage::url($staff->user->pic_url),
                'phone_number'  => !empty($staff->user->phone_number)?$staff->user->phone_number:null,
                'full_name' => $firstname . ' '. $lastname,
                'trial'     => isset($staff->user->trial) && $staff->user->trial == 1 ? "トライアル" : "本登録" ,
                't_stat'    => isset($staff->user->trial) ? $staff->user->trial : 0,
                'active'    => isset($staff->user->status) ? $staff->user->status : 0,
                't_checked' => isset($staff->user->trial) && $staff->user->trial == 1 ? true : false ,
                'differ'=>  empty($staff->user->last_login) ? "0 日前" : date_diff(date_create($staff->user->last_login),date_create(date('Y-m-d H:i:s')))->format("%a"). " 日前",
                'differInt'=>  empty($staff->user->last_login) ? 0 : (int)date_diff(date_create($staff->user->last_login),date_create(date('Y-m-d H:i:s')))->format("%a"),
                'last_connection'=>!empty($staff->negotations) ? date_create($staff->negotations->last()['updated_at'])->format("Y/m/d H:i") : '',
                'last_login'=> empty($staff->user->last_login) ?  null : $staff->user->last_login ? date_format(date_create($staff->user->last_login),"Y/m/d H:i") : '',
                'connections'=> !empty($staff->negotations) ? $staff->negotations()->orderBy('id','DESC')->take(5)->get() : null,
                'trial_start'=> empty($staff->user->trial_start) ?  null : $staff->user->trial_start ? date_format(date_create($staff->user->trial_start),"Y/m/d") : '',
                'end_of_trial'=> empty($staff->user->trial_start) ? null : date("Y/m/d",strtotime('+7 days',strtotime($staff->user->trial_start))),
                'registered_date'=> empty($staff->user->registered_date) ?  null : $staff->user->registered_date ? date_format(date_create($staff->user->registered_date),"Y/m/d") : '',
                'created_at'=> empty($staff->user->created_at) ?  null : $staff->user->created_at ? date_format(date_create($staff->user->created_at),"Y/m/d H:i:s") : '',
            ]
        ];


    }

}
