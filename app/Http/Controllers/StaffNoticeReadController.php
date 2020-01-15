<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Staff;
use App\StaffNotice;
use Artisan;
use DB;
use App\Http\Requests\StaffNoticeRequest;

class StaffNoticeReadController extends Controller
{
    //

    public function update(Request $request){
        try {
            $notices = DB::table("staff_notices")->where("id",[$request->id])->update(['read' => 1]);
            return $notices;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffNoticeReadController:update',
                    'messages' => $e
                )
            ]);
        }
    }

    public function check(Request $request) {
        if ($request->statusCode == 0) {
            \Slack::from('Mee2box')->to('CK0NLHUUS')->withIcon(':warning:')->attach([
                'fallback' => 'False alarm !',
                'pretext' => ' `False alarm From` *Mee2box!*',
                'color' => 'danger',
                'fields' => [
                    [
                        'title' => 'Function Error in :',
                        'value' => $request->location,
                        'short' => false 
                    ],
                    [
                        'title' => 'Encountered Error By :',
                        'value' => $request->userFname.' '.$request->userLname,
                        'short' => true
                    ],
                    [
                        'title' => 'Email Account :',
                        'value' => $request->userEmail,
                        'short' => true 
                    ],
                    [
                        'title' => 'Operating System :',
                        'value' => $request->specsOS,
                        'short' => true 
                    ],
                    [
                        'title' => 'Operating System Version :',
                        'value' => $request->specsOSversion,
                        'short' => true 
                    ],
                    [
                        'title' => 'Browser :',
                        'value' => $request->specsBrowser,
                        'short' => true 
                    ],
                    [
                        'title' => 'Browser Version:',
                        'value' => $request->specsBrowserversion,
                        'short' => true 
                    ],
                ],
                'mrkdwn_in' => ['pretext','fields', 'text'],
            ])->send();
        } else {
            if ($request->userAuth){
                \Slack::from('Mee2box')->to('CK0NLHUUS')->withIcon(':warning:')->attach([
                    'fallback' => 'Warning Error !',
                    'pretext' => '`Warning Logs From` *Mee2box!*',
                    'author_name' => 'i2m Production',
                    'color' => 'danger',
                    'ts' => 123456789,
                    'fields' => [
                        [
                            'title' => 'Function Error in :',
                            'value' => $request->location,
                            'short' => false 
                        ],
                        [
                            'title' => 'Alert Message From the Page :',
                            'value' => $request->statusPageMsg,
                            'short' => false 
                        ],
                        // [
                        //     'title' => 'Warning Error :',
                        //     'value' => $request->statusText,
                        //     'short' => false
                        // ],
                        [
                            'title' => 'Warning Logs Message :',
                            'value' => $request->statusMsg,
                            'short' => false 
                        ],
                        [
                            'title' => 'Encountered Error By :',
                            'value' => $request->userFname.' '.$request->userLname,
                            'short' => true
                        ],
                        [
                            'title' => 'Email Account :',
                            'value' => $request->userEmail,
                            'short' => true 
                        ],
                        [
                            'title' => 'Operating System :',
                            'value' => $request->specsOS,
                            'short' => true 
                        ],
                        [
                            'title' => 'Operating System Version :',
                            'value' => $request->specsOSversion,
                            'short' => true 
                        ],
                        [
                            'title' => 'Browser :',
                            'value' => $request->specsBrowser,
                            'short' => true 
                        ],
                        [
                            'title' => 'Browser Version:',
                            'value' => $request->specsBrowserversion,
                            'short' => true 
                        ],
                    ],
                    
                    // 'mrkdwn_in' => ['pretext','fields', 'text'],
                ])->send();
                
            }else{
               
                $ve = '{"Fail":"Fail","data":$request}';
                return $ver;
            }
            
        }
        
    }
    
}
