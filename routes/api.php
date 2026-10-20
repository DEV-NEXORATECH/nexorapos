<?php

use App\Http\Controllers\Api\Mobile\AuthController;
use App\Http\Controllers\Api\Mobile\AccessController;
use App\Http\Controllers\Api\Mobile\AdminCrudController;
use App\Http\Controllers\Api\Mobile\CatalogController;
use App\Http\Controllers\Api\Mobile\CompanyAdminController;
use App\Http\Controllers\Api\Mobile\PosController;
use App\Http\Controllers\Api\Mobile\ReportController;
use App\Http\Controllers\Api\Mobile\SalesController;
use App\Http\Controllers\Api\Mobile\BillingController;
use App\Http\Controllers\Api\Mobile\UploadController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CompanyPaymentController;

Route::post('midtrans/notification', [CompanyPaymentController::class, 'notification']);

Route::prefix('mobile/v1')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('auth/refresh', [AuthController::class, 'refresh'])->middleware('throttle:20,1');

    Route::middleware('mobile.api')->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/devices', [AuthController::class, 'devices']);
        Route::delete('auth/devices/{id}', [AuthController::class, 'revokeDevice']);

        Route::get('companies', [CatalogController::class, 'companies']);
        Route::get('dashboard', [CatalogController::class, 'dashboard']);
        Route::get('license', [CatalogController::class, 'license']);
        Route::post('billing/license/qris', [BillingController::class, 'createQris']);
        Route::get('billing/license/qris/{orderId}', [BillingController::class, 'status']);
        Route::get('features', [CatalogController::class, 'features']);
        Route::get('stores', [CatalogController::class, 'stores']);
        Route::get('categories', [CatalogController::class, 'categories']);
        Route::get('products', [CatalogController::class, 'products']);
        Route::get('customers', [CatalogController::class, 'customers']);
        Route::post('customers', [CatalogController::class, 'storeCustomer']);
        Route::get('payment-methods', [CatalogController::class, 'paymentMethods']);

        Route::get('pos/sales', [SalesController::class, 'index']);
        Route::get('pos/sales/{id}', [SalesController::class, 'show']);
        Route::get('pos/reconciliation', [SalesController::class, 'reconciliation']);
        Route::post('pos/payments/qris', [SalesController::class, 'createQris']);
        Route::get('pos/payments/qris/{orderId}', [SalesController::class, 'qrisStatus']);

        Route::post('admin/companies', [CompanyAdminController::class, 'store']);
        Route::put('admin/companies/{id}', [CompanyAdminController::class, 'update']);
        Route::patch('admin/company-profile', [CompanyAdminController::class, 'updateProfile']);
        Route::patch('admin/companies/{id}/license', [CompanyAdminController::class, 'updateLicense']);
        Route::patch('admin/companies/{id}/features', [CompanyAdminController::class, 'updateFeatures']);

        Route::get('admin/users', [AccessController::class, 'users']);
        Route::post('admin/users', [AccessController::class, 'storeUser']);
        Route::get('admin/users/{id}', [AccessController::class, 'showUser']);
        Route::put('admin/users/{id}', [AccessController::class, 'updateUser']);
        Route::delete('admin/users/{id}', [AccessController::class, 'destroyUser']);
        Route::get('admin/roles', [AccessController::class, 'roles']);
        Route::post('admin/roles', [AccessController::class, 'storeRole']);
        Route::put('admin/roles/{id}', [AccessController::class, 'updateRole']);
        Route::delete('admin/roles/{id}', [AccessController::class, 'destroyRole']);
        Route::get('admin/permissions', [AccessController::class, 'permissions']);
        Route::get('admin/mobile-menu', [AccessController::class, 'mobileMenu']);

        Route::post('admin/uploads/company-logo', [UploadController::class, 'companyLogo']);
        Route::post('admin/uploads/users/{id}/profile-image', [UploadController::class, 'userProfile']);
        Route::post('admin/uploads/products/{id}/image', [UploadController::class, 'productImage']);

        Route::get('admin/audit-logs', [ReportController::class, 'auditLogs']);
        Route::get('admin/reports/sales/{format}', [ReportController::class, 'salesExport']);
        Route::get('admin/reports/shifts/{format}', [ReportController::class, 'shiftExport']);
        Route::get('admin/reports/reconciliation/{format}', [ReportController::class, 'reconciliationExport']);

        Route::get('admin/{resource}', [AdminCrudController::class, 'index']);
        Route::post('admin/{resource}', [AdminCrudController::class, 'store']);
        Route::get('admin/{resource}/{id}', [AdminCrudController::class, 'show']);
        Route::put('admin/{resource}/{id}', [AdminCrudController::class, 'update']);
        Route::delete('admin/{resource}/{id}', [AdminCrudController::class, 'destroy']);

        Route::post('pos/sales/{id}/cancel-payment', [\App\Http\Controllers\Api\Mobile\CancelMobilePayment::class, '__invoke'])->middleware('throttle:15,1');
        Route::post('pos/checkout', [PosController::class, 'checkout'])->middleware('throttle:60,1');
        Route::post('pos/sales/{id}/void', [PosController::class, 'voidSale'])->middleware('throttle:30,1');
        Route::get('pos/held-bills', [PosController::class, 'holdBills']);
        Route::post('pos/held-bills', [PosController::class, 'storeHoldBill']);
        Route::post('pos/held-bills/merge', [PosController::class, 'mergeHoldBills']);
        Route::post('pos/held-bills/{id}/restore', [PosController::class, 'restoreHoldBill']);
        Route::delete('pos/held-bills/{id}', [PosController::class, 'deleteHoldBill']);
        Route::post('pos/split-preview', [PosController::class, 'splitPreview']);

        Route::post('pos/shifts/open', [PosController::class, 'openShift']);
        Route::post('pos/shifts/close', [PosController::class, 'closeShift']);
        Route::get('pos/shifts/report', [PosController::class, 'shiftReport']);
    });
});
