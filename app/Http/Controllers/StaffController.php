<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Artisan;
use App\Admin;
use App\Staff;
use App\Transformers;
use Hash;
use Dingo\Api\Exception\ResourceException;
use File;
use Response;
use Log;
use App\StaffNegotation;

class StaffController extends ApiController
{
    /**
     * Get Staff
     *
     * @param void
     * @return staff
     */
    public function getStaff()
    {
        try {
            return $this->transformItem($this->user->staff, new Transformers\StaffTransformer, 'staff');
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:getStaff',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Check Peer
     *
     * @param void
     * @return staff
     */
    public function checkPeerId(Request $request)
    {
        try {
            $peer_id = $request->input('peer_id');
            if($peer_id) {
                $staff = Staff::getStaffWithPeerId($peer_id)->get();
                if(!$staff->count()) {
                    return $this->response->array(['message' => 'Peer Key is Free']);
                }
            }
            return $this->response->errorForbidden();
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:checkPeerId',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Start Meeting
     *
     * @param void
     * @return staff
     */
    public function startMeeting(Request $request)
    {
        try {
            $peer_id = $request->input('peer_id');
            $this->user->staff()->update(['in_meeting' => 1,'client_peer_id' => $peer_id]);
            return $this->transformItem($this->user->staff, new Transformers\StaffTransformer, 'staff');
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:startMeeting',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Stop Meeting
     *
     * @param void
     * @return staff
     */
    public function stopMeeting()
    {
        try {
            $this->user->staff->update(['in_meeting' => 0, 'client_peer_id' => null]);
            return $this->transformItem($this->user->staff, new Transformers\StaffTransformer, 'staff');
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:stopMeeting',
                    'messages' => $e
                )
            ]);
        }
    }

     /**
     * Update
     *
     * @param UpdateStaffRequest $request
     * @return staff
     */
    public function _update(Requests\UpdateStaffRequest $request)
    {
        try {
            $data = $request->all();
            $pic_url = '';

            if ($this->request->password) {
                    $data['password'] = bcrypt($this->request->password);
            } else{
                    $data['password'] = $this->user->password;
            }

            if($request->hasFile('avatar')) {
                $a_request = [
                    'file_key' => 'avatar',
                    'file_path' => \Config::get('proto.uploads.avatar'),
                    'file_prefix' => 'avatar',
                    'current_file_url' => ''
                ];

                if($this->user->pic_url) {
                    $a_request = array_merge($a_request, ['current_file_url' => $this->user->pic_url]);
                }

                $request->merge($a_request);
                $a_file = $this->uploadFile($request, 'pic_url');

                $data = empty($a_file) ?
                    array_except($data,['avatar','role']) :
                    array_merge(array_except($data,['avatar','role']), ['pic_url' => $a_file['pic_url']]);
            } else {
                $data = array_except($data,['avatar','role', 'pic_url']);
            }

            if(!empty($data['current_password'])) {
                if(!Hash::check($data['current_password'], $this->user->password)) {
                    $error = [
                        'current_password' => 'Invalid Password.'
                    ];
                    throw new ResourceException('Invalid Inputs', $error);
                }

                $this->request->merge($data);
            }

            $this->user->update($data);
            $user = $this->transformItem($this->user, new Transformers\UserTransformer);

            return $this->response->array(compact('user'))->setStatusCode(200);
        } catch(ResourceException $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:_update',
                    'messages' => $e
                )
            ]);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:_update',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Create shared notes
     *
     * @param Request
     * @return document
     */
    public function createNotes(Request $request)
    {
        try {
            $staff_id = $request->input('staff_id');
            $notes = $request->input('notes');

            $storagePath = storage_path(sprintf('app/public/%s', \Config::get('proto.uploads.staff_notes')));
            $file_name = 'sharednotes-'.date('m-d-Y-his').'.dat';
            $myfile = fopen($storagePath.'/'.$file_name, "wb") or die("Unable to open file!");
            $content = encrypt(urldecode($notes));
            fwrite($myfile,$content,strlen($content));

            $file_size = fstat($myfile)['size'];
            fclose($myfile);

            return $this->response->array(['file_name' => $file_name, 'file_size' => $file_size])->setStatusCode(200);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:createNotes',
                    'messages' => $e
                )
            ]);
        }
    }

   // m2b-81

     /**
     * Get Audio Screen Names
     *
     * @param Request
     * @return videos
     */
    public function getAudioScreenNames(Request $request)
    {
        try {
            $connect_id = $request->input('connect_id');

            $date = date('m-d-Y-his');
            $file_screen = sprintf('%s', \Config::get('proto.uploads.staff_vid_temp')).'/sharedvideo-'.$connect_id.'-'.$date.'.mkv';                            
            $file_audio = sprintf('%s', \Config::get('proto.uploads.staff_vid_temp')).'/sharedaudio-'.$connect_id.'-'.$date.'.webm';
            $file_videos = sprintf('%s', \Config::get('proto.uploads.staff_videos')).'/sharedvideos-'.$connect_id.'-'.$date.'.mp4';  

            return $this->response->array(['file_screen' => $file_screen, 'file_videos' => $file_videos, 'file_link' => 'storage/app/public/'.$file_videos, 'file_audio' => $file_audio])->setStatusCode(200);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:getAudioScreenNames',
                    'messages' => $e
                )
            ]);
        }
    }    


  /**
     * Create Screen Files
     *
     * @param Request
     * @return videos
     */
    public function createScreenFiles(Request $request)
    {
        try {
            Log::info('createScreenFiles'); 

            $storagePath = storage_path(sprintf('app/public/%s', \Config::get('proto.uploads.staff_videos')));
            if(!File::exists($storagePath)) {
                File::makeDirectory($storagePath,0777);
            }

            $videosTemp = storage_path(sprintf('app/public/%s', \Config::get('proto.uploads.staff_vid_temp')));
            if(!File::exists($videosTemp)) {
                File::makeDirectory($videosTemp,0777);
            }        
            
            $screen_name = $request->input('screen_name');                
            $screen = $videosTemp.'/'.explode('/temp/',$screen_name)[1];  

            Log::info('screen: '. $screen); 
                        
            // save screen blob to video file
            if( $request->file('screen_blob') ) {  

                Log::info('screen blob exist');
                $blob_file = $request->file('screen_blob');        
                $videos_save = $storagePath.'/'.explode('/videos/',$screen_name)[1];  
                File::move($blob_file, $videos_save);
                Log::info('videos_save: '. $videos_save); 

            }
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:createScreenFiles',
                    'messages' => $e
                )
            ]);
        }
    } 

 /**
     * Create Audio Files
     *
     * @param Request
     * @return videos
     */
    public function createAudioFiles(Request $request)
    {
        try {
            Log::info('------------ createAudioFiles - start'); 
            Log::info('audio_name: ' . $request->input('audio_name')); 

            $storagePath = storage_path(sprintf('app/public/%s', \Config::get('proto.uploads.staff_videos')));
            $videosTemp = storage_path(sprintf('app/public/%s', \Config::get('proto.uploads.staff_vid_temp')));

            Log::info('storagePath: ' . $storagePath); 
            Log::info('videosTemp: ' . $videosTemp);         
            
            $audio_name = $request->input('audio_name');                
            $audio = $videosTemp.'/'.explode('/temp/',$audio_name)[1];   
            
            Log::info('audio: '. $audio); 
            
            // save audio blob to audio file
            if( $request->file('audio_blob') ) {

                Log::info('audio blob exist');
                $blob_file = $request->file('audio_blob');        
                $audios_save = $storagePath.'/'.explode('/videos/',$audio_name)[1];  
                Log::info('audios_save: '. $audios_save); 
                File::move($blob_file, $audios_save);

            } else Log::info('blob does not exist');
            Log::info('------------ createAudioFiles - end'); 
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:createAudioFiles',
                    'messages' => $e
                )
            ]);
        }            
    }     


   /**
     * Create Audio Screen Files
     *
     * @param Request
     * @return videos
     */
    public function mergeAudioScreenFiles(Request $request)
    {
        try {
            $storagePath = storage_path(sprintf('app/public/%s', \Config::get('proto.uploads.staff_videos')));
            $videosTemp = storage_path(sprintf('app/public/%s', \Config::get('proto.uploads.staff_vid_temp')));
            
            $screen_name = $request->input('screen_name');        
            $audio_name = $request->input('audio_name');        
            $videos_name = $request->input('videos_name');    
            
            $screen = $videosTemp.'/'.explode('/temp/',$screen_name)[1];  
            $audio = $videosTemp.'/'.explode('/temp/',$audio_name)[1];    
            $videos = $storagePath.'/'.explode('/videos/',$videos_name)[1];  
            
            $text_file = str_replace(".mp4",".txt",$videos_name);
            $text_file = $storagePath.explode('/videos',$text_file)[1];             

            // if video and audio files exist - then merge files into one video file in mp4 format
            if( File::exists($screen) && File::exists($audio) ) {

                $runApp = 'ionice -c 2 -n 7 nice -n 19 ffmpeg -y -progress ' . $text_file . ' -i ' . $screen . ' -i ' . $audio . ' -vcodec libx264 -shortest -preset ultrafast ' . $videos . ' &';

                if (DIRECTORY_SEPARATOR == '\\') {
                    $apid = $this->getPidInWindows($runApp, $storagePath, "r");
                } else if (DIRECTORY_SEPARATOR == '/') {
                    // unix, linux, mac
                    $apid = $this->getPidInLinux($runApp, $storagePath);
                }
           }       
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:mergeAudioScreenFiles',
                    'messages' => $e
                )
            ]);
        }            
    }     

        
    /**
     * Decrypt Dat File 
     * @param $file
     * @return bool|string
     */

    public function decryptDatUrl(Request $request) {    
        try {
            $url = $request->input('url');
            $storagePath = storage_path(sprintf('app/public/%s', \Config::get('proto.uploads.staff_videos')));   
            $chk_vid = $storagePath.explode('/videos',$url)[1]; 
            $chk_dat = str_replace('.mp4','.dat',$chk_vid);

            if(File::exists($chk_dat)) {
                $uploadsPath = \Config::get('proto.uploads.staff_videos');   
                $datfile = $uploadsPath.explode('/videos',$chk_dat)[1];
                $vidfile = $uploadsPath.explode('/videos',$url)[1]; 
                
                $decryptedContent = decrypt($this->storage->get($datfile));
                $this->storage->put($vidfile,$decryptedContent);  

                while( !File::exists($chk_vid) ) {
                    sleep(2);
                }                

            }

            $success = false;
            if(File::exists($chk_vid)) {
                $success = true;
            }

            return $this->response->array(['success' => $success])->setStatusCode(200); 
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:decryptDatUrl',
                    'messages' => $e
                )
            ]);
        }             
    }

        
    /**
     * Remove Video File/Dat
     * @param $file
     * @return bool|string
     */

    public function removeVideoFile(Request $request) {    
        try { 
            $url = $request->input('url');
            $all = $request->input('opt');
            $storagePath = storage_path(sprintf('app/public/%s', \Config::get('proto.uploads.staff_videos')));   
            $file = $storagePath.explode('/videos',$url)[1]; 

            $datfile = $text_file = str_replace(".mp4",".dat",$file);
            if(File::exists($datfile)) {
                sleep(1);            
                if($all == 'YES') unlink($datfile);  
                if(File::exists($file)) unlink($file);                        
            }
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:removeVideoFile',
                    'messages' => $e
                )
            ]);
        } 
    }

    /**
     * Get check video url 
     * @param $file
     * @return bool|string
     */

    public function checkVideoUrl(Request $request) {
        try {
            $file = $request->input('url'); 
            $video_exist = false;        
            $text_exist = false;
            $remove_flag = 0;
            $percent = 0;  
            $process = '';      
            $size = 0;        
            $dura = 0;          

            $storagePath = storage_path(sprintf('app/public/%s', \Config::get('proto.uploads.staff_videos')));        
            $videosTemp = storage_path(sprintf('app/public/%s', \Config::get('proto.uploads.staff_vid_temp')));

            $video = explode('/videos',$file);
            $file_name = $storagePath.$video[1];           
            $videofile = $video[1];
            $filename = explode('.',$video[1])[0];
            
            $text_file = str_replace(".mp4",".txt",$file_name);
            $data_file = str_replace(".mp4",".dat",$file_name);

            // check file existence - then merging process is in progress
            if(File::exists($text_file)) {
                $text_exist = true;                

                $changeext = str_replace(".mp4",".webm",$file_name);
                $changepfx = str_replace("sharedvideos","sharedaudio",$changeext);
                
                $audio_file = $videosTemp.explode('/videos',$changepfx)[1];   

                // get recording duration from audio since video has no duration on it
                if(File::exists($audio_file)) {
                    $dura = $this->getDuration($audio_file);
                    while( $dura == 0 ) {
                        sleep(1);
                    }                

                    if($dura) {
                        $proProgress = $this->monitorMergingProgress($text_file, $dura);                    
                        if($proProgress['process'] == 'end') 
                            $proProgress['progress'] = 100;   
                        $percent = $proProgress['progress'];
                        $size = $proProgress['size'];
                        $process = $proProgress['process'];

                        if($percent >= 100) {
                            $text_exist = false;
                            $video_exist = true;
                            if(File::exists($text_file)) 
                                $remove_flag = 1;
                        }
                    }   
                }
            // Check for merge video file existence - then switch video_exist flag to true
            } else if(File::exists($file_name)) {         
                $video_exist = true;

                if(File::exists($text_file)) 
                    $remove_flag = 1;
                $dura = $this->getDuration($file_name);
                while( $dura == 0 ) {
                    sleep(1);
                }             

                $filelink = \Config::get('proto.uploads.staff_videos').'/'.$filename;
                $save_dat = $filelink.'.dat';

                if(!File::exists($data_file)) { 
                    $vfilename = $filelink.'.mp4';
                    $encryptedContent = encrypt($this->storage->get($vfilename));
                    $this->storage->put($save_dat,$encryptedContent);
                    while( !File::exists($data_file) ) {
                        sleep(2);
                    }                
                    // if(File::exists($file_name)) 
                    //     unlink($file_name);
                }

            } else if(File::exists($data_file)) { 
                $text_exist = false;
                $video_exist = true;
            } 
            
            if($remove_flag == 1) {
                sleep(1);

                if(File::exists($text_file)) 
                    unlink($text_file);

                $screen = $videosTemp.'/'.explode('/videos/',$text_file)[1];  
                
                $video = str_replace(".txt",".mkv",$screen);
                $video = str_replace("sharedvideos","sharedvideo",$video);
                if(File::exists($video)) 
                    unlink($video);          
                
                $audio = str_replace(".mkv",".webm",$video);
                $audio = str_replace("sharedvideo","sharedaudio",$audio);
                if(File::exists($audio)) 
                    unlink($audio);                       
            }

            return $this->response->array(['video_exist' => $video_exist, 'text_exist' => $text_exist, 'pro_percent' => $percent, 'file' => $file, 'duration' => $dura, 'size' => $size, 'process' => $process])->setStatusCode(200); 
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:checkVideoUrl',
                    'messages' => $e
                )
            ]);
        }
    }

    public function monitorMergingProgress($txtfile, $lent) {
        try {
            $user_agent = getenv("HTTP_USER_AGENT");
            
            $progress = 0;
            $process = '';

            $processProgress['progress'] = 0;
            $processProgress['process'] = '';
            $processProgress['size'] = 0;

            // check text file existence - then get merging progress status
            if(File::exists($txtfile)) {
            
                $content = file_get_contents($txtfile);
            
                // get duration - start
                preg_match_all("/out_time=(.{2}):(.{2}):(.{2})/", $content, $matches);
                $rawTime = array_pop($matches[0]);

                $ar = explode("=", $rawTime);
                $time = array_reverse(explode(":", $ar[1]));            

                $brktime = floatval($time[0]);
                $brktime = $brktime + intval($time[1]) * 60;
                $brktime = $brktime + intval($time[2]) * 60 * 60;            

                $processProgress['progress'] = round(($brktime/$lent) * 100);    
                // get duration - end

                $processProgress['process'] = '';
                preg_match_all("/progress=end/", $content, $endmatch);
                $endpop = array_pop($endmatch[0]);
                if(count($endpop)) {
                    $endbrk = explode("=", $endpop);
                    $processProgress['process'] =  $endbrk[1];
                }

                // get file size - start
                preg_match_all("/total_size=(?P<digit>\d+)/", $content, $matches);
                $rawSize = array_pop($matches[0]);

                $file_size = explode("=", $rawSize);
                $processProgress['size'] = $file_size[1];
                // get file size - end
            }  

            return $processProgress; 
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:monitorMergingProgress',
                    'messages' => $e
                )
            ]);
        }            
    }  
    
    public function getDuration($link) {
        try {
            $dura = 0;                  

            if (DIRECTORY_SEPARATOR == '\\') {
                $cmd = "ffmpeg -i ".$link." 2>&1";
            } else if (DIRECTORY_SEPARATOR == '/') {
                $cmd = "export HOME=/tmp/ && ffmpeg -i ".$link." 2>&1";            
            }

            $dur = shell_exec($cmd);

            if(preg_match("/: Invalid /", $dur)) {
                Log::info("getDuration preg_match false");
                return $this->response->array(['duration' => $dura])->setStatusCode(200); 
            }
            preg_match("/Duration: (.{2}):(.{2}):(.{2})/", $dur, $duration);
            if(isset($duration[1])) {
                $hours = $duration[1];
                $minutes = $duration[2];
                $seconds = $duration[3];

                $dura = $seconds + ($minutes*60) + ($hours*60*60);
            } else {
                Log::info("getDuration !isset(duration[1]) false");
            }
            return $dura;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:getDuration',
                    'messages' => $e
                )
            ]);
        }            
    }    

    public function getPidInWindows($runApp, $storagePath, $action) {
        try {
            $descriptorspec = array (  
                0 => array("pipe", "r"),  
                1 => array("pipe", "w"),  
            );  

            if ( is_resource( $prog = proc_open("start /b " . $runApp, $descriptorspec, $pipes, $storagePath, NULL) ) ) { 
                // Get Parent process Id  
                $ppid = proc_get_status($prog); 

                //Process Id is  
                $pid=$ppid['pid'];   
                $lop = 0;           
                
                while (true) {
                    $exist = 0;
                    $lineCount = 1;
                    $filter = [];
                    $output = array_filter(explode(" ", shell_exec("wmic process get parentprocessid,processid | find \"$pid\"")));  
                    foreach($output as $value) {
                        $val = trim($value);
                        if($val) {
                            array_push($filter, $val);
                            if($val == $pid) $exist++;
                        }
                        $lineCount++;
                    }
                    if($exist > 1) break;
                }
        
                $apid = [];      
                $switch = 0;
                $exist = 0;
                foreach($filter as $value) {    
                    $val = trim($value);        
                    if($switch == 0) {
                        if($val == $pid) $exist = 1;
                        else $exist = 0;
                        $switch = 1;
                    } else {
                        if($exist == 1) {
                            array_push($apid, $val);                    
                        }
                        $switch = 0;
                    }
                }

                return $apid;

            } else {  
                Log::info("Failed to execute!");
                return;
            }  
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:getPidInWindows',
                    'messages' => $e
                )
            ]);
        }              
    }    

    public function getPidInLinux($runApp, $storagePath) {
        try {
            $descriptorspec = array (  
                0 => array("pipe", "r"),  
                1 => array("pipe", "w"),  
            );  

            if (is_resource($prog = proc_open("export HOME=/tmp/ && " . $runApp, $descriptorspec, $pipes, $storagePath, NULL) ) ) {
                // Get Parent process Id  
                $ppid = proc_get_status($prog);  
                $pid=$ppid['pid']; 
                $pid=$pid+1;

                $apid = [];

                //Process Id is  
                array_push($apid,$pid);                             

            } else {  
                Log::info("Failed to execute!");
                exit();  
            }  
            
            return $apid;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:getPidInLinux',
                    'messages' => $e
                )
            ]);
        }              
    }

   /**
     * Fetch Total Disk Usage
     *
     * @param Request
     * @return size
     */
    public function fetchTotalDiskUsage() {
        try {        
            if ($this->user->role == \Config::get('proto.role.company_user')) {
                $result = CompanyUserNegotation::negotationByCompanyUser($this->user->company_user->id)->sum('video_size');
            } else {
                $result = StaffNegotation::negotationByStaff($this->user->staff->id)->sum('video_size');
            }
            return $this->response->array(['total_size' => $result])->setStatusCode(200);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:fetchTotalDiskUsage',
                    'messages' => $e
                )
            ]);
        }                  
    }
    // m2b-81        
}
