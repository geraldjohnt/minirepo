<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'content', 'broadcast', 'cover_url', 'created_by'
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

    /**
     * Relationship between staff notice
     */
    public function staff_notices() {
        return $this->hasMany('App\StaffNotice');
    }

    //scopes

    public function scopeIsBroadcast($query){
        return $query->where('broadcast', true);
    }
}
