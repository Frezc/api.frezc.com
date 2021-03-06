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
Route::group(['middleware' => ['throttle:10', 'email']], function($api) {
	Route::post('register', 'UserController@register');
	Route::post('resetPassword', 'AuthenticateController@resetPassword');
});

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

	Route::get('user/{id}', 'UserController@show')->where('id', '[0-9]+');
	Route::post('user', 'UserController@update');
	Route::post('changePassword', 'UserController@changePassword');

	// general
	Route::post('auth', 'AuthenticateController@authenticate');
	Route::get('refresh', 'AuthenticateController@refresh');
	Route::get('unauth', 'AuthenticateController@unauth');

	// TodoLite
	Route::get('todolist', 'UserController@todolist');
	Route::get('history', 'UserController@history');
	Route::get('todos/{id}', 'TodoController@show')->where('id', '[0-9]+');
	Route::post('todos/{id}', 'TodoController@update')->where('id', '[0-9]+');
	Route::post('todos', 'TodoController@store');
	Route::post('todos/{id}/finish', 'TodoController@finish')->where('id', '[0-9]+');
	Route::post('todos/{id}/layside', 'TodoController@layside')->where('id', '[0-9]+');

	// nimingban
	Route::group(['prefix' => 'nimingban'], function () {
		Route::get('id', 'NimingbanController@id');
		Route::get('branches', 'NimingbanController@getBranches');
		Route::post('branches', 'NimingbanController@createBranches');
		Route::get('branches/{id}', 'NimingbanController@branch')->where('id', '[0-9]+');
		Route::get('branches/{id}/replies', 'NimingbanController@getReplies')->where('id', '[0-9]+');
		Route::post('branches/{id}/replies', 'NimingbanController@createReply')->where('id', '[0-9]+');
	});
});

