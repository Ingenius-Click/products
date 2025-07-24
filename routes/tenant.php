<?php

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here is where you can register tenant-specific routes for your module.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Ingenius\Products\Http\Controllers\CategoryController;
use Ingenius\Products\Http\Controllers\ProductsController;

Route::middleware([
    'api',
])->prefix('api')->group(function () {

    Route::prefix('products')->group(function () {

        Route::middleware(['tenant.user'])->group(function () {
            // List all products
            Route::get('/', [ProductsController::class, 'index'])->middleware('tenant.has.feature:list-products');

            // Create a new product
            Route::post('/', [ProductsController::class, 'store'])->middleware('tenant.has.feature:create-product');

            // Update a specific product
            Route::put('/{product}', [ProductsController::class, 'update'])->middleware('tenant.has.feature:update-product');

            // Delete a specific product
            Route::delete('/{product}', [ProductsController::class, 'destroy'])->middleware('tenant.has.feature:delete-product');
        });

        // Show a specific product
        Route::get('/{product}', [ProductsController::class, 'show'])->middleware('tenant.has.feature:view-product');
    });

    Route::prefix('categories')->group(function () {
        Route::middleware(['tenant.user'])->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->middleware('tenant.has.feature:list-categories');

            Route::post('/', [CategoryController::class, 'store'])->middleware('tenant.has.feature:create-category');

            Route::get('/{category}', [CategoryController::class, 'show'])->middleware('tenant.has.feature:view-category');

            Route::put('/{category}', [CategoryController::class, 'update'])->middleware('tenant.has.feature:update-category');

            Route::delete('/{category}', [CategoryController::class, 'destroy'])->middleware('tenant.has.feature:delete-category');
        });
    });
});

// Route::get('tenant-example', function () {
//     return 'Hello from tenant-specific route! Current tenant: ' . tenant('id');
// });