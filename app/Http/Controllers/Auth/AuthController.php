<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Request;
use App\User;
use Dingo\Api\Contract\Http\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    protected $redirectTo = '/';

    public function signUp(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        try{
            $user = User::create($request->all());
        }catch(\Exception $e){
            if($e->getCode() == 23000)
                return Answer::set(409, Lang::get('api.alreadyExiist'));
            else
                return Answer::set(400, Lang::get('api.notEnoughData'));
        }
        $token = JWTAuth::fromUser($user);
        return Answer::set(200, 'success');
    }
}
