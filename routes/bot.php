<?php


use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Bot Routes
|--------------------------------------------------------------------------
|
| Here is where you can register bot routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group with "bot" prefix and "bot." name prefix.
|
*/
Route::group(['prefix' => 'auth','as' => 'auth.'], function () {

    Route::post('send',[\App\Http\Controllers\Bot\Auth\AuthController::class, 'send'])->name('send');
    Route::post( 'verify',[\App\Http\Controllers\Bot\Auth\AuthController::class, 'verify'])->name('verify');

});

