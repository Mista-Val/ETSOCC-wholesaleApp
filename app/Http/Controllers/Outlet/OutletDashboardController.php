<?php 
namespace App\Http\Controllers\Outlet;

use App\Http\Controllers\Controller;
use App\Models\CashRemittance;
use App\Models\DebitCollection;
use App\Models\DownPayment;
use App\Models\Location;
use App\Models\RecordExpense;
use App\Models\Sale;
use App\Models\SoldProduct;
use App\Models\StockTransferRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OutletDashboardController extends Controller
{
    // public function indexOutlet(){
    //      return view('web.outlet.pages.dashboard');
    // }


    public function indexOutlet()
    {
        $user = auth()->guard('outlet')->user();
        $outlet = $user->outlet;

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

        if ($outlet) {
            $outletId = $outlet->id;

            $totalStockUnits = $outlet->stocks()->sum('product_quantity');

            $totalTransferredUnits = StockTransferRequest::where('supplier_id', $outletId)
                ->where('stock_transfer_requests.type', 'outlet')
                ->join('stock_transfer_requests_products', 'stock_transfer_requests.id', '=', 'stock_transfer_requests_products.transfer_request_id')
                ->sum('stock_transfer_requests_products.set_quantity');

            $totalReceivedUnits = StockTransferRequest::where('receiver_id', $outletId)
                ->where('stock_transfer_requests.type', 'warehouse')
                ->whereIn('status', ['dispatched', 'completed'])
                ->join('stock_transfer_requests_products', 'stock_transfer_requests.id', '=', 'stock_transfer_requests_products.transfer_request_id')
                ->sum('stock_transfer_requests_products.set_quantity');

            $startOfToday = Carbon::today();
            $endOfToday = Carbon::now();

            $todaySalesAmount = Sale::where('location_id', $outletId)
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startOfToday, $endOfToday])
                ->sum('total_amount');

            $todaySalesStockUnits = SoldProduct::whereHas('sale', function ($query) use ($outletId, $startOfToday, $endOfToday) {
                $query->where('location_id', $outletId)
                    ->whereBetween('created_at', [$startOfToday, $endOfToday]);
            })
                ->sum('quantity');

            $todayTotalDownPayment = DownPayment::where('location_id', $outletId)
                ->where('type', 'down_payment')
                ->whereBetween('created_at', [$startOfToday, $endOfToday])
                ->sum('amount');

            $todayTotalDebtCollection = DebitCollection::where('location_id', $outletId)
                ->where('type', 'dept_collection')
                ->whereBetween('created_at', [$startOfToday, $endOfToday])
                ->sum('amount');

            $todayTotalCashSaleCollection = Sale::where('location_id', $outletId)
                ->where('payment_method', 'Cash')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startOfToday, $endOfToday])
                ->sum('total_amount');

            $totalDebtCollection = DebitCollection::where('location_id', $outletId)
                ->where('type', 'dept_collection')
                ->sum('amount');

            $todayTotalCashRemittedCollection = CashRemittance::where('location_id', $outletId)
                ->where('status','accepted')
                ->whereBetween('created_at', [$startOfToday, $endOfToday])
                ->sum('amount');

            $todayTotalLogExpenseCollection = RecordExpense::where('location_id', $outletId)
                ->where('status','accepted')
                ->whereBetween('created_at', [$startOfToday, $endOfToday])
                ->sum('amount');

            $todayCashPayment = Sale::where('location_id', $outletId)
                ->where('payment_method', 'Cash')
                ->whereBetween('created_at', [$startOfToday, $endOfToday])
                ->sum('total_amount');

            $totalPendingRequestUnits = StockTransferRequest::where('supplier_id', $outletId)
                ->where('stock_transfer_requests.transfer_type', 'return')
                ->where('status', 'pending')
                ->join('stock_transfer_requests_products', 'stock_transfer_requests.id', '=', 'stock_transfer_requests_products.transfer_request_id')
                ->sum('stock_transfer_requests_products.set_quantity');

            $availableCashPayment = Location::where('id', $outletId)
                ->sum('balance');
        }

        return view('web.outlet.pages.dashboard', compact(
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
            'totalDebtCollection',
            'todayTotalCashSaleCollection',
            'availableCashPayment'
        ));
    }
}
