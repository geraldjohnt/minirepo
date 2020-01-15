<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Maknz\Slack\Client;

class checkUserLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:login';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the mee2box users is not login for 3 days';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->checkUser() > 0 ? $this->sendToSlack() : null;
    }



    /**
     * Execute the check user login function.
     *
     * @return count
     */
    public function checkUser(){
        $userNotLogin = [];

        foreach (User::all() as $value) {
            $differInt = !$this->validateDate($value['last_login']) ? 0 : (int)date_diff(date_create($value['last_login']),date_create(date('Y-m-d H:i:s')))->format("%a");
            $differInt > 2 ? array_push($userNotLogin, $value) : null;
        }
        return count($userNotLogin);
    }


    /**
     * Execute if date is valid
     *
     * @return boolean
     */
    public function validateDate($date){
        return (bool)strtotime($date);
    }


    /**
     * Execute send to slack if has found
     *
     * @return void
     */
    public function sendToSlack(){
        $client = new Client('https://hooks.slack.com/services/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');

        $client->withIcon(':warning:')->
        attach([
                'fallback' => 'Warning !',
                'pretext' => '`Notice Logs From` *Mee2box!*',
                'author_name' => 'i2m Production',
                'color' => 'warning',
                'fields' => [
                    [ 
                    
                        'title' => 'Alert Message From Mee2box :',
                        'value' => "3日以上Mee2boxにログインしていないユーザーがいます。ユーザーを確認し、フォローしてください。",
                        'short' => false 
                    ]
                ],
                 'mrkdwn_in' => ['pretext','fields', 'text'],
                
        ])->send();

    }
}
