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

Route::post('/login', 'App\Http\Controllers\API\WordpressAuthController@login');
Route::post('/login/social', 'App\Http\Controllers\API\WordpressAuthController@loginSocial');
Route::post('/register', 'App\Http\Controllers\API\WordpressAuthController@register');
Route::get('/debug/migrate','App\Http\Controllers\API\DebugController@migrate');
// Route::get('/user/{user}','App\Http\Controllers\API\WordpressAuthController@show');
Route::middleware(['auth:sanctum'])->group(function(){
	Route::get('/user/{user}','App\Http\Controllers\API\WordpressAuthController@show');
    Route::get('/user','App\Http\Controllers\API\WordpressAuthController@getCurrentUser');    
    Route::post('/update', 'App\Http\Controllers\API\WordpressAuthController@update');
    Route::post('/upload_profile_photo', 'App\Http\Controllers\API\WordpressAuthController@uploadProfilePhoto');
    Route::post('/logout', 'App\Http\Controllers\API\WordpressAuthController@logout');
	Route::apiResource('/video', 'App\Http\Controllers\API\VideoController');
	Route::get('/newfeed/community/{page}', 'App\Http\Controllers\API\VideoController@getNewFeedTab');
	Route::get('/video/community/detail/{video}', 'App\Http\Controllers\API\VideoController@show');
	Route::get('/newfeed/tv/{page}', 'App\Http\Controllers\API\VideoController@getNewFeedTv');
	Route::put('/video/seen/{video}', 'App\Http\Controllers\API\VideoController@viewed');
	Route::get('/friend/video/{user}', 'App\Http\Controllers\API\VideoController@getFriendVideo');
	Route::get('/search', 'App\Http\Controllers\API\SearchController@search');
	Route::get('/friend/list/{page}', 'App\Http\Controllers\API\FriendController@index');
	Route::post('/friendrequest', 'App\Http\Controllers\API\FriendRequestController@store'); 
	Route::get('/friend/request/list', 'App\Http\Controllers\API\FriendRequestController@index'); 
	Route::post('/friend/accept', 'App\Http\Controllers\API\FriendRequestController@accept'); 
	Route::post('/friend/decline', 'App\Http\Controllers\API\FriendRequestController@decline'); 
	Route::get('/video/comment/{video}', 'App\Http\Controllers\API\VideoController@getComment'); 
	Route::post('/video/comment', 'App\Http\Controllers\API\VideoController@addComment'); 
	Route::delete('/video/comment/{comment}', 'App\Http\Controllers\API\VideoController@deleteComment'); 
	Route::get('/video/like/{video}', 'App\Http\Controllers\API\VideoController@getLike'); 
	Route::post('/video/like', 'App\Http\Controllers\API\VideoController@addLike'); 
	Route::delete('/video/like/{video}', 'App\Http\Controllers\API\VideoController@unLike'); 
});
