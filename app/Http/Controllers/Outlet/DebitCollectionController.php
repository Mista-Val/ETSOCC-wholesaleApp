<?php

namespace App\Http\Controllers\Outlet;

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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DebitCollectionController extends Controller
{
    // ============================================
    // DEBT COLLECTION METHODS
    // ============================================

    /**
     * Display debt collections list
     */
    public function debtCollections(Request $request)
    {
        $userOutlet = $this->getUserOutlet();

        $query = DebitCollection::with('coustomer')
            ->where('location_id', $userOutlet->id)
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

        $datas = $query->orderBy('date', 'desc')->paginate(10);
        $datas->appends($request->all());

        return view('web.outlet.debit_collection.index', compact('datas'));
    }

    /**
     * Show create debt collection form
     */
    public function debtCollections_create()
    {
        $users = Customer::all();
        return view('web.outlet.debit_collection.create', compact('users'));
    }

    /**
     * Store new debt collection
     */
    // public function debtCollections_store(Request $request)
    // {
    //     $request->validate([
    //         'user_id' => 'required|string|max:255',
    //         'date' => 'required|date|before_or_equal:today',
    //         'amount' => 'required|numeric|min:1|max:99999999', // 8 digits max
    //         'payment_method' => 'required|string|in:cash,bank,upi,cheque',
    //         'remark' => 'required|string|max:255',
    //     ], [
    //         'user_id.required' => 'Please select a customer.',
    //         'user_id.string' => 'Invalid customer selection.',
    //         'user_id.max' => 'Customer name is too long.',
    //         'date.required' => 'Please select a date.',
    //         'date.date' => 'The date format is invalid.',
    //         'date.before_or_equal' => 'Collection date cannot be in the future.',
    //         'amount.required' => 'Please enter the amount received.',
    //         'amount.numeric' => 'Amount must be a valid number.',
    //         'amount.min' => 'Amount must be at least 1.',
    //         'amount.max' => 'Amount cannot exceed 8 digits (99,999,999).',
    //         'payment_method.required' => 'Please select a payment method.',
    //         'payment_method.string' => 'Payment method must be a valid string.',
    //         'payment_method.in' => 'Invalid payment method selected.',
    //         'remark.required' => 'Please enter a remark.',
    //         'remark.string' => 'Remark must be valid text.',
    //         'remark.max' => 'Remark is too long (max 255 characters).',
    //     ]);

    //     $userOutlet = $this->getUserOutlet();

    //     DebitCollection::create([
    //         'location_id' => $userOutlet->id,
    //         'customer_id' => $request->user_id,
    //         'type' => 'dept_collection',
    //         'date' => $request->date,
    //         'amount' => $request->amount,
    //         'payment_method' => $request->payment_method,
    //         'remarks' => $request->remark ?? null,
    //     ]);

    //     return redirect()->route('outlet.debtCollections')
    //         ->with('success', 'Debt collection created successfully.');
    // }


    //  public function debtCollections_store(Request $request)
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

    //         $userOutlet = $this->getUserOutlet();

      
    //         DB::beginTransaction();

    //         // Create debt collection record
    //          DebitCollection::create([
    //         'location_id' => $userOutlet->id,
    //         'customer_id' => $request->user_id,
    //         'type' => 'dept_collection',
    //         'date' => $request->date,
    //         'amount' => $request->amount,
    //         'payment_method' => $request->payment_method,
    //         'remarks' => $request->remark ?? null,
    //     ]);

    //         // Decrement customer credit balance
    //         $customer->decrement('credit_balance', $validated['amount']);

    //         // If payment method is cash, increment location cash balance
    //         if (strtolower($validated['payment_method']) === 'cash') {
    //             $userOutlet->increment('balance', $validated['amount']);
    //         }

    //         DB::commit();

    //         return redirect()->route('outlet.debtCollections')
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

        $userOutlet = $this->getUserOutlet();

        // Get or create customer location balance
        $customerLocationBalance = CustomerLocationBalance::firstOrCreate(
            [
                'customer_id' => $validated['user_id'],
                'location_id' => $userOutlet->id,
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
            'location_id'    => $userOutlet->id,
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
            $userOutlet->increment('balance', $validated['amount']);
        }

        DB::commit();

        return redirect()->route('outlet.debtCollections')
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

    // ============================================
    // RECORD EXPENSES METHODS
    // ============================================

    /**
     * Display record expenses list
     */
    public function recordExpenses(Request $request)
    {
        $userOutlet = $this->getUserOutlet();

        $query = RecordExpense::with('receiver')->where('location_id', $userOutlet->id);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('amount', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%");
            });
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $datas = $query->orderBy('id', 'desc')->paginate(10);
        $datas->appends($request->all());

        return view('web.outlet.record_expenses.index', compact('datas'));
    }

    /**
     * Show create record expense form
     */
    public function recordExpenses_create()
    {
        // $users = User::where('status', 1)
        //     ->where('role_id', 2)
        //     ->get();
        $users = User::where('status', 1)->where('role', 'supervisor')->get();
        return view('web.outlet.record_expenses.create', compact('users'));
    }

    /**
     * Store new record expense
     */
    public function recordExpenses_store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required',
            'amount' => 'required|numeric|min:1',
            'purpose' => 'required|string',
            'remark' => 'required|string|max:255',
        ], [
            'receiver_id.required'  => 'Please select the receiver name.',
            'amount.required' => 'Please enter the expense amount.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount must be at least 1.',
            'purpose.required' => 'Please enter the expense purpose.',
            'purpose.string' => 'Purpose must be valid text.',
            'remark.required' => 'Please enter a remark.',
            'remark.string' => 'Remark must be valid text.',
            'remark.max' => 'Remark is too long (max 255 characters).',
        ]);

        $userOutlet = $this->getUserOutlet();

        if ($request->amount > $userOutlet->balance) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['amount' => 'Expense amount cannot exceed available balance of ₹' . number_format($userOutlet->balance, 2)]);
        }

        RecordExpense::create([
            'location_id' => $userOutlet->id,
            'amount' => $request->amount,
            'purpose' => $request->purpose,
            'remarks' => $request->remark ?? null,
            'receiver_id' => $request->receiver_id,
        ]);

        return redirect()->route('outlet.recordExpenses')
            ->with('success', 'Record expense created successfully.');
    }

    // ============================================
    // CASH REMITTANCE METHODS
    // ============================================

    /**
     * Display cash remittance list
     */
    public function cashRemittance(Request $request)
    {
        $userOutlet = $this->getUserOutlet();

        $query = CashRemittance::with('coustomer')
            ->where('location_id', $userOutlet->id);

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

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $datas = $query->orderBy('id', 'desc')->paginate(10);
        $datas->appends($request->all());

        return view('web.outlet.cashRemittance.index', compact('datas'));
    }

    /**
     * Show create cash remittance form
     */
    public function cashRemittance_create()
    {
        $users = User::where('status', 1)
            ->whereIn('role', ['supervisor', 'admin'])
            ->get();

        return view('web.outlet.cashRemittance.create', compact('users'));
    }

    /**
     * Store new cash remittance
     */
    public function cashRemittance_store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required',
            'amount' => 'required|numeric|min:1',
            'role' => 'required|string',
            'remark' => 'required|string|max:255',
        ], [
            'receiver_id.required' => 'Please select the receiver name.',
            'amount.required' => 'Please enter the amount being remitted.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount must be at least 1.',
            'role.required' => 'Please select a role.',
            'role.string' => 'Role must be valid text.',
            'remark.required' => 'Please enter a remark.',
            'remark.string' => 'Remark must be valid text.',
            'remark.max' => 'Remark is too long (max 255 characters).',
        ]);

        $userOutlet = $this->getUserOutlet();

         if ($request->amount > $userOutlet->balance) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Insufficient balance. Your current balance is $' . number_format($userOutlet->balance, 2));
        }

        $cashRemittance = CashRemittance::create([
            'location_id' => $userOutlet->id,
            'receiver_id' => $request->receiver_id,
            'amount' => $request->amount,
            'role' => $request->role,
            'remarks' => $request->remark ?? null,
            'status' => 'pending',
        ]);

        $receiver = User::find($request->receiver_id,);
        //   dd($receiver->toArray());
        if ($receiver) {
            $receiver->notify(new SuperVisorNotification($cashRemittance, 'created'));
        }

        return redirect()->route('outlet.cashRemittance')
            ->with('success', 'Cash remittance created successfully.');
    }

    /**
     * View cash remittance details
     */
    public function cashRemittance_view($id)
    {
        $datas = CashRemittance::with('coustomer')
            ->where('id', $id)
            ->firstOrFail();

        return view('web.outlet.cashRemittance.view', compact('datas'));
    }

    // ============================================
    // DAILY SALES SUMMARY METHOD
    // ============================================

    /**
     * Display daily sales summary
     */
    public function daily_sales_summary()
    {
        $user = auth()->guard('outlet')->user();
        $outlet = $user->outlet;

        $totalCashSale = 0;
        $totalCredit = 0;
        $totalDownPayment = 0;

        if ($outlet) {
            $outletId = $outlet->id;

            $totalCashSale = Sale::where('location_id', $outletId)
                ->where('payment_method', 'Cash')
                ->sum('total_amount');

            $totalCredit = Sale::where('location_id', $outletId)
                ->where('payment_method', 'Credit')
                ->sum('total_amount');

            $totalDownPayment = Sale::where('location_id', $outletId)
                ->where('payment_method', 'Down Payment')
                ->sum('total_amount');

            $totalBankTransfer = Sale::where('location_id', $outletId)
                ->where('payment_method', 'Bank Transfer')
                ->sum('total_amount');
        }

        return view('web.outlet.cashRemittance.daily_sales_summary', compact(
            'totalCashSale',
            'totalCredit',
            'totalDownPayment',
            'totalBankTransfer'
        ));
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Get current user's outlet
     */
    private function getUserOutlet()
    {
        $loggedInUser = auth()->guard('outlet')->user();
        $userOutlet = null;

        if ($loggedInUser) {
            $userOutlet = Location::where('user_id', $loggedInUser->id)->first();
        }

        if (!$userOutlet) {
            $userOutlet = (object) ['id' => 0];
        }

        return $userOutlet;
    }
}
