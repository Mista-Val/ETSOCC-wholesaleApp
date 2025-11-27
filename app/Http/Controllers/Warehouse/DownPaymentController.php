<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerLocationBalance;
use App\Models\DownPayment;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DownPaymentController extends Controller
{
    /**
     * Display down payment list with totals
     */
    public function downPayment(Request $request)
    {
        $userWarehouse = $this->getUserWarehouse();

        $query = DownPayment::with('coustomer')
            ->where('location_id', $userWarehouse->id)
            ->where('type', 'down_payment');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('coustomer', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                })
                    ->orWhere('payment_method', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%");
            });
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        // Calculate totals
        $totalDownPayment = $query->sum('amount');

        $totalSalesDownPayment = DB::table('sales')
            ->where('location_id', $userWarehouse->id)
            ->where('payment_method', 'Down Payment')
            ->sum('total_amount');

        $datas = $query->orderBy('date', 'desc')->paginate(10);
        $datas->appends($request->all());

        return view('web.warehouse.down_payment.index', compact(
            'datas',
            'totalDownPayment',
            'totalSalesDownPayment'
        ));
    }

    /**
     * Show create down payment form
     */
    public function downPayment_create()
    {
        $users = Customer::all();
        return view('web.warehouse.down_payment.create', compact('users'));
    }


    public function downPayment_store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string|max:255',
            'date' => 'required|date|date_equals:today',
            'amount' => 'required|numeric|min:1|max:99999999',
            'payment_method' => 'required|string',
            'remark' => 'required|string|max:255',
        ], [
            'user_id.required' => 'Please select a customer.',
            'user_id.string' => 'Invalid customer selection.',
            'user_id.max' => 'Customer name is too long.',
            'date.required' => 'Please select a date.',
            'date.date' => 'The date format is invalid.',
            'date.date_equals' => 'The date must be today. Past or future dates are not allowed.',
            'amount.required' => 'Please enter the down payment amount.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount must be at least 1.',
            'amount.max' => 'Amount cannot exceed 8 digits (999,999,99).',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.string' => 'Payment method must be a valid string.',
            'remark.required' => 'Please enter a remark.',
            'remark.string' => 'Remark must be valid text.',
            'remark.max' => 'Remark is too long (max 255 characters).',
        ]);

        $userWarehouse = $this->getUserWarehouse();

        DownPayment::create([
            'location_id' => $userWarehouse->id,
            'customer_id' => $request->user_id,
            'type' => 'down_payment',
            'date' => $request->date,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'remarks' => $request->remark ?? null,
        ]);


        $customerId = $request->user_id;
        $locationId = $userWarehouse->id; 
        $amount = $request->amount;

         CustomerLocationBalance::updateOrCreate(
                [
                    'customer_id' => $customerId,
                    'location_id' => $locationId,
                ],
                [
                    'balance' => DB::raw("balance + $amount")
                ]
            );
        
    if ($request->payment_method == 'cash') {
        $userWarehouse->increment('balance', $request->amount);
    }

        return redirect()->route('warehouse.downPayment')
            ->with('success', 'Down payment created successfully and location-specific balance updated.');
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Get current user's outlet
     */
    private function getUserWarehouse()
    {
        $loggedInUser = auth()->guard('warehouse')->user();
        $userWarehouse = null;

        if ($loggedInUser) {
            $userWarehouse = Location::where('user_id', $loggedInUser->id)->first();
        }

        if (!$userWarehouse) {
            $userWarehouse = (object) ['id' => 0];
        }

        return $userWarehouse;
    }
}
