@extends('admin.sub_layout')
@section('title', 'Sales Report Details')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            
            {{-- Page Header --}}
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Sales Report Details</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sales-report.index') }}">Sales Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Details</li>
                </ol>
            </div>

            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">

                            {{-- Sales Info --}}
                            <h4 class="mb-4">Sale Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="20%">Sale ID</th>
                                    <td>#{{ $sale->id }}</td>
                                </tr>
                                <tr>
                                    <th>Location</th>
                                    <td>{{ $sale->location->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Customer Name</th>
                                    <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                                </tr>
                                 <tr>
                                    <th>Customer Phone Number</th>
                                    <td>{{ $sale->customer->phone_number ?? 'N/A' }}</td>
                                </tr>
                                 <tr>
                                    <th>Customer Address</th>
                                    <td>{{ $sale->customer->address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Method</th>
                                    <td>
                                        <span class="badge badge-pill badge-primary-light">
                                            {{ ucfirst($sale->payment_method) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Amount</th>
                                    <td>
                                        <strong class="text-success">
                                            ${{ number_format($sale->total_amount, 2) }}
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Sale Date</th>
                                    <td>{{ $sale->created_at->format('d M, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Remark</th>
                                    <td>{{ $sale->remark ?? '-' }}</td>
                                </tr>
                            </table>

                            {{-- Products Info --}}
                            <h4 class="mt-5 mb-4">Products Sold</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th>Product Name</th>
                                            <th width="15%">Unit Price</th>
                                            <th width="10%">Quantity</th>
                                            <th width="15%">Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($sale->soldProducts as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->product->name ?? '-' }}</td>
                                                <td>${{ number_format($item->per_unit_amount, 2) }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>${{ number_format($item->total_product_amount, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No products found in this sale.</td>
                                            </tr>
                                        @endforelse
                                        
                                        @if($sale->soldProducts->isNotEmpty())
                                            <tr class="table-active">
                                                <td colspan="4" class="text-right"><strong>Grand Total:</strong></td>
                                                <td><strong>${{ number_format($sale->soldProducts->sum('total_product_amount'), 2) }}</strong></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            {{-- Back Button --}}
                            <div class="text-right mt-4">
                                <a href="{{ route('admin.sales-report.index') }}" class="btn btn-secondary">
                                    <i class="si si-arrow-left"></i> Back to List
                                </a>
                                {{-- <button onclick="window.print()" class="btn btn-primary">
                                    <i class="si si-printer"></i> Print Report
                                </button> --}}
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection