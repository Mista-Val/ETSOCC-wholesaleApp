<?php

namespace App\Http\Controllers\Outlet;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockTransferRequest;
use App\Models\StockTransferRequestsProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferRequestController extends Controller
{
    // ============================================
    // OUTLET TRANSFER REQUEST METHODS
    // ============================================

    /**
     * Display outlet transfer requests list
     */
    public function index(Request $request)
    {
        $userOutlet = $this->getUserOutletFromAuth();

        $query = StockTransferRequest::with(['outlet', 'senderOutlet'])
            ->where('receiver_id', $userOutlet->id ?? 0)
            ->where('type', 'outlet_request')
            ->orderBy('created_at', 'desc');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('outlet', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Outlet filter
        if ($request->filled('outlet_id')) {
            $query->where('receiver_id', $request->outlet_id);
        }

        $transferStocks = $query->paginate(10);

        return view('web.outlet.transfer_request.index', compact('transferStocks'));
    }

    /**
     * Show create transfer request form
     */
    public function create()
    {
        $loggedInUser = auth()->guard('outlet')->user();
        $userOutlet = null;

        if ($loggedInUser) {
            $userOutlet = Location::where('user_id', $loggedInUser->id)->first();
        }

        $outlets = Location::where('status', 1)
            ->where('id', '!=', $userOutlet->id ?? 0)
            ->where('type', 'outlet')
            ->get();

        $products = Product::where('status', 1)->get();

        return view('web.outlet.transfer_request.create', compact('outlets', 'products'));
    }

    /**
     * Store new transfer request
     */
    public function store(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:locations,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ], [
            'outlet_id.required' => 'Please select an outlet.',
            'outlet_id.exists' => 'The selected outlet is invalid.',
            'products.required' => 'Please add at least one product.',
            'products.array' => 'Products must be a valid list.',
            'products.min' => 'Please add at least one product.',
            'products.*.product_id.required' => 'Please select a product.',
            'products.*.product_id.exists' => 'The selected product is invalid.',
            'products.*.quantity.required' => 'Please enter a quantity.',
            'products.*.quantity.integer' => 'Quantity must be a number.',
            'products.*.quantity.min' => 'Quantity must be at least 1.',
        ]);

        $loggedInUser = auth()->guard('outlet')->user();
        $userOutlet = null;

        if ($loggedInUser) {
            $userOutlet = Location::where('user_id', $loggedInUser->id)->first();
        }

        if (!$userOutlet) {
            return redirect()->back()->with('error', 'No outlet assigned to this user.');
        }

        // Prevent requesting from the same outlet
        if ($userOutlet->id == $request->outlet_id) {
            return redirect()->back()->with('error', 'Cannot request stock from the same outlet.');
        }

        // Validate stock availability in source outlet
        $stockErrors = [];
        foreach ($request->products as $index => $item) {
            $stock = Stock::where('location_id', $request->outlet_id)
                ->where('product_id', $item['product_id'])
                ->first();

            $availableStock = $stock ? $stock->product_quantity : 0;

            if ($item['quantity'] > $availableStock) {
                $product = Product::find($item['product_id']);
                $productName = $product ? $product->name : 'Product';

                $stockErrors["products.{$index}.quantity"] = "Insufficient stock for {$productName} in source outlet. Available: {$availableStock}, Requested: {$item['quantity']}";
            }
        }

        if (!empty($stockErrors)) {
            return back()->withErrors($stockErrors)->withInput();
        }

        DB::beginTransaction();

        try {
            $transferRequest = StockTransferRequest::create([
                'supplier_id' => $request->outlet_id,
                'receiver_id' => $userOutlet->id,
                'type' => 'outlet_request',
                'remarks' => $request->remarks ?? null,
                'status' => 'created',
            ]);

            foreach ($request->products as $product) {
                StockTransferRequestsProduct::create([
                    'transfer_request_id' => $transferRequest->id,
                    'product_id' => $product['product_id'],
                    'set_quantity' => $product['quantity'],
                    'received_quantity' => 0,
                    'type' => 'outlet_request',
                    'status' => 'created',
                ]);
            }

            DB::commit();

            return redirect()->route('outlet.outlet-transfer-request')
                ->with('success', 'Transfer request created successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Transfer request error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while creating the request. Please try again.')
                ->withInput();
        }
    }

    /**
     * View transfer request details
     */
    public function view($id)
    {
        $transferStock = StockTransferRequest::with('outlet', 'items.product')->findOrFail($id);
        return view('web.outlet.transfer_request.view', compact('transferStock'));
    }

    // ============================================
    // RETURNED REQUESTS METHODS
    // ============================================

    /**
     * Display returned requests list
     */
    public function returned_requests(Request $request)
    {
        $userOutlet = $this->getUserOutletFromAuth();

        $query = StockTransferRequest::with('outlet', 'items')
            ->where('supplier_id', $userOutlet->id ?? 0)
            ->where('transfer_type', 'return')
            ->where('type', 'outlet')
            ->orderBy('created_at', 'desc');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('outlet', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transferStocks = $query->paginate(10);

        return view('web.outlet.returned_requests.index', compact('transferStocks'));
    }

    /**
     * Show create returned request form
     */
    public function returned_requests_create()
    {
        $warhouse = Location::where('type', 'warehouse')
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->get();

        $products = Product::where('status', 1)
            ->orderBy('id', 'desc')
            ->get();

        $users = User::where('status', 1)->where('role', 'supervisor')->get();

        return view('web.outlet.returned_requests.create', compact('warhouse', 'products','users'));
    }

    /**
     * Store new returned request
     */
    // public function returned_requests_store(Request $request)
    // {
    //     $request->validate([
    //         'outlet_id' => 'required|exists:locations,id',
    //         'remarks' => 'nullable|string|max:500',
    //         'products' => 'required|array|min:1',
    //         'products.*.product_id' => 'required|exists:products,id',
    //         'products.*.quantity' => 'required|integer|min:1',
    //     ], [
    //         'outlet_id.required' => 'Please select a warehouse.',
    //         'outlet_id.exists' => 'Selected warehouse is invalid.',
    //         'remarks.max' => 'Remarks cannot exceed 500 characters.',
    //         'products.required' => 'Please add at least one product.',
    //         'products.min' => 'Please add at least one product.',
    //         'products.*.product_id.required' => 'Please select a product.',
    //         'products.*.product_id.exists' => 'Selected product is invalid.',
    //         'products.*.quantity.required' => 'Please enter quantity.',
    //         'products.*.quantity.integer' => 'Quantity must be a number.',
    //         'products.*.quantity.min' => 'Quantity must be at least 1.',
    //     ]);

    //     $loggedInUser = auth()->guard('outlet')->user();
    //     $userOutlet = null;

    //     if ($loggedInUser) {
    //         $userOutlet = Location::where('user_id', $loggedInUser->id)->first();
    //     }

    //     if (!$userOutlet) {
    //         return redirect()->back()->with('error', 'No outlet assigned to this user.');
    //     }

    //     // Validate that selected warehouse is different from user's outlet
    //     if ($userOutlet->id == $request->outlet_id) {
    //         return redirect()->back()->with('error', 'Cannot return stock to the same location.');
    //     }

    //     // Validate stock availability and source warehouse
    //     $stockErrors = [];
    //     foreach ($request->products as $index => $item) {
    //         $stock = Stock::where('location_id', $userOutlet->id)
    //             ->where('product_id', $item['product_id'])
    //             ->first();

    //         $totalStockQuantity = $stock ? $stock->product_quantity : 0;
    //         $product = Product::find($item['product_id']);
    //         $productName = $product ? $product->name : 'Product';

    //         // Check if there's enough stock to return
    //         if ($item['quantity'] > $totalStockQuantity) {
    //             $stockErrors["products.{$index}.quantity"] = "Insufficient stock for {$productName}. Available: {$totalStockQuantity}, Requested: {$item['quantity']}";
    //             continue;
    //         }

    //         // Check if this product was received from the selected warehouse
    //         $receivedFromWarehouse = StockTransferRequestsProduct::whereHas('transferRequest', function ($query) use ($request, $userOutlet) {
    //             $query->where('supplier_id', $request->outlet_id)
    //                 ->where('receiver_id', $userOutlet->id);
    //         })
    //             ->where('product_id', $item['product_id'])
    //             ->exists();

    //         if (!$receivedFromWarehouse) {
    //             $stockErrors["products.{$index}.product_id"] = "{$productName} was not received from the selected warehouse. You can only return products to their source warehouse.";
    //         }
    //     }

    //     if (!empty($stockErrors)) {
    //         return back()->withErrors($stockErrors)->withInput();
    //     }

    //     DB::beginTransaction();

    //     try {
    //         $transferStock = StockTransferRequest::create([
    //             'supplier_id' => $userOutlet->id,
    //             'receiver_id' => $request->outlet_id,
    //             'type' => 'outlet',
    //             'remark' => $request->remarks,
    //             'transfer_type' => 'return',
    //             'status' => 'pending',
    //         ]);

    //         foreach ($request->products as $product) {
    //             StockTransferRequestsProduct::create([
    //                 'transfer_request_id' => $transferStock->id,
    //                 'product_id' => $product['product_id'],
    //                 'set_quantity' => $product['quantity'],
    //                 'type' => 'outlet',
    //                 'status' => 'pending',
    //             ]);
    //         }

    //         DB::commit();

    //         return redirect()->route('outlet.returned-requests')
    //             ->with('success', 'Return request created successfully!');
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         Log::error('Return request error: ' . $e->getMessage());
    //         return redirect()->back()
    //             ->with('error', 'An error occurred while creating return request. Please try again.')
    //             ->withInput();
    //     }
    // }


    public function returned_requests_store(Request $request)
{
    $request->validate([
        'supplier_name' => 'required',
        'outlet_id' => 'required|exists:locations,id',
        'remarks' => 'required|string|max:500',
        'products' => 'required|array|min:1',
        'products.*.product_id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|integer|min:1',
    ], [
        'supplier_name.required' => 'Please select a receiver.',
        'outlet_id.required' => 'Please select a warehouse.',
        'outlet_id.exists' => 'Selected warehouse is invalid.',
        'remarks.required' => 'Reason is required.',
        'remarks.max' => 'Reason cannot exceed 500 characters.',
        'products.required' => 'Please add at least one product.',
        'products.min' => 'Please add at least one product.',
        'products.*.product_id.required' => 'Please select a product.',
        'products.*.product_id.exists' => 'Selected product is invalid.',
        'products.*.quantity.required' => 'Please enter quantity.',
        'products.*.quantity.integer' => 'Quantity must be a number.',
        'products.*.quantity.min' => 'Quantity must be at least 1.',
    ]);

    $loggedInUser = auth()->guard('outlet')->user();
    $userOutlet = null;

    if ($loggedInUser) {
        $userOutlet = Location::where('user_id', $loggedInUser->id)->first();
    }

    if (!$userOutlet) {
        return redirect()->back()->with('error', 'No outlet assigned to this user.');
    }

    if ($userOutlet->id == $request->outlet_id) {
        return redirect()->back()->with('error', 'Cannot return stock to the same location.');
    }

    // Validate stock availability and source warehouse
    $stockErrors = [];
    foreach ($request->products as $index => $item) {
        $stock = Stock::where('location_id', $userOutlet->id)
            ->where('product_id', $item['product_id'])
            ->first();

        $totalStockQuantity = $stock ? $stock->product_quantity : 0;
        $product = Product::find($item['product_id']);
        $productName = $product ? $product->name : 'Product';

        if ($item['quantity'] > $totalStockQuantity) {
            $stockErrors["products.{$index}.quantity"] = "Insufficient stock for {$productName}. Available: {$totalStockQuantity}, Requested: {$item['quantity']}";
            continue;
        }

        $receivedFromWarehouse = StockTransferRequestsProduct::whereHas('transferRequest', function ($query) use ($request, $userOutlet) {
            $query->where('supplier_id', $request->outlet_id)
                ->where('receiver_id', $userOutlet->id);
        })
            ->where('product_id', $item['product_id'])
            ->exists();

        if (!$receivedFromWarehouse) {
            $stockErrors["products.{$index}.product_id"] = "{$productName} was not received from the selected warehouse. You can only return products to their source warehouse.";
        }
    }

    if (!empty($stockErrors)) {
        return back()->withErrors($stockErrors)->withInput();
    }

    DB::beginTransaction();

    try {
        $transferStock = StockTransferRequest::create([
            'supplier_name'=> $request->supplier_name,
            'supplier_id' => $userOutlet->id,
            'receiver_id' => $request->outlet_id,
            'type' => 'outlet',
            'remark' => $request->remarks,
            'transfer_type' => 'return',
            'status' => 'pending',
        ]);

        foreach ($request->products as $product) {
            StockTransferRequestsProduct::create([
                'transfer_request_id' => $transferStock->id,
                'product_id' => $product['product_id'],
                'set_quantity' => $product['quantity'],
                'type' => 'outlet',
                'status' => 'pending',
            ]);
        }

        // ✅ Send notification to warehouse
        $warehouse = Location::find($request->outlet_id);
        if ($warehouse) {
            try {
                $warehouse->notify(new \App\Notifications\StockNotification($transferStock, 'return_created', [
                    'outlet_name' => $userOutlet->name ?? 'Unknown Outlet'
                ]));
            } catch (\Exception $e) {
                Log::warning('Notification to warehouse failed: ' . $e->getMessage());
            }
        }

        DB::commit();

        return redirect()->route('outlet.returned-requests')
            ->with('success', 'Return request created successfully!');
    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Return request error: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'An error occurred while creating return request. Please try again.')
            ->withInput();
    }
}


    /**
     * View returned request details
     */
    public function returned_requests_view($id)
    {
        $transferStock = StockTransferRequest::with('outlet', 'items.product')->findOrFail($id);
        return view('web.outlet.returned_requests.view', compact('transferStock'));
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Get current user's outlet from auth
     */
    private function getUserOutletFromAuth()
    {
        $loggedInUsers = auth()->guard('outlet')->user();
        $loggedInUser = $loggedInUsers->outlet;
        $userOutlet = null;

        if ($loggedInUser) {
            $userOutlet = Location::where('user_id', $loggedInUser->user_id)
                ->where('type', 'outlet')
                ->where('id', $loggedInUser->id)
                ->first();
        }

        return $userOutlet;
    }
}