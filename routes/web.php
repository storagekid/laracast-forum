<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('home');
});

Auth::routes();

Route::get('/threads', 'ThreadController@index')->name('home');
Route::get('/threads/create', 'ThreadController@create');
Route::delete('/threads/{channel}/{thread}', 'ThreadController@destroy');
Route::post('/threads', 'ThreadController@store')->middleware('must-be-confirmed');
Route::get('/threads/{channel}', 'ThreadController@index');
Route::get('/threads/{channel}/{thread}', 'ThreadController@show');

Route::post('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionController@store');
Route::delete('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionController@destroy');

// Route::resource('threads','ThreadController');

Route::get('/threads/{channel}/{thread}/replies', 'ReplyController@index');
Route::post('/threads/{channel}/{thread}/replies', 'ReplyController@store');

Route::post('/replies/{reply}/favorite', 'FavoriteController@store');
Route::delete('/replies/{reply}/favorite', 'FavoriteController@destroy');

Route::patch('/replies/{reply}', 'ReplyController@update');
Route::delete('/replies/{reply}', 'ReplyController@destroy');

Route::get('/profiles/{user}', 'ProfileController@show')->name('profile');
Route::delete('/profiles/{user}/notifications/{notification}', 'NotificationController@destroy');
Route::get('/profiles/{user}/notifications', 'NotificationController@index');

Route::get('/register/confirm', 'Auth\RegisterConfirmationController@index');

Route::get('/api/users', 'Api\UserController@index');
Route::post('/api/users/{user}/avatar', 'Api\UserAvatarController@store')->middleware('auth')->name('avatar');


