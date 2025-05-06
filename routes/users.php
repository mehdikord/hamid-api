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

    //Customers
    Route::prefix('customers')->as('customers.')->group(function () {

        //Statuses
        Route::prefix('{customer}/statuses')->as('statuses.')->group(function () {

            Route::post('',[\App\Http\Controllers\Users\Customers\CustomerController::class,'statuses_store'])->name('store');

        });

        //Reports
        Route::prefix('{customer}/reports')->as('reports.')->group(function () {

            Route::post('',[\App\Http\Controllers\Users\Customers\CustomerController::class,'reports_store'])->name('store');
            Route::get('latest',[\App\Http\Controllers\Users\Customers\CustomerController::class,'all_reports_latest'])->name('latest');

        });

        //Invoices
        Route::prefix('{customer}/invoices')->as('invoices.')->group(function () {

            Route::post('',[\App\Http\Controllers\Users\Customers\CustomerController::class,'invoices_store'])->name('store');
            Route::get('latest',[\App\Http\Controllers\Users\Customers\CustomerController::class,'all_invoice_latest'])->name('latest');

            Route::post('target',[\App\Http\Controllers\Users\Customers\CustomerController::class,'invoices_target_store'])->name('target_store');

        });

        //Projects
        Route::prefix('{customer}/projects')->as('projects.')->group(function () {

            Route::get('',[\App\Http\Controllers\Users\Customers\CustomerController::class,'projects'])->name('index');

        });


        //Statuses
        Route::get('statuses',[\App\Http\Controllers\Users\Customers\CustomerController::class,'statuses'])->name('statuses.all');

        Route::get('',[\App\Http\Controllers\Users\Customers\CustomerController::class,'index'])->name('index');

        Route::get('{customer}',[\App\Http\Controllers\Users\Customers\CustomerController::class,'show'])->name('show');

        Route::put('{customer}',[\App\Http\Controllers\Users\Customers\CustomerController::class,'update'])->name('update');



    });


});
