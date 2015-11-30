<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
// Route::resource('user', 'UserController');
// // Route::post('auth', 'AuthenticateController@authenticate');
// Route::post('auth', function(Request $request){
//   require 'auth';
// });


Route::get('/', function () {
    return view('welcome');
});

$api = app('Dingo\Api\Routing\Router');

$api -> version('v1',  function($api){
  // $api->get('users', 'App\Http\Controllers\AuthenticateController@index');
  $api->get('bgm_info/{id}', 'App\Http\Controllers\AnimeStatisticsController@showBgmInfo')
      ->where('id','[0-9]+');
});
