<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'parent_id',
        'status',
        'team',
        'leader',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function children()
    {
        return $this->hasMany('User', 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo('User', 'parent_id');
    }

    public function tickets(){
        return $this->belongsToMany('Ticket');
    }

    public function types(){
        return $this->belongsToMany('Type', 'user_id');
    }

    public function comments(){
        return $this->hasMany('Comment');
    }
}
