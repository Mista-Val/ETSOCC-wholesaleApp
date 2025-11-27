@extends('web.auth.app')
@section('content')
@include('web.supervisor.shared.header')
@php
    use Illuminate\Support\Str;
@endphp
<main class="dashboard-screen-bg relative">
    <section class="dashboard-title-section bg-white border-b border-gry-50">
        <div class="container-fluid">
            <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                <h1 class="h6 text-gry-800">Cash In Hand</h1>
                <div class="breadcrumb flex items-center gap-[10px] flex-wrap">
                     <a href="{{ route('supervisor.supervisor-dashboard') }}">
                       <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    <span class="body-14 text-gry-800 bold">Cash Movements</span>
                    <span class="text-gry-300">/</span>
                    <span class="body-14 text-gry-800">Cash In Hand</span>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
        <div class="container-fluid">
               <form method="GET" action="{{ route('supervisor.cashInHand') }}" class="search-record-box flex items-center justify-between flex-wrap gap-[15px] mb-[15px]">
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
                 <a href="{{ route('supervisor.cashInHand') }}" class="btn btn-secondary">Clear Filter</a>
            </div>
         </form>

      <div class="white-box p-[0]">
    <div class="overflow-x-auto">
        <table class="w-full whitespace-nowrap text-sm text-left text-gray-600">
            <thead class="text-gry-900 semibold body-14-regular uppercase border-b border-[#E9E9E9]">
                <tr>
                    <th class="px-6 py-3">Bank Name</th>
                    <th class="px-6 py-3">Depositor Name</th>
                    <th class="px-6 py-3">Amount</th> 
                    <th class="px-6 py-3">Deposit Date</th> 
                    <th class="px-6 py-3">Reference No.</th> 
                    <th class="px-6 py-3">Remarks</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 body-14-regular text-gry-500">
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">State Bank of India</td>
                    <td class="px-6 py-3">Rohit Sharma</td>
                    <td class="px-6 py-3">₹10,000</td>
                    <td class="px-6 py-3">2025-10-09</td>
                    <td class="px-6 py-3">REF12345</td>
                    <td class="px-6 py-3">Monthly deposit</td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">HDFC Bank</td>
                    <td class="px-6 py-3">Priya Verma</td>
                    <td class="px-6 py-3">₹5,500</td>
                    <td class="px-6 py-3">2025-10-08</td>
                    <td class="px-6 py-3">REF67890</td>
                    <td class="px-6 py-3">Project payment</td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">ICICI Bank</td>
                    <td class="px-6 py-3">Ankit Mehra</td>
                    <td class="px-6 py-3">₹7,200</td>
                    <td class="px-6 py-3">2025-10-07</td>
                    <td class="px-6 py-3">REF54321</td>
                    <td class="px-6 py-3">Advance payment</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="flex flex-wrap gap-[15px] items-center justify-between mt-4 border-t border-[#E9E9E9] p-[15px]">
        <span class="text-gray-500">Showing 1 to 3 of 3 entries</span>
    </div>
</div>

        </div>
    </section>
</main>
@endsection