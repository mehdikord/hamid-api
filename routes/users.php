<?php

//All Users Routing

//Authenticate

use Illuminate\Support\Facades\Route;

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
        Route::get('projects',[\App\Http\Controllers\Users\Profile\ProfileController::class,'projects'])->name('projects');

    });

    //Customers
    Route::prefix('customers')->as('customers.')->group(function () {

        Route::get('consultant',[\App\Http\Controllers\Users\Customers\CustomerController::class,'consultant'])->name('consultant');
        Route::get('consultant/old',[\App\Http\Controllers\Users\Customers\CustomerController::class,'consultant_old'])->name('consultant.old');

        Route::get('seller',[\App\Http\Controllers\Users\Customers\CustomerController::class,'seller'])->name('seller');




        //Statuses
        Route::prefix('{customer}/statuses')->as('statuses.')->group(function () {

            Route::post('',[\App\Http\Controllers\Users\Customers\CustomerController::class,'statuses_store'])->name('store');

        });

        //Reports
        Route::prefix('{customer}/reports')->as('reports.')->group(function () {
            Route::post('',[\App\Http\Controllers\Users\Customers\CustomerController::class,'reports_store'])->name('store');
            Route::get('',[\App\Http\Controllers\Users\Customers\CustomerController::class,'reports_index'])->name('index');
            Route::get('latest',[\App\Http\Controllers\Users\Customers\CustomerController::class,'all_reports_latest'])->name('latest');

        });

        //Invoices
        Route::prefix('{customer}/invoices')->as('invoices.')->group(function () {

            Route::get('',[\App\Http\Controllers\Users\Customers\CustomerController::class,'invoices_index'])->name('index');
            Route::post('',[\App\Http\Controllers\Users\Customers\CustomerController::class,'invoices_store'])->name('store');
            Route::get('latest',[\App\Http\Controllers\Users\Customers\CustomerController::class,'all_invoice_latest'])->name('latest');

            Route::post('target',[\App\Http\Controllers\Users\Customers\CustomerController::class,'invoices_target_store'])->name('target_store');

        });

        //Projects
        Route::prefix('{customer}/projects')->as('projects.')->group(function () {

            Route::prefix('{project}/reports')->as('reports.')->group(function () {
               Route::post('',[\App\Http\Controllers\Users\Customers\CustomerController::class,'projects_report_store'])->name('store');
            });
            Route::prefix('{project}/invoices')->as('invoices.')->group(function () {
               Route::post('',[\App\Http\Controllers\Users\Customers\CustomerController::class,'projects_invoice_store'])->name('store');
            });

            Route::get('',[\App\Http\Controllers\Users\Customers\CustomerController::class,'projects'])->name('index');
            Route::get('own/{project}',[\App\Http\Controllers\Users\Customers\CustomerController::class,'projects_own'])->name('index');
            Route::get('fields/{project}',[\App\Http\Controllers\Users\Customers\CustomerController::class,'projects_fields'])->name('fields');
            Route::get('levels/{project}',[\App\Http\Controllers\Users\Customers\CustomerController::class,'projects_levels'])->name('levels');

        });


        //Statuses
        Route::get('statuses',[\App\Http\Controllers\Users\Customers\CustomerController::class,'statuses'])->name('statuses.all');

        Route::get('levels',[\App\Http\Controllers\Users\Customers\CustomerController::class,'levels'])->name('levels.all');

        Route::get('',[\App\Http\Controllers\Users\Customers\CustomerController::class,'index'])->name('index');

        Route::get('{customer}',[\App\Http\Controllers\Users\Customers\CustomerController::class,'show'])->name('show');

        Route::put('{customer}',[\App\Http\Controllers\Users\Customers\CustomerController::class,'update'])->name('update');


    });

    //Project
    Route::prefix('projects')->as('projects.')->group(function () {
        Route::get('all',[\App\Http\Controllers\Users\Products\ProductController::class,'all'])->name('all');
    });


});
