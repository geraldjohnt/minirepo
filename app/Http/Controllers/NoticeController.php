<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Artisan;
use App\Notice;
use App\Http\Requests\NoticeRequest;
use App\Transformers\NoticeTransformer;

class NoticeController extends ApiController
{

    /**
     * @param Request
     * @return void
     */
    public function __construct(NoticeRequest $request)
    {
        try {
            parent::__construct($request);
            $this->model = new Notice;
            $this->transformer = new NoticeTransformer;
            $this->key = 'notice';
            $this->request = $request;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'NoticeController:_construct',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Add Notice
     *
     * @param Request
     * @return notice
     */
    public function store()
    {
        try {
            $data = [
                'created_by' => $this->user->id
            ];

            $data['files_attached'][] = [
                'file_key' => 'cover',
                'file_path' => \Config::get('proto.uploads.notice_cover'),
                'file_prefix' => 'notice',
                'field_key' => 'cover_url'
            ];

            $this->request->merge($data);
            return parent::store();
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'NoticeController:store',
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
            $this->request->offsetUnset('cover_url');
            $this->request->offsetUnset('created_by');

            $data['files_attached'][] = [
                'file_key' => 'cover',
                'file_path' => \Config::get('proto.uploads.notice_cover'),
                'file_prefix' => 'notice',
                'field_key' => 'cover_url'
            ];

            $this->request->merge($data);
            return parent::update($id);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'NoticeController:update',
                    'messages' => $e
                )
            ]);
        }
    }

}
