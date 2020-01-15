<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Staff;
use Log;

class CheckPeers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-peers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        Log::info('Checking Peers...');
        //check staff in meeting
        $o_staff = Staff::getStaffInMeeting()->get();
        if(!$o_staff->count()) {
            Log::info('No Active Meeting');
            return;
        }

        $client = new Client();
        $headers =  [ 
                        'headers' => [
                            'Accept'     => 'application/json',
                            'Origin'     => env('APP_URL') 
                        ]   
                    ];

        //checking active peers
        try {
            $domain = $client->request('GET', env('PEER_CHECK_URL'), $headers);
            $signaling = json_decode($domain->getBody());
            $check_url = 'https://'.$signaling->domain.'/api/apikeys/'.env('PEER_API_KEY').'/clients/';            
            $active_peers = $client->request('GET', $check_url, $headers);
            $a_active_peers = json_decode($active_peers->getBody());
            print_r($a_active_peers);
        } catch(\GuzzleHttp\Exception\ClientException $ex) {
            Log::error($ex);
            return;
        }

        foreach($o_staff as $key => $staff) {
            if($staff->client_peer_id) {
                //if client_peer_id is not active, clear meeting status
                if(!in_array($staff->client_peer_id, $a_active_peers)) {
                    $staff->update(['in_meeting' => 0, 'client_peer_id' => null]);
                    Log::info('Cleared Meeting Status for staff_id = '.$staff->id);
                }
            }
        }

    }
}
