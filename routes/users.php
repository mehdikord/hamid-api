<?php
//All Users Routing

//Authenticate
Route::prefix('auth')->as('auth.')->group(function () {
    Route::prefix('otp')->as('otp.')->group(function () {
       Route::post('send', [\App\Http\Controllers\Users\Auth\AuthController::class,'otp_send'])->name('send');
       Route::post('verify', [\App\Http\Controllers\Users\Auth\AuthController::class,'otp_verify'])->name('verify');
    });
    Route::post('login', [\App\Http\Controllers\Users\Auth\AuthController::class,'login'])->name('login');
});

//Enable User Authentication middleware
Route::group(['middleware' => ['auth:users']], function () {

    //Profile
    Route::prefix('profile')->as('profile.')->group(function () {

        Route::get('',[\App\Http\Controllers\Users\Profile\ProfileController::class,'index'])->name('index');


    });



});
