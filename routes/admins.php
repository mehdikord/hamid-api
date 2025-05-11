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

        //Customers
        Route::prefix('{project}/customers')->as('customers.')->group(function () {
            Route::get('',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'get_customers'])->name('get');
            Route::post('',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'add_customers'])->name('add');
            Route::delete('{customer}',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'delete_customers'])->name('delete');
            Route::post('assigned',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'assigned_customers'])->name('assign');
            Route::post('assigned/single',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'assigned_customers_single'])->name('assign.single');
            Route::post('change/status',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'customers_change_status'])->name('change.status');
        });

        //Project Fields
        Route::prefix('{project}/fields')->as('fields.')->group(function () {
            Route::get('',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'get_fields'])->name('get');
            Route::post('',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'store_fields'])->name('store');
        });

        Route::prefix('{project}/reports')->as('reports.')->group(function () {
            Route::get('/latest',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'get_latest_reports'])->name('get_latest_reports');
        });

        Route::prefix('{project}/invoices')->as('invoices.')->group(function () {
            Route::get('/latest',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'get_latest_invoices'])->name('get_latest_invoices');

        });
    });

    Route::apiResource('projects',\App\Http\Controllers\Admins\Projects\ProjectController::class);

    //import-methods
    Route::get('import-methods/all',[\App\Http\Controllers\Admins\ImportMethods\ImportMethodController::class, 'all'])->name('import-methods.all');
    Route::apiResource('import-methods',\App\Http\Controllers\Admins\ImportMethods\ImportMethodController::class);

    Route::prefix('fields')->as('fields.')->group(function () {
        Route::get('all',[\App\Http\Controllers\Admins\Fields\FieldController::class, 'all'])->name('all');
    });
    //Fields
    Route::apiResource('fields',\App\Http\Controllers\Admins\Fields\FieldController::class);


    //Customers
    Route::prefix('customers')->as('customers.')->group(function () {
        //Settings
        Route::prefix('settings')->as('settings.')->group(function () {
            Route::get('statuses/all',[\App\Http\Controllers\Admins\Customers\CustomerSettingsStatusController::class, 'all'])->name('all');
            Route::apiResource('statuses',\App\Http\Controllers\Admins\Customers\CustomerSettingsStatusController::class);

        });

    });
    Route::apiResource('customers',\App\Http\Controllers\Admins\Customers\CustomerController::class);


    //Tags
    Route::get('tags/all',[\App\Http\Controllers\Admins\Tags\TagController::class, 'all'])->name('tags.all');
    Route::apiResource('tags',\App\Http\Controllers\Admins\Tags\TagController::class);








});


