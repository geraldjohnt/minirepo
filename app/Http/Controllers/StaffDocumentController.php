<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Staff;
use App\StaffDocument;
use App\StaffFolder;
use App\StaffDocumentPage;
use App\Http\Requests\StaffDocumentRequest;
use App\Transformers\StaffDocumentTransformer;
use File;

class StaffDocumentController extends ApiController
{

    /**
     * @param Request
     * @return void
     */
    
    public function __construct(StaffDocumentRequest $request)
    {
        try {
            parent::__construct($request);
            $this->model = new StaffDocument;
            $this->transformer = new StaffDocumentTransformer;
            $this->key = 'document';
            $this->request = $request;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffDocumentController:_construct',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Get Staff Documents
     *
     * @param Request
     * @return paginated collection
     */
    
    public function index()
    {
        try {
            $this->model = StaffDocument::documentsByStaff($this->user->staff->id, $this->request->folder);
            return parent::index();
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffDocumentController:index',
                    'messages' => $e
                )
            ]);
        }
    }

    public function showFolderContent(){
        try {
            $this->model = StaffDocument::documentsByStaff($this->user->staff->id, $this->request->folder);
            return parent::index();
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffDocumentController:showFolderContent',
                    'messages' => $e
                )
            ]);
        }
    }

    public function getAllDocuments(){
        try {
            $this->model = StaffDocument::allDocumentsByStaff($this->user->staff->id);
            return parent::index();
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffDocumentController:getAllDocuments',
                    'messages' => $e
                )
            ]);
        }
    }
    
    /**
     * Add Document
     *
     * @param Request
     * @return document
     */
    
    public function store()
    {
        try {
            $return_statement = [];
            $this->request->offsetUnset('file_url');
            $this->request->offsetUnset('files_attached');
            
            $data = [
                'return_created' => 1,
                'staff_id' => $this->user->staff->id,
                'created_by' => $this->user->id,
                'folder_id' => $this->request->folder_id
            ];

            $data['files_attached'][] = [
                'file_key' => 'file',
                'file_path' => \Config::get('proto.uploads.staff_docs'),
                'file_prefix' => 'document',
                'field_key' => 'file_url'
            ];

            $this->request->merge($data);
            $item = parent::store();

            // Commented for development purposes
            // convert file
            if($item) {
                // $page_path = storage_path(\Config::get('proto.uploads.staff_doc_pages'));
                $page_path = storage_path(sprintf('app/public/%s', \Config::get('proto.uploads.staff_doc_pages')));
                $a_files = $this->convertFileToJpg($item->file_url, $item->file_name, $page_path);

                if($a_files) {
                    foreach($a_files as $file) {
                        // For Local
                        $file = str_replace('C:\xampp\htdocs\mee2box\storage\a','a',$file);
                        $a_file_details = $this->checkFile($file);
                        array_push($return_statement, $a_file_details);

                        // Commented, a_file_details in not found
                        // StaffDocumentPage::create([
                        //     'staff_document_id' => $item->id,
                        //     'image_url'         => $file,
                        //     'mime_type'         => $a_file_details['mime'],
                        //     'size'              => $a_file_details['size']
                        // ]);

                        // // For Local
                        StaffDocumentPage::create([
                            'staff_document_id' => $item->id,
                            'image_url'         => $file,
                            'mime_type'         => 'jpg',
                            'size'              => 0
                        ]);
                    }
                }
            }

            $page_path1 = storage_path(sprintf('app/public/%s', \Config::get('proto.uploads.staff_doc_pages')));
            $page_path2 = storage_path( \Config::get('proto.uploads.staff_doc_pages'));
            // return compact('page_path','page_path1','page_path2','return_statement','a_files');
            return $this->response->created();
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffDocumentController:store',
                    'messages' => $e
                )
            ]);
        }
    }


     /**
     * Show specific item
     *
     * @param Request
     * @return document
     */

    public function show($id)
    {
        try {
            // $item = $this->model->pages();
            // return parent::show($id);
            $this->model = StaffDocument::documentByStaff($this->user->staff->id, $id);
            return parent::index();
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffDocumentController:show',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Update Document
     *
     * @param Request
     * @return document
     */
    
    public function update($id)
    {
        try {
            $this->request->offsetUnset('staff_id');
            $this->request->offsetUnset('created_by');
            $this->request->offsetUnset('file_url');

            $data = [
                'return_updated' => 1
            ];

            $this->request->merge($data);
            $item = parent::update($id);

            $req = $this->request->only('pages');

            foreach($req['pages'] as $key => $value) {
                foreach($item->pages as $page) {
                    if($page->id == $key) {
                        $page->update(['annotation' => $value]);
                        break;
                    }
                }
            }

            $result = $this->transformItem($item, $this->transformer);
            return $this->response->array([$this->key => $result])->setStatusCode(200);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffDocumentController:update',
                    'messages' => $e
                )
            ]);
        }
    }
}
