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
 
Route::middleware('throttle:60,1')->post('/login', 'AuthController@login')->name('login');


Route::group(['middleware'=>['auth:sanctum'] ],function(){

    Route::post('/logout', 'AuthController@logout')->name('logout');
    Route::post('/loan/apply','LoanController@applyLoan')->name('apply.loan');
    Route::put('/loan/change-status','LoanController@changeLoanApplicationStatus')->name('loan.application.status.change');
    Route::post('/loan/payment','LoanController@weeklyLoanPayments')->name('loan.payment');    

});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


