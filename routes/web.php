<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

/* ---------------- Storefront ---------------- */
Route::get('/', [StoreController::class, 'home'])->name('home');
Route::get('/shop', [StoreController::class, 'shop'])->name('shop');
Route::get('/collections/{category:slug}', [StoreController::class, 'shop'])->name('category');
Route::get('/product/{product:slug}', [StoreController::class, 'product'])->name('product');
Route::post('/product/{product}/review', [StoreController::class, 'review'])->name('review')->middleware('throttle:5,1');
Route::get('/wishlist', [StoreController::class, 'wishlist'])->name('wishlist');
Route::get('/track', [StoreController::class, 'track'])->name('track');
Route::get('/contact', [StoreController::class, 'contact'])->name('contact');
Route::post('/contact', [StoreController::class, 'sendMessage'])->name('contact.send')->middleware('throttle:5,1');
Route::post('/subscribe', [StoreController::class, 'subscribe'])->name('subscribe')->middleware('throttle:5,1');
Route::get('/pages/{page}', [StoreController::class, 'page'])->name('page')
    ->whereIn('page', ['about', 'faq', 'shipping', 'returns', 'privacy', 'terms']);

Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::get('/cart/drawer', [CartController::class, 'drawer'])->name('cart.drawer');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/coupon', [CartController::class, 'coupon'])->name('cart.coupon');

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'place'])->name('checkout.place')->middleware('throttle:10,1');
Route::get('/order/{number}/thank-you', [CheckoutController::class, 'success'])->name('order.success');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AccountController::class, 'loginForm'])->name('login');
    Route::post('/login', [AccountController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AccountController::class, 'registerForm'])->name('register');
    Route::post('/register', [AccountController::class, 'register'])->middleware('throttle:10,1');
});
Route::post('/logout', [AccountController::class, 'logout'])->name('logout')->middleware('auth');
Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::post('/account', [AccountController::class, 'update'])->name('account.update');
    Route::get('/account/orders/{number}', [AccountController::class, 'order'])->name('account.order');
});

/* ---------------- Admin dashboard ---------------- */
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [Admin\AuthController::class, 'form'])->name('login');
    Route::post('/login', [Admin\AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware('staff')->group(function () {
        Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/export', [Admin\OrderController::class, 'export'])->name('orders.export');
        Route::get('/orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/invoice', [Admin\OrderController::class, 'invoice'])->name('orders.invoice');
        Route::put('/orders/{order}', [Admin\OrderController::class, 'update'])->name('orders.update');
        Route::post('/orders/bulk', [Admin\OrderController::class, 'bulk'])->name('orders.bulk');

        Route::resource('products', Admin\ProductController::class)->except('show');
        Route::post('/products/{product}/duplicate', [Admin\ProductController::class, 'duplicate'])->name('products.duplicate');
        Route::delete('/product-images/{image}', [Admin\ProductController::class, 'deleteImage'])->name('products.image.delete');
        Route::resource('categories', Admin\CategoryController::class)->except('show');

        Route::get('/customers', [Admin\CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customer}', [Admin\CustomerController::class, 'show'])->name('customers.show');

        Route::resource('coupons', Admin\CouponController::class)->except('show');

        Route::get('/reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
        Route::put('/reviews/{review}', [Admin\ReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/reviews/{review}', [Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

        Route::get('/messages', [Admin\MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{message}', [Admin\MessageController::class, 'show'])->name('messages.show');
        Route::delete('/messages/{message}', [Admin\MessageController::class, 'destroy'])->name('messages.destroy');
        Route::get('/subscribers', [Admin\MessageController::class, 'subscribers'])->name('subscribers.index');
        Route::get('/subscribers/export', [Admin\MessageController::class, 'exportSubscribers'])->name('subscribers.export');

        Route::middleware('admin')->group(function () {
            Route::get('/settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
            Route::put('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');
            Route::get('/staff', [Admin\StaffController::class, 'index'])->name('staff.index');
            Route::post('/staff', [Admin\StaffController::class, 'store'])->name('staff.store');
            Route::delete('/staff/{user}', [Admin\StaffController::class, 'destroy'])->name('staff.destroy');
        });
    });
});
