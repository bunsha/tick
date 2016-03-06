<?php

namespace App\Api;

use App\Account;
use App\Activity;
use App\ActivitySetting;
use App\Address;
use App\Classes\Socket\Pusher;
use App\Contact;
use App\Entity;
use App\EntityData;
use App\Phone;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Lead;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class LeadsController extends ApiController
{
    public function createLead(Request $request){
        $lead = new Lead();
        $lead->name = $request->name;
        $lead->activity_setting = $request->setting;
        if($request->next_action)
            $lead->next_action = Carbon::createFromFormat('m/d/Y g:i A', $request->next_action)->toDateTimeString();
        else
            $lead->next_action = null;
        $lead->active = 1;
        $lead->progress = 0;
        $lead->level = 0;
        $lead->save();

        $entity = new Entity();
        $entity->name = $request->entity['name'];
        $entity->type = $request->entity['type'];
        $entity->save();

        $lead->entity_id = $entity->id;
        $lead->save();

        $user = User::find($request->userId);
        $user->leads()->attach($lead);
        $account = Account::find($user->account_id);
        $account->entities()->attach($entity);

        foreach($request->entity['entity_data'] as $eData){
            $entityData = new EntityData();
            $entityData->key = $eData['key'];
            $entityData->value = $eData['value'];
            $entityData->name = $eData['name'];
            $entityData->entity_id = $entity->id;
            $entityData->type = strtolower($eData['type']); //TODO
            $entityData->save();
        }

        foreach($request->entity['contacts'] as $cntct){
            $contact = new Contact();
            $contact->email = $cntct['email'];
            $contact->first_name = $cntct['first_name'];
            $contact->last_name = $cntct['last_name'];
            $contact->title = $cntct['title'];
            $contact->type = $cntct['type'];
            $contact->entity_id = $entity->id;
            $contact->save();
            foreach($cntct['addresses'] as $adr){
                $address = new Address();
                $address->name = $adr['name'];
                $address->full_string = $adr['full_string'];
                $address->contact_id = $contact->id;
                $address->save();
                foreach($adr['phones'] as $ph){
                    $phone = new Phone();
                    $phone->number = $ph['number'];
                    $phone->available_from = $ph['available_from'];
                    $phone->available_to = $ph['available_to'];
                    $phone->type = $ph['type'];
                    $phone->address_id = $address->id;
                    $phone->save();
                }
            }
        }

        $activity = new Activity();
        $activity->lead_id = $lead->id;
        $activity->user_id = $user->id;
        $activity->type = 'info';
        $activity->status = 'done';
        $activity->name = 'Created';
        $activity->visible = 1;
        $activity->note = $request->note;
        $activity->schedule_time = Carbon::now()->toDateTimeString();
        $activity->save();
    }

    public function addLeadWithSelectedEntity(Request $request){
        $lead = new Lead();
        $lead->name = $request->name;
        $lead->activity_setting = $request->setting;
        $lead->active = 1;
        $lead->progress = 0;
        $lead->level = 0;
        if($request->next_action)
            $lead->next_action = Carbon::createFromFormat('m/d/Y g:i A', $request->next_action)->toDateTimeString();
        else
            $lead->next_action = Carbon::now()->toDateTimeString();
        $lead->entity_id = $request->entity_id;
        $lead->save();

        $user = User::find($this->user->id);
        $user->leads()->attach($lead);
        $account = Account::find($user->account_id);
        $entity = Entity::find($request->entity_id);
        $account->entities()->attach($entity);


        $activity = new Activity();
        $activity->lead_id = $lead->id;
        $activity->user_id = $user->id;
        $activity->type = 'info';
        $activity->status = 'done';
        $activity->name = 'Created';
        $activity->visible = 1;
        $activity->note = $request->note;
        $activity->schedule_time = Carbon::now()->toDateTimeString();
        $activity->save();
        return $lead;
    }

    public function attachUsersToLead(Request $request){
        $item = Lead::find($request->lead_id);
        $item->users()->sync($request->users, false);
        return \Response::make('OK', 200);
    }

    public function reattachUsersToLead(Request $request){
        $item = Lead::find($request->lead_id);
        $item->users()->sync($request->users);
        return \Response::make('OK', 200);
    }

    public function getLead(Request $request){
        $lead =  Lead::with(
            'entity.entity_data',
            'entity.address',
            'entity.contacts.phones',
            'tags'
        )
            ->with(['activities' => function($query){
                $query->orderBy('created_at', 'desc');
            }])
            ->orderBy('next_action', 'asc')
            ->find($request->id);
        $lead->freezed = 1;
        $lead->freezed_by = $this->user->id;
        $lead->save();
        $data = [
            'topic_id' => 'leads',
            'data' => 'User '.$this->user->email.' Is working on lead '.$lead->id.' You cannot work on it now.'
        ];
        Pusher::sendDataToServer($data);

        return $lead;
    }
    public function unfreeze(Request $request){
        $lead = Lead::find($request->id);
        $lead->freezed = null;
        $lead->freezed_by = null;
        $lead->save();
    }


    public function getMy(Request $request){
        $users = $this->_getUserChildren($this->user->id);
        $leadIds = [];
        $temp = \DB::table('lead_user')->select('lead_id')->whereIn('user_id', $users->original)->get();
        foreach($temp as $i){
            $leadIds[] = $i->lead_id;
        }
        if(!isset($request->limit)){
            $limit = 999999;
        }else{
            $limit = $request->limit;
        }
        if($request->active){
            $active = 1;
        }else{
            $active = 0;
        }
        $query =  \DB::select('
            call getMyLeads("'.implode(", ", $users->original).'", '.$limit.', "'.$request->exclude.'", "'.$active.'")'
        );
        $i=0;
        echo '[';
        foreach($query as $lead){
            $i++;
            if($i == 1)
                echo $lead->json;
            else
                echo ', '.$lead->json;
        }
        echo ']';
    }

    public function getMyWithTags(Request $request){
        $users = $this->_getUserChildren($this->user->id);
        $leadIds = [];
        $temp = \DB::table('lead_user')->select('lead_id')->whereIn('user_id', $users->original)->get();
        foreach($temp as $i){
            $leadIds[] = $i->lead_id;
        }
        if(!isset($request->limit)){
            $limit = 999999;
        }else{
            $limit = $request->limit;
        }
        if($request->active){
            $active = 1;
        }else{
            $active = 0;
        }
        $query =  \DB::select('
            call getMyLeadsWithTags("'.implode(", ", $users->original).'", '.$limit.', "'.$request->exclude.'", "'.$active.'", "'.$request->tags.'")'
        );
        $i=0;

        //print_r($query);
        echo '[';
        foreach($query as $lead){
            $i++;
            if($i == 1)
                echo "{";
            else
                echo ",{";
            echo
            '"id": '.$lead->id.''.
			',"name": "'.$lead->name.'"'.
			',"progress": "'.$lead->progress.'"'.
			',"level": "'.$lead->level.'"'.
            ',"entity_id": "'.$lead->entity_id.'"'.
            ',"created_at": "'.$lead->created_at.'"'.
			',"next_action": "'.$lead->next_action.'"'.
			',"status": "'.$lead->status.'"'.
			',"activity_setting": '.$lead->activity_setting.''.
            ',"tags":['.$lead->theTags.']'.''.
            ',"activities":['.$lead->activities.']';
            echo "}";

        }
        echo ']';
    }



    //**********Activities**********//

    public function createActivity(Request $request){
        $activity = new Activity();
        $activity = Activity::create($request->all());
        if($request->schedule_time)
            $activity->schedule_time = Carbon::createFromFormat('m/d/Y g:i A', $request->schedule_time)->toDateTimeString();
        else
            $activity->schedule_time = Carbon::now()->addDays(2)->toDateTimeString();
        $activity->save();
        $lead = $activity->lead;
        if($lead->next_action < $activity->schedule_time){
            $lead->next_action = $activity->schedule_time;
            $lead->save();
        }

//        $activity->type = $request->type;
//        $activity->user_id = $this->user->id;
//        $activity->note = $request->note;
//        $activity->status = $request->status;
//        $activity->name = $request->name;
//        if(isset($request->schedule_time))
//            $activity->schedule_time = $request->schedule_time;
//        else
//            $activity->schedule_time = Carbon::now()->addDays(2)->toDateTimeString();
//        $lead->activities()->save($activity);

    }



    public function getSettings(Request $request){
        $settings = $this->user->account->activitySettings()->with([
            'elevationItems' => function ($query){
                $query->where('type', 'elevation');
            }
        ])
            ->with([
                'salesItems' => function ($query){
                    $query->where('type', 'sales');
                }
            ])
            ->with([
                'leadDataItems' => function ($query){
                    $query->where('type', 'lead_data');
                }
            ])

            ->get();
        return $settings;
    }
}
