<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\OutletController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Warehouse\WarehouseAuthController;
use App\Http\Controllers\Warehouse\WarehouseDashboardController;
use App\Http\Controllers\Warehouse\ForgotPasswordController;
use App\Http\Controllers\Warehouse\StockController as WarehouseStockController;
use App\Events\TestBroadcastEvent;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\Admin\CmsPageController;
use App\Http\Controllers\admin\EmailTemplateController;
use App\Http\Controllers\admin\FaqController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StockRequestController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Outlet\DebitCollectionController;
use App\Http\Controllers\Outlet\DownPaymentController;
use App\Http\Controllers\Outlet\NotificationController;
use App\Http\Controllers\Outlet\OutletController as OutletOutletController;
use App\Http\Controllers\Outlet\OutletDashboardController;
use App\Http\Controllers\Outlet\TransferRequestController;
use App\Http\Controllers\Supervisor\SupervisorController;
use App\Http\Controllers\Warehouse\CashHandlingController;
use App\Http\Controllers\Warehouse\DownPaymentController as WarehouseDownPaymentController;

Route::get('/admin', function () {
    return 'Admin Login Page';
});


Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'App\Http\Controllers\Admin'], function () {

    Route::controller(AuthController::class)->group(function () {

        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/do-login', 'login')->name('do-login');
        Route::get('/forgot-password', 'create')->name('password.request');
        Route::post('/forgot-password', 'sendOtp')->name('send-otp');
        Route::get('/reset-password', 'resetPassword')->name('reset-password');
        Route::put('/verify-otp', 'verifyOtp')->name('verify-otp');
        Route::put('/forgot-password', 'resetPasswordUpdate')->name('reset-password-update');

        Route::any('/admin-resend-otp', 'admin_resend_otp')->name('admin-resend-otp');
    });


    Route::group(['middleware' => 'admin'], function () {

        Route::get('/profile', 'AuthController@index')->name('profile');
        Route::put('/profile/update', 'AuthController@update')->name('profile.update');
        Route::get('/profile/change-password', 'AuthController@changePassword')->name('profile.changePassword');
        Route::put('/profile/change-password', 'AuthController@updatePassword')->name('profile.updatePassword');
        Route::any('/logout', 'AuthController@logOut')->name('logout');
        Route::get('/dashboard', 'DashboardController@dashboard')->name('dashboard');

        Route::post('/upload/image', 'DashboardController@uploadImage')->name('upload.image');

        Route::get('/email-templates/status/{id}', 'EmailTemplateController@changeStatus')->name('email-templates.status');
        Route::get('/banners/status/{id}', 'BannerController@changeStatus')->name('banners.status');
        Route::get('/testimonials/status/{id}', 'TestimonialController@changeStatus')->name('testimonials.status');
        Route::get('/categories/status/{id}', 'CategoryController@changeStatus')->name('categories.status');
        Route::get('/faq/status/{id}', 'FaqController@changeStatus')->name('faq.status');
        Route::get('/cms-page/status/{id}', 'CmsPageController@changeStatus')->name('cms-page.status');


        Route::get('stock-management/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('stock-management/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('stock-management/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('stock-management/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('stock-management/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::get('stock-management/products/{id}', [ProductController::class, 'show'])->name('products.show');
        Route::delete('stock-management/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');


        Route::any('stock-returened', [StockRequestController::class, 'stock_returened'])->name('stock-returened');
        Route::any('returned-requests-list', [StockRequestController::class, 'returned_requests_list'])->name('returned-requests-list');
        Route::any('/returned-requests-view/{id}', [StockRequestController::class, 'returned_requests_view'])->name('returned-requests-view');
        Route::patch('/returned-requests-accept/{id}', [StockRequestController::class, 'returned_requests_accept'])->name('returned-requests-accept');
        Route::patch('/returned-requests-reject/{id}', [StockRequestController::class, 'returned_requests_reject'])->name('returned-requests-reject');

        Route::any('accross-outlets-returened', [StockRequestController::class, 'accross_outlets_returened'])->name('accross-outlets-returened');

        Route::any('all-across-warehouse', [StockRequestController::class, 'allAcrossWarehouse'])->name('all-across-warehouse');

        Route::post('products/import', [ProductController::class, 'import'])->name('products.import');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
        Route::get('/warehouses/create', [WarehouseController::class, 'create'])->name('warehouses.create');
        Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
        Route::get('/warehouses/{id}/edit', [WarehouseController::class, 'edit'])->name('warehouses.edit');
        Route::put('/warehouses/{id}', [WarehouseController::class, 'update'])->name('warehouses.update');
        Route::delete('/warehouses/{id}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');
        Route::get('/warehouses/view/{id}', [WarehouseController::class, 'show'])->name('warehouses.show');


        Route::get('/outlets', [OutletController::class, 'index'])->name('outlets.index');
        Route::get('/outlets/create', [OutletController::class, 'create'])->name('outlets.create');
        Route::post('/outlets', [OutletController::class, 'store'])->name('outlets.store');
        Route::get('/outlets/{id}/edit', [OutletController::class, 'edit'])->name('outlets.edit');
        Route::put('/outlets/{id}', [OutletController::class, 'update'])->name('outlets.update');
        Route::get('/outlets/{id}', [OutletController::class, 'show'])->name('outlets.show');
        Route::delete('/outlets/{id}', [OutletController::class, 'destroy'])->name('outlets.destroy');

        Route::get('stock-management/stock', [StockController::class, 'index'])->name('stock.index');
        Route::get('stock-management/stock/create', [StockController::class, 'create'])->name('stock.create');
        Route::post('stock-management/stock', [StockController::class, 'store'])->name('stock.store');
        Route::get('stock-management/view/stock/{id}', [StockController::class, 'show'])->name('stock.show');
        Route::get('stock-management/stock/{id}/edit', [StockController::class, 'edit'])->name('stock.edit');
        Route::put('stock-management/stock/{id}', [StockController::class, 'update'])->name('stock.update');
        Route::post('stock-management/stock/update-status', [StockController::class, 'updateStatus'])->name('stock.updateStatus');
        Route::delete('stock-management/stock/{id}', [StockController::class, 'destroy'])->name('stock.destroy');


        Route::get('/external-cash-inflow', [WarehouseController::class, 'indexExternalCashInflow'])->name('external-cash-inflow.index');
        Route::get('/external-cash-inflow/create', [WarehouseController::class, 'createExternalCashInflow'])->name('external-cash-inflow.create');
        Route::post('/external-cash-inflow', [WarehouseController::class, 'storeExternalCashInflow'])->name('external-cash-inflow.store');
        Route::get('/external-cash-inflow/{id}/edit', [WarehouseController::class, 'editExternalCashInflow'])->name('external-cash-inflow.edit');
        Route::put('/external-cash-inflow/{id}', [WarehouseController::class, 'updateExternalCashInflow'])->name('external-cash-inflow.update');
        Route::delete('/external-cash-inflow/{id}', [WarehouseController::class, 'destroyExternalCashInflow'])->name('external-cash-inflow.destroy');
        Route::get('/external-cash-inflow/view/{id}', [WarehouseController::class, 'showExternalCashInflow'])->name('external-cash-inflow.show');


        Route::get('/external-cash-outflow', [WarehouseController::class, 'indexExternalCashOutflow'])->name('external-cash-outflow.index');
        Route::get('/external-cash-outflow/view/{id}', [WarehouseController::class, 'showExternalCashOutflow'])->name('external-cash-outflow.show');

        Route::get('/sales-report', [ReportController::class, 'indexSalesReport'])->name('sales-report.index');

        Route::get('/sales-report-view/{id}', [ReportController::class, 'showSalesReport'])->name('sales-report.show');

        Route::get('/stock-movement-report', [ReportController::class, 'indexStockMovementsReport'])->name('stock-movement.index');

        Route::get('/stock-movement-report-view/{id}', [ReportController::class, 'showStockMovementsReport'])->name('stock-movement.show');

        Route::get('/cash-remittance-report', [ReportController::class, 'indexCashRemittanceReport'])->name('cash-remittance.index');

        Route::get('/down-payment-report', [ReportController::class, 'indexDownPaymentReport'])->name('down-payment.index');

        Route::get('/debt-collection-report', [ReportController::class, 'indexDebtCollectionReport'])->name('debt-collection.index');

        Route::get('/debt-collection-report-view/{id}', [ReportController::class, 'showDebtCollectionReport'])->name('debt-collection.show');

        Route::get('/cash-handling-report', [ReportController::class, 'indexCashHandlingReport'])->name('cash-handling.index');

        Route::get('/customers', [ReportController::class, 'indexCustomer'])->name('customers.index');

        Route::get('/view/{id}', [ReportController::class, 'showCustomer'])->name('customers.show');


        Route::get('/record-expense', [WarehouseController::class, 'indexRecordExpense'])->name('record-expense.index');

        Route::patch('record-expense/{id}/accept', [WarehouseController::class, 'acceptRecordExpense'])->name('record-expense.accept');
        Route::patch('record-expense/{id}/reject', [WarehouseController::class, 'rejectRecordExpense'])->name('record-expense.reject');

        Route::get('/cash-refund', [WarehouseController::class, 'indexCashRefund'])->name('cash-refund.index');

        Route::resources([
            'email-templates' => EmailTemplateController::class,
            'banners' => BannerController::class,
            'testimonials' => TestimonialController::class,
            'categories' => CategoryController::class,
            'faq' => FaqController::class,
            'cms-page' => CmsPageController::class,

        ]);
        Route::get('config', 'GlobalSettingController@config');
    });
});

// Warehouse Routes
Route::get('/', [WarehouseAuthController::class, 'index'])->name('index');
Route::group(['prefix' => 'warehouse', 'as' => 'warehouse.', 'namespace' => 'App\Http\Controllers\Warehouse'], function () {

    Route::get('/', function () {
        return redirect()->route('index');
    });

    Route::middleware('warehouse')->group(function () {


        Route::get('/dashboard', [WarehouseDashboardController::class, 'index'])->name('dashboard');

        Route::get('/my-account', [WarehouseAuthController::class, 'myAccount'])->name('myAccount');
        Route::post('/update-account', [WarehouseAuthController::class, 'updateAccount'])->name('updateAccount');
        Route::get('/account/update-password', [WarehouseAuthController::class, 'showChangePasswordForm'])->name('showChangePasswordForm');
        Route::post('/account/update-password', [WarehouseAuthController::class, 'updatePassword'])->name('updatePassword');

        Route::get('/purchase-order-create', [WarehouseAuthController::class, 'create_order'])->name('purchase-order-create');
        Route::get('/purchase-order', [WarehouseAuthController::class, 'purchase_order'])->name('purchase-order');

        Route::get('/stock-detail', [WarehouseAuthController::class, 'stockDetail'])->name('stockDetail');
        // Route::get('/1', [WarehouseAuthController::class, 'recieveStock'])->name('recieveStock');

        Route::get('/transfer-to-outlets-detail/{id}', [WarehouseAuthController::class, 'transferOutletsStocksDetails'])
            ->name('transferoutletsorderdetails');

        Route::get('/transfer-to-outlets-create', [WarehouseAuthController::class, 'transferOutletsCreate'])
            ->name('transferoutletscreate');

        Route::post('/transfer-to-outlets-store', [WarehouseAuthController::class, 'transferOutletsStore'])
            ->name('transferoutletsstore');

        Route::get('/transfer-to-outlets', [WarehouseAuthController::class, 'transferOutlets'])->name('transferoutlets');

        Route::post('/transfer-to-outlets-update-status', [WarehouseAuthController::class, 'transferOutletsUpdateStatus'])->name('updateStatus');

        Route::get('/create-order', [WarehouseAuthController::class, 'createorder'])->name('createorder');
        Route::get('/recieve-stock', [WarehouseStockController::class, 'index'])->name('recieve-stock');
        Route::get('recieve-show/{id}', [WarehouseStockController::class, 'show'])->name('recieve-show');
        Route::get('status-update/{id}', [WarehouseStockController::class, 'status_update'])->name('recieve-status-update');
        Route::post('/update-stock/{id}', [WarehouseStockController::class, 'updateStatus'])->name('update-stock');

        Route::any('/outlet-levels', [WarehouseStockController::class, 'outlet_levels'])->name('outlet-levels');


        Route::any('/warehouse-levels', [WarehouseStockController::class, 'warehouse_levels'])->name('warehouse-levels');

        Route::post('/customer-store', [WarehouseAuthController::class, 'customerStore'])
            ->name('customerStore');

        Route::get('/customer', [WarehouseAuthController::class, 'customerList'])->name('customerList');

        // Route::put('/customer/{customer}', [WarehouseAuthController::class, 'customerUpdate'])
        //     ->name('customerUpdate');

        // Route::delete('/customer/{customer}', [WarehouseAuthController::class, 'customerDestroy'])
        //     ->name('customerDestroy');

        // Route for Update (PUT)
        Route::put('/customer/{customer}', [WarehouseAuthController::class, 'customerUpdate'])
            ->name('customerUpdate'); // Added 'warehouse.' prefix for consistency

        // Route for Delete (DELETE)
        Route::delete('/customer/{customer}', [WarehouseAuthController::class, 'customerDestroy'])
            ->name('customerDestroy'); // Added 'warehouse.' prefix for consistency

        Route::get('/sales-orders', [WarehouseAuthController::class, 'salesOrders'])->name('salesOrders');

        Route::get('/create-sales', [WarehouseAuthController::class, 'createSales'])
            ->name('createSales');

        Route::post('/store-sales', [WarehouseAuthController::class, 'storeSales'])->name('storeSales');

        Route::get('/sales-details/{id}', [WarehouseAuthController::class, 'salesDetails'])
            ->name('salesDetails');

        Route::get('/sales-invoice/{id}', [WarehouseAuthController::class, 'salesInvoice'])
            ->name('salesInvoice');

        Route::post('/sales/{sale}/generate-waybill', [WarehouseAuthController::class, 'generateWaybill'])->name('generateWaybill');

        Route::get('/waybill/{id}', [WarehouseAuthController::class, 'viewWaybillInvoice'])->name('viewWaybillInvoice');

        Route::any('/return-list', [WarehouseStockController::class, 'return_list'])->name('return-list');
        Route::any('/return-view/{id}', [WarehouseStockController::class, 'return_view'])->name('return-view');
        Route::any('/return-list-status-accept/{id}', [WarehouseStockController::class, 'return_list_status_accept'])->name('return-list-status-accept');
        Route::any('/return-list-status-reject/{id}', [WarehouseStockController::class, 'return_list_status_reject'])->name('return-list-status-reject');


        Route::get('/debtCollections', [CashHandlingController::class, 'debtCollections'])->name('debtCollections');
        Route::get('/debtCollections-create', [CashHandlingController::class, 'debtCollections_create'])->name('debtCollections-create');
        Route::post('/debtCollections-store', [CashHandlingController::class, 'debtCollections_store'])->name('debtCollections-store');

        Route::get('/recordExpenses', [CashHandlingController::class, 'recordExpenses'])->name('recordExpenses');
        Route::get('/recordExpenses-create', [CashHandlingController::class, 'recordExpenses_create'])->name('recordExpenses-create');
        Route::post('/recordExpenses-store', [CashHandlingController::class, 'recordExpenses_store'])->name('recordExpenses-store');

        Route::get('/cashRemittance', [CashHandlingController::class, 'cashRemittance'])->name('cashRemittance');
        Route::get('/cashRemittance-create', [CashHandlingController::class, 'cashRemittance_create'])->name('cashRemittance-create');
        Route::post('/cashRemittance-store', [CashHandlingController::class, 'cashRemittance_store'])->name('cashRemittance-store');
        Route::get('/cashRemittance-view/{id}', [CashHandlingController::class, 'cashRemittance_view'])->name('cashRemittance-view');

        Route::get('/daily-sales-summary', [CashHandlingController::class, 'daily_sales_summary'])->name('daily-sales-summary');

        Route::get('/outlet-stock-request', [WarehouseAuthController::class, 'outletStockRequest'])->name('outletStockRequest');

        Route::get('/outlet-stock-request-detail/{id}', [WarehouseAuthController::class, 'outletStockRequestDetails'])
            ->name('outletStockRequestDetails');

        Route::any('/outlet-stock-request-accept/{id}', [WarehouseAuthController::class, 'outlet_stock_request_accept'])->name('outlet-stock-request-accept');
        Route::any('/outlet-stock-request-reject/{id}', [WarehouseAuthController::class, 'outlet_stock_request_reject'])->name('outlet-stock-request-reject');

        Route::post('/notifications/{id}/mark-as-read', [WarehouseDashboardController::class, 'markAsRead'])->name('notifications.mark-as-read');
        Route::post('/notifications/mark-all-read', [WarehouseDashboardController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::get('/notifications/unread-count', [WarehouseDashboardController::class, 'getUnreadCount'])->name('notifications.unread-count');
        Route::get('/notifications', [WarehouseDashboardController::class, 'notificationList'])->name('notifications.all');


        Route::get('/downPayment', [WarehouseDownPaymentController::class, 'downPayment'])->name('downPayment');
        Route::get('/downPayment-create', [WarehouseDownPaymentController::class, 'downPayment_create'])->name('downPayment-create');
        Route::post('/downPayment-store', [WarehouseDownPaymentController::class, 'downPayment_store'])->name('downPayment-store');

        Route::get('/product-management', [WarehouseAuthController::class, 'productManagementList'])->name('productManagementList');
        Route::put('/product/{product}/update-price', [WarehouseAuthController::class, 'productUpdatePrice'])
            ->name('productUpdatePrice');

        Route::get('/sales/{sale}/get-details', [WarehouseAuthController::class, 'getSaleDetails'])
            ->name('getSaleDetails');

        Route::post('/sales/{sale}/refund', [WarehouseAuthController::class, 'processRefund'])
            ->name('processRefund');
    });
});

//Outlet Routes
Route::group(['prefix' => 'outlet', 'as' => 'outlet.', 'namespace' => 'App\Http\Controllers\Warehouse'], function () {

    Route::middleware('outlet')->group(function () {
        Route::get('/outlet-dashboard', [OutletDashboardController::class, 'indexOutlet'])->name('outlet-dashboard');

        Route::get('/my-account', [WarehouseAuthController::class, 'myAccount'])->name('myAccount');
        Route::post('/update-account', [WarehouseAuthController::class, 'updateAccount'])->name('updateAccount');
        Route::get('/account/update-password', [WarehouseAuthController::class, 'showChangePasswordForm'])->name('showChangePasswordForm');
        Route::post('/account/update-password', [WarehouseAuthController::class, 'updatePassword'])->name('updatePassword');

        Route::post('/logout', [WarehouseAuthController::class, 'logout'])->name('logout');

        Route::get('/receive-stock', [OutletOutletController::class, 'index'])->name('receive-stock');
        Route::get('view/stock/{id}', [OutletOutletController::class, 'show'])->name('receive-show');
        Route::get('status-update/{id}', [OutletOutletController::class, 'status_update'])->name('receive-status-update');
        Route::post('/update-stock/{id}', [OutletOutletController::class, 'updateStatus'])->name('update-stock');

        Route::get('/transfer-to-outlets-detail/{id}', [OutletOutletController::class, 'transferOutletsStocksDetails'])->name('transferoutletsorderdetails');
        Route::get('/transfer-to-outlets-create', [OutletOutletController::class, 'transferOutletsCreate'])->name('transferoutletscreate');
        Route::post('/transfer-to-outlets-store', [OutletOutletController::class, 'transferOutletsStore'])->name('transferoutletsstore');
        Route::get('/transfer-to-outlets', [OutletOutletController::class, 'transferOutlets'])->name('transferoutlets');

        Route::get('/outlet-transfer-request', [TransferRequestController::class, 'index'])->name('outlet-transfer-request');
        Route::get('/outlet-create-request', [TransferRequestController::class, 'create'])->name('outlet-create-request');
        Route::post('/outlet-store-request', [TransferRequestController::class, 'store'])->name('outlet-store-request');
        Route::get('/outlet-view-request/{id}', [TransferRequestController::class, 'view'])->name('outlet-view-request');

        Route::get('/returned-requests', [TransferRequestController::class, 'returned_requests'])->name('returned-requests');
        Route::get('/returned-requests-create', [TransferRequestController::class, 'returned_requests_create'])->name('returned-requests-create');
        Route::post('/returned-requests-store', [TransferRequestController::class, 'returned_requests_store'])->name('returned-requests-store');
        Route::get('/returned-requests-view/{id}', [TransferRequestController::class, 'returned_requests_view'])->name('returned-requests-view');



        Route::get('/debtCollections', [DebitCollectionController::class, 'debtCollections'])->name('debtCollections');
        Route::get('/debtCollections-create', [DebitCollectionController::class, 'debtCollections_create'])->name('debtCollections-create');
        Route::post('/debtCollections-store', [DebitCollectionController::class, 'debtCollections_store'])->name('debtCollections-store');

        Route::get('/recordExpenses', [DebitCollectionController::class, 'recordExpenses'])->name('recordExpenses');
        Route::get('/recordExpenses-create', [DebitCollectionController::class, 'recordExpenses_create'])->name('recordExpenses-create');
        Route::post('/recordExpenses-store', [DebitCollectionController::class, 'recordExpenses_store'])->name('recordExpenses-store');

        Route::get('/cashRemittance', [DebitCollectionController::class, 'cashRemittance'])->name('cashRemittance');
        Route::get('/cashRemittance-create', [DebitCollectionController::class, 'cashRemittance_create'])->name('cashRemittance-create');
        Route::post('/cashRemittance-store', [DebitCollectionController::class, 'cashRemittance_store'])->name('cashRemittance-store');
        Route::get('/cashRemittance-view/{id}', [DebitCollectionController::class, 'cashRemittance_view'])->name('cashRemittance-view');

        Route::get('/daily-sales-summary', [DebitCollectionController::class, 'daily_sales_summary'])->name('daily-sales-summary');

        Route::get('/downPayment', [DownPaymentController::class, 'downPayment'])->name('downPayment');
        Route::get('/downPayment-create', [DownPaymentController::class, 'downPayment_create'])->name('downPayment-create');
        Route::post('/downPayment-store', [DownPaymentController::class, 'downPayment_store'])->name('downPayment-store');

        Route::post('/customer-store', [OutletOutletController::class, 'customerStore'])
            ->name('customerStore');

        Route::get('/customer', [OutletOutletController::class, 'customerList'])->name('customerList');

        Route::put('/customer/{customer}', [OutletOutletController::class, 'customerUpdate'])
            ->name('customerUpdate');

        Route::delete('/customer/{customer}', [OutletOutletController::class, 'customerDestroy'])
            ->name('customerDestroy');

        Route::get('/sales-orders', [OutletOutletController::class, 'salesOrders'])->name('salesOrders');

        Route::get('/create-sales', [OutletOutletController::class, 'createSales'])
            ->name('createSales');

        Route::get('/search-customer', [OutletOutletController::class, 'searchCustomer'])->name('search-customer');

        Route::post('/store-sales', [OutletOutletController::class, 'storeSales'])->name('storeSales');

        Route::get('/sales-details/{id}', [OutletOutletController::class, 'salesDetails'])
            ->name('salesDetails');

        Route::get('/sales-invoice/{id}', [OutletOutletController::class, 'salesInvoice'])
            ->name('salesInvoice');

        Route::post('/store-stock-request', [OutletOutletController::class, 'storeStockRequest'])->name('storeStockRequest');

        Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.all');

        Route::any('/outlet-levels', [OutletOutletController::class, 'outlet_levels'])->name('outlet-levels');

        Route::any('/warehouse-levels', [OutletOutletController::class, 'warehouse_levels'])->name('warehouse-levels');

        Route::get('/outlet-request', [OutletOutletController::class, 'outletRequest'])->name('outlet-request');

        Route::get('view/outlet-request/{id}', [OutletOutletController::class, 'showOutletRequest'])->name('outlet-request-show');

        Route::any('/outlet-request-status-accept/{id}', [OutletOutletController::class, 'outlet_request_status_accept'])->name('outlet-request-status-accept');
        Route::any('/outlet-request-status-reject/{id}', [OutletOutletController::class, 'outlet_request_status_reject'])->name('outlet-request-status-reject');

        Route::get('/product-management', [OutletOutletController::class, 'productManagementList'])->name('productManagementList');
        Route::put('/product/{product}/update-price', [OutletOutletController::class, 'productUpdatePrice'])
            ->name('productUpdatePrice');

        Route::post('/sales/{sale}/generate-waybill', [OutletOutletController::class, 'generateWaybill'])->name('generateWaybill');
        Route::get('/waybill/{id}', [OutletOutletController::class, 'viewWaybillInvoice'])->name('viewWaybillInvoice');

        Route::get('/sales/{sale}/get-details', [OutletOutletController::class, 'getSaleDetails'])
            ->name('getSaleDetails');

        Route::post('/sales/{sale}/refund', [OutletOutletController::class, 'processRefund'])
            ->name('processRefund');
    });
});


//Supervisor Routes
Route::group(['prefix' => 'supervisor', 'as' => 'supervisor.', 'namespace' => 'App\Http\Controllers\Supervisor'], function () {

    Route::middleware('supervisor')->group(function () {
        Route::get('/supervisor-dashboard', [SupervisorController::class, 'indexDashboard'])->name('supervisor-dashboard');

        Route::get('/my-account', [SupervisorController::class, 'myAccount'])->name('myAccount');
        Route::post('/update-account', [SupervisorController::class, 'updateAccount'])->name('updateAccount');
        Route::get('/account/update-password', [SupervisorController::class, 'showChangePasswordForm'])->name('showChangePasswordForm');
        Route::post('/account/update-password', [SupervisorController::class, 'updatePassword'])->name('updatePassword');

        Route::post('/logout', [SupervisorController::class, 'logout'])->name('logout');

        Route::get('/cashRemittance', [SupervisorController::class, 'cashRemittance'])->name('cashRemittance');

        Route::any('/cash_remittance-status-accept/{id}', [SupervisorController::class, 'cash_remittance_status_accept'])->name('cash_remittance-status-accept');
        Route::any('/cash_remittance-status-reject/{id}', [SupervisorController::class, 'cash_remittance_status_reject'])->name('cash_remittance-status-reject');

        Route::get('/bank-deposit-create', [SupervisorController::class, 'bankDepositCreate'])
            ->name('bankDepositCreate');

        Route::post('/bank-deposit-store', [SupervisorController::class, 'bankDepositStore'])
            ->name('bankDepositStore');

        Route::get('/bank-deposit', [SupervisorController::class, 'bankDeposit'])->name('bankDeposit');

        Route::get('/cash-in-hand', [SupervisorController::class, 'cashInHand'])->name('cashInHand');

        Route::get('/external-source-create', [SupervisorController::class, 'externalResourceCreate'])
            ->name('externalResourceCreate');

        Route::post('/external-source-store', [SupervisorController::class, 'externalResourceStore'])
            ->name('externalResourceStore');

        Route::get('/external-source', [SupervisorController::class, 'externalResource'])->name('externalResource');

        Route::get('/final-cash-create', [SupervisorController::class, 'finalCashCreate'])
            ->name('finalCashCreate');

        Route::post('/final-cash-store', [SupervisorController::class, 'finalCashStore'])
            ->name('finalCashStore');

        Route::get('/final-cash', [SupervisorController::class, 'finalCashDestination'])->name('finalCashDestination');

        Route::get('/external-cash-inflow-create', [SupervisorController::class, 'externalCashInflowCreate'])
            ->name('externalCashInflowCreate');

        Route::post('/external-cash-inflow-store', [SupervisorController::class, 'externalCashInflowStore'])
            ->name('externalCashInflowStore');

        Route::get('/external-cash-inflow', [SupervisorController::class, 'externalCashInflow'])->name('externalCashInflow');

        Route::get('/external-cash-outFlow-create', [SupervisorController::class, 'externalCashOutFlowCreate'])
            ->name('externalCashOutFlowCreate');

        Route::post('/external-cash-outFlow-store', [SupervisorController::class, 'externalCashOutFlowStore'])
            ->name('externalCashOutFlowStore');

        Route::get('/external-cash-outFlow', [SupervisorController::class, 'externalCashOutFlow'])->name('externalCashOutFlow');


        Route::post('/notifications/{id}/mark-as-read', [SupervisorController::class, 'markAsRead'])->name('notifications.mark-as-read');
        Route::post('/notifications/mark-all-read', [SupervisorController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::get('/notifications/unread-count', [SupervisorController::class, 'getUnreadCount'])->name('notifications.unread-count');
        Route::get('/notifications', [SupervisorController::class, 'notificationList'])->name('notifications.all');

        Route::get('/record-expense-request', [SupervisorController::class, 'indexRecordExpense'])->name('record-expense-request');

        Route::any('record-expense-accept/{id}', [SupervisorController::class, 'acceptRecordExpense'])->name('record-expense-accept');
        Route::any('record-expense-reject/{id}', [SupervisorController::class, 'rejectRecordExpense'])->name('record-expense-reject');

        Route::any('/outlet-levels', [SupervisorController::class, 'outlet_levels'])->name('outlet-levels');

        Route::any('/warehouse-levels', [SupervisorController::class, 'warehouse_levels'])->name('warehouse-levels');

        Route::any('/sales-list', [SupervisorController::class, 'salesList'])->name('salesList');

        Route::get('/sales-list-view/{id}', [SupervisorController::class, 'sales_list_view'])->name('sales_list_view');

        Route::get('/return-request', [SupervisorController::class, 'indexReturnRequest'])->name('return-request');
        Route::get('/return-request-accept/{id}', [SupervisorController::class, 'return_request_accept'])
            ->name('return-request-accept');

        Route::get('/return-request-reject/{id}', [SupervisorController::class, 'return_request_reject'])
            ->name('return-request-reject');

        Route::get('/cash-refund-request', [SupervisorController::class, 'indexRefunds'])->name('cash-refund-request');

        Route::any('cash-refund-accept/{id}', [SupervisorController::class, 'acceptRefund'])->name('cash-refund-accept');
        Route::any('cash-refund-reject/{id}', [SupervisorController::class, 'rejectRefund'])->name('cash-refund-reject');
    });
});

//Auth Routes
Route::get('/login', [WarehouseAuthController::class, 'LoginForm'])->name('login');
Route::post('/login', [WarehouseAuthController::class, 'login'])->name('login');
Route::post('/logout', [WarehouseAuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [ForgotPasswordController::class, 'forgot_password'])->name('forgot-password');
Route::post('send-otp', [ForgotPasswordController::class, 'sendOtp'])->name('send-otp');
Route::get('/otp-verify', [WarehouseAuthController::class, 'showOtpForm'])->name('otp.verify');
Route::post('/otp-verify', [WarehouseAuthController::class, 'verifyOtp'])->name('otp.verify.submit');
Route::post('/otp/reset', [ForgotPasswordController::class, 'resetOtp'])->name('otp.reset');
Route::get('/reset-password', [WarehouseAuthController::class, 'resetpassword'])->name('reset-password')->middleware('otp.verified');
Route::post('/reset-password', [WarehouseAuthController::class, 'resetpasswordSubmit'])->name('reset-password.submit')
    ->middleware('otp.verified');
