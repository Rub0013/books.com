<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/{local?}', 'WelcomeController@show')->where('local','am|en');

Route::group([
    'prefix' => '{local}',
    'where' => ['local' => 'am|en'],
], function() {
    Route::get('/facebook', 'FacebookController@redirectToProvider')->name('facebook.login');
    Route::get('/google', 'GoogleController@redirectToProvider')->name('google.login');
    Route::get('/profile', 'ProfileController@show')->middleware('check');
    Route::get('/chat/{id?}', 'ChatController@chat')->where('id','[0-9]+')->middleware('check');
    Route::get('/friends', 'HomeController@index')->name('friends');
    Route::get('/calendar', 'CalendarController@calendar')->middleware('check');
    Route::get('/lybrary/{page?}', 'LybraryController@lybrary')->where('page','[0-9]+');
    Route::get('/add_books', 'LybraryController@add_books')->middleware('check');
    Route::get('/about', 'AboutController@about');
    Route::get('/search_one/{id}', 'SearchController@search_show_one')->where('id','[0-9]+');

    Route::group([
        'prefix' => 'headadmin',
    ], function() {
        Route::get('/', 'HeadAdmin@view')->name('headadmin_view');
        Route::get('/all_user_roles', 'HeadAdmin@all_user_roles')->name('headadmin_view');
        Route::post('/all_user_roles', 'HeadAdmin@change_user_roles')->name('headadmin_view');
    });

});
Route::get('/delete_book/{id}/{name}', 'LybraryController@delete')->where('id','[0-9]+');
Route::get('{local}/page_not_found', 'ErrorController@show')->name('error');
Route::get('facebook/callback', 'FacebookController@handleProviderCallback');
Route::get('google/callback', 'GoogleController@handleProviderCallback');

Route::post('/add_books', 'lybraryController@add_books');
Route::post('/update_book', 'lybraryController@updating');
Route::post('/like_unlike', 'LybraryController@like');
Route::post('/find_friend', 'FriendsController@find_friend');
Route::post('/search_in_friend_list', 'FriendsController@search_in_friend_list');
Route::post('/send_request', 'FriendsController@send_request');
Route::post('/all_notes', 'HomeController@total_notifications');
Route::post('/online', 'FriendsController@friends_online');
Route::post('/accept_request', 'FriendsController@accept_requests');
Route::post('/deny_request', 'FriendsController@deny_requests');
Route::post('/remuve_friend', 'FriendsController@remuve_friend');
Route::post('/search_book', 'SearchController@search_show');
Route::post('/mail_conversation', 'ChatController@mail_conversation');
Route::post('/current_friends_notes', 'ChatController@current_friends_notes');
Route::post('/send_message', 'ChatController@add_chat');
Route::post('/check_live', 'ChatController@live_chat');
Route::post('/delete_message', 'ChatController@delete_message');
Route::post('/delete_conversation', 'ChatController@delete_conversation');
Route::post('/change_message', 'ChatController@change_message');
Route::post('/update_info', 'ProfileController@update');

Route::match(['get','post'],'{local}/test/{id?}', 'TestController@testing');

//Auth

Route::get('{locale}/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('{locale}/login', 'Auth\LoginController@login');
Route::post('{locale}/logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('{locale}/register', 'Auth\RegisterController@showRegistrationForm');
Route::post('{locale}/register', 'Auth\RegisterController@register');



//login_as
Route::post('/login_as', 'HeadAdmin@login_as');
Route::get('/logout-back', 'HeadAdmin@logout_back');

