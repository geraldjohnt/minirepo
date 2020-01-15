<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Artisan;
use App\Staff;
use App\StaffNegotation;
use App\Http\Requests\StaffNegotationRequest;
use App\Transformers\StaffNegotationTransformer;

class StaffNegotationController extends ApiController
{
    /**
     * @param Request
     * @return void
     */
    public function __construct(StaffNegotationRequest $request)
    {
        try {
            parent::__construct($request);
            $this->model = new StaffNegotation;
            $this->transformer = new StaffNegotationTransformer;
            $this->key = 'negotation';
            $this->request = $request;

            $this->middleware('negotation.owned')->except('index', 'store');
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffNegotationController:_construct',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Get Staff Negotations
     *
     * @param void
     * @return paginated collection
     */
    public function index()
    {
        try {
            $this->model = StaffNegotation::negotationByStaff($this->user->staff->id)->orderBy('created_at','desc');
            return parent::index();
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffNegotationController:index',
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
            $this->request->offsetUnset('duration');

            $data = [
                'return_created' => 1,
                'staff_id' => $this->user->staff->id,
            ];

            $this->request->merge($data);
            $item = parent::store();
            $result = $this->transformItem($item, $this->transformer);
            return $this->response->array([$this->key => $result])->setStatusCode(201);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffNegotationController:store',
                    'messages' => $e
                )
            ]);
        }
    }

}
