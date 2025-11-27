@extends('web.auth.app')

@section('content')
    @include('web.warehouse.shared.header')

    <main class="dashboard-screen-bg relative">
        <section class="dashboard-title-section bg-white border-b border-gry-50">
            <div class="container-fluid">
                <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                    <h1 class="h6 text-gry-800">Receive Stock</h1>
                    <div class="breadcrumb flex items-center gap-[10px] flex-wrap">
                        <a href="{{ route('warehouse.dashboard') }}">
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
                        <span class="body-14 text-gry-800 bold">Stock Operations</span>
                        <span class="text-gry-300">/</span>
                        <span class="body-14 text-gry-800">Receive Stock</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
            <div class="container-fluid">

                {{-- Filter Form --}}
                <form method="GET" action="{{ route('warehouse.recieve-stock') }}"
                    class="search-record-box flex items-center justify-between flex-wrap gap-[15px] mb-[15px]"
                    id="warehouseReceiveStockFilterForm">

                    <div class="search-box relative max-w-[390px] w-full">
                        <input type="text" name="search" placeholder="Search by supplier or warehouse" 
                            value="{{ request('search') }}" class="form-control !pr-[50px]" />
                        <button type="submit" class="absolute top-1/2 right-[15px] translate-y-[-50%]">
                            <img src="{{ asset('web/images/search.svg') }}" alt="search" />
                        </button>
                    </div>

                    <div class="date-record-btn w-full md:w-auto flex items-center gap-[15px] flex-wrap md:flex-nowrap">
                        <input type="date" name="date" value="{{ request('date') }}"
                            class="form-control max-w-[100%]" placeholder="Date" id="customDate"
                            onchange="document.getElementById('warehouseReceiveStockFilterForm').submit()" />

                        <a href="{{ route('warehouse.recieve-stock') }}" class="btn btn-secondary">Clear Filter</a>
                    </div>
                </form>

                {{-- Table --}}
                <div class="white-box p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap text-sm text-left text-gray-600">
                            <thead class="text-gry-900 semibold body-14-regular uppercase border-b border-[#E9E9E9]">
                                <tr>
                                    <th class="px-6 py-3">S.No</th>
                                    <th class="px-6 py-3">Supplier Name</th>
                                    <th class="px-6 py-3">Warehouse</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Created Date</th>
                                    <th class="px-6 py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 body-14-regular text-gry-500">
                                @forelse($stocks as $index => $stock)
                                    <tr class="hover:bg-gray-50">
                                        {{-- Serial Number with proper pagination --}}
                                        <td class="px-6 py-3">{{ $stocks->firstItem() + $index }}</td>
                                        
                                        <td class="px-6 py-3">{{ ucwords($stock->supplier_name) }}</td>
                                        
                                        <td class="px-6 py-3">
                                            @if (!empty($stock->receiverWarehouse) && $stock->receiverWarehouse->name)
                                                {{ ucwords($stock->receiverWarehouse->name) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        
                                        <td class="px-6 py-3
                                            @if (strtolower($stock->status) == 'dispatched') text-[var(--color-status-warning-800)] body-14-semibold
                                            @elseif(strtolower($stock->status) == 'accepted') text-green-600 body-14-semibold
                                            @elseif(strtolower($stock->status) == 'partially accepted') text-blue-600 body-14-semibold
                                            @else body-14-semibold @endif">
                                            {{ ucfirst($stock->status) }}
                                        </td>
                                        
                                        <td class="px-6 py-3">{{ $stock->created_at->format('Y-m-d') }}</td>
                                        
                                        <td class="px-6 py-3 text-center">
                                            {{-- View Button --}}
                                            <a href="{{ route('warehouse.recieve-show', $stock->id) }}">
                                                <button class="p-2 rounded-full bg-pink-100 text-pink-500 hover:bg-pink-200"
                                                    title="View Details">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                            </a>

                                            {{-- Update Status Button (Show until fully accepted) --}}
                                            @if ($stock->status !== 'accepted')
                                                <a href="{{ route('warehouse.recieve-status-update', $stock->id) }}">
                                                    <button
                                                        class="p-2 rounded-full bg-pink-100 text-pink-500 hover:bg-pink-200"
                                                        title="Update Status">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-3 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center gap-2 py-8">
                                                <img src="{{ asset('web/images/stock.png') }}" alt="stock"
                                                    class="w-16 h-16" />
                                                <h3><strong>No received stocks found.</strong></h3>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Links --}}
                    @if($stocks->total() > 0)
                        <div class="border-t border-[#E9E9E9] p-[15px]">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Showing {{ $stocks->firstItem() }} to {{ $stocks->lastItem() }} of {{ $stocks->total() }} results
                                </div>
                                <div>
                                    {{ $stocks->withQueryString()->links('vendor.pagination.custom-new') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>
@endsection