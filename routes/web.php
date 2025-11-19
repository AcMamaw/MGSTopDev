<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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
use App\Http\Controllers\DashboardController;


// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('maincontent.dashboard');
    })->name('dashboard');

    // Main content
    Route::prefix('maincontent')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/delivery', [DeliveryController::class, 'index'])->name('delivery');
        Route::get('/purchaseorder', [OrderController::class, 'index'])->name('purchaseorder');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/payment', [PaymentController::class, 'index'])->name('payment');
    });

    // Inventory
    Route::prefix('inventory')->group(function () {
        Route::get('/instock', [StockInController::class, 'index'])->name('instock');
        Route::get('/outstock', [StockOutController::class, 'index'])->name('outstock');
        Route::get('/stock', [InventoryController::class, 'index'])->name('stock');
        Route::get('/stockadjustment', [StockAdjustmentController::class, 'index'])->name('stockadjustment');
    });

    // Management
    Route::prefix('management')->group(function () {
        Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
        Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier');
        Route::get('/employee', [EmployeeController::class, 'index'])->name('employee');
        Route::get('/role', [RoleController::class, 'index'])->name('role');
    });

    // Store
    Route::prefix('managestore')->group(function () {
        Route::get('/product', [ProductController::class, 'index'])->name('product');
    });

    // Form submissions
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::post('/delivery/store', [DeliveryController::class, 'store'])->name('deliveries.store');
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/auth/users/store', [AuthController::class, 'createEmployeeUser'])->name('auth.users.store');
    Route::post('/deliveries/{delivery}/stock-in', [DeliveryController::class, 'stockIn'])->name('deliveries.stockin');
    Route::post('/orders/customer/store', [OrderController::class, 'storeCustomer'])->name('orders.customer.store');

    Route::get('/roles/fetch', [RoleController::class, 'fetch'])->name('roles.fetch');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Default home
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});
