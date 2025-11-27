<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockTransferRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockRequestController extends Controller
{
    //
    public function stock_returened()
    {
        return view('admin.stock_returened.index');
    }
    public function accross_outlets_returened()
    {
        $stocks = Stock::with(['product', 'location'])
            ->where('type', 'outlet')->orderBy('id', 'desc')
            ->get();


        $outlets = Location::where('type', 'outlet')->get(); // use get() for all outlets, not paginate
        // dd($stocks->toArray());
        return view('admin.accross_outlets_returened.index', compact('outlets', 'stocks'));
    }

    // public function returned_requests_list()
    // {
    //     $data = StockTransferRequest::with('outlet', 'items')->where('transfer_type', 'return')->orderBy('created_at', 'asc')->get();
    //     return view('admin.returned_requests.index', compact('data'));
    // }
    public function returned_requests_list()
    {
        $data = StockTransferRequest::with('outlet', 'items')
            ->where('transfer_type', 'return')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.returned_requests.index', compact('data'));
    }
    public function returned_requests_view($id)
    {
        $data = StockTransferRequest::with('outlet', 'items')->where('transfer_type', 'return')->orderBy('created_at', 'desc')->where('id', base64_decode($id))->first();
        return view('admin.returned_requests.view', compact('data'));
    }

    // app/Http/Controllers/Admin/StockRequestController.php

    public function returned_requests_accept($id)
    {
        $transfer = StockTransferRequest::findOrFail(base64_decode($id));

        // Check if supervisor approved first
        if ($transfer->status !== 'accepted_by_warehouse_supervisor') {
            return redirect()->back()->with('error', 'This request must be accepted by supervisor first.');
        }

        DB::beginTransaction();
        try {
            // Update approval status
            $transfer->update([
                'status' => 'accepted_by_all'
            ]);

            // Process stock movements
            // foreach ($transfer->items as $item) {
            //     // 1. Increase warehouse stock (receiver)
            //     $warehouseStock = Stock::where('product_id', $item->product_id)
            //         ->where('location_id', $transfer->receiver_id)
            //         ->where('type', 'warehouse')
            //         ->first();

            //     if ($warehouseStock) {
            //         $warehouseStock->increment('product_quantity', $item->quantity);
            //     } else {
            //         // Create new stock record if doesn't exist
            //         Stock::create([
            //             'product_id' => $item->product_id,
            //             'location_id' => $transfer->receiver_id,
            //             'product_quantity' => $item->quantity,
            //             'type' => 'warehouse'
            //         ]);
            //     }

            //     // 2. Decrease outlet stock (sender)
            //     $outletStock = Stock::where('product_id', $item->product_id)
            //         ->where('location_id', $transfer->supplier_id)
            //         ->where('type', 'outlet')
            //         ->first();

            //     if ($outletStock) {
            //         if ($outletStock->product_quantity >= $item->quantity) {
            //             $outletStock->decrement('product_quantity', $item->quantity);
            //         } else {
            //             throw new \Exception("Insufficient stock in outlet for product ID: {$item->product_id}. Available: {$outletStock->product_quantity}, Required: {$item->quantity}");
            //         }
            //     } else {
            //         throw new \Exception("Product ID: {$item->product_id} not found in outlet stock.");
            //     }
            // }

            foreach ($transfer->items as $item) {
                $this->processReturnItem($item, $transfer->receiver_id, $transfer->supplier_id);
            }

            DB::commit();

            Log::info("Admin accepted return request #{$transfer->id}. Stock updated successfully.");

            return redirect()->route('admin.returned-requests-list')
                ->with('success', 'Return request accepted successfully and stock updated!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Admin Accept Return Error: ' . $e->getMessage(), [
                'transfer_id' => $transfer->id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to process return: ' . $e->getMessage());
        }
    }

    /**
     * Process individual return item stock update
     */
    private function processReturnItem($item, $warehouseId, $outletId)
    {
        // 1. Increase quantity in warehouse
        $warehouseStock = Stock::where('product_id', $item->product_id)
            ->where('location_id', $warehouseId)
            ->first();

        if ($warehouseStock) {
            $warehouseStock->product_quantity += $item->set_quantity;
            $warehouseStock->save();
        } else {
            Stock::create([
                'product_id'       => $item->product_id,
                'location_id'      => $warehouseId,
                'product_quantity' => $item->set_quantity,
                'type'             => 'warehouse'
            ]);
        }

        // 2. Decrease quantity from outlet
        $outletStock = Stock::where('product_id', $item->product_id)
            ->where('location_id', $outletId)
            ->first();

        if ($outletStock) {
            $oldQuantity = $outletStock->product_quantity;
            $newQuantity = $oldQuantity - $item->set_quantity;

            // Prevent negative stock
            if ($newQuantity < 0) {
                Log::warning('Outlet stock would be negative', [
                    'product_id'       => $item->product_id,
                    'current_quantity' => $oldQuantity,
                    'return_quantity'  => $item->set_quantity
                ]);
                $newQuantity = 0;
            }

            $outletStock->product_quantity = $newQuantity;
            $outletStock->save();
        }
    }

    public function returned_requests_reject($id)
    {
        $transfer = StockTransferRequest::findOrFail(base64_decode($id));

        // Check if already processed
        // if (in_array($transfer->status, ['accepted_by_admin', 'rejected_by_admin', 'rejected_by_supervisor', 'rejected_by_warehouse'])) {
        //     return redirect()->back()->with('error', 'This request has already been processed.');
        // }

        DB::beginTransaction();
        try {
            $transfer->update([
                'status' => 'rejected_by_admin'
            ]);

            DB::commit();

            Log::info("Admin rejected return request #{$transfer->id}.");

            return redirect()->back()
                ->with('success', 'Return request rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin Reject Return Error: ' . $e->getMessage(), [
                'transfer_id' => $transfer->id
            ]);

            return redirect()->back()->with('error', 'Failed to reject return. Please try again.');
        }
    }

    public function allAcrossWarehouse()
    {
        $stocks = Stock::with(['product', 'location'])
            ->where('type', 'warehouse')->orderBy('id', 'desc')
            ->get();


        $outlets = Location::where('type', 'warehouse')->get();
        // dd($stocks->toArray());
        return view('admin.all_across_warehouse.index', compact('outlets', 'stocks'));
    }
}
