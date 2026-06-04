<?php

use App\Http\Controllers\CollectionWebController;
use App\Http\Controllers\ColorWebController;
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

// Redirect old login path to new admin login path
Route::redirect('/login', '/admin/login');
Route::redirect('/', '/admin');

// Authentication Routes
Route::get('/admin/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/admin/login', [UserWebController::class, 'login'])->name('login.process');
Route::post('/admin/logout', [UserWebController::class, 'logout'])->name('logout');
Route::delete('/admin/logout', [UserWebController::class, 'logout']);

// Admin Area - Protected by is_admin_web
Route::middleware(['is_admin_web'])->prefix('admin')->group(function () {
    
    // Dashboard Home
    Route::get('/', function() {
        $totalOrder = \App\Models\Order::count();
        $totalProduct = \App\Models\Product::count();
        $totalIncome = \App\Models\Order::where('payment_status', 'paid')->sum('total_price');
        return view('components.welcome', compact('totalOrder', 'totalProduct', 'totalIncome'));
    })->name('admin');

    // Orders
    Route::controller(OrderWebController::class)->group(function() {
        Route::get('/orders', 'showOrders')->name('orders.index');
        Route::get('/orders/{orderId}', 'showDetail')->name('orders.detail');
        Route::patch('/orders/{orderId}/status', 'updateStatus')->name('orders.updateStatus');
        Route::post('/orders/{orderId}/resend-wa', 'resendTrackingWA')->name('orders.resendWA');
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
    Route::controller(ProductVariantWebController::class)->group(function() {
        Route::get('/products/{productId}/variants/create', 'showCreateForm')->name('variant.create.form');
        Route::post('/products/{productId}/variants/create', 'createProductVariant')->name('variant.create');
        Route::patch('/products/{productId}/variants/{variantId}/update', 'updateProductVariant')->name('variant.update');
        Route::delete('/products/{productId}/variants/{variantId}/delete', 'deleteProductVariant')->name('variant.delete');
    });

    // Collections
    Route::controller(CollectionWebController::class)->group(function() {
        Route::get('/collections', 'getCollections')->name('collections.index');
        Route::get('/collections/create', function() {
            return view('components.collections.create_collection');
        })->name('collections.create.form');
        Route::post('/collections/create', 'createCollection')->name('collections.create');
        Route::get('/collections/{collectionId}/products', 'getProductsPerCollection')->name('collections.products');
        Route::get('/collections/{collectionId}/edit', 'editCollection')->name('collections.edit.form');
        Route::put('/collections/{collectionId}/update', 'updateCollection')->name('collections.update');
        Route::delete('/collections/{collectionId}/delete', 'deleteCollection')->name('collections.delete');
    });

    // Colors
    Route::controller(ColorWebController::class)->group(function() {
        Route::get('/colors/get', 'getColors')->name('colors.index');
        Route::get('/colors/create', function() {
            return view('components.other.components.color.create_color');
        })->name('colors.create.form');
        Route::post('/colors/create', 'createColor')->name('colors.create');
        Route::get('/colors/{colorId}/edit', 'showEditForm')->name('colors.edit.form');
        Route::put('/colors/{colorId}/update', 'updateColor')->name('colors.update');
        Route::delete('/colors/{colorId}/delete', 'deleteColor')->name('colors.delete');
    });

    // Scents
    Route::controller(ScentWebController::class)->group(function() {
        Route::get('/scents/get', 'getScents')->name('scents.index');
        Route::get('/scents/create', function() {
            return view('components.other.components.scent.create_scent');
        })->name('scents.create.form');
        Route::post('/scents/create', 'createScent')->name('scents.create');
        Route::get('/scents/{scentId}/edit', 'showEditForm')->name('scents.edit.form');
        Route::put('/scents/{scentId}/update', 'updateScent')->name('scents.update');
        Route::delete('/scents/{scentId}/delete', 'deleteScent')->name('scents.delete');
        Route::put('/scents/{scentId}/toggle', 'toggleStatus')->name('scents.toggle');
    });

    // Types
    Route::controller(TypeWebController::class)->group(function() {
        Route::get('/types/get', 'getTypes')->name('types.index');
        Route::get('/types/create', function() {
            return view('components.other.components.type.create_type');
        })->name('types.create.form');
        Route::post('/types/create', 'createType')->name('types.create');
        Route::get('/types/{typeId}/edit', 'showEditForm')->name('types.edit.form');
        Route::put('/types/{typeId}/update', 'updateType')->name('types.update');
        Route::delete('/types/{typeId}/delete', 'deleteType')->name('types.delete');
    });

    // Statuses
    Route::controller(StatusWebController::class)->group(function() {
        Route::get('/status/get', 'getStatuses')->name('statuses.index');
        Route::get('/status/create', function() {
            return view('components.other.components.status.create_status');
        })->name('statuses.create.form');
        Route::post('/status/create', 'createStatus')->name('statuses.create');
        Route::get('/status/{statusId}/edit', 'showEditForm')->name('statuses.edit.form');
        Route::put('/status/{statusId}/update', 'updateStatus')->name('statuses.update');
        Route::delete('/status/{statusId}/delete', 'deleteStatus')->name('statuses.delete');
    });

    // Helper Route to run migrations on cPanel
    Route::get('/run-migration', function () {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            return "Migration success: <br><pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
        } catch (\Exception $e) {
            return "Migration failed: " . $e->getMessage();
        }
    })->name('admin.migrate');
});

// ⚠️ TEMPORARY - Log viewer untuk debugging
Route::get('/view-logs', function () {
    $logPath = storage_path('logs/laravel.log');
    if (!file_exists($logPath)) {
        return 'Log file tidak ditemukan.';
    }
    $lines = file($logPath);
    $lastLines = array_slice($lines, -100); // Ambil 100 baris terakhir
    return '<pre>' . implode('', $lastLines) . '</pre>';
});