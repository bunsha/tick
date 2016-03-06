<?php

namespace App\Api;

use App\Classes\Socket\Pusher;
use App\Http\Controllers\Controller;
use App\User;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiController extends Controller
{
    use Helpers;

    public $user;
    public $error;
    public $children;
    public $childrenArray;
    public $roles;
    public $permissions;

    public function __construct(){
        $this->user = $this->getUser();
        $this->account = $this->user->account;
        $this->permissions = $this->getPermissions();
        return $this;
    }


    public function checkToken(){
        return $this;
    }

    public function getUser(){
        return JWTAuth::parseToken()->authenticate();
    }

    public function getUserChildren(){
        return $this->user->getDescendants();
    }

    public function getChildrenArray(){
        $this->children = $this->getUserChildren();
        $children = [];
        if(count($this->children) > 0){
            foreach($this->children as $i){
                $children[] = $i->id;
            }
        }
        return (array)$children;
    }

    public function getChildrenWithMeArray(){
        $this->children = $this->getUserChildren();
        $children = [$this->user->id];
        if(count($this->children) > 0){
            foreach($this->children as $i){
                $children[] = $i->id;
            }
        }
        return (array)$children;
    }

    public function getRoles(){
        return $this->user->roles;
    }

    public function getPermissions(){
        $permissions = [];
        foreach($this->getRoles() as $role){
            foreach($role->permissions as $permission){
                $permissions[] = $permission->name;
            }
        }
        return $permissions;
    }

    public function getLeadsCount(){
        return DB::select('CALL getUserLeadsCount('.$this->user->id.', '.$this->user->lft.', '.$this->user->rgt.')');
    }
}
