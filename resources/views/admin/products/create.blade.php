@extends('admin.sub_layout')
@section('title', 'Product')
@section('sub_content')

    <div class="main-content side-content pt-0">
        <div class="container-fluid">
            <div class="inner-body">
                <div class="page-header d-block">
                    <h2 class="main-content-title tx-24 mg-b-5">Add Product</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('admin/stock-management/products') }}">Products</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Product</li>
                    </ol>
                </div>

                <div class="row sidemenu-height">
                    <div class="col-lg-12">
                        <div class="card custom-card">
                            <div class="card-body">

                                {!! Form::open(['route' => 'admin.products.store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}

                                {{-- First Row: Name & SKU --}}
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        {!! Form::label('name', 'Name') !!} <span class="text-danger">*</span>
                                        {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => 'Enter Product Name']) !!}
                                        @error('name')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        {!! Form::label('sku', 'SKU') !!} <span class="text-danger">*</span>
                                        {!! Form::text('sku', old('sku'), ['class' => 'form-control', 'placeholder' => 'Enter SKU']) !!}
                                        @error('sku')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Second Row: Stock & Status --}}
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        {!! Form::label('status', 'Status') !!} <span class="text-danger">*</span>
                                        <div class="select-down-arrow">
                                            {!! Form::select('status', config('global.status'), old('status', 1), ['class' => 'form-control']) !!}
                                        </div>
                                        @error('status')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Third Row: Min Price & Max Price --}}
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        {!! Form::label('min_price', 'Minimum Price') !!} <span class="text-danger">*</span>
                                        {!! Form::number('min_price', old('min_price'), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter Minimum Price',
                                            'min' => '0',
                                            'step' => '1',
                                            'oninput' => 'validatePrice(this)',
                                        ]) !!}
                                        @error('min_price')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        {!! Form::label('max_price', 'Maximum Price') !!} <span class="text-danger">*</span>
                                        {!! Form::number('max_price', old('max_price'), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter Maximum Price',
                                            'min' => '0',
                                            'step' => '1',
                                            'oninput' => 'validatePrice(this)',
                                        ]) !!}
                                        @error('max_price')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Fourth Row: Category & Unit of Measurement --}}
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        {!! Form::label('category', 'Category') !!} <span class="text-danger">*</span>
                                        <div class="select-down-arrow">
                                            {!! Form::select(
                                                'category',
                                                [
                                                    '' => 'Choose Category',
                                                    'category1' => 'Category 1',
                                                    'category2' => 'Category 2',
                                                    'category3' => 'Category 3',
                                                    'category4' => 'Category 4',
                                                ],
                                                old('category'),
                                                ['class' => 'form-control'],
                                            ) !!}
                                        </div>
                                        @error('category')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        {!! Form::label('product_package', 'Product Package') !!} <span class="text-danger">*</span>
                                        <div class="select-down-arrow">
                                            {!! Form::select(
                                                'product_package',
                                                [
                                                    '' => 'Choose Product Package',
                                                    'pouches' => 'Pouches',
                                                    'jar' => 'Jar',
                                                    'trays' => 'Trays',
                                                    'packs' => 'Packs',
                                                    'boxes' => 'Boxes',
                                                ],
                                                old('product_package'),
                                                ['class' => 'form-control', 'id' => 'product_package'],
                                            ) !!}
                                        </div>
                                        @error('product_package')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Dynamic Input Field for Package Quantity --}}
                                    <div class="col-md-6" id="package_quantity_wrapper" style="display: none;">
                                        {!! Form::label('package_quantity', 'Package Quantity') !!} <span class="text-danger">*</span>
                                        {!! Form::number('package_quantity', old('package_quantity'), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter Quantity',
                                            'min' => '1',
                                            'step' => '1',
                                        ]) !!}
                                        @error('package_quantity')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Fifth Row: Destination & Remarks --}}
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        {!! Form::label('remarks', 'Remarks') !!}
                                        {!! Form::text('remarks', old('remarks'), ['class' => 'form-control', 'placeholder' => 'Enter remarks']) !!}
                                    </div>
                                </div>

                                {{-- Product Image --}}
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        {!! Form::label('image', 'Product Image') !!} <span class="text-danger">*</span>
                                        <div class="input-group file-browser">
                                            {!! Form::text('image_text', null, [
                                                'class' => 'form-control browse-file',
                                                'placeholder' => 'Choose image',
                                                'readonly',
                                            ]) !!}
                                            <label class="input-group-btn m-0">
                                                <span class="btn btn-primary line-height-24">
                                                    Browse
                                                    {!! Form::file('image', [
                                                        'onchange' => 'previewImage(event)',
                                                        'accept' => 'image/*',
                                                        'style' => 'display: none;',
                                                    ]) !!}
                                                </span>
                                            </label>
                                        </div>
                                        @error('image')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror

                                        {{-- Image Preview --}}
                                        <div class="mt-2">
                                            <img id="product_img" src="#" alt="Image Preview"
                                                style="width: 85px; height: 85px; display: none;">
                                        </div>
                                    </div>
                                </div>

                                {{-- Form Actions --}}
                                <div class="form-actions row mt-3">
                                    <div class="col-md-12">
                                        <button class="btn btn-primary line-height-24" type="submit">
                                            <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                        </button>

                                        <a class="btn btn-secondary line-height-24 ml-3" href="{{ url()->current() }}">
                                            <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                        </a>
                                    </div>
                                </div>

                                <div class="hr hr-24"></div>

                                {!! Form::close() !!}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const output = document.getElementById('product_img');
            const file = event.target.files[0];

            if (file) {
                output.src = URL.createObjectURL(file);
                output.style.display = 'block';
                output.onload = function() {
                    URL.revokeObjectURL(output.src);
                }

                // Update the text input with filename
                const textInput = event.target.closest('.file-browser').querySelector('.browse-file');
                if (textInput) {
                    textInput.value = file.name;
                }
            }
        }

        function validatePrice(input) {
            // Remove any non-digit characters
            let value = input.value.replace(/[^0-9]/g, '');

            // If empty, set to empty string
            if (value === '') {
                input.value = '';
                return;
            }

            // Convert to integer and set back
            let intValue = parseInt(value);

            // Prevent negative values
            if (intValue < 0) {
                intValue = 0;
            }

            input.value = intValue;
        }

        // Show/hide package quantity input based on product package selection
        document.addEventListener('DOMContentLoaded', function() {
            const productPackageSelect = document.getElementById('product_package');
            const packageQuantityWrapper = document.getElementById('package_quantity_wrapper');

            if (productPackageSelect) {
                // Check on page load (for old values)
                if (productPackageSelect.value && productPackageSelect.value !== '') {
                    packageQuantityWrapper.style.display = 'block';
                }

                // Listen for changes
                productPackageSelect.addEventListener('change', function() {
                    if (this.value && this.value !== '') {
                        packageQuantityWrapper.style.display = 'block';
                    } else {
                        packageQuantityWrapper.style.display = 'none';
                    }
                });
            }
        });

        // Additional validation on form submit
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');

            if (form) {
                form.addEventListener('submit', function(e) {
                    const minPrice = document.querySelector('input[name="min_price"]');
                    const maxPrice = document.querySelector('input[name="max_price"]');

                    // Ensure values are integers
                    if (minPrice && minPrice.value) {
                        minPrice.value = parseInt(minPrice.value) || 0;
                    }

                    if (maxPrice && maxPrice.value) {
                        maxPrice.value = parseInt(maxPrice.value) || 0;
                    }

                    // Check if max price is greater than min price
                    if (minPrice && maxPrice && minPrice.value && maxPrice.value) {
                        if (parseInt(maxPrice.value) < parseInt(minPrice.value)) {
                            e.preventDefault();
                            alert('Maximum price must be greater than or equal to minimum price.');
                            return false;
                        }
                    }
                });
            }
        });

        // Prevent typing decimal point, minus sign, and 'e' character
        document.addEventListener('DOMContentLoaded', function() {
            const priceInputs = document.querySelectorAll('input[name="min_price"], input[name="max_price"]');

            priceInputs.forEach(function(input) {
                input.addEventListener('keydown', function(e) {
                    // Prevent: decimal point (.), minus (-), plus (+), 'e', 'E'
                    if (['.', '-', '+', 'e', 'E'].includes(e.key)) {
                        e.preventDefault();
                    }
                });

                // Prevent paste of invalid characters
                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                    const cleanedText = pastedText.replace(/[^0-9]/g, '');
                    if (cleanedText) {
                        input.value = parseInt(cleanedText) || '';
                    }
                });
            });
        });
    </script>
@endsection