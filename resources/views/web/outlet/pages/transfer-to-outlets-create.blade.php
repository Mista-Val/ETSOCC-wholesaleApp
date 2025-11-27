@extends('web.auth.app')
@section('content')
    @include('web.outlet.shared.header')

    <main class="dashboard-screen-bg relative">
        <section class="dashboard-title-section bg-white border-b border-gry-50 bg-white">
            <div class="container-fluid">
                <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                    <h1 class="h6 text-gry-800">Create Transfer</h1>
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
                        <span class="body-14 text-gry-800 bold">Transfer Out</span>
                        <span class="text-gry-300">/</span>
                        <span class="body-14 text-gry-800">Create Transfer</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
            <div class="container-fluid">
                <div class="bg-white white-box">
                    <form action="{{ route('outlet.transferoutletsstore') }}" method="POST">
                        @csrf

                        <!-- Outlet Selection -->
                        <div class="form-group">
                            <label>Outlet to Transfer</label>
                            <div class="relative">
                                <select class="form-control appearance-none pr-[50px] w-full" name="outlet_id">
                                    <option value="">Select outlet</option>
                                    @foreach ($outlets as $outlet)
                                        <option value="{{ $outlet->id }}"
                                            {{ old('outlet_id') == $outlet->id ? 'selected' : '' }}>
                                            {{ ucwords($outlet->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                    <svg class="h-6 w-6 text-gray-800" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            @error('outlet_id')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Items Section -->
                        <h5 class="body-14-semibold text-gry-700 bold mb-[10px]">Select Items</h5>
                        <div id="items-wrapper">
                            <!-- First Row (Always Present) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-[15px] items-start item-row">
                                <div class="form-group m-0 md:mb-[15px]">
                                    <label>Item Name</label>
                                    <div class="relative">
                                        <select class="form-control appearance-none pr-[50px] w-full"
                                            name="products[0][product_id]">
                                            <option value="">Select item name</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    {{ old('products.0.product_id') == $product->id ? 'selected' : '' }}>
                                                    {{ ucwords($product->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                            <svg class="h-6 w-6 text-gray-800" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    @error('products.0.product_id')
                                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="flex gap-2 items-start">
                                    <div class="form-group flex-1 m-0 md:mb-[15px]">
                                        <label>Quantity</label>
                                        <input type="number" name="products[0][quantity]"
                                            value="{{ old('products.0.quantity') }}" placeholder="Enter quantity"
                                            class="form-control" min="1" step="1"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '');" />
                                        @error('products.0.quantity')
                                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="button"
                                        class="btn btn-primary btn-square border-0 mt-[26px] p-0 remove-row flex-shrink-0">
                                        <img src="{{ asset('web/images/close.svg') }}" alt="close" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="form-group">
                            <label>Add Remarks</label>
                            <textarea class="form-control" name="remarks" placeholder="Enter remarks">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit and Add More Buttons -->
                        <div class="submit-addmore-items flex flex-wrap gap-[15px] justify-between mt-4">
                            <button class="btn btn-primary min-w-[145px]" type="submit">Submit</button>
                            <button type="button" id="add-more" class="btn btn-outline"> + Add More Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    @push('scripts')
        <script>
            let rowIndex = {{ count(old('products', [])) > 0 ? count(old('products', [])) : 1 }};

            function updateProductOptions() {
                let selected = Array.from(document.querySelectorAll('select[name^="products"]'))
                    .map(s => s.value)
                    .filter(v => v !== "");

                document.querySelectorAll('select[name^="products"]').forEach(select => {
                    let current = select.value;
                    Array.from(select.options).forEach(opt => {
                        if (opt.value === "") return;
                        opt.disabled = selected.includes(opt.value) && opt.value !== current;
                    });
                });
            }

            // Restore old input rows on page load (when validation fails)
            @if (old('products') && count(old('products')) > 1)
                document.addEventListener('DOMContentLoaded', function() {
                    const wrapper = document.getElementById('items-wrapper');
                    const oldProducts = @json(old('products'));
                    const errors = @json($errors->messages());

                    // Skip the first one as it's already in the DOM
                    for (let i = 1; i < oldProducts.length; i++) {
                        let productId = oldProducts[i].product_id || '';
                        let quantity = oldProducts[i].quantity || '';

                        // Get errors for this specific index
                        let productError = errors[`products.${i}.product_id`] ? errors[`products.${i}.product_id`][0] : '';
                        let quantityError = errors[`products.${i}.quantity`] ? errors[`products.${i}.quantity`][0] : '';

                        let newRow = `
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-[15px] items-start item-row">
                            <div class="form-group m-0 md:mb-[15px]">
                                <label>Item Name</label>
                                <div class="relative">
                                    <select class="form-control appearance-none pr-[50px] w-full" name="products[${i}][product_id]">
                                        <option value="">Select item name</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" ${productId == '{{ $product->id }}' ? 'selected' : ''}>
                                                {{ ucwords($product->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                        <svg class="h-6 w-6 text-gray-800" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                ${productError ? `<div class="text-red-500 text-sm mt-1">${productError}</div>` : ''}
                            </div>
                            <div class="flex gap-2 items-start">
                                <div class="form-group flex-1 m-0 md:mb-[15px]">
                                    <label>Quantity</label>
                                    <input type="number" name="products[${i}][quantity]" value="${quantity}" placeholder="Enter quantity" class="form-control" min="1" step="1" oninput="this.value = this.value.replace(/[^0-9]/g, '');"/>
                                    ${quantityError ? `<div class="text-red-500 text-sm mt-1">${quantityError}</div>` : ''}
                                </div>
                                <button type="button" class="btn btn-primary btn-square border-0 mt-[26px] p-0 remove-row flex-shrink-0">
                                    <img src="{{ asset('web/images/close.svg') }}" alt="close" />
                                </button>
                            </div>
                        </div>`;
                        wrapper.insertAdjacentHTML('beforeend', newRow);
                    }
                    updateProductOptions();
                });
            @endif

            // Add More Item Button
            document.getElementById('add-more').addEventListener('click', function() {
                const wrapper = document.getElementById('items-wrapper');

                let newRow = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-[15px] items-start item-row">
                    <div class="form-group m-0 md:mb-[15px]">
                        <label>Item Name</label>
                        <div class="relative">
                            <select class="form-control appearance-none pr-[50px] w-full" name="products[${rowIndex}][product_id]">
                                <option value="">Select item name</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ ucwords($product->name) }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                <svg class="h-6 w-6 text-gray-800" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2 items-start">
                        <div class="form-group flex-1 m-0 md:mb-[15px]">
                            <label>Quantity</label>
                            <input type="number" name="products[${rowIndex}][quantity]" placeholder="Enter quantity" class="form-control" min="1" step="1" oninput="this.value = this.value.replace(/[^0-9]/g, '');"/>
                        </div>
                        <button type="button" class="btn btn-primary btn-square border-0 mt-[26px] p-0 remove-row flex-shrink-0">
                            <img src="{{ asset('web/images/close.svg') }}" alt="close" />
                        </button>
                    </div>
                </div>`;
                wrapper.insertAdjacentHTML('beforeend', newRow);
                rowIndex++;
                updateProductOptions();
            });

            // Update product options when selection changes
            document.addEventListener('change', function(e) {
                if (e.target.matches('select[name^="products"]')) {
                    updateProductOptions();
                }
            });

            // Remove row functionality
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-row')) {
                    const rows = document.querySelectorAll('.item-row');
                    if (rows.length > 1) {
                        e.target.closest('.item-row').remove();
                        updateProductOptions();
                    } else {
                        alert('At least one item is required.');
                    }
                }
            });

            // Initialize on page load
            updateProductOptions();
        </script>
    @endpush
@endsection