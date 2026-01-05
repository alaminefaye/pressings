<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\PriceController;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // Orders
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('/orders/{id}/assign', [OrderController::class, 'assignEmployee'])->name('orders.assign');

        // Customers
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');

        // Services
        Route::resource('services', ServiceController::class);

        // Prices
        Route::get('/prices', [PriceController::class, 'index'])->name('prices.index');
        Route::post('/prices', [PriceController::class, 'update'])->name('prices.update');

        // Employees
        Route::resource('employees', \App\Http\Controllers\Admin\EmployeeController::class);

        // Drivers
        Route::resource('drivers', \App\Http\Controllers\Admin\DriverController::class);

        // Deliveries
        Route::get('/deliveries', [\App\Http\Controllers\Admin\DeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('/deliveries/{id}', [\App\Http\Controllers\Admin\DeliveryController::class, 'show'])->name('deliveries.show');
        Route::post('/deliveries/{id}/assign', [\App\Http\Controllers\Admin\DeliveryController::class, 'assignDriver'])->name('deliveries.assign');
        Route::post('/deliveries/{id}/status', [\App\Http\Controllers\Admin\DeliveryController::class, 'updateStatus'])->name('deliveries.update-status');

        // Promotions
        Route::resource('promotions', \App\Http\Controllers\Admin\PromotionController::class);

        // Reports
        Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');

        // Settings
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    });
});
