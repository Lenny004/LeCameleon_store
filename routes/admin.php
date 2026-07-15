<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryWarningController;
use App\Http\Controllers\Admin\DispatchScheduleController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\LogisticsCompanyController;
use App\Http\Controllers\Admin\LogisticsVehicleController;
use App\Http\Controllers\Admin\LogisticsWorkerController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReturnRequestController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShippingZoneController;
use App\Http\Controllers\Admin\SvMunicipalityController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin|staff'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('products', ProductController::class);
        Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('inventory', [InventoryController::class, 'store'])->name('inventory.store');

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
        Route::post('orders/{order}/capture-payment', [OrderController::class, 'capturePayment'])->name('orders.capture-payment');
        Route::patch('orders/{order}/shipment', [OrderController::class, 'updateShipment'])->name('orders.shipment');
        Route::post('orders/{order}/shipment-events', [OrderController::class, 'storeShipmentEvent'])->name('orders.shipment-events');

        Route::get('offers', [OfferController::class, 'index'])->name('offers.index');
        Route::patch('offers/{offer}/accept', [OfferController::class, 'accept'])->name('offers.accept');
        Route::patch('offers/{offer}/decline', [OfferController::class, 'decline'])->name('offers.decline');
        Route::patch('offers/{offer}/counter', [OfferController::class, 'counter'])->name('offers.counter');

        Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::patch('messages/{message}/read', [ContactMessageController::class, 'markRead'])->name('messages.read');

        Route::get('return-requests', [ReturnRequestController::class, 'index'])->name('return-requests.index');
        Route::patch('return-requests/{returnRequest}/approve', [ReturnRequestController::class, 'approve'])->name('return-requests.approve');
        Route::patch('return-requests/{returnRequest}/deny', [ReturnRequestController::class, 'deny'])->name('return-requests.deny');
        Route::patch('return-requests/{returnRequest}/refund', [ReturnRequestController::class, 'refund'])->name('return-requests.refund');

        Route::resource('users', UserController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('brands', BrandController::class);
        Route::resource('coupons', CouponController::class);

        Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::patch('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
        Route::patch('reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

        Route::get('activity', [ActivityController::class, 'index'])->name('activity.index');

        Route::prefix('logistics')->name('logistics.')->group(function () {
            Route::resource('companies', LogisticsCompanyController::class);
            Route::resource('workers', LogisticsWorkerController::class);
            Route::resource('vehicles', LogisticsVehicleController::class);

            Route::get('municipalities', [SvMunicipalityController::class, 'index'])->name('municipalities.index');
            Route::get('municipalities/{svMunicipality}/edit', [SvMunicipalityController::class, 'edit'])->name('municipalities.edit');
            Route::put('municipalities/{svMunicipality}', [SvMunicipalityController::class, 'update'])->name('municipalities.update');

            Route::get('zones', [ShippingZoneController::class, 'index'])->name('zones.index');
            Route::post('zones', [ShippingZoneController::class, 'store'])->name('zones.store');
            Route::put('zones/{zone}', [ShippingZoneController::class, 'update'])->name('zones.update');
            Route::delete('zones/{zone}', [ShippingZoneController::class, 'destroy'])->name('zones.destroy');
            Route::get('zones/rates-matrix', [ShippingZoneController::class, 'ratesMatrix'])->name('zones.rates-matrix');
            Route::put('zones/rates-matrix', [ShippingZoneController::class, 'updateRatesMatrix'])->name('zones.rates-matrix.update');

            Route::get('dispatch', [DispatchScheduleController::class, 'index'])->name('dispatch.index');
            Route::post('dispatch', [DispatchScheduleController::class, 'store'])->name('dispatch.store');
            Route::put('dispatch/{schedule}', [DispatchScheduleController::class, 'update'])->name('dispatch.update');
            Route::delete('dispatch/{schedule}', [DispatchScheduleController::class, 'destroy'])->name('dispatch.destroy');

            Route::get('warnings', [DeliveryWarningController::class, 'index'])->name('warnings.index');
            Route::post('warnings', [DeliveryWarningController::class, 'store'])->name('warnings.store');
            Route::put('warnings/{warning}', [DeliveryWarningController::class, 'update'])->name('warnings.update');
            Route::delete('warnings/{warning}', [DeliveryWarningController::class, 'destroy'])->name('warnings.destroy');
        });
    });
