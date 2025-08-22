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

Route::middleware(['auth:api'])->group(function () {
    Route::get('marketplace',[\App\Http\Controllers\ApiController::class,'getMarketplace']);

    // service
    Route::apiResource('services', \App\Http\Controllers\Api\ServiceController::class);
});
