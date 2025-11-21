<?php

use App\Http\Controllers\ContactUsController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CostController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SamplePackController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

// required in header "  Accept:application/json "
Route::group(['prefix' => 'v1'], function () {

    // common
    Route::post('upload_file', [Controller::class, 'file_upload_api']);

    Route::post('delete_file', [Controller::class, 'delete_file_api']);
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/enum-values/{table}/{column}', [Controller::class, 'getEnumValues']);
    });

    Route::group(['prefix' => 'auth'], function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('check_availability', [AuthController::class, 'check_availability']);
        Route::post('send_otp', [AuthController::class, 'send_otp']);
        Route::post('verify_otp', [AuthController::class, 'verify_otp']);
        Route::post('reset_password', [AuthController::class, 'reset_password']);

        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::get('logout', [AuthController::class, 'logout']);
        });
    });

    Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () { // , 'CheckPermissions'
        Route::apiResource('users', UserController::class);
        Route::apiResource('categories', CategoriesController::class);
        Route::apiResource('products', ProductController::class);
        Route::apiResource('orders', OrderController::class);
        Route::apiResource('coupons', CouponController::class);

        Route::get('system/{id}', [SystemController::class, 'get']);
        Route::post('system/{id}', [SystemController::class, 'update']);
        Route::post('order-update/{id}', [OrderController::class, 'orderUpdate']);

        Route::put('categories/{id}/status', [CategoriesController::class, 'updateStatus']);
        Route::put('products/{id}/status', [ProductController::class, 'updateStatus']);
        Route::put('products/{id}/duplicate', [ProductController::class, 'duplicateProduct']);
        Route::put('products/{id}/options', [ProductController::class, 'updateOptions']);
        Route::put('products/{id}/json', [ProductController::class, 'updateJson']);

        Route::get('/roles', [RoleController::class, 'index']);
        Route::put('update-user-status/{id}', [UserController::class, 'updateStatus']);

        Route::post('download-file', [Controller::class, 'downloadFile']);

        Route::get('/dashboard/stats', [DashboardController::class, 'getSystemStats']);

    });

    // (Public APIs)
    Route::prefix('frontend')->group(function () {
        Route::post('apply-coupon', [CouponController::class, 'apply']);
        Route::post('/sample-pack-request', [SamplePackController::class, 'store']);
        Route::post('/contact-us', [ContactUsController::class, 'store']);
        Route::get('categories/{category_id?}', [CategoriesController::class, 'getCategories']);
        Route::get('product/{product_name}', [ProductController::class, 'getProduct']);
        Route::get('products/{category_id?}', [ProductController::class, 'getProducts']);
        Route::get('configaration/{id}', [SystemController::class, 'getConfigarations']);
        Route::get('order_item/{id}', [CartController::class, 'getOrderItem']);
        Route::get('autocomplete/products', [ProductController::class, 'autocomplete']);

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('orders', [OrderController::class, 'getOrders']);
            Route::post('profile_update', [UserController::class, 'profile_update']);
            Route::post('update_password', [UserController::class, 'update_password']);
        });
    });

    Route::prefix('cart')->group(function () {
        Route::post('/estimate_cost', [CostController::class, 'estimate_cost']);
        Route::post('/add', [CartController::class, 'addToCart']);
        Route::post('/get', [CartController::class, 'getCart']);                         // Get cart list
        Route::post('/update/{orderItemId}', [CartController::class, 'updateCartItem']); // Update item quantity or remove item
    });

    Route::prefix('checkout')->middleware(['auth:sanctum'])->group(function () {
        Route::post('coupons/apply', [CouponController::class, 'apply']);

        Route::post('/details/{orderId}', [CheckoutController::class, 'updateOrderDetails']); // Update order details
        Route::post('/status/{orderId}', [CheckoutController::class, 'updateOrderStatus']);   // Update order status or payment status
        Route::post('/payment/{orderId}', [CheckoutController::class, 'processPayment']);     // Process payment
    });
});
