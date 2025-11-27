@extends('web.auth.app')

@section('content')
    @include('web.outlet.shared.header')

    {{-- Set Price Modal --}}
    <div id="set-price-modal"
        class="fixed inset-0 z-50 {{ session('edit_product') && $errors->any() ? 'flex' : 'hidden' }} items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="modal-box bg-white rounded-xl shadow-lg w-full max-w-md p-0 relative" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between py-3 px-4 border-b border-gray-200">
                <h2 class="h6 text-gray-800 font-semibold">Set Outlet Product Price</h2>
                <button type="button" onclick="closePriceModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="41" height="42" viewBox="0 0 41 42" fill="none">
                        <ellipse opacity="0.15" cx="20.5" cy="21" rx="20.5" ry="21" fill="#01ABEC" />
                        <path d="M20.4998 31.9375C26.3722 31.9375 31.1769 27.0156 31.1769 21C31.1769 14.9844 26.3722 10.0625 20.4998 10.0625C14.6274 10.0625 9.82275 14.9844 9.82275 21C9.82275 27.0156 14.6274 31.9375 20.4998 31.9375Z"
                            stroke="#01ABEC" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M17.4785 24.0949L23.5217 17.9043" stroke="#01ABEC" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M23.5217 24.0949L17.4785 17.9043" stroke="#01ABEC" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <div class="modal-content p-4">
                <form method="POST" action="" id="set-price-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-gray-700 mb-1">
                            <span class="font-semibold">Product:</span> 
                            <span id="modal-product-name" class="text-gray-900"></span>
                        </p>
                        <p class="text-sm text-gray-700 mb-1">
                            <span class="font-semibold">SKU:</span> 
                            <span id="modal-product-sku" class="text-gray-900"></span>
                        </p>
                        <p class="text-sm text-gray-700">
                            <span class="font-semibold">Price Range:</span> 
                            <span class="text-green-600">$<span id="modal-min-price"></span></span> - 
                            <span class="text-green-600">$<span id="modal-max-price"></span></span>
                        </p>
                    </div>

                    <div class="form-group mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Outlet Product Price <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></span>
                            <input type="number" 
                                name="outlet_price" 
                                id="outlet-price-input"
                                step="0.01"
                                min="0"
                                required
                                class="form-control w-full border border-gray-300 rounded-lg pl-8 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              />
                        </div>
                        <input type="hidden" id="min-price-hidden" />
                        <input type="hidden" id="max-price-hidden" />
                        
                        @if (session('edit_product'))
                            @error('outlet_price')
                                <small class="text-red-500 text-xs mt-1 block">{{ $message }}</small>
                            @enderror
                        @endif
                        {{-- <small class="text-gray-500 text-xs mt-1 block">Enter price between min and max price range</small> --}}
                    </div>

                    <button type="submit"
                        class="w-full bg-pink-500 hover:bg-pink-600 text-white font-semibold text-base py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <span>Set Price</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <main class="dashboard-screen-bg relative">
        {{-- Title Section --}}
        <section class="dashboard-title-section bg-white border-b border-gray-200">
            <div class="container-fluid">
                <div class="dashboard-inner flex items-center justify-between gap-2 flex-wrap py-2">
                    <h1 class="h6 text-gray-800">Product Price  Management</h1>
                    <div class="breadcrumb flex items-center gap-[10px] flex-wrap">
                        <a href="{{ route('outlet.outlet-dashboard') }}">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.39173 2.34954L2.61673 6.97453C1.96673 7.4912 1.55006 8.5829 1.69172 9.39956L2.80006 16.0329C3.00006 17.2162 4.13339 18.1745 5.33339 18.1745H14.6667C15.8584 18.1745 17.0001 17.2079 17.2001 16.0329L18.3084 9.39956C18.4417 8.5829 18.0251 7.4912 17.3834 6.97453L11.6084 2.35789C10.7167 1.64122 9.27506 1.64121 8.39173 2.34954Z"
                                    stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M10.0001 12.9167C11.1507 12.9167 12.0834 11.9839 12.0834 10.8333C12.0834 9.68274 11.1507 8.75 10.0001 8.75C8.84949 8.75 7.91675 9.68274 7.91675 10.8333C7.91675 11.9839 8.84949 12.9167 10.0001 12.9167Z"
                                    stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                        <span class="text-gry-300">/</span>
                        <span class="body-14 text-gry-800 bold">Product Price Management</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="container-fluid mt-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="container-fluid mt-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Content Section --}}
        <section class="dashboard-content py-4 md:py-8 flex-1">
            <div class="container-fluid">
                {{-- Search and Filter Form --}}
                <form method="GET" action="{{ route('outlet.productManagementList') }}"
                    class="search-record-box flex items-center justify-between flex-wrap gap-[15px] mb-[15px]">
                    <div class="search-box relative max-w-[390px] w-full">
                        <input type="text" name="search" placeholder="Search by name or SKU" value="{{ request('search') }}"
                            class="form-control !pr-[50px]" />
                        <button type="submit" class="absolute top-1/2 right-[15px] translate-y-[-50%]">
                            <img src="{{ asset('web/images/search.svg') }}" alt="search" />
                        </button>
                    </div>

                    <div class="date-record-btn w-full md:w-auto flex items-center gap-[15px] flex-wrap md:flex-nowrap">
                        <a href="{{ route('outlet.productManagementList') }}" class="btn btn-secondary">Clear Filter</a>
                    </div>
                </form>

                {{-- Products Table --}}
                <div class="white-box p-0 rounded-lg shadow">
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap text-sm text-left text-gray-600">
                            <thead class="text-gray-900 font-semibold uppercase border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3">ID</th>
                                    <th class="px-6 py-3">Name</th>
                                    <th class="px-6 py-3">SKU</th>
                                    <th class="px-6 py-3">Min Price</th>
                                    <th class="px-6 py-3">Max Price</th>
                                    <th class="px-6 py-3">Set Price</th>
                                    <th class="px-6 py-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($products as $product)
                                       <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3">#{{ $product->id }}</td>
                                        <td class="px-6 py-3">{{ ucwords($product->name) }}</td>
                                        <td class="px-6 py-3">
                                            {{-- <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $product->sku }}
                                            </span> --}}
                                             {{ $product->sku }}
                                        </td>
                                        <td class="px-6 py-3">
                                            <span class="">${{ number_format($product->min_price, 2) }}</span>
                                        </td>
                                        <td class="px-6 py-3">
                                            <span class="">${{ number_format($product->max_price, 2) }}</span>
                                        </td>
                                        @if($product->outlet_price)
                                         <td class="px-6 py-3">
                                            <span class="">${{($product->outlet_price) }}</span>
                                        </td>
                                        @else
                                         <td class="px-6 py-3">
                                            <span class="">-</span>
                                        </td>
                                        @endif
                                        <td class="px-6 py-3 text-center">
                                            <button
                                                onclick='openPriceModal({{ $product->id }}, "{{ addslashes($product->name) }}", "{{ $product->sku }}", {{ $product->min_price }}, {{ $product->max_price }}, {{ $product->outlet_price ?? 0 }})'
                                                class="px-4 py-2 rounded-lg bg-pink-500 text-white hover:bg-pink-600 inline-flex items-center justify-center gap-2 transition-colors duration-200"
                                                title="Set Outlet Price">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-sm font-medium">Set Price</span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-3 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center gap-2 py-8">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                                <h3><strong>No products found.</strong></h3>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    {{-- <div class="flex flex-wrap gap-4 items-center justify-between mt-4 border-t border-gray-200 p-4">
                        {{ $products->links() }}
                    </div> --}}
                     @if ($products->total() > 0)
                        <div class="border-t border-[#E9E9E9] p-[15px]">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of
                                    {{ $products->total() }} results
                                </div>
                                <div>
                                    {{ $products->withQueryString()->links('vendor.pagination.custom-new') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>

    <script>
        /**
         * Clear validation error messages from a modal
         */
        function clearValidationErrors(modalId) {
            const errorMessages = document.querySelectorAll(`#${modalId} .text-red-500`);
            errorMessages.forEach(msg => msg.parentNode?.removeChild(msg));
        }

        /**
         * Open Set Price Modal
         */
        function openPriceModal(productId, productName, productSku, minPrice, maxPrice, outletPrice) {
            const modal = document.getElementById('set-price-modal');
            const form = document.getElementById('set-price-form');
            const priceInput = document.getElementById('outlet-price-input');
            const minPriceHidden = document.getElementById('min-price-hidden');
            const maxPriceHidden = document.getElementById('max-price-hidden');

            // Set modal display info
            document.getElementById('modal-product-name').textContent = productName;
            document.getElementById('modal-product-sku').textContent = productSku;
            document.getElementById('modal-min-price').textContent = minPrice.toFixed(2);
            document.getElementById('modal-max-price').textContent = maxPrice.toFixed(2);

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            const updateUrlBase = "{{ route('outlet.productUpdatePrice', ['product' => 'PLACEHOLDER']) }}";
            form.action = updateUrlBase.replace('PLACEHOLDER', productId);

            // Set input constraints
            priceInput.setAttribute('min', minPrice);
            priceInput.setAttribute('max', maxPrice);
            minPriceHidden.value = minPrice;
            maxPriceHidden.value = maxPrice;

            @if (session('edit_product') && $errors->any())
                priceInput.value = "{{ old('outlet_price', session('edit_product_price')) }}";
            @else
                priceInput.value = outletPrice > 0 ? outletPrice.toFixed(2) : '';
                clearValidationErrors('set-price-modal');
            @endif

            // Add real-time validation
            priceInput.addEventListener('input', function() {
                const value = parseFloat(this.value);
                const min = parseFloat(minPrice);
                const max = parseFloat(maxPrice);
                
                if (value < min || value > max) {
                    this.setCustomValidity(`Price must be between ₹${min.toFixed(2)} and ₹${max.toFixed(2)}`);
                } else {
                    this.setCustomValidity('');
                }
            });
        }

        /**
         * Close Set Price Modal
         */
        function closePriceModal() {
            const modal = document.getElementById('set-price-modal');
            const form = document.getElementById('set-price-form');
            const priceInput = document.getElementById('outlet-price-input');

            modal.classList.add('hidden');
            modal.classList.remove('flex');

            if (priceInput) {
                priceInput.value = '';
                priceInput.setCustomValidity('');
            }

            form.action = '';
            clearValidationErrors('set-price-modal');
        }

        /**
         * Initialize on page load
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Reopen price modal if validation errors exist
            @if (session('edit_product') && $errors->any())
                openPriceModal(
                    {{ session('edit_product_id') }},
                    '{{ session('edit_product_name') }}',
                    '{{ session('edit_product_sku') }}',
                    {{ session('edit_product_min_price') }},
                    {{ session('edit_product_max_price') }},
                    {{ session('edit_product_price', 0) }}
                );
            @endif
        });

        /**
         * Handle Escape key to close modals
         */
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePriceModal();
            }
        });
    </script>
@endsection