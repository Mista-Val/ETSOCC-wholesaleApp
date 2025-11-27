<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display Dashboard Page
     */
    public function dashboard()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        // 🏭 Weekly Warehouse Sales (Current Month Only)
        $warehouseSales = Sale::join('locations', 'sales.location_id', '=', 'locations.id')
            ->where('locations.type', '=', 'warehouse')
            ->whereBetween('sales.created_at', [$startOfMonth, $endOfMonth])
            ->select(
                DB::raw('WEEK(sales.created_at, 1) as week_number'),
                DB::raw('CONCAT("Week ", WEEK(sales.created_at, 1) - WEEK(DATE_SUB(DATE_FORMAT(sales.created_at, "%Y-%m-01"), INTERVAL 1 DAY), 1)) as week_label'),
                DB::raw('SUM(sales.total_amount) as total_sales')
            )
            ->groupBy('week_number', 'week_label')
            ->orderBy('week_number', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'week' => $item->week_label,
                    'total_sales' => round($item->total_sales, 2)
                ];
            });

        // 🏪 Weekly Outlet Sales (Current Month Only)
        $outletSales = Sale::join('locations', 'sales.location_id', '=', 'locations.id')
            ->where('locations.type', '=', 'outlet')
            ->whereBetween('sales.created_at', [$startOfMonth, $endOfMonth])
            ->select(
                DB::raw('WEEK(sales.created_at, 1) as week_number'),
                DB::raw('CONCAT("Week ", WEEK(sales.created_at, 1) - WEEK(DATE_SUB(DATE_FORMAT(sales.created_at, "%Y-%m-01"), INTERVAL 1 DAY), 1)) as week_label'),
                DB::raw('SUM(sales.total_amount) as total_sales')
            )
            ->groupBy('week_number', 'week_label')
            ->orderBy('week_number', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'week' => $item->week_label,
                    'total_sales' => round($item->total_sales, 2)
                ];
            });

        return view('admin.dashboard', compact('warehouseSales', 'outletSales'));
    }

    // CKEditor file upload
    public function uploadImage(Request $request)
    {
        if ($request->file('upload')) {
            $fileName = uploadFile($request->file('upload'), 'uploads/ckeditor', '');
            $url = asset('uploads/ckeditor/' . $fileName);
            return response()->json(['url' => $url]);
        } else {
            return response()->json(['url' => '']);
        }
    }
}
