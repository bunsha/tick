<?php

$api = app('Dingo\Api\Routing\Router');



$api->version('v1', function ($api) {
    $api->post('login', 'App\Http\Controllers\AuthenticateController@authenticate');
    $api->post('signUp', 'App\Http\Controllers\AuthenticateController@signUp');
});


$api->version('v1', ['middleware' => 'App\Http\Middleware\ApiAuth'], function ($api){
    $api->post('tickets/my', 'App\Http\Controllers\TicketsController@getMy');
});


Route::get('login', 'AuthController@login');
Route::get('register', 'AuthController@register');