@extends('web.auth.app')
@section('content')
    @include('web.supervisor.shared.header')
    @php
        use Illuminate\Support\Str;
        use Carbon\Carbon;
    @endphp
    <main class="dashboard-screen-bg relative">
        <section class="dashboard-title-section bg-white border-b border-gry-50">
            <div class="container-fluid">
                <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                    <h1 class="h6 text-gry-800">Record Expense Request</h1>
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
                        <span class="body-14 text-gry-800 bold">Record Expense Request</span>
                        <span class="text-gry-300">/</span>
                        <span class="body-14 text-gry-800">Record Expense Request</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
            <div class="container-fluid">
                <form method="GET" action="{{ route('supervisor.record-expense-request') }}" id="externalCashInflowFilterForm"
                    class="search-record-box flex items-center justify-between flex-wrap gap-[15px] mb-[15px]">
                    <div class="search-box relative max-w-[390px] w-full">
                        <input type="text" name="search" placeholder="Search by received from"
                            value="{{ request('search') }}" class="form-control !pr-[50px]" />
                        <button type="submit" class="absolute top-1/2 right-[15px] translate-y-[-50%]">
                            <img src="{{ asset('web/images/search.svg') }}" alt="search" />
                        </button>
                    </div>

                    <div class="date-record-btn w-full md:w-auto flex items-center gap-[15px] flex-wrap md:flex-nowrap">
                           <input type="date" name="date" value="{{ request('date') }}"
                            class="form-control max-w-[100%] " placeholder="Date" id="customDate"
                            onchange="document.getElementById('externalCashInflowFilterForm').submit()" />
                        <a href="{{ route('supervisor.record-expense-request') }}" class="btn btn-secondary">Clear Filter</a>
                    </div>
                </form>

                <div class="white-box p-[0]">
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap text-sm text-left text-gray-600">
                            <thead class="text-gry-900 semibold body-14-regular uppercase border-b border-[#E9E9E9]">
                                <tr>
                                    <th class="px-6 py-3">Amount</th>
                                    <th class="px-6 py-3">Date</th>
                                    <th class="px-6 py-3">Received From</th>
                                    <th class="px-6 py-3">Purpose</th>
                                    <th class="px-6 py-3">Remarks</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 body-14-regular text-gry-500">
                                @if (isset($recordExpense))
                                    @forelse($recordExpense as $transaction)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-3">$ {{ $transaction->amount ?? '-' }}</td>
                                            <td class="px-6 py-3">
                                                {{ Carbon::parse($transaction->created_at ?? $transaction->created_at)->format('Y-m-d') }}
                                            </td>
                                            <td class="px-6 py-3">{{ $transaction->location->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-3 relative group">
    <span>{{ Str::words(ucwords($transaction->purpose) ?? '-', 5, '...') }}</span>
    <div class="absolute hidden group-hover:block bg-gray-800 text-white text-xs rounded py-1 px-2 -top-8 left-0 whitespace-nowrap z-10">
        {{ $transaction->purpose }}
    </div>
</td>
<td class="px-6 py-3 relative group">
    <span>{{ Str::words(ucwords($transaction->remarks) ?? '-', 5, '...') }}</span>
    <div class="absolute hidden group-hover:block bg-gray-800 text-white text-xs rounded py-1 px-2 -top-8 left-0 whitespace-nowrap z-10">
        {{ $transaction->remarks }}
    </div>
</td>

                                            <td class="px-6 py-3">
                                                @if($transaction->approval_status == 'accepted_by_admin')
                                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                        Accepted by Admin and you
                                                    </span>
                                                @elseif($transaction->approval_status == 'accepted_by_supervisor')
                                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        Pending Admin Approval
                                                    </span>
                                                @elseif($transaction->approval_status == 'rejected_by_supervisor')
                                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                        Rejected by You
                                                    </span>
                                                @elseif($transaction->approval_status == 'rejected_by_admin')
                                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                        Rejected by Admin
                                                    </span>
                                                @else
                                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending Review
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-3 text-center">
                                                {{-- Show buttons only if pending (supervisor hasn't acted) --}}
                                                @if(($transaction->approval_status == 'pending' || $transaction->approval_status == null) && $transaction->status == 'pending')
                                                    <div class="flex items-center justify-center gap-2">
                                                        <a href="{{ route('supervisor.record-expense-accept', $transaction->id) }}"
                                                           class="btn btn-primary px-4 py-2 text-sm">Approve</a>
                                                        <a href="{{ route('supervisor.record-expense-reject', $transaction->id) }}"
                                                           class="btn btn-outline btn-pink px-4 py-2 text-sm">Reject</a>
                                                    </div>
                                                @else
                                                    {{-- Show status text when supervisor already acted --}}
                                                    <span class="text-gray-400 text-sm">
                                                        -
                                                    </span>
                                                @endif
                                            </td>
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
                                            Data is not available.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    {{-- <div
                        class="flex flex-wrap gap-[15px] items-center justify-between mt-4 border-t border-[#E9E9E9] p-[15px]">
                        {{ $recordExpense->links() }}
                    </div> --}}
                     @if ($recordExpense->total() > 0)
                        <div class="border-t border-[#E9E9E9] p-[15px]">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Showing {{ $recordExpense->firstItem() }} to {{ $recordExpense->lastItem() }} of
                                    {{ $recordExpense->total() }} results
                                </div>
                                <div>
                                    {{ $recordExpense->withQueryString()->links('vendor.pagination.custom-new') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>

@endsection