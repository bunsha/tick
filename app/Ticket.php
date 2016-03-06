<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $fillable = [
        'id',
        'name',
        'content',
        'status',
        'type'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function users(){
        return $this->belongsToMany('App\User');
    }

    public function comments(){
        return $this->hasMany('App\Comment');
    }
}
