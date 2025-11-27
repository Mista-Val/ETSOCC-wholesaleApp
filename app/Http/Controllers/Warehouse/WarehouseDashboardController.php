<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\CashRemittance;
use App\Models\DebitCollection;
use App\Models\Location;
use App\Models\RecordExpense;
use App\Models\Sale;
use App\Models\SoldProduct;
use App\Models\StockTransferRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WarehouseDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->guard('warehouse')->user();
        $warehouse = $user->warehouse;

        // Initialize all variables to avoid "undefined variable" errors
        $totalStockUnits = 0;
        $totalTransferredUnits = 0;
        $totalReceivedUnits = 0;
        $totalPendingRequestUnits = 0;
        $todaySalesAmount = 0;
        $todaySalesStockUnits = 0;
        $todayTotalDownPayment = 0;
        $todayCashPayment = 0;
        $todayTotalDebtCollection = 0;
        $todayTotalCashRemittedCollection = 0;
        $todayTotalLogExpenseCollection = 0;
        $totalDebtCollection = 0;

        if ($warehouse) {
            $warehouseId = $warehouse->id;

            $totalStockUnits = $warehouse->stocks()->sum('product_quantity');

            $totalTransferredUnits = StockTransferRequest::where('supplier_id', $warehouseId)
                ->where('stock_transfer_requests.type', 'warehouse')
                ->join('stock_transfer_requests_products', 'stock_transfer_requests.id', '=', 'stock_transfer_requests_products.transfer_request_id')
                ->sum('stock_transfer_requests_products.set_quantity');

            $totalReceivedUnits = StockTransferRequest::where('receiver_id', $warehouseId)
                ->where('stock_transfer_requests.type', 'admin')
                ->whereIn('status', ['dispatched', 'accepted'])
                ->join('stock_transfer_requests_products', 'stock_transfer_requests.id', '=', 'stock_transfer_requests_products.transfer_request_id')
                ->sum('stock_transfer_requests_products.set_quantity');

            $startOfToday = Carbon::today();
            $endOfToday = Carbon::now();

            $todaySalesAmount = Sale::where('location_id', $warehouseId)
                ->whereBetween('created_at', [$startOfToday, $endOfToday])
                ->sum('total_amount');

            $todaySalesStockUnits = SoldProduct::whereHas('sale', function ($query) use ($warehouseId, $startOfToday, $endOfToday) {
                $query->where('location_id', $warehouseId)
                    ->whereBetween('created_at', [$startOfToday, $endOfToday]);
            })
                ->sum('quantity');

            $todayTotalDownPayment = Sale::where('location_id', $warehouseId)
                ->where('payment_method', 'Down Payment')
                ->whereBetween('created_at', [$startOfToday, $endOfToday])
                ->sum('total_amount');

            $todayTotalDebtCollection = DebitCollection::where('location_id', $warehouseId)
                ->where('type', 'dept_collection')
                ->whereBetween('created_at', [$startOfToday, $endOfToday])
                ->sum('amount');

            $totalDebtCollection = DebitCollection::where('location_id', $warehouseId)
                ->where('type', 'dept_collection')
                ->sum('amount');

            $todayTotalCashRemittedCollection = CashRemittance::where('location_id', $warehouseId)
                ->whereBetween('created_at', [$startOfToday, $endOfToday])
                ->sum('amount');

            $todayTotalLogExpenseCollection = RecordExpense::where('location_id', $warehouseId)
                ->where('status','accepted')
                ->whereBetween('created_at', [$startOfToday, $endOfToday])
                ->sum('amount');

            $todayCashPayment = Sale::where('location_id', $warehouseId)
                ->where('payment_method', 'Cash')
                ->whereBetween('created_at', [$startOfToday, $endOfToday])
                ->sum('total_amount');

            $availableCashPayment = Location::where('id', $warehouseId)
                ->sum('balance');

            $totalPendingRequestUnits = StockTransferRequest::where('receiver_id', $warehouseId)
                ->where('stock_transfer_requests.transfer_type', 'return')
                ->where('status', 'pending')
                ->join('stock_transfer_requests_products', 'stock_transfer_requests.id', '=', 'stock_transfer_requests_products.transfer_request_id')
                ->sum('stock_transfer_requests_products.set_quantity');
        }

        return view('web.warehouse.pages.dashboard', compact(
            'totalStockUnits',
            'totalTransferredUnits',
            'totalReceivedUnits',
            'totalPendingRequestUnits',
            'todaySalesAmount',
            'todayTotalDownPayment',
            'todayCashPayment',
            'todaySalesStockUnits',
            'todayTotalDebtCollection',
            'todayTotalCashRemittedCollection',
            'todayTotalLogExpenseCollection',
            'availableCashPayment',
            'totalDebtCollection'
        ));
    }

    public function transferOutletDetails(Request $request)
    {
        return view('web.warehouse.pages.dashboard');
    }



    public function markAsRead($id)
    {
        $user = auth()->guard('warehouse')->user();
        $warehouse = $user->warehouse;

        if ($warehouse) {
            $notification = $warehouse->notifications()->find($id);

            if ($notification) {
                $notification->markAsRead();
                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false], 404);
    }

    // Mark all notifications as read
    public function markAllAsRead()
    {
        $user = auth()->guard('warehouse')->user();
        $warehouse = $user->warehouse;

        if ($warehouse) {
            $warehouse->unreadNotifications->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    // Get unread count
    public function getUnreadCount()
    {
        $user = auth()->guard('warehouse')->user();
        $warehouse = $user->warehouse;

        $count = $warehouse ? $warehouse->unreadNotifications()->count() : 0;

        return response()->json(['count' => $count]);
    }

    // Show all notifications page
    public function notificationList()
    {
        $user = auth()->guard('warehouse')->user();
        $warehouse = $user->warehouse;

        $notifications = $warehouse ? $warehouse->notifications()->paginate(20) : collect();

        return view('web.warehouse.pages.notification-list', compact('notifications'));
    }
}
