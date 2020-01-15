<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffNegotation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'staff_id', 'notes', 'voice_report', 'video_report', 'video_size', 'duration'
    ];

    public function getDates() {
        return array();
     }
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    /**
     * Relationship between staff
     */
    public function staff() {
        return $this->belongsTo('App\Staff');
    }

    //scopes
    
    public function scopeNegotationByStaff($query, $id){
        return $query->where('staff_id', $id);
    }
}
