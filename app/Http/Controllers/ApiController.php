<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Http\Request as UpdateRequest;
use App\Http\Controllers\Auth\AuthController as Auth;
use App\Traits\FileTrait;
use Artisan;
use Storage;

abstract class ApiController extends Controller
{
    use FileTrait;

	/**
     * The Authenticated User
     *
     * @var App\User
     */
    protected $user;

	/**
     * The Eloquent model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The number of models to return for pagination.
     *
     * @var int|null
     */
    protected $per_page;

    /**
     * The Transformer instance
     *
     * @var \League\Fractal\TransformerAbstract
     */
    protected $transformer;

    /**
     * The Transformer key
     *
     * @var String
     */
    protected $key;

    /**
     * The Request
     *
     * @var Illuminate\Http\Request
     */
    protected $request;

    /**
     * The Storage
     *
     * @var Storage
     */
    protected $storage;

    /**
     * Initialize per_page
     *
     * @param Request
     * @return void
     */
    public function __construct(Request $request)
    {
        try {
            $auth = new Auth();
            $this->user = $request->user() ?: $auth->getUser();
            $this->per_page = $request->input('per_page');
            $this->per_page = !empty($this->per_page) ? $this->per_page : \Config::get('proto.defaults.pagination');
            $this->request = $request;
            $this->storage = Storage::disk('public');
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'ApiController:_construct',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Get Paginated Collection
     *
     * @param Request
     * @return paginated collection
     */
    public function index()
    {
        try {
            $collection = $this->model->paginate($this->per_page);

            if (!$collection->count())
            {
                return $this->response->noContent();
            }

            return $this->transformPaginatedCollection($collection, $this->transformer, str_plural($this->key));
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'ApiController:index',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Add an item
     *
     * @param UpdateRequest
     * @return model item
     */
    public function store()
    {
        try {
            $data = $this->request->all();

            //file upload
            if($this->request->files_attached) {
                foreach($this->request->files_attached as $file) {
                    if($file['field_key']) {
                        $this->request->merge($file);
                        $a_file = $this->uploadFile($this->request, $file['field_key']);
                        if(!empty($a_file)) {
                            $data = array_merge($data, $a_file);
                        }
                    }
                }
            }

            $result = $this->model->create($data);
            if($this->request->return_created) {
                return $result;
            }

            return $this->response->created();
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'ApiController:store',
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
            $item = $this->model->findOrFail($id);
            return $this->transformItem($item, $this->transformer, $this->key);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'ApiController:show',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Update specific item
     *
     * @param UpdateRequest
     * @return model item
     */
    public function update($id)
    {
        try {
            $item = $this->model->findOrFail($id);
            $data = $this->request->all();

            //file upload
            if($this->request->files_attached) {
                foreach($this->request->files_attached as $file) {
                    if($file['field_key']) {
                        $this->request->merge($file);
                        $a_file = $this->uploadFile($this->request, $file['field_key']);
                        if(!empty($a_file)) {
                            $data = array_merge($data, $a_file);
                        }
                    }
                }
            }

            $item->update($data);
            if($this->request->return_updated) {
                return $item;
            }

            $result = $this->transformItem($item, $this->transformer);
            return $this->response->array([$this->key => $result])->setStatusCode(200);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'ApiController:update',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Delete a specific item
     *
     * @param  int $id
     * @return Message String
     */
    public function destroy($id)
    {
        try {
            $item = $this->model->findOrFail($id);
            $item->delete();

            return $this->response->array(['message' => ucfirst($this->key).' Successfully Deleted'])->setStatusCode(200);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'ApiController:destroy',
                    'messages' => $e
                )
            ]);
        }
    }

}
