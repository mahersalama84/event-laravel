<?php

use App\Http\Controllers\Api\Customer\CustomerController;
use App\Http\Controllers\Api\Expo\ExpoController;
use App\Http\Controllers\Api\General\GeneralController;
use App\Http\Controllers\Api\Occasion\OccasionController;
use App\Http\Controllers\Api\Occasion\WishController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/storage', function () {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return response()->json('ok', 200);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group(['middleware' => ['mobile.localization', 'json.response', 'throttle:30,2']], function () {
    Route::post('/customers/login', [CustomerController::class, 'login']);
    Route::post('/customers/otplogin', [CustomerController::class, 'otpLogin']);
    Route::post('/customers/otpguest', [CustomerController::class, 'otpGuest']);
});

Route::group(['middleware' => ['mobile.localization', 'json.response']], function () {
    Route::get('/notify', [ExpoController::class, 'notify']);
    Route::get('/notifications', [ExpoController::class, 'notifications']);
    Route::get('/maketoken', [ExpoController::class, 'MakeToken']);
    Route::get('/deletetoken', [ExpoController::class, 'DeleteToken']);

    Route::get('/advertisements', [GeneralController::class, 'advertisements']);
    Route::get('/customers/paginate', [CustomerController::class, 'paginateCustomers']);
});

Route::group(['middleware' => ['mobile.localization', 'json.response', 'auth:sanctum', 'role:customer']], function () {
    Route::get('/occasions/paginate', [OccasionController::class, 'paginateOccasions']);

    Route::post('/customers/registerexpopushtoken', [CustomerController::class, 'RegisterExpoPushToken']);

    Route::post('/customers/follow', [CustomerController::class, 'follow'])->middleware('activated');
    Route::post('/customers/processfollower', [CustomerController::class, 'processFollower'])->middleware(['activated', 'is.follower']);
    Route::post('/customers/followers', [CustomerController::class, 'paginateFollowers'])->middleware('activated');
    Route::post('/customers/followings', [CustomerController::class, 'paginateFollowings'])->middleware('activated');
    Route::post('/customers/search', [CustomerController::class, 'search']);
    Route::get('/customers/profile', [CustomerController::class, 'profile']);
    Route::get('/customers/logout', [CustomerController::class, 'logout']);
    Route::post('/customers/updateprofile', [CustomerController::class, 'updateProfile'])->middleware('activated');
    Route::post('/customers/uploadimage', [CustomerController::class, 'uploadImage'])->middleware('activated');
    Route::get('/customers/deleteimage', [CustomerController::class, 'deleteImage'])->middleware('activated');
    Route::get('/customers/myoccasions', [CustomerController::class, 'myOccasions']);

    Route::post('/customers/followingsoccasions', [CustomerController::class, 'FollowingsOccasions'])->middleware(['activated']);

    Route::post('/customers/otheroccasions', [CustomerController::class, 'otherOccasions'])->middleware(['activated', 'is.following', 'is.accepted.following']);
    Route::post('/customers/occasions/attend', [OccasionController::class, 'attend'])->middleware(['activated', 'is.following', 'is.accepted.following']);
    Route::post('/customers/occasions/paginateattendence', [OccasionController::class, 'paginateAttendence'])->middleware(['activated', 'is.following', 'is.accepted.following']);
    Route::post('/customers/occasions/getwishes', [OccasionController::class, 'getWishes'])->middleware(['activated', 'is.following', 'is.accepted.following']);
    Route::post('/customers/wishes/book', [WishController::class, 'book'])->middleware(['activated', 'is.following', 'is.accepted.following']);

    Route::post('/customers/occasions', [OccasionController::class, 'create'])->middleware('activated');
    Route::put('/customers/occasions/{occasion}', [OccasionController::class, 'update'])->middleware('activated');
    Route::delete('/customers/occasions/{occasion}', [OccasionController::class, 'destroy'])->middleware('activated');

    Route::post('/customers/wishes/uploadimage', [WishController::class, 'uploadImage'])->middleware('activated');
    Route::post('/customers/wishes/deleteimage', [WishController::class, 'deleteImage'])->middleware('activated');
    Route::post('/customers/wishes', [WishController::class, 'create'])->middleware('activated');
    Route::put('/customers/wishes/{wish}', [WishController::class, 'update'])->middleware('activated');
    Route::delete('/customers/wishes/{wish}', [WishController::class, 'destroy'])->middleware('activated');
});
