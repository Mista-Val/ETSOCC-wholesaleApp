<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockTransferRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockController extends Controller
{
    /**
     * Get the warehouse for the currently authenticated user
     */
    private function getUserWarehouse()
    {
        $user = auth()->guard('warehouse')->user();

        if (!$user || !$user->warehouse) {
            return (object) ['id' => 0];
        }

        $warehouse = Location::where('user_id', $user->warehouse->user_id)
            ->where('id', $user->warehouse->id)
            ->first();

        return $warehouse ?? (object) ['id' => 0];
    }

    /**
     * Display received stock listing
     */
    public function index(Request $request)
    {
        $warehouse = $this->getUserWarehouse();

        $query = StockTransferRequest::with(['warehouse', 'items.product'])
            ->where('receiver_id', $warehouse->id)
            ->whereIn('status', ['dispatched', 'accepted', 'partially accepted'])
            ->where('type', 'admin');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('supplier_name', 'like', "%{$search}%")
                    ->orWhereHas('warehouse', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $stocks = $query->latest()->paginate(10);

        return view('web.warehouse.pages.recieve-stock', compact('stocks'));
    }

    /**
     * Show stock details
     */
    public function show($id)
    {
        $stock = StockTransferRequest::with(['warehouse', 'items.product'])->findOrFail($id);
        return view('web.warehouse.pages.stock-detail', compact('stock'));
    }

    /**
     * Show status update form
     */
    public function status_update(Request $request, $id)
    {
        $stock = StockTransferRequest::with(['warehouse', 'items.product'])->findOrFail($id);
        return view('web.warehouse.pages.update-status', compact('stock'));
    }


    public function updateStatus(Request $request, $id)
    {
        $stock = StockTransferRequest::with(['items.product'])->findOrFail($id);

        // Normalize null values to 0 BEFORE validation
        $request->merge([
            'received_quantity' => array_map(
                fn($v) => $v ?? 0,
                $request->input('received_quantity', [])
            )
        ]);

        // Build dynamic validation rules
        $rules = $this->buildValidationRules($stock, $request->status);

        // ✅ CUSTOM MESSAGES with dynamic remaining quantity
        $messages = [
            'received_quantity.*.required' => 'Please enter the received quantity.',
            'received_quantity.*.numeric'  => 'Please enter a valid number for received quantity.',
            'received_quantity.*.min'      => 'Please enter at least 1 quantity for partial acceptance.',
        ];

        // ✅ Add dynamic max message for each item
        foreach ($stock->items as $item) {
            $remaining = max(0, $item->set_quantity - $item->received_quantity);
            $messages["received_quantity.{$item->id}.max"] = "Received quantity cannot be more than the remaining quantity (Remaining: {$remaining}).";
        }

        $validatedData = $request->validate($rules, $messages);

        // ✅ SPECIAL CHECK: If this is FIRST TIME partial accept (status is NOT already "partially accepted")
        if ($request->status === 'partially accepted' && $stock->status !== 'partially accepted') {
            $isReceivingFullQuantity = true;

            foreach ($stock->items as $item) {
                $newReceived = (float) ($validatedData['received_quantity'][$item->id] ?? 0);
                $totalAfter = $item->received_quantity + $newReceived;

                // If any item is NOT fully received, then it's valid partial
                if ($totalAfter < $item->set_quantity) {
                    $isReceivingFullQuantity = false;
                    break;
                }
            }

            // ❌ Block ONLY on first time if receiving full quantity
            if ($isReceivingFullQuantity) {
                return back()->withErrors([
                    'status' => 'You cannot partially accept when receiving full quantity. Please use Accept button instead.'
                ])->withInput();
            }
        }

        // ✅ For PARTIAL ACCEPT: At least one item must have quantity > 0
        if ($request->status === 'partially accepted') {
            $hasReceivedSomething = false;

            foreach ($stock->items as $item) {
                $newReceived = (float) ($validatedData['received_quantity'][$item->id] ?? 0);

                if ($newReceived > 0) {
                    $hasReceivedSomething = true;
                    break;
                }
            }

            // ❌ Block if NO quantity entered at all
            if (!$hasReceivedSomething) {
                return back()->withErrors([
                    'status' => 'Please enter quantity for at least one item to partially accept.'
                ])->withInput();
            }
        }

        // ✅ Update received quantities incrementally
        foreach ($stock->items as $item) {
            $itemId = $item->id;
            $newReceived = (float) ($validatedData['received_quantity'][$itemId] ?? 0);

            // Add newly received quantity
            $item->received_quantity += $newReceived;

            // Update remarks if provided
            if ($request->has('remarks') && isset($request->remarks[$itemId])) {
                $item->remarks = $request->remarks[$itemId];
            }

            $item->save();

            // Update warehouse stock only if quantity received
            if ($newReceived > 0) {
                $this->updateWarehouseStock($stock->receiver_id, $item->product_id, $newReceived);
            }
        }

        // ✅ Auto-detect if all items fully received
        $allReceived = $stock->items->every(fn($i) => $i->received_quantity >= $i->set_quantity);

        if ($allReceived) {
            $stock->status = 'accepted'; // ✅ Automatically mark as accepted when everything is received
        } else {
            $stock->status = 'partially accepted'; // ✅ Remains partially accepted if anything is left
        }

        $stock->save();

        return redirect()->route('warehouse.recieve-stock', $id)
            ->with('success', 'Stock updated successfully.');
    }



    private function buildValidationRules($stock, $status)
    {
        $rules = [];

        foreach ($stock->items as $item) {
            $remainingQty = max(0, ($item->set_quantity - $item->received_quantity));

            // ✅ For PARTIAL ACCEPT: Allow 0 to remaining
            $minValue = 0;

            $rules["received_quantity.{$item->id}"] = [
                'required',
                'numeric',
                "min:$minValue",
                "max:$remainingQty", // ✅ Cannot exceed remaining quantity
            ];

            // ✅ For ACCEPTED status → must receive EXACT remaining quantity for ALL items
            if ($status === 'accepted') {
                $rules["received_quantity.{$item->id}"][] = function ($attribute, $value, $fail) use ($item) {
                    $remaining = $item->set_quantity - $item->received_quantity;
                    if ((float)$value != $remaining) {
                        $fail("You must receive full remaining quantity ({$remaining}) for {$item->product->name} to accept completely.");
                    }
                };
            }
        }

        return $rules;
    }

    /**
     * Update or create warehouse stock record
     */
    private function updateWarehouseStock($warehouseId, $productId, $quantity)
    {
        $warehouseStock = Stock::where('location_id', $warehouseId)
            ->where('product_id', $productId)
            ->first();

        if ($warehouseStock) {
            $warehouseStock->product_quantity += $quantity;
            $warehouseStock->save();
        } else {
            Stock::create([
                'location_id'      => $warehouseId,
                'product_id'       => $productId,
                'product_quantity' => $quantity,
                'type'             => 'warehouse',
            ]);
        }
    }

    public function outlet_levels(Request $request)
    {
        // Base query to filter stocks only for 'outlet' locations
        $query = Stock::query()
            ->whereHas('location', function ($q) {
                $q->where('type', 'outlet');
            });

        // Check if a specific outlet is selected
        if ($request->filled('outlet_id')) {
            // Filter by the specific outlet (original logic)
            $query->where('location_id', $request->outlet_id)
                ->with(['product', 'location']) // Eager load for single location view
                ->orderBy('id', 'desc');

            // Apply pagination
            $stocks = $query->paginate(10);
        } else {
            // **LOGIC FOR "All Outlets" (Total Sum)**

            // Select the product_id and calculate the sum of product_quantity
            // We also join the products table to get product details (name, sku)
            $query->select('product_id', DB::raw('SUM(product_quantity) as product_quantity'))
                ->with('product') // Eager load product details
                ->groupBy('product_id')
                ->join('products', 'stocks.product_id', '=', 'products.id')
                ->selectRaw('products.sku, products.name') // Select product details for use in view/pagination
                ->orderBy('products.name', 'asc'); // Order by product name for better grouping view

            // Apply pagination
            // Note: The structure of $stocks items changes here, 
            // they will now be query builder results with aggregated quantity.
            $stocks = $query->paginate(10);
        }

        // Get all outlets for the filter dropdown
        $outlets = Location::where('type', 'outlet')->get();

        // Return JSON for AJAX requests
        // if ($request->ajax()) {
        //     // Render table rows HTML
        //     $tableHtml = view('web.warehouse.outlet_levels.partials.stocks_table', compact('stocks'))->render();

        //     // Render pagination HTML only if there are multiple pages
        //     // Ensure you append all necessary request parameters
        //     $paginationHtml = $stocks->hasPages()
        //         ? $stocks->appends(['outlet_id' => $request->outlet_id])->links()->render()
        //         : '';

        //     return response()->json([
        //         'tableHtml' => $tableHtml,
        //         'paginationHtml' => $paginationHtml,
        //         'hasPages' => $stocks->hasPages()
        //     ]);
        // }

            if ($request->ajax()) {
            // Render table rows HTML
            $tableHtml = view('web.warehouse.outlet_levels.partials.stocks_table', compact('stocks'))->render();

            // Render pagination HTML with custom view and proper structure
            $paginationHtml = '';
            if ($stocks->total() > 0) {
                $paginationHtml = '<div class="flex items-center justify-between">';
                
                // Left side: Showing text
                $paginationHtml .= '<div class="text-sm text-gray-600">';
                $paginationHtml .= 'Showing ' . $stocks->firstItem() . ' to ' . $stocks->lastItem() . ' of ' . $stocks->total() . ' results';
                $paginationHtml .= '</div>';
                
                // Right side: Pagination links
                $paginationHtml .= '<div>';
                $paginationHtml .= $stocks->appends(['outlet_id' => $request->outlet_id])->links('vendor.pagination.custom-new')->render();
                $paginationHtml .= '</div>';
                
                $paginationHtml .= '</div>';
            }

            return response()->json([
                'tableHtml' => $tableHtml,
                'paginationHtml' => $paginationHtml,
                'total' => $stocks->total(),
                'hasPages' => $stocks->hasPages()
            ]);
        }

        // Return view for initial load
        return view('web.warehouse.outlet_levels.index', compact('stocks', 'outlets'));
    }


    public function warehouse_levels(Request $request)
    {
        // Base query to filter stocks only for 'warehouse' locations
        $query = Stock::query()
            ->whereHas('location', function ($q) {
                $q->where('type', 'warehouse');
            });

        // Check if a specific warehouse is selected
        if ($request->filled('warehouse_id')) {
            // Filter by the specific warehouse (original logic)
            $query->where('location_id', $request->warehouse_id)
                ->with(['product', 'location']) // Eager load for single location view
                ->orderBy('id', 'desc');

            // Apply pagination
            $stocks = $query->paginate(10);
        } else {
            // **LOGIC FOR "All Warehouses" (Total Sum)**

            // Select the product_id and calculate the sum of product_quantity
            // Join the products table to get product details (name, sku)
            $query->select('product_id', DB::raw('SUM(product_quantity) as product_quantity'))
                ->with('product') // Eager load product details
                ->groupBy('product_id')
                ->join('products', 'stocks.product_id', '=', 'products.id')
                ->selectRaw('products.sku, products.name') // Select product details
                ->orderBy('products.name', 'asc'); // Order by product name

            // Apply pagination
            // The resulting items will be query builder results with aggregated quantity
            $stocks = $query->paginate(10);
        }

        // Get all warehouses for the filter dropdown
        $warehouses = Location::where('type', 'warehouse')->get();

        // Return JSON for AJAX requests
        // if ($request->ajax()) {
        //     // Render table rows HTML
        //     $tableHtml = view('web.warehouse.warehouse_levels.partials.stocks_table', compact('stocks'))->render();

        //     // Render pagination HTML only if there are multiple pages
        //     $paginationHtml = $stocks->hasPages()
        //         ? $stocks->appends(['warehouse_id' => $request->warehouse_id])->links()->render()
        //         : '';

        //     return response()->json([
        //         'tableHtml' => $tableHtml,
        //         'paginationHtml' => $paginationHtml,
        //         'hasPages' => $stocks->hasPages()
        //     ]);
        // }

               if ($request->ajax()) {
        // Render table rows HTML
        $tableHtml = view('web.warehouse.warehouse_levels.partials.stocks_table', compact('stocks'))->render();

        // ✅ Render pagination HTML with custom view and proper structure
        $paginationHtml = '';
        if ($stocks->total() > 0) {
            $paginationHtml = '<div class="flex items-center justify-between">';
            
            // Left side: Showing text
            $paginationHtml .= '<div class="text-sm text-gray-600">';
            $paginationHtml .= 'Showing ' . $stocks->firstItem() . ' to ' . $stocks->lastItem() . ' of ' . $stocks->total() . ' results';
            $paginationHtml .= '</div>';
            
            // Right side: Pagination links
            $paginationHtml .= '<div>';
            $paginationHtml .= $stocks->appends(['warehouse_id' => $request->warehouse_id])->links('vendor.pagination.custom-new')->render();
            $paginationHtml .= '</div>';
            
            $paginationHtml .= '</div>';
        }

        return response()->json([
            'tableHtml' => $tableHtml,
            'paginationHtml' => $paginationHtml,
            'total' => $stocks->total(),
            'hasPages' => $stocks->hasPages()
        ]);
    }

        // Return view for initial load
        return view('web.warehouse.warehouse_levels.index', compact('stocks', 'warehouses'));
    }

    /**
     * Display return stock requests
     */
    public function return_list(Request $request)
    {
        $warehouse = $this->getUserWarehouse();

        $query = StockTransferRequest::with('senderOutlet', 'items')
            ->where('transfer_type', 'return')
            ->where('receiver_id', $warehouse->id)
            ->where('type', 'outlet');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('outlet', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
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

        $transferStocks = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('web.warehouse.return.index', compact('transferStocks'));
    }


    public function return_list_status_accept($id)
    {
        try {
            DB::beginTransaction();

            $stock = StockTransferRequest::with('items')->findOrFail($id);

            // Check if already accepted
            if ($stock->status === 'accepted_by_warehouse') {
                return redirect()->back()->with('error', 'Request already accepted.');
            }

            // Get warehouse and outlet locations
            $warehouse = $this->getUserWarehouse();

            if (!$warehouse || $warehouse->id === 0) {
                Log::error('Warehouse location not found');
                DB::rollBack();
                return redirect()->back()->with('error', 'Warehouse location not found.');
            }

            $outletLocation = Location::find($stock->supplier_id);

            if (!$outletLocation) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Outlet location not found.');
            }

            // Update stock quantities for each item
            // foreach ($stock->items as $item) {
            //     $this->processReturnItem($item, $warehouse->id, $outletLocation->id);
            // }

            // Update request status
            $stock->status = 'accepted_by_warehouse';
            $stock->save();

            // 🔔 Send notification to outlet that return is accepted
            $outletReceiver = Location::find($outletLocation->id);
            if ($outletReceiver) {
                $outletReceiver->notify(new \App\Notifications\StockNotification($stock, 'return_accepted'));
            } else {
                Log::warning('Outlet receiver not found for return acceptance notification.', [
                    'receiver_id' => $stock->receiver_id,
                    'transfer_id' => $stock->id
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Return request accepted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Return acceptance error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while processing the return: ' . $e->getMessage());
        }
    }
    /**
     * Reject return request
     */
    public function return_list_status_reject($id)
    {
        $stock = StockTransferRequest::findOrFail($id);
        $stock->status = 'rejected_by_warehouse';
        $stock->save();

        return redirect()->back()->with('success', 'Request rejected successfully.');
    }

    /**
     * View return request details
     */
    public function return_view($id)
    {
        $transferStock = StockTransferRequest::with('outlet', 'items.product')->findOrFail($id);
        return view('web.warehouse.return.view', compact('transferStock'));
    }
}
