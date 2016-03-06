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
    public $code;


    const NEW_TICKET = 'new';
    const TICKET_IN_PROGRESS = 'progress';
    const DONE_TICKET = 'done';
    const FINISHED_TICKET = 'finished';
    const NEED_HELP_TICKET = 'help';
    const CLOSED = 'closed';


    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        return $this;
    }

    public function __destruct(){
        // TODO: Implement __destruct() method.
    }

}
