<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'UserController@store');  

    Route::middleware('auth:api')->group(function () {
        Route::post('users/{id}', 'UserController@update');
        Route::post('users', 'UserController@delete');
        Route::get('users/{user_id}', 'UserController@show');

        Route::post('music-store', 'MusicController@store'); 
        Route::post('music/{id}', 'MusicController@update'); 
        Route::post('music', 'MusicController@delete');
        Route::get('music/{user_id}', 'MusicController@show');
    });
});