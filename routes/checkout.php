<?php

use App\Http\Controllers\Checkout\CheckoutController;
use App\Http\Controllers\Checkout\CheckoutItemController;
use App\Http\Controllers\Checkout\CheckoutDiscountController;

Route::group([
    'namespace' => 'Yab\ShoppingCart\Http\Controllers\Checkout',
], function () {
    Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('checkout/{checkout}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::put('checkout/{checkout}', [CheckoutController::class, 'update'])->name('checkout.update');
    Route::delete('checkout/{checkout}', [CheckoutController::class, 'destroy'])->name('checkout.destroy');

    Route::post('checkout/{checkout}/items', [CheckoutItemController::class, 'store'])->name('checkout.items.store');
    Route::put('checkout/{checkout}/items/{itemId}', [CheckoutItemController::class, 'update'])->name('checkout.items.update');
    Route::delete('checkout/{checkout}/items/{itemId}', [CheckoutItemController::class, 'destroy'])->name('checkout.items.destroy');

    Route::post('checkout/{checkout}/discount', [CheckoutDiscountController::class, 'store'])->name('checkout.discount');
});
