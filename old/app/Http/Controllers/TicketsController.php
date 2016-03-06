<?php

namespace App\Http\Controllers;

use App\Ticket;
use Illuminate\Http\Request;

use App\Http\Requests;

class TicketsController extends ApiController
{
    public function get(Request $request){
        return Ticket::find($request->id)
            ->with('comments')
            ->get();
    }

    public function getMy(Request $request){
        return Ticket::with('comments')
            ->get();

            //$this->user;
    }

    public function getAll(Request $request){

    }

    public function getCurrent(Request $request){

    }
}
