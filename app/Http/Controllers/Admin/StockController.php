<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\ReceivedStock;
use App\Models\ReceivedStockItem;
use App\Events\StockDispatched;
use App\Models\Location;
use App\Models\StockTransferRequest;
use App\Models\StockTransferRequestsProduct;
use App\Notifications\StockRequestSendNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockController extends Controller
{
    //list
    public function index()
    {
        // $stocks = ReceivedStock::with(['warehouse', 'items.product'])->latest()->get();
        return view('admin.stock.index');
    }

    //
    public function create()
    {
        $warehouses = Location::where('type', 'warehouse')->where('status', 1)->get();
        $products = Product::where('status', 1)->get();;

        return view('admin.stock.create', compact('warehouses', 'products'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'supplier_name' => 'required|string|max:255',
    //         'warehouse_id' => 'required|exists:locations,id',
    //         'items' => 'required|array|min:1',
    //         'items.*.product_id' => 'required|exists:products,id',
    //         'items.*.quantity' => 'required|integer|min:1|regex:/^[0-9]+$/',
    //     ], [
    //         'supplier_name.required' => 'Please enter the supplier name.',
    //         'warehouse_id.required' => 'Please select a warehouse.',
    //         'items.required' => 'At least one product must be added.',
    //         'items.*.product_id.required' => 'Please select a product.',
    //         'items.*.product_id.exists' => 'One of the selected products is invalid.',
    //         'items.*.quantity.required' => 'Please enter a quantity for each product.',
    //         'items.*.quantity.integer' => 'Quantity must be a whole number, decimals are not allowed.',
    //         'items.*.quantity.min' => 'Quantity must be at least 1.',
    //         'items.*.quantity.regex' => 'Quantity must be a valid whole number without decimals.',
    //     ]);

    //     // Create the main received stock entry
    //     $receivedStock = StockTransferRequest::create([
    //         'supplier_name' => $request->supplier_name,
    //         'receiver_id' => $request->warehouse_id,
    //         'status' => 'created',
    //         'type' => 'admin',
    //     ]);

    //     // Save related items using relationship
    //     foreach ($request->items as $item) {
    //         StockTransferRequestsProduct::create([
    //             'transfer_request_id' => $receivedStock->id,
    //             'product_id' => $item['product_id'],
    //             'set_quantity' => $item['quantity'],
    //             'type' => 'admin',
    //         ]);
    //     }

    //     return redirect()->route('admin.stock.index')->with('success', 'Received Stock created successfully.');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'warehouse_id' => 'required|exists:locations,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1|regex:/^[0-9]+$/',
        ], [
            'supplier_name.required' => 'Please enter the supplier name.',
            'warehouse_id.required' => 'Please select a warehouse.',
            'items.required' => 'At least one product must be added.',
            'items.*.product_id.required' => 'Please select a product.',
            'items.*.product_id.exists' => 'One of the selected products is invalid.',
            'items.*.quantity.required' => 'Please enter a quantity for each product.',
            'items.*.quantity.integer' => 'Quantity must be a whole number, decimals are not allowed.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.quantity.regex' => 'Quantity must be a valid whole number without decimals.',
        ]);

        try {
            DB::beginTransaction();

            // Create the main received stock entry
            $receivedStock = StockTransferRequest::create([
                'supplier_name' => $request->supplier_name,
                'receiver_id' => $request->warehouse_id,
                'status' => 'created',
                'type' => 'admin',
            ]);

            // Save related items using relationship
            foreach ($request->items as $item) {
                StockTransferRequestsProduct::create([
                    'transfer_request_id' => $receivedStock->id,
                    'product_id' => $item['product_id'],
                    'set_quantity' => $item['quantity'],
                    'type' => 'admin',
                ]);
            }

            // Send notification to warehouse
            // $warehouseReceiver = Location::where('id', $request->warehouse_id)
            //     ->where('type', 'warehouse')
            //     ->first();

            // if ($warehouseReceiver) {
            //     $warehouseReceiver->notify(new StockRequestSendNotification($receivedStock));
            // } else {
            //     Log::warning('Warehouse receiver not found for notification.', ['warehouse_id' => $request->warehouse_id]);
            // }

            DB::commit();

            return redirect()->route('admin.stock.index')->with('success', 'Received Stock created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create received stock.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create received stock. Please try again.');
        }
    }

    public function show($id)
    {
        $stock = StockTransferRequest::with(['warehouse', 'items.product'])->findOrFail($id);
        return view('admin.stock.show', compact('stock'));
    }
    //edit
    public function edit($id)
    {
        $stock = StockTransferRequest::with('items')->findOrFail($id);
        $warehouses = Location::where('type', 'warehouse')->where('status', 1)->get();
        $products = Product::all();

        return view('admin.stock.edit', compact('stock', 'warehouses', 'products'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'warehouse_id' => 'required|exists:locations,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $stock = StockTransferRequest::findOrFail($id);
        $stock->update([
            'supplier_name' => $request->supplier_name,
            'receiver_id' => $request->warehouse_id,
        ]);

        // Clear old items
        $stock->items()->delete();

        // Re-add new ones
        foreach ($request->items as $item) {
            $stock->items()->create([
                'product_id' => $item['product_id'],
                'set_quantity' => $item['quantity'],
            ]);
        }

        return redirect()->route('admin.stock.index')->with('success', 'Received Stock updated successfully.');
    }


    // In StockController.php

    // public function updateStatus(Request $request)
    // {


    //     // Validate the input data
    //     $request->validate([
    //         'stock_id' => 'required|exists:stock_transfer_requests,id',
    //         'status' => 'required|in:created,dispatched', // Ensure the status is valid
    //     ]);

    //     // Find the stock by ID
    //     $stock = StockTransferRequest::findOrFail($request->stock_id);

    //     // Update the status
    //     $stock->status = $request->status;
    //     $stock->save();

    //     // ✅ Fire the broadcast event if dispatched
    //     if ($stock->status === 'dispatched') {
    //         sendNotification($stock->id, $stock->warehouse_id, '');
    //     }

    //     // Return a response to indicate success
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Stock status updated successfully.',
    //         'new_status' => $stock->status,
    //     ]);
    // }

    public function updateStatus(Request $request)
    {
        // Validate the input data
        $request->validate([
            'stock_id' => 'required|exists:stock_transfer_requests,id',
            'status' => 'required|in:created,dispatched', // Ensure the status is valid
        ]);

        try {
            DB::beginTransaction();

            // Find the stock by ID
            $stock = StockTransferRequest::findOrFail($request->stock_id);

            // Update the status
            $stock->status = $request->status;
            $stock->save();

            // ✅ Fire the broadcast event if dispatched
            if ($stock->status === 'dispatched') {
                sendNotification($stock->id, $stock->warehouse_id, '');

                // Send notification to warehouse
                $warehouseReceiver = Location::where('id', $stock->receiver_id)
                    ->where('type', 'warehouse')
                    ->first();

                if ($warehouseReceiver) {
                    // Pass 'dispatched' as the second parameter to customize the notification
                    $warehouseReceiver->notify(new StockRequestSendNotification($stock, 'dispatched'));
                } else {
                    Log::warning('Warehouse receiver not found for dispatch notification.', [
                        'stock_id' => $stock->id,
                        'receiver_id' => $stock->receiver_id
                    ]);
                }
            }

            DB::commit();

            // Return a response to indicate success
            return response()->json([
                'status' => 'success',
                'message' => 'Stock status updated successfully.',
                'new_status' => $stock->status,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update stock status.', [
                'stock_id' => $request->stock_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update stock status. Please try again.',
            ], 500);
        }
    }
}
