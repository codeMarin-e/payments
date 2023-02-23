<?php

use App\Http\Controllers\Admin\PaymentMethodController;
use App\Models\PaymentMethod;

Route::group([
    'controller' => PaymentMethodController::class,
    'middleware' => ['auth:admin', 'can:view,'.PaymentMethod::class],
    'as' => 'payments.', //naming prefix
    'prefix' => 'payments', //for routes
], function() {
    Route::get('', 'index')->name('index');
    Route::post('', 'store')->name('store')->middleware('can:create,'.PaymentMethod::class);
    Route::get('create', 'create')->name('create')->middleware('can:create,'.PaymentMethod::class);
    Route::get('{chPaymentMethod}/edit', 'edit')->name('edit');
    Route::get('{chPaymentMethod}/move/{direction}', "move")->name('move')->middleware('can:update,chPaymentMethod');

    // @HOOK_ROUTES_MODEL

    Route::get('{chPaymentMethod}', 'edit')->name('show');
    Route::patch('{chPaymentMethod}', 'update')->name('update')->middleware('can:update,chPaymentMethod');
    Route::delete('{chPaymentMethod}', 'destroy')->name('destroy')->middleware('can:delete,chPaymentMethod');

    // @HOOK_ROUTES
});
