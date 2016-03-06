<?php

namespace App\Api;

use App\Account;
use App\Answer;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Lang;

class AccountController extends ApiController
{
    public $item;
    public $itemUser;

    public function __construct(Request $request){
        parent::__construct();
        if($request->account_id > 0){
            $this->item = Account::withTrashed()->find($request->id);
        }
        return $this;
    }

    protected function exist(){
        if($this->item === null){
            throw new \Exception(Lang::get('api.accountNotExist'));
        }
        return true;
    }
    protected function userExist(){
        if($this->itemUser === null){
            throw new \Exception(Lang::get('api.userNotExist'));
        }
        return true;
    }
    protected function checkUser(Request $request){
        if($request->user_id == null)
            return false;
        $this->itemUser = User::find($request->user_id);
        if(!$this->userExist()){
            throw new \Exception(Lang::get('api.userNotFound'));
        }
        if($this->itemUser->account_id == null){
            return true;
        }

        else
            throw new \Exception(Lang::get('api.userNotFound'));
    }
    protected function checkUserByEmail(Request $request){
        if($request->email == null)
            return false;
        $this->itemUser = User::where('email', $request->email)->first();
        if(!$this->userExist()){
            throw new \Exception(Lang::get('api.userNotFound'));
        }
        if($this->itemUser->account_id == null)
            return true;
        else
            throw new \Exception(Lang::get('api.userNotFound'));
    }

    /**
     * Create an account.
     * @param Request $request
     * @request string $name
     * @request string|null $description
     * @request string|null $owner_title
     * @request string|null $type {personal|corporate}
     * @object Answer
     * @return {status_code, result:{name, id, type, description, created_at, updated_at}}
     */
    public function create(Request $request){
        try{
            $item = Account::create($request->all());
            $item->save();
        }catch(\Exception $e){
            $this->error[] = $e;
            return Answer::set(500, Lang::get('api.notEnoughData'));
        }
        return Answer::set(200, $item);
    }

    /**
     * Return an Account.
     * @param Request $request
     * @request int $id
     * @object Answer
     * @return {status_code, result:{name, id, type, description, created_at, updated_at}}
     */
    public function get(){
        if($this->account)
            return Answer::set(200, $this->account);
        return Answer::set(500, Lang::get('api.internal'));
    }

    /**
     * Edit an account.
     * @param Request $request
     * @request int $id
     * @request string $name
     * @request string|null $description
     * @request string|null $type {personal|corporate}
     * @object Answer
     * @return {status_code, result:{name, id, type, description, created_at, updated_at}}
     */
    public function edit(Request $request){
        if(isset($this->account))
            try{
                $this->account->update($request->all());
            }catch (\Exception $e){
                $this->error[] = $e;
                return Answer::set(500, Lang::get('api.notEnoughData'));
            }
        return Answer::set(200, $this->account);
    }

    /**
     * Delete an Account.
     * @param Request $request
     * @object Answer
     * @return {status_code, result}
     */
    public function delete(){
        if(isset($this->account)){
            try{
                $this->account->delete();
            }catch (\Exception $e){
                $this->error[] = $e;
                return Answer::set(500, Lang::get('api.internal'));
            }
            return Answer::set(200, Lang::get('api.deleted'));
        }
        return Answer::set(200, Lang::get('api.notExist'));
    }

    /**
     * Restore an Account.
     * @param Request $request
     * @object Answer
     * @return {status_code, result}
     */
    public function restore(){
        if(!isset($this->account)){
            try{
                $account = Account::withTrashed()->find($this->user->account_id);
                $account->restore();
                $this->account = $account;
            }catch (\Exception $e){
                $this->error[] = $e;
                return Answer::set(500, Lang::get('api.internal'));
            }
        }
        return Answer::set(200, Lang::get('api.restored'));
    }

    /**
     * Returns list of users attached to this account
     * @param Request $request
     * @object Answer
     * @return {status_code, result:{id:{id, first_name, last_name, email, account_id, parent_id, username, children{id:{id, first_name, last_name, email, account_id, parent_id, username, children{}}}}}}
     */
    public function getAllUsers(){
        if(isset($this->account))
            return Answer::set(200, $this->user->account->users->toHierarchy());
        return Answer::set(500, Lang::get('api.internal'));
    }

    /**
     * Returns Account info with roles->permissions and activiteSettings
     * @param Request $request
     * @object Answer
     * @return {status_code, result:{BIG JSON}
     */
    public function info(){
        $account = Account::find($this->accountId)
            ->with('roles.permissions', 'activitySettings.pitches')
            ->with([
                'activitySettings.elevationItems' => function ($query){
                    $query->where('type', 'elevation');
                }
            ])
            ->with([
                'activitySettings.salesItems' => function ($query){
                    $query->where('type', 'sales');
                }
            ])
            ->with([
                'activitySettings.leadDataItems' => function ($query){
                    $query->where('type', 'lead_data');
                }
            ])
            ->get();
        return $account;
    }

    /**
     * Attach User to auth based Account
     * @param Request $request
     * @request int|null $user_id
     * @request string|null $email
     * @object Answer
     * @return {status_code, result:{BIG JSON}
     */
    public function attachUser(Request $request){
        if(isset($this->account)){
            if($this->checkUserByEmail($request) OR $this->checkUser($request)){
                try{
                    $this->itemUser->account_id = $this->account->id;
                    $this->itemUser->save();
                }catch (\Exception $e){
                    $this->error[] = $e;
                    return Answer::set(500, Lang::get('api.internal'));
                }
                return Answer::set(200, Lang::get('api.attached'));
            }
            return Answer::set(500, Lang::get('api.noUser'));
        }
        return Answer::set(500, Lang::get('api.noAccount'));
    }

    /**
     * Detach User from auth based Account
     * @param Request $request
     * @request int $user_id
     * @object Answer
     * @return {status_code, result:{BIG JSON}
     */
    public function detachUser(Request $request){
        if(isset($this->account) && $this->user->id != $request->user_id){
            try{
                $this->itemUser = User::where('account_id', $this->account->id)->where('id', $request->user_id)->first();
                if($this->itemUser !== null){
                    $this->itemUser->account_id = null;
                    $this->itemUser->save();
                    return Answer::set(200, Lang::get('api.detached'));
                }else{
                    return Answer::set(500, Lang::get('api.noUser'));
                }
            }catch (\Exception $e){
                $this->error[] = $e;
                return Answer::set(500, 'api.internal');
            }
        }elseif($this->user->depth >  0){
            return Answer::set(409, Lang::get('api.youCannotDetachYourParent'));
        }
        return Answer::set(409, Lang::get('api.youCannotDetachYourself'));
    }
}