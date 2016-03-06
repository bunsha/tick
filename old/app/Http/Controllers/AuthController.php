<?php

namespace App\Http\Controllers;

use App\Answer;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register()
    {
        return view('auth/register');
    }

    public function login()
    {
        return view('auth/login');
    }

    public function signIn()
    {
        return view('auth/login');
    }

    public function signUp(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->team_id = $request->team_id;
        $user->status = $request->status;
        $user->parent_id = $request->parent_id;
        $user->leader = $request->leader;
        $user->team = $request->team;

        try{
            $user->save();
        }catch(\Exception $e){
            return Answer::set(500, 'notEnoughData');
        }

        $token = JWTAuth::fromUser($user);

        return [
            'token'=> $token,
            'user' => $user->with('tickets')->with('types')->get(),
            'can_create' => boolval($user->leader)
        ];
    }
}
