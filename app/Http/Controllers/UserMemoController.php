<?php

namespace App\Http\Controllers;
use Artisan;
use Illuminate\Http\Request;
use App\Http\Requests;

class UserMemoController extends Controller
{
    public function getMemo(){
        try {
            $memo = $this->user->sales_memo;
            return $memo;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'UserMemoController:getMemo',
                    'messages' => $e
                )
            ]);
        }
    }

    public function updateMemo(Request $request){
        try {
            $this->user->sales_memo = $request->memo;
            $this->user->save();
            return 200;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'UserMemoController:updateMemo',
                    'messages' => $e
                )
            ]);
        }
    }
}
