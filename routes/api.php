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
	Route::get('/user/{user_id}','App\Http\Controllers\API\WordpressAuthController@show');
    Route::get('/user','App\Http\Controllers\API\WordpressAuthController@getCurrentUser');
    Route::post('/upload_verify_photo','App\Http\Controllers\API\WordpressAuthController@uploadVerifyPhoto');

    Route::post('/update', 'App\Http\Controllers\API\WordpressAuthController@update');
    Route::post('/store_device_token', 'App\Http\Controllers\API\WordpressAuthController@storeNotifyToken');
    Route::post('/upload_profile_photo', 'App\Http\Controllers\API\WordpressAuthController@uploadProfilePhoto');
    Route::post('/logout', 'App\Http\Controllers\API\WordpressAuthController@logout');
	Route::get('/search', 'App\Http\Controllers\API\SearchController@search');
	Route::get('/friend/list/{user}', 'App\Http\Controllers\API\FriendController@index');
	Route::post('/friendrequest', 'App\Http\Controllers\API\FriendRequestController@store'); 
	Route::get('/friend/request/list', 'App\Http\Controllers\API\FriendRequestController@index'); 
	Route::post('/friend/accept', 'App\Http\Controllers\API\FriendRequestController@accept'); 
	Route::post('/friend/decline', 'App\Http\Controllers\API\FriendRequestController@decline');
	Route::post('/friend/cancel', 'App\Http\Controllers\API\FriendRequestController@cancel');
	Route::get('/friend/follow/{user}', 'App\Http\Controllers\API\FriendController@followingUser');	
	Route::get('/friend/who_follow_me/{user}', 'App\Http\Controllers\API\FriendController@whoFollowMe');	
	Route::post('/friend/follow', 'App\Http\Controllers\API\FriendRequestController@follow');	
	Route::delete('/friend/delete/{user}', 'App\Http\Controllers\API\FriendController@destroy');
	Route::delete('/friend/unfollow', 'App\Http\Controllers\API\FriendRequestController@unFollow');	

	Route::apiResource('/video', 'App\Http\Controllers\API\VideoController');
	Route::get('/newfeed/community/{page}', 'App\Http\Controllers\API\VideoController@getNewFeedTab');
	Route::get('/video/community/detail/{video}', 'App\Http\Controllers\API\VideoController@show');
	Route::put('/video/seen/{video}', 'App\Http\Controllers\API\VideoController@viewed');
	Route::get('/friend/video/{user}', 'App\Http\Controllers\API\VideoController@getFriendVideo');
	Route::get('/video/comment/{video}', 'App\Http\Controllers\API\VideoController@getComment'); 
	Route::post('/video/comment', 'App\Http\Controllers\API\VideoController@addComment'); 
	Route::put('/video/comment/{comment}', 'App\Http\Controllers\API\VideoController@updateComment'); 
	Route::delete('/video/comment/{comment}', 'App\Http\Controllers\API\VideoController@deleteComment'); 
	Route::get('/video/like/{video}', 'App\Http\Controllers\API\VideoController@getLike'); 
	Route::post('/video/like', 'App\Http\Controllers\API\VideoController@addLike'); 
	Route::delete('/video/like/{video}', 'App\Http\Controllers\API\VideoController@unLike');
		
	Route::get('/newfeed/tv/{page}', 'App\Http\Controllers\API\VideoTvController@getNewFeedTv');
	Route::get('/video/tv/detail/{video}', 'App\Http\Controllers\API\VideoTvController@show');
	Route::get('/friend/tv/video/{user}', 'App\Http\Controllers\API\VideoTvController@getFriendVideo');
	Route::put('/video/tv/seen/{video}', 'App\Http\Controllers\API\VideoTvController@viewed');
	Route::post('/video/tv/comment', 'App\Http\Controllers\API\VideoTvController@addComment'); 
	Route::put('/video/tv/comment/{comment}', 'App\Http\Controllers\API\VideoTvController@updateComment'); 
	Route::get('/video/tv/comment/{video}', 'App\Http\Controllers\API\VideoTvController@getComment'); 
	Route::delete('/video/tv/comment/{comment}', 'App\Http\Controllers\API\VideoTvController@deleteComment'); 
	Route::get('/video/tv/like/{video}', 'App\Http\Controllers\API\VideoTvController@getLike'); 
	Route::post('/video/tv/like', 'App\Http\Controllers\API\VideoTvController@addLike'); 
	Route::delete('/video/tv/like/{video}', 'App\Http\Controllers\API\VideoTvController@unLike'); 

	Route::get('/notify/list', 'App\Http\Controllers\API\NotifyController@index');
	Route::put('/notify/read_markup_all', 'App\Http\Controllers\API\NotifyController@markAllRead');
	Route::put('/notify/read_markup/{notify}', 'App\Http\Controllers\API\NotifyController@markRead');
	Route::delete('/notify/{notify}', 'App\Http\Controllers\API\NotifyController@destroy');
	Route::get('/notify/detail/{notify}', 'App\Http\Controllers\API\NotifyController@show');

	Route::get('/favorite/list', 'App\Http\Controllers\API\FavoriteController@index');
	Route::get('/favorite/detail/{favorite}', 'App\Http\Controllers\API\FavoriteController@show');
	Route::post('/favorite', 'App\Http\Controllers\API\FavoriteController@store');
	Route::put('/favorite/{favorite}', 'App\Http\Controllers\API\FavoriteController@update');
	Route::post('/favorite/video/{favorite}', 'App\Http\Controllers\API\FavoriteController@addVideo');
	Route::delete('/favorite/{favorite}', 'App\Http\Controllers\API\FavoriteController@destroy');
	Route::delete('/favorite/video/{favorite}', 'App\Http\Controllers\API\FavoriteController@deleteVideo');
});

Route::middleware(['admin'])->group(function(){
	Route::get('/admin/video', 'App\Http\Controllers\API\VideoController@adminGetVideo');
	Route::put('/admin/video/{video}', 'App\Http\Controllers\API\VideoController@update');
	Route::delete('/admin/video/{video}', 'App\Http\Controllers\API\VideoController@adminDeleteVideo');
});