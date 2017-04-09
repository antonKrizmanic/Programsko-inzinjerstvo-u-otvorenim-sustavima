<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','role'
    ];

    protected $dates = ['deleted_at'];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password','remember_token',
    ];

    public static function getEmail($id){
        $user = User::find($id);
        return $user['email'];
    }
    public static function  getId($mail){
        $user = User::where('email',$mail)->first();
        return $user['id'];
    }

    public function interests()
    {
        return $this->belongsToMany('App\Event','interests','user_id','event_id');
    }

    public function comments()
    {
        return $this->belongsToMany('App\Event','comments','user_id','event_id');
    }

    public function events(){
        return $this->hasMany('App\Event');
    }

    public function grades(){
        return $this->hasMany('App\Grade');
    }

}
