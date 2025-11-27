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
                    <h1 class="h6 text-gry-800">Refund Requests</h1>
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
                        <span class="body-14 text-gry-800 bold">Refund Requests</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
            <div class="container-fluid">
                <form method="GET" action="{{ route('supervisor.cash-refund-request') }}" id="refundFilterForm"
                    class="search-record-box flex items-center justify-between flex-wrap gap-[15px] mb-[15px]">
                    <div class="search-box relative max-w-[390px] w-full">
                        <input type="text" name="search" placeholder="Search by customer or reason"
                            value="{{ request('search') }}" class="form-control !pr-[50px]" />
                        <button type="submit" class="absolute top-1/2 right-[15px] translate-y-[-50%]">
                            <img src="{{ asset('web/images/search.svg') }}" alt="search" />
                        </button>
                    </div>

                    <div class="date-record-btn w-full md:w-auto flex items-center gap-[15px] flex-wrap md:flex-nowrap">
                        <input type="date" name="date" value="{{ request('date') }}" class="form-control max-w-[100%]"
                            placeholder="Date" id="customDate"
                            onchange="document.getElementById('refundFilterForm').submit()" />
                        <a href="{{ route('supervisor.cash-refund-request') }}" class="btn btn-secondary">Clear Filter</a>
                    </div>
                </form>

                <div class="white-box p-[0]">
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap text-sm text-left text-gray-600">
                            <thead class="text-gry-900 semibold body-14-regular uppercase border-b border-[#E9E9E9]">
                                <tr>
                                    <th class="px-6 py-3">Order ID</th>
                                    <th class="px-6 py-3">Customer Name</th>
                                    <th class="px-6 py-3">Refund Amount</th>
                                    <th class="px-6 py-3">Date</th>
                                    <th class="px-6 py-3">Location</th>
                                    {{-- <th class="px-6 py-3">Reason</th> --}}
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 body-14-regular text-gry-500">
                                @if (isset($refunds))
                                    @forelse($refunds as $refund)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-3">#{{ $refund->sale->id ?? '-' }}</td>
                                            <td class="px-6 py-3">{{ ucwords($refund->sale->customer->name ?? 'N/A') }}</td>
                                            <td class="px-6 py-3 font-semibold">$
                                                {{ number_format($refund->refund_amount, 2) }}</td>
                                            <td class="px-6 py-3">
                                                {{ Carbon::parse($refund->created_at)->format('Y-m-d') }}
                                            </td>
                                            <td class="px-6 py-3">
                                                {{ Str::words(ucwords($refund->location->name) ?? '-', 5, '...') }}
                                            </td>
                                            <td class="px-6 py-3">
                                                @if ($refund->status == 'accepted_by_supervisor')
                                                    <span
                                                        class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                        Approved
                                                    </span>
                                                @elseif($refund->status == 'rejected_by_supervisor')
                                                    <span
                                                        class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                        Rejected
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending Review
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-3 text-center">
                                                @if ($refund->status == 'created' || $refund->status == null)
                                                    <div class="flex items-center justify-center gap-2">
                                                        <a href="{{ route('supervisor.cash-refund-accept', $refund->id) }}"
                                                            class="btn btn-primary px-4 py-2 text-sm"
                                                            onclick="return confirm('Are you sure you want to approve this refund?')">
                                                            Approve
                                                        </a>
                                                        {{-- <button onclick="openRejectModal({{ $refund->id }})"
                                                           class="btn btn-outline btn-pink px-4 py-2 text-sm">
                                                           Reject
                                                        </button> --}}
                                                        <a href="{{ route('supervisor.cash-refund-reject', $refund->id) }}"
                                                            class="btn btn-primary px-4 py-2 text-sm"
                                                            onclick="return confirm('Are you sure you want to reject this refund?')">
                                                            Reject
                                                        </a>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 text-sm">
                                                        -
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-6 text-gray-400">
                                                No refund requests found.
                                            </td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center py-6 text-red-500">
                                            Data is not available.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    {{-- <div class="flex flex-wrap gap-[15px] items-center justify-between mt-4 border-t border-[#E9E9E9] p-[15px]">
                        {{ $refunds->links() }}
                    </div> --}}
                    {{-- Replace your entire pagination section with this --}}
                    @if ($refunds->total() > 0)
                        <div class="border-t border-[#E9E9E9] p-[15px]">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Showing {{ $refunds->firstItem() }} to {{ $refunds->lastItem() }} of
                                    {{ $refunds->total() }} results
                                </div>
                                <div>
                                    {{ $refunds->withQueryString()->links('vendor.pagination.custom-new') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>

    {{-- Rejection Modal --}}
    <div id="reject-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="modal-box bg-white rounded-xl shadow-lg w-full max-w-md p-0 relative">
            <div class="flex items-center justify-between py-4 px-6 border-b border-gray-200 bg-white">
                <h2 class="text-xl text-gray-800 font-semibold">Reject Refund Request</h2>
                <button type="button" onclick="closeRejectModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="41" height="42" viewBox="0 0 41 42"
                        fill="none">
                        <ellipse opacity="0.15" cx="20.5" cy="21" rx="20.5" ry="21"
                            fill="#EF4444" />
                        <path
                            d="M20.4998 31.9375C26.3722 31.9375 31.1769 27.0156 31.1769 21C31.1769 14.9844 26.3722 10.0625 20.4998 10.0625C14.6274 10.0625 9.82275 14.9844 9.82275 21C9.82275 27.0156 14.6274 31.9375 20.4998 31.9375Z"
                            stroke="#EF4444" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M17.4785 24.0949L23.5217 17.9043" stroke="#EF4444" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M23.5217 24.0949L17.4785 17.9043" stroke="#EF4444" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <div class="modal-content p-6">
                <form method="POST" action="" id="reject-form">
                    @csrf
                    <div class="form-group">
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Rejection Reason <span class="text-red-500">*</span>
                        </label>
                        <textarea name="rejection_reason" rows="4" required
                            class="form-control w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                            placeholder="Enter rejection reason..."></textarea>
                    </div>
                    <div class="flex gap-3 mt-6">
                        <button type="button" onclick="closeRejectModal()"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold text-base py-3 px-4 rounded-lg transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 bg-pink-500 hover:bg-pink-600 text-white font-semibold text-base py-3 px-4 rounded-lg transition-colors duration-200">
                            Reject Refund
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRejectModal(refundId) {
            const modal = document.getElementById('reject-modal');
            const form = document.getElementById('reject-form');

            // Set form action URL
            form.action = `{{ url('supervisor/cash-refund-accept') }}/${refundId}`;

            // Show modal
            modal.style.display = 'flex';
            modal.classList.remove('hidden');
        }

        function closeRejectModal() {
            const modal = document.getElementById('reject-modal');
            const form = document.getElementById('reject-form');

            // Hide modal
            modal.style.display = 'none';
            modal.classList.add('hidden');

            // Reset form
            form.reset();
            form.action = '';
        }

        // Handle Escape key to close modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRejectModal();
            }
        });
    </script>

@endsection
