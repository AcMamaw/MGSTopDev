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
use App\Http\Controllers\CategoryController; 
use App\Http\Controllers\JoborderController;


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
        Route::get('/sales', [OrderController::class, 'index'])->name('purchaseorder');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/payment', [PaymentController::class, 'index'])->name('payment');
        Route::get('/request', [RequestController::class, 'index'])->name('request');
 
        Route::get('/joborders', [JoborderController::class, 'index'])->name('joborders');
        Route::post('/joborders/{orderId}/update-status', [JoborderController::class, 'updateStatus'])
            ->name('joborders.update-status');
        
        Route::post('/joborders/{orderId}/complete', [JoborderController::class, 'completeJobOrder'])
            ->name('joborders.complete');

        Route::get('/joborders/joborder-history', [JoborderController::class, 'jobOrderHistory'])->name('joborders.joborder-history');

        Route::get('/joborders/{joborderId}', [JoborderController::class, 'show'])
            ->name('joborders.show');});
        
        Route::post('/joborders/{orderId}/pick', [JoborderController::class, 'pickJobOrder'])->name('joborders.pick');
        Route::post('/joborders/{orderId}/done', [JoborderController::class, 'doneJobOrder'])->name('joborders.done');

        Route::post('/deliveries/{deliveryId}/update-status', [RequestController::class, 'updateStatus'])
            ->name('deliveries.update-status');

        Route::post('/stock-adjustments/{id}/approve', [RequestController::class, 'approveStockAdjustment'])
            ->name('stock-adjustments.approve');
       
        Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');

        Route::post('/payments/update', [OrderController::class, 'updatePayment'])->name('payments.update');

        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');

        Route::post('/reports/generate', [ReportController::class, 'generate'])
            ->name('reports.generate');

        Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    // --------------------------
    // Inventory 
    // --------------------------
    Route::prefix('inventory')->group(function () {
        Route::get('/instock', [StockInController::class, 'index'])->name('instock');
        Route::get('/outstock', [StockOutController::class, 'index'])->name('outstock');
        Route::get('/stock', [InventoryController::class, 'index'])->name('stock');

        Route::post('/instock/store', [StockInController::class, 'store'])->name('instock.store');
        Route::post('/instock/storeproduct', [StockInController::class, 'storeProduct'])->name('instock.storeProduct');

        
        // Stock Adjustment
        Route::get('/stockadjustment', [StockAdjustmentController::class, 'index'])->name('stockadjustment');
        Route::post('/stockadjustment/store', [StockAdjustmentController::class, 'store'])->name('stockadjustment.store');
        Route::post('/stock-adjustments/{id}/approve', [StockAdjustmentController::class, 'approveStockAdjustment'])->name('stockadjustments.approve');
        Route::patch('/stockadjustment/{id}/reject', [StockAdjustmentController::class, 'reject'])->name('stockadjustment.reject');
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
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');  
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::post('/products/{product}/image', [ProductController::class, 'updateImage'])->name('products.updateImage');
   });

    // --------------------------
    // Store Management
    // --------------------------
    Route::prefix('managestore')->group(function () {
        Route::get('/product', [ProductController::class, 'index'])->name('product');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/category', [CategoryController::class, 'index'])->name('category');
        Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
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
     Route::get('/employees/active', [OrderController::class, 'getActiveEmployees'])
        ->name('employees.active');
    
    Route::post('/orders/assign', [OrderController::class, 'assignJobOrder'])
        ->name('orders.assign');  

    Route::post('/auth/users/store', [AuthController::class, 'createEmployeeUser'])
        ->middleware('role:Admin')->name('auth.users.store');

    
    // --------------------------
    // Archive 
    // --------------------------
    Route::put('/categories/{category}/archive', [CategoryController::class, 'archive'])
    ->name('categories.archive');
    Route::put('/categories/{category}/unarchive', [CategoryController::class, 'unarchive'])
        ->name('categories.unarchive');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
        ->name('categories.destroy');

    Route::put('/products/{product}/archive', [ProductController::class, 'archive'])
        ->name('products.archive');
    Route::put('/products/{product}/unarchive', [ProductController::class, 'unarchive'])
        ->name('products.unarchive');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->name('products.destroy');


    Route::put('/employees/{id}/archive', [EmployeeController::class, 'archive'])
        ->name('employees.archive');
    Route::put('/employees/{id}/unarchive', [EmployeeController::class, 'unarchive'])
        ->name('employees.unarchive');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])
        ->name('employees.destroy');


    Route::put('/suppliers/{id}/archive', [SupplierController::class, 'archive'])
        ->name('suppliers.archive');
    Route::put('/suppliers/{id}/unarchive', [SupplierController::class, 'unarchive'])
        ->name('suppliers.unarchive');
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])
        ->name('suppliers.destroy');
    

    Route::put('/roles/{id}/archive', [RoleController::class, 'archive'])
        ->name('roles.archive');
    Route::put('/roles/{id}/unarchive', [RoleController::class, 'unarchive'])
        ->name('roles.unarchive');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])
        ->name('roles.destroy');
      
    Route::put('/customers/{id}/archive', [CustomerController::class, 'archive'])
        ->name('customers.archive');

    Route::put('/customers/{id}/unarchive', [CustomerController::class, 'unarchive'])
        ->name('customers.unarchive');

    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])
        ->name('customers.destroy');
    
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
