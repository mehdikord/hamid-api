<?php


//All Admins Routing

Route::prefix('auth')->group(function () {

    Route::post('login',[\App\Http\Controllers\Admins\Auth\AuthController::class, 'login'])->name('login');

});

//Enable middleware

Route::middleware('auth:admins')->group(function () {


    Route::prefix('users')->as('users.')->group(function () {
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
        Route::get('{project}/customers',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'get_customers'])->name('get_customers');

        Route::post('{project}/change/password',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'change_password'])->name('change.password');
    });

    Route::apiResource('projects',\App\Http\Controllers\Admins\Projects\ProjectController::class);




});

?>
