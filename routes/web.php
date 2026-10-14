<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\PermissionCategoryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\RoleUserController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\POS\ProductController;
use App\Http\Controllers\POS\CategoryController;
use App\Http\Controllers\POS\SupplierController;
use App\Http\Controllers\POS\CustomerController;
use App\Http\Controllers\POS\PurchaseController;
use App\Http\Controllers\POS\PurchaseItemController;
use App\Http\Controllers\POS\ProductVariantController;
use App\Http\Controllers\POS\StockController;
use App\Http\Controllers\POS\StoreController;
use App\Http\Controllers\POS\DiscountCountroller;
use App\Http\Controllers\POS\SaleController;
use App\Http\Controllers\POS\SaleItemController;
use App\Http\Controllers\POS\MethodController;
use App\Http\Controllers\POS\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\POS\TaxController;
use App\Http\Controllers\Admin\CompanyPaymentController;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        return redirect($user->role && strtolower($user->role->role) === 'kasir'
            ? 'admin-panel/pos/sell'
            : 'admin-panel/dashboard');
    }

    return app(AuthenticatedSessionController::class)->create();
})->name('home');

// Profile management routes (required)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', \App\Http\Middleware\CheckCompanyLicense::class, 'Admin', 'XSS'])->group(function () {
    Route::get('admin-panel/dashboard', [AdminController::class, 'adminPanel'])->name('dashboard');
    Route::get('admin-panel/business-process', [AdminController::class, 'businessProcess']);
    Route::get('admin-panel/sales-data', [AdminController::class, 'getSalesData']);
    Route::get('admin-panel/features', [FeatureController::class, 'index']);
    Route::get('admin-panel/features/create', [FeatureController::class, 'create']);
    Route::get('admin-panel/features/{id}/manage', [FeatureController::class, 'show']);
    Route::post('admin-panel/features/company', [FeatureController::class, 'storeCompany']);
    Route::post('admin-panel/features/{id}/domain/retry', [FeatureController::class, 'retryDomain']);
    Route::post('admin-panel/features/update', [FeatureController::class, 'update']);
    Route::post('admin-panel/features/billing', [FeatureController::class, 'updateBilling']);
    Route::post('admin-panel/features/payment/qris', [CompanyPaymentController::class, 'createQris']);
    Route::post('admin-panel/settings/license/payment/qris', [CompanyPaymentController::class, 'createQris']);
    Route::post('admin-panel/pos/payment/qris', [CompanyPaymentController::class, 'createPosQris']);

    // Companies
    Route::get('admin-panel/companies/listing', [\App\Http\Controllers\Admin\CompanyController::class, 'index']);
    Route::get('admin-panel/companies/add', [\App\Http\Controllers\Admin\CompanyController::class, 'add']);
    Route::post('admin-panel/companies/add', [\App\Http\Controllers\Admin\CompanyController::class, 'store']);
    Route::get('admin-panel/companies/show/{id}', [\App\Http\Controllers\Admin\CompanyController::class, 'show']);
    Route::get('admin-panel/companies/edit/{id}', [\App\Http\Controllers\Admin\CompanyController::class, 'edit']);
    Route::post('admin-panel/companies/update/{id}', [\App\Http\Controllers\Admin\CompanyController::class, 'update']);
    Route::delete('admin-panel/companies/delete/{id}', [\App\Http\Controllers\Admin\CompanyController::class, 'destroy']);




    // Users CRUD
    Route::get('admin-panel/users/listing', [UserController::class, 'index']);
    Route::get('admin-panel/users/add', [UserController::class, 'post']);
    Route::post('admin-panel/users/add', [UserController::class, 'store']);
    Route::get('admin-panel/users/edit/{id}', [UserController::class, 'edit']);
    Route::post('admin-panel/users/update/{id}', [UserController::class, 'update']);
    Route::delete('admin-panel/users/delete/{id}', [UserController::class, 'destroy']);

    // Roles
    Route::get('admin-panel/roles/listing', [RoleController::class, 'index']);
    Route::get('admin-panel/roles/add', [RoleController::class, 'add']);
    Route::post('admin-panel/roles/add', [RoleController::class, 'store']);
    Route::get('admin-panel/roles/edit/{id}', [RoleController::class, 'edit']);
    Route::post('admin-panel/roles/update/{id}', [RoleController::class, 'update']);
    Route::delete('admin-panel/roles/delete/{id}', [RoleController::class, 'destroy']);

    // Permissions
    Route::get('admin-panel/permissions/listing', [PermissionController::class, 'index']);
    Route::get('admin-panel/permissions/add', [PermissionController::class, 'add']);
    Route::post('admin-panel/permissions/add', [PermissionController::class, 'store']);
    Route::get('admin-panel/permissions/edit/{id}', [PermissionController::class, 'edit']);
    Route::post('admin-panel/permissions/update/{id}', [PermissionController::class, 'update']);
    Route::delete('admin-panel/permissions/delete/{id}', [PermissionController::class, 'destroy']);

    // Role Permission
    Route::get('admin-panel/role-permissions/listing', [RolePermissionController::class, 'index']);
    Route::get('admin-panel/role-permissions/add', [RolePermissionController::class, 'add']);
    Route::post('admin-panel/role-permissions/add', [RolePermissionController::class, 'store']);
    Route::get('admin-panel/role-permissions/edit/{id}', [RolePermissionController::class, 'edit']);
    Route::post('admin-panel/role-permissions/update/{id}', [RolePermissionController::class, 'update']);
    Route::delete('admin-panel/role-permissions/delete/{id}', [RolePermissionController::class, 'destroy']);

    // User Role
    Route::get('admin-panel/user-role/listing', [RoleUserController::class, 'index']);
    Route::get('admin-panel/user-role/add', [RoleUserController::class, 'add']);
    Route::post('admin-panel/user-role/add', [RoleUserController::class, 'store']);
    Route::get('admin-panel/user-role/edit/{id}', [RoleUserController::class, 'edit']);
    Route::post('admin-panel/user-role/update/{id}', [RoleUserController::class, 'update']);
    Route::delete('admin-panel/user-role/delete/{id}', [RoleUserController::class, 'destroy']);

    // Permission Categories
    Route::get('admin-panel/permission-categories/listing', [PermissionCategoryController::class, 'index']);
    Route::get('admin-panel/permission-categories/add', [PermissionCategoryController::class, 'add']);
    Route::post('admin-panel/permission-categories/add', [PermissionCategoryController::class, 'store']);
    Route::get('admin-panel/permission-categories/edit/{id}', [PermissionCategoryController::class, 'edit']);
    Route::post('admin-panel/permission-categories/update/{id}', [PermissionCategoryController::class, 'update']);
    Route::delete('admin-panel/permission-categories/delete/{id}', [PermissionCategoryController::class, 'destroy']);

    // Settings
    Route::get('admin-panel/settings/license', [SettingController::class, 'license']);
    Route::get('admin-panel/settings/company', [SettingController::class, 'company']);
    Route::post('admin-panel/settings/company/update', [SettingController::class, 'updateCompany']);
    Route::get('admin-panel/settings/listing', [SettingController::class, 'index']);
    Route::get('admin-panel/settings/add', [SettingController::class, 'add']);
    Route::Post('admin-panel/settings/add', [SettingController::class, 'store']);
    Route::get('admin-panel/settings/edit/{id}', [SettingController::class, 'edit']);
    Route::post('admin-panel/settings/update/{id}', [SettingController::class, 'update']);
    Route::delete('admin-panel/settings/delete/{id}', [SettingController::class, 'destroy']);

    // POS Tax
    Route::get('admin-panel/tax/listing', [TaxController::class, 'index']);
    Route::get('admin-panel/tax/add', [TaxController::class, 'add']);
    Route::post('admin-panel/tax/add', [TaxController::class, 'store']);
    Route::get('admin-panel/tax/edit/{id}', [TaxController::class, 'edit']);
    Route::post('admin-panel/tax/update/{id}', [TaxController::class, 'update']);
    Route::delete('admin-panel/tax/delete/{id}', [TaxController::class, 'destroy']);

    //Products ROute
    Route::get('admin-panel/products/listing', [ProductController::class, 'index']);
    Route::get('admin-panel/products/show/{id}', [ProductController::class, 'show']);
    Route::get('admin-panel/products/add', [ProductController::class, 'add']);
    Route::post('admin-panel/products/add', [ProductController::class, 'store']);
    Route::get('admin-panel/products/edit/{id}', [ProductController::class, 'edit']);
    Route::post('admin-panel/products/update/{id}', [ProductController::class, 'update']);
    Route::delete('admin-panel/products/delete/{id}', [ProductController::class, 'destroy']);
    Route::get('admin-panel/products/export', [ProductController::class, 'export']);
    Route::post('admin-panel/products/import', [ProductController::class, 'import']);
    Route::get('admin-panel/products/recycle', [ProductController::class, 'deletedProducts']);
    Route::get('admin-panel/products/restore/{id}', [ProductController::class, 'restore']);
    Route::delete('admin-panel/products/force-delete/{id}', [ProductController::class, 'forceDelete']);
    // Categories CRUD
    Route::get('admin-panel/categories/listing', [CategoryController::class, 'index']);
    Route::get('admin-panel/categories/add', [CategoryController::class, 'add']);
    Route::post('admin-panel/categories/add', [CategoryController::class, 'store']);
    Route::get('admin-panel/categories/edit/{id}', [CategoryController::class, 'edit']);
    Route::post('admin-panel/categories/update/{id}', [CategoryController::class, 'update']);
    Route::delete('admin-panel/categories/delete/{id}', [CategoryController::class, 'destroy']);

    //Route For Suppliers
    Route::get('admin-panel/supplier/listing', [SupplierController::class, 'index']);
    Route::get('admin-panel/supplier/add', [SupplierController::class, 'add']);
    Route::post('admin-panel/supplier/add', [SupplierController::class, 'store']);
    Route::get('admin-panel/supplier/edit/{id}', [SupplierController::class, 'edit']);
    Route::post('admin-panel/supplier/update/{id}', [SupplierController::class, 'update']);
    Route::delete('admin-panel/supplier/delete/{id}', [SupplierController::class, 'destroy']);

    //Route For Customers
    Route::get('admin-panel/customer/listing', [CustomerController::class, 'index']);
    Route::post('admin-panel/pos/customers/api', [CustomerController::class, 'apiStore']);
    Route::get('admin-panel/customer/add', [CustomerController::class, 'add']);
    Route::post('admin-panel/customer/add', [CustomerController::class, 'store']);
    Route::get('admin-panel/customer/edit/{id}', [CustomerController::class, 'edit']);
    Route::post('admin-panel/customer/update/{id}', [CustomerController::class, 'update']);
    Route::delete('admin-panel/customer/delete/{id}', [CustomerController::class, 'destroy']);

    //Route For Purchase
    Route::get('admin-panel/purchase/listing', [PurchaseController::class, 'index']);
    Route::get('admin-panel/purchase/add', [PurchaseController::class, 'add']);
    Route::post('admin-panel/purchase/add', [PurchaseController::class, 'store']);
    Route::get('admin-panel/purchase/show/{id}', [PurchaseController::class, 'show']);
    Route::get('admin-panel/purchase/edit/{id}', [PurchaseController::class, 'edit']);
    Route::post('admin-panel/purchase/update/{id}', [PurchaseController::class, 'update']);
    Route::delete('admin-panel/purchase/delete/{id}', [PurchaseController::class, 'destroy']);

    //Route for Purchase Items
    Route::get('admin-panel/purchase-items/listing', [PurchaseItemController::class, 'index']);
    Route::get('admin-panel/purchase-item/add', [PurchaseItemController::class, 'add']);
    Route::post('admin-panel/purchase-item/add', [PurchaseItemController::class, 'store']);
    Route::get('admin-panel/purchase-items/edit/{id}', [PurchaseItemController::class, 'edit']);
    Route::post('admin-panel/purchase-items/update/{id}', [PurchaseItemController::class, 'update']);
    Route::delete('admin-panel/purchase-items/delete/{id}', [PurchaseItemController::class, 'destroy']);

    //Route for multiple stores
    Route::get('admin-panel/stores/listing', [StoreController::class, 'index']);
    Route::get('admin-panel/stores/add', [StoreController::class, 'add']);
    Route::post('admin-panel/stores/add', [StoreController::class, 'store']);
    Route::get('admin-panel/stores/show/{id}', [StoreController::class, 'show']);
    Route::get('admin-panel/stores/edit/{id}', [StoreController::class, 'edit']);
    Route::post('admin-panel/stores/update/{id}', [StoreController::class, 'update']);
    Route::delete('admin-panel/stores/delete/{id}', [StoreController::class, 'destroy']);

    //Route FOr Product Variant Controller
    Route::get('admin-panel/variants/listing', [ProductVariantController::class, 'index']);
    Route::get('admin-panel/variants/add', [ProductVariantController::class, 'add']);
    Route::post('admin-panel/variants/add', [ProductVariantController::class, 'store']);
    Route::get('admin-panel/variants/edit/{id}', [ProductVariantController::class, 'edit']);
    Route::post('admin-panel/variants/update/{id}', [ProductVariantController::class, 'update']);
    Route::delete('admin-panel/variants/delete/{id}', [ProductVariantController::class, 'destroy']);

    //ROute For Stock Movement
    Route::get('admin-panel/stock/listing', [StockController::class, 'index']);
    Route::get('admin-panel/stock/add', [StockController::class, 'add']);
    Route::post('admin-panel/stock/add', [StockController::class, 'store']);
    Route::get('admin-panel/stock/edit/{id}', [StockController::class, 'edit']);
    Route::post('admin-panel/stock/update/{id}', [StockController::class, 'update']);
    Route::delete('admin-panel/stock/delete/{id}', [StockController::class, 'destroy']);

    //Route For Discounts

    Route::get('admin-panel/discounts/listing', [DiscountCountroller::class, 'index']);
    Route::get('admin-panel/discounts/add', [DiscountCountroller::class, 'add']);
    Route::post('admin-panel/discounts/add', [DiscountCountroller::class, 'store']);
    Route::get('admin-panel/discounts/edit/{id}', [DiscountCountroller::class, 'edit']);
    Route::post('admin-panel/discounts/update/{id}', [DiscountCountroller::class, 'update']);
    Route::delete('admin-panel/discounts/delete/{id}', [DiscountCountroller::class, 'destroy']);

    //Route For Sales
    Route::get('admin-panel/sales/listing', [SaleController::class, 'index']);
    Route::get('admin-panel/sales/reconciliation', [SaleController::class, 'reconciliation']);
    Route::get('admin-panel/pos/shifts/report', [SaleController::class, 'shiftReport']);
    Route::get('admin-panel/sales/add', [SaleController::class, 'add']);
    Route::post('admin-panel/sales/add', [SaleController::class, 'store']);
    Route::get('admin-panel/sales/edit/{id}', [SaleController::class, 'edit']);
    Route::post('admin-panel/sales/update/{id}', [SaleController::class, 'update']);
    Route::delete('admin-panel/sales/delete/{id}', [SaleController::class, 'destroy']);

    // POS Selling Screen and Cart
    Route::get('admin-panel/pos/sell', [SaleController::class, 'pos']);
    Route::post('admin-panel/pos/cart/add', [SaleController::class, 'addToCart']);
    Route::post('admin-panel/pos/cart/custom', [SaleController::class, 'addCustomAmount']);
    Route::post('admin-panel/pos/cart/update', [SaleController::class, 'updateCart']);
    Route::post('admin-panel/pos/cart/remove', [SaleController::class, 'removeFromCart']);
    Route::post('admin-panel/pos/hold-bills', [SaleController::class, 'holdBill']);
    Route::post('admin-panel/pos/hold-bills/{id}/restore', [SaleController::class, 'restoreHeldBill']);
    Route::delete('admin-panel/pos/hold-bills/{id}', [SaleController::class, 'deleteHeldBill']);
    Route::post('admin-panel/pos/shifts/open', [SaleController::class, 'openShift']);
    Route::post('admin-panel/pos/shifts/close', [SaleController::class, 'closeShift']);
    Route::post('admin-panel/pos/checkout', [SaleController::class, 'checkout']);
    Route::get('admin-panel/pos/invoice/{id}', [SaleController::class, 'invoice']);
    Route::get('admin-panel/pos/pdf/{id}', [SaleController::class, 'pdf']);

    //Route For Sales
    Route::get('admin-panel/sale-items/listing', [SaleItemController::class, 'index']);
    Route::get('admin-panel/sale-items/add', [SaleItemController::class, 'add']);
    Route::post('admin-panel/sale-items/add', [SaleItemController::class, 'store']);
    Route::get('admin-panel/sale-items/edit/{id}', [SaleItemController::class, 'edit']);
    Route::post('admin-panel/sale-items/update/{id}', [SaleItemController::class, 'update']);
    Route::delete('admin-panel/sale-items/delete/{id}', [SaleItemController::class, 'destroy']);



    //Route For Payment Methods
    Route::get('admin-panel/payment-methods/listing', [MethodController::class, 'index']);
    Route::get('admin-panel/payment-methods/add', [MethodController::class, 'add']);
    Route::post('admin-panel/payment-methods/add', [MethodController::class, 'store']);
    Route::get('admin-panel/payment-methods/edit/{id}', [MethodController::class, 'edit']);
    Route::post('admin-panel/payment-methods/update/{id}', [MethodController::class, 'update']);
    Route::delete('admin-panel/payment-methods/delete/{id}', [MethodController::class, 'destroy']);


    //Route For Payment
    Route::get('admin-panel/payments/listing', [PaymentController::class, 'index']);
    Route::get('admin-panel/payments/add', [PaymentController::class, 'add']);
    Route::post('admin-panel/payments/add', [PaymentController::class, 'store']);
    Route::get('admin-panel/payments/edit/{id}', [PaymentController::class, 'edit']);
    Route::post('admin-panel/payments/update/{id}', [PaymentController::class, 'update']);
    Route::delete('admin-panel/payments/delete/{id}', [PaymentController::class, 'destroy']);



});

// Customer Facing Display (CFD) Routes - Public Access
Route::get('cfd/{cashier_id}', [\App\Http\Controllers\POS\CfdController::class, 'index']);
Route::get('cfd/api/data/{cashier_id}', [\App\Http\Controllers\POS\CfdController::class, 'getData']);
