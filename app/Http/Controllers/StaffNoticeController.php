<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Staff;
use App\StaffNotice;
use DB;
use Artisan;
use App\Http\Requests\StaffNoticeRequest;
use App\Transformers\StaffNoticeTransformer;

class StaffNoticeController extends ApiController
{
    /**
     * @param Request
     * @return void
     */
    public function __construct(StaffNoticeRequest $request)
    {
        try {
            parent::__construct($request);
            $this->model = new StaffNotice;
            $this->transformer = new StaffNoticeTransformer;
            $this->key = 'notice';
            $this->request = $request;

            $this->middleware('notice.owned')->except('index', 'store');
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffNoticeController:_construct',
                    'messages' => $e
                )
            ]);
        }
    }

    /**
     * Get Staff Notices
     *
     * @param void
     * @return paginated collection
     */
    
    public function index()
    {
        // $this->model = StaffNotice::noticesByStaff($this->user->staff->id);
        
        // return parent::index();
        try {
            $notices = DB::table("staff_notices")
            ->where('staff_id', $this->user->staff->id)
            ->join('notices', 'staff_notices.notice_id' , '=' , 'notices.id')
            ->join('users', 'notices.created_by', '=', 'users.id')
            ->select('pic_url','title','content','notice_id','read','first_name','last_name','staff_id','staff_notices.created_at','staff_notices.id')
            ->orderBy('created_at', 'desc')->get();

            $this->model = compact('notices');
            return $this->model;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffNoticeController:index',
                    'messages' => $e
                )
            ]);
        }
    }
}
