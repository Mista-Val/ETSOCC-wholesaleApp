@extends('admin.sub_layout')
@section('title', 'Receive Stock')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Create Stock</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Receive Stock</li>
                </ol>
            </div>

            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">

                            {!! Form::open(['route' => 'admin.stock.store', 'method' => 'POST']) !!}

                            <div class="row">
                                {{-- Supplier Name --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('supplier_name', 'Supplier Name') !!} <span class="text-danger">*</span>
                                    {!! Form::text('supplier_name', old('supplier_name'), ['class' => 'form-control', 'placeholder' => 'Enter Supplier Name']) !!}
                                    <div class="text-danger">{{ $errors->first('supplier_name') }}</div>
                                </div>
                                
                                {{-- Warehouse Dropdown --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('warehouse_id', 'Warehouse') !!} <span class="text-danger">*</span>
                                    <div class="select-down-arrow">
                                        <select name="warehouse_id" class="form-control">
                                            <option value="">Select Warehouse</option>
                                            @foreach ($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                                    {{ $warehouse->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="text-danger">{{ $errors->first('warehouse_id') }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Dynamic Product + Quantity Rows --}}
                            <div id="item_wrapper">
                                @php
                                    $oldItems = old('items', [[]]);
                                @endphp
                                
                                @foreach($oldItems as $index => $oldItem)
                                <div class="form-row item-row mb-3" data-index="{{ $index }}">
                                    <div class="form-group col-md-6">
                                        <label>Product <span class="text-danger">*</span></label>
                                        <select name="items[{{ $index }}][product_id]" class="form-control product-select">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" 
                                                    {{ (old("items.$index.product_id") == $product->id) ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has("items.$index.product_id"))
                                            <div class="text-danger">{{ $errors->first("items.$index.product_id") }}</div>
                                        @endif
                                    </div>
                                    
                                    <div class="form-group {{ $index == 0 ? 'col-md-6' : 'col-md-5' }}">
                                        <label>Quantity <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               name="items[{{ $index }}][quantity]" 
                                               class="form-control" 
                                               placeholder="Quantity"
                                               value="{{ old("items.$index.quantity") }}"
                                               min="1"
                                               step="1">
                                        @if ($errors->has("items.$index.quantity"))
                                            <div class="text-danger">{{ $errors->first("items.$index.quantity") }}</div>
                                        @endif
                                    </div>
                                    
                                    @if($index > 0)
                                    <div class="form-group col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-item" style="height: 38px;">✖</button>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>

                            {{-- General items error (if no items added) --}}
                            @if ($errors->has('items'))
                                <div class="text-danger mb-3">{{ $errors->first('items') }}</div>
                            @endif

                            <button type="button" class="btn btn-info btn-sm mb-3" id="addItem">+ Add More Item</button>

                            {{-- Submit Buttons --}}
                            <div class="form-actions mt-4">
                                <button class="btn btn-primary" type="submit">Submit</button>
                                <a class="btn btn-secondary ml-3" href="{{ url()->current() }}">Reset</a>
                            </div>

                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let itemIndex = {{ count(old('items', [1])) }};

    const products = @json($products);

    function generateProductOptions(selectedValue = '') {
        let options = `<option value="">Select Product</option>`;
        products.forEach(p => {
            options += `<option value="${p.id}" ${p.id == selectedValue ? 'selected' : ''}>${p.name}</option>`;
        });
        return options;
    }

    function updateProductDropdowns() {
        let selectedValues = [];
        $('.product-select').each(function() {
            const val = $(this).val();
            if (val) selectedValues.push(val);
        });

        $('.product-select').each(function() {
            const currentVal = $(this).val();
            $(this).find('option').each(function() {
                const optionVal = $(this).val();
                if (optionVal && optionVal !== currentVal && selectedValues.includes(optionVal)) {
                    $(this).prop('disabled', true);
                    $(this).addClass('d-none');
                } else {
                    $(this).prop('disabled', false);
                    $(this).removeClass('d-none');
                }
            });
        });
    }

    $('#addItem').click(function() {
        let newRow = `
            <div class="form-row item-row mb-3" data-index="${itemIndex}">
                <div class="form-group col-md-6">
                    <label>Product <span class="text-danger">*</span></label>
                    <select name="items[${itemIndex}][product_id]" class="form-control product-select">
                        ${generateProductOptions()}
                    </select>
                </div>
                <div class="form-group col-md-5">
                    <label>Quantity <span class="text-danger">*</span></label>
                    <input type="number" 
                           name="items[${itemIndex}][quantity]" 
                           class="form-control" 
                           placeholder="Quantity"
                           min="1"
                           step="1">
                </div>
                <div class="form-group col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-item" style="height: 38px;">✖</button>
                </div>
            </div>
        `;
        $('#item_wrapper').append(newRow);
        itemIndex++;
        updateProductDropdowns();
    });

    $(document).on('click', '.remove-item', function() {
        $(this).closest('.item-row').remove();
        updateProductDropdowns();
    });

    $(document).on('change', '.product-select', function() {
        updateProductDropdowns();
    });

    // Prevent decimal input in quantity fields
    $(document).on('input', 'input[type="number"][name*="quantity"]', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    $(document).ready(function() {
        updateProductDropdowns();
    });
</script>

<style>
    option.option-disabled {
        color: #999999;
        background-color: #f5f5f5;
        font-style: italic;
    }
</style>

@endsection