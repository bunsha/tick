<?php

namespace App;


use Illuminate\Support\Facades\Response;

class Answer
{
    public static function set($status = false, $message = false){
        return  Response::make($message, $status);
    }
}
