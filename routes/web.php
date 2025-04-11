<?php

use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Occasion\OccasionController;
use App\Http\Controllers\Occasion\WishController;
use App\Http\Controllers\Offers\AdvertisementController;
use App\Http\Controllers\User\LogController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('lang/{lang}', function ($lang) {
//     app()->setlocale($lang);
//     session()->put('locale',$lang);
//     Log::info("Locale set to: " . $lang . " (Selected language: " . $lang . ")");

//     // dd(app()->getLocale());
//     return redirect()->route('dashboard');
// })->name('lang');


Route::get('/language/{language}', function ($language) {
    Session()->put('locale', $language);
    return redirect()->back();
})->name('language');


// Route::get('/', function () {
//     return Inertia::render('Auth/Login', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::group(['middleware' => ['auth', 'verified', 'role:admin']], function () {
    Route::get('/dashboard', [DashboardController::class, 'stats'])->name('dashboard');

    Route::get('/profile/setshowcustomers', [ProfileController::class, 'setShowCustomers'])->name('profile.setshowcustomers');
    Route::get('/profile/setshowusers', [ProfileController::class, 'setShowUsers'])->name('profile.setshowusers');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.delete');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::get('/customers/{customer}/getoccasions', [CustomerController::class, 'getOccasions'])->name('customers.getoccasions');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::patch('/customers/{customer}/toggleactive', [CustomerController::class, 'toggleActive'])->name('customers.toggleactive');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.delete');
    Route::delete('/customers/deleteoccasion/{occasion}', [CustomerController::class, 'DeleteOccasion'])->name('customers.deleteoccasion');


    Route::get('/occasions', [OccasionController::class, 'index'])->name('occasions.index');
    Route::get('/occasions/create', [OccasionController::class, 'create'])->name('occasions.create');
    Route::post('/occasions/searchcustomer', [OccasionController::class, 'searchCustomer'])->name('occasions.searchcustomer');
    Route::get('/occasions/{occasion}/getwishes', [OccasionController::class, 'getWishes'])->name('occasions.getwishes');
    Route::post('/occasions', [OccasionController::class, 'store'])->name('occasions.store');
    Route::get('/occasions/{occasion}', [OccasionController::class, 'edit'])->name('occasions.edit');
    Route::put('/occasions/{occasion}', [OccasionController::class, 'update'])->name('occasions.update');
    Route::delete('/occasions/{occasion}', [OccasionController::class, 'destroy'])->name('occasions.delete');
    Route::delete('/occasions/deletewish/{wish}', [OccasionController::class, 'DeleteWish'])->name('occasions.deletewish');

    Route::post('/wishes/{wish}', [WishController::class, 'update'])->name('wishes.update');
    Route::post('/wishes', [WishController::class, 'create'])->name('wishes.create');

    Route::get('/advertisements', [AdvertisementController::class, 'index'])->name('advertisements.index');
    Route::get('/advertisements/create', [AdvertisementController::class, 'create'])->name('advertisements.create');
    Route::post('/advertisements', [AdvertisementController::class, 'store'])->name('advertisements.store');
    Route::delete('/advertisements/{advertisement}', [AdvertisementController::class, 'destroy'])->name('advertisements.delete');
    Route::patch('/advertisements/{advertisement}/togglepublished', [AdvertisementController::class, 'togglePublished'])->name('advertisements.togglepublished');

    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    Route::get('/logs/clear', [LogController::class, 'clear'])->name('logs.clear');
});

require __DIR__ . '/auth.php';
