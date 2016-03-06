<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiAuth
{
    public function handle($request, Closure $next)
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()){
                return response()->json(['user_not_found'], 404);
            }
        }catch(\Tymon\JWTAuth\Exceptions\TokenExpiredException $e){
            return response()->json(['token_expired'], $e->getStatusCode());
        }catch(\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
            return response()->json(['token_invalid'], $e->getStatusCode());
        }catch(\Tymon\JWTAuth\Exceptions\JWTException $e){
            return response()->json(['token_invalid'], $e->getStatusCode());
        }
        return $next($request);
    }
}
