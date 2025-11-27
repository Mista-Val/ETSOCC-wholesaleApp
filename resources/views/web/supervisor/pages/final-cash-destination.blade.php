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
                    <h1 class="h6 text-gry-800">Final Cash Destination</h1>
                    <div class="breadcrumb flex items-center gap-[10px] flex-wrap">
                        <a>
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
                        <span class="body-14 text-gry-800 bold">External Cash</span>
                        <span class="text-gry-300">/</span>
                        <span class="body-14 text-gry-800">Final Cash Destination</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
            <div class="container-fluid">
                <form method="GET" action="{{ route('supervisor.finalCashDestination') }}" id="finalCashDestinationFilterForm"
                    class="search-record-box flex items-center justify-between flex-wrap gap-[15px] mb-[15px]">
                    <div class="search-box relative max-w-[390px] w-full">
                        <input type="text" name="search" placeholder="Search" value="{{ request('search') }}"
                            class="form-control !pr-[50px]" />
                        <button type="submit" class="absolute top-1/2 right-[15px] translate-y-[-50%]">
                            <img src="{{ asset('web/images/search.svg') }}" alt="search" />
                        </button>
                    </div>

                    <div class="date-record-btn w-full md:w-auto flex items-center gap-[15px] flex-wrap md:flex-nowrap">
                        <a href="{{ route('supervisor.finalCashDestination') }}" class="btn btn-secondary">Clear Filter</a>
                        <a href="{{ route('supervisor.finalCashCreate') }}" class="btn btn-primary">
                            Create Cash Entry
                        </a>
                    </div>
                </form>

                <div class="white-box p-[0]">
                    <div class="overflow-x-auto">
                      <table class="w-full whitespace-nowrap text-sm text-left text-gray-600">
                            <thead class="text-gry-900 semibold body-14-regular uppercase border-b border-[#E9E9E9]">
                                <tr>
                                    <th class="px-6 py-3">Final Destination</th>
                                    <th class="px-6 py-3">Cash Handler Name</th>
                                    <th class="px-6 py-3">Responsible Person</th>
                                    <th class="px-6 py-3">Amount</th>
                                    <th class="px-6 py-3">Date</th>
                                    <th class="px-6 py-3">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 body-14-regular text-gry-500">
                                @if (isset($datas))
                                    @forelse($datas as $transaction)
                                        <tr class="hover:bg-gray-50">
                                            {{-- Use new keys: final_destination --}}
                                            <td class="px-6 py-3">
                                                {{ Str::words(ucwords($transaction->final_destination ?? '-'), 5, '...') }}</td>
                                            <td class="px-6 py-3">
                                                {{ Str::words(ucwords($transaction->cash_handler_name ?? '-'), 5, '...') }}</td>
                                            {{-- Use new keys: responsible_person --}}
                                            <td class="px-6 py-3">
                                                {{ Str::words(ucwords($transaction->responsible_person ?? '-'), 5, '...') }}</td>
                                            {{-- Amount remains the same --}}
                                            <td class="px-6 py-3">{{ number_format($transaction->amount, 2) ?? '-' }}</td>
                                            {{-- Date remains the same --}}
                                            <td class="px-6 py-3">
                                                {{ Carbon::parse($transaction->created_at)->format('Y-m-d') }}</td>
                                            {{-- Remarks remains the same --}}
                                            <td class="px-6 py-3">
                                                {{ Str::words(ucwords($transaction->remarks) ?? '-', 5, '...') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-6 text-gray-400">
                                                No records found.
                                            </td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center py-6 text-red-500">
                                           Data is not available.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div
                        class="flex flex-wrap gap-[15px] items-center justify-between mt-4 border-t border-[#E9E9E9] p-[15px]">
                        {{ $datas->links() }}
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection