<?php

use App\Http\Controllers\Landing\ActionController;
use Illuminate\Support\Facades\Route;



Route::prefix('forms')->as('forms')->group(function () {
    Route::get('/{token}',[\App\Http\Controllers\Landing\Forms\FormController::class, 'get_form'])->name('get');
    Route::post('/{token}',[\App\Http\Controllers\Landing\Forms\FormController::class, 'store_form'])->name('store');
});
Route::prefix('actions')->as('actions.')->group(function () {

    Route::get('activation/{token}',[ActionController::class, 'activation'])->name('activation');

    

});
