<?php

namespace App\Api;
use App\Team;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class TeamsController extends ApiController
{
    public function get(Request $request){
        return Team::where('account_id', $this->accountId)->first();
    }
    public function save(Request $request){
        $team = Team::firstOrNew(['account_id' => $request->account]);
        $team->account_id = $request->account;
        $team->theData = $request->data;
        $team->save();
        return $team;
    }
}
