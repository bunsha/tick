<?php

namespace App\Api;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use App\Answer;
use App\Http\Requests;
use Illuminate\Support\Facades\Lang;

class RolesController extends ApiController
{
    public $item;

    public function __construct(Request $request){
        parent::__construct();
        $this->item = Role::withTrashed()->find($request->id);
        return $this;
    }

    protected function checkAccount(){

            return true;
        throw new \Exception(Lang::get('api.roleNotFound'));
    }

    public function exist(){
        if($this->item === null){
            throw new \Exception(Lang::get('api.roleNotFound'));
        }
        return true;
    }

    /**
    * Create a role and attach it to account_id of logged in user.
    * @param Request $request
    * @request string $name
    * @request string|null $plural
    * @request string|null $description
    * @request string|null $display_name
    * @object Answer
    * @return {status_code, result:{"name", "plural", "display_name", "description", "account_id", "id"}}
    */
    public function create(Request $request){
        $item = Role::create($request->all());
        $item->account_id = $this->getUser()->account->id;
        try{
            $item->save();
        }catch(\Exception $e){
            $this->error[] = $e;
            return Answer::set(500, Lang::get('api.notEnoughData'));
        }
        return Answer::set(200, Role::find($item->id));
    }

    /**
     * Create a role with permissions and attach it to account_id of logged in user.
     * @param Request $request
     * @request string $name
     * @request string|null $plural
     * @request array|null $permissions
     * @request string|null $description
     * @request string|null $display_name
     * @object Answer
     * @return {status_code, result:{"name", "plural", "display_name", "description", "account_id", "id", "permissions"}}
     */
    public function createWithPermissions(Request $request){
        $parent = Role::find($request->parent_id);
        $item = Role::create($request->all());
        $item->display_name = $request->name;
        $item->name = htmlspecialchars(trim($request->name));
        $item->account_id = $this->getUser()->account->id;
        $item->makeChildOf($parent);
        if($request->secretary != 'false'){
            $item->secretary = 1;
        }else{
            $item->secretary = 0;
        }
        try{
            $item->save();
            $item->permissions()->sync($request->permissions);
        }catch(\Exception $e){
            $this->error[] = $e;
            return Answer::set(500, Lang::get('api.notEnoughData'));
        }
        return Answer::set(200, Role::find($item->id));
    }

    /**
    * Create a bunch of roles and attach to account_id of logged in user.
    * @param Request $request
    * @request array $roles ⇊
    *      @request string $name
    *      @request string|null $plural
    *      @request string|null $description
    *      @request string|null $display_name
    * @object Answer
    * @return {status_code, result:{"name", "plural", "display_name", "description", "account_id", "id"}}
    */
    public function bulkCreate(Request $request){
        foreach($request->roles as $role){
            $this->create($role);
        }
        return Answer::set(200, Lang::get('api.saved'));
    }

    /**
    * Return a role.
    * @param Request $request
    * @request int $id
    * @object Answer
    * @return {status_code, result:{"name", "plural", "display_name", "description", "account_id", "id"}}
    */
    public function get(Request $request){
        if($this->checkAccount())
            return Answer::set(200, $this->item);
        return Answer::set(200, Lang::get('api.roleNotFound'));
    }

    /**
    * Return a role with permissions.
    * @param Request $request
    * @request int $id
    * @object Answer
    * @return {status_code, result:{"name", "plural", "display_name", "description", "account_id, id, "permissions":{id, name, display_name, description}d", "id"}}
    */
    public function getWithPermissions(Request $request){
        if($this->checkAccount())
            return $this->item;
            return Answer::set(200, $this->item->with('permissions')->find($request->id));
        return Answer::set(200, Lang::get('api.roleNotFound'));
    }
    /**
    * Delete a role.
    * @param Request $request
    * @request int $id
    * @object Answer
    * @return {status_code, result}
    */
    public function delete(Request $request){
        if($this->checkAccount())
        try{
            $this->item->delete();
        }catch (\Exception $e){
            $this->error[] = $e;
            return Answer::set(500, Lang::get('api.internal'));
        }
        return Answer::set(200, Lang::get('api.deleted'));
    }

    /**
    * Restore a role.
    * @param Request $request
    * @request int $id
    * @object Answer
    * @return {status_code, result}
    */
    public function restore(Request $request){
        if($this->checkAccount())
        try{
            $this->item->restore();
        }catch (\Exception $e){
            $this->error[] = $e;
            return Answer::set(500, Lang::get('api.internal'));
        }
        return Answer::set(200, Lang::get('api.restored'));
    }

    /**
    * Attach a role to specified account.
    * @param Request $request
    * @request int $id
    * @request int $account_id
    * @object Answer
    * @return {status_code, result}
    */
    public function attachToAccount(Request $request){
        $this->item->account_id = $request->account_id;
        try{
            $this->item->save();
        }catch (\Exception $e) {
            return Answer::set(200, Lang::get('api.accountNotExist'));
        }
        return Answer::set(200, Lang::get('api.attached'));
    }

    /**
    * Detach a role from specified account.
    * @param Request $request
    * @request int $id
    * @object Answer
    * @return {status_code, result}
    */
    public function detachToAccount(Request $request){
        $this->item->account_id = null;
        $this->item->save();
        return Answer::set(200, Lang::get('api.detached'));
    }

    /**
    * Edit a role.
    * @param Request $request
    * @request string $name
    * @request string|null $plural
    * @request string|null $description
    * @request string|null $display_name
    * @object Answer
    * @return {status_code, result:{"name", "plural", "display_name", "description", "account_id", "id"}}
    */
    public function edit(Request $request){
        if($this->checkAccount())
        try{
            $this->item->update($request->all());
        }catch (\Exception $e){
            $this->error[] = $e;
            return Answer::set(500, Lang::get('api.notEnoughData'));
        }
        return Answer::set(200, $this->item);
    }

    /**
    * Get all permissions of the specified role.
    * @param Request $request
    * @request int $id
    * @object Answer
    * @return {status_code, result:{id, name, display_name, description}}
    */
    public function getPermissions(){
        if($this->item)
            return Answer::set(200, $this->item->permissions);
        return Answer::set(200, Lang::get('api.roleNotFound'));
    }

    /**
    * Attach specified permission of the specified role.
    * @param Request $request
    * @request int $id
    * @request int $permission_id
    * @object Answer
    * @return {status_code, result}
    */
    public function attachPermission(Request $request){
        if($this->checkAccount())
        try{
            $this->item->permissions()->attach($request->permission_id);
        }catch (\Exception $e) {
            return Answer::set(200, Lang::get('api.alreadyAttached'));
        }
        return Answer::set(200, Lang::get('api.attached'));
    }

    /**
    * Detach specified permission of the specified role.
    * @param Request $request
    * @request int $id
    * @request int $permission_id
    * @object Answer
    * @return {status_code, result}
    */
    public function detachPermission(Request $request){
        if($this->checkAccount())
        try{
            $this->item->permissions()->detach($request->permission_id);
        }catch (\Exception $e) {
            return Answer::set(200, Lang::get('api.alreadyDetached'));
        }
        return Answer::set(200, Lang::get('api.detached'));
    }

    /**
     * Detach specified permission of the specified role.
     * @param Request $request
     * @request int $id
     * @request int $permission_id
     * @object Answer
     * @return {status_code, result}
     */
    public function getMy(Request $request){
        if($this->checkAccount())
            try{
                return User::where('id', $this->user->id)->with('roles.permissions')->get();
            }catch (\Exception $e) {
                return Answer::set(200, Lang::get('api'));
            }
        return Answer::set(200, Lang::get('api.detached'));
    }

    public function getAccountRoles(Request $request){
        if($this->checkAccount())
            try{
                return Role::where('account_id', $this->user->account_id)->get()->toHierarchy();
            }catch (\Exception $e) {
                return Answer::set(200, Lang::get('api.alreadyDetached'));
            }
        return true;
    }

    public function getAccountRolesFlat(Request $request){
        if($this->checkAccount())
            try{
                return Role::where('account_id', $this->user->account_id)->get();
            }catch (\Exception $e) {
                return Answer::set(200, Lang::get('api.alreadyDetached'));
            }
        return true;
    }



    public function createFromSetup(Request $request){
        $account =  $this->getUser()->account->id;
        function saveChildren($obj, $account){
            if(!empty($obj['children'])){
                foreach($obj['children'] as $child){
                    if(isset($child['is_secretary']))
                        $secretary = $child['is_secretary'];
                    else
                        $secretary = null;
                    $item = Role::create([
                        'name' => htmlspecialchars(trim($child['title'])),
                        'display_name' => $child['title'],
                        'plural' => $child['plural'],
                        'description' => $child['description'],
                        'secretary' => $secretary,
                        'account_id' => $account
                    ]);
                    $item->save();
                    $item->permissions()->sync(explode(',', $child['permissions']));
                    saveChildren($child, $account);
                }
            }else{
                return;
            }
            return;
        };
        $items = $request->data;
        foreach($items as $item){
            saveChildren($item, $account);
        }
    }

    public function createParent(Request $request){
        $item = Role::where('account_id', $this->getUser()->account->id)
            ->where('parent_id', null)
            ->first();

        if($item){
            $item->display_name = $request->name;
            $item->name = htmlspecialchars(trim($request->name));
            $item->makeRoot();
            $item->save();
        }else{
            $item = Role::create([
                'name' => htmlspecialchars(trim($request->name)),
                'display_name' => $request->name,
                'plural' => null,
                'description' => null,
                'secretary' => 0,
                'account_id' => $this->getUser()->account->id
            ]);
            $item->makeRoot();
        }
        $item->makeRoot();
        $this->user->roles()->sync([$item->id]);
        try{
            $item->save();
        }catch(\Exception $e){
            $this->error[] = $e;
            return Answer::set(500, Lang::get('api.notEnoughData'));
        }
        return Answer::set(200, $item);
    }

    public function maxDepth(Request $request){
        return \DB::table('roles')->where('account_id', $request->account_id)->max('depth');
    }
}
