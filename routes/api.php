<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('login',function (){
    return [
        'code'=>401,
        'status'=>'error',
        'message'=>'un authorize user',
        'data'=>null
    ];
})->name('login');

Route::post('login',[\App\Http\Controllers\AuthController::class,'login']);
Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('seller-registration',[\App\Http\Controllers\AuthController::class,'sellerRegistration']);
Route::post('business-hour/{id}',[\App\Http\Controllers\AuthController::class,'businessHour']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('marketplace',[\App\Http\Controllers\ApiController::class,'getMarketplace']);
    Route::get('product-service-by-marketplace/{id}',[\App\Http\Controllers\ApiController::class,'productServiceByMarketplace']);
    Route::post('services-update/{id}',[\App\Http\Controllers\Api\ServiceController::class,'update']);
    // service
    Route::apiResource('services', \App\Http\Controllers\Api\ServiceController::class);

    Route::get('partners/{id}/availability', [\App\Http\Controllers\Api\BookingController::class, 'availability']);
    Route::post('bookings', [\App\Http\Controllers\Api\BookingController::class, 'store']);
    Route::get('bookings/{id}', [\App\Http\Controllers\Api\BookingController::class, 'show']);
    Route::post('bookings/{id}/confirm', [\App\Http\Controllers\Api\BookingController::class, 'confirm']);
    Route::post('bookings/{id}/check-in', [\App\Http\Controllers\Api\BookingController::class, 'checkIn']);
    Route::post('bookings/{id}/complete', [\App\Http\Controllers\Api\BookingController::class, 'complete']);
    Route::post('bookings/{id}/cancel', [\App\Http\Controllers\Api\BookingController::class, 'cancel']);
});
