<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\MedicineController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
|
| Routes for API version 1.
|
*/

// Public routes with auth rate limiter (5/min - brute force protection)
Route::middleware('throttle:auth')->group(function (): void {
    Route::post('register', [AuthController::class, 'register'])->name('api.v1.register');
    Route::post('login', [AuthController::class, 'login'])->name('api.v1.login');
});

// Protected routes with authenticated rate limiter (120/min)
Route::middleware(['auth:sanctum', 'throttle:authenticated'])->group(function (): void {
    Route::post('logout', [AuthController::class, 'logout'])->name('api.v1.logout');
    Route::get('me', [AuthController::class, 'me'])->name('api.v1.me');

    // Email verification
    Route::post('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware('signed')
        ->name('verification.verify');
    Route::post('email/resend', [AuthController::class, 'resendVerificationEmail'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Cart
    Route::get('cart', [CartController::class, 'index'])->name('api.v1.cart.index');
    Route::post('cart', [CartController::class, 'store'])->name('api.v1.cart.store');
    Route::put('cart/{cartItem}', [CartController::class, 'update'])->name('api.v1.cart.update');
    Route::delete('cart/{cartItem}', [CartController::class, 'destroy'])->name('api.v1.cart.destroy');
    Route::delete('cart', [CartController::class, 'clear'])->name('api.v1.cart.clear');

    // OTP Verification
    Route::post('otp/send', [AuthController::class, 'sendOtp'])->name('api.v1.otp.send');
    Route::post('otp/verify', [AuthController::class, 'verifyOtp'])->name('api.v1.otp.verify');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('api.v1.notifications.index');
    Route::get('notifications/unread', [NotificationController::class, 'unread'])->name('api.v1.notifications.unread');
    Route::patch('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('api.v1.notifications.read');
    Route::patch('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('api.v1.notifications.read-all');

    // Orders (Customer)
    Route::post('orders', [OrderController::class, 'placeOrder'])->name('api.v1.orders.place');
    Route::get('orders', [OrderController::class, 'myOrders'])->name('api.v1.orders.index');
    Route::get('orders/{order}', [OrderController::class, 'showMyOrder'])->name('api.v1.orders.show');
    Route::get('orders/{order}/receipt', [OrderController::class, 'receipt'])->name('api.v1.orders.receipt');

    // Admin routes
    Route::middleware('admin')->group(function (): void {
        // Categories (admin)
        Route::post('categories', [CategoryController::class, 'store'])->name('api.v1.categories.store');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('api.v1.categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('api.v1.categories.destroy');

        // Medicines (admin)
        Route::post('medicines', [MedicineController::class, 'store'])->name('api.v1.medicines.store');
        Route::put('medicines/{medicine}', [MedicineController::class, 'update'])->name('api.v1.medicines.update');
        Route::delete('medicines/{medicine}', [MedicineController::class, 'destroy'])->name('api.v1.medicines.destroy');
        Route::patch('medicines/{medicine}/toggle', [MedicineController::class, 'toggle'])->name('api.v1.medicines.toggle');
        Route::delete('medicines/{medicine}/images/{image}', [MedicineController::class, 'deleteImage'])->name('api.v1.medicines.images.destroy');

        // Orders (admin)
        Route::get('admin/orders', [OrderController::class, 'allOrders'])->name('api.v1.admin.orders.index');
        Route::get('admin/orders/{order}', [OrderController::class, 'showOrder'])->name('api.v1.admin.orders.show');
        Route::patch('admin/orders/{order}/confirm', [OrderController::class, 'confirm'])->name('api.v1.admin.orders.confirm');
        Route::patch('admin/orders/{order}/reject', [OrderController::class, 'reject'])->name('api.v1.admin.orders.reject');
        Route::patch('admin/orders/{order}/complete', [OrderController::class, 'complete'])->name('api.v1.admin.orders.complete');
        Route::get('admin/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('api.v1.admin.orders.receipt');

        // Reports
        Route::get('admin/reports/sales', [ReportController::class, 'sales'])->name('api.v1.admin.reports.sales');
        Route::get('admin/reports/inventory', [ReportController::class, 'inventory'])->name('api.v1.admin.reports.inventory');
        Route::get('admin/reports/frequently-out-of-stock', [ReportController::class, 'frequentlyOutOfStock'])->name('api.v1.admin.reports.frequently-out-of-stock');

        // Low stock alerts
        Route::get('admin/notifications/low-stock', [NotificationController::class, 'lowStockAlerts'])->name('api.v1.admin.notifications.low-stock');
    });
});

// Public routes
Route::get('categories', [CategoryController::class, 'index'])->name('api.v1.categories.index');
Route::get('categories/{category}', [CategoryController::class, 'show'])->name('api.v1.categories.show');
Route::get('medicines', [MedicineController::class, 'index'])->name('api.v1.medicines.index');
Route::get('medicines/{medicine}', [MedicineController::class, 'show'])->name('api.v1.medicines.show');

// Password reset routes (public with rate limiting)
Route::middleware('throttle:6,1')->group(function (): void {
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])
        ->name('password.email');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])
        ->name('password.reset');
});
