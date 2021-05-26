<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RulesController;
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
Route::get('/getUserReservations/{user_id}', [ReservationController::class, 'getUserReservations']);
Route::get('/daily/{start}/{end}', [ReservationController::class, 'daily']);
Route::get('/court_play/{time}', [ReservationController::class, 'court_play']);
Route::get('/member_play/{time}', [ReservationController::class, 'member_play']);
Route::get('/rainout/{start}/{end}', [ReservationController::class, 'rainout']);
Route::get('/reservation_users/{id}/{user_id}', [ReservationController::class, 'reservation_users']);
Route::get('/avail_reservations/{date_input_milliseconds}/{findType}', [ReservationController::class, 'avail_reservations']);
Route::get('/avail_reservations/{search_type}/{date_input_milliseconds}/{findType}', [ReservationController::class, 'avail_reservations']);

Route::prefix('/reservation')->group( function() {
    // Route::get('/{id}', [ReservationController::class, 'reservation_users']);
    Route::post('/store', [ReservationController::class, 'store']);
    Route::post('/memberStore', [ReservationController::class, 'memberStore']);
    Route::post('/storeReoccur', [ReservationController::class, 'storeReoccur']);
    Route::post('/resUsers', [ReservationController::class, 'resUsers']);
    Route::put('/adminupdate', [ReservationController::class, 'adminupdate']);
    Route::put('/update', [ReservationController::class, 'update']);
    Route::delete('/{id}', [ReservationController::class, 'destroy']);
    Route::delete('/deleteReoccur/{id}', [ReservationController::class, 'deleteReoccur']);
});


Route::get('/actions', [ActionController::class, 'index']);

Route::prefix('/action')->group( function() {
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

Route::get('/rules', [RulesController::class, 'index']);
