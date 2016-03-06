<?php

namespace App\Api;

use App\Activity;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ActivitiesController extends Controller
{
    public function create(Request $request){
        $activity = Activity::create($request->all());
        if($request->schedule_time)
            $activity->schedule_time = Carbon::createFromFormat('m/d/Y g:i A', $request->schedule_time)->toDateTimeString();
        else
            $activity->schedule_time = Carbon::now()->addDays(3)->toDateTimeString();
        $activity->save();
        $lead = $activity->lead;
        if($lead->next_action < $activity->schedule_time){
            $lead->next_action = $activity->schedule_time;
            $lead->save();
        }


        return $activity;
    }
}
