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


Route::get('/', function () {
    return view('welcome');
});

// 发送邮件
Route::get('sendVerifyEmail', 'EmailController@sendVerifyEmail')->middleware(['throttle:3']);
Route::post('register', 'UserController@register')->middleware(['throttle:10', 'email']);

Route::group(['middleware' => ['api']], function($api) {
	// statistics.frezc.com
	Route::group(['middleware' => 'statistics'], function ($api) {
		Route::get('bgm_info/{id}', 'AnimeStatisticsController@showBgmInfo')
	    ->where('id','[0-9]+');
		Route::get('relate_info/{id}', 'AnimeStatisticsController@showRelateInfo')
		    ->where('id','[0-9]+');
		Route::group(['middleware' => 'throttle:3'], function ($api) {
		    Route::get('anime_rank', 'AnimeStatisticsController@getAnimeRank');
		});
		Route::get('fetchAnimelist', 'CrawlController@fetchAnimelist');
	});

	// 测试用
	Route::get('user/{id}', 'UserController@show')->where('id', '[0-9]+');

	// general
	Route::post('auth', 'AuthenticateController@authenticate');
	Route::get('refresh', 'AuthenticateController@refresh');
	Route::get('unauth', 'AuthenticateController@unauth');

	// TodoLite
	Route::get('todolist', 'UserController@todolist')->where('id', '[0-9]+');
	Route::get('todos/{id}', 'TodoController@show')->where('id', '[0-9]+');
	Route::post('todos/{id}', 'TodoController@update')->where('id', '[0-9]+');
	Route::post('todos', 'TodoController@store');
	Route::post('todos/{id}/finish', 'TodoController@finish')->where('id', '[0-9]+');
	Route::post('todos/{id}/layside', 'TodoController@layside')->where('id', '[0-9]+');
});

