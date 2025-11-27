@extends('web.auth.app')
@section('content')
    @include('web.supervisor.shared.header')
    @php
        use Illuminate\Support\Str;
    @endphp
    @php
        use Carbon\Carbon;
    @endphp
    <main class="dashboard-screen-bg relative">
        <section class="dashboard-title-section bg-white border-b border-gry-50">
            <div class="container-fluid">
                <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                    <h1 class="h6 text-gry-800">Bank Deposit</h1>
                    <div class="breadcrumb flex items-center gap-[10px] flex-wrap">
                         <a href="{{ route('supervisor.supervisor-dashboard') }}">
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
                        <span class="body-14 text-gry-800 bold">Cash Movements</span>
                        <span class="text-gry-300">/</span>
                        <span class="body-14 text-gry-800">Bank Deposit</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
            <div class="container-fluid">
                <form method="GET" action="{{ route('supervisor.bankDeposit') }}" id="bankDepositFilterForm"
                    class="search-record-box flex items-center justify-between flex-wrap gap-[15px] mb-[15px]">
                    <div class="search-box relative max-w-[390px] w-full">
                        <input type="text" name="search" placeholder="Search" value="{{ request('search') }}"
                            class="form-control !pr-[50px]" />
                        <button type="submit" class="absolute top-1/2 right-[15px] translate-y-[-50%]">
                            <img src="{{ asset('web/images/search.svg') }}" alt="search" />
                        </button>
                    </div>

                    <div class="date-record-btn w-full md:w-auto flex items-center gap-[15px] flex-wrap md:flex-nowrap">
                        <input type="date" name="date" value="{{ request('date') }}"
                            class="form-control max-w-[100%] " placeholder="Date" id="customDate"
                            onchange="document.getElementById('bankDepositFilterForm').submit()" />
                        <a href="{{ route('supervisor.bankDeposit') }}" class="btn btn-secondary">Clear Filter</a>
                        <a href="{{ route('supervisor.bankDepositCreate') }}" class="btn btn-primary">
                            Create Bank Deposit
                        </a>
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
                                {{-- Check if $datas is defined and iterate --}}
                                @if (isset($datas))
                                    @forelse($datas as $deposit)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-3">
                                                {{ Str::words(ucwords($deposit->bank_name ?? '-'), 5, '...') }}</td>
                                            <td class="px-6 py-3">
                                                {{ Str::words(ucwords($deposit->depositor_name ?? '-'), 5, '...') }}</td>
                                            <td class="px-6 py-3">{{ number_format($deposit->amount, 2) ?? '-' }}</td>
                                            <td class="px-6 py-3">
                                                {{ Carbon::parse($deposit->deposit_date)->format('Y-m-d') }}</td>
                                            <td class="px-6 py-3">{{ $deposit->reference_number ?? '-' }}</td>
                                            <td class="px-6 py-3">
                                                {{ Str::words(ucwords($deposit->remarks) ?? '-', 5, '...') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-6 text-gray-400">
                                                No records found.
                                            </td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center py-6 text-red-500">
                                            Deposit data is not available.
                                        </td>
                                    </tr>
                                @endif
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
@endsection
