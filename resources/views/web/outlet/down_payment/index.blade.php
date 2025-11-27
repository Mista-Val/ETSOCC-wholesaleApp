@extends('web.auth.app')
@section('content')
    @include('web.outlet.shared.header')
    @php
        use Illuminate\Support\Str;
    @endphp
    <main class="dashboard-screen-bg relative">
        <section class="dashboard-title-section bg-white border-b border-gry-50">
            <div class="container-fluid">
                <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                    <h1 class="h6 text-gry-800">Down Payment</h1>
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
                        <span class="body-14 text-gry-800 bold">Down Payment</span>
                        <!-- <span class="text-gry-300">/</span>
                            <span class="body-14 text-gry-800">Down Payment</span> -->
                    </div>
                </div>
            </div>
        </section>
        <section class="dashboard-content py-[15px] md:py-[30px]">
            <div class="container-fluid">
                <div class="space-y-6">
                    <!-- Today's Overview -->
                    <div class="today-overview">
                        <!-- <h2 class="h6 mb-3">Today's Overview</h2> -->
                        <div class="grid gap-4 md:grid-cols-3 lg:grid-cols-3">
                            <div class="bg-white rounded-[16px] shadow-[0px_1px_78px_rgba(0,0,0,0.06)] p-4 flex flex-col">
                                <span class="body-18 semibold text-gry-800">Total Down Payments Made</span>
                                <div class="mt-2 flex items-center gap-[10px] flex-wrap">
                                    <div class="flex-1 flex flex-col gap-[5px]">
                                        <!-- <span class="semibold text-gry-400">Amount</span>    -->
                                        <span class="h5 text-gry-800">${{ $totalDownPayment }}</span>
                                    </div>
                                    <img src="{{ asset('web/images/d-icon1.svg') }}" alt="icon"
                                        class="w-[45px] basis-[45px]" />
                                </div>
                            </div>
                            <div class="bg-white rounded-[16px] shadow-[0px_1px_78px_rgba(0,0,0,0.06)] p-4 flex flex-col">
                                <span class="body-18 semibold text-gry-800">Total Used On Orders</span>
                                <div class="mt-2 flex items-center gap-[10px] flex-wrap">
                                    <div class="flex-1 flex flex-col gap-[5px]">
                                        <!-- <span class="semibold text-gry-400">Payments</span>    -->
                                        <span class="h5 text-gry-800">${{ $totalSalesDownPayment }}</span>
                                    </div>
                                    <img src="{{ asset('web/images/d-icon2.svg') }}" alt="icon"
                                        class="w-[45px] basis-[45px]" />
                                </div>
                            </div>
                            <div class="bg-white rounded-[16px] shadow-[0px_1px_78px_rgba(0,0,0,0.06)] p-4 flex flex-col">
                                <span class="body-18 semibold text-gry-800">Remaining Balance</span>
                                <div class="mt-2 flex items-center gap-[10px] flex-wrap">
                                    <div class="flex-1 flex flex-col gap-[5px]">
                                        <!-- <span class="semibold text-gry-400">Debt</span>    -->
                                        <span class="h5 text-gry-800">${{ $totalDownPayment - $totalSalesDownPayment }}</span>
                                    </div>
                                    <img src="{{ asset('web/images/d-icon3.svg') }}" alt="icon"
                                        class="w-[45px] basis-[45px]" />
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>
        <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
            <div class="container-fluid">
                <h2 class="body-18 semibold text-gry-800 mb-2">Record Customer Down Payment</h2>
                {{-- <form method="GET" action="{{ route('outlet.downPayment') }}" class="search-record-box flex items-center justify-between flex-wrap gap-[15px] mb-[15px]">
            <div class="search-box relative max-w-[390px] w-full">
               <input 
                  type="text" 
                  name="search" 
                  placeholder="Search" 
                  value="{{ request('search') }}" 
                  class="form-control !pr-[50px]" 
               />
               <button type="submit" class="absolute top-1/2 right-[15px] translate-y-[-50%]">
                  <img src="{{ asset('web/images/search.svg') }}" alt="search" />
               </button>
            </div>

            <div class="date-record-btn w-full md:w-auto flex items-center gap-[15px] flex-wrap md:flex-nowrap">
               <input type="date" name="date" value="{{ request('date') }}" class="form-control max-w-[100%] " 
                  placeholder="Date" id="myDateField"
               /> 
                 <a href="{{ route('outlet.downPayment') }}" class="btn btn-secondary">Clear Filter</a>
                 <a href="{{ route('outlet.downPayment-create') }}" class="btn btn-primary">
                        Create Down Payment
                    </a>
            </div>
         </form> --}}

                <form method="GET" action="{{ route('outlet.downPayment') }}"
                    class="search-record-box flex items-center justify-between flex-wrap gap-[15px] mb-[15px]"
                    id="downPaymentFilterForm">

                    <div class="search-box relative max-w-[390px] w-full">
                        <input type="text" name="search" placeholder="Search" value="{{ request('search') }}"
                            class="form-control !pr-[50px]" />
                        <button type="submit" class="absolute top-1/2 right-[15px] translate-y-[-50%]">
                            <img src="{{ asset('web/images/search.svg') }}" alt="search" />
                        </button>
                    </div>

                    <div class="date-record-btn w-full md:w-auto flex items-center gap-[15px] flex-wrap md:flex-nowrap">
                        <input type="date" name="date" value="{{ request('date') }}"
                            class="form-control max-w-[100%] cursor-pointer " placeholder="Date" id="myDateField"
                            onchange="document.getElementById('downPaymentFilterForm').submit()" />

                        <a href="{{ route('outlet.downPayment') }}" class="btn btn-secondary">Clear Filter</a>
                        <a href="{{ route('outlet.downPayment-create') }}" class="btn btn-primary">
                            Create Down Payment
                        </a>
                    </div>
                </form>

                <div class="white-box p-[0]">
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap text-sm text-left text-gray-600">
                            <thead class="text-gry-900 semibold body-14-regular uppercase border-b border-[#E9E9E9]">
                                <tr>
                                    <th class="px-6 py-3">Customer Name</th>
                                    <th class="px-6 py-3">Date</th>
                                    <th class="px-6 py-3">Amount Received</th>
                                    <th class="px-6 py-3">Payment Method</th>
                                    <th class="px-6 py-3">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 body-14-regular text-gry-500">
                                @forelse($datas as $transfer)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3">
                                            {{ Str::words(ucwords($transfer->coustomer->name ?? '-'), 5, '...') }}</td>
                                        <td class="px-6 py-3">{{ ucwords($transfer->date ?? '-') }}</td>
                                        <td class="px-6 py-3">${{ $transfer->amount }}</td>
                                        <td class="px-6 py-3">{{ ucfirst($transfer->payment_method) }}</td>
                                        <td class="px-6 py-3">
                                            {{ Str::words(ucwords($transfer->remarks ?? '-'), 5, '...') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-3 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center gap-2">

                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" class="w-16 h-16">
                                                    <path
                                                        d="M21 11.5a8.38 8.38 0 0 1-5.36 7.42c-.5.18-.94.28-1.4.38-1.57.32-3.24.47-5.06-.05C6.1 18.73 3 15.3 3 11.5 3 6.81 7.03 3 12 3c4.77 0 8.67 3.59 8.97 8.25" />
                                                    <path d="M12 10v4m-2-2h4" />
                                                    <path d="M16 19l2 2 4-4" />
                                                </svg>

                                                <h3><strong>No down payments found.</strong></h3>

                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- <div
                        class="flex flex-wrap gap-[15px] items-center justify-between mt-4 border-t border-[#E9E9E9] p-[15px]">
                        {{ $datas->links() }}
                    </div> --}}
                       @if ($datas->total() > 0)
                        <div class="border-t border-[#E9E9E9] p-[15px]">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Showing {{ $datas->firstItem() }} to {{ $datas->lastItem() }} of
                                    {{ $datas->total() }} results
                                </div>
                                <div>
                                    {{ $datas->withQueryString()->links('vendor.pagination.custom-new') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>
        <script>
    document.getElementById('myDateField').addEventListener('click', function() {
        this.showPicker();
    });
</script>
@endsection
