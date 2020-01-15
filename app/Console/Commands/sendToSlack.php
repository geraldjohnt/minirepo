<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class sendToSlack extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendToSlack:message {args}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send to slack the error message encountered';

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
        $args = $this->argument('args');
        $this->sendToSlackErrorMessage($args);
    }

    public function sendToSlackErrorMessage($info)
    {
        if($info) {
            \Slack::from('Mee2box')->to('CK0NLHUUS')->withIcon(':warning:')->attach([
                'fallback' => 'Warning Error !',
                'pretext' => '`Warning Logs From` *Mee2box!*',
                'author_name' => 'i2m Production',
                'color' => 'warning',
                'fields' => [
                    [
                        'title' => 'Function Error in :',
                        'value' => $info['location'],
                        'short' => false
                    ],
                    [
                        'title' => 'Warning Logs Message :',
                        'value' => $info['messages'],
                        'short' => false
                    ],
                ],
                'mrkdwn_in' => ['pretext','fields', 'text'],
            ])->send();
        }
    }
}
