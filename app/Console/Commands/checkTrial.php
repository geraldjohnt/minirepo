<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use App\User;

class checkTrial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:trial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will check all users on trial version';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the check trial function.
     *
     * @return count
     */
    public function checkTrialUser()
    {
        $trialuser6 = [];
        $trialuser7 = [];
        foreach (User::all()->where('trial', 1) as $value) {
            $differtrial = !$this->validateDate($value['created_at']) ? 0 : (int) date_diff(date_create($value['created_at']), date_create(date('Y-m-d')))->format('%a');
            $differtrial+1 == 6 ? array_push($trialuser6, $value) : ($differtrial+1 == 7 ? array_push($trialuser7, $value) : null);
        }

        $sendEmail = [
            0 => [
                'trial' => 6,
                'Users' => $trialuser6,
            ],
            1 => [
                'trial' => 7,
                'Users' => $trialuser7,
            ],
        ];
        $this->sendEmail($sendEmail);
    }

    // First step for sending Email then passed to trials
    public function sendEmail($sendEmail)
    {
        foreach ($sendEmail as $value) {
            $this->getTrials($value);
        }
    }

    // Second Step for sending Email then select trials for 6 days and 7 days
    public function getTrials($value)
    {
        $value['trial'] == 6 && count($value['Users']) ? $this->AutoSendEmail($value, 'emails.trial_users.user_trial_before_ends', $value['trial']) : ($value['trial'] == 7 && count($value['Users']) ? $this->AutoSendEmail($value, 'emails.trial_users.user_trial_today_ends', $value['trial']) : null);
    }

    // Third Step for sending Email then AutoSend Email
    public function AutoSendEmail($value, $template, $trial)
    {
        foreach ($value['Users'] as $user) {
            $data = [
                        'title' => $trial == 6 ? 'Mee2boxトライアル終了一日前のお知らせ' : 'Mee2box無料トライアル終了のお知らせ',
                        'email' => $user['email'],
                        'full_name' => $user['last_name'].' '.$user['first_name'],
                    ];

            if($trial != 6){
                $endTrial = User::find($user['id']);
                $endTrial->deactivateUser();
            }

            Mail::send($template, ['data' => $data], function ($m) use ($data) {
                $m->to($data['email'], $data['full_name']);
                $m->bcc('support@mee2box.com','support@mee2box.com');
                $m->subject($data['title']);
            });
        }
    }

    /**
     * Execute if date is valid.
     *
     * @return bool
     */
    public function validateDate($date)
    {
        return (bool) strtotime($date);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->checkTrialUser();
    }
}
