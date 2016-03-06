<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $table = 'comments';

    protected $fillable = [
        'user_id',
        'ticket_id',
        'content',
        'subject'
    ];

    protected $hidden = [
        'updated_at', 'deleted_at',
    ];

    public function ticket(){
        return $this->belongsTo('Ticket', 'ticket_id');
    }

    public function user(){
        return $this->belongsTo('User', 'user_id');
    }
}
