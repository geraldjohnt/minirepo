<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffDocumentPage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'staff_document_id', 'annotation', 'image_url', 'mime_type', 'size'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    /**
     * Relationship between staff_document
     */
    public function staff_document() {
        return $this->belongsTo('App\StaffDocument');
    }

    //scopes

    public function scopePagesByDocumentId($query, $id){
        return $query->where('staff_document_id', $id);
    }

}
