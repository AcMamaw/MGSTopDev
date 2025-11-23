<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController; 
use App\Http\Controllers\RequestController;

// --------------------------
// Guest Routes
// --------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

// --------------------------
// Authenticated Routes
// --------------------------
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --------------------------
    // Main Content
    // --------------------------
    Route::prefix('maincontent')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']); // optional alias
        Route::get('/delivery', [DeliveryController::class, 'index'])->name('delivery');
        Route::get('/purchaseorder', [OrderController::class, 'index'])->name('purchaseorder');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/payment', [PaymentController::class, 'index'])->name('payment');
        Route::get('/request', [RequestController::class, 'index'])->name('request');
    });

    // --------------------------
    // Inventory 
    // --------------------------
    Route::prefix('inventory')->group(function () {
        Route::get('/instock', [StockInController::class, 'index'])->name('instock');
        Route::get('/outstock', [StockOutController::class, 'index'])->name('outstock');
        Route::get('/stock', [InventoryController::class, 'index'])->name('stock');

        // Stock Adjustment
        Route::get('/stockadjustment', [StockAdjustmentController::class, 'index'])->name('stockadjustment');
    });

    // --------------------------
    // --------------------------
    // Management 
    // --------------------------
    Route::prefix('management')->group(function () {
        // View pages
        Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
        Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier');
        Route::get('/employee', [EmployeeController::class, 'index'])->name('employee');
        Route::get('/role', [RoleController::class, 'index'])->name('role');

        // Form submissions
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');

        // Optional fetch route
        Route::get('/roles/fetch', [RoleController::class, 'fetch'])->name('roles.fetch');
    });

    // --------------------------
    // Store Management
    // --------------------------
    Route::prefix('managestore')->group(function () {
        Route::get('/product', [ProductController::class, 'index'])->name('product');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    });

    // --------------------------
    // Deliveries
    // --------------------------
    Route::post('/delivery/store', [DeliveryController::class, 'store'])->name('deliveries.store');
    Route::post('/deliveries/{delivery}/stock-in', [DeliveryController::class, 'stockIn'])->name('deliveries.stockin');
    Route::post('/deliveries/{delivery}/update-status', [RequestController::class, 'updateStatus'])->name('deliveries.updateStatus');

    // --------------------------
    // Orders
    // --------------------------
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
    Route::post('/orders/customer/store', [OrderController::class, 'storeCustomer'])->name('orders.customer.store');
    Route::post('/orders/{order}/complete', [OrderController::class, 'markAsCompleted'])->name('orders.complete');
    Route::post('/payments/update', [OrderController::class, 'updatePayment'])->name('payments.update');

    // --------------------------
    // Employee Users (Admin only)
    // --------------------------
    Route::post('/auth/users/store', [AuthController::class, 'createEmployeeUser'])
        ->middleware('role:Admin')->name('auth.users.store');

    // --------------------------
    // Logout
    // --------------------------
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// --------------------------
// Default home redirect
// --------------------------
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});
