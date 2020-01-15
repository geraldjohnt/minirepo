<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Traits\FileTrait;
use App\StaffDocument;
use Artisan;
use Storage;
use Response;
use App\StaffDocumentPage;

class FileController extends Controller
{
    use FileTrait;

    protected $storage;

    public function __construct()
    {
        $this->storage = Storage::disk('public');
    }

    /**
     * Show User Avatar
     *
     * @param Image path
     * @return image
     */
    public function showAvatar($image)
    {
        try {
            $avatar = \Config::get('proto.uploads.avatar').'/'.$image;
            return $this->showFile($avatar, $image);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'FileController:showAvatar',
                    'messages' => $e
                )
            ]);
        }
    }
    
    /**
     * Download Staff Document Secured
     *
     * @param peerID_time - consists the peerID and milliseconds from Jan 1970
     * @param Staff Document Id
     * @param file_name
     * @return file
     */
    public function downloadDocumentSecured($peerId_time, $staff_document_id, $file_name)
    {
        /**
         * Check if download link expired
         * The link automatically expires after 1 minute
         */
        $split = explode('-', $peerId_time, 2);
        $peerID = !empty($split[0]) ? $split[0] : '';
        $second = !empty($split[1]) ? $split[1] : '';
        $cookie = isset($_COOKIE['peerId']) ? $_COOKIE['peerId'] : '';
        $temSec = explode(' ', microtime());
        $maxSec = ((int)$temSec[1]) * 1000 + ((int)round($temSec[0] * 1000));


        try {
            if ($peerID == $cookie && $second >= $maxSec) {
                $document = StaffDocument::allowDownload()->findOrFail($staff_document_id);


                if ($document) {
                  return $this->decryptDownload($document,$file_name);
                }
            } else {
                throw new ModelNotFoundException;
            }
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'FileController:downloadDocumentSecured',
                    'messages' => $e
                )
            ]);

            return view('errors/document_status', [
                'error_message' => 'ドキュメント共有の有効時間が経過しました。<br />再度資料を共有したユーザーに共有申請をしてください。'
            ]);
        } catch(ModelNotFoundException $e) {
            // Artisan::call('sendToSlack:message', [
            //     'args' => array(
            //         'location' => 'FileController:downloadDocumentSecured',
            //         'messages' => $e
            //     )
            // ]);

            return view('errors/document_status', [
                'error_message' => 'ドキュメント共有の有効時間が経過しました。<br />再度資料を共有したユーザーに共有申請をしてください。'
            ]);
        }
    }

    /**
     * Download Staff Document
     *
     * @param Staff Document Id
     * @return image
     */
    public function downloadDocument($staff_document_id, $file_name)
    {
        try {
            $document = StaffDocument::allowDownload()->findOrFail($staff_document_id);

            if($document) {
              return $this->decryptDownload($document,$file_name);
            }
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'FileController:downloadDocument',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Show User Document Page
     *
     * @param Image path
     * @return image
     */
    public function showDocumentPage($image)
    {
        try {
            $page = \Config::get('proto.uploads.staff_doc_pages').'/'.$image;
            return $this->showFile($page, $image);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:showDocumentPage',
                    'messages' => $e
                )
            ]);
        }
    }
    
    public function showClientDocumentPage($image){
        try {
            $page = \Config::get('proto.uploads.client_doc_pages').'/'.$image;
            return $this->showFile($page, $image);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'FileController:showClientDocumentPage',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Create a txt file from notes
     *
     * @param String notes
     * @return tmp_link
     */
    public function downloadNotes($file_name)
    {
        try {
            $file= \Config::get('proto.uploads.staff_notes').'/'.$file_name;
            $notes = decrypt($this->storage->get($file));
            $filename = str_replace('.dat','.txt',$file_name);

            $headers = [
              'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
              'Content-Type'        => 'text/plain',
              'Content-Disposition' => 'attachment; filename='.$filename,
            ];

            return response()->make($notes,200,$headers);

        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'FileController:downloadNotes',
                    'messages' => $e
                )
            ]);
        }
    }

    public function deleteFromStorage(Request $request){
        try {
            $docs = StaffDocument::where('id', $request->id)->first();
            $pages = StaffDocumentPage::where('staff_document_id', $request->id)->get();
            $merge = [];
            foreach($pages as $page){
            $path =  str_replace('app/public/','',$page->image_url);
            $merge[] = $path;
            $page->delete();
            }
            $delete_docs = $this->storage->delete($docs->file_url);
            $delete_pages = $this->storage->delete($merge);

            return compact('pages','merge');
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'FileController:deleteFromStorage',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * @param $document file data
     * @param $file_name
     * @return \Illuminate\Http\Response
    */
    private function decryptDownload($document,$file_name){
      try {
        $ext = explode('.',$document->file_name)[1];
        $filename = str_replace($ext,'dat',$file_name);
        $file = str_replace($file_name,$filename,$document->file_url);
        $fileContent = decrypt($this->storage->get($file));

        $headers = [
            'Content-Description'         => 'File Transfer',
            'Content-Type'                =>  $document->mime_type,
            'Content-Disposition'         => 'attachment; filename='.$file_name,
        ];

        return response()->make($fileContent,200,$headers);

      } catch(Exception $e) {
        Artisan::call('sendToSlack:message', [
          'args' => array(
              'location' => 'FileController:decryptDownload',
              'messages' => $e
          )
        ]);
      }
    }

    /**
    * strip and change unecessary tags for better viewing content on document
    * @param $content
    * @return modified content
    */
    private function stripElements($content){
        try {

        $ripElmnts = \Config::get('tinymce.strip');

        foreach($ripElmnts as $elmnt => $repElmnt)
            $content = str_replace($elmnt,$repElmnt,$content);

            return "\xEF\xBB\xBF ".$content;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'FileController:stripElements',
                    'messages' => $e
                )
            ]);
        }
    }

    // m2b-81
	/**
     * Show User Video Folder 
     *
     * @param Video path
     * @return video
     */
    public function showStaffVideo($video)
    {
        $page = \Config::get('proto.uploads.staff_videos').'/'.$video;
        return $this->showFile($page, $video);
    }  
    
    /**
     * Show User Video Temp Folder 
     *
     * @param Temp path
     * @return video
     */
    public function showStaffVidTemp($video)
    {
        $page = \Config::get('proto.uploads.staff_vid_temp').'/'.$video;
        return $this->showFile($page, $video);
    }  
    // m2b-81    

}
