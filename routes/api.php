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
Route::middleware(['auth:sanctum'])->group(function(){
    Route::get('/user','App\Http\Controllers\API\WordpressAuthController@getCurrentUser');
    Route::post('/update', 'App\Http\Controllers\API\WordpressAuthController@update');
    Route::post('/upload_profile_photo', 'App\Http\Controllers\API\WordpressAuthController@uploadProfilePhoto');
    Route::post('/logout', 'App\Http\Controllers\API\WordpressAuthController@logout');
	Route::apiResource('/video', 'App\Http\Controllers\API\VideoController');
	Route::put('/video/viewed/{video}', 'App\Http\Controllers\API\VideoController@viewed');
});
