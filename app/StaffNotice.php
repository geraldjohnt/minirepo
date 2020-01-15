<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffNotice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'notice_id', 'staff_id', 'read', 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    /**
     * Relationship between notice
     */
    public function notice() {
        return $this->belongsTo('App\Notice');
    }

    /**
     * Relationship between staff
     */
    public function staff() {
        return $this->belongsTo('App\Staff');
    }

    //scopes

    public function scopeNoticesByNotice($query, $id){
        return $query->where('notice_id', $id);
    }
    
    public function scopeNoticesByStaff($query, $id){
        return $query->where('staff_id', $id);
    }

}
