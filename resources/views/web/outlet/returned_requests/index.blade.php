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
                    <h1 class="h6 text-gry-800">Returned Request</h1>
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
                        <span class="body-14 text-gry-800 bold">Stock Transfer</span>
                        <span class="text-gry-300">/</span>
                        <span class="body-14 text-gry-800">Returned Request</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
            <div class="container-fluid">
                <form method="GET" action="{{ route('outlet.returned-requests') }}"
                    class="search-record-box flex items-center justify-between flex-wrap gap-[15px] mb-[15px]"
                    id="returnedRequestsFilterForm">

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
                            onchange="document.getElementById('returnedRequestsFilterForm').submit()" />

                        <select class="form-control" name="status"
                            onchange="document.getElementById('returnedRequestsFilterForm').submit()">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="accepted_by_warehouse"
                                {{ request('status') == 'accepted_by_warehouse' ? 'selected' : '' }}>Accepted by Warehouse
                            </option>
                            <option value="accepted_by_supervisor"
                                {{ request('status') == 'accepted_by_supervisor' ? 'selected' : '' }}>Accepted by Supervisor
                            </option>
                            <option value="accepted_by_admin"
                                {{ request('status') == 'accepted_by_admin' ? 'selected' : '' }}>Accepted by Admin</option>
                            <option value="rejected_by_warehouse"
                                {{ request('status') == 'rejected_by_warehouse' ? 'selected' : '' }}>Rejected by Warehouse
                            </option>
                            <option value="rejected_by_supervisor"
                                {{ request('status') == 'rejected_by_supervisor' ? 'selected' : '' }}>Rejected by
                                Supervisor</option>
                            <option value="rejected_by_admin"
                                {{ request('status') == 'rejected_by_admin' ? 'selected' : '' }}>Rejected by Admin</option>
                        </select>

                        <a href="{{ route('outlet.returned-requests') }}" class="btn btn-secondary">Clear Filter</a>

                        <a href="{{ route('outlet.returned-requests-create') }}" class="btn btn-primary">
                            Create Returned Request
                        </a>
                    </div>
                </form>

                <div class="white-box p-[0]">
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap text-sm text-left text-gray-600">
                            <thead class="text-gry-900 semibold body-14-regular uppercase border-b border-[#E9E9E9]">
                                <tr>
                                    <th class="px-6 py-3">Stock ID</th>
                                    <th class="px-6 py-3">Destination Warehouse</th>
                                    <th class="px-6 py-3">Outlet</th>
                                    <th class="px-6 py-3">Date</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 body-14-regular text-gry-500">
                                @forelse($transferStocks as $transfer)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3">#{{ $transfer->id }}</td>
                                        <td class="px-6 py-3">
                                            {{ Str::words(ucwords($transfer->outlet->name ?? '-'), 5, '...') }}
                                        </td>
                                        <td class="px-6 py-3">
                                            {{ Str::words(ucwords($transfer->senderOutlet->name ?? '-'), 5, '...') }}
                                        </td>
                                        <td class="px-6 py-3">{{ $transfer->created_at->format('Y-m-d') }}</td>
                                        <td class="px-6 py-3">
                                            @php
                                                $statusConfig = [
                                                    'pending' => [
                                                        'color' => 'text-[var(--color-status-warning-800)]',
                                                        'label' => 'Pending Warehouse',
                                                    ],
                                                    'accepted_by_warehouse' => [
                                                        'color' => 'text-blue-600',
                                                        'label' => 'Pending Supervisor',
                                                    ],
                                                    'accepted_by_warehouse_supervisor' => [
                                                        'color' => 'text-blue-700',
                                                        'label' => 'Pending Admin Approval',
                                                    ],
                                                    'accepted_by_all' => [
                                                        'color' => 'text-[var(--color-status-success-700)]',
                                                        'label' => 'Accepted & Completed',
                                                    ],
                                                    'rejected_by_warehouse' => [
                                                        'color' => 'text-[var(--color-secondary-400)]',
                                                        'label' => 'Rejected by Warehouse',
                                                    ],
                                                    'rejected_by_supervisor' => [
                                                        'color' => 'text-[var(--color-secondary-400)]',
                                                        'label' => 'Rejected by Supervisor',
                                                    ],
                                                    'rejected_by_admin' => [
                                                        'color' => 'text-[var(--color-secondary-400)]',
                                                        'label' => 'Rejected by Admin',
                                                    ],
                                                ];
                                                $currentStatus = $statusConfig[$transfer->status] ?? [
                                                    'color' => 'text-gray-500',
                                                    'label' => ucfirst($transfer->status),
                                                ];
                                            @endphp
                                            <span class="{{ $currentStatus['color'] }} body-14-semibold">
                                                {{ $currentStatus['label'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            <a href="{{ route('outlet.returned-requests-view', $transfer->id) }}"
                                                class="p-2 rounded-full bg-pink-100 text-pink-500 hover:bg-pink-200 inline-flex items-center justify-center"
                                                title="View Details">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-3 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" class="w-16 h-16">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                                    <polyline points="14 2 14 8 20 8" />
                                                    <path d="M12 17V12l-2 1" />
                                                    <path d="M12 12l2 1" />
                                                    <circle cx="12" cy="14" r="2" />
                                                    <path d="M18 15h-8c-2.209 0-4-1.791-4-4V6" />
                                                </svg>
                                                <h3><strong>No return requests found.</strong></h3>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- <div
                        class="flex flex-wrap gap-[15px] items-center justify-between mt-4 border-t border-[#E9E9E9] p-[15px]">
                        {{ $transferStocks->links() }}
                    </div> --}}
                     @if ($transferStocks->total() > 0)
                        <div class="border-t border-[#E9E9E9] p-[15px]">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Showing {{ $transferStocks->firstItem() }} to {{ $transferStocks->lastItem() }} of
                                    {{ $transferStocks->total() }} results
                                </div>
                                <div>
                                    {{ $transferStocks->withQueryString()->links('vendor.pagination.custom-new') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>
@endsection
