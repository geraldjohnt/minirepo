<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffFolder extends Model
{

    protected $table = 'staff_folder';

	protected $fillable = [
        'staff_id', 'title'
    ];

     /**
     * Relationship between staff
     */
    public function staff() {
        return $this->belongsTo('App\Staff');
    }

    /**
     * Relationship between document pages
     */
    public function documents() {
        return $this->hasMany('App\StaffDocument');
    }
    
    //scopes
    public function scopeDocumentsByFolder($query, $id, $folder){
        return $query->where('staff_id', $id)->where('folder_id', $folder);
    }
}
