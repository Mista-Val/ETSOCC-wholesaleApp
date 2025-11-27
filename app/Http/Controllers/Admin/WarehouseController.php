<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Models\ExternalCashInflow;
use App\Models\ExternalCashOutflow;
use App\Models\Location;
use App\Models\Product;
use App\Models\RecordExpense;
use App\Models\Refund;
use App\Models\Stock;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class WarehouseController extends Controller
{
    // Show list of warehouses
    public function index()
    {
        // $warehouses = Warehouse::with('manager')->orderBy('created_at', 'desc')->paginate(10);
        // return view('admin.warehouses.index', compact('warehouses'));
        return view('admin.warehouses.index');
    }


    // Show form to create warehouse
    public function create()
    {
        $warehouseManagers = User::where('role', 'warehouse-manager')->get();
        return view('admin.warehouses.create', compact('warehouseManagers'));
    }

    // Store new warehouse
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'user_id' => 'required|exists:users,id',
        ], [
            'user_id.required' => 'Warehouse manager is required,please select',
            'name.unique' => 'This warehouse name already exists.',
        ]);


        $warehouse = Location::create([
            'name' => $request->name,
            'address' => $request->address,
            'description' => $request->description,
            'status' => $request->status,
            'type' => 'warehouse',
            'user_id' => $request->user_id,
        ]);
        $products = Product::where('status', 1)->get();

        foreach ($products as $product) {
            Stock::create([
                'location_id' => $warehouse->id,
                'product_id' => $product->id,
                'product_quantity' => 0,
                'type' => 'warehouse',
            ]);
        }

        $user = User::find($request->user_id);
        $warehouse_username = $user->name;
        $email = $user->email;
        $mailData = attachEmailTemplate('add-warehouse', [
            'name' => ucfirst($warehouse_username),
            'warehousename' => ucfirst($request->name),
            'email' => $email,
        ]);

        sendEmail($mailData['body'], $mailData['subject'], $email);

        return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse created successfully.');
    }

    // Show form to edit warehouse
    public function edit($id)
    {
        $warehouse = Location::findOrFail($id);
        $warehouseManagers = User::where('role', 'warehouse-manager')->get();

        return view('admin.warehouses.edit', compact('warehouse', 'warehouseManagers'));
    }

    // Update warehouse
    public function update(Request $request, $id)
    {
        $request->validate([
            // 'name' => 'required|string|max:255',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('locations', 'name')->ignore($id),
            ],
            'address' => 'required|string|max:500',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'user_id' => 'required|exists:users,id',
        ], [
            'user_id.required' => 'Warehouse manager is required',
            'name.unique' => 'This warehouse name already exists.',
        ]);

        $warehouse = Location::findOrFail($id);
        $warehouse->update([
            'name' => $request->name,
            'address' => $request->address,
            'description' => $request->description,
            'status' => $request->status,
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse updated successfully.');
    }

    // Delete warehouse

    public function show($id)
    {
        // $warehouse = Location::with(['user', 'stocks.product'])->findOrFail($id);
        $warehouse = Location::with(['user'])->findOrFail($id);

        return view('admin.warehouses.show', compact('warehouse'));
    }

    public function indexExternalCashInflow()
    {
        $externalCashInflows = ExternalCashInflow::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.external-cash-inflow.index', compact('externalCashInflows'));
    }

    // Show create form
    public function createExternalCashInflow()
    {
        $supervisors = User::where('role', 'supervisor')->pluck('name', 'id');
        return view('admin.external-cash-inflow.create', compact('supervisors'));
    }

    // Store new inflow
    public function storeExternalCashInflow(Request $request)
    {
        $request->validate([
            'source'        => 'required|string|max:100',
            'amount'        => 'required|numeric|min:0.01',
            'received_date' => 'required|date',
            'received_from' => 'required|string|max:100',
            'remarks'       => 'nullable|string|max:500',
            'supervisor_id'   => 'required|exists:users,id',
        ]);

        ExternalCashInflow::create($request->only([
            'source',
            'amount',
            'received_date',
            'received_from',
            'remarks',
            'supervisor_id'
        ]));

        $supervisor = \App\Models\User::find($request->supervisor_id);

        if ($supervisor) {
            $supervisor->increment('balance', $request->amount);
        }

        return redirect()->route('admin.external-cash-inflow.index')
            ->with('success', 'External Cash Inflow recorded successfully!');
    }

    // Show edit form
    public function editExternalCashInflow($id)
    {
        $data = ExternalCashInflow::findOrFail($id);

        // Fetch supervisors to populate the dropdown
        $supervisors = User::where('role', 'supervisor')->pluck('name', 'id');

        // Pass both the data and the supervisors to the view
        return view('admin.external-cash-inflow.edit', compact('data', 'supervisors'));
    }


    // Update existing inflow
    public function updateExternalCashInflow(Request $request, $id)
    {
        $request->validate([
            'source'          => 'required|string|max:100',
            'amount'          => 'required|numeric|min:0.01',
            'received_date'   => 'required|date',
            'received_from'   => 'required|string|max:100',
            'remarks'         => 'nullable|string|max:500',
            'supervisor_id'   => 'required|exists:users,id', // <-- ADDED: Validation
        ]);

        $data = ExternalCashInflow::findOrFail($id);
        $data->update($request->only([
            'source',
            'amount',
            'received_date',
            'received_from',
            'remarks',
            'supervisor_id' // <-- ADDED: Include the new field in the update
        ]));

        return redirect()->route('admin.external-cash-inflow.index')
            ->with('success', 'External Cash Inflow updated successfully.');
    }

    // Show details
    public function showExternalCashInflow($id)
    {
        $data = ExternalCashInflow::findOrFail($id);
        $supervisorBalance = $data->supervisor->balance ?? 0;
        return view('admin.external-cash-inflow.show', compact('data','supervisorBalance'));
    }

    // Delete inflow
    public function destroyExternalCashInflow($id)
    {
        $data = ExternalCashInflow::findOrFail($id);
        $data->delete();

        return response()->json(['success' => true, 'message' => 'External Cash Inflow deleted successfully.']);
    }

    public function indexRecordExpense()
    {
        $recordExpense = RecordExpense::with('location')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('admin.record-expense.index', compact('recordExpense'));
    }
    // public function acceptRecordExpense($id)
    // {
    //     $item = RecordExpense::findOrFail($id);

    //     if ($item->status === 'accepted') {
    //         return redirect()->back()->with('error', 'Record already accepted.');
    //     }

    //     $location = Location::findOrFail($item->location_id);


    //     DB::beginTransaction();
    //     try {
    //         $item->update([
    //             'status' => 'accepted',
    //         ]);

    //         $location->decrement('balance', $item->amount);

    //         DB::commit();

    //         return redirect()->back()->with('success', 'Record accepted successfully and balance updated.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         Log::error('Accept Record Expense Error: ' . $e->getMessage());

    //         return redirect()->back()->with('error', 'Failed to accept record. Please try again.');
    //     }
    // }
    // public function rejectRecordExpense($id)
    // {
    //     $item = RecordExpense::findOrFail($id);

    //     $item->update([
    //         'status' => 'rejected',
    //     ]);

    //     return redirect()->back()->with('success', 'Record rejected successfully.');
    // }

    public function acceptRecordExpense($id)
    {
        $item = RecordExpense::findOrFail($id);

        // Check if already accepted by admin
        if ($item->status === 'accepted' && $item->approval_status === 'accepted_by_admin') {
            return redirect()->back()->with('error', 'Record already accepted by admin.');
        }

        // Admin can only accept if supervisor has accepted
        if ($item->approval_status !== 'accepted_by_supervisor') {
            return redirect()->back()->with('error', 'This request must be accepted by supervisor first.');
        }

        $location = Location::findOrFail($item->location_id);

        DB::beginTransaction();
        try {
            $item->update([
                'status' => 'accepted',
                'approval_status' => 'accepted_by_admin'
            ]);

            // Deduct balance only when admin accepts
            $location->decrement('balance', $item->amount);

            DB::commit();

            return redirect()->back()->with('success', 'Record accepted successfully and balance updated.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Admin Accept Record Expense Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to accept record. Please try again.');
        }
    }

    public function rejectRecordExpense($id)
    {
        $item = RecordExpense::findOrFail($id);

        // Check if already rejected
        if ($item->status === 'rejected' && in_array($item->approval_status, ['rejected_by_admin', 'rejected_by_supervisor'])) {
            return redirect()->back()->with('error', 'Record already rejected.');
        }

        DB::beginTransaction();
        try {
            $item->update([
                'status' => 'rejected',
                'approval_status' => 'rejected_by_admin'
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Record rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Admin Reject Record Expense Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to reject record. Please try again.');
        }
    }

      public function indexCashRefund()
    {
        $cashRefund = Refund::with('location','customer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('admin.cash-refund.index', compact('cashRefund'));
    }

      public function indexExternalCashOutflow()
    {
        $externalCashOutFlow = ExternalCashOutflow::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.external-cash-outflow.index', compact('externalCashOutFlow'));
    }

    // Show details
    public function showExternalCashOutflow($id)
    {
        $data = ExternalCashOutflow::findOrFail($id);
        $supervisorBalance = $data->supervisor->balance ?? 0;
        return view('admin.external-cash-outflow.show', compact('data','supervisorBalance'));
    }
}
