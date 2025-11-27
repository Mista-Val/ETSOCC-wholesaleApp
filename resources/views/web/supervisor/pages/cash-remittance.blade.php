@extends('web.auth.app')
@section('content')
@include('web.supervisor.shared.header')

@php
    use Illuminate\Support\Str;
    $activeTab = request('tab', 'requests');
    $requestsRoute = route('supervisor.cashRemittance', ['tab' => 'requests']);
    $historyRoute = route('supervisor.cashRemittance', ['tab' => 'history']);
    $baseClass = 'px-6 py-3 body-14-semibold rounded-md transition-colors duration-150';
    $activeClass = 'text-white bg-[#EC188B] shadow-md'; 
    $inactiveClass = 'text-gry-500 bg-white border border-[#E9E9E9] hover:bg-gray-50';
@endphp

<main class="dashboard-screen-bg relative">
    <section class="dashboard-title-section bg-white border-b border-gry-50">
        <div class="container-fluid">
            <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                <h1 class="h6 text-gry-800">Cash Remittances</h1>
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
                    <span class="body-14 text-gry-800">Cash Remittances</span>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
        <div class="container-fluid">
            {{-- Tab Navigation --}}
            <div class="mb-[15px]">
                <div class="flex gap-4"> 
                    <a href="{{ $requestsRoute }}"
                       class="{{ $baseClass }} {{ $activeTab === 'requests' ? $activeClass : $inactiveClass }}">
                        Remittance Requests
                    </a>
                    <a href="{{ $historyRoute }}"
                       class="{{ $baseClass }} {{ $activeTab === 'history' ? $activeClass : $inactiveClass }}">
                        Remittance History
                    </a>
                </div>
            </div>

            {{-- Search Filter Form --}}
            <form method="GET" action="{{ route('supervisor.cashRemittance', ['tab' => $activeTab]) }}"
                class="search-record-box flex items-center justify-between flex-wrap gap-[15px] mb-[15px]">
                
                <!-- Hidden input to maintain tab state -->
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                
                <div class="search-box relative max-w-[390px] w-full">
                    <input type="text" name="search" placeholder="Search by name, amount, remarks..."
                        value="{{ request('search') }}" class="form-control !pr-[50px]" />
                    <button type="submit" class="absolute top-1/2 right-[15px] translate-y-[-50%]">
                        <img src="{{ asset('web/images/search.svg') }}" alt="search" />
                    </button>
                </div>
                <div class="date-record-btn w-full md:w-auto flex items-center gap-[15px] flex-wrap md:flex-nowrap">
                    <a href="{{ route('supervisor.cashRemittance', ['tab' => $activeTab]) }}" class="btn btn-secondary">Clear Filter</a>
                </div>
            </form>

            {{-- Data Table --}}
            <div class="white-box p-0">
                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap text-sm text-left text-gray-600">
                        <thead class="text-gry-900 semibold body-14-regular uppercase border-b border-[#E9E9E9]">
                            <tr>
                                <th class="px-6 py-3">Remitter Name</th>
                                <th class="px-6 py-3">Location</th>
                                <th class="px-6 py-3">Amount</th>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Remarks</th>
                                <th class="px-6 py-3 text-center">
                                    {{ $activeTab === 'requests' ? 'Actions' : 'Status' }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 body-14-regular text-gry-500">
                            @php
                                $statusColors = [
                                    'rejected' => 'text-[var(--color-secondary-400)]',
                                    'accepted' => 'text-[var(--color-status-success-700)]', 
                                    'pending' => 'text-yellow-600', 
                                ];
                            @endphp

                            @forelse($datas as $index => $data)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3">
                                        {{ Str::words(ucwords($data->coustomer->name ?? '-'), 3, '...') }}
                                    </td>
                                    <td class="px-6 py-3">
                                        {{ $data->location->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-3 font-semibold">
                                        ${{ number_format($data->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-3">
                                        {{ $data->created_at ? $data->created_at->format('Y-m-d') : '-' }}
                                    </td>
                                    <td class="px-6 py-3">
                                        {{ Str::limit(ucwords($data->remarks ?? '-'), 30) }}
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        @if($activeTab === 'requests')
                                            @if(isset($data->status) && $data->status == 'pending')
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="{{ route('supervisor.cash_remittance-status-accept', $data->id) }}"
                                                       class="btn btn-primary px-4 py-2 text-sm">Approve</a>
                                                    <a href="{{ route('supervisor.cash_remittance-status-reject', $data->id) }}"
                                                       class="btn btn-outline btn-pink px-4 py-2 text-sm">Reject</a>
                                                </div>
                                            @endif
                                        @else
                                            <span class="{{ $statusColors[$data->status] ?? 'text-gray-500' }} body-14-semibold">
                                                {{ ucfirst($data->status ?? 'N/A') }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <h3><strong>No cash remittance records found.</strong></h3>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Container --}}
                @if($datas->total() > 0)
                    <div class="border-t border-[#E9E9E9] p-[15px]">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                Showing {{ $datas->firstItem() }} to {{ $datas->lastItem() }} of {{ $datas->total() }} results
                            </div>
                            <div>
                                {{ $datas->appends(['tab' => $activeTab, 'search' => request('search')])->links('vendor.pagination.custom-new') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</main>
@endsection