<?php

namespace App\Api;

use App\Address;
use App\Contact;
use App\EntityData;
use App\Phone;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ContactsController extends Controller
{
    public function attachContacts(Request $request){
        $data = [];
        foreach($request->contacts as $contact){
            $data[] = [
                'first_name' => $contact->first_name,
                'last_name' => $contact->last_name,
                'type' => $contact->type,
                'entity_id' => $contact->entity_id,
                'title' => $contact->title,
                'email' => $contact->email
            ];
        }
        Contact::insert($data);
        return \Response::make('OK', 200);
    }

    public function detachAllContacts(Request $request){
        Contact::where('entity_id', $request->entity_id)
            ->update(['entity_id' => null]);
        return \Response::make('OK', 200);
    }

    public function detachContact(Request $request){
        Contact::where('id', $request->id)
            ->update(['entity_id' => null]);
        return \Response::make('OK', 200);
    }

    public function deleteAllContacts(Request $request){
        $items = Contact::where('entity_id', $request->entity_id)->delete();
        return \Response::make('OK', 200);
    }

    public function reattachContacts(Request $request){
        $this->detachAllContacts($request);
        $this->attachContacts($request);
        return \Response::make('OK', 200);
    }

    public function getAllContacts($entity_id){
        return Contact::where('entity_id', $entity_id)->with('addresses.phones')->get();
    }

    public function getContact($id){
        return Contact::where('id', $id)->with('addresses.phones')->get();
    }

    public function changeContactInfo(Request $request){
        if($request->type == 'contact'){
            $item = Contact::find($request->entity);
            if($request->name == 'first_name')
                $item->first_name = $request->value;
            elseif($request->name == 'last_name')
                $item->last_name = $request->value;
            elseif($request->name == 'email')
                $item->email = $request->value;
            elseif($request->name == 'title')
                $item->title = $request->value;
            else{}
            $item->save();
        }elseif($request->type == 'address'){
            $item = Address::find($request->entity);
            $item->full_string = $request->value;
            $item->save();
            return $request->all();
        }elseif($request->type == 'phone'){
            $item = Phone::find($request->entity);
            if($request->name == 'type')
                $item->type = $request->value;
            elseif($request->name == 'number')
                $item->number = $request->value;
            elseif($request->name == 'from')
                $item->available_from = $request->value;
            elseif($request->name == 'to')
                $item->available_to = $request->value;
            else{}
            $item->save();
            return $request->all();
        }else{

        }
    }

    public function changeLeadInfo(Request $request){
        $item = EntityData::find($request->entity);
        $item->value = $request->value;
        $item->save();
    }
}
