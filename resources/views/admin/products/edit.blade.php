@extends('admin.sub_layout')
@section('title', 'Edit Product')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Edit Product</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url("admin/dashboard") }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Product</li>
                </ol>
            </div>

            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">

                            {!! Form::model($product, ['route' => ['admin.products.update', $product->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}

                            {{-- First Row: Name & SKU --}}
                            <div class="form-group row">
                                <div class="col-md-6">
                                    {!! Form::label('name', 'Name') !!} <span class="text-danger">*</span>
                                    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter Product Name']) !!}
                                    @error('name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    {!! Form::label('sku', 'SKU') !!} <span class="text-danger">*</span>
                                    {!! Form::text('sku', null, ['class' => 'form-control', 'placeholder' => 'Enter SKU']) !!}
                                    @error('sku')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Second Row: Status --}}
                            <div class="form-group row">
                                <div class="col-md-6">
                                    {!! Form::label('status', 'Status') !!} <span class="text-danger">*</span>
                                    <div class="select-down-arrow">
                                        {!! Form::select('status', config('global.status'), null, ['class' => 'form-control']) !!}
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
                                    {!! Form::number('min_price', null, [
                                        'class' => 'form-control', 
                                        'placeholder' => 'Enter Minimum Price', 
                                        'min' => '0',
                                        'step' => '1',
                                        'oninput' => 'validatePrice(this)'
                                    ]) !!}
                                    @error('min_price')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    {!! Form::label('max_price', 'Maximum Price') !!} <span class="text-danger">*</span>
                                    {!! Form::number('max_price', null, [
                                        'class' => 'form-control', 
                                        'placeholder' => 'Enter Maximum Price', 
                                        'min' => '0',
                                        'step' => '1',
                                        'oninput' => 'validatePrice(this)'
                                    ]) !!}
                                    @error('max_price')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Fourth Row: Category --}}
                            <div class="form-group row">
                                <div class="col-md-6">
                                    {!! Form::label('category', 'Category') !!} <span class="text-danger">*</span>
                                    <div class="select-down-arrow">
                                        {!! Form::select('category', [
                                            '' => 'Choose Category',
                                            'category1' => 'Category 1',
                                            'category2' => 'Category 2',
                                            'category3' => 'Category 3',
                                            'category4' => 'Category 4'
                                        ], null, ['class' => 'form-control']) !!}
                                    </div>
                                    @error('category')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Product Package & Package Quantity Row --}}
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
                                            null,
                                            ['class' => 'form-control', 'id' => 'product_package'],
                                        ) !!}
                                    </div>
                                    @error('product_package')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Dynamic Input Field for Package Quantity --}}
                                <div class="col-md-6" id="package_quantity_wrapper" style="display: {{ old('product_package', $product->product_package) ? 'block' : 'none' }};">
                                    {!! Form::label('package_quantity', 'Package Quantity') !!} <span class="text-danger">*</span>
                                    {!! Form::number('package_quantity', null, [
                                        'class' => 'form-control',
                                        'placeholder' => 'Enter Quantity',
                                        'min' => '1',
                                        'step' => '1',
                                        'id' => 'package_quantity'
                                    ]) !!}
                                    @error('package_quantity')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Fifth Row: Remarks --}}
                            <div class="form-group row">
                                <div class="col-md-6">
                                    {!! Form::label('remarks', 'Remarks') !!}
                                    {!! Form::text('remarks', null, ['class' => 'form-control', 'placeholder' => 'Enter remarks']) !!}
                                </div>
                            </div>

                            {{-- Product Image --}}
                            <div class="form-group row">
                                <div class="col-md-6">
                                    {!! Form::label('image', 'Product Image') !!}
                                    <div class="input-group file-browser">
                                        {!! Form::text('image_text', null, ['class' => 'form-control browse-file', 'placeholder' => 'Choose image', 'readonly']) !!}
                                        <label class="input-group-btn m-0">
                                            <span class="btn btn-primary line-height-24">
                                                Browse
                                                {!! Form::file('image', ['onchange' => "loadFile(event,'product_img')", 'accept' => 'image/*', 'style' => 'display: none;']) !!}
                                            </span>
                                        </label>
                                    </div>
                                    @error('image')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror

                                    {{-- Image Preview --}}
                                    <div class="mt-2">
                                        <img id="product_img" src="{{ $product->image ? asset('storage/'.$product->image) : asset('img/logo.png') }}" width="85" height="85" alt="Product Image">
                                    </div>
                                </div>
                            </div>

                            {{-- Form Actions --}}
                            <div class="form-actions row mt-3">
                                <div class="col-md-12">
                                    <button class="btn btn-primary line-height-24" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i> Update
                                    </button>

                                    <a class="btn btn-secondary line-height-24 ml-3" href="{{ route('admin.products.index') }}">
                                        <i class="ace-icon fa fa-undo bigger-110"></i> Cancel
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
    function loadFile(event, id) {
        const output = document.getElementById(id);
        const file = event.target.files[0];
        
        if (file) {
            output.src = URL.createObjectURL(file);
            output.style.display = 'block';
            output.onload = function () {
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
        const packageQuantityInput = document.getElementById('package_quantity');
        
        // Store the original package and quantity values from database
        const originalPackage = productPackageSelect ? productPackageSelect.value : '';
        const originalQuantity = packageQuantityInput ? packageQuantityInput.value : '';

        if (productPackageSelect && packageQuantityWrapper) {
            // Function to toggle visibility
            function togglePackageQuantity() {
                if (productPackageSelect.value && productPackageSelect.value !== '') {
                    packageQuantityWrapper.style.display = 'block';
                } else {
                    packageQuantityWrapper.style.display = 'none';
                    // Clear the quantity input when hiding
                    if (packageQuantityInput) {
                        packageQuantityInput.value = '';
                    }
                }
            }

            // Check on page load
            togglePackageQuantity();

            // Listen for changes
            productPackageSelect.addEventListener('change', function() {
                if (this.value && this.value !== '') {
                    packageQuantityWrapper.style.display = 'block';
                    
                    // Clear quantity field if package has changed from original
                    if (this.value !== originalPackage && packageQuantityInput) {
                        packageQuantityInput.value = '';
                    } else if (this.value === originalPackage && packageQuantityInput) {
                        // Restore original quantity if going back to original package
                        packageQuantityInput.value = originalQuantity;
                    }
                } else {
                    packageQuantityWrapper.style.display = 'none';
                    if (packageQuantityInput) {
                        packageQuantityInput.value = '';
                    }
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
        const priceInputs = document.querySelectorAll('input[name="min_price"], input[name="max_price"], input[name="package_quantity"]');
        
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