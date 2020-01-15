<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ClientSlackController extends Controller
{


    public function check(Request $request) {
        
        if($request->client){
            \Slack::from('Mee2box')->to('CK0NLHUUS')->withIcon(':warning:')->attach([
                'fallback' => 'Warning Error !',
                'pretext' => '`Warning Logs From` *Mee2box!*',
                'author_name' => 'i2m Production',
                'color' => 'danger',
                'ts' => '',
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
        }
        
    }


}
