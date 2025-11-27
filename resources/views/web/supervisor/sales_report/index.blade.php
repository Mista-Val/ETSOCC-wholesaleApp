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
                    <h1 class="h6 text-gry-800">Sales Report</h1>
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
                        <span class="body-14 text-gry-800 bold">Sales Overview</span>
                        <span class="text-gry-300">/</span>
                        <span class="body-14 text-gry-800">Sales Report</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
            <div class="container-fluid">

                <form method="GET" action="{{ route('supervisor.salesList') }}" id="filterForm"
                    class="search-record-box flex items-center justify-between flex-wrap gap-[15px] mb-[15px]">

                    <div class="flex items-center gap-[15px] flex-wrap flex-1">
                        {{-- 🔍 Search by Customer --}}
                        <div class="search-box relative max-w-[300px] w-full">
                            <input type="text" name="search" placeholder="Search by customer name"
                                value="{{ request('search') }}" class="form-control !pr-[50px]" />
                            <button type="submit" class="absolute top-1/2 right-[15px] translate-y-[-50%]">
                                <img src="{{ asset('web/images/search.svg') }}" alt="search" />
                            </button>
                        </div>

                        {{-- 🏢 Location Filter --}}
                        <div class="max-w-[200px] w-full">
                            <select name="location_id" class="form-control" onchange="this.form.submit()">
                                <option value="">All Locations</option>
                                @foreach ($locations as $loc)
                                    <option value="{{ $loc->id }}"
                                        {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                                        {{ ucwords($loc->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 📅 Date Filter --}}
                        {{-- <div class="max-w-[200px] w-full">
                        <input type="date" name="date" value="{{ request('date') }}" class="form-control"
                            onchange="this.form.submit()" />
                    </div> --}}
                        <div class="max-w-[200px] w-full">
                            <input type="date" name="date" value="{{ request('date') }}" class="form-control"
                                onclick="this.showPicker()" onchange="this.form.submit()" />
                        </div>
                    </div>

                    {{-- Clear Filter Button --}}
                    <div class="date-record-btn w-full md:w-auto flex items-center gap-[15px] flex-wrap md:flex-nowrap">
                        <a href="{{ route('supervisor.salesList') }}" class="btn btn-secondary">Clear Filter</a>
                    </div>
                </form>

                <div class="white-box p-[0]">
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap text-sm text-left text-gray-600">
                            <thead class="text-gry-900 semibold body-14-regular uppercase border-b border-[#E9E9E9]">
                                <tr>
                                    <th class="px-6 py-3">Customer</th>
                                    <th class="px-6 py-3">Location</th>
                                    <th class="px-6 py-3">Payment Method</th>
                                    <th class="px-6 py-3">Total Amount</th>
                                    <th class="px-6 py-3">Date</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Remark</th>
                                    <th class="px-6 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 body-14-regular text-gry-500">
                                @forelse($sales as $sale)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 capitalize">{{ $sale->customer->name ?? '-' }}</td>
                                        <td class="px-6 py-3 capitalize">{{ $sale->location->name ?? '-' }}</td>
                                        <td class="px-6 py-3 capitalize">{{ $sale->payment_method ?? '-' }}</td>
                                        <td class="px-6 py-3">${{ number_format($sale->total_amount, 2) }}</td>
                                        <td class="px-6 py-3">{{ Carbon::parse($sale->created_at)->format('Y-m-d') }}</td>
                                        <td class="px-6 py-3">{{ ucwords($sale->status ?? '-') }}</td>
                                        <td class="px-6 py-3">{{ Str::limit($sale->remark ?? '-', 30) }}</td>
                                        <td class="px-6 py-3">
                                            <a href="{{ route('supervisor.sales_list_view', $sale->id) }}">
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
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-6 text-gray-400">No sales found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    {{-- @if ($sales->hasPages())
                    <div class="flex flex-wrap gap-[15px] items-center justify-between mt-4 border-t border-[#E9E9E9] p-[15px]">
                        {{ $sales->links() }}
                    </div>
                @endif --}}
                    @if ($sales->total() > 0)
                        <div class="border-t border-[#E9E9E9] p-[15px]">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Showing {{ $sales->firstItem() }} to {{ $sales->lastItem() }} of
                                    {{ $sales->total() }} results
                                </div>
                                <div>
                                    {{ $sales->withQueryString()->links('vendor.pagination.custom-new') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>
@endsection
