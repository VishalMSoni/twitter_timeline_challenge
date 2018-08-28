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
    return view('welcome'); 
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//redirecting to twitter
Route::get('/redirect', 'SocialAuthTwitterController@redirect');

//Callback from twitter after login
Route::get('/callback', 'SocialAuthTwitterController@callback');

Route::get('/twitterTimeline', 'SocialAuthTwitterController@getUserTweets');

Route::get('/getDetails', 'SocialAuthTwitterController@getSearchDetails');

Route::get('/downloadFollowers', 'SocialAuthTwitterController@getFollowers');

Route::get('htmlToPdfView', array('as'=>'htmlToPdfView','uses'=>'SocialAuthTwitterController@getFollowers'));