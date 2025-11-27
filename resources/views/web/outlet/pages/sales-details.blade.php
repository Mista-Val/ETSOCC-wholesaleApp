@extends('web.auth.app')
@section('content')
@include('web.outlet.shared.header')

<main class="dashboard-screen-bg relative">
    <section class="dashboard-title-section bg-white border-b border-gry-50">
        <div class="container-fluid">
            <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                <h1 class="h6 text-gry-800">Sales & Orders Details</h1>
                <div class="breadcrumb flex items-center gap-[10px] flex-wrap">
                     <a href="{{ route('outlet.outlet-dashboard') }}">
                        <!-- Back icon -->
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.39173 2.34954L2.61673 6.97453C1.96673 7.4912 1.55006 8.5829 1.69172 9.39956L2.80006 16.0329C3.00006 17.2162 4.13339 18.1745 5.33339 18.1745H14.6667C15.8584 18.1745 17.0001 17.2079 17.2001 16.0329L18.3084 9.39956C18.4417 8.5829 18.0251 7.4912 17.3834 6.97453L11.6084 2.35789C10.7167 1.64122 9.27506 1.64121 8.39173 2.34954Z"
                                  stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round"
                                  stroke-linejoin="round"/>
                            <path d="M10.0001 12.9167C11.1507 12.9167 12.0834 11.9839 12.0834 10.8333C12.0834 9.68274 11.1507 8.75 10.0001 8.75C8.84949 8.75 7.91675 9.68274 7.91675 10.8333C7.91675 11.9839 8.84949 12.9167 10.0001 12.9167Z"
                                  stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <span class="text-gry-300">/</span>
                    <a href="{{route('outlet.salesOrders')}}" class="body-14 text-gry-800 bold">Sales & Orders</a>
                    <span class="text-gry-300">/</span>
                    <span class="body-14 text-gry-800">Order Details</span>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
        <div class="container-fluid">
            <div class="white-box">
                <div
                    class="bg-white box-shadow-[0px_1px_78px_rgba(0,0,0,0.06)] rounded-[16px] mb-[15px] p-[15px] border border-disabled-gry-100">
                    <div class="flex gap-[15px] flex-col">

                        <div class="border-b flex flex-col gap-[5px] border-disabled-gry-100 pb-[15px]">
                            <p class="body-16-regular text-gry-900">Order ID</p>
                            <p class="body-16-semibold text-gry-900">#{{ $sale->id }}</p>
                        </div>

                        <div class="border-b flex flex-col gap-[5px] border-disabled-gry-100 pb-[15px]">
                            <p class="body-16-regular text-gry-900">Customer Name</p>
                            <p class="body-16-semibold text-gry-900">{{ ucfirst($sale->customer->name) ?? '-' }}</p>
                        </div>


                         <div class="border-b flex flex-col gap-[5px] border-disabled-gry-100 pb-[15px]">
                            <p class="body-16-regular text-gry-900">Customer Address</p>
                            <p class="body-16-semibold text-gry-900">{{ ucfirst($sale->customer->address) ?? '-' }}</p>
                        </div>

                        <div class="border-b flex flex-col gap-[5px] border-disabled-gry-100 pb-[15px]">
                            <p class="body-16-regular text-gry-900">Payment Method</p>
                            <p class="body-16-semibold text-gry-900">{{ $sale->payment_method }}</p>
                        </div>

                        <div class="border-b flex flex-col gap-[5px] border-disabled-gry-100 pb-[15px]">
                            <p class="body-16-regular text-gry-900">Date</p>
                            <p class="body-16-semibold text-gry-900">{{ $sale->created_at->format('Y-m-d') }}</p>
                        </div>

                        <div class="border-b flex flex-col gap-[5px] border-disabled-gry-100 pb-[15px]">
                            <p class="body-16-regular text-gry-900">Total Amount</p>
                            <p class="body-16-semibold text-gry-900">$ {{ number_format($sale->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="h6 mb-2">Items List</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap text-sm text-left text-gray-600">
                            <thead class="text-gry-900 semibold body-14-regular uppercase border-b border-[#E9E9E9]">
                                <tr>
                                    <th class="px-6 py-3">Item Name</th>
                                    <th class="px-6 py-3">Unit Price</th>
                                    <th class="px-6 py-3">Quantity</th>
                                    <th class="px-6 py-3">Total Price</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 body-14-regular text-gry-500">
                                @foreach ($sale->soldProducts as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3">{{ ucfirst($item->product->name) ?? '-' }}</td>
                                        <td class="px-6 py-3">$ {{ number_format($item->per_unit_amount, 2) }}</td>
                                        <td class="px-6 py-3">{{ $item->quantity }}</td>
                                        <td class="px-6 py-3">$ {{ number_format($item->total_product_amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($sale->remark)
                    <div class="mt-[15px]">
                        <h3 class="h6 text-gry-900">Remark</h3>
                        <p class="body-14-regular text-gry-500">{{ ucfirst($sale->remark) }}</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</main>
@endsection
