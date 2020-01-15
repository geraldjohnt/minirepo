<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
//use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    //use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'company', 'department', 'post', 'pic_url', 'phone_number', 'role', 'trial', 'status', 'registered_date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'password_reset_token'
    ];

    /**
     * Relationship between admin
     */
    public function admin() {
        return $this->hasOne('App\Admin');
    }

    /**
     * Relationship between staff
     */
    public function staff() {
        return $this->hasOne('App\Staff');
    }

    public function isAdmin() {
        return $this->role == \Config::get('proto.role.admin');
    }

    public function isStaff() {
        return $this->role == \Config::get('proto.role.staff');
    }

    public function getRole() {
        return array_search( $this->role, \Config::get('proto.role') );
    }

    public function lastLogin(){
        $this->last_login = date_create('now', timezone_open('Asia/Tokyo'))->format('Y-m-d H:i:s');
        $this->save();
    }
    
    public function deactivateUser(){
        $this->status = 0;
        $this->save();
    }

    public function isActive(){
        return $this->status;
    }

    public function registeredDate() {
        $this->registered_date = date_create('now', timezone_open('Asia/Tokyo'))->format('Y-m-d H:i:s');
        $this->save();
    }
}
