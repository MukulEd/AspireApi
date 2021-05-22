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

Route::middleware('throttle:60,1')->post('/login', 'AuthController@login')->name('api.login');


Route::group(['middleware'=>['auth:sanctum'] ],function(){

    Route::post('/logout', 'AuthController@logout')->name('api.logout');
    

});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


