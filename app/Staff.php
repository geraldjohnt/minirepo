<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Staff extends Model
{
    //use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'title', 'in_meeting', 'client_peer_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
    

    /**
     * Relationship between user
     */
    public function user() {
        return $this->belongsTo('App\User');
    }
    
    public function negotations(){
        return $this->hasMany('App\StaffNegotation');
    }
    /**
     * Relationship between documents
     */
    public function documents() {
        return $this->hasMany('App\StaffDocument');
    }

    /**
     * Relationship between folders
     */
    public function folders() {
        return $this->hasMany('App\StaffFolder');
    }

    /**
     * Relationship between notices
     */
    public function notices() {
        return $this->hasMany('App\StaffNotice');
    }

    //scopes 
    
    public function scopeGetStaffInMeeting($query){
        return $query->where('in_meeting', 1);
    }

    public function scopeGetStaffWithPeerId($query, $id){
        return $query->where('client_peer_id', $id);
    }

    public function scopeGetAllStaffNegotation(){
        
         $staff = Staff::with('negotations')->with('user')->get();
         $staffs = array();
         foreach($staff as $value){
            $firstname = !empty($value->user->first_name) ? $value->user->first_name : null;
            $lastname = !empty($value->user->last_name) ? $value->user->last_name : null;

	    $temp = [
                    'staff_id' => $value->id,
                    'profile' => [
                            'company' => !empty($value->user->company) ? $value->user->company : null,
                            'full_name' => $firstname . ' '. $lastname,
                            'differ'=>  empty($value->user->last_login) ? '0 日前' : date_diff(date_create($value->user->last_login),date_create(date('Y-m-d H:i:s')))->format("%a"). ' 日前',
                            'differInt'=>  empty($value->user->last_login) ? 0 : (int)date_diff(date_create($value->user->last_login),date_create(date('Y-m-d H:i:s')))->format("%a"),
                            'last_connection'=>empty($value->negotations) ? null : !empty($value->negotations->last()['updated_at']) ? date_create($value->negotations->last()['updated_at'])->format("Y/m/d H:i") : null,
                            'last_login'=> empty($value->user->last_login) ?  null : $value->user->last_login ? date_format(date_create($value->user->last_login),"Y/m/d H:i") : '',
                            'negotations' => !empty($value->negotations) ? $value->negotations : null
                    ]       

            ];

            array_push($staffs,$temp);
            
         }

         return $staffs;
     
    }
        
    public function getLog(){
        return DB::getQueryLog();
    }
}
