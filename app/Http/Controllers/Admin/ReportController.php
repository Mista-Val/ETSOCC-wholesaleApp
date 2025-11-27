<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerLocationBalance;
use App\Models\DownPayment;
use App\Models\Sale;
use App\Models\StockTransferRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function indexSalesReport()
    {
        // 1. Remove the data fetching: $salesReports = Sale::orderBy('created_at', 'desc')->paginate(10);
        // 2. The view name must match the path where your layout is, e.g., 'admin.reports.index' 
        //    if you put the layout file there.
        // 3. We'll assume the view file is admin/sales-reports/index.blade.php
        return view('admin.sales-reports.index'); // No need to compact('salesReports')
    }

    public function showSalesReport($id)
    {
        $sale = Sale::with([
            'customer',
            'location',
            'soldProducts.product'
        ])->findOrFail($id);
        return view('admin.sales-reports.show', compact('sale'));
    }

    public function indexStockMovementsReport()
    {
        // 1. Remove the data fetching: $salesReports = Sale::orderBy('created_at', 'desc')->paginate(10);
        // 2. The view name must match the path where your layout is, e.g., 'admin.reports.index' 
        //    if you put the layout file there.
        // 3. We'll assume the view file is admin/sales-reports/index.blade.php
        return view('admin.stock-movements.index'); // No need to compact('salesReports')
    }

    public function showStockMovementsReport($id)
    {
        $transfer = StockTransferRequest::with([
            'warehouse',
            'outlet',
            // 'stockTransferDetails.product'
        ])->findOrFail($id);

        return view('admin.stock-movements.show', compact('transfer'));
    }
    public function indexCashRemittanceReport()
    {
        // 1. Remove the data fetching: $salesReports = Sale::orderBy('created_at', 'desc')->paginate(10);
        // 2. The view name must match the path where your layout is, e.g., 'admin.reports.index' 
        //    if you put the layout file there.
        // 3. We'll assume the view file is admin/sales-reports/index.blade.php
        return view('admin.cash-remittance-report.index'); // No need to compact('salesReports')
    }

    public function indexDownPaymentReport()
    {
        // 1. Remove the data fetching: $salesReports = Sale::orderBy('created_at', 'desc')->paginate(10);
        // 2. The view name must match the path where your layout is, e.g., 'admin.reports.index' 
        //    if you put the layout file there.
        // 3. We'll assume the view file is admin/sales-reports/index.blade.php
        return view('admin.down-payment-report.index'); // No need to compact('salesReports')
    }

    public function indexDebtCollectionReport()
    {
        // 1. Remove the data fetching: $salesReports = Sale::orderBy('created_at', 'desc')->paginate(10);
        // 2. The view name must match the path where your layout is, e.g., 'admin.reports.index' 
        //    if you put the layout file there.
        // 3. We'll assume the view file is admin/sales-reports/index.blade.php
        return view('admin.debt-collection-report.index'); // No need to compact('salesReports')
    }

    public function showDebtCollectionReport($id)
    {
        // Fetch the down payment record with related data
        $payment = DownPayment::with([
            'coustomer',
            'location',
            // 'user'
        ])->findOrFail($id);

        // Get customer's balance information for ONLY the specific location where payment was made
        $locationBalance = CustomerLocationBalance::with('location')
            ->where('customer_id', $payment->customer_id)
            ->where('location_id', $payment->location_id)
            ->first();

        return view('admin.debt-collection-report.show', compact('payment', 'locationBalance'));
    }

    public function indexCashHandlingReport()
    {
        // 1. Remove the data fetching: $salesReports = Sale::orderBy('created_at', 'desc')->paginate(10);
        // 2. The view name must match the path where your layout is, e.g., 'admin.reports.index' 
        //    if you put the layout file there.
        // 3. We'll assume the view file is admin/sales-reports/index.blade.php
        return view('admin.cash-handling-report.index'); // No need to compact('salesReports')
    }

    public function indexCustomer()
    {
        // 1. Remove the data fetching: $salesReports = Sale::orderBy('created_at', 'desc')->paginate(10);
        // 2. The view name must match the path where your layout is, e.g., 'admin.reports.index' 
        //    if you put the layout file there.
        // 3. We'll assume the view file is admin/sales-reports/index.blade.php
        return view('admin.customers.index'); // No need to compact('salesReports')
    }

    // In the appropriate Controller, e.g., app/Http/Controllers/Admin/CustomerController.php

    public function showCustomer($id)
    {
        // Eager load sales, and for each sale, load its location and its soldProducts,
        // and for each sold product, load the Product details.
        $customer = Customer::with(['sales' => function ($query) {
            $query->with(['location', 'soldProducts.product']); // <-- CORRECTED: Using soldProducts
        }])->findOrFail($id);

        return view('admin.customers.show', compact('customer'));
    }
}
