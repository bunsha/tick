<?php

$api = app('Dingo\Api\Routing\Router');



$api->version('v1', function ($api) {
    $api->post('login', 'App\Http\Controllers\AuthenticateController@authenticate');
    $api->post('signUp', 'App\Http\Controllers\AuthController@signUp');
});


$api->version('v1', ['middleware' => 'App\Http\Middleware\ApiAuth'], function ($api){
    $api->post('tickets/types', 'App\Http\Controllers\TicketsController@getTypes');
    $api->post('tickets/types/my', 'App\Http\Controllers\TicketsController@getMyTypes');

    $api->post('tickets/my', 'App\Http\Controllers\TicketsController@getMy');
    $api->post('tickets/get', 'App\Http\Controllers\TicketsController@get');
    $api->post('tickets/progress', 'App\Http\Controllers\TicketsController@inProgress');
    $api->post('tickets/doneByMe', 'App\Http\Controllers\TicketsController@doneByMe');
    $api->post('tickets/closedMy', 'App\Http\Controllers\TicketsController@closedMy');

    $api->post('tickets/take', 'App\Http\Controllers\TicketsController@take');
    $api->post('tickets/finish', 'App\Http\Controllers\TicketsController@finish');
    $api->post('tickets/askHelp', 'App\Http\Controllers\TicketsController@askHelp');


    $api->post('tickets/addComment', 'App\Http\Controllers\TicketsController@addComment');

});

$api->version('v1', ['middleware' => 'App\Http\Middleware\Leader'], function ($api){
    $api->post('tickets', 'App\Http\Controllers\TicketsController@getAll');
    $api->post('tickets/done', 'App\Http\Controllers\TicketsController@done');
    $api->post('tickets/closed', 'App\Http\Controllers\TicketsController@closed');

    $api->post('tickets/create', 'App\Http\Controllers\TicketsController@create');
    $api->post('tickets/close', 'App\Http\Controllers\TicketsController@close');
    $api->post('tickets/setDone', 'App\Http\Controllers\TicketsController@setDone');
});





Route::get('login', 'AuthController@login');
Route::get('register', 'AuthController@register');