<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\RegisterController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/reservations', [ReservationController::class, 'index']);

Route::prefix('/reservation')->group( function() {
    Route::post('/store', [ReservationController::class, 'store']);
    Route::put('/{id}', [ReservationController::class, 'update']);
    Route::delete('/{id}', [ReservationController::class, 'destroy']);
});


Route::get('/actions', [ActionController::class, 'index']);

Route::prefix('/actions')->group( function() {
    Route::post('/store', [ActionController::class, 'store']);
    Route::put('/update', [ActionController::class, 'update']);
    Route::delete('/destroy', [ActionController::class, 'destroy']);
});


Route::get('/users', [UserController::class, 'index']);

Route::prefix('/user')->group( function() {
    Route::post('/store', [UserController::class, 'store']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});