<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Ticket;
use App\Type;
use Illuminate\Http\Request;

use App\Http\Requests;

class TicketsController extends ApiController
{
    public function getAll(Request $request){
        return Ticket::with('comments.user')
            ->where('team_id', $this->user->team)
            ->get();
    }

    public function get(Request $request){
        return Ticket::with('comments.user')->find($request->ticket_id);
    }

    public function getMy(Request $request){
        $temp = [];
        foreach($this->user->types as $type){
            $temp[] = $type->id;
        }
        if($this->user->leader){
            return Ticket::with('comments.user')
                ->whereIn('type',  $temp)
                ->where('team_id', $this->user->team)
                ->whereIn('status',[
                    self::NEW_TICKET,
                    self::NEED_HELP_TICKET
                ])
                ->get();
        }

        return Ticket::with('comments.user')
            ->whereIn('type',  $temp)
            ->where('team_id', $this->user->team)
            ->whereIn('status',[
                self::NEW_TICKET,
                self::NEED_HELP_TICKET,
            ])
            ->get();
    }

    public function done(Request $request){
        return Ticket::with('comments.user')
            ->where('team_id', $this->user->team)
            ->whereIn('status',[
                self::DONE_TICKET
            ])
            ->get();
    }

    public function doneByMe(Request $request){
        return $this->user->tickets()
            ->with('comments.user')
            ->where('team_id', $this->user->team)
            ->whereIn('status',[
                self::DONE_TICKET
            ])
            ->get();
    }

    public function inProgress(Request $request){
        return $this->user->tickets()
            ->with('comments.user')
            ->where('team_id', $this->user->team)
            ->whereIn('status',[
                self::TICKET_IN_PROGRESS,
                self::NEED_HELP_TICKET,
                self::FINISHED_TICKET
            ])
            ->get();
    }

    public function closed(Request $request){
        return Ticket::with('comments.user')
            ->where('team_id', $this->user->team)
            ->whereIn('status',[
                self::CLOSED
            ])
            ->get();
    }

    public function closedMy(Request $request){
        return $this->user->tickets()
            ->with('comments.user')
            ->where('team_id', $this->user->team)
            ->whereIn('status',[
                self::CLOSED
            ])
            ->get();
    }

    public function create(Request $request){
        $ticket = Ticket::create($request->all());
        $ticket->team_id = $this->user->team;
        $ticket->save();
        return $ticket;
    }

    public function take(Request $request){
        $ticket = Ticket::find($request->ticket_id);
        $this->user->tickets()->save($ticket);
        $ticket->status = self::TICKET_IN_PROGRESS;
        $ticket->save();
        return $ticket;
    }

    public function close(Request $request){
        $ticket = Ticket::find($request->ticket_id);
        $ticket->status = self::CLOSED;
        $ticket->save();
        return $ticket;
    }

    public function finish(Request $request){
        $ticket = Ticket::find($request->ticket_id);
        $ticket->status = self::FINISHED_TICKET;
        $ticket->save();
        return $ticket;
    }

    public function setDone(Request $request){
        $ticket = Ticket::find($request->ticket_id);
        $ticket->status = self::DONE_TICKET;
        $ticket->save();
        return $ticket;
    }

    public function askHelp(Request $request){
        $ticket = Ticket::find($request->ticket_id);
        $ticket->status = self::NEED_HELP_TICKET;
        $ticket->save();
        return $ticket;
    }


    public function addComment(Request $request){
        $ticket = Ticket::find($request->ticket_id);
        $comment = Comment::create($request->all());
        $ticket->comments()->save($comment);
        return $ticket;
    }

    public function getTypes(Request $request){
        return Type::all();
    }

    public function getMyTypes(Request $request){
        return $this->user->types;
    }

}
