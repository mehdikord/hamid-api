<?php
use Illuminate\Support\Facades\Route;



Route::prefix('forms')->as('forms')->group(function () {
    Route::get('/{token}',[\App\Http\Controllers\Landing\Forms\FormController::class, 'get_form'])->name('get');
    Route::post('/{token}',[\App\Http\Controllers\Landing\Forms\FormController::class, 'store_form'])->name('store');



});
