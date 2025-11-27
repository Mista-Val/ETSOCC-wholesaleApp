@extends('web.auth.app')
@section('content')
    @include('web.warehouse.shared.header')
    <main class="dashboard-screen-bg relative">
        <section class="dashboard-title-section bg-white border-b border-gry-50">
            <div class="container-fluid">
                <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                    <h1 class="h6 text-gry-800">Waybill Invoice</h1>
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
                        <a href="{{route('warehouse.salesOrders')}}" class="body-14 text-gry-800 bold">Sales & Orders</a>
                        <span class="text-gry-300">/</span>
                        <span class="body-14 text-gry-800">Waybill Invoice</span>
                    </div>
                </div>
            </div>
        </section>
        <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
            <div class="container">
                <div class="max-w-[800px] mx-auto">
                    <div id="waybill-invoice" class="bg-white mx-auto p-8 border border-gray-200 shadow-sm">

                        {{-- Header --}}
                        <div class="flex justify-between items-start mb-8">
                            <div>
                                <h1 class="text-3xl font-bold">Waybill Invoice</h1>
                                <p class="text-gray-400">{{ $waybill->waybill_number }}</p>
                            </div>
                            <div>
                                <img src="/web/images/logo.svg" alt="Logo" class="h-16">
                            </div>
                        </div>

                        {{-- Order Information --}}
                        <div class="mb-6 p-4 bg-pink-50 rounded-lg border border-pink-200">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">Order ID</p>
                                    <p class="text-base text-gray-900">#{{ $waybill->sale->id }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">Customer Name</p>
                                    <p class="text-base text-gray-900">{{ ucwords($waybill->sale->customer->name) }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Shipment Details --}}
                        <div class="grid grid-cols-2 gap-8 mb-6 border border-gray-200 divide-x">
                            <div class="p-4">
                                <h2 class="font-semibold mb-4 text-lg">Shipment Information</h2>
                                
                                <div class="mb-3">
                                    <p class="text-sm font-semibold text-gray-700">Loading Date</p>
                                    <p class="text-sm text-gray-600">{{ $waybill->loading_date->format('d M, Y') }}</p>
                                </div>

                                <div class="mb-3">
                                    <p class="text-sm font-semibold text-gray-700">Estimated Delivery</p>
                                    <p class="text-sm text-gray-600">{{ $waybill->estimated_delivery_date->format('d M, Y') }}</p>
                                </div>

                                <div class="mb-3">
                                    <p class="text-sm font-semibold text-gray-700">Status</p>
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $waybill->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $waybill->status === 'in_transit' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $waybill->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $waybill->status)) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <h2 class="font-semibold mb-4 text-lg">Package Details</h2>
                                
                                <div class="mb-3">
                                    <p class="text-sm font-semibold text-gray-700">Number of Packages</p>
                                    <p class="text-sm text-gray-600">{{ $waybill->number_of_packages }}</p>
                                </div>

                                <div class="mb-3">
                                    <p class="text-sm font-semibold text-gray-700">Total Quantity</p>
                                    <p class="text-sm text-gray-600">{{ $waybill->quantity }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- From/To Section --}}
                        <div class="grid grid-cols-2 gap-8 mb-6 border border-gray-200 divide-x">
                            <div class="p-4">
                                <h2 class="font-semibold mb-4 text-lg">From (Warehouse)</h2>
                                
                                <div class="mb-3">
                                    <p class="text-sm font-semibold text-gray-700">Warehouse Name</p>
                                    <p class="text-sm text-gray-600">{{ $waybill->warehouse_name }}</p>
                                </div>

                                <div class="mb-3">
                                    <p class="text-sm font-semibold text-gray-700">Loader Name</p>
                                    <p class="text-sm text-gray-600">{{ $waybill->loader_name }}</p>
                                </div>

                                <div class="mb-3">
                                    <p class="text-sm font-semibold text-gray-700">Loader Position</p>
                                    <p class="text-sm text-gray-600">{{ $waybill->loader_position }}</p>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <h2 class="font-semibold mb-4 text-lg">To (Outlet)</h2>
                                
                                <div class="mb-3">
                                    <p class="text-sm font-semibold text-gray-700">Outlet Name</p>
                                    <p class="text-sm text-gray-600">{{ ucwords($waybill->outlet->name) }}</p>
                                </div>

                                <div class="mb-3">
                                    <p class="text-sm font-semibold text-gray-700">Receiver Name</p>
                                    <p class="text-sm text-gray-600">{{ $waybill->receiver_name }}</p>
                                </div>

                                <div class="mb-3">
                                    <p class="text-sm font-semibold text-gray-700">Receiver Position</p>
                                    <p class="text-sm text-gray-600">{{ $waybill->receiver_position }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Products Table --}}
                        <div class="mb-6">
                            <h2 class="font-semibold mb-4 text-lg">Products</h2>
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="border-b text-left">
                                        <th class="py-2">Item</th>
                                        <th class="py-2 text-center">Qty</th>
                                        <th class="py-2 text-right">Unit Price</th>
                                        <th class="py-2 text-right">Total Price</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach ($waybill->sale->soldProducts as $item)
                                        <tr>
                                            <td class="py-3">{{ $item->product->name ?? '-' }}</td>
                                            <td class="py-3 text-center">{{ $item->quantity }}</td>
                                            <td class="py-3 text-right">${{ number_format($item->per_unit_amount, 2) }}</td>
                                            <td class="py-3 text-right">${{ number_format($item->total_product_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="border-t font-semibold">
                                        <td colspan="3" class="py-3 text-right">Total</td>
                                        <td class="py-3 text-right">${{ number_format($waybill->sale->total_amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Shipping Remarks --}}
                        @if($waybill->shipping_remarks)
                            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <h2 class="font-semibold mb-2">Shipping Remarks</h2>
                                <p class="text-sm text-gray-600">{{ $waybill->shipping_remarks }}</p>
                            </div>
                        @endif

                        {{-- Footer --}}
                        {{-- <div class="mt-8 pt-6 border-t border-gray-200">
                            <p class="font-medium">Thank you for the business!</p>
                            <p class="text-xs text-gray-500 mt-2">Generated on {{ $waybill->created_at->format('d M, Y h:i A') }}</p>
                        </div> --}}
                    </div>
                </div>
                
                {{-- Download Button --}}
                <div class="flex justify-center mt-10">
                    <button class="btn btn-primary" onclick="downloadPDF()" title="Download Waybill as PDF">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Waybill
                    </button>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const element = document.getElementById("waybill-invoice");

            const opt = {
                margin: 0,
                filename: "waybill-{{ $waybill->waybill_number }}.pdf",
                image: {
                    type: "jpeg",
                    quality: 1
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true
                },
                jsPDF: {
                    unit: "mm",
                    format: "a4",
                    orientation: "portrait"
                }
            };

            window.html2pdf().set(opt).from(element).save();
        }
    </script>
@endpush