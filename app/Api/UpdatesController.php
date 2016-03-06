<?php

namespace App\Api;

use App\Answer;
use App\Lead;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class UpdatesController extends ApiController
{
    public $item;

    public function __construct(Request $request){
        parent::__construct();
        return $this;
    }

    public function my(){
        $items = \DB::select('CALL getFreezedLeads('.$this->user->id.', '.$this->user->lft.', '.$this->user->rgt.')');
        $result = ['leads' => $items];
        return $result;
    }
}
