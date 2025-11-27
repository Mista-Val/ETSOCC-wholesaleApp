@extends('web.auth.app')

@section('content')
    @include('web.warehouse.shared.header')

    <main class="dashboard-screen-bg relative">
        <section class="dashboard-title-section bg-white border-b border-gry-50">
            <div class="container-fluid">
                <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                    <h1 class="h6 text-gry-800">Outlet Levels</h1>
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
                        <span class="body-14 text-gry-800">Outlet Levels</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
            <div class="container-fluid">

                <div class="search-record-box flex items-center justify-between flex-wrap gap-[15px] mb-[15px]">
                    <div class="search-box relative max-w-[390px] w-full">
                        <select name="outlet_id" id="outletSelect" class="form-control !pr-[50px] w-full appearance-none">
                            <option value="">All Outlets</option>
                            @foreach ($outlets as $out)
                                <option value="{{ $out->id }}" {{ request('outlet_id') == $out->id ? 'selected' : '' }}>
                                    {{ ucwords($out->name) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                            <svg class="h-6 w-6 text-gray-800" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <div class="date-record-btn w-full md:w-auto flex items-center gap-[15px] flex-wrap md:flex-nowrap">
                        <a href="{{ route('warehouse.outlet-levels') }}" class="btn btn-secondary">Clear Filter</a>
                    </div>
                </div>

                <div class="white-box p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap text-sm text-left text-gray-600" id="stocksTable">
                            <thead class="text-gry-900 semibold body-14-regular uppercase border-b border-[#E9E9E9]">
                                <tr>
                                    <th class="px-6 py-3">SKU</th>
                                    <th class="px-6 py-3">Product Name</th>
                                    <th class="px-6 py-3">Available Stock</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 body-14-regular text-gry-500">
                                @include('web.warehouse.outlet_levels.partials.stocks_table', ['stocks' => $stocks])
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Container --}}
                    @if ($stocks->total() > 0)
                        <div id="paginationContainer" class="border-t border-[#E9E9E9] p-[15px]">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Showing {{ $stocks->firstItem() }} to {{ $stocks->lastItem() }} of {{ $stocks->total() }} results
                                </div>
                                <div>
                                    {{ $stocks->appends(['outlet_id' => request('outlet_id')])->links('vendor.pagination.custom-new') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>

    <script>
        // ✅ Handle outlet dropdown change
        document.getElementById('outletSelect').addEventListener('change', function() {
            let outletId = this.value;
            let url = "{{ route('warehouse.outlet-levels') }}";
            
            if (outletId) {
                url += "?outlet_id=" + outletId;
            }

            fetchData(url);
        });

        // ✅ Function to fetch data and update UI
        function fetchData(url) {
            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Update table body
                    document.querySelector('#stocksTable tbody').innerHTML = data.tableHtml;
                    
                    // Handle pagination container
                    let paginationContainer = document.getElementById('paginationContainer');
                    let whiteBox = document.querySelector('.white-box');
                    
                    if (data.total > 0) {
                        if (paginationContainer) {
                            // Update existing pagination
                            paginationContainer.innerHTML = data.paginationHtml;
                        } else {
                            // Create new pagination container
                            let container = document.createElement('div');
                            container.id = 'paginationContainer';
                            container.className = 'border-t border-[#E9E9E9] p-[15px]';
                            container.innerHTML = data.paginationHtml;
                            whiteBox.appendChild(container);
                        }
                    } else {
                        // Remove pagination if no results
                        if (paginationContainer) {
                            paginationContainer.remove();
                        }
                    }
                    
                    // Re-attach pagination handlers
                    attachPaginationHandlers();
                })
                .catch(error => console.error('Error:', error));
        }

        // ✅ Attach click handlers to pagination links
        function attachPaginationHandlers() {
            document.querySelectorAll('#paginationContainer a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    let url = this.getAttribute('href');
                    
                    // Fetch data via AJAX
                    fetchData(url);
                    
                    // Smooth scroll to table top
                    document.querySelector('.white-box').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                });
            });
        }

        // ✅ Initial attachment on page load
        document.addEventListener('DOMContentLoaded', function() {
            attachPaginationHandlers();
        });
    </script>
@endsection