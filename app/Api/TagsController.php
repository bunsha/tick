<?php

namespace App\Api;

use App\Answer;
use App\Lead;
use App\Tag;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class TagsController extends ApiController
{
    public $item;

    public function __construct(Request $request){
        parent::__construct();
        $this->item = Tag::withTrashed()->find($request->id);
        return $this;
    }

    public function checkAccount(){
        if($this->exist()){
            if($this->item->account_id === $this->accountId)
                return true;
        }
        throw new \Exception(Lang::get('api.tagNotFound'));
    }

    public function exist(){
        if($this->item === null){
            throw new \Exception(Lang::get('api.tagNotFound'));
        }
        return true;
    }

    /*
    * Create a tag and attach it to given lead
    * @param Request $request
    * @request string $name
    * @request string|null $lead_id
    * @object Answer
    * @return {status_code, result:{"name", "lead_id"}}
    */
    public function create(Request $request){
        $item = Tag::create($request->all());
        try{
            $item->save();
        }catch(\Exception $e){
            $this->error[] = $e;
            return Answer::set(500, Lang::get('api.notEnoughData'));
        }
        return Answer::set(200, $item);
    }



    public function get(Request $request){
        if($this->exist())
            return Answer::set(200, $this->item);
        return Answer::set(200, Lang::get('api.tagNotFound'));
    }

    public function getAllByAccount(Request $request){

    }

    public function getAllByLead(Request $request){

    }

    public function getMy(Request $request){
        return DB::select('CALL getAllMyTags('.$this->user->id.')');
    }

    public function addTags($lead_id, Request $tags){
        foreach($tags as $tag){
            $item = Tag::firstOrNew(['name' => $tag, 'lead_id' => $lead_id]);
        }
        return \Response::make('OK', 200);
    }

    public function deleteTags($lead_id, Request $tags){
        foreach($tags as $tag){
            Tag::where('name', $tag)->where('lead_id', $lead_id)->delete();
        }
        return \Response::make('OK', 200);
    }

    public function removeTag($lead_id, $tag){
        Tag::where('name', $tag)->where('lead_id', $lead_id)->delete();
        return \Response::make('OK', 200);
    }

    public function attachToLead(Request $request){
        $tag = Tag::find($request->tag);
        $tag->lead_id = $request->lead;
        $tag->save();
        return Answer::set(200, Lang::get('api.success'));
    }
    public function detachFromToLead(Request $request){
        $tag = Tag::find($request->tag);
        $tag->lead_id = null;
        $tag->save();
        return Answer::set(200, Lang::get('api.success'));
    }
}
