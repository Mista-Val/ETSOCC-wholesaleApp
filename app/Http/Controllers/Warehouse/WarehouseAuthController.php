<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerLocationBalance;
use App\Models\Location;
use App\Models\Notification;
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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Notifications\StockRequestAcceptedNotification;
use App\Notifications\StockRequestRejectedNotification;

class WarehouseAuthController extends Controller
{
    /**
     * Get the warehouse for the currently authenticated user
     */
    private function getUserWarehouse()
    {
        $user = auth()->guard('warehouse')->user();

        if (!$user || !$user->warehouse) {
            return null;
        }

        return Location::where('user_id', $user->warehouse->user_id)
            ->where('id', $user->warehouse->id)
            ->first();
    }

    /**
     * Show login form
     */
    public function LoginForm(Request $request)
    {
        return view('web.auth.login', ['role' => $request->role]);
    }

    /**
     * Handle login for different user roles
     */
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email'    => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     $credentials = $request->only('email', 'password');
    //     $role = $request->input('role');

    //     // Define role configurations
    //     $roleConfigs = [
    //         'warehouse-manager' => [
    //             'guard' => 'warehouse',
    //             'route' => 'warehouse.dashboard'
    //         ],
    //         'outlet-manager' => [
    //             'guard' => 'outlet',
    //             'route' => 'outlet.outlet-dashboard'
    //         ],
    //         'supervisor' => [
    //             'guard' => 'supervisor',
    //             'route' => 'supervisor.supervisor-dashboard'
    //         ],
    //     ];

    //     if (!isset($roleConfigs[$role])) {
    //         return back()->with('error', 'Invalid role selected.');
    //     }

    //     $config = $roleConfigs[$role];
    //     $guard = $config['guard'];

    //     if (Auth::guard($guard)->attempt($credentials)) {
    //         $user = Auth::guard($guard)->user();

    //         if ($user->role !== $role || $user->status !== 1) {
    //             Auth::guard($guard)->logout();
    //             return back()->withErrors(['email' => 'Unauthorized or inactive account.']);
    //         }

    //         return redirect()->route($config['route'])->with('success', 'Login successfully.');
    //     }

    //     return back()->with('error', 'Email or Account doesn\'t exist.');
    // }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $role = $request->input('role');

        // Define role configurations
        $roleConfigs = [
            'warehouse-manager' => [
                'guard' => 'warehouse',
                'route' => 'warehouse.dashboard'
            ],
            'outlet-manager' => [
                'guard' => 'outlet',
                'route' => 'outlet.outlet-dashboard'
            ],
            'supervisor' => [
                'guard' => 'supervisor',
                'route' => 'supervisor.supervisor-dashboard'
            ],
        ];

        if (!isset($roleConfigs[$role])) {
            return back()->with('error', 'Invalid role selected.');
        }

        $config = $roleConfigs[$role];
        $guard = $config['guard'];

        // Check if email exists first
        $userModel = Auth::guard($guard)->getProvider()->getModel();
        $user = $userModel::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email not found.');
        }

        // If email exists but password is wrong
        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Incorrect password.');
        }

        // Check if role or status invalid
        if ($user->role !== $role || $user->status !== 1) {
            return back()->withErrors(['email' => 'Unauthorized or inactive account.']);
        }

        // Attempt login
        if (Auth::guard($guard)->attempt($credentials)) {
            return redirect()->route($config['route'])->with('success', 'Login successfully.');
        }

        return back()->with('error', 'Something went wrong. Please try again.');
    }


    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        $redirectRoute = 'index';

        if (Auth::guard('warehouse')->check()) {
            Auth::guard('warehouse')->logout();
        } elseif (Auth::guard('outlet')->check()) {
            Auth::guard('outlet')->logout();
        } else {
            $redirectRoute = 'login';
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($redirectRoute)->with('success', 'Logged out successfully.');
    }

    /**
     * Show OTP verification form
     */
    public function showOtpForm(Request $request)
    {
        return view('web.auth.otp', ['email' => session('email')]);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $email = session('email');
        $enteredOtp = implode('', $request->otp);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP.'])->withInput();
        }

        if ($user->otp_expire_time && Carbon::now()->gt($user->otp_expire_time)) {
            return back()->withErrors(['otp' => 'OTP has expired. Please request a new one.'])->withInput();
        }

        if ($user->otp == $enteredOtp) {
            $user->otp = null;
            $user->otp_expire_time = null;
            $user->save();

            session(['otp_verified_for' => $user->email, 'otp_verified_at' => now()]);

            return redirect()->route('reset-password')->with('success', 'OTP verified successfully.');
        }

        return back()->withErrors(['otp' => 'Invalid OTP. Please try again.'])->withInput();
    }

    /**
     * Show my account page
     */
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
        } else {
            return redirect()->route('index')->with('error', 'Please login first');
        }

        return view($viewPath, compact('user', 'role'));
    }

    /**
     * Update account information
     */
    public function updateAccount(Request $request)
    {
        if (Auth::guard('warehouse')->check()) {
            $user = Auth::guard('warehouse')->user();
        } elseif (Auth::guard('outlet')->check()) {
            $user = Auth::guard('outlet')->user();
        } else {
            return redirect()->route('index')->with('error', 'Something went wrong');
        }

        $request->validate([
            'fullname'      => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email,' . $user->id,
            'mobile'        => 'required|digits:10',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        ], [
            'profile_image.image' => 'The profile image must be an actual image file.',
            'profile_image.mimes' => 'The profile image must be a file of type: jpg, jpeg, png, svg.',
            'profile_image.max'   => 'The profile image must not be larger than 2MB.',
        ]);

        // Parse full name into first and last name
        $fullName = trim($request->fullname);
        $nameParts = explode(' ', $fullName, 2);

        $user->name = $request->fullname;
        $user->first_name = ucfirst($nameParts[0]);
        $user->last_name = ucfirst($nameParts[1] ?? '');
        $user->email = $request->email;
        $user->mobile = $request->mobile;

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $this->handleProfileImageUpload($request->file('profile_image'), $user);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Handle profile image upload
     */
    private function handleProfileImageUpload($image, $user)
    {
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $uploadPath = public_path('web/images/');

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Delete old image if exists
        if (!empty($user->profile_image)) {
            $oldImagePath = public_path($user->profile_image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $image->move($uploadPath, $imageName);
        $user->profile_image = 'web/images/' . $imageName;
    }

    /**
     * Show change password form
     */
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
        } else {
            return redirect()->route('index')->with('error', 'Unauthorized access.');
        }

        return view($viewPath, compact('user', 'role'));
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        if (Auth::guard('warehouse')->check()) {
            $user = Auth::guard('warehouse')->user();
        } elseif (Auth::guard('outlet')->check()) {
            $user = Auth::guard('outlet')->user();
        } else {
            return redirect()->route('index')->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'old_password' => ['required'],
            'new_password' => ['required', 'min:6', 'confirmed'],
        ], [
            'old_password.required'      => 'Old password is required.',
            'new_password.required'      => 'New password is required.',
            'new_password.min'           => 'New password must be at least 6 characters.',
            'new_password.confirmed'     => 'New password and confirmation do not match.',
        ]);

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Old password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Show reset password form
     */
    public function resetpassword(Request $request)
    {
        return view('web.auth.resetpassword', ['email' => session('email')]);
    }

    /**
     * Submit reset password
     */
    public function resetpasswordSubmit(Request $request)
    {
        $email = session('otp_verified_for');

        if (!$email) {
            return redirect()->route('warehouse.login')
                ->withErrors('Email session expired. Please try again.');
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('warehouse.login')
                ->withErrors('User not found.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        session()->forget(['otp_verified_for', 'otp_verified_at']);

        return redirect()->route('index')->with('success', 'Password reset successfully.');
    }

    /**
     * Dashboard index
     */
    public function index(Request $request)
    {
        return view('web.warehouse.pages.index');
    }

    /**
     * Transfer outlets - create form
     */
    public function transferOutletsCreate(Request $request)
    {
        $outlets = Location::where('status', 1)->where('type', 'outlet')->get();
        $products = Product::where('status', 1)->get();

        return view('web.warehouse.pages.transfer-to-outlets-create', compact('outlets', 'products'));
    }

    /**
     * Store transfer to outlets
     */
    // public function transferOutletsStore(Request $request)
    // {
    //     $request->validate([
    //         'outlet_id'                => 'required|exists:locations,id',
    //         'remarks'                  => 'nullable|string|max:500',
    //         'products'                 => 'required|array|min:1',
    //         'products.*.product_id'    => 'required|exists:products,id',
    //         'products.*.quantity'      => 'required|integer|min:1',
    //     ], [
    //         'outlet_id.required'              => 'Please select an outlet.',
    //         'outlet_id.exists'                => 'Selected outlet is invalid.',
    //         'products.required'               => 'Please add at least one product.',
    //         'products.*.product_id.required'  => 'Please select a product.',
    //         'products.*.product_id.exists'    => 'Selected product is invalid.',
    //         'products.*.quantity.required'    => 'Please enter a quantity.',
    //         'products.*.quantity.integer'     => 'Quantity must be a positive whole number.',
    //         'products.*.quantity.min'         => 'Quantity must be at least 1.',
    //     ]);

    //     $warehouse = Location::where('user_id', auth()->guard('warehouse')->id())->firstOrFail();

    //     // Validate stock availability
    //     $stockErrors = $this->validateStockAvailability($request->products, $warehouse->id);

    //     if (!empty($stockErrors)) {
    //         return back()->withErrors($stockErrors)->withInput();
    //     }

    //     // Create transfer request
    //     $transferStock = StockTransferRequest::create([
    //         'receiver_id' => $request->outlet_id,
    //         'supplier_id' => $warehouse->id,
    //         'remark'      => $request->remarks,
    //         'type'        => 'warehouse',
    //         'status'      => 'created',
    //     ]);

    //     // Create transfer items
    //     foreach ($request->products as $item) {
    //         StockTransferRequestsProduct::create([
    //             'transfer_request_id' => $transferStock->id,
    //             'product_id'          => $item['product_id'],
    //             'set_quantity'        => $item['quantity'],
    //             'type'                => 'warehouse',
    //             'status'              => 'created',
    //         ]);
    //     }

    //     return redirect()->route('warehouse.transferoutlets')
    //         ->with('success', 'Stock transfer request created successfully!');
    // }


    public function transferOutletsStore(Request $request)
    {
        $request->validate([
            'outlet_id'                => 'required|exists:locations,id',
            'remarks'                  => 'nullable|string|max:500',
            'products'                 => 'required|array|min:1',
            'products.*.product_id'    => 'required|exists:products,id',
            'products.*.quantity'      => 'required|integer|min:1',
        ], [
            'outlet_id.required'              => 'Please select an outlet.',
            'outlet_id.exists'                => 'Selected outlet is invalid.',
            'products.required'               => 'Please add at least one product.',
            'products.*.product_id.required'  => 'Please select a product.',
            'products.*.product_id.exists'    => 'Selected product is invalid.',
            'products.*.quantity.required'    => 'Please enter a quantity.',
            'products.*.quantity.integer'     => 'Quantity must be a positive whole number.',
            'products.*.quantity.min'         => 'Quantity must be at least 1.',
        ]);

        try {
            DB::beginTransaction();

            $warehouse = Location::where('user_id', auth()->guard('warehouse')->id())->firstOrFail();

            // Validate stock availability
            $stockErrors = $this->validateStockAvailability($request->products, $warehouse->id);

            if (!empty($stockErrors)) {
                DB::rollBack();
                return back()->withErrors($stockErrors)->withInput();
            }

            // Create transfer request
            $transferStock = StockTransferRequest::create([
                'receiver_id' => $request->outlet_id,
                'supplier_id' => $warehouse->id,
                'remark'      => $request->remarks,
                'type'        => 'warehouse',
                'status'      => 'created',
            ]);

            // Create transfer items
            foreach ($request->products as $item) {
                StockTransferRequestsProduct::create([
                    'transfer_request_id' => $transferStock->id,
                    'product_id'          => $item['product_id'],
                    'set_quantity'        => $item['quantity'],
                    'type'                => 'warehouse',
                    'status'              => 'created',
                ]);
            }

            // Send notification to outlet
            // $outletReceiver = Location::where('id', $request->outlet_id)
            //     ->where('type', 'outlet')
            //     ->first();

            // if ($outletReceiver) {
            //     $outletReceiver->notify(new StockNotification($transferStock, 'created'));
            // } else {
            //     Log::warning('Outlet receiver not found for transfer notification.', [
            //         'outlet_id' => $request->outlet_id,
            //         'transfer_id' => $transferStock->id
            //     ]);
            // }

            DB::commit();

            return redirect()->route('warehouse.transferoutlets')
                ->with('success', 'Stock transfer request created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create stock transfer.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create stock transfer. Please try again.');
        }
    }

    /**
     * Validate stock availability for transfer
     */
    private function validateStockAvailability($products, $warehouseId)
    {
        $stockErrors = [];

        foreach ($products as $index => $item) {
            $stock = Stock::where('location_id', $warehouseId)
                ->where('product_id', $item['product_id'])
                ->first();

            $totalStockQuantity = $stock ? $stock->product_quantity : 0;

            // Get allocated quantity in pending requests
            $allocatedQuantity = StockTransferRequestsProduct::whereHas('request', function ($query) use ($warehouseId) {
                $query->where('supplier_id', $warehouseId)
                    ->where('type', 'warehouse')
                    ->where('status', 'created');
            })
                ->where('product_id', $item['product_id'])
                ->sum('set_quantity');

            $availableQuantity = $totalStockQuantity - $allocatedQuantity;

            if ($item['quantity'] > $availableQuantity) {
                $product = Product::find($item['product_id']);
                $productName = $product ? $product->name : 'Product';

                $stockErrors["products.{$index}.quantity"] = "Insufficient stock for {$productName}. Total Stock: {$totalStockQuantity}, Already Allocated: {$allocatedQuantity}, Available: {$availableQuantity}, Requested: {$item['quantity']}";
            }
        }

        return $stockErrors;
    }

    /**
     * List transfers to outlets
     */
    public function transferOutlets(Request $request)
    {
        $warehouse = $this->getUserWarehouse();

        $query = StockTransferRequest::with('outlet')
            ->where('type', 'warehouse');

        if ($warehouse) {
            $query->where('supplier_id', $warehouse->id);
        } else {
            $query->where('id', 0);
        }

        // Search filter
        if ($request->filled('search')) {
            $query->whereHas('outlet', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
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

        return view('web.warehouse.pages.transfer-to-outlets', compact('transferStocks'));
    }

    /**
     * View transfer details
     */
    public function transferOutletsStocksDetails($id)
    {
        $transferStock = StockTransferRequest::with('outlet', 'items.product')->findOrFail($id);
        return view('web.warehouse.pages.transfer-to-outlets-detail', compact('transferStock'));
    }

    /**
     * Update transfer status
     */
    // public function transferOutletsUpdateStatus(Request $request)
    // {
    //     $request->validate([
    //         'transfer_id' => 'required|exists:stock_transfer_requests,id',
    //         'status'      => 'required|in:created,dispatched',
    //     ]);

    //     $transferStock = StockTransferRequest::findOrFail($request->transfer_id);
    //     $transferStock->status = $request->status;
    //     $transferStock->save();

    //     return redirect()->route('warehouse.transferoutlets')
    //         ->with('success', 'Transfer stock status updated successfully!');
    // }


    public function transferOutletsUpdateStatus(Request $request)
    {
        $request->validate([
            'transfer_id' => 'required|exists:stock_transfer_requests,id',
            'status'      => 'required|in:created,dispatched',
        ]);

        $transferStock = StockTransferRequest::findOrFail($request->transfer_id);
        $transferStock->status = $request->status;
        $transferStock->save();

        // Send notification when dispatched
        if ($request->status === 'dispatched') {
            $outletReceiver = \App\Models\Location::find($transferStock->receiver_id);

            if ($outletReceiver) {
                $outletReceiver->notify(new \App\Notifications\StockNotification($transferStock, 'dispatched'));
            } else {
                Log::warning('Outlet receiver not found for dispatched notification.', [
                    'outlet_id' => $transferStock->receiver_id,
                    'transfer_id' => $transferStock->id
                ]);
            }
        }

        return redirect()->route('warehouse.transferoutlets')
            ->with('success', 'Transfer stock status updated successfully!');
    }


    /**
     * Customer management - Store
     */
    public function customerStore(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'phone_number' => 'required|numeric|digits:10|unique:customers,phone_number',
            'address'      => 'required|string|max:255',
        ], [
            'name.required'            => 'Customer name is required.',
            'phone_number.required'    => 'Phone number is required.',
            'phone_number.numeric'     => 'Phone number must contain only numbers.',
            'phone_number.digits'      => 'Phone number must be exactly 10 digits.',
            'phone_number.unique'      => 'This phone number is already registered.',
            'address.required'            => 'Customer address is required.',
        ]);

        Customer::create([
            'name'         => $request->name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'balance'      => 0,
        ]);

        return redirect()->back()->withInput()
            ->with('success', 'Customer created successfully!');
    }

    /**
     * Customer management - List
     */
    public function customerList(Request $request)
    {
        $query = Customer::orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(10);

        return view('web.warehouse.pages.customer-list', compact('customers'));
    }

    /**
     * Customer management - Update
     */
    public function customerUpdate(Request $request, Customer $customer)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'address'         => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:customers,phone_number,' . $customer->id,
        ], [
            'name.required'         => 'Customer name is required.',
            'name.address'         => 'Customer address is required.',
            'phone_number.required' => 'Phone number is required.',
            'phone_number.unique'   => 'This phone number is already registered.',
        ]);

        try {
            $customer->update([
                'name'         => $request->name,
                'phone_number' => $request->phone_number,
                'address' => $request->address
            ]);

            return redirect()->route('warehouse.customerList')
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
     * Customer management - Delete
     */
    public function customerDestroy(Customer $customer)
    {
        try {
            $customer->delete();
            return redirect()->route('warehouse.customerList')
                ->with('success', 'Customer deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting customer. Please try again.');
        }
    }

    /**
     * Sales orders listing
     */
    // public function salesOrders(Request $request)
    // {
    //     $user = auth()->guard('warehouse')->user();
    //     $warehouses = $user->warehouse;

    //     $query = Sale::with('customer')
    //         ->where('location_id', $warehouses->id)
    //         ->latest();

    //     // Search filter
    //     if ($request->filled('search')) {
    //         $query->whereHas('customer', function ($q) use ($request) {
    //             $q->where('name', 'like', '%' . $request->search . '%');
    //         });
    //     }

    //     // Date filter
    //     if ($request->filled('date')) {
    //         $query->whereDate('created_at', $request->date);
    //     }

    //     $sales = $query->paginate(10);

    //     return view('web.warehouse.pages.sales-orders', compact('sales'));
    // }

    public function salesOrders(Request $request)
    {
        $user = auth()->guard('warehouse')->user();
        $warehouseId = optional($user->warehouse)->id; // prevents "Attempt to read property" error

        if (!$warehouseId) {
            return redirect()->back()->with('error', 'Warehouse not found for this user.');
        }

        $query = Sale::with(['customer', 'waybill','refund'])
            ->where('location_id', $warehouseId)
            ->latest();

        if ($request->filled('search')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $sales = $query->paginate(10);

        $outlets = Location::where('type', 'outlet')
            ->where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        // Get active supervisors for refund modal
        $supervisors = User::where('status', 1)
            ->where('role', 'supervisor')
            ->orderBy('name')
            ->get();


        return view('web.warehouse.pages.sales-orders', compact('sales', 'outlets','supervisors'));
    }


    /**
     * Create sales form
     */
    // public function createSales(Request $request)
    // {
    //     $user = auth()->guard('warehouse')->user();
    //     $warehouseId = $user->warehouse->id;

    //     // Get products with available stock
    //     $products = Product::where('status', 1)
    //         ->whereHas('stocks', function ($query) use ($warehouseId) {
    //             $query->where('location_id', $warehouseId)
    //                 ->where('product_quantity', '>', 0);
    //         })
    //         ->get();

    //     $customers = Customer::all();

    //     return view('web.warehouse.pages.create-sales', compact('products', 'customers'));
    // }

    public function createSales(Request $request)
    {
        $user = auth()->guard('warehouse')->user();

        // Check if user exists and has an outlet
        // if (!$user || !$user->warehouse) {
        //     return redirect()->route('warehouse.login')->with('error', 'Please login to continue.');
        // }

        $warehouseId = $user->warehouse->id;

        $products = Product::where('status', 1)->get();

        // Get customers with their location-specific balances for this outlet
        $customers = Customer::with(['locationBalances' => function ($query) use ($warehouseId) {
            $query->where('location_id', $warehouseId);
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

        return view('web.warehouse.pages.create-sales', compact('products', 'customers'));
    }

    /**
     * Store sales order
     */
    // public function storeSales(Request $request)
    // {
    //     $user = auth()->guard('warehouse')->user();
    //     $warehouses = $user->warehouse;

    //     $orderItems = json_decode($request->order_items, true);

    //     if (empty($orderItems)) {
    //         return back()->withErrors(['order_items' => 'No products selected'])->withInput();
    //     }

    //     try {
    //         DB::beginTransaction();


    //         if ($request->payment_method === 'Down Payment') {
    //             $customer = Customer::find($request->customer_id);

    //             if (!$customer) {
    //                 return back()->withErrors(['customer_id' => 'Customer not found.'])->withInput();
    //             }

    //             if ($request->total_amount > $customer->balance) {
    //                 return back()->withErrors([
    //                     'payment_method' => "Insufficient customer balance. Available balance: ₹{$customer->balance}, Sale amount: ₹{$request->total_amount}"
    //                 ])->withInput();
    //             }
    //         }

    //         $sale = Sale::create([
    //             'customer_id'    => $request->customer_id,
    //             'location_id'    => $warehouses->id,
    //             'payment_method' => $request->payment_method,
    //             'remark'         => $request->remark,
    //             'total_amount'   => $request->total_amount,
    //             'status' => 'completed'
    //         ]);

    //         foreach ($orderItems as $item) {
    //             $this->processSaleItem($item, $sale->id, $warehouses->id, $request->customer_id);
    //         }

    //         if ($request->payment_method === 'Cash') {
    //             $warehouses->increment('balance', $request->total_amount);
    //         }

    //         if ($request->payment_method === 'Down Payment') {
    //             $customer = Customer::find($request->customer_id);
    //             $customer->decrement('balance', $request->total_amount);
    //         }

    //         if ($request->payment_method === 'Credit') {
    //             $customer = Customer::find($request->customer_id);
    //             $customer->increment('credit_balance', $request->total_amount);
    //         }

    //         DB::commit();

    //         return redirect()->route('warehouse.salesOrders')
    //             ->with('success', 'Sale created successfully and stock updated!');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Sale creation failed: ' . $e->getMessage());

    //         return back()->withErrors(['system_error' => 'A system error occurred while processing the sale.'])->withInput();
    //     }
    // }

    /**
     * Process individual sale item
     */
    // private function processSaleItem($item, $saleId, $locationId, $customerId)
    // {
    //     $productId = $item['id'];
    //     $quantitySold = $item['quantity'];

    //     $productName = Product::find($productId)?->name ?? "Unknown Product (ID: {$productId})";

    //     $stock = Stock::where('product_id', $productId)
    //         ->where('location_id', $locationId)
    //         ->first();

    //     if (!$stock) {
    //         throw new \Exception("Stock record not found for Product: {$productName}.");
    //     }

    //     if ($stock->product_quantity < $quantitySold) {
    //         throw new \Exception("Insufficient stock for Product: {$productName}. Available: {$stock->product_quantity}, Requested: {$quantitySold}.");
    //     }

    //     SoldProduct::create([
    //         'sale_id'              => $saleId,
    //         'product_id'           => $productId,
    //         'location_id'          => $locationId,
    //         'customer_id'          => $customerId,
    //         'per_unit_amount'      => $item['price'],
    //         'quantity'             => $quantitySold,
    //         'total_product_amount' => $item['price'] * $quantitySold,
    //     ]);

    //     $stock->product_quantity -= $quantitySold;
    //     $stock->save();
    // }


    public function storeSales(Request $request)
    {
        $user = auth()->guard('warehouse')->user();
        $warehouses = $user->warehouse;
        $locationId = $warehouses->id;

        $orderItems = json_decode($request->order_items, true);

        if (empty($orderItems)) {
            return back()->withErrors(['order_items' => 'No products selected'])->withInput();
        }

        // Validate stock availability BEFORE transaction - collect ALL errors
        $stockErrors = [];

        foreach ($orderItems as $item) {
            $productId = $item['id'];
            $quantitySold = $item['quantity'];
            $productName = Product::find($productId)?->name ?? "Unknown Product (ID: {$productId})";

            $stock = Stock::where('product_id', $productId)
                ->where('location_id', $warehouses->id)
                ->first();

            if (!$stock) {
                $stockErrors[] = "Stock record not found for Product: {$productName}.";
                continue;
            }

            if ($stock->product_quantity < $quantitySold) {
                $stockErrors[] = "Insufficient stock for Product: {$productName}. Available: {$stock->product_quantity}, Requested: {$quantitySold}.";
            }
        }

        // If there are any stock errors, return them all
        if (!empty($stockErrors)) {
            return back()->withErrors([
                'order_items' => implode(' | ', $stockErrors)
            ])->withInput();
        }

        try {
            DB::beginTransaction();

            // if ($request->payment_method === 'Down Payment') {
            //     $customer = Customer::find($request->customer_id);

            //     if (!$customer) {
            //         return back()->withErrors(['customer_id' => 'Customer not found.'])->withInput();
            //     }

            //     if ($request->total_amount > $customer->balance) {
            //         return back()->withErrors([
            //             'payment_method' => "Insufficient customer balance. Available balance: ₹{$customer->balance}, Sale amount: ₹{$request->total_amount}"
            //         ])->withInput();
            //     }
            // }

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

            $sale = Sale::create([
                'customer_id'    => $request->customer_id,
                'location_id'    => $warehouses->id,
                'payment_method' => $request->payment_method,
                'remark'         => $request->remark,
                'total_amount'   => $request->total_amount,
                'status' => 'completed',
                'refund_status'=> 'pending'
            ]);

            foreach ($orderItems as $item) {
                $this->processSaleItem($item, $sale->id, $warehouses->id, $request->customer_id);
            }

            // Payment Logic
            switch ($request->payment_method) {
                case 'Cash':
                    // ✅ Add total amount to outlet balance
                    $warehouses->increment('balance', $request->total_amount);
                    break;

                case 'Down Payment':
                    // ✅ Reduce customer balance for this specific outlet location
                    CustomerLocationBalance::updateOrCreate(
                        [
                            'customer_id' => $request->customer_id,
                            'location_id' => $warehouses->id,
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
                            'location_id' => $warehouses->id,
                        ],
                        [
                            'credit_balance' => DB::raw("credit_balance + {$request->total_amount}")
                        ]
                    );
                    break;
            }

            // if ($request->payment_method === 'Cash') {
            //     $warehouses->increment('balance', $request->total_amount);
            // }

            // if ($request->payment_method === 'Down Payment') {
            //     $customer = Customer::find($request->customer_id);
            //     $customer->decrement('balance', $request->total_amount);
            // }

            // if ($request->payment_method === 'Credit') {
            //     $customer = Customer::find($request->customer_id);
            //     $customer->increment('credit_balance', $request->total_amount);
            // }

            DB::commit();

            return redirect()->route('warehouse.salesOrders')
                ->with('success', 'Sale created successfully and stock updated!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale creation failed: ' . $e->getMessage());

            return back()->withErrors(['system_error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Process individual sale item
     */
    private function processSaleItem($item, $saleId, $locationId, $customerId)
    {
        $productId = $item['id'];
        $quantitySold = $item['quantity'];

        SoldProduct::create([
            'sale_id'              => $saleId,
            'product_id'           => $productId,
            'location_id'          => $locationId,
            'customer_id'          => $customerId,
            'per_unit_amount'      => $item['price'],
            'quantity'             => $quantitySold,
            'total_product_amount' => $item['price'] * $quantitySold,
        ]);

        $stock = Stock::where('product_id', $productId)
            ->where('location_id', $locationId)
            ->first();

        $stock->product_quantity -= $quantitySold;
        $stock->save();
    }

    /**
     * Sales details
     */
    public function salesDetails($id)
    {
        $sale = Sale::with(['customer', 'soldProducts.product'])->findOrFail($id);
        return view('web.warehouse.pages.sales-details', compact('sale'));
    }

    /**
     * Sales invoice
     */
    public function salesInvoice($id)
    {
        $sale = Sale::with(['customer', 'soldProducts.product'])->findOrFail($id);
        return view('web.warehouse.pages.sales-invoice', compact('sale'));
    }

    /**
     * Outlet stock request listing
     */
    public function outletStockRequest(Request $request)
    {
        $warehouse = $this->getUserWarehouse();

        $query = StockTransferRequest::with('senderOutlet')
            ->where('type', 'stock-request');

        if ($warehouse) {
            $query->where('receiver_id', $warehouse->id);
        } else {
            $query->where('id', 0);
        }

        // Search filter
        if ($request->filled('search')) {
            $query->whereHas('senderOutlet', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
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

        $requestStocks = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('web.warehouse.pages.outlet-stock-request', compact('requestStocks'));
    }

    /**
     * Outlet stock request details
     */
    public function outletStockRequestDetails($id)
    {
        $transferStock = StockTransferRequest::with('outlet', 'items.product')->findOrFail($id);
        return view('web.warehouse.pages.stock-outlet-request-details', compact('transferStock'));
    }

    /**
     * Accept outlet stock request
     */
    // public function outlet_stock_request_accept($id)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $stock = StockTransferRequest::with('items')->findOrFail($id);

    //         if ($stock->status === 'completed') {
    //             return redirect()->back()->with('error', 'Request already accepted.');
    //         }

    //         $stock->status = 'completed';
    //         $stock->save();

    //         $outletLocation = Location::find($stock->supplier_id);

    //         if (!$outletLocation) {
    //             DB::rollBack();
    //             return redirect()->back()->with('error', 'Outlet location not found.');
    //         }

    //         // Update related sale if exists
    //         $sale = Sale::where('location_id', $outletLocation->id)
    //             ->where('customer_id', $stock->supplier_name)
    //             ->first();

    //         if ($sale) {
    //             $sale->status = 'completed';
    //             $sale->save();
    //         } else {
    //             Log::warning('No matching sale found to update.', [
    //                 'stock_request_id' => $id,
    //                 'location_id'      => $outletLocation->id,
    //                 'customer_id'      => $stock->supplier_name,
    //             ]);
    //         }

    //         // Notification::create([
    //         //     'type'             => 'Stock Transfer',
    //         //     'notifiable_type'  => 'warehouse', 
    //         //     'notifiable_id'    => $outletLocation->id,   
    //         //     'data'             => 'Stock transfer request #' . $stock->id . ' has been accepted successfully.',
    //         //     'read_at'          => null,
    //         // ]);

    //          $outletLocation->notify(new StockRequestAcceptedNotification($stock));

    //         DB::commit();

    //         return redirect()->back()->with('success', 'Request accepted successfully.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Stock request acceptance failed: ' . $e->getMessage());

    //         return redirect()->back()->with('error', 'An error occurred while processing the request: ' . $e->getMessage());
    //     }
    // }

    // public function outlet_stock_request_accept($id)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $stock = StockTransferRequest::with('items')->findOrFail($id);

    //         if ($stock->status === 'completed') {
    //             return redirect()->back()->with('error', 'Request already accepted.');
    //         }

    //         $stock->status = 'completed';
    //         $stock->save();

    //         $outletLocation = Location::find($stock->supplier_id);
    //         $saleData = Sale::where('id', $stock->supplier_name)->where('location_id', $outletLocation->id)->first();

    //         if (!$outletLocation) {
    //             DB::rollBack();
    //             return redirect()->back()->with('error', 'Outlet location not found.');
    //         }

    //         // Update the specific sale using both sale_id and location_id for security
    //         if ($stock->supplier_name) {
    //             $sale = Sale::where('id', $stock->supplier_name)
    //                 ->where('location_id', $outletLocation->id)
    //                 ->first();

    //             if ($sale && $sale->status !== 'completed') {
    //                 $sale->status = 'completed';
    //                 // $sale->type = 'available'; // Update type from 'unavailable' to 'available'
    //                 $sale->save();

    //                 Log::info('Sale status updated to completed.', [
    //                     'stock_request_id' => $id,
    //                     'sale_id'          => $sale->id,
    //                     'location_id'      => $outletLocation->id,
    //                 ]);
    //             } elseif (!$sale) {
    //                 Log::warning('Sale not found or does not belong to this outlet.', [
    //                     'stock_request_id' => $id,
    //                     'sale_id'          => $stock->supplier_name,
    //                     'location_id'      => $outletLocation->id,
    //                 ]);
    //             } else {
    //                 Log::info('Sale already completed.', [
    //                     'stock_request_id' => $id,
    //                     'sale_id'          => $sale->id,
    //                 ]);
    //             }
    //         } else {
    //             Log::warning('No sale_id found in stock transfer request.', [
    //                 'stock_request_id' => $id,
    //             ]);
    //         }

    //         if ($saleData->payment_method === 'Cash') {
    //             $outletLocation->increment('balance', $saleData->total_amount);
    //         }

    //         // if ($saleData->payment_method === 'Down Payment') {
    //         //     // You might want to update the specific CustomerLocationBalance here
    //         //     // instead of the general Customer balance if that's the source of truth,
    //         //     // but sticking to your provided logic for now:
    //         //     $customer = Customer::find($saleData->customer_id);
    //         //     $customer->decrement('balance', $saleData->total_amount);
    //         // }

    //         // if ($saleData->payment_method === 'Credit') {
    //         //     $customer = Customer::find($saleData->customer_id);
    //         //     $customer->increment('credit_balance', $saleData->total_amount);
    //         // }

    //         $outletLocation->notify(new StockRequestAcceptedNotification($stock));

    //         DB::commit();

    //         return redirect()->back()->with('success', 'Request accepted successfully and sale status updated.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Stock request acceptance failed: ' . $e->getMessage());

    //         return redirect()->back()->with('error', 'An error occurred while processing the request: ' . $e->getMessage());
    //     }
    // }

    // public function outlet_stock_request_accept($id)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $stock = StockTransferRequest::with('items')->findOrFail($id);

    //         if ($stock->status === 'completed') {
    //             return redirect()->back()->with('error', 'Request already accepted.');
    //         }

    //         $stock->status = 'completed';
    //         $stock->save();

    //         $outletLocation = Location::find($stock->supplier_id);
    //         $saleData = Sale::where('id', $stock->supplier_name)
    //             ->where('location_id', $outletLocation->id)
    //             ->first();

    //         if (!$outletLocation) {
    //             DB::rollBack();
    //             return redirect()->back()->with('error', 'Outlet location not found.');
    //         }

    //         // Update the specific sale using both sale_id and location_id for security
    //         if ($stock->supplier_name) {
    //             $sale = Sale::where('id', $stock->supplier_name)
    //                 ->where('location_id', $outletLocation->id)
    //                 ->first();

    //             if ($sale && $sale->status !== 'completed') {
    //                 $sale->status = 'completed';
    //                 $sale->save();

    //                 Log::info('Sale status updated to completed.', [
    //                     'stock_request_id' => $id,
    //                     'sale_id'          => $sale->id,
    //                     'location_id'      => $outletLocation->id,
    //                 ]);
    //             } elseif (!$sale) {
    //                 Log::warning('Sale not found or does not belong to this outlet.', [
    //                     'stock_request_id' => $id,
    //                     'sale_id'          => $stock->supplier_name,
    //                     'location_id'      => $outletLocation->id,
    //                 ]);
    //             } else {
    //                 Log::info('Sale already completed.', [
    //                     'stock_request_id' => $id,
    //                     'sale_id'          => $sale->id,
    //                 ]);
    //             }
    //         } else {
    //             Log::warning('No sale_id found in stock transfer request.', [
    //                 'stock_request_id' => $id,
    //             ]);
    //         }

    //         // ✅ Payment Logic (matching your reference code)
    //         switch ($saleData->payment_method) {
    //             case 'Cash':
    //                 // Add total amount to outlet balance
    //                 $outletLocation->increment('balance', $saleData->total_amount);
    //                 break;

    //             case 'Down Payment':
    //                 // Reduce customer balance for this specific outlet location
    //                 CustomerLocationBalance::updateOrCreate(
    //                     [
    //                         'customer_id' => $saleData->customer_id,
    //                         'location_id' => $outletLocation->id,
    //                     ],
    //                     [
    //                         'balance' => DB::raw("balance - {$saleData->total_amount}")
    //                     ]
    //                 );
    //                 break;

    //             case 'Credit':
    //                 // Increase customer's credit balance for this specific outlet location
    //                 CustomerLocationBalance::updateOrCreate(
    //                     [
    //                         'customer_id' => $saleData->customer_id,
    //                         'location_id' => $outletLocation->id,
    //                     ],
    //                     [
    //                         'credit_balance' => DB::raw("credit_balance + {$saleData->total_amount}")
    //                     ]
    //                 );
    //                 break;
    //         }

    //         $outletLocation->notify(new StockRequestAcceptedNotification($stock));

    //         DB::commit();

    //         return redirect()->back()->with('success', 'Request accepted successfully and sale status updated.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Stock request acceptance failed: ' . $e->getMessage());

    //         return redirect()->back()->with('error', 'An error occurred while processing the request: ' . $e->getMessage());
    //     }
    // }
    /**
     * Reject outlet stock request
     */
    public function outlet_stock_request_reject($id)
    {
        try {
            DB::beginTransaction();

            $stock = StockTransferRequest::findOrFail($id);

            if ($stock->status === 'rejected') {
                return redirect()->back()->with('error', 'Request already rejected.');
            }

            // Update stock request status
            $stock->status = 'rejected';
            $stock->save();

            // Find outlet location
            $outletLocation = Location::find($stock->supplier_id);
            if (!$outletLocation) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Outlet location not found.');
            }

            // 🔍 Find the related sale (if exists)
            $sale = null;
            if ($stock->supplier_name) { // supplier_name used as sale_id in your logic
                $sale = Sale::where('id', $stock->supplier_name)
                    ->where('location_id', $outletLocation->id)
                    ->first();

                if ($sale) {
                    // ✅ Update sale status if not already rejected/cancelled
                    if ($sale->status !== 'rejected') {
                        $sale->status = 'rejected';
                        $sale->save();

                        Log::info('Sale marked as rejected.', [
                            'stock_request_id' => $id,
                            'sale_id'          => $sale->id,
                            'location_id'      => $outletLocation->id,
                        ]);
                    } else {
                        Log::info('Sale already marked as rejected.', [
                            'stock_request_id' => $id,
                            'sale_id'          => $sale->id,
                        ]);
                    }
                } else {
                    Log::warning('No matching sale found for this outlet.', [
                        'stock_request_id' => $id,
                        'sale_id'          => $stock->supplier_name,
                        'location_id'      => $outletLocation->id,
                    ]);
                }
            } else {
                Log::warning('No sale_id (supplier_name) linked to this stock request.', [
                    'stock_request_id' => $id,
                ]);
            }

            // 🔔 Send rejection notification to outlet
            $outletLocation->notify(new StockRequestRejectedNotification($stock));

            DB::commit();

            return redirect()->back()->with('success', 'Request rejected successfully, sale status updated, and notification sent.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock request rejection failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while rejecting the request: ' . $e->getMessage());
        }
    }

    // public function outlet_stock_request_accept($id)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $stock = StockTransferRequest::with('items')->findOrFail($id);

    //         if ($stock->status === 'completed') {
    //             return redirect()->back()->with('error', 'Request already accepted.');
    //         }

    //         $stock->status = 'completed';
    //         $stock->save();

    //         $outletLocation = Location::find($stock->supplier_id); // Outlet (receiver)
    //         $warehouseLocation = Location::find($stock->receiver_id); // Warehouse (sender)

    //         $saleData = Sale::where('id', $stock->supplier_name)
    //             ->where('location_id', $outletLocation->id)
    //             ->first();

    //         if (!$outletLocation) {
    //             DB::rollBack();
    //             return redirect()->back()->with('error', 'Outlet location not found.');
    //         }

    //         if (!$warehouseLocation) {
    //             DB::rollBack();
    //             return redirect()->back()->with('error', 'Warehouse location not found.');
    //         }

    //         // ✅ Validate warehouse stock availability for all products FIRST
    //         $insufficientStockProducts = [];

    //         foreach ($stock->items as $item) {
    //             $productId = $item->product_id;
    //             $transferQuantity = $item->set_quantity; // Shortage quantity to transfer

    //             $warehouseStock = Stock::where('location_id', $warehouseLocation->id)
    //                 ->where('product_id', $productId)
    //                 ->first();

    //             $product = Product::find($productId);
    //             $productName = $product ? $product->name : "Product ID: {$productId}";

    //             if (!$warehouseStock) {
    //                 $insufficientStockProducts[] = "{$productName} - Not available in warehouse";
    //             } elseif ($warehouseStock->product_quantity < $transferQuantity) {
    //                 $insufficientStockProducts[] = "{$productName} - Required: {$transferQuantity}, Available: {$warehouseStock->product_quantity}";
    //             }
    //         }

    //         // If any product has insufficient stock, show all errors and rollback
    //         if (!empty($insufficientStockProducts)) {
    //             DB::rollBack();
    //             $errorMessage = "Insufficient stock in warehouse for the following products:\n" . implode("\n", $insufficientStockProducts);
    //             return redirect()->back()->with('error', $errorMessage);
    //         }

    //         // ✅ Update Stock for each product in the transfer request
    //         foreach ($stock->items as $item) {
    //             $productId = $item->product_id;
    //             $transferQuantity = $item->set_quantity; // Shortage quantity to transfer
    //             $availableQuantity = $item->received_quantity; // Already available at outlet

    //             // 1. DECREASE warehouse stock (sender)
    //             $warehouseStock = Stock::where('location_id', $warehouseLocation->id)
    //                 ->where('product_id', $productId)
    //                 ->first();

    //             $warehouseStock->product_quantity -= $transferQuantity;
    //             $warehouseStock->save();

    //             Log::info('Warehouse stock decreased.', [
    //                 'product_id' => $productId,
    //                 'warehouse_id' => $warehouseLocation->id,
    //                 'decreased_by' => $transferQuantity,
    //                 'new_quantity' => $warehouseStock->product_quantity
    //             ]);

    //             $outletStock = Stock::firstOrCreate(
    //                 [
    //                     'location_id' => $outletLocation->id,
    //                     'product_id' => $productId,
    //                     'type' => 'outlet'
    //                 ],
    //                 [
    //                     'product_quantity' => 0
    //                 ]
    //             );

    //             $outletStock->product_quantity -= $transferQuantity;
    //             $outletStock->save();
    //         }

    //         // Update the specific sale using both sale_id and location_id for security
    //         if ($stock->supplier_name) {
    //             $sale = Sale::where('id', $stock->supplier_name)
    //                 ->where('location_id', $outletLocation->id)
    //                 ->first();

    //             if ($sale && $sale->status !== 'completed') {
    //                 $sale->status = 'completed';
    //                 $sale->save();

    //                 Log::info('Sale status updated to completed.', [
    //                     'stock_request_id' => $id,
    //                     'sale_id'          => $sale->id,
    //                     'location_id'      => $outletLocation->id,
    //                 ]);
    //             } elseif (!$sale) {
    //                 Log::warning('Sale not found or does not belong to this outlet.', [
    //                     'stock_request_id' => $id,
    //                     'sale_id'          => $stock->supplier_name,
    //                     'location_id'      => $outletLocation->id,
    //                 ]);
    //             } else {
    //                 Log::info('Sale already completed.', [
    //                     'stock_request_id' => $id,
    //                     'sale_id'          => $sale->id,
    //                 ]);
    //             }
    //         } else {
    //             Log::warning('No sale_id found in stock transfer request.', [
    //                 'stock_request_id' => $id,
    //             ]);
    //         }

    //         // ✅ Payment Logic (matching your reference code)
    //         if ($saleData) {
    //             switch ($saleData->payment_method) {
    //                 case 'Cash':
    //                     // Add total amount to outlet balance
    //                     $outletLocation->increment('balance', $saleData->total_amount);
    //                     break;

    //                 case 'Down Payment':
    //                     // Reduce customer balance for this specific outlet location
    //                     CustomerLocationBalance::updateOrCreate(
    //                         [
    //                             'customer_id' => $saleData->customer_id,
    //                             'location_id' => $outletLocation->id,
    //                         ],
    //                         [
    //                             'balance' => DB::raw("balance - {$saleData->total_amount}")
    //                         ]
    //                     );
    //                     break;

    //                 case 'Credit':
    //                     // Increase customer's credit balance for this specific outlet location
    //                     CustomerLocationBalance::updateOrCreate(
    //                         [
    //                             'customer_id' => $saleData->customer_id,
    //                             'location_id' => $outletLocation->id,
    //                         ],
    //                         [
    //                             'credit_balance' => DB::raw("credit_balance + {$saleData->total_amount}")
    //                         ]
    //                     );
    //                     break;
    //             }
    //         }

    //         $outletLocation->notify(new StockRequestAcceptedNotification($stock));

    //         DB::commit();

    //         return redirect()->back()->with('success', 'Request accepted successfully. Stock transferred and sale status updated.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Stock request acceptance failed: ' . $e->getMessage());

    //         return redirect()->back()->with('error', 'An error occurred while processing the request: ' . $e->getMessage());
    //     }
    // }
    public function outlet_stock_request_accept($id)
    {
        try {
            DB::beginTransaction();

            $stock = StockTransferRequest::with('items')->findOrFail($id);

            if ($stock->status === 'completed') {
                return redirect()->back()->with('error', 'Request already accepted.');
            }

            $stock->status = 'completed';
            $stock->save();

            $outletLocation = Location::find($stock->supplier_id); // Outlet
            $warehouseLocation = Location::find($stock->receiver_id); // Warehouse

            $saleData = Sale::where('id', $stock->supplier_name)
                ->where('location_id', $outletLocation->id)
                ->first();

            if (!$outletLocation) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Outlet location not found.');
            }

            if (!$warehouseLocation) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Warehouse location not found.');
            }

            // ✅ Validate stock first
            $insufficientStockProducts = [];
            foreach ($stock->items as $item) {
                $productId = $item->product_id;
                $transferQuantity = $stock->collect_all
                    ? ($item->set_quantity + $item->received_quantity)
                    : $item->set_quantity;

                $warehouseStock = Stock::where('location_id', $warehouseLocation->id)
                    ->where('product_id', $productId)
                    ->first();

                $product = Product::find($productId);
                $productName = $product ? $product->name : "Product ID: {$productId}";

                if (!$warehouseStock) {
                    $insufficientStockProducts[] = "{$productName} - Not available in warehouse";
                } elseif ($warehouseStock->product_quantity < $transferQuantity) {
                    $insufficientStockProducts[] = "{$productName} - Required: {$transferQuantity}, Available: {$warehouseStock->product_quantity}";
                }
            }

            if (!empty($insufficientStockProducts)) {
                DB::rollBack();
                $errorMessage = "Insufficient stock in warehouse for the following products:\n" . implode("\n", $insufficientStockProducts);
                return redirect()->back()->with('error', $errorMessage);
            }

            // ✅ Update Stock logic
            foreach ($stock->items as $item) {
                $productId = $item->product_id;

                if ($stock->collect_all) {
                    // 🟢 If collect_all = true → only decrease warehouse
                    $warehouseDecreaseQty = $item->set_quantity + $item->received_quantity;

                    $warehouseStock = Stock::where('location_id', $warehouseLocation->id)
                        ->where('product_id', $productId)
                        ->first();

                    if ($warehouseStock) {
                        $warehouseStock->product_quantity -= $warehouseDecreaseQty;
                        $warehouseStock->save();

                        Log::info('Warehouse stock decreased (collect_all = true).', [
                            'product_id' => $productId,
                            'warehouse_id' => $warehouseLocation->id,
                            'decreased_by' => $warehouseDecreaseQty,
                            'new_quantity' => $warehouseStock->product_quantity
                        ]);
                    }
                } else {
                    // 🔹 collect_all = false → normal logic
                    $warehouseDecreaseQty = $item->set_quantity;
                    $outletDecreaseQty = $item->received_quantity;

                    // Decrease warehouse
                    $warehouseStock = Stock::where('location_id', $warehouseLocation->id)
                        ->where('product_id', $productId)
                        ->first();

                    if ($warehouseStock) {
                        $warehouseStock->product_quantity -= $warehouseDecreaseQty;
                        $warehouseStock->save();

                        Log::info('Warehouse stock decreased (normal).', [
                            'product_id' => $productId,
                            'warehouse_id' => $warehouseLocation->id,
                            'decreased_by' => $warehouseDecreaseQty,
                            'new_quantity' => $warehouseStock->product_quantity
                        ]);
                    }

                    // Decrease outlet
                    $outletStock = Stock::where('location_id', $outletLocation->id)
                        ->where('product_id', $productId)
                        ->first();

                    if ($outletStock) {
                        $outletStock->product_quantity -= $outletDecreaseQty;
                        $outletStock->save();

                        Log::info('Outlet stock decreased (normal sale).', [
                            'product_id' => $productId,
                            'outlet_id' => $outletLocation->id,
                            'decreased_by' => $outletDecreaseQty,
                            'new_quantity' => $outletStock->product_quantity
                        ]);
                    }
                }
            }

            // ✅ Update Sale
            if ($stock->supplier_name) {
                $sale = Sale::where('id', $stock->supplier_name)
                    ->where('location_id', $outletLocation->id)
                    ->first();

                if ($sale && $sale->status !== 'completed') {
                    $sale->status = 'completed';
                    $sale->save();

                    Log::info('Sale status updated to completed.', [
                        'stock_request_id' => $id,
                        'sale_id'          => $sale->id,
                        'location_id'      => $outletLocation->id,
                    ]);
                }
            }

            // ✅ Payment logic
            if ($saleData) {
                switch ($saleData->payment_method) {
                    case 'Cash':
                        $outletLocation->increment('balance', $saleData->total_amount);
                        break;
                    case 'Down Payment':
                        CustomerLocationBalance::updateOrCreate(
                            [
                                'customer_id' => $saleData->customer_id,
                                'location_id' => $outletLocation->id,
                            ],
                            [
                                'balance' => DB::raw("balance - {$saleData->total_amount}")
                            ]
                        );
                        break;
                    case 'Credit':
                        CustomerLocationBalance::updateOrCreate(
                            [
                                'customer_id' => $saleData->customer_id,
                                'location_id' => $outletLocation->id,
                            ],
                            [
                                'credit_balance' => DB::raw("credit_balance + {$saleData->total_amount}")
                            ]
                        );
                        break;
                }
            }

            $outletLocation->notify(new StockRequestAcceptedNotification($stock));

            DB::commit();

            return redirect()->back()->with('success', 'Request accepted successfully. Stock transferred and sale status updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock request acceptance failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while processing the request: ' . $e->getMessage());
        }
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

        return view('web.warehouse.pages.product-management', compact('products'));
    }


    public function productUpdatePrice(Request $request, Product $product)
    {
        $request->validate([
            'warehouse_price' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($product) {
                    if ($value < $product->min_price) {
                        $fail("The warehouse price must be at least ₹{$product->min_price}.");
                    }
                    if ($value > $product->max_price) {
                        $fail("The warehouse price cannot exceed ₹{$product->max_price}.");
                    }
                },
            ],
        ]);

        try {
            $product->update([
                'warehouse_price' => $request->warehouse_price
            ]);

            return redirect()->route('warehouse.productManagementList')
                ->with('success', 'Warehouse price updated successfully for ' . $product->name);
        } catch (\Exception $e) {
            return redirect()->route('warehouse.productManagementList')
                ->with('error', 'Failed to update warehouse price. Please try again.')
                ->withInput()
                ->with([
                    'edit_product' => true,
                    'edit_product_id' => $product->id,
                    'edit_product_name' => $product->name,
                    'edit_product_sku' => $product->sku,
                    'edit_product_min_price' => $product->min_price,
                    'edit_product_max_price' => $product->max_price,
                    'edit_product_price' => $request->warehouse_price
                ]);
        }
    }

    public function generateWaybill(Request $request, $saleId)
    {
        // Validate the incoming request
         $validated = $request->validate([
        'waybill_number' => 'required|string|max:255|unique:waybills,waybill_number',
        'loading_date' => 'required|date|after_or_equal:today',
        'estimated_delivery_date' => 'required|date|after_or_equal:loading_date',
        'outlet_id' => 'required|exists:locations,id',
        'loader_name' => 'required|string|max:255',
        'loader_position' => 'required|string|max:255',
        'number_of_packages' => 'required|integer|min:1',
        'quantity' => 'required|integer|min:1',
        'receiver_name' => 'required|string|max:255',
        'receiver_position' => 'required|string|max:255',
        'shipping_remarks' => 'nullable|string|max:1000',
    ], [
        'loading_date.after_or_equal' => 'Loading date cannot be in the past.',
        'estimated_delivery_date.after_or_equal' => 'Estimated delivery date must be on or after the loading date.',
    ]);
        try {
            DB::beginTransaction();

            // Get the authenticated warehouse user
            $user = auth()->guard('warehouse')->user();
            $warehouse = $user->warehouse;

            // Fetch the sale
            $sale = Sale::where('id', $saleId)
                ->where('location_id', $warehouse->id)
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
                'location_id' => $warehouse->id,
                'waybill_number' => $validated['waybill_number'],
                'loading_date' => $validated['loading_date'],
                'estimated_delivery_date' => $validated['estimated_delivery_date'],
                'warehouse_name' => $warehouse->name,
                'outlet_id' => $validated['outlet_id'],
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
                ->route('warehouse.salesOrders')
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

        return view('web.warehouse.pages.way-bill', compact('waybill'));
    }
    /**
     * Process refund
     */
    // public function processRefund(Request $request, $saleId)
    // {
    //     $request->validate([
    //         'refund_amount' => 'required|numeric|min:0.01',
    //         'refund_reason' => 'required|string'
    //     ]);

    //     $sale = Sale::with('customer')->findOrFail($saleId);

    //     // Validate refund amount doesn't exceed sale amount
    //     if ($request->refund_amount > $sale->total_amount) {
    //         return back()->withErrors([
    //             'refund_amount' => "Refund amount ($" . number_format($request->refund_amount, 2) . ") cannot exceed sale amount ($" . number_format($sale->total_amount, 2) . ")"
    //         ]);
    //     }

    //     try {
    //         DB::beginTransaction();

    //         // Create refund record (you'll need to create this table)
    //         $refund = Refund::create([
    //             'sale_id' => $sale->id,
    //             'customer_id' => $sale->customer_id,
    //             'location_id' => $sale->location_id,
    //             'refund_amount' => $request->refund_amount,
    //             'refund_reason' => $request->refund_reason,
    //             'refund_notes' => $request->refund_notes,
    //             'processed_by' => auth()->guard('warehouse')->id(),
    //             'status' => 'completed'
    //         ]);

    //         // Reverse the payment based on original payment method
    //         switch ($sale->payment_method) {
    //             case 'Cash':
    //                 // Deduct from outlet balance
    //                 $sale->location->decrement('balance', $request->refund_amount);
    //                 break;

    //             case 'Down Payment':
    //                 // Add back to customer's down payment balance
    //                 CustomerLocationBalance::updateOrCreate(
    //                     [
    //                         'customer_id' => $sale->customer_id,
    //                         'location_id' => $sale->location_id,
    //                     ],
    //                     [
    //                         'balance' => DB::raw("balance + {$request->refund_amount}")
    //                     ]
    //                 );
    //                 break;

    //             case 'Credit':
    //                 // Reduce customer's credit balance
    //                 CustomerLocationBalance::updateOrCreate(
    //                     [
    //                         'customer_id' => $sale->customer_id,
    //                         'location_id' => $sale->location_id,
    //                     ],
    //                     [
    //                         'credit_balance' => DB::raw("credit_balance - {$request->refund_amount}")
    //                     ]
    //                 );
    //                 break;
    //         }

    //         DB::commit();

    //         return redirect()->route('warehouse.salesOrders')
    //             ->with('success', 'Refund processed successfully! Amount: $' . number_format($request->refund_amount, 2));
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Refund processing failed: ' . $e->getMessage());

    //         return back()->withErrors(['system_error' => 'Failed to process refund: ' . $e->getMessage()]);
    //     }
    // }


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
                'supervisor_id' => $request-> supervisor_id,
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
