<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffDocument extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'staff_id', 'title', 'description', 'file_url', 'file_name', 'mime_type', 'size', 'allow_download', 'created_by','folder_id'
    ];
    
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
    
    /**
     * Relationship between Staff Folder
     */
    public function staff_folder() {
        return $this->belongsTo('App\StaffFolder');
    }
    
    /**
     * Relationship between document pages
     */
    public function pages() {
        return $this->hasMany('App\StaffDocumentPage');
    }
    
    //scopes
    public function scopeDocumentsByStaff($query, $id, $folder){
        return $query->where('staff_id', $id)->where('folder_id', $folder);
    }

    public function scopeDocumentByStaff($query, $id, $document){
        return $query->where('staff_id', $id)->where('id', $document);
    }

    public function scopeAllDocumentsByStaff($query, $id){
        return $query->where('staff_id', $id);
    }
    
    public function scopeDocumentsByFolder($query, $id, $folder){
        return $query->where('staff_id', $id)->where('folder_id', $folder);
    }
    
    public function scopeAllowDownload($query){
        return $query->where('allow_download', true);
    }
}
