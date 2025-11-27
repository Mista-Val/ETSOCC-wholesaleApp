@extends('web.auth.app')
@section('content')
    @include('web.outlet.shared.header')

    <style>
        .btn-danger {
            background-color: #ec188b !important;
            color: #ffffff !important;
            border-color: #ec188b !important;
        }

        .btn-outline.btn-pink {
            color: #ec188b !important;
            border-color: #ec188b !important;
        }

        #customer-dropdown {
            position: absolute;
            z-index: 50;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            margin-top: 0.25rem;
            max-height: 15rem;
            overflow-y: auto;
        }

        .customer-dropdown-item {
            padding: 0.5rem 1rem;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
        }

        .customer-dropdown-item:hover {
            background-color: #f3f4f6;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal[open] {
            display: flex;
        }

        .modal-box {
            background: white;
            border-radius: 0.5rem;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        /* Custom SweetAlert2 Styles */
        .swal2-confirm:not([disabled]) {
            /* Sets the normal button color (same as your JS setting) */
            background-color: #ec188b !important;
            border-left-color: #ec188b !important;
            border-right-color: #ec188b !important;
        }

        .swal2-confirm:focus,
        .swal2-confirm:hover {
            /* Ensures the color stays the same or a specific shade on hover/focus */
            background-color: #d6167c !important;
            /* Slightly darker shade for a subtle hover effect */
            box-shadow: 0 0 10px #ec188b;
            /* Optional: Add a subtle glow on hover/focus */
        }
    </style>

    <main class="dashboard-screen-bg relative">
        <section class="dashboard-title-section bg-white border-b border-gry-50">
            <div class="container-fluid">
                <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                    <h1 class="h6 text-gry-800">Sales & Orders</h1>
                    <div class="breadcrumb flex items-center gap-[10px] flex-wrap">
                        <a href="{{ route('outlet.outlet-dashboard') }}">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8.39173 2.34954L2.61673 6.97453C1.96673 7.4912 1.55006 8.5829 1.69172 9.39956L2.80006 16.0329C3.00006 17.2162 4.13339 18.1745 5.33339 18.1745H14.6667C15.8584 18.1745 17.0001 17.2079 17.2001 16.0329L18.3084 9.39956C18.4417 8.5829 18.0251 7.4912 17.3834 6.97453L11.6084 2.35789C10.7167 1.64122 9.27506 1.64121 8.39173 2.34954Z"
                                    stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M10.0001 12.9167C11.1507 12.9167 12.0834 11.9839 12.0834 10.8333C12.0834 9.68274 11.1507 8.75 10.0001 8.75C8.84949 8.75 7.91675 9.68274 7.91675 10.8333C7.91675 11.9839 8.84949 12.9167 10.0001 12.9167Z"
                                    stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </a>
                        <span class="text-gry-300">/</span>
                        <a href="{{ route('outlet.salesOrders') }}" class="body-14 text-gry-800 bold">Sales & Orders</a>
                    </div>
                </div>
            </div>
        </section>
        <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
            <div class="container-fluid">
                <div class="flex flex-wrap gap-[15px] md:gap-[30px]">
                    <div class="flex-1">
                        <div class="mb-6 relative">
                            <input type="text" id="product-search" placeholder="Search Product"
                                value="{{ request('search') }}" class="pl-[45px] form-control">
                            <button class="absolute left-3 top-1/2 -translate-y-1/2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M11.5 21C16.7467 21 21 16.7467 21 11.5C21 6.25329 16.7467 2 11.5 2C6.25329 2 2 6.25329 2 11.5C2 16.7467 6.25329 21 11.5 21Z"
                                        stroke="#333333" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M22 22L20 20" stroke="#333333" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                        <div class="grid [grid-template-columns:repeat(auto-fit,minmax(235px,1fr))] gap-[15px]"
                            id="products-grid">
                            @forelse($products as $product)
                                <div class="white-box text-center flex flex-col gap-[15px] md:gap-[25px] product-card"
                                    data-product-name="{{ strtolower($product->name) }}">
                                    <h3 class="body-16 semibold">{{ $product->name }}</h3>
                                    <button class="btn btn-outline btn-pink w-full add-product-btn"
                                        data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}"
                                        data-product-price="{{ $product->outlet_price ?? 0 }}">
                                        Add
                                    </button>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-8">
                                    <p class="text-gry-500">No products found</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <form id="sales-form" action="{{ route('outlet.storeSales') }}" method="POST">
                        @csrf
                        <div class="max-w-[648px] w-full white-box sticky top-[110px] h-[fit-content]">
                            <h2 class="body-20 text-gry-800 border-b pb-[10px] semibold mb-[15px]">Order List</h2>

                            @if ($errors->any() && !($errors->has('phone_number') || $errors->has('name')))
                                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-red-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div class="flex-1">
                                            <h3 class="text-sm font-medium text-red-800 mb-1">Error</h3>
                                            <ul class="text-sm text-red-700 space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    @if (!str_contains($error, 'phone_number') && !str_contains($error, 'name'))
                                                        <li>{{ $error }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="flex flex-col gap-[10px] mb-[15px]">
                                <label for="phone" class="body-14 semibold">Customer Details</label>
                                <div class="flex items-center gap-[15px]">
                                    <div class="flex-1 relative" id="phone-search-container">
                                        <input type="text" id="phone-search"
                                            placeholder="Enter phone number to search customer" class="form-control">
                                    </div>
                                    <button type="button" class="btn btn-outline btn-pink gap-[5px]" id="add-customer-btn"
                                        onclick="document.getElementById('add-customer-modal').showModal()">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                            viewBox="0 0 25 24" fill="none">
                                            <path
                                                d="M12.5 12C15.2614 12 17.5 9.76142 17.5 7C17.5 4.23858 15.2614 2 12.5 2C9.73858 2 7.5 4.23858 7.5 7C7.5 9.76142 9.73858 12 12.5 12Z"
                                                stroke="#EC188B" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M3.91016 22C3.91016 18.13 7.76015 15 12.5002 15C13.4602 15 14.3902 15.13 15.2602 15.37"
                                                stroke="#EC188B" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M22.5 18C22.5 18.32 22.46 18.63 22.38 18.93C22.29 19.33 22.13 19.72 21.92 20.06C21.23 21.22 19.96 22 18.5 22C17.47 22 16.54 21.61 15.84 20.97C15.54 20.71 15.28 20.4 15.08 20.06C14.71 19.46 14.5 18.75 14.5 18C14.5 16.92 14.93 15.93 15.63 15.21C16.36 14.46 17.38 14 18.5 14C19.68 14 20.75 14.51 21.47 15.33C22.11 16.04 22.5 16.98 22.5 18Z"
                                                stroke="#EC188B" stroke-width="1.5" stroke-miterlimit="10"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M19.9897 17.9805H17.0098" stroke="#EC188B" stroke-width="1.5"
                                                stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M18.5 16.5195V19.5095" stroke="#EC188B" stroke-width="1.5"
                                                stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        Add
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="customer_id" id="customer_id" value="">
                            <input type="hidden" name="location_id" id="location_id"
                                value="{{ auth()->user()->location_id ?? 1 }}">
                            <input type="hidden" name="total_amount" id="total_amount_input" value="0">
                            <input type="hidden" name="payment_method" id="payment_method_input" value="Cash">
                            <input type="hidden" name="order_items" id="order_items_input" value="{}">

                            <div class="flex justify-between items-center white-box mb-[15px]" id="customer-info"
                                style="display: none;">
                                <div class="flex flex-col gap-[3px]">
                                    <p class="body-16-regular text-gry-900" id="customer-name">James Anderson</p>
                                    <p class="body-16-regular semibold" id="customer-phone">+91 45124 451236</p>
                                </div>
                                <button type="button" id="remove-customer-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="41" height="42"
                                        viewBox="0 0 41 42" fill="none">
                                        <ellipse opacity="0.15" cx="20.5" cy="21" rx="20.5"
                                            ry="21" fill="#01ABEC" />
                                        <path
                                            d="M20.4998 31.9375C26.3722 31.9375 31.1769 27.0156 31.1769 21C31.1769 14.9844 26.3722 10.0625 20.4998 10.0625C14.6274 10.0625 9.82275 14.9844 9.82275 21C9.82275 27.0156 14.6274 31.9375 20.4998 31.9375Z"
                                            stroke="#01ABEC" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M17.4785 24.0949L23.5217 17.9043" stroke="#01ABEC" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M23.5217 24.0949L17.4785 17.9043" stroke="#01ABEC" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>

                            <div class="text-center white-box mb-[15px]" id="no-customer-found" style="display: none;">
                                <p class="body-14-regular text-gry-500">No customer found</p>
                            </div>
                            <hr class="mb-[15px]" id="customer-divider" style="display: none;" />

                            <div class="mb-4" id="order-details-section" style="display: none;">
                                <div class="flex justify-between items-center mb-[15px]">
                                    <div class="flex flex-col gap-[5px]">
                                        <h3 class="body-18-semibold text-gry-800">Order Details</h3>
                                        <p class="body-16-regular text-gry-900">Items : <span id="item-count">0</span></p>
                                    </div>
                                    <button type="button" class="body-16-semibold text-secondary-500"
                                        id="clear-all-btn">Clear All</button>
                                </div>
                                <div class="overflow-y-auto whitespace-nowrap">
                                    <table class="w-full">
                                        <thead class="body-16-semibold text-gry-900 border-b border-[#E6E6E6]">
                                            <tr>
                                                <th class="text-left py-[10px]">Product Name</th>
                                                <th class="py-[10px] text-center px-[10px]">Quantity</th>
                                                <th class="py-[10px] text-right">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody id="order-items"></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="flex justify-between items-center font-semibold text-lg border-t border-b pt-3 pb-3 mb-[15px]"
                                id="total-section" style="display: none;">
                                <span class="body-16-semibold text-gry-900">Total</span>
                                <span class="body-16-semibold text-gry-900">$<span id="total-amount">0</span></span>
                            </div>

                            <div class="mb-[15px] flex flex-col gap-[10px]">
                                <h3 class="body-18-semibold text-gry-800">Select Payment Method</h3>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button"
                                        class="btn !bg-blue-400 !text-white !border-blue-400 payment-method-btn selected"
                                        data-method="Cash">Cash</button>
                                    <button type="button"
                                        class="btn !bg-white !border !border-gray-300 !text-gray-600 payment-method-btn"
                                        data-method="Bank Transfer">Bank Transfer</button>
                                    <button type="button"
                                        class="btn !bg-white !border !border-gray-300 !text-gray-600 payment-method-btn"
                                        data-method="Down Payment">Down Payment</button>
                                    <button type="button"
                                        class="btn !bg-white !border !border-gray-300 !text-gray-600 payment-method-btn"
                                        data-method="Credit">Credit</button>
                                </div>

                                <div class="available-balance-box flex justify-between items-center flex-wrap gap-[10px] bg-secondary-50 border border-secondary-200 p-[10px] rounded-[8px]"
                                    id="balance-section" style="display: none;">
                                    <span class="body-14-regular text-gry-900">Available Balance</span>
                                    <span class="body-16-semibold text-gry-900">$<span
                                            id="available-balance">0</span></span>
                                </div>
                            </div>

                            <hr class="mb-[15px]" />

                            <div class="mb-[15px] form-group">
                                <label for="remark">Add Remarks</label>
                                <input type="text" name="remark" placeholder="Enter remark" class="form-control"
                                    value="{{ old('remark') }}">
                            </div>

                            <button type="submit" class="btn w-full bg-primary-500" id="create-sale-btn">
                                Create
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <dialog id="add-customer-modal" class="modal" @if ($errors->has('phone_number') || $errors->has('name')) open @endif>
        <div class="modal-box p-0 relative">
            <div class="flex items-center justify-between py-3 px-4 border-b border-gry-50">
                <h2 class="h6">Add new customer</h2>
                <button type="button" class="btn btn-sm btn-ghost"
                    onclick="document.getElementById('add-customer-modal').close()">
                    {{-- <img src="{{ asset('web/images/close1.png') }}" alt="Close" class="w-5 h-5" /> --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="41" height="42" viewBox="0 0 41 42"
                        fill="none">
                        <ellipse opacity="0.15" cx="20.5" cy="21" rx="20.5" ry="21"
                            fill="#01ABEC" />
                        <path
                            d="M20.4998 31.9375C26.3722 31.9375 31.1769 27.0156 31.1769 21C31.1769 14.9844 26.3722 10.0625 20.4998 10.0625C14.6274 10.0625 9.82275 14.9844 9.82275 21C9.82275 27.0156 14.6274 31.9375 20.4998 31.9375Z"
                            stroke="#01ABEC" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M17.4785 24.0949L23.5217 17.9043" stroke="#01ABEC" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M23.5217 24.0949L17.4785 17.9043" stroke="#01ABEC" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                </button>
            </div>

            <div class="modal-content p-4">
                <form method="POST" action="{{ route('outlet.customerStore') }}">
                    @csrf
                    <div class="form-group mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Phone number</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                            class="form-control @error('phone_number') border-red-500 @enderror"
                            placeholder="Enter phone number" required />
                        @error('phone_number')
                            <small class="text-red-500 text-xs mt-1 block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Customer name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="form-control @error('name') border-red-500 @enderror" placeholder="Enter Name"
                            required />
                        @error('name')
                            <small class="text-red-500 text-xs mt-1 block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Customer address</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                            class="form-control @error('address') border-red-500 @enderror" placeholder="Enter address"
                            required />
                        @error('address')
                            <small class="text-red-500 text-xs mt-1 block">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn bg-pink-500 text-white w-full mt-2">
                        Create
                    </button>
                </form>
            </div>
        </div>
    </dialog>

    {{-- <dialog id="stock-validation-modal" class="modal">
        <div class="modal-box p-0 relative" style="max-width: 650px;">
            <div class="flex items-center justify-between py-3 px-4 border-b border-gry-50">
                <h2 class="h6">Insufficient Stock</h2>
            </div>

            <div class="modal-content p-4" style="max-height: 500px; overflow-y: auto;">
                <p class="text-sm text-gray-700 mb-4">The following products have insufficient stock. Please request stock
                    transfer from a warehouse:</p>

                <div id="stock-issues-list" class="space-y-3"></div>

                <div class="mt-6 flex gap-3">
                    <button type="button" id="request-stock-btn" class="btn bg-pink-500 text-white flex-1"
                        onclick="requestStockTransfer()">
                        Request Stock Transfer
                    </button>
                </div>
            </div>
        </div>
    </dialog> --}}

    <dialog id="stock-validation-modal" class="modal">
    <div class="modal-box p-0 relative" style="max-width: 650px;">
        <div class="flex items-center justify-between py-3 px-4 border-b border-gry-50">
            <h2 class="h6">Insufficient Stock</h2>
        </div>

        <div class="modal-content p-4" style="max-height: 500px; overflow-y: auto;">
            <p class="text-sm text-gray-700 mb-4">
                The following products have insufficient stock. Please request stock transfer from a warehouse:
            </p>

            <div id="stock-issues-list" class="space-y-3"></div>

            <div class="mt-6 flex gap-3">
                <button 
                    type="button" 
                    id="request-stock-btn" 
                    class="btn bg-pink-500 text-white flex-1"
                    onclick="requestStockTransfer()"
                >
                    Request Stock Transfer
                </button>
            </div>
        </div>
    </div>
</dialog>


    <script>
        let orderItems = {};
        let totalAmount = 0;
        let selectedCustomer = null;
        let selectedPaymentMethod = 'Cash';
        const productButtonMap = {};
        const customers = @json($customers ?? []);

        const stockIssues = @json(session('stock_issues') ?? []);
        const warehouses = @json(session('warehouses') ?? []);
        const showStockModal = @json(session('show_stock_modal') ?? false);

        function restoreOrderState() {
            let oldOrderItemsRaw = @json(old('order_items') ?? '{}');
            let oldCustomerId = @json(old('customer_id') ?? '');
            let oldPaymentMethod = @json(old('payment_method') ?? 'Cash');
            let oldOrderItems = {};

            try {
                if (typeof oldOrderItemsRaw === 'string') {
                    oldOrderItems = JSON.parse(oldOrderItemsRaw);
                } else if (typeof oldOrderItemsRaw === 'object') {
                    oldOrderItems = oldOrderItemsRaw;
                }
            } catch (e) {
                console.error('Error parsing old order items:', e);
                oldOrderItems = {};
            }

            if (oldOrderItems && Object.keys(oldOrderItems).length > 0) {
                orderItems = oldOrderItems;
                updateOrderDisplay();
                Object.keys(orderItems).forEach(id => updateProductCardButton(id));
            }

            if (oldCustomerId && customers.length > 0) {
                const customer = customers.find(c => c.id == oldCustomerId);
                if (customer) {
                    selectedCustomer = customer;
                    document.getElementById('phone-search').value = customer.phone_number || '';
                    selectCustomer(customer);
                }
            }

            if (oldPaymentMethod) {
                selectedPaymentMethod = oldPaymentMethod;
                const paymentBtn = document.querySelector(`.payment-method-btn[data-method="${oldPaymentMethod}"]`);
                if (paymentBtn) {
                    selectPaymentMethod(oldPaymentMethod, paymentBtn);
                }
            }
        }

        function addProductToOrder(productId, productName, productPrice, buttonElement) {
            const id = productId.toString();
            if (orderItems[id]) {
                orderItems[id].quantity += 1;
            } else {
                orderItems[id] = {
                    id: id,
                    name: productName,
                    price: parseFloat(productPrice),
                    quantity: 1
                };
                if (buttonElement) {
                    productButtonMap[id] = buttonElement;
                }
            }
            updateOrderDisplay();
        }

        function updateQuantity(productId, change) {
            const id = productId.toString();
            if (orderItems[id]) {
                orderItems[id].quantity += change;
                if (orderItems[id].quantity <= 0) {
                    delete orderItems[id];
                }
                updateOrderDisplay();
            }
        }

        function removeProductFromOrder(productId) {
            const id = productId.toString();
            if (orderItems[id]) {
                delete orderItems[id];
                updateOrderDisplay();
            }
        }

        function updateProductCardButton(productId) {
            const id = productId.toString();
            let button = productButtonMap[id];

            if (!button) {
                button = document.querySelector(`.product-card [data-product-id="${id}"]`);
                if (!button) return;
                productButtonMap[id] = button;
            }

            const productName = button.getAttribute('data-product-name');
            const productPrice = button.getAttribute('data-product-price');

            if (orderItems[id] && orderItems[id].quantity > 0) {
                button.textContent = 'Remove';
                button.classList.remove('btn-pink', 'btn-outline');
                button.classList.add('btn-danger');
                button.onclick = function() {
                    removeProductFromOrder(id);
                };
            } else {
                button.textContent = 'Add';
                button.classList.remove('btn-danger');
                button.classList.add('btn-outline', 'btn-pink');
                button.onclick = function() {
                    addProductToOrder(id, productName, productPrice, button);
                };
                if (productButtonMap[id]) {
                    delete productButtonMap[id];
                }
            }
        }

        function updateOrderDisplay() {
            const orderItemsContainer = document.getElementById('order-items');
            const itemCountElement = document.getElementById('item-count');
            const totalAmountElement = document.getElementById('total-amount');
            const orderDetailsSection = document.getElementById('order-details-section');
            const totalSection = document.getElementById('total-section');

            const mappedButtonIds = Object.keys(productButtonMap);
            orderItemsContainer.innerHTML = '';

            let itemCount = 0;
            totalAmount = 0;
            const currentOrderIds = Object.keys(orderItems);
            const hasItems = currentOrderIds.length > 0;
            orderDetailsSection.style.display = hasItems ? 'block' : 'none';
            totalSection.style.display = hasItems ? 'flex' : 'none';

            Object.values(orderItems).forEach(item => {
                const itemTotal = item.price * item.quantity;
                totalAmount += itemTotal;
                itemCount += item.quantity;

                const row = document.createElement('tr');
                row.innerHTML = `
                <td class="body-14-regular text-gry-500 py-[10px]">${item.name}</td>
                <td class="py-[10px] px-[10px]">
                    <div class="flex m-auto p-[5px] justify-between items-center border border-primary-500 gap-2 max-w-[125px] rounded-[8px]">
                        <button type="button" onclick="updateQuantity(${item.id}, -1)" class="w-[20px] h-[25px] flex items-center justify-center text-primary-500 text-[24px]">-</button>
                        <span class="body-16-semibold text-primary-500">${item.quantity}</span>
                        <button type="button" onclick="updateQuantity(${item.id}, 1)" class="w-[20px] h-[25px] flex items-center justify-center text-primary-500 text-[24px]">+</button>
                    </div>
                </td>
                <td class="py-[10px] text-right body-14-regular text-gry-500">${itemTotal.toFixed(2)}</td>
            `;
                orderItemsContainer.appendChild(row);
                updateProductCardButton(item.id);
            });

            itemCountElement.textContent = itemCount;
            totalAmountElement.textContent = totalAmount.toFixed(2);

            mappedButtonIds.forEach(id => {
                if (!currentOrderIds.includes(id)) {
                    updateProductCardButton(id);
                }
            });

            updateFormInputs();
        }

        function updateFormInputs() {
            document.getElementById('total_amount_input').value = totalAmount.toFixed(2);
            document.getElementById('order_items_input').value = JSON.stringify(orderItems);
            document.getElementById('payment_method_input').value = selectedPaymentMethod;
            document.getElementById('customer_id').value = selectedCustomer ? selectedCustomer.id : '';
        }

        function clearAllItems() {
            const idsToUpdate = Object.keys(orderItems);
            orderItems = {};
            idsToUpdate.forEach(id => {
                updateProductCardButton(id);
            });
            updateOrderDisplay();
        }

        function searchProducts() {
            const searchTerm = document.getElementById('product-search').value.toLowerCase();
            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach(card => {
                const productName = card.getAttribute('data-product-name');
                card.style.display = productName.includes(searchTerm) ? 'flex' : 'none';
            });
        }

        // function searchCustomer() {
        //     const phoneNumber = document.getElementById('phone-search').value.trim();
        //     const customerInfo = document.getElementById('customer-info');
        //     const noCustomerFound = document.getElementById('no-customer-found');
        //     const balanceSection = document.getElementById('balance-section');
        //     const customerDivider = document.getElementById('customer-divider');

        //     const existingDropdown = document.getElementById('customer-dropdown');
        //     if (existingDropdown) {
        //         existingDropdown.remove();
        //     }

        //     customerInfo.style.display = 'none';
        //     noCustomerFound.style.display = 'none';
        //     balanceSection.style.display = 'none';
        //     customerDivider.style.display = 'none';

        //     if (phoneNumber.length === 0) {
        //         selectedCustomer = null;
        //         updateFormInputs();
        //         return;
        //     }

        //     const matchingCustomers = customers.filter(c =>
        //         c.phone_number && c.phone_number.toString().includes(phoneNumber)
        //     );

        //     if (matchingCustomers.length > 0) {
        //         const exactMatch = matchingCustomers.find(c =>
        //             c.phone_number.toString() === phoneNumber
        //         );

        //         if (exactMatch) {
        //             selectCustomer(exactMatch);
        //         } else if (matchingCustomers.length === 1) {
        //             selectCustomer(matchingCustomers[0]);
        //         } else {
        //             showCustomerDropdown(matchingCustomers);
        //         }
        //     } else {
        //         selectedCustomer = null;
        //         noCustomerFound.style.display = 'block';
        //         customerDivider.style.display = 'block';
        //         updateFormInputs();
        //     }
        // }



        // function showCustomerDropdown(matchedCustomers) {
        //     const phoneSearchContainer = document.getElementById('phone-search-container');
        //     const dropdown = document.createElement('div');
        //     dropdown.id = 'customer-dropdown';

        //     matchedCustomers.forEach(customer => {
        //         const item = document.createElement('div');
        //         item.className = 'customer-dropdown-item';
        //         item.innerHTML = `
        //         <div style="font-weight: 600; color: #1f2937;">${customer.name || 'Unknown'}</div>
        //         <div style="font-size: 0.875rem; color: #4b5563;">${customer.phone_number || ''}</div>
        //     `;
        //         item.onclick = function() {
        //             selectCustomer(customer);
        //             document.getElementById('phone-search').value = customer.phone_number;
        //             dropdown.remove();
        //         };
        //         dropdown.appendChild(item);
        //     });

        //     phoneSearchContainer.appendChild(dropdown);

        //     setTimeout(() => {
        //         document.addEventListener('click', function closeDropdown(e) {
        //             if (!phoneSearchContainer.contains(e.target)) {
        //                 dropdown.remove();
        //                 document.removeEventListener('click', closeDropdown);
        //             }
        //         });
        //     }, 100);
        // }


    function searchCustomer() {
    // Get the search input value and convert it to lowercase for case-insensitive searching
    const searchValue = document.getElementById('phone-search').value.trim().toLowerCase();
    const customerInfo = document.getElementById('customer-info');
    const noCustomerFound = document.getElementById('no-customer-found');
    const balanceSection = document.getElementById('balance-section');
    const customerDivider = document.getElementById('customer-divider');

    const existingDropdown = document.getElementById('customer-dropdown');
    if (existingDropdown) {
        existingDropdown.remove();
    }

    // Reset UI elements
    customerInfo.style.display = 'none';
    noCustomerFound.style.display = 'none';
    balanceSection.style.display = 'none';
    customerDivider.style.display = 'none';

    if (searchValue.length === 0) {
        selectedCustomer = null;
        updateFormInputs();
        return;
    }

    // --- Core Logic: Filter by Phone Number OR Name ---
    const matchingCustomers = customers.filter(c => {
        const phoneNumberStr = c.phone_number ? c.phone_number.toString() : '';
        const nameStr = c.name ? c.name.toLowerCase() : '';
        
        // Match if the search value is included in the phone number OR the name
        return phoneNumberStr.includes(searchValue) || nameStr.includes(searchValue);
    });
    // --------------------------------------------------

    if (matchingCustomers.length > 0) {
        // Check for an exact match on phone number or a full match on name
        const exactMatch = matchingCustomers.find(c =>
            (c.phone_number && c.phone_number.toString() === searchValue) || 
            (c.name && c.name.toLowerCase() === searchValue)
        );

        if (exactMatch) {
            selectCustomer(exactMatch);
        } else if (matchingCustomers.length === 1) {
            selectCustomer(matchingCustomers[0]);
        } else {
            showCustomerDropdown(matchingCustomers);
        }
    } else {
        selectedCustomer = null;
        noCustomerFound.style.display = 'block';
        customerDivider.style.display = 'block';
        updateFormInputs();
    }
}

            function showCustomerDropdown(matchedCustomers) {
    const phoneSearchContainer = document.getElementById('phone-search-container');
    const dropdown = document.createElement('div');
    dropdown.id = 'customer-dropdown';

    matchedCustomers.forEach(customer => {
        const item = document.createElement('div');
        // Added a style for better visual separation for phone/name in the dropdown
        item.className = 'customer-dropdown-item hover:bg-gray-100 p-2 cursor-pointer border-b last:border-b-0'; 
        item.innerHTML = `
            <div style="font-weight: 600; color: #1f2937;">${customer.name || 'Unknown'}</div>
            <div style="font-size: 0.875rem; color: #4b5563;">${customer.phone_number || ''}</div>
        `;
        item.onclick = function() {
            selectCustomer(customer);
            // Optionally, update the input field with the selected customer's name or phone
            document.getElementById('phone-search').value = customer.name || customer.phone_number; 
            dropdown.remove();
        };
        dropdown.appendChild(item);
    });

    // Added some basic positioning styles for the dropdown to sit below the input
    dropdown.style.position = 'absolute';
    dropdown.style.zIndex = '10';
    dropdown.style.width = '100%';
    dropdown.style.maxHeight = '200px';
    dropdown.style.overflowY = 'auto';
    dropdown.style.backgroundColor = '#fff';
    dropdown.style.border = '1px solid #ccc';
    dropdown.style.borderRadius = '5px';
    dropdown.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';

    phoneSearchContainer.appendChild(dropdown);

    // Keep the logic to close the dropdown on outside click
    setTimeout(() => {
        document.addEventListener('click', function closeDropdown(e) {
            if (!phoneSearchContainer.contains(e.target)) {
                dropdown.remove();
                document.removeEventListener('click', closeDropdown);
            }
        });
    }, 100);
}



        function selectCustomer(customer) {
            selectedCustomer = customer;
            document.getElementById('customer-name').textContent = customer.name || 'Unknown';
            document.getElementById('customer-phone').textContent = customer.phone_number || '';
            document.getElementById('available-balance').textContent = Number(customer.balance || 0).toFixed(2);
            document.getElementById('customer-info').style.display = 'flex';
            document.getElementById('balance-section').style.display = 'flex';
            document.getElementById('customer-divider').style.display = 'block';
            updateFormInputs();
        }

        function removeCustomer() {
            selectedCustomer = null;
            document.getElementById('phone-search').value = '';
            document.getElementById('customer-info').style.display = 'none';
            document.getElementById('no-customer-found').style.display = 'none';
            document.getElementById('balance-section').style.display = 'none';
            document.getElementById('customer-divider').style.display = 'none';
            const existingDropdown = document.getElementById('customer-dropdown');
            if (existingDropdown) {
                existingDropdown.remove();
            }
            updateFormInputs();
        }

        function selectPaymentMethod(method, button) {
            selectedPaymentMethod = method;
            document.querySelectorAll('.payment-method-btn').forEach(btn => {
                btn.classList.remove('!bg-blue-400', '!text-white', '!border-blue-400', 'selected');
                btn.classList.add('!bg-white', '!border', '!border-gray-300', '!text-gray-600');
            });
            button.classList.remove('!bg-white', '!border-gray-300', '!text-gray-600');
            button.classList.add('!bg-blue-400', '!text-white', '!border-blue-400', 'selected');
            updateFormInputs();
        }

        function validateForm() {
            if (Object.keys(orderItems).length === 0) {
                alert('Please add at least one product to the order.');
                return false;
            }
            if (totalAmount <= 0) {
                alert('Order total must be greater than 0.');
                return false;
            }
            if (!selectedCustomer) {
                alert('Please select or add a customer for this order.');
                return false;
            }
            return true;
        }

    //     function displayStockIssues() {
    //         const container = document.getElementById('stock-issues-list');
    //         if (!container || stockIssues.length === 0) return;

    //         container.innerHTML = '';

    //         const warehouseSelect = `
    //     <div class="mb-4">
    //         <label class="block text-sm font-medium text-gray-700 mb-2">
    //             Select Warehouse for All Products
    //         </label>
    //         <select 
    //             id="warehouse-select" 
    //             class="form-control w-full" 
    //             required
    //         >
    //             <option value="">-- Select Warehouse --</option>
    //             ${warehouses.map(wh => `<option value="${wh.id}">${wh.name}</option>`).join('')}
    //         </select>
    //     </div>
    // `;

    //         container.innerHTML += warehouseSelect;

    //         stockIssues.forEach((issue, index) => {
    //             const issueCard = document.createElement('div');
    //             issueCard.className = 'bg-pink-50 border border-pink-200 rounded-lg p-4';

    //             issueCard.innerHTML = `
    //         <div class="flex justify-between items-start mb-2">
    //             <div class="flex-1">
    //                 <h3 class="font-semibold text-gray-900 mb-2">${issue.product_name}</h3>
    //                 <div class="text-sm text-gray-700 space-y-1">
    //                     <p>Requested Quantity: <span class="font-semibold text-gray-900">${issue.requested}</span></p>
    //                     <p>Available Stock: <span class="font-semibold text-gray-900">${issue.available}</span></p>
    //                     <p class="text-pink-600 font-semibold mt-2">
    //                         Shortage: ${issue.requested - issue.available} units
    //                     </p>
    //                 </div>
    //             </div>
    //         </div>
    //     `;

    //             container.appendChild(issueCard);
    //         });
    //     }


    function displayStockIssues() {
    const container = document.getElementById('stock-issues-list');
    if (!container || stockIssues.length === 0) return;

    container.innerHTML = '';

    const warehouseSelect = `
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Select Warehouse for All Products
            </label>
            <select 
                id="warehouse-select" 
                class="form-control w-full" 
                required
            >
                <option value="">-- Select Warehouse --</option>
                ${warehouses.map(wh => `<option value="${wh.id}">${wh.name}</option>`).join('')}
            </select>
        </div>

        <!-- ✅ New Checkbox -->
        <div class="flex items-center mb-4">
            <input 
                id="collect-all-checkbox" 
                type="checkbox" 
                class="h-4 w-4 text-pink-600 border-gray-300 rounded focus:ring-pink-500"
            >
            <label for="collect-all-checkbox" class="ml-2 text-sm text-gray-700">
                Do you want to collect all stock from the warehouse?
            </label>
        </div>
    `;

    container.innerHTML += warehouseSelect;

    stockIssues.forEach((issue) => {
        const issueCard = document.createElement('div');
        issueCard.className = 'bg-pink-50 border border-pink-200 rounded-lg p-4';

        issueCard.innerHTML = `
            <div class="flex justify-between items-start mb-2">
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 mb-2">${issue.product_name}</h3>
                    <div class="text-sm text-gray-700 space-y-1">
                        <p>Requested Quantity: <span class="font-semibold text-gray-900">${issue.requested}</span></p>
                        <p>Available Stock: <span class="font-semibold text-gray-900">${issue.available}</span></p>
                        <p class="text-pink-600 font-semibold mt-2">
                            Shortage: ${issue.requested - issue.available} units
                        </p>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(issueCard);
    });
}


        function requestStockTransfer() {
            const warehouseSelect = document.getElementById('warehouse-select');
            const collectAllCheckbox = document.getElementById('collect-all-checkbox');
            if (!warehouseSelect.value) {
                warehouseSelect.classList.add('border-red-500');
                alert('Please select a warehouse.');
                return;
            } else {
                warehouseSelect.classList.remove('border-red-500');
            }

            const selectedWarehouseId = warehouseSelect.value;
            const collectAll = collectAllCheckbox ? collectAllCheckbox.checked : false;

            const transferRequests = stockIssues.map(issue => ({
                product_id: issue.product_id,
                quantity: issue.requested - issue.available,
                available: issue.available
            }));

            const saleId = @json(session('sale_id') ?? null);

            const customerId = selectedCustomer ? selectedCustomer.id : null;

            const requestBtn = document.getElementById('request-stock-btn');
            requestBtn.disabled = true;
            requestBtn.textContent = 'Sending Request...';

            fetch('{{ route('outlet.storeStockRequest') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                            '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        transfer_requests: transferRequests,
                        warehouse_id: selectedWarehouseId,
                        location_id: {{ auth()->user()->location_id ?? 1 }},
                        customer_id: customerId,
                        sale_id: saleId,
                        collect_all: collectAll
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => Promise.reject(new Error(text || response.statusText)));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById('stock-validation-modal').close();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Stock transfer request sent successfully. The sale will be created once the warehouse accepts the request.',
                            confirmButtonColor: '#ec188b',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '{{ route('outlet.salesOrders') }}';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message || 'Failed to send transfer request',
                            confirmButtonColor: '#ec188b',
                            confirmButtonText: 'OK'
                        });
                        requestBtn.disabled = false;
                        requestBtn.textContent = 'Request Stock Transfer';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while sending the transfer request.',
                        confirmButtonColor: '#ec188b',
                        confirmButtonText: 'OK'
                    });
                    requestBtn.disabled = false;
                    requestBtn.textContent = 'Request Stock Transfer';
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            restoreOrderState();

            document.querySelectorAll('.add-product-btn').forEach(button => {
                const productId = button.getAttribute('data-product-id');
                updateProductCardButton(productId);
            });

            document.querySelectorAll('.payment-method-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const method = this.getAttribute('data-method');
                    selectPaymentMethod(method, this);
                });
            });

            document.getElementById('sales-form').addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    return false;
                }
                const submitBtn = document.getElementById('create-sale-btn');
                submitBtn.textContent = 'Creating...';
                submitBtn.disabled = true;
            });

            document.getElementById('clear-all-btn').addEventListener('click', clearAllItems);
            document.getElementById('product-search').addEventListener('input', searchProducts);
            document.getElementById('phone-search').addEventListener('input', searchCustomer);
            document.getElementById('remove-customer-btn').addEventListener('click', removeCustomer);

            if (showStockModal && stockIssues.length > 0) {
                displayStockIssues();
                document.getElementById('stock-validation-modal').showModal();
            }

            updateOrderDisplay();
            updateFormInputs();
        });
    </script>

@endsection
