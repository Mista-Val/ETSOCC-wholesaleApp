<?php

namespace App\Http\Controllers\Outlet;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerLocationBalance;
use App\Models\Location;
use App\Models\Product;
use App\Models\Refund;
use App\Models\Sale;
use App\Models\SoldProduct;
use App\Models\Stock;
use App\Models\StockTransferRequest;
use App\Models\StockTransferRequestsProduct;
use App\Models\User;
use App\Models\Waybill;
use App\Notifications\StockNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\StockRequestSendNotification;
use Illuminate\Validation\Rule;

class OutletController extends Controller
{
    // ============================================
    // STOCK RECEIVING METHODS
    // ============================================

    /**
     * Display list of stocks to receive from warehouses
     */
    public function index(Request $request)
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

        $query = StockTransferRequest::with([
            'warehouse',
            'items.product'
        ])
            ->where('receiver_id', $userOutlet->id ?? 0)
            ->where('type', 'warehouse')
            ->whereIn('status', ['accepted', 'dispatched', 'partially accepted']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('warehouse', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%");
                })
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $stocks = $query->latest()->paginate(10);

        return view('web.outlet.pages.receive-stock', compact('stocks'));
    }

    /**
     * Show stock details
     */
    public function show($id)
    {
        $stock = StockTransferRequest::with(['warehouse', 'items.product'])->findOrFail($id);
        return view('web.outlet.pages.stock-detail', compact('stock'));
    }

    /**
     * Show status update form
     */
    public function status_update(Request $request, $id)
    {
        $stock = StockTransferRequest::with(['warehouse', 'items.product'])->findOrFail($id);
        return view('web.outlet.pages.update-status', compact('stock'));
    }

    /**
     * Update stock receipt status
     */
    // public function updateStatus(Request $request, $id)
    // {
    //     $stock = StockTransferRequest::with(['items.product'])->findOrFail($id);
    //     $rules = [];

    //     foreach ($stock->items as $item) {
    //         $rules["received_quantity.{$item->id}"] = [
    //             'required',
    //             'numeric',
    //             'min:0',
    //             'max:' . $item->set_quantity,
    //         ];
    //         $rules["remarks.{$item->id}"] = ['nullable', 'string', 'max:255'];
    //     }

    //     $messages = [
    //         'received_quantity.*.max' => 'Received quantity cannot be more than the ordered quantity.',
    //         'received_quantity.*.required' => 'Please enter the received quantity.',
    //         'received_quantity.*.numeric' => 'Received quantity must be a number.',
    //         'received_quantity.*.min' => 'Received quantity cannot be negative.',
    //     ];

    //     $validatedData = $request->validate($rules, $messages);

    //     foreach ($stock->items as $item) {
    //         $itemId = $item->id;
    //         $receivedQty = $validatedData['received_quantity'][$itemId] ?? 0;

    //         $item->received_quantity = $receivedQty;

    //         if (isset($validatedData['remarks'][$itemId])) {
    //             $item->remarks = $validatedData['remarks'][$itemId];
    //         }

    //         $item->save();

    //         $warehouseId = $stock->receiver_id;
    //         $productId = $item->product_id;

    //         // Update outlet stock
    //         $outletStock = Stock::where('location_id', $warehouseId)
    //             ->where('product_id', $productId)
    //             ->first();

    //         if ($outletStock) {
    //             $outletStock->product_quantity += $receivedQty;
    //             $outletStock->save();
    //         } else {
    //             Stock::create([
    //                 'location_id' => $warehouseId,
    //                 'product_id' => $productId,
    //                 'product_quantity' => $receivedQty,
    //                 'type' => 'outlet',
    //             ]);
    //         }

    //         // Deduct from warehouse stock
    //         $warehouseStock = Stock::where('location_id', $stock->supplier_id)
    //             ->where('product_id', $item->product_id)
    //             ->where('type', 'warehouse')
    //             ->first();

    //         if ($warehouseStock) {
    //             $warehouseStock->product_quantity -= $receivedQty;
    //             if ($warehouseStock->product_quantity < 0) {
    //                 $warehouseStock->product_quantity = 0;
    //             }
    //             $warehouseStock->save();
    //         }
    //     }

    //     $stock->status = 'completed';
    //     $stock->save();

    //     return redirect()->route('outlet.receive-stock', $id)->with('success', 'Stock updated successfully.');
    // }


    // public function updateStatus(Request $request, $id)
    // {
    //     $stock = StockTransferRequest::with(['items.product'])->findOrFail($id);
    //     $rules = [];

    //     foreach ($stock->items as $item) {
    //         $rules["received_quantity.{$item->id}"] = [
    //             'required',
    //             'numeric',
    //             'min:0',
    //             'max:' . $item->set_quantity,
    //         ];
    //         $rules["remarks.{$item->id}"] = ['nullable', 'string', 'max:255'];
    //     }

    //     $messages = [
    //         'received_quantity.*.max' => 'Received quantity cannot be more than the ordered quantity.',
    //         'received_quantity.*.required' => 'Please enter the received quantity.',
    //         'received_quantity.*.numeric' => 'Received quantity must be a number.',
    //         'received_quantity.*.min' => 'Received quantity cannot be negative.',
    //     ];

    //     $validatedData = $request->validate($rules, $messages);

    //     foreach ($stock->items as $item) {
    //         $itemId = $item->id;
    //         $receivedQty = $validatedData['received_quantity'][$itemId] ?? 0;

    //         $item->received_quantity = $receivedQty;

    //         if (isset($validatedData['remarks'][$itemId])) {
    //             $item->remarks = $validatedData['remarks'][$itemId];
    //         }

    //         $item->save();

    //         $warehouseId = $stock->receiver_id;
    //         $productId = $item->product_id;

    //         // Update outlet stock
    //         $outletStock = Stock::where('location_id', $warehouseId)
    //             ->where('product_id', $productId)
    //             ->first();

    //         if ($outletStock) {
    //             $outletStock->product_quantity += $receivedQty;
    //             $outletStock->save();
    //         } else {
    //             Stock::create([
    //                 'location_id' => $warehouseId,
    //                 'product_id' => $productId,
    //                 'product_quantity' => $receivedQty,
    //                 'type' => 'outlet',
    //             ]);
    //         }

    //         // Deduct from warehouse stock
    //         $warehouseStock = Stock::where('location_id', $stock->supplier_id)
    //             ->where('product_id', $item->product_id)
    //             ->where('type', 'warehouse')
    //             ->first();

    //         if ($warehouseStock) {
    //             $warehouseStock->product_quantity -= $receivedQty;
    //             if ($warehouseStock->product_quantity < 0) {
    //                 $warehouseStock->product_quantity = 0;
    //             }
    //             $warehouseStock->save();
    //         }
    //     }

    //     // ✅ Mark transfer completed
    //     $stock->status = 'completed';
    //     $stock->save();

    //     // 🔔 Send notification to warehouse
    //     $warehouseReceiver = \App\Models\Location::find($stock->supplier_id);
    //     if ($warehouseReceiver) {
    //         $warehouseReceiver->notify(new \App\Notifications\StockNotification($stock, 'stock_received_by_outlet'));
    //     } else {
    //         Log::warning('Warehouse not found for stock completion notification.', [
    //             'supplier_id' => $stock->supplier_id,
    //             'transfer_id' => $stock->id
    //         ]);
    //     }

    //     return redirect()->route('outlet.receive-stock', $id)->with('success', 'Stock updated successfully.');
    // }


    // public function updateStatus(Request $request, $id)
    // {
    //     $stock = StockTransferRequest::with(['items.product'])->findOrFail($id);

    //     // 1. Build dynamic validation rules based on the desired status
    //     $rules = $this->buildValidationRules($stock, $request->status);

    //     $messages = [
    //         'received_quantity.*.max'      => 'Received quantity cannot be more than the ordered quantity.',
    //         'received_quantity.*.required' => 'Please enter the received quantity.',
    //         'received_quantity.*.numeric'  => 'Received quantity must be a number.',
    //         'received_quantity.*.min'      => 'Received quantity cannot be negative.',
    //         'status.required'              => 'The status (accepted or partially accepted) is required.'
    //     ];

    //     // Ensure status field is included in rules for validation
    //     $rules['status'] = [
    //         'required',
    //         Rule::in(['accepted', 'partially accepted']),
    //     ];

    //     // 2. Normalize null values to 0 before validation
    //     $request->merge([
    //         'received_quantity' => array_map(
    //             fn($v) => $v ?? 0,
    //             $request->input('received_quantity', [])
    //         )
    //     ]);

    //     $validatedData = $request->validate($rules, $messages);
    //     $newStatus = $validatedData['status'];

    //     // 3. Validate partial acceptance logic: cannot use 'partially accepted' if everything arrived
    //     if ($newStatus === 'partially accepted' && $this->allItemsFullyReceived($stock, $validatedData)) {
    //         return back()->withErrors([
    //             'status' => 'You cannot submit as partially accepted when all items are fully received. Use "accepted" instead.'
    //         ])->withInput();
    //     }

    //     // 4. Process Stock Items and Update Inventory
    //     $this->processAndUpdateInventory($stock, $validatedData, $request);

    //     // 5. Update overall transfer status to the requested status
    //     $stock->status = $newStatus;
    //     $stock->save();

    //     // 6. Send notification to warehouse (supplier)
    //     $warehouseReceiver = Location::find($stock->supplier_id);
    //     if ($warehouseReceiver) {
    //         $notificationType = ($newStatus === 'accepted' || $newStatus === 'partially accepted')
    //             ? 'stock_received_by_outlet'
    //             : 'stock_rejected_by_outlet';

    //         $warehouseReceiver->notify(new StockNotification($stock, $notificationType));
    //     } else {
    //         Log::warning('Warehouse not found for stock completion notification.', [
    //             'supplier_id' => $stock->supplier_id,
    //             'transfer_id' => $stock->id
    //         ]);
    //     }

    //     return redirect()->route('outlet.receive-stock', $id)
    //         ->with('success', "Stock updated and transfer marked as '{$newStatus}' successfully.");
    // }

    // /**
    //  * Build dynamic validation rules for stock items based on the new status.
    //  *
    //  * @param \App\Models\StockTransferRequest $stock
    //  * @param string $status
    //  * @return array
    //  */
    // private function buildValidationRules($stock, $status)
    // {
    //     $rules = [];

    //     foreach ($stock->items as $item) {
    //         // General rules for received quantity
    //         $rules["received_quantity.{$item->id}"] = [
    //             'required',
    //             'numeric',
    //             'min:0',
    //             // Maximum received quantity is the ordered quantity
    //             'max:' . ($item->set_quantity ?? 0),
    //         ];

    //         $rules["remarks.{$item->id}"] = ['nullable', 'string', 'max:255'];

    //         // Specific rule for 'accepted' status
    //         if ($status === 'accepted') {
    //             $rules["received_quantity.{$item->id}"][] = function ($attribute, $value, $fail) use ($item) {
    //                 if ((int)$value != (int)$item->set_quantity) {
    //                     $fail("Received quantity of {$item->product->name} must match requested quantity ({$item->set_quantity}) for a full acceptance.");
    //                 }
    //             };
    //         }
    //     }

    //     return $rules;
    // }

    // /**
    //  * Check if all items are fully received (received quantity equals set quantity).
    //  *
    //  * @param \App\Models\StockTransferRequest $stock
    //  * @param array $validatedData
    //  * @return bool
    //  */
    // private function allItemsFullyReceived($stock, $validatedData)
    // {
    //     foreach ($stock->items as $item) {
    //         $receivedQty = $validatedData['received_quantity'][$item->id] ?? 0;
    //         if ((int)$receivedQty < (int)$item->set_quantity) {
    //             return false;
    //         }
    //     }
    //     return true;
    // }



    //     public function updateStatus(Request $request, $id)
    // {
    //     $stock = StockTransferRequest::with(['items.product'])->findOrFail($id);

    //     // Normalize null values to 0 BEFORE validation
    //     $request->merge([
    //         'received_quantity' => array_map(
    //             fn($v) => $v ?? 0,
    //             $request->input('received_quantity', [])
    //         )
    //     ]);

    //     // Ensure status field is included in rules for validation
    //     $request->merge([
    //         'status' => $request->status ?? 'partially accepted'
    //     ]);

    //     // Build dynamic validation rules based on the desired status
    //     $rules = $this->buildValidationRules($stock, $request->status);

    //     // Add status validation
    //     $rules['status'] = [
    //         'required',
    //         Rule::in(['accepted', 'partially accepted']),
    //     ];

    //     $messages = [
    //         'received_quantity.*.required' => 'Please enter the received quantity.',
    //         'received_quantity.*.numeric'  => 'Received quantity must be a number.',
    //         'received_quantity.*.min'      => 'Please enter at least 1 quantity for partial acceptance.',
    //         'status.required'              => 'The status (accepted or partially accepted) is required.'
    //     ];

    //     // Add dynamic max message for each item
    //     foreach ($stock->items as $item) {
    //         $remaining = max(0, $item->set_quantity - $item->received_quantity);
    //         $messages["received_quantity.{$item->id}.max"] = "Received quantity cannot be more than the remaining quantity (Remaining: {$remaining}).";
    //     }

    //     $validatedData = $request->validate($rules, $messages);
    //     $newStatus = $validatedData['status'];

    //     // ✅ SPECIAL CHECK: If this is FIRST TIME partial accept (status is NOT already "partially accepted")
    //     if ($newStatus === 'partially accepted' && $stock->status !== 'partially accepted') {
    //         $isReceivingFullQuantity = true;

    //         foreach ($stock->items as $item) {
    //             $newReceived = (float) ($validatedData['received_quantity'][$item->id] ?? 0);
    //             $totalAfter = $item->received_quantity + $newReceived;

    //             // If any item is NOT fully received, then it's valid partial
    //             if ($totalAfter < $item->set_quantity) {
    //                 $isReceivingFullQuantity = false;
    //                 break;
    //             }
    //         }

    //         // ❌ Block ONLY on first time if receiving full quantity
    //         if ($isReceivingFullQuantity) {
    //             return back()->withErrors([
    //                 'status' => 'You cannot partially accept when receiving full quantity. Please use Accept button instead.'
    //             ])->withInput();
    //         }
    //     }

    //     // ✅ For PARTIAL ACCEPT: At least one item must have quantity > 0
    //     if ($newStatus === 'partially accepted') {
    //         $hasReceivedSomething = false;

    //         foreach ($stock->items as $item) {
    //             $newReceived = (float) ($validatedData['received_quantity'][$item->id] ?? 0);

    //             if ($newReceived > 0) {
    //                 $hasReceivedSomething = true;
    //                 break;
    //             }
    //         }

    //         // ❌ Block if NO quantity entered at all
    //         if (!$hasReceivedSomething) {
    //             return back()->withErrors([
    //                 'status' => 'Please enter quantity for at least one item to partially accept.'
    //             ])->withInput();
    //         }
    //     }

    //     // Process Stock Items and Update Inventory
    //     $this->processAndUpdateInventory($stock, $validatedData, $request);

    //     // ✅ Auto-detect if all items fully received
    //     $allReceived = $stock->items->every(fn($i) => $i->received_quantity >= $i->set_quantity);

    //     if ($allReceived) {
    //         $stock->status = 'accepted'; // ✅ Automatically mark as accepted when everything is received
    //     } else {
    //         $stock->status = 'partially accepted'; // ✅ Remains partially accepted if anything is left
    //     }

    //     $stock->save();

    //     // Send notification to warehouse (supplier)
    //     $warehouseReceiver = Location::find($stock->supplier_id);
    //     if ($warehouseReceiver) {
    //         $notificationType = ($stock->status === 'accepted' || $stock->status === 'partially accepted')
    //             ? 'stock_received_by_outlet'
    //             : 'stock_rejected_by_outlet';

    //         $warehouseReceiver->notify(new StockNotification($stock, $notificationType));
    //     } else {
    //         Log::warning('Warehouse not found for stock completion notification.', [
    //             'supplier_id' => $stock->supplier_id,
    //             'transfer_id' => $stock->id
    //         ]);
    //     }

    //     return redirect()->route('outlet.receive-stock', $id)
    //         ->with('success', "Stock updated successfully.");
    // }

    // /**
    //  * Build dynamic validation rules for stock items based on the new status.
    //  */
    // private function buildValidationRules($stock, $status)
    // {
    //     $rules = [];

    //     foreach ($stock->items as $item) {
    //         $remainingQty = max(0, ($item->set_quantity - $item->received_quantity));

    //         // ✅ For PARTIAL ACCEPT: Allow 0 to remaining
    //         $minValue = 0;

    //         $rules["received_quantity.{$item->id}"] = [
    //             'required',
    //             'numeric',
    //             "min:$minValue",
    //             "max:$remainingQty", // ✅ Cannot exceed remaining quantity
    //         ];

    //         $rules["remarks.{$item->id}"] = ['nullable', 'string', 'max:255'];

    //         // ✅ For ACCEPTED status → must receive EXACT remaining quantity for ALL items
    //         if ($status === 'accepted') {
    //             $rules["received_quantity.{$item->id}"][] = function ($attribute, $value, $fail) use ($item) {
    //                 $remaining = $item->set_quantity - $item->received_quantity;
    //                 if ((float)$value != $remaining) {
    //                     $fail("You must receive full remaining quantity ({$remaining}) for {$item->product->name} to accept completely.");
    //                 }
    //             };
    //         }
    //     }

    //     return $rules;
    // }


    //     /**
    //      * Helper to handle stock item updates and inventory changes (Add to Outlet, Deduct from Warehouse).
    //      *
    //      * @param \App\Models\StockTransferRequest $stock
    //      * @param array $validatedData
    //      * @param \Illuminate\Http\Request $request
    //      * @return void
    //      */
    //     private function processAndUpdateInventory($stock, $validatedData, $request)
    //     {
    //         foreach ($stock->items as $item) {
    //             $itemId = $item->id;
    //             $receivedQty = $validatedData['received_quantity'][$itemId] ?? 0;

    //             // Update received quantity and remarks on the transfer item
    //             $item->received_quantity = $receivedQty;
    //             if (isset($validatedData['remarks'][$itemId])) {
    //                 $item->remarks = $validatedData['remarks'][$itemId];
    //             }
    //             $item->save();

    //             $receiverId = $stock->receiver_id; // Outlet location
    //             $productId = $item->product_id;

    //             // A) Update OUTLET stock (RECEIVER: ADD quantity)
    //             $outletStock = Stock::where('location_id', $receiverId)
    //                 ->where('product_id', $productId)
    //                 ->where('type', 'outlet')
    //                 ->first();

    //             if ($outletStock) {
    //                 $outletStock->product_quantity += $receivedQty;
    //                 $outletStock->save();
    //             } else {
    //                 Stock::create([
    //                     'location_id'      => $receiverId,
    //                     'product_id'       => $productId,
    //                     'product_quantity' => $receivedQty,
    //                     'type'             => 'outlet',
    //                 ]);
    //             }

    //             // B) Deduct from WAREHOUSE stock (SUPPLIER: DEDUCT quantity)
    //             $warehouseStock = Stock::where('location_id', $stock->supplier_id)
    //                 ->where('product_id', $item->product_id)
    //                 ->where('type', 'warehouse')
    //                 ->first();

    //             if ($warehouseStock) {
    //                 $warehouseStock->product_quantity -= $receivedQty;
    //                 // Prevent negative stock (optional)
    //                 if ($warehouseStock->product_quantity < 0) {
    //                     $warehouseStock->product_quantity = 0;
    //                 }
    //                 $warehouseStock->save();
    //             } else {
    //                 Log::error("Cannot deduct stock. Missing warehouse stock record.", [
    //                     'supplier_id' => $stock->supplier_id,
    //                     'product_id'  => $item->product_id,
    //                 ]);
    //             }
    //         }
    //     }


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

        // Ensure status field is included in rules for validation
        $request->merge([
            'status' => $request->status ?? 'partially accepted'
        ]);

        // Build dynamic validation rules based on the desired status
        $rules = $this->buildValidationRules($stock, $request->status);

        // Add status validation
        $rules['status'] = [
            'required',
            Rule::in(['accepted', 'partially accepted']),
        ];

        $messages = [
            'received_quantity.*.required' => 'Please enter the received quantity.',
            'received_quantity.*.numeric'  => 'Received quantity must be a number.',
            'received_quantity.*.min'      => 'Please enter at least 1 quantity for partial acceptance.',
            'status.required'              => 'The status (accepted or partially accepted) is required.'
        ];

        // Add dynamic max message for each item
        foreach ($stock->items as $item) {
            $remaining = max(0, $item->set_quantity - $item->received_quantity);
            $messages["received_quantity.{$item->id}.max"] = "Received quantity cannot be more than the remaining quantity (Remaining: {$remaining}).";
        }

        $validatedData = $request->validate($rules, $messages);
        $newStatus = $validatedData['status'];

        // ✅ SPECIAL CHECK: If this is FIRST TIME partial accept (status is NOT already "partially accepted")
        if ($newStatus === 'partially accepted' && $stock->status !== 'partially accepted') {
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
        if ($newStatus === 'partially accepted') {
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

        // Process Stock Items and Update Inventory
        $this->processAndUpdateInventory($stock, $validatedData, $request);

        // ✅ Auto-detect if all items fully received
        $allReceived = $stock->items->every(fn($i) => $i->received_quantity >= $i->set_quantity);

        if ($allReceived) {
            $stock->status = 'accepted'; // ✅ Automatically mark as accepted when everything is received
        } else {
            $stock->status = 'partially accepted'; // ✅ Remains partially accepted if anything is left
        }

        $stock->save();

        // Send notification to warehouse (supplier)
        $warehouseReceiver = Location::find($stock->supplier_id);
        if ($warehouseReceiver) {
            $notificationType = ($stock->status === 'accepted' || $stock->status === 'partially accepted')
                ? 'stock_received_by_outlet'
                : 'stock_rejected_by_outlet';

            $warehouseReceiver->notify(new StockNotification($stock, $notificationType));
        } else {
            Log::warning('Warehouse not found for stock completion notification.', [
                'supplier_id' => $stock->supplier_id,
                'transfer_id' => $stock->id
            ]);
        }

        return redirect()->route('outlet.receive-stock', $id)
            ->with('success', "Stock updated successfully.");
    }

    /**
     * Build dynamic validation rules for stock items based on the new status.
     */
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

            $rules["remarks.{$item->id}"] = ['nullable', 'string', 'max:255'];

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
     * Process stock items and update inventory incrementally.
     */
    private function processAndUpdateInventory($stock, $validatedData, $request)
    {
        foreach ($stock->items as $item) {
            $itemId = $item->id;
            $newReceived = (float) ($validatedData['received_quantity'][$itemId] ?? 0);

            // ✅ Add newly received quantity (incremental, NOT replace)
            $item->received_quantity += $newReceived;

            // Update remarks if provided
            if ($request->has('remarks') && isset($request->remarks[$itemId])) {
                $item->remarks = $request->remarks[$itemId];
            }

            $item->save();

            // ✅ Update outlet and warehouse stock only if quantity received
            if ($newReceived > 0) {
                $receiverId = $stock->receiver_id; // Outlet location
                $productId = $item->product_id;

                // A) Update OUTLET stock (RECEIVER: ADD quantity)
                $outletStock = Stock::where('location_id', $receiverId)
                    ->where('product_id', $productId)
                    ->where('type', 'outlet')
                    ->first();

                if ($outletStock) {
                    $outletStock->product_quantity += $newReceived;
                    $outletStock->save();
                } else {
                    Stock::create([
                        'location_id'      => $receiverId,
                        'product_id'       => $productId,
                        'product_quantity' => $newReceived,
                        'type'             => 'outlet',
                    ]);
                }

                // B) Deduct from WAREHOUSE stock (SUPPLIER: DEDUCT quantity)
                $warehouseStock = Stock::where('location_id', $stock->supplier_id)
                    ->where('product_id', $item->product_id)
                    ->where('type', 'warehouse')
                    ->first();

                if ($warehouseStock) {
                    $warehouseStock->product_quantity -= $newReceived;
                    // Prevent negative stock (optional)
                    if ($warehouseStock->product_quantity < 0) {
                        $warehouseStock->product_quantity = 0;
                    }
                    $warehouseStock->save();
                } else {
                    Log::error("Cannot deduct stock. Missing warehouse stock record.", [
                        'supplier_id' => $stock->supplier_id,
                        'product_id'  => $item->product_id,
                    ]);
                }
            }
        }
    }


    // ============================================
    // OUTLET TO OUTLET TRANSFER METHODS
    // ============================================

    /**
     * Display outlet transfer list
     */
    public function transferOutlets(Request $request)
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

        $query = StockTransferRequest::with('outlet')
            ->where('supplier_id', $userOutlet->id ?? 0)
            ->where('type', 'outlet')
            ->where('transfer_type', '!=', 'return');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('outlet', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
                ->orWhere('id', 'like', "%{$search}%");
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $transferStocks = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('web.outlet.pages.transfer-to-outlets', compact('transferStocks'));
    }

    /**
     * Show create transfer form
     */
    public function transferOutletsCreate(Request $request)
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

        return view('web.outlet.pages.transfer-to-outlets-create', compact('outlets', 'products'));
    }

    /**
     * Store outlet transfer
     */
    public function transferOutletsStore(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:locations,id',
            'remarks' => 'nullable|string|max:500',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ], [
            'outlet_id.required' => 'Please select an outlet.',
            'outlet_id.exists' => 'Selected outlet is invalid.',
            'remarks.max' => 'Remarks cannot exceed 500 characters.',
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

        // Prevent transferring to the same outlet
        if ($userOutlet->id == $request->outlet_id) {
            return redirect()->back()->with('error', 'Cannot transfer stock to the same outlet.');
        }

        // Validate stock availability
        $stockErrors = [];
        foreach ($request->products as $index => $item) {
            $stock = Stock::where('location_id', $userOutlet->id)
                ->where('product_id', $item['product_id'])
                ->first();

            $totalStockQuantity = $stock ? $stock->product_quantity : 0;

            if ($item['quantity'] > $totalStockQuantity) {
                $product = Product::find($item['product_id']);
                $productName = $product ? $product->name : 'Product';

                $stockErrors["products.{$index}.quantity"] = "Insufficient stock for {$productName}. Available: {$totalStockQuantity}, Requested: {$item['quantity']}";
            }
        }

        if (!empty($stockErrors)) {
            return back()->withErrors($stockErrors)->withInput();
        }

        DB::beginTransaction();

        try {
            $transferStock = StockTransferRequest::create([
                'supplier_id' => $userOutlet->id,
                'receiver_id' => $request->outlet_id,
                'type' => 'outlet',
                'remarks' => $request->remarks,
                'status' => 'completed',
            ]);

            foreach ($request->products as $product) {
                StockTransferRequestsProduct::create([
                    'transfer_request_id' => $transferStock->id,
                    'product_id' => $product['product_id'],
                    'set_quantity' => $product['quantity'],
                    'received_quantity' => $product['quantity'],
                    'type' => 'outlet',
                    'status' => 'completed',
                ]);

                // Update sender outlet stock
                $senderStock = Stock::where('location_id', $userOutlet->id)
                    ->where('product_id', $product['product_id'])
                    ->lockForUpdate()
                    ->first();

                if ($senderStock) {
                    $senderStock->product_quantity -= $product['quantity'];
                    if ($senderStock->product_quantity < 0) {
                        $senderStock->product_quantity = 0;
                    }
                    $senderStock->save();
                }

                // Update receiver outlet stock
                $receiverStock = Stock::where('location_id', $request->outlet_id)
                    ->where('product_id', $product['product_id'])
                    ->lockForUpdate()
                    ->first();

                if ($receiverStock) {
                    $receiverStock->product_quantity += $product['quantity'];
                    $receiverStock->save();
                } else {
                    Stock::create([
                        'location_id' => $request->outlet_id,
                        'product_id' => $product['product_id'],
                        'product_quantity' => $product['quantity'],
                        'type' => 'outlet',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('outlet.transferoutlets')->with('success', 'Stock transfer completed successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Stock transfer error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during stock transfer. Please try again.')->withInput();
        }
    }

    /**
     * Show transfer details
     */
    public function transferOutletsStocksDetails($id)
    {
        $transferStock = StockTransferRequest::with('outlet', 'items.product')->findOrFail($id);
        return view('web.outlet.pages.transfer-to-outlets-detail', compact('transferStock'));
    }

    // ============================================
    // CUSTOMER MANAGEMENT METHODS
    // ============================================

    /**
     * Store new customer
     */
    public function customerStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|numeric|digits:10|unique:customers,phone_number',
        ], [
            'name.required' => 'Customer name is required.',
            'address.required' => 'Customer address is required.',
            'phone_number.required' => 'Phone number is required.',
            'phone_number.numeric' => 'Phone number must contain only numbers.',
            'phone_number.digits' => 'Phone number must be exactly 10 digits.',
            'phone_number.unique' => 'This phone number is already registered.',
        ]);

        Customer::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'balance' => 0,
        ]);

        return redirect()->back()->withInput()
            ->with('success', 'Customer created successfully!');
    }

    /**
     * Display customer list
     */
    public function customerList(Request $request)
    {
        $query = Customer::orderBy('id', 'desc');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(10);

        return view('web.outlet.pages.customer-list', compact('customers'));
    }

    /**
     * Update customer
     */
    public function customerUpdate(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:customers,phone_number,' . $customer->id,
        ], [
            'name.required' => 'Customer name is required.',
            'address.required' => 'Customer address is required.',
            'phone_number.required' => 'Phone number is required.',
            'phone_number.unique' => 'This phone number is already registered.',
        ]);

        try {
            $customer->update([
                'name' => $request->name,
                'address' => $request->address,
                'phone_number' => $request->phone_number,
            ]);

            return redirect()->route('outlet.customerList')
                ->with('success', 'Customer updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating customer. Please try again.')
                ->with('edit_customer', true)
                ->with('edit_customer_id', $customer->id)
                ->with('edit_customer_name', $request->name)
                ->with('edit_customer_phone', $request->phone_number)
                ->withInput();
        }
    }

    /**
     * Delete customer
     */
    public function customerDestroy(Customer $customer)
    {
        try {
            $customer->delete();

            return redirect()->route('outlet.customerList')
                ->with('success', 'Customer deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting customer. Please try again.');
        }
    }

    // ============================================
    // SALES MANAGEMENT METHODS
    // ============================================

    /**
     * Display sales orders
     */
    public function salesOrders(Request $request)
    {
        $user = auth()->guard('outlet')->user();
        $outlets = $user->outlet;

        $query = Sale::with('customer')
            ->where('location_id', $outlets->id)
            ->latest();

        // Search filter
        if ($request->filled('search')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Date filter
        if ($request->filled('date')) {
            $filterDate = $request->date;
            $query->whereDate('created_at', $filterDate);
        }

        $sales = $query->paginate(10);

        // Get active supervisors for refund modal
        $supervisors = User::where('status', 1)
            ->where('role', 'supervisor')
            ->orderBy('name')
            ->get();

        return view('web.outlet.pages.sales-orders', compact('sales','supervisors'));
    }

    /**
     * Show create sales form
     */
    // public function createSales(Request $request)
    // {
    //     $user = auth()->guard('outlet')->user();
    //     $outletId = $user->outlet->id;

    //     $products = Product::where('status', 1)->get();
    //     $customers = Customer::all();

    //     return view('web.outlet.pages.create-sales', compact('products', 'customers'));
    // }


    public function createSales(Request $request)
    {
        $user = auth()->guard('outlet')->user();

        // Check if user exists and has an outlet
        if (!$user || !$user->outlet) {
            return redirect()->route('outlet.login')->with('error', 'Please login to continue.');
        }

        $outletId = $user->outlet->id;

        $products = Product::where('status', 1)->get();

        // Get customers with their location-specific balances for this outlet
        $customers = Customer::with(['locationBalances' => function ($query) use ($outletId) {
            $query->where('location_id', $outletId);
        }])->get()->map(function ($customer) {
            $locationBalance = $customer->locationBalances->first();

            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone_number' => $customer->phone_number,
                'balance' => $locationBalance ? $locationBalance->balance : 0,
                'credit_balance' => $locationBalance ? $locationBalance->credit_balance : 0,
            ];
        });

        return view('web.outlet.pages.create-sales', compact('products', 'customers'));
    }



    public function searchCustomer(Request $request)
    {
        $search = $request->input('search');

        if (empty($search)) {
            return response()->json([]);
        }

        $customers = Customer::where(function ($query) use ($search) {
            $query->where('phone_number', 'LIKE', '%' . $search . '%')
                ->orWhere('name', 'LIKE', '%' . $search . '%');
        })
            ->limit(10)
            ->get(['id', 'name', 'phone_number']);

        return response()->json($customers);
    }

    /**
     * Store new sale
     */
    public function storeSales(Request $request)
    {
        $user = auth()->guard('outlet')->user();
        $outlet = $user->outlet;
        $locationId = $outlet->id;

        $orderItems = json_decode($request->order_items, true);

        if (empty($orderItems)) {
            return back()->withErrors(['order_items' => 'No products selected'])->withInput();
        }

        try {
            DB::beginTransaction();

            if ($request->payment_method === 'Down Payment') {
                $customerBalance = CustomerLocationBalance::where('customer_id', $request->customer_id)
                    ->where('location_id', $locationId)
                    ->first();

                $availableBalance = $customerBalance ? $customerBalance->balance : 0;

                if ($request->total_amount > $availableBalance) {

                    $formattedAvailableBalance = number_format($availableBalance, 2);
                    $formattedSaleAmount = number_format($request->total_amount, 2);

                    return back()->withErrors([
                        'payment_method' => "Insufficient customer down payment balance at this location. Available: ₹{$formattedAvailableBalance}, Sale Amount: ₹{$formattedSaleAmount}"
                    ])->withInput();
                }
            }

            $stockIssues = [];

            foreach ($orderItems as $item) {
                $productId = $item['id'];
                $quantitySold = $item['quantity'];

                $product = Product::find($productId);
                $productName = $product?->name ?? "Unknown Product (ID: {$productId})";

                $stock = Stock::where('product_id', $productId)
                    ->where('location_id', $locationId)
                    ->first();

                if (!$stock || $stock->product_quantity < $quantitySold) {
                    $availableQty = $stock?->product_quantity ?? 0;

                    $stockIssues[] = [
                        'product_id' => $productId,
                        'product_name' => $productName,
                        'requested' => $quantitySold,
                        'available' => $availableQty,
                        'error_type' => !$stock ? 'not_found' : 'insufficient'
                    ];
                }
            }

            $saleStatus = empty($stockIssues) ? 'completed' : 'pending';

            $salesType = empty($stockIssues) ? 'available' : 'unavailable';

            $sale = Sale::create([
                'customer_id' => $request->customer_id,
                'location_id' => $locationId,
                'payment_method' => $request->payment_method,
                'remark' => $request->remark,
                'total_amount' => $request->total_amount,
                'status' => $saleStatus,
                'type' => $salesType
            ]);

            foreach ($orderItems as $item) {
                $productId = $item['id'];
                $quantitySold = $item['quantity'];

                SoldProduct::create([
                    'sale_id' => $sale->id,
                    'product_id' => $productId,
                    'location_id' => $locationId,
                    'customer_id' => $request->customer_id,
                    'per_unit_amount' => $item['price'],
                    'quantity' => $quantitySold,
                    'total_product_amount' => $item['price'] * $quantitySold,
                ]);
            }
            if (empty($stockIssues)) {
                foreach ($orderItems as $item) {
                    $productId = $item['id'];
                    $quantitySold = $item['quantity'];

                    $stock = Stock::where('product_id', $productId)
                        ->where('location_id', $locationId)
                        ->first();

                    $stock->product_quantity -= $quantitySold;
                    $stock->save();
                }
            }

            // if ($request->payment_method === 'Cash') {
            //     $outlet->increment('balance', $request->total_amount);
            // }

            // if ($request->payment_method === 'Down Payment') {
            //     $customer = Customer::find($request->customer_id);
            //     $customer->decrement('balance', $request->total_amount);
            // }

            // if ($request->payment_method === 'Credit') {
            //     $customer = Customer::find($request->customer_id);
            //     $customer->increment('credit_balance', $request->total_amount);
            // }

            // ... (stock decrement logic)

            // if (empty($stockIssues)) {
            //     // This block runs ONLY for 'completed' sales where stock was available and updated.

            //     // Payment Logic - Move this section here
            //     if ($request->payment_method === 'Cash') {
            //         $outlet->increment('balance', $request->total_amount);
            //     }

            //     if ($request->payment_method === 'Down Payment') {
            //         // You might want to update the specific CustomerLocationBalance here
            //         // instead of the general Customer balance if that's the source of truth,
            //         // but sticking to your provided logic for now:
            //         $customer = Customer::find($request->customer_id);
            //         $customer->decrement('balance', $request->total_amount);
            //     }

            //     if ($request->payment_method === 'Credit') {
            //         $customer = Customer::find($request->customer_id);
            //         $customer->increment('credit_balance', $request->total_amount);
            //     }
            // }


            if (empty($stockIssues)) {
                // This block runs ONLY for 'completed' sales where stock was available and updated.

                // Payment Logic
                switch ($request->payment_method) {
                    case 'Cash':
                        // ✅ Add total amount to outlet balance
                        $outlet->increment('balance', $request->total_amount);
                        break;

                    case 'Down Payment':
                        // ✅ Reduce customer balance for this specific outlet location
                        CustomerLocationBalance::updateOrCreate(
                            [
                                'customer_id' => $request->customer_id,
                                'location_id' => $outlet->id,
                            ],
                            [
                                'balance' => DB::raw("balance - {$request->total_amount}")
                            ]
                        );
                        break;

                    case 'Credit':
                        // ✅ Increase customer's credit balance for this specific outlet location
                        CustomerLocationBalance::updateOrCreate(
                            [
                                'customer_id' => $request->customer_id,
                                'location_id' => $outlet->id,
                            ],
                            [
                                'credit_balance' => DB::raw("credit_balance + {$request->total_amount}")
                            ]
                        );
                        break;
                }
            }


            DB::commit();

            if (!empty($stockIssues)) {
                $warehouses = Location::where('type', 'warehouse')
                    ->where('id', '!=', $locationId)
                    ->get(['id', 'name']);

                return back()
                    ->withInput()
                    ->with('stock_issues', $stockIssues)
                    ->with('warehouses', $warehouses)
                    ->with('show_stock_modal', true)
                    ->with('sale_id', $sale->id);
            }

            return redirect()->route('outlet.salesOrders')
                ->with('success', 'Sale created successfully and stock updated!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale creation failed: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors([
                'system_error' => 'A system error occurred while processing the sale.'
            ])->withInput();
        }
    }

    /**
     * Display sales list
     */
    public function salesList(Request $request)
    {
        $query = Sale::orderBy('id', 'desc');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_method', 'like', "%{$search}%")
                    ->orWhere('total_amount', 'like', "%{$search}%");
            });
        }

        $sales = $query->paginate(10);

        return view('web.outlet.pages.sales-list', compact('sales'));
    }

    /**
     * Show sale details
     */
    public function salesDetails($id)
    {
        $sale = Sale::with(['customer', 'soldProducts.product'])->findOrFail($id);
        return view('web.outlet.pages.sales-details', compact('sale'));
    }

    /**
     * Generate sales invoice
     */
    public function salesInvoice($id)
    {
        $sale = Sale::with(['customer', 'soldProducts.product'])->findOrFail($id);
        return view('web.outlet.pages.sales-invoice', compact('sale'));
    }

    // ============================================
    // STOCK REQUEST METHODS
    // ============================================

    /**
     * Store stock request
     */
    // public function storeStockRequest(Request $request)
    // {
    //     $user = auth()->guard('outlet')->user();
    //     $outlets = $user->outlet;

    //     try {
    //         DB::beginTransaction();

    //         // Create main stock transfer request
    //         $transfer = StockTransferRequest::create([
    //             'supplier_name' => $request->sale_id,
    //             'supplier_id' => $outlets->id,
    //             'receiver_id' => $request->warehouse_id,
    //             'type' => 'stock-request',
    //             'status' => 'pending',
    //             'transfer_type' => 'stock-request'
    //         ]);

    //         // Create product entries
    //         foreach ($request->transfer_requests as $item) {
    //             StockTransferRequestsProduct::create([
    //                 'transfer_request_id' => $transfer->id,
    //                 'product_id' => $item['product_id'],
    //                 'set_quantity' => $item['quantity'],
    //                 'received_quantity' => 0,
    //                 'type' => 'stock-request',
    //             ]);
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Stock transfer request created successfully.'
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to create stock transfer request.'
    //         ], 500);
    //     }
    // }


    public function storeStockRequest(Request $request)
    {
        $user = auth()->guard('outlet')->user();
        $outlets = $user->outlet;

        try {
            DB::beginTransaction();

            // Create main stock transfer request
            $transfer = StockTransferRequest::create([
                'supplier_name' => $request->sale_id, // Note: This field name 'supplier_name' is being used for 'sale_id'
                'supplier_id' => $outlets->id, // The requesting outlet's ID
                'receiver_id' => $request->warehouse_id, // The receiving warehouse's ID
                'type' => 'stock-request',
                'status' => 'pending',
                'transfer_type' => 'stock-request',
                'collect_all' => $request->boolean('collect_all'),
            ]);

            // dd($transfer);

            // Create product entries (existing logic)
            foreach ($request->transfer_requests as $item) {
                StockTransferRequestsProduct::create([
                    'transfer_request_id' => $transfer->id,
                    'product_id' => $item['product_id'],
                    'set_quantity' => $item['quantity'],
                    'received_quantity' => $item['available'] ?? 0,
                    'type' => 'stock-request',
                ]);
            }

            $warehouseReceiver = Location::where('id', $request->warehouse_id)
                ->where('type', 'warehouse')
                ->first();

            if ($warehouseReceiver) {
                $warehouseReceiver->notify(new StockRequestSendNotification($transfer));
            } else {
                Log::warning('Warehouse receiver not found for notification.', ['warehouse_id' => $request->warehouse_id]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock transfer request created successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create stock transfer request. Error: ' . $e->getMessage() // Include error for debugging
            ], 500);
        }
    }


    // public function outlet_levels(Request $request)
    // {
    //     $query = Stock::with(['product', 'location'])
    //         ->whereHas('location', function ($q) {
    //             $q->where('type', 'outlet');
    //         });

    //     // Filter by outlet
    //     if ($request->filled('outlet_id')) {
    //         $query->where('location_id', $request->outlet_id);
    //     }

    //     $stocks = $query->orderBy('id', 'desc')->paginate(10);
    //     $outlets = Location::where('type', 'outlet')->get();

    //     // Return JSON for AJAX requests
    //     if ($request->ajax()) {
    //         // Render table rows HTML
    //         $tableHtml = view('web.outlet.outlet_levels.partials.stocks_table', compact('stocks'))->render();

    //         // Render pagination HTML only if there are multiple pages
    //         $paginationHtml = $stocks->hasPages()
    //             ? $stocks->appends(['outlet_id' => $request->outlet_id])->links()->render()
    //             : '';

    //         return response()->json([
    //             'tableHtml' => $tableHtml,
    //             'paginationHtml' => $paginationHtml,
    //             'hasPages' => $stocks->hasPages()
    //         ]);
    //     }

    //     return view('web.outlet.outlet_levels.index', compact('stocks', 'outlets'));
    // }


    public function outlet_levels(Request $request)
    {
        // Base query to filter stocks only for 'outlet' locations
        $query = Stock::query()
            ->whereHas('location', function ($q) {
                $q->where('type', 'outlet');
            });

        // Check if a specific outlet is selected
        if ($request->filled('outlet_id')) {
            // Filter by the specific outlet
            $query->where('location_id', $request->outlet_id)
                ->with(['product', 'location']) // Eager load for single location view
                ->orderBy('id', 'desc');

            // Apply pagination
            $stocks = $query->paginate(10);
        } else {
            // **LOGIC FOR "All Outlets" (Total Sum)**

            // Select the product_id and calculate the sum of product_quantity
            $query->select('product_id', DB::raw('SUM(product_quantity) as product_quantity'))
                ->with('product') // Eager load product details
                ->groupBy('product_id')
                ->join('products', 'stocks.product_id', '=', 'products.id')
                ->selectRaw('products.sku, products.name') // Select product details
                ->orderBy('products.name', 'asc'); // Order by product name

            // Apply pagination
            $stocks = $query->paginate(10);
        }

        // Get all outlets for the filter dropdown
        $outlets = Location::where('type', 'outlet')->get();

        // Return JSON for AJAX requests
        // if ($request->ajax()) {
        //     // Render table rows HTML
        //     $tableHtml = view('web.outlet.outlet_levels.partials.stocks_table', compact('stocks'))->render();

        //     // Render pagination HTML only if there are multiple pages
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
            $tableHtml = view('web.outlet.outlet_levels.partials.stocks_table', compact('stocks'))->render();

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

        return view('web.outlet.outlet_levels.index', compact('stocks', 'outlets'));
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
        //     $tableHtml = view('web.outlet.warehouse_levels.partials.stocks_table', compact('stocks'))->render();

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
        $tableHtml = view('web.outlet.warehouse_levels.partials.stocks_table', compact('stocks'))->render();

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
        return view('web.outlet.warehouse_levels.index', compact('stocks', 'warehouses'));
    }

    /**
     * Display list of stock requests to receive from outlet
     */
    public function outletRequest(Request $request)
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

        $query = StockTransferRequest::with([
            'outlet',
            'items.product'
        ])
            ->where('supplier_id', $userOutlet->id ?? 0)
            ->where('type', 'outlet_request')
            ->whereIn('status', ['created', 'accepted']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('outlet', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%");
                })
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $stocks = $query->latest()->paginate(10);

        return view('web.outlet.pages.outlet-request', compact('stocks'));
    }

    public function showOutletRequest($id)
    {
        $stock = StockTransferRequest::with(['outlet', 'items.product'])->findOrFail($id);
        return view('web.outlet.pages.outlet-request-stock-detail', compact('stock'));
    }


    // public function outlet_request_status_accept($id)
    // {
    //     $stock = StockTransferRequest::findOrFail($id);
    //     $stock->status = 'accepted';
    //     $stock->save();
    //     return redirect()->back()->with('success', 'Request accepted successfully.');
    // }

    public function outlet_request_status_accept($id)
    {
        try {
            DB::beginTransaction();

            $transferRequest = StockTransferRequest::with('items.product')->findOrFail($id);

            // Check if already accepted
            if ($transferRequest->status === 'accepted') {
                DB::rollBack();
                return redirect()->back()->with('error', 'Request already accepted.');
            }

            // Update request status
            $transferRequest->status = 'accepted';
            $transferRequest->save();

            // Process each item
            foreach ($transferRequest->items as $item) {
                $productId = $item->product_id;
                $quantity = $item->quantity;

                Log::info('Processing transfer item', [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'sender_location' => $transferRequest->supplier_id,
                    'receiver_location' => $transferRequest->receiver_id
                ]);

                // Deduct stock from sender outlet (supplier_id)
                $senderStock = Stock::where('location_id', $transferRequest->supplier_id)
                    ->where('product_id', $productId)
                    ->where('type', 'outlet')
                    ->first();



                if ($senderStock) {
                    Log::info('Sender stock before deduction', [
                        'stock_id' => $senderStock->id,
                        'current_quantity' => $senderStock->product_quantity
                    ]);

                    $senderStock->product_quantity -= $quantity;
                    $senderStock->save();

                    Log::info('Sender stock after deduction', [
                        'stock_id' => $senderStock->id,
                        'new_quantity' => $senderStock->product_quantity
                    ]);
                } else {
                    Log::warning('Sender stock not found', [
                        'location_id' => $transferRequest->supplier_id,
                        'product_id' => $productId
                    ]);
                }

                // Add stock to receiver outlet (receiver_id)
                $receiverStock = Stock::where('location_id', $transferRequest->receiver_id)
                    ->where('product_id', $productId)
                    ->where('type', 'outlet')
                    ->first();

                if ($receiverStock) {
                    Log::info('Receiver stock before addition', [
                        'stock_id' => $receiverStock->id,
                        'current_quantity' => $receiverStock->product_quantity
                    ]);

                    $receiverStock->product_quantity += $quantity;
                    $receiverStock->save();

                    Log::info('Receiver stock after addition', [
                        'stock_id' => $receiverStock->id,
                        'new_quantity' => $receiverStock->product_quantity
                    ]);
                } else {
                    Log::info('Creating new receiver stock entry');

                    Stock::create([
                        'location_id' => $transferRequest->receiver_id,
                        'product_id' => $productId,
                        'product_quantity' => $quantity,
                        'type' => 'outlet',
                    ]);

                    Log::info('New receiver stock created');
                }
            }

            DB::commit();

            Log::info('Transfer request accepted successfully', ['transfer_id' => $id]);

            return redirect()->back()->with('success', 'Request accepted and stock transferred successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Transfer acceptance error', [
                'transfer_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function outlet_request_status_reject($id)
    {
        $stock = StockTransferRequest::findOrFail($id);
        $stock->status = 'rejected';
        $stock->save();
        return redirect()->back()->with('success', 'Request reject successfully.');
    }


    public function productManagementList(Request $request)
    {
        $query = Product::orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(10);

        return view('web.outlet.pages.product-management', compact('products'));
    }


    public function productUpdatePrice(Request $request, Product $product)
    {
        $request->validate([
            'outlet_price' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($product) {
                    if ($value < $product->min_price) {
                        $fail("The outlet price must be at least ₹{$product->min_price}.");
                    }
                    if ($value > $product->max_price) {
                        $fail("The outlet price cannot exceed ₹{$product->max_price}.");
                    }
                },
            ],
        ]);

        try {
            $product->update([
                'outlet_price' => $request->outlet_price
            ]);

            return redirect()->route('outlet.productManagementList')
                ->with('success', 'Outlet price updated successfully for ' . $product->name);
        } catch (\Exception $e) {
            return redirect()->route('outlet.productManagementList')
                ->with('error', 'Failed to update outlet price. Please try again.')
                ->withInput()
                ->with([
                    'edit_product' => true,
                    'edit_product_id' => $product->id,
                    'edit_product_name' => $product->name,
                    'edit_product_sku' => $product->sku,
                    'edit_product_min_price' => $product->min_price,
                    'edit_product_max_price' => $product->max_price,
                    'edit_product_price' => $request->outlet_price
                ]);
        }
    }

    public function generateWaybill(Request $request, $saleId)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'waybill_number' => 'required|string|max:255|unique:waybills,waybill_number',
            'loading_date' => 'required|date',
            'estimated_delivery_date' => 'required|date|after_or_equal:loading_date',
            // 'outlet_id' => 'required|exists:locations,id',
            'loader_name' => 'required|string|max:255',
            'loader_position' => 'required|string|max:255',
            'number_of_packages' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1',
            'receiver_name' => 'required|string|max:255',
            'receiver_position' => 'required|string|max:255',
            'shipping_remarks' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Get the authenticated warehouse user
            $user = auth()->guard('outlet')->user();
            $outlet = $user->outlet;

            // Fetch the sale
            $sale = Sale::where('id', $saleId)
                ->where('location_id', $outlet->id)
                ->firstOrFail();

            // Check if waybill already exists for this sale
            $existingWaybill = Waybill::where('sale_id', $sale->id)->first();
            if ($existingWaybill) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->withErrors(['waybill_error' => 'Waybill already exists for this sale. Waybill Number: ' . $existingWaybill->waybill_number])
                    ->withInput();
            }

            // Create waybill record in database
            $waybill = Waybill::create([
                'sale_id' => $sale->id,
                'location_id' => $outlet->id,
                'waybill_number' => $validated['waybill_number'],
                'loading_date' => $validated['loading_date'],
                'estimated_delivery_date' => $validated['estimated_delivery_date'],
                'warehouse_name' => $outlet->name,
                'outlet_id' => $outlet->id,
                'loader_name' => $validated['loader_name'],
                'loader_position' => $validated['loader_position'],
                'number_of_packages' => $validated['number_of_packages'],
                'quantity' => $validated['quantity'],
                'receiver_name' => $validated['receiver_name'],
                'receiver_position' => $validated['receiver_position'],
                'shipping_remarks' => $validated['shipping_remarks'],
                'status' => 'delivered',
            ]);

            DB::commit();

            // Redirect with success message
            return redirect()
                ->route('outlet.salesOrders')
                ->with('success', 'Waybill generated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Waybill generation failed: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return back()->withErrors([
                'waybill_error' => 'Failed to generate waybill invoice. Error: ' . $e->getMessage()
            ])->withInput();
        }
    }


    public function viewWaybillInvoice($id)
    {
        $waybill = Waybill::with(['sale.customer', 'sale.soldProducts.product', 'outlet'])
            ->findOrFail($id);

        return view('web.outlet.pages.way-bill', compact('waybill'));
    }

    public function getSaleDetails($id)
    {
        $sale = Sale::with(['customer', 'soldProducts.product'])->findOrFail($id);

        return response()->json([
            'total_amount' => $sale->total_amount,
            'customer_name' => $sale->customer->name,
            'order_id' => '#' . $sale->id
        ]);
    }

    public function processRefund(Request $request, $id)
    {
        try {
            $request->validate([
                'refund_amount' => 'required|numeric|min:0.01',
                'refund_reason' => 'required|string'
            ]);

            $sale = Sale::with(['customer', 'location'])->findOrFail($id);

            // Check if refund amount is valid
            if ($request->refund_amount > $sale->total_amount) {
                return back()->with('error', 'Refund amount cannot exceed sale amount');
            }

            // Check if sale is already refunded
            $existingRefund = Refund::where('sale_id', $sale->id)->first();
            if ($existingRefund) {
                return back()->with('error', 'This sale has already been refunded');
            }

            // Create refund record
            $refund = Refund::create([
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'location_id' => $sale->location_id,
                'refund_amount' => $request->refund_amount,
                'refund_reason' => $request->refund_reason,
                'supervisor_id' => $request->supervisor_id,
                'status' => 'created'
            ]);

            $sale->update(['refund_status' => 'pending']);
            return back()->with('success', 'Refund processed successfully. Amount: $' . number_format($request->refund_amount, 2));
        } catch (\Exception $e) {
            Log::error('Refund Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to process refund. Please try again.');
        }
    }
}
