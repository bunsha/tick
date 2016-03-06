<?php

namespace App\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;

class AuthenticateController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if(! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        }catch(JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        $user = User::where('email', $request->email)->first();
        $permissions = [];
        foreach($user->roles as $role){
            foreach($role->permissions as $permission){
                $permissions[] = $permission->name;
            }
        }
        $leadsCount = DB::select('CALL getUserLeadsCount('.$user->id.', '.$user->lft.', '.$user->rgt.')');
        return [
            'token'=> $token,
            'user' => $user->with('account')->get(),
            'account' => $user->account,
            'children' => $user->getDescendants(),
            'roles' => $user->roles,
            'permissions' => $permissions,
            'leads_count' => $leadsCount,
        ];
    }

    public function getAuthenticatedUser()
    {
        try {
            if(! $user = JWTAuth::parseToken()->authenticate()){
                return response()->json(['user_not_found'], 404);
            }
        }catch(\Tymon\JWTAuth\Exceptions\TokenExpiredException $e){
            return response()->json(['token_expired'], $e->getStatusCode());
        }catch(\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
            return response()->json(['token_invalid'], $e->getStatusCode());
        }catch(\Tymon\JWTAuth\Exceptions\JWTException $e){
            return response()->json(['token_absent'], $e->getStatusCode());
        }
        return response()->json(compact('user'));
    }
}