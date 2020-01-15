<?php

namespace App\Http\Controllers;

use Mail;
use Artisan;
use App\User;
use App\Http\Requests;
use Illuminate\Http\Request;
use Maknz\Slack\Client;

class EmailController extends Controller
{
	/**
     * Send Email Feedback
     *
     * @param Requests\EmailFeedbackRequest
     * @return Sent Response
     */
    public function sendFeedback(Requests\EmailFeedbackRequest $request) {
        try {
            $data = $request->all();
            $data['user_name'] = $request->user() ?
                                $request->user()->first_name.' '.$request->user()->last_name :
                                $request->name;
            $data['user_email'] = $request->user() ?
                                $request->user()->email :
                                $request->email;
            $data['feedback'] = \Config::get('proto.emails.feedback');

            Mail::send('emails.feedback', ['data' => $data], function ($m) use ($data) {
                $m->to($data['feedback']['email'], $data['feedback']['name'])
                    //->cc($data['feedback']['cc'][0]['email'], $data['feedback']['cc'][0]['name'])
                    ->subject('User Feedback');
            });

            return $this->response->array(['message' => 'Thank you for the feedback!'])->setStatusCode(200);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'EmailController:sendFeedback',
                    'messages' => $e
                )
            ]);
        }
    }

   

    /**
     * Send Email Feedback
     *
     * @param Requests\EmailFeedbackRequest
     * @return Sent Response
     */
    public function sendResetPassLink(User $user, $token) {
        try {
            $data = [
                'email' => $user->email,
                'full_name' => $user->last_name.' '.$user->first_name,
                'reset_pass_link' => env('APP_URL').'/#/reset-password/'.$token,
                'forgot_password' => \Config::get('proto.emails.forgot_password')
            ];

            Mail::send('emails.forgot_password.to_user', ['data' => $data], function ($m) use ($data) {
                $m->from($data['forgot_password']['email'],$data['forgot_password']['name'])
                    ->to($data['email'], $data['full_name'])
                    ->subject($data['forgot_password']['title']);
            });

            return $this->response->array(['message' => 'Sent Reset Password Link!'])->setStatusCode(200);
        } catch(Exception $ex) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'EmailController:sendResetPassLink',
                    'messages' => $ex
                )
            ]);
        }
    }

    /**
     * Send User Credentials
     */
    public function sendCredentials(Requests\CredentialsRequest $request) {
        try {
            $data = $request->all();
            $data['credentials'] = \Config::get('proto.emails.credentials');
            $selectedView = '';

            if($data['trial'] == '1') {
                $selectedView = 'emails.credentials.to_user_trial';
                $data['subject'] = 'Mee2box無料トライアルへようこそ';
            } else {
                $selectedView = 'emails.credentials.to_user_paid';
                $data['subject'] = 'Mee2box登録完了のお知らせ';
            }

        Mail::send($selectedView, ['data' => $data], function ($m) use ($data) {
            $m->to($data['email'], $data['last_name']);
            $m->bcc('yes_mee2_ml@yes-inc.co.jp','yes_mee2_ml@yes-inc.co.jp');
            // $m->bcc('co@i2m.jp','co@i2m.jp');
            $m->subject($data['subject']);
        });

            return $this->response->array(['message' => 'Sent User Credentials!'])->setStatusCode(200);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'EmailController:sendCredentials',
                    'messages' => $e
                )
            ]);
        }
    }


     /**
     * Send User Notification From Mee2box Homepage 
     */

     public function sendNotification(Request $req){
        
        try {

            $request_type = '';
        
            if($req->status === 'document'){
                $request_type = ' 資料請求 ';
            }
            else if($req->status === 'trial'){
                $request_type = ' 7日間のお試し ';
            }
            else if($req->status === 'apply'){
                $request_type = ' 申し込み ';
            }
            else{
                $request_type = ' 無料トライアル申し込み ';
            }

            $data = [
                'email'         => $req->email,
                'client_name'   => $req->client_name,
                'client_phonetic' => $req->client_phonetic,
                'number'        => $req->contact,
                'inquiry_txt'   => isset($req->inquiry_txt) ? $req->inquiry_txt : null,
                'inquiry_type'  => $request_type,
                'company_name'  => $req->company
            ];

            $this->sendToSlack($data);

            Mail::send('emails.homepage_notification',['data' => $data], function($msg) use($data) {
                $msg->to($data['email']);
                $msg->bcc('yes_mee2_ml@yes-inc.co.jp','yes_mee2_ml@yes-inc.co.jp');
                // $msg->bcc('co@i2m.jp','co@i2m.jp');
                $msg->subject('お問い合わせありがとうございました。／Mee2box YES株式会社');
            });

        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'EmailController:sendNotification',
                    'messages' => $e
                )
            ]);
        }


    }
 

    public function sendToSlack($data){

        try {
        $client = new Client('https://hooks.slack.com/services/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');

        $client->withIcon(':warning:')->
        attach([
                'fallback' => 'Warning !',
                'pretext' => '`Notice Logs From` *Mee2box!*',
                'author_name' => 'Google Spreadsheet',
                'color' => 'good',
                'fields' => [
                    [
                    
                        'title' => '',
                        'value' => "[ ".$data['company_name']." ] からお問い合わせがありました。",
                        'short' => false 
                    ],
                    [
                    
                        'title' => '',
                        'value' => "[".$data['inquiry_type']." ] スプレッドシートを確認し、返信してください。",
                        'short' => false 
                    ],
                    [
                    
                        'title' => '',
                        'value' => "https://docs.google.com/spreadsheets/d/1XXs0Ya0qCRHC_cSOLZtVQfYTRU3yyp6ki7e7toEnE5o/edit?usp=sharing",
                        'short' => false 
                    ],

                ],
                 'mrkdwn_in' => ['pretext','fields', 'text'],
                
        ])->send();


        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'EmailController:sendToSlack',
                    'messages' => $e
                )
            ]);
        }


    }




}
