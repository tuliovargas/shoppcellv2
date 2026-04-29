<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BrandModelController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\CashiersInfoController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\OrderCommentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderUploadController;
use App\Http\Controllers\PasswordVerifyController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SystemConfigurationController;
use App\Http\Controllers\TaxInstallmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\PromotionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('user/current', [UserController::class, 'current']);
    Route::get('user/has_role/{role}', [UserController::class, 'userHasRole']);
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Route::middleware('permission:register category')->group(function () {
    Route::get('brands/{brand}/delete', [BrandController::class, 'delete'])->name('brands.delete');
    Route::resource('brands', BrandController::class);
    // });

    // Route::middleware('permission:register category')->group(function () {
    Route::get('categories/{category}/delete', [CategoryController::class, 'delete'])->name('categories.delete');
    Route::resource('categories', CategoryController::class);
    // });

    Route::post('clients/get', [ClientController::class, 'getClientsDatasource']);
    Route::get('clients/{client}/delete', [ClientController::class, 'delete'])->name('clients.delete');
    Route::get('clients/count/{when?}', [ClientController::class, 'getCount']);
    Route::get('clients/validateCpfCnpj', [ClientController::class, 'validateCpfCnpj']);
    Route::resource('clients', ClientController::class);

    Route::get('maintenances/overdue', [MaintenanceController::class, 'getOverdue']);
    Route::resource('maintenances', MaintenanceController::class);

    //Route::middleware('permission:register user')->group(function () {
    Route::get('users/{user}/delete', [UserController::class, 'delete'])->name('users.delete');
    Route::resource('users', UserController::class);
    //});

    //Route::middleware('permission:register product')->group(function () {
	  Route::get('products/{product}/log', [ProductController::class, 'log'])->name('products.log');
	  Route::get('products/{product}/delete', [ProductController::class, 'delete'])->name('products.delete');
    Route::resource('products', ProductController::class);
    //});

    //Route::middleware('permission:register order')->group(function () {
    Route::get('pdv', [OrderController::class, 'create']);
    Route::get('orders/last', [OrderController::class, 'last']);
    Route::resource('orders', OrderController::class);
    Route::resource('orders.comments', OrderCommentController::class);
    Route::resource('orders.uploads', OrderUploadController::class);
    Route::post('orders/{order}/refund', [OrderController::class, 'refund']);
    //});

    // Route::middleware('permission:register supplier')->group(function () {
    Route::get('suppliers/{supplier}/delete', [SupplierController::class, 'delete'])->name('suppliers.delete');
    Route::resource('suppliers', SupplierController::class);
    // });

    Route::prefix('configurations')->name('configurations.')/*->middleware(['permission:update system config'])*/->group(function () {
        Route::get('get', [SystemConfigurationController::class, 'index'])->name('system.index');
        Route::get('system', [SystemConfigurationController::class, 'edit'])->name('system.edit');
        Route::put('system', [SystemConfigurationController::class, 'update'])->name('system.update');
        Route::get('tax-installments', [TaxInstallmentController::class, 'edit'])->name('tax-installments.edit');
        Route::put('tax-installments', [TaxInstallmentController::class, 'update'])->name('tax-installments.update');
        Route::get('checklists/{checklist}/delete', [ChecklistController::class, 'delete'])->name('checklists.delete');
        Route::resource('checklists', ChecklistController::class);
    });

    Route::get('tax-installments', [TaxInstallmentController::class, 'index'])->name('tax-installments.index');

    Route::resource('coupons', CouponController::class);
    Route::get('coupons/{coupon}/delete', [CouponController::class, 'delete'])->name('coupons.delete');

    Route::post('secretPassword', [PasswordVerifyController::class, 'index'])->name('passwordverify.index');

    Route::resource('stocks', StockController::class);
    Route::post('stocks/updateAll', [StockController::class, 'updateAll']);
    Route::get('cashierInfo/history', [CashiersInfoController::class, 'history']);
    Route::resource('cashierInfo', CashiersInfoController::class)->only(['index', 'store', 'update']);
    Route::delete('cashierInfo', [CashiersInfoController::class, 'destroy']);
    Route::resource('expense-types', ExpenseTypeController::class);
    Route::resource('cashier', CashierController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::resource('banks', BankController::class);
    Route::get('promotions/{type?}', [PromotionController::class, 'index']);
    // Route::resource('comments', CommentController::class);

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('cashier', [ReportController::class, 'cashier'])->name('cashier');
        Route::get('commission', [ReportController::class, 'commission'])->name('commission');
        Route::get('maintenance', [ReportController::class, 'maintenance'])->name('maintenance');
        Route::get('sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('expenses', [ReportController::class, 'expenses'])->name('expenses');
        Route::get('inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('requests', [ReportController::class, 'requests'])->name('requests');
        Route::get('cost', [ReportController::class, 'cost'])->name('cost');
        Route::get('period', [ReportController::class, 'period'])->name('period');
    });

    Route::post('commission', [CommissionController::class, 'process']);

    Route::get('brand-models/{brand_model}/delete', [BrandModelController::class, 'delete'])->name('brand-models.delete');
    Route::resource('brand-models', BrandModelController::class);

    Route::get('payment-methods/{payment_method}/delete', [PaymentMethodController::class, 'delete'])->name('payment-methods.delete');
    Route::resource('payment-methods', PaymentMethodController::class);

    Route::get('streets', [AddressController::class, 'streets']);

    Route::prefix('print')->name('print.')->group(function () {
        Route::get('order/{id}', [PrintController::class, 'order'])->name('order');
        Route::get('cancellation/{id}', [PrintController::class, 'cancellation'])->name('cancellation');
    });
});
