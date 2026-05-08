<?php

use App\Http\Controllers\CollectionWebController;
use App\Http\Controllers\ColorWebController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderWebController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\ProductVariantWebController;
use App\Http\Controllers\ProductWebController;
use App\Http\Controllers\ScentWebController;
use App\Http\Controllers\StatusWebController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\TypeWebController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserWebController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [UserWebController::class, 'login'])->name('login.process');

Route::post('/logout', [UserWebController::class, 'logout'])->name('logout');
Route::delete('/logout', [UserWebController::class, 'logout']); // Support DELETE method too

/*
|--------------------------------------------------------------------------
| ADMIN AREA - PROTECTED BY is_admin_web MIDDLEWARE
|--------------------------------------------------------------------------
*/

Route::middleware(['is_admin_web'])->group(function() {

    // Admin Dashboard Home
    Route::get('/', function() {
        $totalOrder = \App\Models\Order::count();
        $totalProduct = \App\Models\Product::count();
        $totalIncome = \App\Models\Order::where('payment_status', 'paid')->sum('total_price');
        
        return view('components.welcome', compact('totalOrder', 'totalProduct', 'totalIncome'));
    })->name('admin');

    // Sensitive Management Routes (Moved from Public)
    Route::get('/products', [ProductWebController::class, 'getProducts'])->name('products.index');
    Route::get('/products/{productId}', [ProductWebController::class, 'showProductDetail'])->name('product.detail');
    Route::get('/collections', [CollectionWebController::class, 'getCollections'])->name('collections.index');
    Route::get('/collections/{collectionId}/products', [CollectionWebController::class, 'getProductsPerCollection'])->name('collections.products');
    Route::get('/orders', [OrderWebController::class, 'showOrders'])->name('orders.index');

    // ADMIN PRODUCT VIEWS
    Route::get('/products-view', function() {
        return view('components.products.list_products');
    })->name('products');

    Route::get('/create-product', function() {
        return view('components.products.create_product');
    })->name('create-product');

    Route::get('/products/{productId}/edit-view', function($productId) {
        return view('components.products.edit_product', ['productId' => $productId]);
    })->name('edit-product');

    Route::get('/detail-product', function() {
        return view('components.products.detail_product');
    })->name('detail-product');

    Route::get('/products/{productId}/variants/create', [ProductVariantWebController::class, 'showCreateForm'])->name('variant.create.form');

    Route::get('/detail-variant', function() {
        return view('components.products.detail_product_variant');
    })->name('detail-variant');

    // ADMIN COLLECTION VIEWS
    Route::get('/collections-view', function() {
        return view('components.collections.list_collection');
    })->name('collections');

    Route::get('/create-collection', function() {
        return view('components.collections.create_collection');
    })->name('create-collection');

    // ADMIN ORDER VIEWS
    Route::get('/orders-view', function() {
        return view('components.orders.list_orders');
    })->name('orders');

    Route::get('/detail-order', function() {
        return view('components.orders.detail_order');
    })->name('detail-order');

    // ADMIN OTHER VIEWS
    Route::get('/status-create', function() {
        return view('components.other.components.status.create_status');
    })->name('create-status');

    Route::get('/type-create', function() {
        return view('components.other.components.type.create_type');
    })->name('create-type');

    Route::get('/color-create', function() {
        return view('components.other.components.color.create_color');
    })->name('create-color');

    Route::get('/scent-create', function() {
        return view('components.other.components.scent.create_scent');
    })->name('create-scent');

    // ADMIN CONTROLLER ROUTES
    Route::post('/image/upload', [ImageController::class, 'uploadImage'])->name('image.upload');

    // Product Management
    Route::post('/products/create', [ProductWebController::class, 'createProduct'])->name('products.create');
    Route::get('/products/{productId}/edit', [ProductWebController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{productId}/update', [ProductWebController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{productId}/delete', [ProductWebController::class, 'destroy'])->name('products.destroy');

    // Collection Management
    Route::post('/collections/create', [CollectionWebController::class, 'createCollection'])->name('collections.create');
    Route::get('/collections/{collectionId}/edit', [CollectionWebController::class, 'editCollection'])->name('collections.edit');
    Route::put('/collections/{collectionId}/update', [CollectionWebController::class, 'updateCollection'])->name('collections.update');
    Route::delete('/collections/{collectionId}/delete', [CollectionWebController::class, 'deleteCollection'])->name('collections.delete');

    // Order Management
    Route::get('/orders/{orderId}', [OrderWebController::class, 'showDetail'])->name('order.detail');
    Route::post('/orders/{orderId}/update-status', [OrderWebController::class, 'updateStatus'])->name('order.updateStatus');
    Route::post('/orders/{orderId}/resend-wa', [OrderWebController::class, 'resendTrackingWA'])->name('order.resendWA');

    // Product Variant Management
    Route::post('/products/{productId}/variants/create', [ProductVariantWebController::class, 'createProductVariant'])->name('variant.create');
    Route::patch('/products/{productId}/variants/{variantId}/update', [ProductVariantWebController::class, 'updateProductVariant'])->name('variant.update');
    Route::delete('/products/{productId}/variants/{variantId}/delete', [ProductVariantWebController::class, 'deleteProductVariant'])->name('variant.delete');
    
    // Type Management
    Route::controller(TypeWebController::class)->group(function() {
        Route::post('/type/create', 'createType')->name('type.create');
        Route::get('/type/get', 'getTypes')->name('type.get');
        Route::get('/type/{typeId}/edit', 'showEditForm')->name('type.edit.form');
        Route::put('/type/{typeId}/update', 'updateType')->name('type.update');
        Route::delete('/type/{typeId}/delete', 'deleteType')->name('type.delete');
    });

    // Color Management
    Route::controller(ColorWebController::class)->group(function() {
        Route::post('/color/create', 'createColor')->name('color.create');
        Route::get('/colors/get', 'getColors')->name('colors.get');
        Route::get('/color/{colorId}/edit', 'showEditForm')->name('color.edit.form');
        Route::put('/color/{colorId}/update', 'updateColor')->name('color.update');
        Route::delete('/color/{colorId}/delete', 'deleteColor')->name('color.delete');
    });

    // Scent Management
    Route::controller(ScentWebController::class)->group(function() {
        Route::post('/scent/create', 'createScent')->name('scent.create');
        Route::get('/scent/get', 'getScents')->name('scent.get');
        Route::get('/scent/{scentId}/edit', 'showEditForm')->name('scent.edit.form');
        Route::put('/scent/{scentId}/update', 'updateScent')->name('scent.update');
        Route::delete('/scent/{scentId}/delete', 'deleteScent')->name('scent.delete');
        Route::put('/scent/{scentId}/toggle', 'toggleStatus')->name('scent.toggle');
    });

    // Status Management
    Route::controller(StatusWebController::class)->group(function() {
        Route::post('/status/create', 'createStatus')->name('status.create');
        Route::get('/status/get', 'getStatuses')->name('status.get');
        Route::get('/status/{statusId}/edit', 'showEditForm')->name('status.edit.form');
        Route::put('/status/{statusId}/update', 'updateStatus')->name('status.update');
        Route::delete('/status/{statusId}/delete', 'deleteStatus')->name('status.delete');
    });

});

