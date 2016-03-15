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

// $api->get('users', 'App\Http\Controllers\AuthenticateController@index');
// $api->get('test', 'App\Http\Controllers\AnimeStatisticsController@test');
Route::get('bgm_info/{id}', 'AnimeStatisticsController@showBgmInfo')
    ->where('id','[0-9]+');
Route::get('relate_info/{id}', 'AnimeStatisticsController@showRelateInfo')
    ->where('id','[0-9]+');
Route::group(['middleware' => 'throttle:3'], function ($api) {
    Route::get('anime_rank', 'AnimeStatisticsController@getAnimeRank');
});
Route::get('fetchAnimelist', 'CrawlController@fetchAnimelist');
