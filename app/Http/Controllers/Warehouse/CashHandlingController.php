<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\CashRemittance;
use App\Models\Customer;
use App\Models\CustomerLocationBalance;
use App\Models\DebitCollection;
use App\Models\Location;
use App\Models\RecordExpense;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\SuperVisorNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashHandlingController extends Controller
{
    /**
     * Get the warehouse for the currently authenticated user
     */
    private function getUserWarehouse()
    {
        $loggedInUser = auth()->guard('warehouse')->user();

        if (!$loggedInUser) {
            return (object) ['id' => 0];
        }

        $warehouse = Location::where('user_id', $loggedInUser->id)->first();

        return $warehouse ?? (object) ['id' => 0];
    }

    /**
     * Display debt collections listing
     */
    public function debtCollections(Request $request)
    {
        $userWarehouse = $this->getUserWarehouse();

        $query = DebitCollection::with('coustomer')
            ->where('location_id', $userWarehouse->id)
            ->where('type', 'dept_collection');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('coustomer', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                })
                    ->orWhere('payment_method', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%");
            });
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $datas = $query->orderBy('date', 'desc')->paginate(10)->appends($request->all());

        return view('web.warehouse.debit_collection.index', compact('datas'));
    }

    /**
     * Show form to create debt collection
     */
    public function debtCollections_create()
    {
        $users = Customer::all();
        return view('web.warehouse.debit_collection.create', compact('users'));
    }

    /**
     * Store debt collection
     */
    // public function debtCollections_store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'user_id'        => 'required|string|max:255',
    //         'date'           => 'required|date|before_or_equal:today', 
    //         'amount'         => 'required|numeric|min:1|max:99999999',
    //         'payment_method' => 'required|string',
    //         'remark'         => 'required|string|max:255',
    //     ], [
    //         'user_id.required'        => 'Please select a customer.',
    //         'user_id.string'          => 'Invalid customer selection.',
    //         'user_id.max'             => 'Customer name is too long.',
    //         'date.required'           => 'Please select a date.',
    //         'date.date'               => 'The date format is invalid.',
    //         'date.before_or_equal'    => 'Collection date cannot be in the future.',
    //         'amount.required'         => 'Please enter the amount received.',
    //         'amount.numeric'          => 'Amount must be a valid number.',
    //         'amount.min'              => 'Amount must be at least 1.',
    //         'amount.max' => 'Amount cannot exceed 8 digits (99,999,999).',
    //         'payment_method.required' => 'Please select a payment method.',
    //         'payment_method.string'   => 'Payment method must be a valid string.',
    //         'remark.string'           => 'Remark must be valid text.',
    //         'remark.max'              => 'Remark is too long (max 255 characters).',
    //     ]);

    //     $userWarehouse = $this->getUserWarehouse();

    //     DebitCollection::create([
    //         'location_id'    => $userWarehouse->id,
    //         'customer_id'    => $validated['user_id'],
    //         'type'           => 'dept_collection',
    //         'date'           => $validated['date'],
    //         'amount'         => $validated['amount'],
    //         'payment_method' => $validated['payment_method'],
    //         'remarks'        => $validated['remark'] ?? null,
    //     ]);

    //     return redirect()->route('warehouse.debtCollections')
    //         ->with('success', 'Debt collection created successfully.');
    // }



    // public function debtCollections_store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'user_id'        => 'required|string|max:255',
    //         'date'           => 'required|date|date_equals:today',
    //         'amount'         => 'required|numeric|min:1|max:99999999',
    //         'payment_method' => 'required|string',
    //         'remark'         => 'required|string|max:255',
    //     ], [
    //         'user_id.required'        => 'Please select a customer.',
    //         'user_id.string'          => 'Invalid customer selection.',
    //         'user_id.max'             => 'Customer name is too long.',
    //         'date.required'           => 'Please select a date.',
    //         'date.date'               => 'The date format is invalid.',
    //         'date.date_equals' =>         'The date must be today. Past or future dates are not allowed.',
    //         'amount.required'         => 'Please enter the amount received.',
    //         'amount.numeric'          => 'Amount must be a valid number.',
    //         'amount.min'              => 'Amount must be at least 1.',
    //         'amount.max' => 'Amount cannot exceed 8 digits (99,999,999).',
    //         'payment_method.required' => 'Please select a payment method.',
    //         'payment_method.string'   => 'Payment method must be a valid string.',
    //         'remark.string'           => 'Remark must be valid text.',
    //         'remark.max'              => 'Remark is too long (max 255 characters).',
    //     ]);

    //     try {
    //         // Get customer
    //         $customer = Customer::find($validated['user_id']);

    //         if (!$customer) {
    //             return redirect()->back()
    //                 ->withInput()
    //                 ->withErrors(['user_id' => 'Customer not found.']);
    //         }

    //         // Check if credit_balance field exists and validate
    //         if (!isset($customer->credit_balance)) {
    //             return redirect()->back()
    //                 ->withInput()
    //                 ->withErrors(['error' => 'Customer credit balance not available.']);
    //         }

    //         // Validate credit balance
    //         if ($validated['amount'] > $customer->credit_balance) {
    //             return redirect()->back()
    //                 ->withInput()
    //                 ->withErrors(['amount' => 'Collection amount cannot exceed customer credit balance of ₹' . number_format($customer->credit_balance, 2)]);
    //         }

    //         $userWarehouse = $this->getUserWarehouse();

    //         DB::beginTransaction();

    //         // Create debt collection record
    //         DebitCollection::create([
    //             'location_id'    => $userWarehouse->id,
    //             'customer_id'    => $validated['user_id'],
    //             'type'           => 'dept_collection',
    //             'date'           => $validated['date'],
    //             'amount'         => $validated['amount'],
    //             'payment_method' => $validated['payment_method'],
    //             'remarks'        => $validated['remark'] ?? null,
    //         ]);

    //         // Decrement customer credit balance
    //         $customer->decrement('credit_balance', $validated['amount']);

    //         // If payment method is cash, increment location cash balance
    //         if (strtolower($validated['payment_method']) === 'cash') {
    //             $userWarehouse->increment('balance', $validated['amount']);
    //         }

    //         DB::commit();

    //         return redirect()->route('warehouse.debtCollections')
    //             ->with('success', 'Debt collection created successfully.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         // Log the error for debugging
    //         Log::error('Debt Collection Error: ' . $e->getMessage());

    //         return redirect()->back()
    //             ->withInput()
    //             ->withErrors(['error' => 'Failed to create debt collection: ' . $e->getMessage()]);
    //     }
    // }


    public function debtCollections_store(Request $request)
{
    $validated = $request->validate([
        'user_id'        => 'required|string|max:255',
        'date'           => 'required|date|date_equals:today',
        'amount'         => 'required|numeric|min:1|max:99999999',
        'payment_method' => 'required|string',
        'remark'         => 'required|string|max:255',
    ], [
        'user_id.required'        => 'Please select a customer.',
        'user_id.string'          => 'Invalid customer selection.',
        'user_id.max'             => 'Customer name is too long.',
        'date.required'           => 'Please select a date.',
        'date.date'               => 'The date format is invalid.',
        'date.date_equals'        => 'The date must be today. Past or future dates are not allowed.',
        'amount.required'         => 'Please enter the amount received.',
        'amount.numeric'          => 'Amount must be a valid number.',
        'amount.min'              => 'Amount must be at least 1.',
        'amount.max'              => 'Amount cannot exceed 8 digits (99,999,999).',
        'payment_method.required' => 'Please select a payment method.',
        'payment_method.string'   => 'Payment method must be a valid string.',
        'remark.string'           => 'Remark must be valid text.',
        'remark.max'              => 'Remark is too long (max 255 characters).',
    ]);

    try {
        // Get customer
        $customer = Customer::find($validated['user_id']);

        if (!$customer) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['user_id' => 'Customer not found.']);
        }

        $userWarehouse = $this->getUserWarehouse();

        // Get or create customer location balance
        $customerLocationBalance = CustomerLocationBalance::firstOrCreate(
            [
                'customer_id' => $validated['user_id'],
                'location_id' => $userWarehouse->id,
            ],
            [
                'balance' => 0,
                'credit_balance' => 0,
            ]
        );

        // Validate credit balance for this location
        if ($validated['amount'] > $customerLocationBalance->credit_balance) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['amount' => 'Collection amount cannot exceed customer credit balance of ₹' . number_format($customerLocationBalance->credit_balance, 2) . ' at this location.']);
        }

        DB::beginTransaction();

        // Create debt collection record
        DebitCollection::create([
            'location_id'    => $userWarehouse->id,
            'customer_id'    => $validated['user_id'],
            'type'           => 'dept_collection',
            'date'           => $validated['date'],
            'amount'         => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'remarks'        => $validated['remark'] ?? null,
        ]);

        // Decrement customer credit balance for this location
        $customerLocationBalance->decrement('credit_balance', $validated['amount']);


        $customerLocationBalance->decrement('balance', $validated['amount']);

        // If payment method is cash, increment location cash balance
        if (strtolower($validated['payment_method']) === 'cash') {
            $userWarehouse->increment('balance', $validated['amount']);
        }

        DB::commit();

        return redirect()->route('warehouse.debtCollections')
            ->with('success', 'Debt collection created successfully.');
            
    } catch (\Exception $e) {
        DB::rollBack();

        // Log the error for debugging
        Log::error('Debt Collection Error: ' . $e->getMessage());

        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Failed to create debt collection: ' . $e->getMessage()]);
    }
}

    /**
     * Display record expenses listing
     */
    public function recordExpenses(Request $request)
    {
        $userWarehouse = $this->getUserWarehouse();

        $query = RecordExpense::where('location_id', $userWarehouse->id);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('amount', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%");
            });
        }

        $datas = $query->orderBy('id', 'desc')->paginate(10)->appends($request->all());

        return view('web.warehouse.record_expenses.index', compact('datas'));
    }

    /**
     * Show form to create record expense
     */
    public function recordExpenses_create()
    {
        // $users = User::where('status', 1)->whereIn('role', ['supervisor', 'admin'])->where('role_id', 2)->get();
        $users = User::where('status', 1)->where('role',  'supervisor')->get();
        return view('web.warehouse.record_expenses.create', compact('users'));
    }

    /**
     * Store record expense
     */
    // public function recordExpenses_store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'receiver_id' => 'required',
    //         'amount'  => 'required|numeric|min:1',
    //         'purpose' => 'required|string',
    //         'remark'  => 'required|string|max:255',
    //     ], [
    //         'receiver_id.required'  => 'Please select the receiver name.',
    //         'amount.required'  => 'Please enter the expense amount.',
    //         'amount.numeric'   => 'Amount must be a valid number.',
    //         'amount.min'       => 'Amount must be at least 1.',
    //         'purpose.required' => 'Purpose is required.',
    //         'remark.string'    => 'Remark must be valid text.',
    //         'remark.max'       => 'Remark is too long (max 255 characters).',
    //     ]);

    //     $userWarehouse = $this->getUserWarehouse();

    //     RecordExpense::create([
    //         'location_id' => $userWarehouse->id,
    //         'amount'      => $validated['amount'],
    //         'purpose'     => $validated['purpose'],
    //         'receiver_id'     => $validated['receiver_id'],
    //         'remarks'     => $validated['remark'] ?? null,
    //     ]);

    //     return redirect()->route('warehouse.recordExpenses')
    //         ->with('success', 'Record expenses created successfully.');
    // }

    public function recordExpenses_store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required',
            'amount'  => 'required|numeric|min:1',
            'purpose' => 'required|string',
            'remark'  => 'required|string|max:255',
        ], [
            'receiver_id.required'  => 'Please select the receiver name.',
            'amount.required'  => 'Please enter the expense amount.',
            'amount.numeric'   => 'Amount must be a valid number.',
            'amount.min'       => 'Amount must be at least 1.',
            'purpose.required' => 'Purpose is required.',
            'remark.string'    => 'Remark must be valid text.',
            'remark.max'       => 'Remark is too long (max 255 characters).',
        ]);

        $userWarehouse = $this->getUserWarehouse();

        // Check if warehouse has sufficient balance
        if ($validated['amount'] > $userWarehouse->balance) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['amount' => 'Expense amount cannot exceed available balance of ₹' . number_format($userWarehouse->balance, 2)]);
        }

        DB::beginTransaction();
        try {
            // Create expense record
            RecordExpense::create([
                'location_id' => $userWarehouse->id,
                'amount'      => $validated['amount'],
                'purpose'     => $validated['purpose'],
                'receiver_id' => $validated['receiver_id'],
                'remarks'     => $validated['remark'] ?? null,
            ]);

            // Decrement warehouse balance
            // $userWarehouse->decrement('balance', $validated['amount']);

            DB::commit();

            return redirect()->route('warehouse.recordExpenses')
                ->with('success', 'Record expenses created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Record Expense Error: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create expense record. Please try again.']);
        }
    }

    /**
     * Display cash remittance listing
     */
    public function cashRemittance(Request $request)
    {
        $userWarehouse = $this->getUserWarehouse();

        $query = CashRemittance::with('coustomer')
            ->where('location_id', $userWarehouse->id);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('coustomer', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                })
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%");
            });
        }

        $datas = $query->orderBy('id', 'desc')->paginate(10)->appends($request->all());

        return view('web.warehouse.cashRemittance.index', compact('datas'));
    }

    /**
     * Show form to create cash remittance
     */
    public function cashRemittance_create()
    {
        $users = User::where('status', 1)->whereIn('role', ['supervisor', 'admin'])->get();
        return view('web.warehouse.cashRemittance.create', compact('users'));
    }

    /**
     * Store cash remittance
     */
    public function cashRemittance_store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required',
            'amount'      => 'required|numeric|min:1',
            'role'        => 'required|string',
            'remark'      => 'required|string|max:255',
        ], [
            'receiver_id.required' => 'Please select the receiver name.',
            'amount.required'      => 'Please enter the amount being remitted.',
            'amount.numeric'       => 'Amount must be a valid number.',
            'amount.min'           => 'Amount must be at least 1.',
            'role.required'        => 'Role is required.',
            'remark.string'        => 'Remark must be valid text.',
            'remark.max'           => 'Remark is too long (max 255 characters).',
        ]);

        $userWarehouse = $this->getUserWarehouse();

        // CashRemittance::create([
        //     'location_id' => $userWarehouse->id,
        //     'receiver_id' => $validated['receiver_id'],
        //     'amount'      => $validated['amount'],
        //     'role'        => $validated['role'],
        //     'remarks'     => $validated['remark'] ?? null,
        //     'status'      => 'pending',
        // ]);

        if ($request->amount > $userWarehouse->balance) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Insufficient balance. Your current balance is $' . number_format($userWarehouse->balance, 2));
        }

        $cashRemittance = CashRemittance::create([
            'location_id' => $userWarehouse->id,
            'receiver_id' => $validated['receiver_id'],
            'amount'      => $validated['amount'],
            'role'        => $validated['role'],
            'remarks'     => $validated['remark'] ?? null,
            'status'      => 'pending',
        ]);

        // 🔔 Notify the supervisor (receiver)
        $receiver = User::find($validated['receiver_id']);
        if ($receiver) {
            $receiver->notify(new SuperVisorNotification($cashRemittance, 'created'));
        }

        return redirect()->route('warehouse.cashRemittance')
            ->with('success', 'Cash remittance created successfully.');
    }

    /**
     * View cash remittance details
     */
    public function cashRemittance_view($id)
    {
        $datas = CashRemittance::with('coustomer')->findOrFail($id);
        return view('web.warehouse.cashRemittance.view', compact('datas'));
    }

    /**
     * Display daily sales summary
     */
    public function daily_sales_summary()
    {
        $user = auth()->guard('warehouse')->user();
        $warehouse = $user->warehouse;

        $totalCashSale = 0;
        $totalCredit = 0;
        $totalDownPayment = 0;

        $startOfToday = Carbon::today();
        $endOfToday = Carbon::now();

        if ($warehouse) {
            $warehouseId = $warehouse->id;

            $totalCashSale = Sale::where('location_id', $warehouseId)
                ->whereBetween('created_at', [$startOfToday, $endOfToday])
                ->where('payment_method', 'Cash')
                ->sum('total_amount');

            $totalCredit = Sale::where('location_id', $warehouseId)
                ->whereBetween('created_at', [$startOfToday, $endOfToday])
                ->where('payment_method', 'Credit')
                ->sum('total_amount');

            $totalDownPayment = Sale::where('location_id', $warehouseId)
                ->whereBetween('created_at', [$startOfToday, $endOfToday])
                ->where('payment_method', 'Down Payment')
                ->sum('total_amount');


            $totalBankTransfer = Sale::where('location_id', $warehouseId)
                ->whereBetween('created_at', [$startOfToday, $endOfToday])
                ->where('payment_method', 'Bank Transfer')
                ->sum('total_amount');
        }

        return view('web.warehouse.cashRemittance.daily_sales_summary', compact(
            'totalCashSale',
            'totalCredit',
            'totalDownPayment',
            'totalBankTransfer'
        ));
    }
}
