<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\StaffFolder;
use App\StaffDocument;
use App\Http\Requests;
use Artisan;
// use Storage;
class StaffFolderController extends Controller
{
	public function store(Request $request){
        try {
            $staff_id = $this->user->staff->id;
            $folder = new StaffFolder;
            $folder->staff_id = $staff_id;
            $folder->title = $request->title;
            $folder->save();
            return $folder;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffFolderController:store',
                    'messages' => $e
                )
            ]);
        }
	}
        
    public function retrieve(){
        try {
            $staff_id = $this->user->staff->id;
            $folders = StaffFolder::where('staff_id', $staff_id)->get();
            // $doc_path = \Config::get('proto.uploads.staff_docs');
            // $page_path = \Config::get('proto.uploads.staff_doc_pages');

            // $storage = Storage::disk('public');
            // $docs = Storage::disk('public')->files($doc_path);
            // $pages = Storage::disk('public')->files($page_path);

            // $mydocs = [
            // 'uploads/staff/documents/document1536571493-224.pdf',
            // 'uploads/staff/documents/document1536649140-966.pdf',
            // 'uploads/staff/documents/document1536651521-352.pdf',
            // 'uploads/staff/documents/document1536657773-125.pdf',
            // 'uploads/staff/documents/document1536662659-743.pdf',
            // 'uploads/staff/documents/document1536732457-422.pdf',
            // 'uploads/staff/documents/document1536818071-976.pdf',
            // 'uploads/staff/documents/document1536905296-482.pdf',
            // 'uploads/staff/documents/document1537337221-724.pdf',
            // 'uploads/staff/documents/document1537774284-244.pdf',
            // 'uploads/staff/documents/document1537774427-997.pdf',
            // 'uploads/staff/documents/document1537774481-222.pdf',
            // 'uploads/staff/documents/document1537774651-121.pdf',
            // 'uploads/staff/documents/document1537870810-895.pdf'
            // ];
            // $mypages = [
            // 'uploads/staff/documents/pages/document1536571493-224-1.jpg',
            // 'uploads/staff/documents/pages/document1536571493-224-2.jpg',
            // 'uploads/staff/documents/pages/document1536649140-966-1.jpg',
            // 'uploads/staff/documents/pages/document1536649140-966-2.jpg',
            // 'uploads/staff/documents/pages/document1536651521-352-1.jpg',
            // 'uploads/staff/documents/pages/document1536651521-352-2.jpg',
            // 'uploads/staff/documents/pages/document1536651521-352-3.jpg',
            // 'uploads/staff/documents/pages/document1536651521-352-4.jpg',
            // 'uploads/staff/documents/pages/document1536651521-352-5.jpg',
            // 'uploads/staff/documents/pages/document1536651521-352-6.jpg',
            // 'uploads/staff/documents/pages/document1536651521-352-7.jpg',
            // 'uploads/staff/documents/pages/document1536651521-352-8.jpg',
            // 'uploads/staff/documents/pages/document1536651521-352-9.jpg',
            // 'uploads/staff/documents/pages/document1536651521-352-10.jpg',
            // 'uploads/staff/documents/pages/document1536651521-352-11.jpg',
            // 'uploads/staff/documents/pages/document1536651521-352-12.jpg',
            // 'uploads/staff/documents/pages/document1536651521-352-13.jpg',
            // 'uploads/staff/documents/pages/document1536651521-352-14.jpg',
            // 'uploads/staff/documents/pages/document1536651521-352-15.jpg',
            // 'uploads/staff/documents/pages/document1536651521-352-16.jpg',
            // 'uploads/staff/documents/pages/document1536657773-125-1.jpg',
            // 'uploads/staff/documents/pages/document1536657773-125-2.jpg',
            // 'uploads/staff/documents/pages/document1536662659-743-1.jpg',
            // 'uploads/staff/documents/pages/document1536662659-743-2.jpg',
            // 'uploads/staff/documents/pages/document1536662659-743-3.jpg',
            // 'uploads/staff/documents/pages/document1536662659-743-4.jpg',
            // 'uploads/staff/documents/pages/document1536662659-743-5.jpg',
            // 'uploads/staff/documents/pages/document1536732457-422-1.jpg',
            // 'uploads/staff/documents/pages/document1536732457-422-2.jpg',
            // 'uploads/staff/documents/pages/document1536818071-976-1.jpg',
            // 'uploads/staff/documents/pages/document1536818071-976-2.jpg',
            // 'uploads/staff/documents/pages/document1536905296-482-1.jpg',
            // 'uploads/staff/documents/pages/document1536905296-482-2.jpg',
            // 'uploads/staff/documents/pages/document1537337221-724-1.jpg',
            // 'uploads/staff/documents/pages/document1537337221-724-2.jpg',
            // 'uploads/staff/documents/pages/document1537337221-724-3.jpg',
            // 'uploads/staff/documents/pages/document1537337221-724-4.jpg',
            // 'uploads/staff/documents/pages/document1537337221-724-5.jpg',
            // 'uploads/staff/documents/pages/document1537337221-724-6.jpg',
            // 'uploads/staff/documents/pages/document1537337221-724-7.jpg',
            // 'uploads/staff/documents/pages/document1537774284-244-1.jpg',
            // 'uploads/staff/documents/pages/document1537774284-244-2.jpg',
            // 'uploads/staff/documents/pages/document1537774284-244-3.jpg',
            // 'uploads/staff/documents/pages/document1537774284-244-4.jpg',
            // 'uploads/staff/documents/pages/document1537774284-244-5.jpg',
            // 'uploads/staff/documents/pages/document1537774284-244-6.jpg',
            // 'uploads/staff/documents/pages/document1537774284-244-7.jpg',
            // 'uploads/staff/documents/pages/document1537774284-244-8.jpg',
            // 'uploads/staff/documents/pages/document1537774284-244-9.jpg',
            // 'uploads/staff/documents/pages/document1537774284-244-10.jpg',
            // 'uploads/staff/documents/pages/document1537774284-244-11.jpg',
            // 'uploads/staff/documents/pages/document1537774284-244-12.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-1.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-2.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-3.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-4.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-5.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-6.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-7.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-8.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-9.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-10.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-11.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-12.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-13.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-14.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-15.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-16.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-17.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-18.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-19.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-20.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-21.jpg',
            // 'uploads/staff/documents/pages/document1537774427-997-22.jpg',
            // 'uploads/staff/documents/pages/document1537774481-222-1.jpg',
            // 'uploads/staff/documents/pages/document1537774481-222-2.jpg',
            // 'uploads/staff/documents/pages/document1537774481-222-3.jpg',
            // 'uploads/staff/documents/pages/document1537774481-222-4.jpg',
            // 'uploads/staff/documents/pages/document1537774481-222-5.jpg',
            // 'uploads/staff/documents/pages/document1537774481-222-6.jpg',
            // 'uploads/staff/documents/pages/document1537774481-222-7.jpg',
            // 'uploads/staff/documents/pages/document1537774481-222-8.jpg',
            // 'uploads/staff/documents/pages/document1537774481-222-9.jpg',
            // 'uploads/staff/documents/pages/document1537774481-222-10.jpg',
            // 'uploads/staff/documents/pages/document1537774481-222-11.jpg',
            // 'uploads/staff/documents/pages/document1537774481-222-12.jpg',
            // 'uploads/staff/documents/pages/document1537774481-222-13.jpg',
            // 'uploads/staff/documents/pages/document1537774481-222-14.jpg',
            // 'uploads/staff/documents/pages/document1537774481-222-15.jpg',
            // 'uploads/staff/documents/pages/document1537774481-222-16.jpg',
            // 'uploads/staff/documents/pages/document1537774481-222-17.jpg',
            // 'uploads/staff/documents/pages/document1537774481-222-18.jpg',
            // 'uploads/staff/documents/pages/document1537774651-121-1.jpg',
            // 'uploads/staff/documents/pages/document1537774651-121-2.jpg',
            // 'uploads/staff/documents/pages/document1537774651-121-3.jpg',
            // 'uploads/staff/documents/pages/document1537774651-121-4.jpg',
            // 'uploads/staff/documents/pages/document1537774651-121-5.jpg',
            // 'uploads/staff/documents/pages/document1537774651-121-6.jpg',
            // 'uploads/staff/documents/pages/document1537774651-121-7.jpg',
            // 'uploads/staff/documents/pages/document1537774651-121-8.jpg',
            // 'uploads/staff/documents/pages/document1537774651-121-9.jpg',
            // 'uploads/staff/documents/pages/document1537774651-121-10.jpg',
            // 'uploads/staff/documents/pages/document1537774651-121-11.jpg',
            // 'uploads/staff/documents/pages/document1537774651-121-12.jpg',
            // 'uploads/staff/documents/pages/document1537774651-121-13.jpg',
            // 'uploads/staff/documents/pages/document1537774651-121-14.jpg',
            // 'uploads/staff/documents/pages/document1537774651-121-15.jpg',
            // 'uploads/staff/documents/pages/document1537870810-895-1.jpg',
            // 'uploads/staff/documents/pages/document1537870810-895-2.jpg',
            // 'uploads/staff/documents/pages/document1537870810-895-3.jpg',
            // 'uploads/staff/documents/pages/document1537870810-895-4.jpg',
            // 'uploads/staff/documents/pages/document1537870810-895-5.jpg',
            // 'uploads/staff/documents/pages/document1537870810-895-6.jpg',
            // 'uploads/staff/documents/pages/document1537870810-895-7.jpg'
            // ];
            // foreach ($mydocs as $mydoc) {
            //     $index = array_search($mydoc, $docs);
            //     if($index !== false){
            //         unset($docs[$index]);
            //     }
            // }
            // foreach ($mypages as $mypage) {
            //     $index2 = array_search($mypage, $pages);
            //     if($index2 !== false){
            //         unset($pages[$index2]);
            //     }
            // }
            // $storage->delete($docs);
            // $storage->delete($pages);
            return $folders;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffFolderController:retrieve',
                    'messages' => $e
                )
            ]);
        }
    }
    public function getFolder(Request $request){
        try {
            $staff_id = $this->user->staff->id;
            $folder = StaffFolder::where('id', $request->id)->first();

            return $folder;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffFolderController:getFolder',
                    'messages' => $e
                )
            ]);
        }
    }
    public function getContents(){
        return 200;
    }
    public function update(Request $request){
        try {
            $folder = StaffFolder::where('id', $request->id)->first();
            $folder->title = $request->title;
            $folder->save();
            return $folder->title;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffFolderController:update',
                    'messages' => $e
                )
            ]);
        }
    }
    public function destroy(Request $request){
        try {
            $staff_id = $this->user->staff->id;
            $folder_id = $request->id;
            $folder = StaffFolder::find($folder_id);
            $folder->delete();
            // Update files under this folder
            $updateContent = $this->updateFolderContents($folder_id, $staff_id);

            return $updateContent;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffFolderController:destroy',
                    'messages' => $e
                )
            ]);
        }
    }
    
    public function updateFolderContents($folder_id, $staff_id){
        try {
            $documents = new StaffDocument;
            $files = StaffDocument::where('staff_id', $staff_id)->where('folder_id', $folder_id)->get();
            if($files){
                foreach($files as $file){
                    $doc_file = StaffDocument::find($file->id);
                    $doc_file->folder_id = 0;
                    $doc_file->save();
                }
            }
            return $files;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffFolderController:updateFolderContents',
                    'messages' => $e
                )
            ]);
        }
    }
     // Move Document
    public function moveDocument(Request $request){
        try {
            $doc = StaffDocument::where('id', $request->id)->first();
            $doc->folder_id = $request->folder;
            $doc->save();
            return $doc;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffFolderController:moveDocument',
                    'messages' => $e
                )
            ]);
        }
    }
}