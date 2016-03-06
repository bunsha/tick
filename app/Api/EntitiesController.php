<?php

namespace App\Api;

use App\Account;
use App\Entity;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class EntitiesController extends ApiController
{
    public function getMy(){
        include('../public/api/entities/getAccountEntities.php');
    }
    public function get($id)
    {
        return Entity::find($id);
    }

    public function getByAccount($id)
    {
        $account =  Account::find($id);
        return $account->entities;

    }
}
