<?php

namespace App\Http\Controllers;

use App\Classes\Socket\Pusher;
use App\Http\Controllers\Controller;
use App\User;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;


class ApiController extends Controller
{
    use Helpers;

    public $user;
    public $error;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        return $this;
    }

    public function __destruct(){
        // TODO: Implement __destruct() method.
    }

}
