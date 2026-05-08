<?php

use App\Http\Controllers\CollectionWebController;
use App\Http\Controllers\ColorWebController;
use App\Http\Controllers\DashboardWebController;
use App\Http\Controllers\OrderWebController;
use App\Http\Controllers\ProductVariantWebController;
use App\Http\Controllers\ProductWebController;
use App\Http\Controllers\ScentWebController;
use App\Http\Controllers\StatusWebController;
use App\Http\Controllers\TypeWebController;
use App\Http\Controllers\UserWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes (Moved to /admin prefix for safety)
Route::get('/admin/login', [UserWebController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [UserWebController::class, 'login'])->name('login.process');
Route::post('/admin/logout', [UserWebController::class, 'logout'])->name('logout');
Route::delete('/admin/logout', [UserWebController::class, 'logout']);

// Admin Area - Protected by is_admin_web
Route::middleware(['is_admin_web'])->prefix('admin')->group(function () {
    
    // Dashboard
    Route::get('/', function() {
        $totalOrder = \App\Models\Order::count();
        $totalProduct = \App\Models\Product::count();
        $totalIncome = \App\Models\Order::where('payment_status', 'paid')->sum('total_price');
        return view('components.welcome', compact('totalOrder', 'totalProduct', 'totalIncome'));
    })->name('admin');

    // Orders
    Route::controller(OrderWebController::class)->group(function() {
        Route::get('/orders', 'getOrders')->name('orders.index');
        Route::get('/orders/{orderId}', 'showOrderDetail')->name('orders.detail');
        Route::patch('/orders/{orderId}/status', 'updateStatus')->name('orders.updateStatus');
        Route::patch('/orders/{orderId}/tracking', 'updateTracking')->name('orders.updateTracking');
    });

    // Products
    Route::controller(ProductWebController::class)->group(function() {
        Route::get('/products', 'getProducts')->name('products.index');
        Route::get('/products/create', function() {
            return view('components.products.create_product');
        })->name('products.create.form');
        Route::post('/products/create', 'createProduct')->name('products.create');
        Route::get('/products/{productId}', 'showProductDetail')->name('products.detail');
        Route::get('/products/{productId}/edit', 'editProduct')->name('products.edit');
        Route::put('/products/{productId}/update', 'updateProduct')->name('products.update');
        Route::delete('/products/{productId}/delete', 'destroy')->name('products.delete');
    });

    // Variants
    Route::patch('/products/{productId}/variants/{variantId}/update', [ProductVariantWebController::class, 'updateProductVariant'])->name('variant.update');

    // Collections
    Route::controller(CollectionWebController::class)->group(function() {
        Route::get('/collections', 'getCollections')->name('collections.index');
        Route::post('/collections/create', 'createCollection')->name('collections.create');
        Route::get('/collections/{collectionId}/edit', 'showEditForm')->name('collections.edit.form');
        Route::put('/collections/{collectionId}/update', 'updateCollection')->name('collections.update');
        Route::delete('/collections/{collectionId}/delete', 'deleteCollection')->name('collections.delete');
    });

    // Colors
    Route::controller(ColorWebController::class)->group(function() {
        Route::get('/colors', 'getColors')->name('colors.get');
        Route::post('/colors/create', 'createColor')->name('colors.create');
        Route::get('/colors/{colorId}/edit', 'showEditForm')->name('color.edit.form');
        Route::put('/colors/{colorId}/update', 'updateColor')->name('color.update');
        Route::delete('/colors/{colorId}/delete', 'deleteColor')->name('color.delete');
    });

    // Scents
    Route::controller(ScentWebController::class)->group(function() {
        Route::get('/scents', 'getScents')->name('scent.get');
        Route::post('/scents/create', 'createScent')->name('scents.create');
        Route::get('/scents/{scentId}/edit', 'showEditForm')->name('scent.edit.form');
        Route::put('/scents/{scentId}/update', 'updateScent')->name('scent.update');
        Route::delete('/scents/{scentId}/delete', 'deleteScent')->name('scent.delete');
    });

    // Types
    Route::controller(TypeWebController::class)->group(function() {
        Route::get('/types', 'getTypes')->name('type.get');
        Route::post('/types/create', 'createType')->name('types.create');
        Route::get('/types/{typeId}/edit', 'showEditForm')->name('type.edit.form');
        Route::put('/types/{typeId}/update', 'updateType')->name('type.update');
        Route::delete('/types/{typeId}/delete', 'deleteType')->name('type.delete');
    });

    // Statuses
    Route::controller(StatusWebController::class)->group(function() {
        Route::get('/status', 'getStatuses')->name('status.get');
        Route::post('/status/create', 'createStatus')->name('status.create');
        Route::get('/status/{statusId}/edit', 'showEditForm')->name('status.edit.form');
        Route::put('/status/{statusId}/update', 'updateStatus')->name('status.update');
        Route::delete('/status/{statusId}/delete', 'deleteStatus')->name('status.delete');
    });
});
