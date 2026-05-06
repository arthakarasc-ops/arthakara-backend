<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AddressController,
    BillingAddressController,
    CartController,
    CollectionController,
    ColorController,
    ImageController,
    ScentController,
    PaymentController,
    NameController,
    OrderController,
    ProductController,
    ProductVariantController,
    ShippingMethodController,
    StatusController,
    TypeController,
    UserController
};

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::controller(UserController::class)->group(function () {
    Route::post('/auth/register', 'userRegister');
    Route::post('/auth/login', 'userLogin');
    Route::post('/auth/admin-login', 'adminLogin');
});

/*
|--------------------------------------------------------------------------
| PUBLIC PRODUCT
|--------------------------------------------------------------------------
*/
Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'getProducts');
    Route::get('/products/{productId}', 'getProductDetail')->whereNumber('productId');
    Route::get('/collections/{collectionId}/products', 'getProductsPerCollection')->whereNumber('collectionId');
});

/*
|--------------------------------------------------------------------------
| PUBLIC DATA
|--------------------------------------------------------------------------
*/
Route::get('/collections', [CollectionController::class, 'getCollections']);
Route::get('/names', [NameController::class, 'getAllProductCollectionNames']);
Route::get('/colors', [ColorController::class, 'getColors']);
Route::get('/scents', [ScentController::class, 'index']); // 🔥 scent public

Route::get('/sizes', [ColorController::class, 'getSizes']);
Route::get('/fabrics', [ColorController::class, 'getFabrics']);
Route::get('/statuses', [StatusController::class, 'getStatuses']);

/*
|--------------------------------------------------------------------------
| SHIPPING
|--------------------------------------------------------------------------
*/
Route::controller(ShippingMethodController::class)->group(function () {
    Route::get('/shipping-methods', 'getShippingMethods');
    Route::get('/shipping-methods/{methodId}', 'getShippingMethod')->whereNumber('methodId');
});



/*
|--------------------------------------------------------------------------
| IMAGE
|--------------------------------------------------------------------------
*/
Route::post('/image/upload', [ImageController::class, 'uploadImage']);

/*
|--------------------------------------------------------------------------
| PAYMENT (Callback - tanpa auth, dipanggil oleh Midtrans)
|--------------------------------------------------------------------------
*/
Route::post('/midtrans-callback', [PaymentController::class, 'callback']);

/*
|--------------------------------------------------------------------------
| AUTH USER
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/billing-addresses/create', [BillingAddressController::class, 'createBillingAddress']);

    /*
    |--------------------------------------------------------------------------
    | USER PROFILE
    |--------------------------------------------------------------------------
    */
    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'getUserData');
        Route::put('/users/update', 'updateUser');
        Route::delete('/logout', 'logoutUser');
    });

    /*
    |--------------------------------------------------------------------------
    | USER ADDRESS
    |--------------------------------------------------------------------------
    */
    Route::controller(AddressController::class)->group(function () {
        Route::get('/users/addresses', 'getUserAddress');
        Route::post('/users/address/create', 'createAddress');
        Route::put('/users/address/{addressId}/edit', 'updateUserAddress')->whereNumber('addressId');
        Route::delete('/users/address/{addressId}/delete', 'deleteUserAddress')->whereNumber('addressId');
    });

    /*
    |--------------------------------------------------------------------------
    | USER ORDER
    |--------------------------------------------------------------------------
    */
    Route::controller(OrderController::class)->group(function () {
        Route::get('/users/orders', 'getUserOrders');
        Route::get('/orders/{orderId}', 'getOrderDetail')->whereNumber('orderId');
        Route::post('/orders/create', 'createNewOrder');
    });

    /*
    |--------------------------------------------------------------------------
    | PAYMENT (Create - butuh auth)
    |--------------------------------------------------------------------------
    */
    Route::post('/pay', [PaymentController::class, 'createTransaction']);

    /*
    |--------------------------------------------------------------------------
    | USER CART
    |--------------------------------------------------------------------------
    */
    Route::controller(CartController::class)->group(function () {
        Route::get('/cart', 'index');
        Route::post('/cart', 'store');
        Route::put('/cart/{id}', 'update')->whereNumber('id');
        Route::delete('/cart/{id}', 'destroy')->whereNumber('id');
        Route::delete('/cart', 'clear');
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY
    |--------------------------------------------------------------------------
    */
    Route::middleware(['is_admin'])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | PRODUCT ADMIN
        |--------------------------------------------------------------------------
        */
        Route::controller(ProductController::class)->group(function () {
            Route::post('/products/create', 'createProduct');
            Route::put('/products/{productId}/update', 'updateProduct')->whereNumber('productId');
            Route::delete('/products/{productId}/delete', 'deleteProduct')->whereNumber('productId');
        });

        /*
        |--------------------------------------------------------------------------
        | PRODUCT VARIANT (WARNA + STOCK)
        |--------------------------------------------------------------------------
        */
        Route::controller(ProductVariantController::class)->group(function () {
            Route::post('/products/create/{productId}/variant', 'createProductVariant')->whereNumber('productId');
            Route::patch('/products/update/{productVariantId}/variant', 'updateProductVariant')->whereNumber('productVariantId');
            Route::delete('/products/delete/{productVariantId}/variant', 'deleteProductVariant')->whereNumber('productVariantId');
        });

        /*
        |--------------------------------------------------------------------------
        | COLLECTION ADMIN
        |--------------------------------------------------------------------------
        */
        Route::controller(CollectionController::class)->group(function () {
            Route::post('/collections/create', 'createCollection');
            Route::put('/collections/{collectionId}/update', 'updateCollection')->whereNumber('collectionId');
            Route::delete('/collections/{collectionId}/delete', 'deleteCollection')->whereNumber('collectionId');
        });

        /*
        |--------------------------------------------------------------------------
        | SCENT ADMIN 🔥
        |--------------------------------------------------------------------------
        */
        Route::controller(ScentController::class)->group(function () {
            Route::post('/scents', 'store');
            Route::put('/scents/{id}', 'update');
            Route::delete('/scents/{id}', 'destroy');
        });

        /*
        |--------------------------------------------------------------------------
        | COLOR ADMIN 🔥
        |--------------------------------------------------------------------------
        */
        Route::controller(ColorController::class)->group(function () {
            Route::post('/colors', 'store');
            Route::put('/colors/{color}', 'update');
            Route::delete('/colors/{color}', 'destroy');
        });
    });
});