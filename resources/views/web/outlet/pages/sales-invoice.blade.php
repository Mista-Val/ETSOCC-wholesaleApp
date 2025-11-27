@extends('web.auth.app')
@section('content')
    @include('web.outlet.shared.header')
    <main class="dashboard-screen-bg relative">
        <section class="dashboard-title-section bg-white border-b border-gry-50">
            <div class="container-fluid">
                <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                    <h1 class="h6 text-gry-800">Sales Invoice</h1>
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
                        <a href="{{route('outlet.salesOrders')}}" class="body-14 text-gry-800 bold">Sales & Orders</a>
                        <span class="text-gry-300">/</span>
                        <span class="body-14 text-gry-800">Order Invoice</span>
                    </div>
                </div>
            </div>
        </section>
        <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
            <div class="container ">
                <div class="max-w-[600px] mx-auto">
                    <div id="invoice" class="bg-white mx-auto p-8 border border-gray-200 shadow-sm">


                        <div class="flex justify-between items-start">
                            <div>
                                <h1 class="text-3xl font-bold text-black">Invoice</h1>
                                <p class="text-gray-800">#{{ $sale->id }}</p>
                            </div>
                            <div>
                                <img src="/web/images/logo.svg" alt="Logo" class="h-16">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-0 mt-8 border border-gray-300">
                            <div class="p-4 border-r border-gray-300">
                                <h2 class="font-semibold text-black">Issued on</h2>
                                <p class="text-sm text-black">{{ $sale->created_at->format('d M, Y') }}</p>

                                <h2 class="mt-6 font-semibold text-black">Payment Method</h2>
                                <p class="text-sm text-black">{{ ucfirst($sale->payment_method) }}</p>
                            </div>
                            <div class="p-4">
                                <h2 class="font-semibold text-black">Billed to</h2>
                                <p class="text-sm text-black">{{ ucfirst($sale->customer->name) ?? '-' }}</p>
                                <p class="text-sm text-black">{{ $sale->customer->phone_number ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="mt-8">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-300 text-left">
                                        <th class="py-2 text-black font-semibold">Item</th>
                                        <th class="py-2 text-center text-black font-semibold">Qty</th>
                                        <th class="py-2 text-right text-black font-semibold">Unit Price</th>
                                        <th class="py-2 text-right text-black font-semibold">Total Price</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($sale->soldProducts as $item)
                                        <tr>
                                            <td class="py-3 text-black">{{ $item->product->name ?? '-' }}</td>
                                            <td class="py-3 text-center text-black">{{ $item->quantity }}</td>
                                            <td class="py-3 text-right text-black">${{ number_format($item->per_unit_amount, 2) }}</td>
                                            <td class="py-3 text-right text-black">${{ number_format($item->total_product_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="border-t border-gray-300 font-semibold">
                                        <td colspan="3" class="py-3 text-right text-black">Total</td>
                                        <td class="py-3 text-right text-black">${{ number_format($sale->total_amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-12">
                            <p class="font-medium text-black">Thank you for the business!</p>
                        </div>
                        @if($sale->type == 'unavailable')
                         <div class="mt-2">
                            <p class="font-semibold text-black">This is accepted by the warehouse.</p>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="flex justify-center mt-10">
                    <button class="btn btn-primary" onclick="downloadPDF()">Download Invoice</button>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const element = document.getElementById("invoice");

            const opt = {
                margin: 0,
                filename: "invoice-{{ $sale->id ?? '1' }}.pdf",
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