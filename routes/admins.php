<?php
//All Admins Routing
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {

    Route::post('login',[\App\Http\Controllers\Admins\Auth\AuthController::class, 'login'])->name('login');

});

//Enable middleware

Route::middleware('auth:admins')->group(function () {


    Route::prefix('users')->as('users.')->group(function () {
        Route::get('all',[\App\Http\Controllers\Admins\Users\UserController::class, 'all'])->name('all');
        Route::get('{user}/activation',[\App\Http\Controllers\Admins\Users\UserController::class, 'activation'])->name('activation');
        Route::post('{user}/change/password',[\App\Http\Controllers\Admins\Users\UserController::class, 'change_password'])->name('change.password');
    });

    Route::apiResource('users',\App\Http\Controllers\Admins\Users\UserController::class);

    //projects
    Route::prefix('projects')->as('projects.')->group(function () {
        Route::get('categories/all',[\App\Http\Controllers\Admins\Projects\ProjectCategoryController::class, 'all'])->name('all');
        Route::apiResource('categories',\App\Http\Controllers\Admins\Projects\ProjectCategoryController::class);
        Route::get('statuses/all',[\App\Http\Controllers\Admins\Projects\ProjectStatusController::class, 'all'])->name('all');
        Route::apiResource('statuses',\App\Http\Controllers\Admins\Projects\ProjectStatusController::class);

    });

    Route::prefix('projects')->as('projects.')->group(function () {
        Route::get('{project}/activation',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'activation'])->name('activation');
        Route::post('{project}/customers',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'add_customers'])->name('add_customers');
        Route::post('{project}/customers/assigned',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'assigned_customers'])->name('assigned_customers');
        Route::get('{project}/customers',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'get_customers'])->name('get_customers');
    });

    Route::apiResource('projects',\App\Http\Controllers\Admins\Projects\ProjectController::class);

    //Customers
    Route::prefix('customers')->as('customers.')->group(function () {
        //Settings
        Route::prefix('settings')->as('settings.')->group(function () {
            Route::get('statuses/all',[\App\Http\Controllers\Admins\Customers\CustomerSettingsStatusController::class, 'all'])->name('all');
            Route::apiResource('statuses',\App\Http\Controllers\Admins\Customers\CustomerSettingsStatusController::class);

        });

    });





});


