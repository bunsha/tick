<?php

namespace App\Api;

use App\Account;
use App\Permission;
use App\Role;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;

class PermissionsController extends ApiController
{
    public $item;

    public function __construct(Request $request){
        parent::__construct();
        return $this;
    }
}
