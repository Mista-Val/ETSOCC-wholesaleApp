@extends('admin.sub_layout')
@section('title', 'Edit Stock')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Edit Received Stock</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.stock.index') }}">Stocks</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </div>

            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">

                            {!! Form::model($stock, ['route' => ['admin.stock.update', $stock->id], 'method' => 'PUT']) !!}

                            <div class="row">
                                {{-- Supplier Name --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('supplier_name', 'Supplier Name') !!} <span class="text-danger">*</span>
                                    {!! Form::text('supplier_name', old('supplier_name', $stock->supplier_name), ['class' => 'form-control', 'placeholder' => 'Enter Supplier Name']) !!}
                                    <div class="text-danger">{{ $errors->first('supplier_name') }}</div>
                                </div>

                                {{-- Warehouse --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('warehouse_id', 'Warehouse') !!} <span class="text-danger">*</span>
                                    <select name="warehouse_id" class="form-control">
                                        <option value="">Select Warehouse</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ $stock->receiver_id == $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="text-danger">{{ $errors->first('warehouse_id') }}</div>
                                </div>
                            </div>

                            {{-- Item Rows --}}
                            <div id="item_wrapper">
                                @foreach ($stock->items as $index => $item)
                                    <div class="form-row item-row mb-3">
                                        <div class="form-group col-md-6">
                                            <label>Product</label>
                                            <select name="items[{{ $index }}][product_id]" class="form-control product-select">
                                                <option value="">Select Product</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" {{ $product->id == $item->product_id ? 'selected' : '' }}>
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has("items.$index.product_id"))
                                                <div class="text-danger">{{ $errors->first("items.$index.product_id") }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group col-md-5">
                                            <label>Quantity</label>
                                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control" placeholder="Quantity" value="{{ $item->set_quantity }}">
                                            @if ($errors->has("items.$index.quantity"))
                                                <div class="text-danger">{{ $errors->first("items.$index.quantity") }}</div>
                                            @endif
                                        </div>

                                        @if ($index > 0)
                                            <div class="form-group col-md-1 d-flex align-items-end">
                                                <button type="button" class="btn btn-danger btn-sm remove-item" style="height: 38px;">✖</button>
                                            </div>
                                        @else
                                            <div class="form-group col-md-1 d-flex align-items-end">
                                                <!-- Empty space for first item -->
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="btn btn-info btn-sm mb-3" id="addItem">+ Add More Item</button>

                            <div class="form-actions mt-4">
                                <button class="btn btn-primary" type="submit">Update</button>
                                <a class="btn btn-secondary ml-3" href="{{ route('admin.stock.index') }}">Cancel</a>
                            </div>

                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let itemIndex = {{ $stock->items->count() }};

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
                    $(this).prop('disabled', true).addClass('d-none');
                } else {
                    $(this).prop('disabled', false).removeClass('d-none');
                }
            });
        });
    }

    $('#addItem').click(function () {
        let newRow = `
            <div class="form-row item-row mb-3">
                <div class="form-group col-md-6">
                    <select name="items[${itemIndex}][product_id]" class="form-control product-select">
                        ${generateProductOptions()}
                    </select>
                </div>
                <div class="form-group col-md-5">
                    <input type="number" name="items[${itemIndex}][quantity]" class="form-control" placeholder="Quantity">
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

    $(document).on('click', '.remove-item', function () {
        $(this).closest('.item-row').remove();
        updateProductDropdowns();
    });

    $(document).on('change', '.product-select', function () {
        updateProductDropdowns();
    });

    $(document).ready(function () {
        updateProductDropdowns();
    });
</script>

@endsection