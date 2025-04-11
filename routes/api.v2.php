<?php

use App\Http\Controllers\Customer\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group(['middleware' => ['json.response','throttle:10,2']], function () {
    Route::post('/customers/login', function(){
        return 'V2';
    });
});

Route::group(['middleware' => ['json.response', 'auth:sanctum']], function () {
    Route::get('/customers/profile', [CustomerController::class, 'profile']);
    Route::get('/customers/logout', [CustomerController::class, 'logout']);
});
