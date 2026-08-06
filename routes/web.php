<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CafeOrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SessionController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| المسارات (Routes)
|--------------------------------------------------------------------------
*/

// ========================
// تسجيل الدخول (Guest)
// ========================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

// ========================
// المسارات المحمية (Auth)
// ========================
Route::middleware('auth')->group(function () {
    // تسجيل الخروج
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // نقطة البيع الموحدة (الشاشة الرئيسية للنظام)
    Route::get('/', [PosController::class, 'index'])->name('dashboard');

    // ========================
    // الجلسات (متاح للمدير والكاشير)
    // ========================
    Route::middleware(CheckRole::class . ':admin,cashier')->group(function () {
        // نقطة البيع الموحدة (الشاشة الرئيسية للكاشير)
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');

        // الجلسات والطلبات
        Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index');
        Route::get('/sessions/active', [SessionController::class, 'active'])->name('sessions.active');
        Route::get('/sessions/create', [SessionController::class, 'create'])->name('sessions.create');
        Route::post('/sessions', [SessionController::class, 'store'])->name('sessions.store');
        Route::get('/sessions/{game_session}', [SessionController::class, 'show'])->name('sessions.show');
        Route::post('/sessions/{game_session}/add-item', [SessionController::class, 'addItem'])->name('sessions.addItem');
        Route::delete('/sessions/{game_session}/remove-item/{itemId}', [SessionController::class, 'removeItem'])->name('sessions.removeItem');
        Route::get('/sessions/{game_session}/checkout', [SessionController::class, 'checkout'])->name('sessions.checkout');
        Route::post('/sessions/{game_session}/close', [SessionController::class, 'close'])->name('sessions.close');
        Route::get('/sessions/{game_session}/invoice', [SessionController::class, 'invoice'])->name('sessions.invoice');
        Route::post('/sessions/{game_session}/cancel', [SessionController::class, 'cancel'])->name('sessions.cancel');

        // صالة الكافيه والطاولات والتيك أواي
        Route::get('/cafe-orders', [CafeOrderController::class, 'index'])->name('cafe-orders.index');
        Route::get('/cafe-orders/active', [CafeOrderController::class, 'active'])->name('cafe-orders.active');
        Route::get('/cafe-orders/create', [CafeOrderController::class, 'create'])->name('cafe-orders.create');
        Route::post('/cafe-orders', [CafeOrderController::class, 'store'])->name('cafe-orders.store');
        Route::get('/cafe-orders/{cafe_order}', [CafeOrderController::class, 'show'])->name('cafe-orders.show');
        Route::post('/cafe-orders/{cafe_order}/add-item', [CafeOrderController::class, 'addItem'])->name('cafe-orders.addItem');
        Route::delete('/cafe-orders/{cafe_order}/remove-item/{itemId}', [CafeOrderController::class, 'removeItem'])->name('cafe-orders.removeItem');
        Route::get('/cafe-orders/{cafe_order}/checkout', [CafeOrderController::class, 'checkout'])->name('cafe-orders.checkout');
        Route::post('/cafe-orders/{cafe_order}/close', [CafeOrderController::class, 'close'])->name('cafe-orders.close');
        Route::get('/cafe-orders/{cafe_order}/invoice', [CafeOrderController::class, 'invoice'])->name('cafe-orders.invoice');
        Route::post('/cafe-orders/{cafe_order}/cancel', [CafeOrderController::class, 'cancel'])->name('cafe-orders.cancel');

        // المصروفات
        Route::resource('expenses', ExpenseController::class)->only(['index', 'create', 'store', 'destroy']);
    });

    // ========================
    // الإدارة (المدير فقط)
    // ========================
    Route::middleware(CheckRole::class . ':admin')->group(function () {
        Route::resource('devices', DeviceController::class)->except('show');
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('products', ProductController::class)->except('show');
        Route::get('/reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
    });
});
