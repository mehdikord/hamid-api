<?php
//All Admins Routing

use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {

    Route::post('login',[\App\Http\Controllers\Admins\Auth\AuthController::class, 'login'])->name('login');

});

//Enable middleware

Route::middleware('auth:admins')->group(function () {

    Route::group(['prefix' => 'members','as' => 'members.'], function () {

        Route::post('{member}/change/password',[\App\Http\Controllers\Admins\Members\MemberController::class, 'change_password'])->name('change.password');
    });
    Route::apiResource('members',\App\Http\Controllers\Admins\Members\MemberController::class);

    Route::prefix('users')->as('users.')->group(function () {

        Route::get('all',[\App\Http\Controllers\Admins\Users\UserController::class, 'all'])->name('all');
        Route::get('{user}/activation',[\App\Http\Controllers\Admins\Users\UserController::class, 'activation'])->name('activation');
        Route::post('{user}/change/password',[\App\Http\Controllers\Admins\Users\UserController::class, 'change_password'])->name('change.password');

        //Roles
//        Route::prefix('{user}/positions')->as('positions.')->group(function () {
//           Route::post('',[\App\Http\Controllers\Admins\Users\UserController::class, 'positions_store'])->name('store');
//
//        });

    });
    Route::apiResource('users',\App\Http\Controllers\Admins\Users\UserController::class);

    Route::prefix('dashboards')->as('dashboards.')->group(function () {
       Route::prefix('reports')->as('reports.')->group(function () {
           Route::get('users/weekly/{project}',[\App\Http\Controllers\Admins\Dashboard\ReportController::class, 'users_weekly'])->name('users_weekly')->withoutMiddleware('auth:admins');
           Route::prefix('projects')->as('projects.')->group(function () {
              Route::get('summery',[\App\Http\Controllers\Admins\Dashboard\ReportController::class, 'projects_summery'])->name('summery')->withoutMiddleware('auth:admins');
           });
       });
    });

    //projects
    Route::prefix('projects')->as('projects.')->group(function () {

        Route::get('all',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'all'])->name('all');

        Route::get('categories/all',[\App\Http\Controllers\Admins\Projects\ProjectCategoryController::class, 'all'])->name('all');
        Route::apiResource('categories',\App\Http\Controllers\Admins\Projects\ProjectCategoryController::class);


        //Customers
        Route::get('customers/columns',action: [\App\Http\Controllers\Admins\Projects\ProjectController::class, 'get_columns'])->name('columns');
        Route::get('invoices/columns',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'invoices_columns'])->name('columns');

        Route::prefix('{project}/customers')->as('customers.')->group(function () {
            Route::get('fields',action: [\App\Http\Controllers\Admins\Projects\ProjectController::class, 'get_customer_fields'])->name('customer_fields');
            Route::get('columns',action: [\App\Http\Controllers\Admins\Projects\ProjectController::class, 'get_columns'])->name('columns');
            Route::get('all',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'all_customers'])->name('all');
            Route::get('pending/success',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'pending_customers_success'])->name('pending.success');
            Route::get('pending',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'pending_customers'])->name('pending');
            Route::get('',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'get_customers'])->name('get');
            Route::post('',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'add_customers'])->name('add');
            Route::delete('{customer}',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'delete_customers'])->name('delete');
            Route::post('delete/multi',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'delete_multi'])->name('delete_multi');
            Route::post('assigned',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'assigned_customers'])->name('assign');
            Route::post('assigned/single',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'assigned_customers_single'])->name('assign.single');
            Route::post('assigned/multi',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'assigned_customers_multi'])->name('assign.multi');
            Route::post('change/status',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'customers_change_status'])->name('change.status');
            Route::post('change/level',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'customers_change_level'])->name('change.level');
            Route::post('change/target',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'customers_change_target'])->name('change.target');
        });

        Route::prefix('{project}')->as('statuses.')->group(function () {
            Route::get('statuses/all',[\App\Http\Controllers\Admins\Projects\ProjectStatusController::class, 'all'])->name('all');
            Route::apiResource('statuses',\App\Http\Controllers\Admins\Projects\ProjectStatusController::class);
        });

        Route::prefix('{project}')->as('levels.')->group(function () {
            Route::get('levels/all',[\App\Http\Controllers\Admins\Projects\ProjectLevelsController::class, 'all'])->name('all');
            Route::apiResource('levels',\App\Http\Controllers\Admins\Projects\ProjectLevelsController::class);
        });

        //Import Methods
        Route::prefix('{project}')->group(function () {
            Route::get('import-methods/all',[\App\Http\Controllers\Admins\ImportMethods\ImportMethodController::class, 'all'])->name('import-methods.all');
            Route::apiResource('import-methods',\App\Http\Controllers\Admins\ImportMethods\ImportMethodController::class);
        });

        //Project Fields
        Route::prefix('{project}')->group(function () {
            Route::get('fields/all',[\App\Http\Controllers\Admins\Fields\FieldController::class, 'all'])->name('fields.all');
            Route::apiResource('fields',\App\Http\Controllers\Admins\Fields\FieldController::class);
        });

        Route::prefix('{project}')->group(function () {
            Route::get('tags/all',[\App\Http\Controllers\Admins\Tags\TagController::class, 'all'])->name('tags.all');
            Route::apiResource('tags',\App\Http\Controllers\Admins\Tags\TagController::class);
        });


        Route::prefix('{project}/forms')->group(function () {
            Route::get('activation/{form}',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'activation_forms'])->name('activation');
            Route::get('',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'get_forms'])->name('get');
            Route::post('',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'store_forms'])->name('store');
            Route::post('{form}',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'update_forms'])->name('update');
            Route::delete('{form}',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'destroy_forms'])->name('destroy');
        });

        //Project Positions
        Route::prefix('{project}/positions')->as('positions.')->group(function () {
            Route::get('',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'get_positions'])->name('get');
            Route::post('',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'store_positions'])->name('store');
        });


        Route::prefix('{project}/reports')->as('reports.')->group(function () {
            Route::get('',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'reports'])->name('index');
            Route::post('',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'reports_store'])->name('store');
            Route::get('download/{report}',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'reports_download'])->name('download');
            Route::post('{report}',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'reports_update'])->name('update');
            Route::delete('{report}',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'reports_destroy'])->name('destroy');
            Route::get('/latest',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'get_latest_reports'])->name('get_latest_reports');
        });

        Route::prefix('{project}/invoices')->as('invoices.')->group(function () {
            Route::get('',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'invoices'])->name('index');
            Route::post('',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'invoices_store'])->name('store');
            Route::get('settle/{invoice}',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'invoices_settle'])->name('change_settle');
            Route::get('download/{invoice}',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'invoices_download'])->name('download');
            Route::post('{invoice}',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'invoices_update'])->name('update');
            Route::delete('{invoice}',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'invoices_destroy'])->name('destroy');
            Route::get('/latest',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'get_latest_invoices'])->name('get_latest_invoices');
        });

        //Messages
        Route::prefix('{project}/messages')->as('messages.')->group(function () {
            Route::get('all',[\App\Http\Controllers\Admins\Projects\ProjectMessageController::class, 'all'])->name('all');
        });
        Route::apiResource('{project}/messages',\App\Http\Controllers\Admins\Projects\ProjectMessageController::class);


        //Exports
        Route::group(['prefix' => '{project}/export','as' => 'export.'],function(){

            Route::get('customers',[\App\Http\Controllers\Admins\Projects\ProjectsExportController::class,'customers'])->name('customers')->withoutMiddleware('auth:admins');
        });

        //Update logo
        Route::post('{project}/logo',[\App\Http\Controllers\Admins\Projects\ProjectController::class, 'update_logo'])->name('update.logo');

    });


    Route::apiResource('projects',\App\Http\Controllers\Admins\Projects\ProjectController::class);

    //import-methods

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

        Route::prefix('{customer}/projects')->as('projects.')->group(function () {
            Route::get('{project}/fields',[\App\Http\Controllers\Admins\Customers\CustomerController::class,'projects_fields'])->name('fields');
            Route::get('{project}/reports',[\App\Http\Controllers\Admins\Customers\CustomerController::class,'projects_reports'])->name('reports');
            Route::get('{project}/invoices',[\App\Http\Controllers\Admins\Customers\CustomerController::class,'projects_invoices'])->name('invoices');
            Route::get('{project}/statuses',[\App\Http\Controllers\Admins\Customers\CustomerController::class,'projects_statuses'])->name('statuses');

        });

    });

    Route::apiResource('customers',\App\Http\Controllers\Admins\Customers\CustomerController::class);

    //Tags
    // Route::get('tags/all',[\App\Http\Controllers\Admins\Tags\TagController::class, 'all'])->name('tags.all');
    // Route::apiResource('tags',\App\Http\Controllers\Admins\Tags\TagController::class);

    //Positions
    Route::get('positions/all',[\App\Http\Controllers\Admins\Positions\PositionController::class, 'all'])->name('positions.all');
    Route::apiResource('positions',\App\Http\Controllers\Admins\Positions\PositionController::class);


    //Reports
    Route::prefix('reports')->as('reports.')->group(function () {
       Route::prefix('projects')->as('projects.')->group(function () {
         Route::prefix('{project}/invoices')->as('invoices.')->group(function () {
            Route::get('users',[\App\Http\Controllers\Admin\Reports\InvoicesController::class,'users'])->name('users');
         });

       });

    });

    //Telegram Groups
    Route::prefix('telegram')->as('telegram.')->group(function () {
        Route::prefix('groups')->as('groups.')->group(function () {
            Route::post('assign/{group}',[\App\Http\Controllers\Admins\TelegramGroups\TelegramGroupsController::class, 'assign'])->name('assign');
            Route::get( '',[\App\Http\Controllers\Admins\TelegramGroups\TelegramGroupsController::class, 'all'])->name('all');
        });
    });


});


