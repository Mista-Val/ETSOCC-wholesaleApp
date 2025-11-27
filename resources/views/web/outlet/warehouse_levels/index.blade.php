@extends('web.auth.app')

@section('content')
    @include('web.outlet.shared.header')

    <main class="dashboard-screen-bg relative">
        <section class="dashboard-title-section bg-white border-b border-gry-50">
            <div class="container-fluid">
                <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                    <h1 class="h6 text-gry-800">Warehouse Levels</h1>
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
                        <span class="body-14 text-gry-800 bold">Stock Operations</span>
                        <span class="text-gry-300">/</span>
                        <span class="body-14 text-gry-800">Warehouse Levels</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
            <div class="container-fluid">

                <form method="GET" action="{{ route('outlet.warehouse-levels') }}"
                    class="search-record-box flex items-center justify-between flex-wrap gap-[15px] mb-[15px]">
                    <div class="search-box relative max-w-[390px] w-full">
                        <select name="warehouse_id" id="warehouseSelect" class="form-control !pr-[50px] w-full">
                            <option value="">All Warehouses</option>
                            @foreach ($warehouses as $ware)
                                <option value="{{ $ware->id }}" {{ request('warehouse_id') == $ware->id ? 'selected' : '' }}>
                                    {{ ucwords($ware->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="date-record-btn w-full md:w-auto flex items-center gap-[15px] flex-wrap md:flex-nowrap">
                        <a href="{{ route('outlet.warehouse-levels') }}" class="btn btn-secondary">Clear Filter</a>
                    </div>
                </form>

                <div class="white-box p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap text-sm text-left text-gray-600" id="stocksTable">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3">SKU</th>
                                    <th class="px-6 py-3">Product Name</th>
                                    <th class="px-6 py-3">Available Stock</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 body-14-regular text-gry-500">
                                @forelse($stocks as $stock)
                                    <tr>
                                        <td class="px-6 py-3">{{ $stock->product->sku ? $stock->product->sku : '-' }}</td>
                                        <td class="px-6 py-3">{{ $stock->product->name }}</td>
                                        <td class="px-6 py-3">{{ $stock->product_quantity ?? 0 }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-3 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <img src="{{ asset('web/images/stock.png') }}" alt="stock"
                                                    class="w-16 h-16" />
                                                <h3><strong>No records found.</strong></h3>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    {{-- @if($stocks->hasPages())
                        <div class="flex justify-end mt-4 border-t border-[#E9E9E9] p-[15px]" id="paginationContainer">
                            {{ $stocks->withQueryString()->links() }}
                        </div>
                    @endif --}}
                    @if ($stocks->total() > 0)
                        <div id="paginationContainer" class="border-t border-[#E9E9E9] p-[15px]">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Showing {{ $stocks->firstItem() }} to {{ $stocks->lastItem() }} of {{ $stocks->total() }} results
                                </div>
                                <div>
                                    {{ $stocks->appends(['warehouse_id' => request('warehouse_id')])->links('vendor.pagination.custom-new') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>

    <script>
        document.getElementById('warehouseSelect').addEventListener('change', function() {
            let warehouseId = this.value;
            let url = "{{ route('outlet.warehouse-levels') }}";
            
            // Add warehouse_id parameter if selected
            if (warehouseId) {
                url += "?warehouse_id=" + warehouseId;
            }

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Update table body
                    document.querySelector('#stocksTable tbody').innerHTML = data.tableHtml;
                    
                    // Update or hide pagination
                    let paginationContainer = document.getElementById('paginationContainer');
                    if (data.paginationHtml && data.paginationHtml.trim() !== '') {
                        if (paginationContainer) {
                            paginationContainer.innerHTML = data.paginationHtml;
                        } else {
                            // Create pagination container if it doesn't exist
                            let container = document.createElement('div');
                            container.id = 'paginationContainer';
                            container.className = 'flex justify-end mt-4 border-t border-[#E9E9E9] p-[15px]';
                            container.innerHTML = data.paginationHtml;
                            document.querySelector('.white-box').appendChild(container);
                        }
                    } else {
                        // Remove pagination if no pages
                        if (paginationContainer) {
                            paginationContainer.remove();
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
@endsection