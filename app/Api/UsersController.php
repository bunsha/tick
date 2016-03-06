<?php

namespace App\Api;

use App\Answer;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use Illuminate\Support\Facades\Lang;

class UsersController extends ApiController
{

    private function _mapTree($src_arr, $parent_id = 0, $tree = array())
    {
        foreach ($src_arr as $idx => $row) {
            if ($row['parent_id'] == $parent_id) {
                foreach ($row as $k => $v)
                    $tree[$row['id']][$k] = $v;
                unset($src_arr[$idx]);
                $tree[$row['id']]['children'] = $this->_mapTree($src_arr, $row['id']);
            }
        }
        ksort($tree);
        return $tree;
    }


    public function makeChild(Request $request)
    {
        $child = User::find($request->child_id);
        $parent = User::find($request->parent_id);
        $child->makeChildOf($parent);
        return \Response::make([$child, $parent], 200);
    }

    public function create(Request $request)
    {
        if(User::where('email', $request->email)->first()){
            return Answer::set(500, 'Email is already in use');
        }else {
            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->account_id = $request->account_id;
            $user->paid = null;
            $user->parent_id = $request->parent_id;
            $user->save();
            return $user;
        }
    }

    public function update(Request $request)
    {
        $user = User::find($request->id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->account_id = $request->account_id;
        $user->paid = null;
        $user->parent_id = $request->parent_id;
        $user->save();
        return \Response::make($user, 200);
    }

    public function pay(Request $request)
    {
        $user = User::find($request->id);
        $user->paid = $request->paid;
        $user->save();
    }

    public function delete(Request $request)
    {
        $user = User::find($request->id);
        $user->delete();
    }

    /**
     * Get User by User.
     * @return User
     */
    public function me()
    {
        return Answer::set(200, $this->user);
    }

    public function bulkSave(Request $request){
        $data = json_decode($request->data);
        foreach($data as $parent){
            $root = User::find($parent->id);
            $this->parseArray($parent);
        }

    }

    public function parseArray($array){
        if(isset($array->children)){
            $root = User::find($array->id);
            foreach($array->children as $child){
                $item = User::find($child->id);
                $item->order = $array->order;
                $item->makeChildOf($root);
                $item->save();
                $this->parseArray($child);
            }
        }
    }

    public function resetTeam(Request $request){
        $users = User::where('account_id', $this->accountId)->where('parent_id', '>', 0)->get();
        $parent = User::where('account_id', $this->accountId)->where('parent_id', null)->first();
        foreach($users as $user){
            $user->parent_id = $parent->id;
            $user->save();
        }
        User::rebuild();
    }

    public function syncRole(Request $request){
        $user = User::find($request->user);
        $user->roles()->sync([$request->role]);
        return $user->roles;
    }
}