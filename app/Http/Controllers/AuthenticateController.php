<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

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

        return [
            'token'=> $token,
            'user' => $user,
            'can_create' => boolval($user->leader)
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