<?php

use Illuminate\Http\Request;

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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
Route::get('users','UserController@index');
Route::post('user','UserController@store');
Route::delete('user/{id}','UserController@destroy');
Route::post('login','UserController@login');
Route::get('user/promote/{id}','UserController@promote');
Route::post('user/interested','InterestController@interested');
Route::post('user/uninterested','InterestController@uninterested');

Route::get('events','EventController@index');
Route::get('event/{id}','EventController@show');
Route::post('event','EventController@store');
Route::post('event/{id}','EventController@update');
Route::delete('event/{id}/{userMail}','EventController@destroy');

Route::post('comment','CommentController@store');
Route::get('comment/{eventId}','CommentController@index');

Route::post('grade','GradeController@store');