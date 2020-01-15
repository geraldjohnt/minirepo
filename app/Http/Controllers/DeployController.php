<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
class DeployController extends Controller
{


    public function deploy(Request $request){
           $process = new Process('sh /var/www/html/dev.webrtc/deploy.sh');
           $process->run(function ($type, $buffer) {
                echo $buffer;
            });           
    
    }


     public function testDeploy(){
    	$process = new Process('sh /var/www/html/dev.webrtc/deploy.sh');
            $process->run(function ($type, $buffer) {
                echo $buffer;
         });

      }
    //  
    // public function deploy(Request $request)
    // {
    //     $githubPayload = $request->getContent();
    //     $githubHash = $request->header('X-Hub-Signature');
 
    //     $localToken = config('app.deploy_secret');
    //     $localHash = 'sha1=' . hash_hmac('sha1', $githubPayload, $localToken, false);
 
    //     if (hash_equals($githubHash, $localHash)) {
    //         $root_path = base_path();

    //         // echo 'cd'.$root_path.'; ./deploy.sh';
    //         // $process = new Process('sh /var/www/html/pa/deploy.sh');
    //         // 
    //         // 
    //         // 
    //         $process = new Process('cd ' . $root_path . '; ./deploy.sh');
    //         $process->run(function ($type, $buffer) {
    //             echo $buffer;
    //         });

    //     }
    // }
}
