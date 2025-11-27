<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\BankDeposit;
use App\Models\CashRemittance;
use App\Models\ExternalCashInflow;
use App\Models\ExternalCashOutflow;
use App\Models\ExternalTransaction;
use App\Models\FinalCashDestination;
use App\Models\Location;
use App\Models\RecordExpense;
use App\Models\Refund;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\StockTransferRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SupervisorController extends Controller
{
    public function indexDashboard()
    {

        $loggedInUser = auth()->guard('supervisor')->user();

        $startOfToday = Carbon::today();
        $endOfToday = Carbon::now();

        $totalRemittedAmount = CashRemittance::where('receiver_id', $loggedInUser->id)->where('status', 'accepted')
            ->sum('amount');

        $totalCashInHandAmount = User::where('id', $loggedInUser->id)
            ->sum('balance');

        $totalRecentCollection = CashRemittance::whereBetween('created_at', [$startOfToday, $endOfToday])
            ->where('receiver_id', $loggedInUser->id)
            ->sum('amount');

        $todaySalesAmount = Sale::whereBetween('created_at', [$startOfToday, $endOfToday])
            ->where('status', 'completed')
            ->sum('total_amount');

        $todayTotalDownPayment = Sale::where('payment_method', 'Down Payment')
            ->whereBetween('created_at', [$startOfToday, $endOfToday])
            ->sum('total_amount');

        $todayCashPayment =  Sale::where('payment_method', 'Cash')
            ->whereBetween('created_at', [$startOfToday, $endOfToday])
            ->where('status', 'completed')
            ->sum('total_amount');

        $todayCreditPayment =  Sale::where('payment_method', 'Credit')
            ->whereBetween('created_at', [$startOfToday, $endOfToday])
            ->where('status', 'completed')
            ->sum('total_amount');

        $todayOutstandingCreditPayment =  Sale::where('payment_method', 'Credit')
            ->where('status', 'completed')
            ->sum('total_amount');

        $invoiceCount = Sale::where('status', 'completed')
            ->count();

        $quantityColumn = 'stock_transfer_requests_products.set_quantity';

        // --- 1. Total Lifetime Received Stock (type = 'admin') ---
        $ReceivedStockSum = StockTransferRequest::receivedStock()
            // Join with the products table
            ->join('stock_transfer_requests_products', 'stock_transfer_requests.id', '=', 'stock_transfer_requests_products.transfer_request_id')
            // Sum the set_quantity column from the joined table
            ->sum($quantityColumn);


        // --- 2. Total Lifetime Transferred Stock (type = 'warehouse' or 'outlet', transfer_type = 'stock') ---
        $TransferredStockSum = StockTransferRequest::transferredStock()
            // Join with the products table
            ->join('stock_transfer_requests_products', 'stock_transfer_requests.id', '=', 'stock_transfer_requests_products.transfer_request_id')
            // Sum the set_quantity column from the joined table
            ->sum($quantityColumn);


        // --- 3. Total Lifetime Returned Stock (transfer_type = 'return') ---
        $ReturnedStockSum = StockTransferRequest::returnedStock()
            // Join with the products table
            ->join('stock_transfer_requests_products', 'stock_transfer_requests.id', '=', 'stock_transfer_requests_products.transfer_request_id')
            // Sum the set_quantity column from the joined table
            ->sum($quantityColumn);

        $totalExpenseAmount = RecordExpense::where('approval_status', 'accepted_by_admin')->sum('amount');

        $downPayments = Sale::where('payment_method', 'Down Payment')->where('status', 'completed')->sum('total_amount');

        return view('web.supervisor.pages.dashboard', compact(
            'todaySalesAmount',
            'todayTotalDownPayment',
            'todayCashPayment',
            'todayCreditPayment',
            'invoiceCount',
            'ReceivedStockSum',
            'TransferredStockSum',
            'ReturnedStockSum',
            'totalRemittedAmount',
            'totalExpenseAmount',
            'downPayments',
            'totalCashInHandAmount',
            'totalRecentCollection',
            'todayOutstandingCreditPayment'
        ));
    }

    public function logout(Request $request)
    {
        if (Auth::guard('supervisor')->check()) {
            Auth::guard('supervisor')->logout();
            $redirectRoute = 'index';
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($redirectRoute)->with('success', 'Logged out successfully.');
    }

    public function myAccount(Request $request)
    {
        if (Auth::guard('warehouse')->check()) {
            $user = Auth::guard('warehouse')->user();
            $role = 'warehouse-manager';
            $viewPath = 'web.warehouse.pages.my-account';
        } elseif (Auth::guard('outlet')->check()) {
            $user = Auth::guard('outlet')->user();
            $role = 'outlet-manager';
            $viewPath = 'web.outlet.pages.my-account';
        } elseif (Auth::guard('supervisor')->check()) {
            $user = Auth::guard('supervisor')->user();
            $role = 'supervisor';
            $viewPath = 'web.supervisor.pages.my-account';
        } else {
            return redirect()->route('index')->with('error', 'Please login first');
        }

        return view($viewPath, compact('user', 'role'));
    }

    public function updateAccount(Request $request)
    {
        if (Auth::guard('warehouse')->check()) {
            $user = Auth::guard('warehouse')->user();
        } elseif (Auth::guard('outlet')->check()) {
            $user = Auth::guard('outlet')->user();
        } elseif (Auth::guard('supervisor')->check()) {
            $user = Auth::guard('supervisor')->user();
        } else {
            return redirect()->route('index')->with('success', 'Something went wrong');
        }


        $request->validate([
            'fullname'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email,' . $user->id,
            'mobile'        => 'required|digits:10',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        ], [
            'profile_image.image' => 'The profile image must be an actual image file.',
            'profile_image.mimes' => 'The profile image must be a file of type: jpg, jpeg, png, svg.',
            'profile_image.max'   => 'The profile image must not be larger than 2MB.',
        ]);

        $fullName = trim($request->fullname);
        $nameParts = explode(' ', $fullName, 2);

        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        $user->name = $request->fullname;
        $user->first_name = ucfirst($firstName);
        $user->last_name  = ucfirst($lastName);
        $user->email  = $request->email;
        $user->mobile = $request->mobile;

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $uploadPath = public_path('web/images/');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            if (!empty($user->profile_image)) {
                $oldImagePath = public_path($user->profile_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image->move($uploadPath, $imageName);
            $user->profile_image = 'web/images/' . $imageName;
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function showChangePasswordForm()
    {
        if (Auth::guard('warehouse')->check()) {
            $user = Auth::guard('warehouse')->user();
            $role = 'warehouse-manager';
            $viewPath = 'web.warehouse.pages.change-password';
        } elseif (Auth::guard('outlet')->check()) {
            $user = Auth::guard('outlet')->user();
            $role = 'outlet-manager';
            $viewPath = 'web.outlet.pages.change-password';
        } elseif (Auth::guard('supervisor')->check()) {
            $user = Auth::guard('supervisor')->user();
            $role = 'supervisor';
            $viewPath = 'web.supervisor.pages.change-password';
        } else {
            return redirect()->route('index')->with('error', 'Unauthorized access.');
        }

        return view($viewPath, compact('user', 'role'));
    }

    public function updatePassword(Request $request)
    {
        if (Auth::guard('warehouse')->check()) {
            $user = Auth::guard('warehouse')->user();
        } elseif (Auth::guard('outlet')->check()) {
            $user = Auth::guard('outlet')->user();
        } elseif (Auth::guard('supervisor')->check()) {
            $user = Auth::guard('supervisor')->user();
        } else {
            return redirect()->route('index')->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'old_password' => ['required'],
            'new_password' => ['required', 'min:6', 'confirmed'],
        ], [
            'old_password.required' => 'Old password is required.',
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'New password must be at least 6 characters.',
            'new_password.confirmed' => 'New password and confirmation do not match.',
        ]);

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Old password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    // public function cashRemittance(Request $request)
    // {
    //     $loggedInUser = auth()->guard('supervisor')->user();

    //     $query = CashRemittance::with(['coustomer', 'location'])
    //         ->where('receiver_id', $loggedInUser->id);

    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->where(function ($q) use ($search) {
    //             $q->whereHas('coustomer', function ($q2) use ($search) {
    //                 $q2->where('name', 'like', "%{$search}%");
    //             })
    //                 ->orWhere('role', 'like', "%{$search}%")
    //                 ->orWhere('amount', 'like', "%{$search}%")
    //                 ->orWhere('remarks', 'like', "%{$search}%");
    //         });
    //     }

    //     $datas = $query->orderBy('id', 'desc')->paginate(10);
    //     $datas->appends($request->all());

    //     return view('web.supervisor.pages.cash-remittance', compact('datas'));
    // }

    public function cashRemittance(Request $request)
    {
        $loggedInUser = auth()->guard('supervisor')->user();

        // Get active tab (default: requests)
        $activeTab = $request->get('tab', 'requests');

        $query = CashRemittance::with(['coustomer', 'location'])
            ->where('receiver_id', $loggedInUser->id);

        // Filter by tab status
        if ($activeTab === 'requests') {
            $query->where('status', 'pending');
        } else {
            $query->whereIn('status', ['accepted', 'rejected']);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('coustomer', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%");
            });
        }

        $datas = $query->orderBy('id', 'desc')->paginate(10);

        // Append all request parameters including tab
        $datas->appends($request->all());

        return view('web.supervisor.pages.cash-remittance', compact('datas'));
    }

    // public function cash_remittance_status_accept($id)
    // {
    //     $cash = CashRemittance::findOrFail($id);
    //     $cash->status = 'accepted';
    //     $cash->save();
    //     return redirect()->back()->with('success', 'Request accepted successfully.');
    // }

    public function cash_remittance_status_accept($id)
    {

        $loggedInUser = auth()->guard('supervisor')->user();

        $cash = CashRemittance::findOrFail($id);

        if ($cash->status === 'accepted') {
            return redirect()->back()->with('info', 'This remittance is already accepted.');
        }

        $cash->status = 'accepted';
        $cash->save();

        $loggedInUser->balance += $cash->amount;
        $loggedInUser->save();

        $location = Location::findOrFail($cash->location_id);

        $location->decrement('balance', $cash->amount);

        ExternalCashInflow::create([
            'supervisor_id' => $loggedInUser->id,
            'source' => 'Cash from outlets/warehouse',
            'amount' => $cash->amount,
            'received_date' => now(),
            'received_from' => $cash->location->id,
            'remarks' => $cash->remarks
        ]);

        return redirect()->back()->with('success', 'Request accepted successfully.');
    }

    public function cash_remittance_status_reject($id)
    {
        $cash = CashRemittance::findOrFail($id);
        $cash->status = 'rejected';
        $cash->save();
        return redirect()->back()->with('success', 'Request reject successfully.');
    }

    public function bankDepositCreate(Request $request)
    {
        return view('web.supervisor.pages.bank-deposit-create');
    }


    public function bankDepositStore(Request $request)
    {
        $loggedInUser = auth()->guard('supervisor')->user();

        $request->validate([
            'amount' => 'required|numeric|min:1|max:99999999',
            'bank_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z\s]+$/'
            ],
            'deposit_date' => 'required|date',
            'depositor_name' => 'required|string|max:255',
            'reference_number' => 'required|string|max:255|unique:bank_deposits,reference_number',
            'remarks' => 'nullable|string|max:500',
        ], [
            'amount.required' => 'Please enter the deposit amount.',
            'amount.min' => 'Amount must be at least 1.',
            'amount.max' => 'Amount cannot exceed 8 digits (99,999,999).',
            'bank_name.required' => 'Please enter the bank name.',
            'bank_name.regex' => 'Bank name may only contain letters and spaces.',
            'reference_number.unique' => 'This reference number has already been used.',
        ]);

        if ($request->amount > $loggedInUser->balance) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Insufficient balance. Your current balance is $' . number_format($loggedInUser->balance, 2));
        }


        BankDeposit::create([
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'deposit_date' => $request->deposit_date,
            'depositor_name' => $request->depositor_name,
            'reference_number' => $request->reference_number,
            'remarks' => $request->remarks,
            'supervisor_id' => $loggedInUser->id
        ]);

        ExternalCashOutflow::create([
            'source' => 'Cash sent to bank',
            'amount' => $request->amount,
            'date' => $request->deposit_date,
            'send_to' => $request->bank_name,
            'remarks' => $request->remarks,
            'supervisor_id' => $loggedInUser->id
        ]);

        $loggedInUser->balance -= $request->amount;
        $loggedInUser->save();

        return redirect()->route('supervisor.bankDeposit')
            ->with('success', 'Bank deposit recorded successfully!');
    }


    public function bankDeposit(Request $request)
    {
        $loggedInUser = auth()->guard('supervisor')->user();

        $search = $request->input('search');
        $date = $request->input('date');

        $deposits = BankDeposit::query()
            ->where('supervisor_id', $loggedInUser->id)
            ->when($search, function ($query, $search) {
                $query->where('bank_name', 'like', "%{$search}%")
                    ->orWhere('depositor_name', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%");
            })
            ->when($date, function ($query, $date) {
                $query->whereDate('deposit_date', $date);
            })
            ->orderBy('deposit_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('web.supervisor.pages.bank-deposit', ['datas' => $deposits]);
    }

    public function cashInHand(Request $request)
    {
        return view('web.supervisor.pages.cash-in-hand');
    }

    public function externalResourceCreate(Request $request)
    {
        return view('web.supervisor.pages.external-resource-create');
    }


    public function externalResourceStore(Request $request)
    {
        $loggedInUser = auth()->guard('supervisor')->user();
        $request->validate([
            'recipient_name' => 'required|string|max:100',
            'purpose' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer',
            'remarks' => 'nullable|string|max:500',
        ], [
            'amount.required' => 'Please enter the amount.',
            'payment_method.in' => 'The selected payment method is invalid.',
        ]);

        ExternalTransaction::create([
            'recipient_name' => $request->recipient_name,
            'purpose' => $request->purpose,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'remarks' => $request->remarks,
            'supervisor_id' => $loggedInUser->id
        ]);

        return redirect()->route('supervisor.externalResource')
            ->with('success', 'External Transaction recorded successfully!');
    }



    public function externalResource(Request $request)
    {
        $loggedInUser = auth()->guard('supervisor')->user();
        $search = $request->input('search');
        $paymentMethod = $request->input('payment_method');

        $transactions = ExternalTransaction::query()
            ->where('supervisor_id', $loggedInUser->id)
            ->when($search, function ($query, $search) {
                $query->where('recipient_name', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%");
            })
            ->when($paymentMethod, function ($query, $paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('web.supervisor.pages.external-resource', [
            'datas' => $transactions,
            'search' => $search,
            'payment_method' => $paymentMethod,
        ]);
    }

    public function finalCashCreate(Request $request)
    {
        return view('web.supervisor.pages.final-cash-destination-create');
    }

    public function finalCashStore(Request $request)
    {
        $loggedInUser = auth()->guard('supervisor')->user();
        $request->validate([
            'final_destination' => 'required|string|max:100',
            'cash_handler_name' => 'required|string|max:100',
            'responsible_person' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string|max:500',
        ], [
            'amount.required' => 'Please enter the transaction amount.',
        ]);

        FinalCashDestination::create([
            'final_destination' => $request->final_destination,
            'cash_handler_name' => $request->cash_handler_name,
            'responsible_person' => $request->responsible_person,
            'amount' => $request->amount,
            'remarks' => $request->remarks,
            'supervisor_id' => $loggedInUser->id
        ]);

        return redirect()->route('supervisor.finalCashDestination')
            ->with('success', 'Final Cash Entry recorded successfully!');
    }

    public function finalCashDestination(Request $request)
    {
        $loggedInUser = auth()->guard('supervisor')->user();
        $search = $request->input('search');

        $transactions = FinalCashDestination::query()
            ->where('supervisor_id', $loggedInUser->id)
            ->when($search, function ($query, $search) {
                $query->where('final_destination', 'like', "%{$search}%")
                    ->orWhere('responsible_person', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('web.supervisor.pages.final-cash-destination', [
            'datas' => $transactions,
            'search' => $search,
        ]);
    }


    public function externalCashInflowCreate(Request $request)
    {
        return view('web.supervisor.pages.external-cash-inflow-create');
    }

    public function externalCashInflowStore(Request $request)
    {

        $loggedInUser = auth()->guard('supervisor')->user();
        $request->validate([
            'source' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'received_date' => 'required|date',
            'received_from' => 'required|string|max:100',
            'remarks' => 'nullable|string|max:500',
        ], [
            'amount.required' => 'Please enter the amount.',
            'received_date.required' => 'The received date is required.',
        ]);

        ExternalCashInflow::create([
            'source' => $request->source,
            'amount' => $request->amount,
            'received_date' => $request->received_date,
            'received_from' => $request->received_from,
            'remarks' => $request->remarks,
            'supervisor_id' => $loggedInUser->id
        ]);

        // Increase amount from user's balance
        $loggedInUser->balance += $request->amount;
        $loggedInUser->save();

        return redirect()->route('supervisor.externalCashInflow')
            ->with('success', 'External Cash Inflow created successfully!');
    }

    public function externalCashInflow(Request $request)
    {
        $loggedInUser = auth()->guard('supervisor')->user();

        $search = $request->input('search');

        $availableBalance = User::where('id', $loggedInUser->id)
            ->value('balance') ?? 0;

        $transactions = ExternalCashInflow::query()
            ->where('supervisor_id', $loggedInUser->id)
            ->when($search, function ($query, $search) {
                $query->where('source', 'like', "%{$search}%")
                    ->orWhere('received_from', 'like', "%{$search}%");
            })
            ->orderBy('received_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('web.supervisor.pages.external-cash-inflow', [
            'datas' => $transactions,
            'search' => $search,
            'availableBalance' => $availableBalance,
        ]);
    }


    public function externalCashOutflowCreate(Request $request)
    {
        return view('web.supervisor.pages.external-cash-outflow-create');
    }

    public function externalCashOutflowStore(Request $request)
    {
        $loggedInUser = auth()->guard('supervisor')->user();
        $request->validate([
            'source' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'send_to' => 'required|string|max:100',
            'remarks' => 'nullable|string|max:500',
        ], [
            'amount.required' => 'Please enter the amount.',
            'date.required' => 'The  date is required.',
        ]);


        if ($request->amount > $loggedInUser->balance) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Insufficient balance. Your current balance is $' . number_format($loggedInUser->balance, 2));
        }

        ExternalCashOutflow::create([
            'source' => $request->source,
            'amount' => $request->amount,
            'date' => $request->date,
            'send_to' => $request->send_to,
            'remarks' => $request->remarks,
            'supervisor_id' => $loggedInUser->id
        ]);

        // Deduct amount from user's balance
        $loggedInUser->balance -= $request->amount;
        $loggedInUser->save();

        return redirect()->route('supervisor.externalCashOutFlow')
            ->with('success', 'External Cash Outflow recorded successfully!');
    }

    public function externalCashOutflow(Request $request)
    {
        $loggedInUser = auth()->guard('supervisor')->user();
        $search = $request->input('search');

        // Calculate available balance (cash in hand)
        $availableBalance = User::where('id', $loggedInUser->id)
            ->value('balance') ?? 0;

        $transactions = ExternalCashOutflow::query()
            ->where('supervisor_id', $loggedInUser->id)
            ->when($search, function ($query, $search) {
                $query->where('source', 'like', "%{$search}%")
                    ->orWhere('send_to', 'like', "%{$search}%");
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('web.supervisor.pages.external-cash-outflow', [
            'datas' => $transactions,
            'search' => $search,
            'availableBalance' => $availableBalance,
        ]);
    }

    public function markAsRead($id)
    {
        $user = auth()->guard('supervisor')->user();
        $supervisor = $user->supervisor;

        if ($supervisor) {
            $notification = $supervisor->notifications()->find($id);

            if ($notification) {
                $notification->markAsRead();
                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false], 404);
    }

    // Mark all notifications as read
    public function markAllAsRead()
    {
        $user = auth()->guard('supervisor')->user();
        $supervisor = $user->supervisor;

        if ($supervisor) {
            $supervisor->unreadNotifications->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    // Get unread count
    public function getUnreadCount()
    {
        $user = auth()->guard('supervisor')->user();
        $supervisor = $user->supervisor;

        $count = $supervisor ? $supervisor->unreadNotifications()->count() : 0;

        return response()->json(['count' => $count]);
    }

    // Show all notifications page
    public function notificationList()
    {
        $user = auth()->guard('supervisor')->user();
        $supervisor = $user->supervisor;

        $notifications = $supervisor ? $supervisor->notifications()->paginate(20) : collect();

        return view('web.supervisor.pages.notification-list', compact('notifications'));
    }

    // public function indexRecordExpense()
    // {
    //    $loggedInUser = auth()->guard('supervisor')->user();
    //     $recordExpense = RecordExpense::with('location')
    //         ->where('receiver_id', $loggedInUser->id) 
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(10);
    //     return view('web.supervisor.pages.record-expense-request', compact('recordExpense'));
    // }

    public function indexRecordExpense(Request $request)
    {
        $loggedInUser = auth()->guard('supervisor')->user();

        $query = RecordExpense::with('location')
            ->where('receiver_id', $loggedInUser->id);

        // Apply search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('location', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Apply status filter (optional)
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', '<=', $request->date);
        }

        $query->orderBy('created_at', 'desc');
        $recordExpense = $query->paginate(10);

        return view('web.supervisor.pages.record-expense-request', compact('recordExpense'));
    }

    public function acceptRecordExpense($id)
    {

        // $loggedInUser = auth()->guard('supervisor')->user();

        $item = RecordExpense::findOrFail($id);

        if ($item->status === 'accepted') {
            return redirect()->back()->with('error', 'Record already accepted.');
        }

        $location = Location::findOrFail($item->location_id);


        DB::beginTransaction();
        try {
            $item->update([
                'status' => 'accepted',
                'approval_status' => 'accepted_by_supervisor'
            ]);

            // $location->decrement('balance', $item->amount);

            DB::commit();

            return redirect()->back()->with('success', 'Record accepted successfully and request send to admin successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Accept Record Expense Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to accept record. Please try again.');
        }
    }
    public function rejectRecordExpense($id)
    {
        $item = RecordExpense::findOrFail($id);

        $item->update([
            'status' => 'rejected',
            'approval_status' => 'rejected_by_supervisor'
        ]);

        return redirect()->back()->with('success', 'Record rejected successfully.');
    }

    // public function warehouse_levels(Request $request)
    // {
    //     // Base query to filter stocks only for 'warehouse' locations
    //     $query = Stock::query()
    //         ->whereHas('location', function ($q) {
    //             $q->where('type', 'warehouse');
    //         });

    //     // Check if a specific warehouse is selected
    //     if ($request->filled('warehouse_id')) {
    //         // Filter by the specific warehouse (original logic)
    //         $query->where('location_id', $request->warehouse_id)
    //             ->with(['product', 'location']) // Eager load for single location view
    //             ->orderBy('id', 'desc');

    //         // Apply pagination
    //         $stocks = $query->paginate(10);
    //     } else {
    //         // **LOGIC FOR "All Warehouses" (Total Sum)**

    //         // Select the product_id and calculate the sum of product_quantity
    //         // Join the products table to get product details (name, sku)
    //         $query->select('product_id', DB::raw('SUM(product_quantity) as product_quantity'))
    //             ->with('product') // Eager load product details
    //             ->groupBy('product_id')
    //             ->join('products', 'stocks.product_id', '=', 'products.id')
    //             ->selectRaw('products.sku, products.name') // Select product details
    //             ->orderBy('products.name', 'asc'); // Order by product name

    //         // Apply pagination
    //         // The resulting items will be query builder results with aggregated quantity
    //         $stocks = $query->paginate(10);
    //     }

    //     // Get all warehouses for the filter dropdown
    //     $warehouses = Location::where('type', 'warehouse')->get();

    //     // Return JSON for AJAX requests
    //     if ($request->ajax()) {
    //         // Render table rows HTML
    //         $tableHtml = view('web.supervisor.warehouse_levels.partials.stocks_table', compact('stocks'))->render();

    //         // Render pagination HTML only if there are multiple pages
    //         $paginationHtml = $stocks->hasPages()
    //             ? $stocks->appends(['warehouse_id' => $request->warehouse_id])->links()->render()
    //             : '';

    //         return response()->json([
    //             'tableHtml' => $tableHtml,
    //             'paginationHtml' => $paginationHtml,
    //             'hasPages' => $stocks->hasPages()
    //         ]);
    //     }

    //     // Return view for initial load
    //     return view('web.supervisor.warehouse_levels.index', compact('stocks', 'warehouses'));
    // }

    // public function outlet_levels(Request $request)
    // {
    //     // Base query to filter stocks only for 'outlet' locations
    //     $query = Stock::query()
    //         ->whereHas('location', function ($q) {
    //             $q->where('type', 'outlet');
    //         });

    //     // Check if a specific outlet is selected
    //     if ($request->filled('outlet_id')) {
    //         // Filter by the specific outlet
    //         $query->where('location_id', $request->outlet_id)
    //             ->with(['product', 'location']) // Eager load for single location view
    //             ->orderBy('id', 'desc');

    //         // Apply pagination
    //         $stocks = $query->paginate(10);
    //     } else {
    //         // **LOGIC FOR "All Outlets" (Total Sum)**

    //         // Select the product_id and calculate the sum of product_quantity
    //         $query->select('product_id', DB::raw('SUM(product_quantity) as product_quantity'))
    //             ->with('product') // Eager load product details
    //             ->groupBy('product_id')
    //             ->join('products', 'stocks.product_id', '=', 'products.id')
    //             ->selectRaw('products.sku, products.name') // Select product details
    //             ->orderBy('products.name', 'asc'); // Order by product name

    //         // Apply pagination
    //         $stocks = $query->paginate(10);
    //     }

    //     // Get all outlets for the filter dropdown
    //     $outlets = Location::where('type', 'outlet')->get();

    //     // Return JSON for AJAX requests
    //     if ($request->ajax()) {
    //         // Render table rows HTML
    //         $tableHtml = view('web.supervisor.outlet_levels.partials.stocks_table', compact('stocks'))->render();

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

    //     return view('web.supervisor.outlet_levels.index', compact('stocks', 'outlets'));
    // }


    public function warehouse_levels(Request $request)
{
    // Base query to filter stocks only for 'warehouse' locations
    $query = Stock::query()
        ->whereHas('location', function ($q) {
            $q->where('type', 'warehouse');
        });

    // Check if a specific warehouse is selected
    if ($request->filled('warehouse_id')) {
        // Filter by the specific warehouse
        $query->where('location_id', $request->warehouse_id)
            ->with(['product', 'location']) // Eager load for single location view
            ->orderBy('id', 'desc');

        // Apply pagination
        $stocks = $query->paginate(10);
    } else {
        // **LOGIC FOR "All Warehouses" (Total Sum)**

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

    // Get all warehouses for the filter dropdown
    $warehouses = Location::where('type', 'warehouse')->get();

    // Return JSON for AJAX requests
    if ($request->ajax()) {
        // Render table rows HTML
        $tableHtml = view('web.supervisor.warehouse_levels.partials.stocks_table', compact('stocks'))->render();

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
    return view('web.supervisor.warehouse_levels.index', compact('stocks', 'warehouses'));
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
        if ($request->ajax()) {
            // Render table rows HTML
            $tableHtml = view('web.supervisor.outlet_levels.partials.stocks_table', compact('stocks'))->render();

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

        return view('web.supervisor.outlet_levels.index', compact('stocks', 'outlets'));
    }

    public function salesList(Request $request)
    {
        $loggedInUser = auth()->guard('supervisor')->user();

        $query = Sale::with(['customer', 'location']);

        /** 🔍 Search by customer name */
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        /** 🏢 Filter by Location */
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        /** 📅 Filter by Date (created_at) */
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        /** 📋 Fetch & paginate with query parameters */
        $sales = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        /** Get all locations for filter dropdown */
        $locations = Location::orderBy('name')->get();

        return view('web.supervisor.sales_report.index', compact('sales', 'locations'));
    }

    public function sales_list_view($id)
    {
        $loggedInUser = auth()->guard('supervisor')->user();

        $sale = Sale::with([
            'customer',
            'location',
            'soldProducts.product'
        ])->findOrFail($id);

        return view('web.supervisor.sales_report.view', compact('sale'));
    }


    public function indexReturnRequest(Request $request)
    {
        $loggedInUser = auth()->guard('supervisor')->user();

        $query = StockTransferRequest::with('outlet', 'items')
            ->where('supplier_name', $loggedInUser->id ?? 0)
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

        return view('web.supervisor.return_request.index', compact('transferStocks'));
    }
    /**
     * Supervisor accepts return request
     * Only after warehouse has accepted
     */
    public function return_request_accept($id)
    {
        $transfer = StockTransferRequest::findOrFail($id);

        // Must be accepted by warehouse first
        if ($transfer->status !== 'accepted_by_warehouse') {
            return redirect()->back()->with('error', 'Warehouse must approve this request first.');
        }

        DB::beginTransaction();
        try {
            $transfer->update([
                'status' => 'accepted_by_warehouse_supervisor'
            ]);



            DB::commit();

            Log::info("Supervisor accepted return request #{$transfer->id}.");

            return redirect()->route('supervisor.return-request')
                ->with('success', 'Return request approved successfully. Waiting for admin approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Supervisor Accept Return Error: ' . $e->getMessage(), [
                'transfer_id' => $transfer->id
            ]);

            return redirect()->back()->with('error', 'Failed to accept return. Please try again.');
        }
    }

    /**
     * Supervisor rejects return request
     * Only after warehouse has accepted
     */
    public function return_request_reject($id)
    {
        $transfer = StockTransferRequest::findOrFail($id);

        // if ($transfer->status !== 'accepted_by_warehouse') {
        //     return redirect()->back()->with('error', 'This request cannot be rejected at this stage.');
        // }

        DB::beginTransaction();
        try {
            $transfer->update([
                'status' => 'rejected_by_supervisor'
            ]);
            $outletLocation = Location::find($transfer->supplier_id);
            if ($outletLocation) {
                $outletLocation->notify(new \App\Notifications\StockNotification($transfer, 'return_rejected_by_supervisor'));
            }

            DB::commit();

            Log::info("Supervisor rejected return request #{$transfer->id}.");

            return redirect()->route('supervisor.return-request')
                ->with('success', 'Return request rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Supervisor Reject Return Error: ' . $e->getMessage(), [
                'transfer_id' => $transfer->id
            ]);

            return redirect()->back()->with('error', 'Failed to reject return. Please try again.');
        }
    }

    // SupervisorController.php

    public function indexRefunds(Request $request)
    {
        $loggedInUser = auth()->guard('supervisor')->user();

        $query = Refund::with(['sale.customer', 'location'])
            ->where('supervisor_id', $loggedInUser->id);

        // Apply search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('refund_reason', 'like', "%{$search}%")
                    ->orWhere('refund_notes', 'like', "%{$search}%")
                    ->orWhereHas('sale.customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('sale', function ($saleQuery) use ($search) {
                        $saleQuery->where('id', 'like', "%{$search}%");
                    });
            });
        }

        // Apply status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Apply date filter
        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', '<=', $request->date);
        }

        $query->orderBy('created_at', 'desc');
        $refunds = $query->paginate(10);

        return view('web.supervisor.pages.cash-refund', compact('refunds'));
    }


    // public function acceptRefund($id)
    // {
    //     $item = Refund::findOrFail($id);

    //     if ($item->status === 'accepted') {
    //         return redirect()->back()->with('error', 'Refund already accepted.');
    //     }

    //     $location = Location::findOrFail($item->location_id);

    //     DB::beginTransaction();
    //     try {
    //         $item->update([
    //             'status' => 'accepted_by_supervisor',
    //         ]);

    //         // Optional: Update location balance if needed
    //         $location->decrement('balance', $item->refund_amount);

    //         DB::commit();

    //         return redirect()->back()->with('success', 'Refund accepted successfully.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         Log::error('Accept Refund Error: ' . $e->getMessage());

    //         return redirect()->back()->with('error', 'Failed to accept refund. Please try again.');
    //     }
    // }

    public function acceptRefund($id)
    {
        // Change 'saleItems' to 'soldProducts' in the eager loading
        $item = Refund::with(['sale.soldProducts.product', 'location'])->findOrFail($id);

        if ($item->status === 'accepted_by_supervisor') {
            return redirect()->back()->with('error', 'Refund already accepted.');
        }

        $location = Location::findOrFail($item->location_id);

        DB::beginTransaction();
        try {
            $item->update([
                'status' => 'accepted_by_supervisor',
            ]);

            if ($item->sale) {
                $item->sale->update([
                    'refund_status' => 'refunded',
                ]);
            }
            // Decrement location balance
            $location->decrement('balance', $item->refund_amount);

            // Restore stock for each product in the sale
            // Change 'saleItems' to 'soldProducts'
            if ($item->sale && $item->sale->soldProducts) {
                foreach ($item->sale->soldProducts as $soldProduct) {
                    // Check if stock record exists for this location and product
                    $stock = Stock::where('location_id', $item->location_id)
                        ->where('product_id', $soldProduct->product_id)
                        ->first();

                    if ($stock) {
                        // If stock record exists, increment the quantity
                        $stock->increment('product_quantity', $soldProduct->quantity);

                        Log::info("Stock incremented for Location: {$item->location_id}, Product: {$soldProduct->product_id}, Quantity: {$soldProduct->quantity}");
                    } else {
                        // If stock record doesn't exist, create new one
                        Stock::create([
                            'location_id' => $item->location_id,
                            'product_id' => $soldProduct->product_id,
                            'product_quantity' => $soldProduct->quantity,
                            'type' => 'refund'
                        ]);

                        Log::info("Stock created for Location: {$item->location_id}, Product: {$soldProduct->product_id}, Quantity: {$soldProduct->quantity}");
                    }
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Refund accepted successfully and stock has been restored.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Accept Refund Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to accept refund. Please try again.');
        }
    }

    public function rejectRefund($id)
    {
        $item = Refund::findOrFail($id);

        if ($item->status === 'rejected') {
            return redirect()->back()->with('error', 'Refund already rejected.');
        }

        DB::beginTransaction();
        try {
            $item->update([
                'status' => 'rejected_by_supervisor'
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Refund rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Reject Refund Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to reject refund. Please try again.');
        }
    }
}
