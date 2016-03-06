<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{

    protected $table = 'types';

    protected $fillable = [
        'id',
        'name'
    ];

    public function tickets(){
        return $this->belongsToMany('Ticket');
    }

    public function users(){
        return $this->belongsToMany('User');
    }
}
