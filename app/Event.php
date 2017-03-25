<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = ['title', 'short_description', 'long_description','photo','user_id'];

    public function interested()
    {
        return $this->belongsToMany('App\User','interests','event_id','user_id');
    }
}
