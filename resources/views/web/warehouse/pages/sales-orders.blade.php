@extends('web.auth.app')
@section('content')
    @include('web.warehouse.shared.header')

    {{-- Waybill Invoice Modal --}}
    <div id="waybill-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="modal-box bg-white rounded-xl shadow-lg w-full max-w-4xl p-0 relative">
            <div class="flex items-center justify-between py-4 px-6 border-b border-gray-200 bg-white">
                <h2 class="text-xl text-gray-800 font-semibold">Generate Waybill Invoice</h2>
                <button type="button" onclick="closeWaybillModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="41" height="42" viewBox="0 0 41 42" fill="none">
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

            <div class="modal-content p-6">
                <form method="POST" action="" id="waybill-form">
                    @csrf

                    {{-- Sale Information Display --}}
                    <div class="mb-6 p-4 bg-pink-50 rounded-lg border border-pink-200">
                        <p class="text-base text-gray-700 mb-2">
                            <span class="font-semibold">Order ID:</span>
                            <span id="modal-order-id" class="text-gray-900"></span>
                        </p>
                        <p class="text-base text-gray-700">
                            <span class="font-semibold">Customer:</span>
                            <span id="modal-customer-name" class="text-gray-900"></span>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Waybill Number --}}
                        <div class="form-group">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Waybill Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="waybill_number" required
                                class="form-control w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                placeholder="Enter waybill number" />
                        </div>

                        {{-- Loading Date --}}
                        {{-- <div class="form-group">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Loading Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="loading_date" required
                                class="form-control w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500" />
                        </div> --}}

                        <!-- Loading Date -->
                        <div class="form-group">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Loading Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="loading_date" id="loading_date" required
                                class="date-picker-input form-control w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 cursor-pointer" />
                        </div>

                        <!-- Estimated Delivery Date -->
                        <div class="form-group">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Estimated Delivery Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="estimated_delivery_date" id="estimated_delivery_date" required
                                class="date-picker-input form-control w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 cursor-pointer" />
                        </div>

                        {{-- Warehouse Name (Read-only from logged-in warehouse) --}}
                        <div class="form-group">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Warehouse Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="warehouse_name"
                                value="{{ auth()->guard('warehouse')->user()->warehouse->name ?? '' }}" readonly
                                class="form-control w-full border border-gray-300 rounded-lg px-4 py-3 bg-gray-100 cursor-not-allowed"
                                placeholder="Warehouse name" />
                        </div>

                        {{-- Loader Name --}}
                        <div class="form-group">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Loader Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="loader_name" required
                                class="form-control w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                placeholder="Enter loader name" />
                        </div>

                        {{-- Loader Position --}}
                        <div class="form-group">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Loader Position <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="loader_position" required
                                class="form-control w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                placeholder="Enter loader position" />
                        </div>

                        {{-- Outlet Name (Dropdown from locations where type='outlet') --}}
                        <div class="form-group">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Outlet Name <span class="text-red-500">*</span>
                            </label>
                            <select name="outlet_id" required
                                class="form-control w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Select Outlet</option>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}">{{ ucwords($outlet->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Number of Packages --}}
                        <div class="form-group">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Number of Packages <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="number_of_packages" min="1" required
                                class="form-control w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                placeholder="Enter number of packages" />
                        </div>

                        {{-- Quantity --}}
                        <div class="form-group">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Quantity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="quantity" min="1" required
                                class="form-control w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                placeholder="Enter quantity" />
                        </div>

                        {{-- Receiver Name --}}
                        <div class="form-group">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Receiver Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="receiver_name" required
                                class="form-control w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                placeholder="Enter receiver name" />
                        </div>

                        {{-- Receiver Position --}}
                        <div class="form-group">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Receiver Position <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="receiver_position" required
                                class="form-control w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                placeholder="Enter receiver position" />
                        </div>
                    </div>

                    {{-- Shipping Remarks --}}
                    <div class="form-group mt-5">
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Shipping Remarks
                        </label>
                        <textarea name="shipping_remarks" rows="4"
                            class="form-control w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                            placeholder="Enter any shipping remarks or special instructions"></textarea>
                    </div>

                    <button type="submit"
                        class="w-full mt-6 bg-pink-500 hover:bg-pink-600 text-white font-semibold text-base py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Generate Waybill Invoice</span>
                    </button>
                </form>
            </div>
        </div>
    </div>


    {{-- Refund Modal --}}
    <div id="refund-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="modal-box bg-white rounded-xl shadow-lg w-full max-w-2xl p-0 relative">
            <div class="flex items-center justify-between py-4 px-6 border-b border-gray-200 bg-white">
                <h2 class="text-xl text-gray-800 font-semibold">Process Refund</h2>
                <button type="button" onclick="closeRefundModal()">
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

            <div class="modal-content p-6">
                <form method="POST" action="" id="refund-form">
                    @csrf

                    {{-- Sale Information Display --}}
                    <div class="mb-6 p-4 bg-pink-50 rounded-lg border border-pink-200">
                        <p class="text-base text-gray-700 mb-2">
                            <span class="font-semibold">Order ID:</span>
                            <span id="refund-modal-order-id" class="text-gray-900"></span>
                        </p>
                        <p class="text-base text-gray-700 mb-2">
                            <span class="font-semibold">Customer:</span>
                            <span id="refund-modal-customer-name" class="text-gray-900"></span>
                        </p>
                        <p class="text-base text-gray-700">
                            <span class="font-semibold">Sale Amount:</span>
                            <span id="refund-modal-sale-amount" class="text-gray-900 font-bold"></span>
                        </p>
                    </div>

                    {{-- Hidden field to store sale amount for validation --}}
                    <input type="hidden" id="refund-max-amount" value="0">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Refund Amount --}}
                        <div class="form-group">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Refund Amount <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold"></span>
                                <input type="number" name="refund_amount" id="refund-amount-input" step="0.01"
                                    min="0.01" required
                                    class="form-control w-full border border-gray-300 rounded-lg pl-8 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                    oninput="validateRefundAmount()" />
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Maximum refund amount: <span id="max-refund-display"
                                    class="font-semibold">$0.00</span></p>
                            <p id="refund-amount-error" class="text-xs text-red-500 mt-1 hidden"></p>
                        </div>

                        {{-- Supervisor Dropdown --}}
                        <div class="form-group">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Supervisor <span class="text-red-500">*</span>
                            </label>
                            <select name="supervisor_id" required
                                class="form-control w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Select Supervisor</option>
                                @foreach ($supervisors as $supervisor)
                                    <option value="{{ $supervisor->id }}">{{ ucwords($supervisor->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Refund Reason (Input Field) --}}
                        <div class="form-group md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Refund Reason <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="refund_reason" required
                                class="form-control w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                placeholder="Enter refund reason" />
                        </div>

                        {{-- Refund Notes --}}
                        {{-- <div class="form-group md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Additional Notes
                            </label>
                            <textarea name="refund_notes" rows="4"
                                class="form-control w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                placeholder="Enter any additional details about the refund..."></textarea>
                        </div> --}}
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="button" onclick="closeRefundModal()"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold text-base py-3 px-4 rounded-lg transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit" id="refund-submit-btn"
                            class="flex-1 bg-pink-500 hover:bg-pink-600 text-white font-semibold text-base py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                            </svg>
                            <span>Process Refund</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <main class="dashboard-screen-bg relative">
        <section class="dashboard-title-section bg-white border-b border-gry-50">
            <div class="container-fluid">
                <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                    <h1 class="h6 text-gry-800">Sales & Orders</h1>
                    <div class="breadcrumb flex items-center gap-[10px] flex-wrap">
                        <a href="{{ route('warehouse.dashboard') }}">
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
                        <span class="body-14 text-gry-800 bold">Sales & Orders</span>
                    </div>
                </div>
            </div>
        </section>
        <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
            <div class="container-fluid">
                <form method="GET" action="{{ route('warehouse.salesOrders') }}"
                    class="search-record-box flex items-center justify-between flex-wrap gap-[15px] mb-[15px]"
                    id="warehouseSalesFilterForm">

                    <div class="search-box relative lg:max-w-[390px] w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search"
                            class="form-control !pr-[50px]">
                        <button type="submit" class="absolute top-1/2 right-[15px] translate-y-[-50%]">
                            <img src="{{ asset('web/images/search.svg') }}" alt="search" />
                        </button>
                    </div>

                    <div class="date-record-btn w-full md:w-auto flex items-center gap-[15px] flex-wrap md:flex-nowrap">
                        <input type="date" name="date" value="{{ request('date') }}"
                            class="form-control max-w-[100%] " placeholder="Date" id="customDate"
                            onchange="document.getElementById('warehouseSalesFilterForm').submit()" />

                        <a href="{{ route('warehouse.salesOrders') }}" class="btn btn-secondary">Clear Filter</a>
                        <a href="{{ route('warehouse.createSales') }}" class="btn btn-primary">
                            Create Sale
                        </a>
                    </div>
                </form>
                <div class="white-box p-[0]">
                    <!-- Table Container -->
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap text-sm text-left text-gray-600">
                            <thead class="text-gry-900 semibold body-14-regular uppercase border-b border-[#E9E9E9]">
                                <tr>
                                    <th class="px-6 py-3">Order ID</th>
                                    <th class="px-6 py-3">Customer Name</th>
                                    <th class="px-6 py-3">Phone</th>
                                    <th class="px-6 py-3">Date</th>
                                    <th class="px-6 py-3">Payment Method</th>
                                    <th class="px-6 py-3">Total Amount</th>
                                    <th class="px-6 py-3">Refund Status</th>
                                    <th class="px-6 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 body-14-regular text-gry-500">
                                @forelse ($sales as $sale)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3">#{{ $sale->id }}</td>
                                        <td class="px-6 py-3">{{ ucwords($sale->customer->name) }}</td>
                                        <td class="px-6 py-3">{{ $sale->customer->phone_number }}</td>
                                        <td class="px-6 py-3">{{ $sale->created_at->format('Y-m-d') }}</td>
                                        <td class="px-6 py-3">{{ $sale->payment_method }}</td>
                                        <td class="px-6 py-3">${{ number_format($sale->total_amount, 2) }}</td>
                                        @if ($sale->refund_status === 'refunded')
                                            <td class="px-6 py-3">
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'text-[var(--color-status-warning-800)]',
                                                        'refunded' => 'text-[var(--color-status-success-700)]',
                                                    ];
                                                @endphp
                                                <span
                                                    class="{{ $statusColors[$sale->refund_status] ?? 'text-gray-500' }} body-14-semibold">
                                                    {{ ucfirst($sale->refund_status ?? '-') }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-6 py-3">
                                                <span class="">
                                                    {{ '-' }}
                                                </span>
                                            </td>
                                        @endif
                                        {{-- <td class="px-6 py-3">{{ $sale->refund_status }}</td> --}}
                                        {{-- <td class="px-6 py-3">
                                            <div class="flex items-center gap-[10px]">
                                                <a href="{{ route('warehouse.salesDetails', $sale->id) }}"
                                                    class="p-2 rounded-full bg-pink-100 text-pink-500 hover:bg-pink-200"
                                                    title="View Details">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                </a>

                                               
                                                <a href="{{ route('warehouse.salesInvoice', $sale->id) }}"
                                                    class="p-2 rounded-full bg-pink-100 text-pink-500 hover:bg-pink-200 w-[32px] h-[32px] flex items-center justify-center"
                                                    title="Download Invoice">
                                                    <i class="iconsax" icon-name="document-download"></i>
                                                </a> --}}

                                        {{-- Conditional Waybill Button --}}
                                        {{-- Action Buttons in Sales Table --}}
                                        <td class="px-6 py-3">
                                            <div class="flex items-center gap-[10px]">
                                                {{-- View Details Button --}}
                                                <a href="{{ route('warehouse.salesDetails', $sale->id) }}"
                                                    class="p-2 rounded-full bg-pink-100 text-pink-500 hover:bg-pink-200"
                                                    title="View Details">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                </a>

                                                {{-- Download Invoice Button --}}
                                                <a href="{{ route('warehouse.salesInvoice', $sale->id) }}"
                                                    class="p-2 rounded-full bg-pink-100 text-pink-500 hover:bg-pink-200 w-[32px] h-[32px] flex items-center justify-center"
                                                    title="Download Sales Invoice">
                                                    <i class="iconsax" icon-name="document-download"></i>
                                                </a>

                                                {{-- Conditional Waybill Button --}}
                                                @if ($sale->waybill && $sale->waybill->status === 'delivered')
                                                    {{-- Download Waybill Button (if waybill status is delivered) --}}
                                                    <a href="{{ route('warehouse.viewWaybillInvoice', $sale->waybill->id) }}"
                                                        class="p-2 rounded-full bg-green-100 text-green-600 hover:bg-green-200 w-[32px] h-[32px] flex items-center justify-center"
                                                        title="Download Waybill Invoice">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    </a>
                                                @else
                                                    {{-- Generate Waybill Button (if waybill doesn't exist or status is not delivered) --}}
                                                    <button
                                                        onclick='openWaybillModal({{ $sale->id }}, "#{{ $sale->id }}", "{{ addslashes($sale->customer->name) }}")'
                                                        class="p-2 rounded-full bg-pink-100 text-pink-500 hover:bg-pink-200 w-[32px] h-[32px] flex items-center justify-center"
                                                        title="Generate Waybill Invoice">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    </button>
                                                @endif
                                                @if ($sale->refund_status === 'pending')
                                                    <button
                                                        onclick='openRefundModal({{ $sale->id }}, "#{{ $sale->id }}", "{{ addslashes($sale->customer->name) }}")'
                                                        class="p-2 rounded-full bg-red-100 text-red-500 hover:bg-red-200 w-[32px] h-[32px] flex items-center justify-center"
                                                        title="Process Refund">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>

                    </div>
                    </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-3 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" class="w-16 h-16">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                </svg>
                                <h3><strong>No sales found.</strong></h3>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                {{-- <div
                    class="flex flex-wrap gap-[15px] items-center justify-between mt-4 border-t border-[#E9E9E9] p-[15px]">
                    {{ $sales->links() }}
                </div> --}}
                {{-- Replace your entire pagination section with this --}}
                @if ($sales->total() > 0)
                    <div class="border-t border-[#E9E9E9] p-[15px]">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                Showing {{ $sales->firstItem() }} to {{ $sales->lastItem() }} of {{ $sales->total() }}
                                results
                            </div>
                            <div>
                                {{ $sales->withQueryString()->links('vendor.pagination.custom-new') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            </div>
        </section>
    </main>

    <script>
        /**
         * Open Waybill Modal
         */
        function openWaybillModal(saleId, orderId, customerName) {
            const modal = document.getElementById('waybill-modal');
            const form = document.getElementById('waybill-form');

            // Set modal display info
            document.getElementById('modal-order-id').textContent = orderId;
            document.getElementById('modal-customer-name').textContent = customerName;

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Set form action URL
            const waybillUrlBase = "{{ route('warehouse.generateWaybill', ['sale' => 'PLACEHOLDER']) }}";
            form.action = waybillUrlBase.replace('PLACEHOLDER', saleId);

            // Set today's date as default for loading date
            const today = new Date().toISOString().split('T')[0];
            document.querySelector('input[name="loading_date"]').value = today;
        }

        /**
         * Close Waybill Modal
         */
        function closeWaybillModal() {
            const modal = document.getElementById('waybill-modal');
            const form = document.getElementById('waybill-form');

            modal.classList.add('hidden');
            modal.classList.remove('flex');

            // Reset form
            form.reset();
            form.action = '';
        }

        /**
         * Open Refund Modal
         */
        function openRefundModal(saleId, orderId, customerName) {
            const modal = document.getElementById('refund-modal');
            const form = document.getElementById('refund-form');

            // Fetch sale details via AJAX
            fetch(`/warehouse/sales/${saleId}/get-details`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Set modal display info
                    document.getElementById('refund-modal-order-id').textContent = orderId;
                    document.getElementById('refund-modal-customer-name').textContent = customerName;
                    document.getElementById('refund-modal-sale-amount').textContent =
                        `$${parseFloat(data.total_amount).toFixed(2)}`;

                    // Store max refund amount
                    document.getElementById('refund-max-amount').value = data.total_amount;
                    document.getElementById('max-refund-display').textContent =
                        `$${parseFloat(data.total_amount).toFixed(2)}`;

                    // Reset refund amount input
                    document.getElementById('refund-amount-input').value = '';
                    document.getElementById('refund-amount-error').classList.add('hidden');
                    document.getElementById('refund-submit-btn').disabled = false;

                    // Show modal
                    modal.style.display = 'flex';
                    modal.classList.remove('hidden');

                    // Set form action URL
                    form.action = `/warehouse/sales/${saleId}/refund`;
                })
                .catch(error => {
                    console.error('Error fetching sale details:', error);
                    alert('Error loading sale details. Please try again.');
                });
        }

        /**
         * Close Refund Modal
         */
        function closeRefundModal() {
            const modal = document.getElementById('refund-modal');
            const form = document.getElementById('refund-form');

            // Hide modal
            modal.style.display = 'none';
            modal.classList.add('hidden');

            // Reset form
            form.reset();
            form.action = '';
            document.getElementById('refund-amount-error').classList.add('hidden');
        }

        /**
         * Validate Refund Amount
         */
        function validateRefundAmount() {
            const refundInput = document.getElementById('refund-amount-input');
            const maxAmount = parseFloat(document.getElementById('refund-max-amount').value);
            const refundAmount = parseFloat(refundInput.value);
            const errorElement = document.getElementById('refund-amount-error');
            const submitBtn = document.getElementById('refund-submit-btn');

            if (isNaN(refundAmount) || refundAmount <= 0) {
                errorElement.textContent = 'Please enter a valid refund amount.';
                errorElement.classList.remove('hidden');
                submitBtn.disabled = true;
                return false;
            }

            if (refundAmount > maxAmount) {
                errorElement.textContent = `Refund amount cannot exceed $${maxAmount.toFixed(2)}`;
                errorElement.classList.remove('hidden');
                submitBtn.disabled = true;
                return false;
            }

            errorElement.classList.add('hidden');
            submitBtn.disabled = false;
            return true;
        }

        document.querySelectorAll('.date-picker-input').forEach(input => {
            input.addEventListener('click', function() {
                this.showPicker();
            });
        });

        /**
         * Handle Escape key to close modals
         */
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeWaybillModal();
                closeRefundModal();
            }
        });
    </script>
@endsection
