<?php

namespace App\Http\Controllers;

use App\Answer;
use App\User;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
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
        $user->team = $request->team_id;
        $user->status = $request->status;
        try{
            $user->save();
            $base64_str = substr($request->avatar, strpos($request->avatar, ",")+1);
            $image = base64_decode($base64_str);
            $png_url = "avatar-".$user->id.".jpg";
            $image = Image::make($image)->resize(100, 100);
            $image->save(public_path().'/avatars/'.$png_url);
            $user->avatar = $png_url;
            $user->save();
        }catch(\Exception $e){
            return $e;
        }

        $token = JWTAuth::fromUser($user);

        return [
            'token'=> $token,
            'user' => $user
        ];
    }
}
