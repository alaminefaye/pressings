<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ClothingTypeController;
use App\Http\Controllers\Api\PriceController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\StatisticsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/send-otp', [AuthController::class, 'requestOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    // Public catalog endpoints
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);
    Route::get('/clothing-types', [ClothingTypeController::class, 'index']);
    Route::get('/clothing-types/{id}', [ClothingTypeController::class, 'show']);
    Route::get('/prices', [PriceController::class, 'index']);
    Route::get('/prices/matrix', [PriceController::class, 'matrix']);
    Route::post('/prices/calculate', [PriceController::class, 'calculate']);
    Route::get('/promotions', [PromotionController::class, 'index']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        
        // Auth
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
        });

        // Addresses
        Route::apiResource('addresses', AddressController::class);
        Route::post('/addresses/{id}/set-default', [AddressController::class, 'setDefault']);

        // Promotions (validation)
        Route::post('/promotions/validate', [PromotionController::class, 'validateCode']);

        // Orders
        Route::get('/orders', [OrderController::class, 'index']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/statistics', [OrderController::class, 'statistics']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);

        // Payments
        Route::post('/orders/{id}/payment', [PaymentController::class, 'process']);
        Route::get('/payments/history', [PaymentController::class, 'history']);

        // Wallet
        Route::get('/wallet', [WalletController::class, 'show']);
        Route::get('/wallet/transactions', [WalletController::class, 'transactions']);
        Route::get('/wallet/statistics', [WalletController::class, 'statistics']);
        Route::post('/wallet/add-funds', [WalletController::class, 'addFunds']);

        // Deliveries
        Route::get('/deliveries', [DeliveryController::class, 'index']);
        Route::get('/deliveries/{id}', [DeliveryController::class, 'show']);
        Route::get('/deliveries/{id}/driver-location', [DeliveryController::class, 'getDriverLocation']);
        Route::get('/deliveries/{id}/location-history', [DeliveryController::class, 'locationHistory']);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
        Route::post('/notifications/test', [NotificationController::class, 'test']);

        // Driver specific routes
        Route::middleware('driver')->group(function () {
            Route::post('/deliveries/{id}/start', [DeliveryController::class, 'start']);
            Route::post('/deliveries/{id}/complete', [DeliveryController::class, 'complete']);
            Route::post('/deliveries/{id}/update-location', [DeliveryController::class, 'updateLocation']);
            Route::get('/driver/statistics', [DeliveryController::class, 'driverStatistics']);
        });

        // Admin routes (services, clothing types, prices, promotions management)
        Route::middleware('admin')->group(function () {
            // Services
            Route::post('/services', [ServiceController::class, 'store']);
            Route::put('/services/{id}', [ServiceController::class, 'update']);
            Route::delete('/services/{id}', [ServiceController::class, 'destroy']);

            // Clothing Types
            Route::post('/clothing-types', [ClothingTypeController::class, 'store']);
            Route::put('/clothing-types/{id}', [ClothingTypeController::class, 'update']);
            Route::delete('/clothing-types/{id}', [ClothingTypeController::class, 'destroy']);

            // Prices
            Route::post('/prices', [PriceController::class, 'store']);
            Route::put('/prices/{id}', [PriceController::class, 'update']);
            Route::delete('/prices/{id}', [PriceController::class, 'destroy']);
            Route::post('/prices/bulk-update', [PriceController::class, 'bulkUpdate']);

            // Promotions
            Route::post('/promotions', [PromotionController::class, 'store']);
            Route::get('/promotions/{id}', [PromotionController::class, 'show']);
            Route::put('/promotions/{id}', [PromotionController::class, 'update']);
            Route::delete('/promotions/{id}', [PromotionController::class, 'destroy']);
            Route::get('/promotions/{id}/stats', [PromotionController::class, 'stats']);

            // Order Management (Admin/Employee)
            Route::post('/orders/{id}/update-status', [OrderController::class, 'updateStatus']);
            Route::post('/orders/{id}/assign-employee', [OrderController::class, 'assignEmployee']);

            // Payment Management (Admin/Employee)
            Route::post('/orders/{id}/confirm-cash-payment', [PaymentController::class, 'confirmCash']);
            Route::post('/orders/{id}/refund', [PaymentController::class, 'refund']);

            // Delivery Management (Admin/Employee)
            Route::post('/deliveries/{id}/assign-driver', [DeliveryController::class, 'assignDriver']);
            Route::get('/drivers/available', [DeliveryController::class, 'availableDrivers']);

            // Statistics (Admin/Employee)
            Route::get('/statistics/overview', [StatisticsController::class, 'overview']);
            Route::get('/statistics/revenue', [StatisticsController::class, 'revenue']);
            Route::get('/statistics/orders', [StatisticsController::class, 'orders']);
            Route::get('/statistics/customers', [StatisticsController::class, 'customers']);
            Route::get('/statistics/deliveries', [StatisticsController::class, 'deliveries']);
            Route::get('/statistics/export', [StatisticsController::class, 'export']);
        });
    });
