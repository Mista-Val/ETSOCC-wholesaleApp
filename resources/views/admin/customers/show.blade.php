@extends('admin.sub_layout')
@section('title', 'View customer')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">

            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Customer Detail</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">All Customers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">View</li>
                </ol>
            </div>

            <div class="card custom-card">
                <div class="card-body">

                    <h4 class="mb-4">Customer Information</h4>
                    <table class="table table-bordered mb-5">
                        <tr>
                            <th style="width: 200px;">Name</th>
                            <td>{{ ucfirst($customer->name) }}</td>
                        </tr>
                        <tr>
                            <th>Phone Number</th>
                            <td>{{ $customer->phone_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Total Sales Count</th>
                            <td>{{ $customer->sales->count() }}</td> 
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $customer->created_at->format('d M, Y') }}</td>
                        </tr>
                    </table>

                    {{-- -------------------------------------------------------------------------------- --}}

                    <h4 class="mb-4">Sales History (Total: {{ $customer->sales->count() }} Sales)</h4>

                    @forelse ($customer->sales as $sale)
                        <div class="card border mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    Sale ID: #{{ $sale->id }} | 
                                    Date: {{ $sale->created_at->format('d M, Y') }} | 
                                    Total Amount: ${{ number_format($sale->total_amount, 2) }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-3">
                                    <strong>Location:</strong> 
                                    {{ $sale->location->name ?? 'N/A' }} 
                                    <span class="badge bg-primary text-white">{{ $sale->location->type ?? 'Unknown Type' }}</span>
                                </p>

                                <h6 class="border-bottom pb-2">Products Sold:</h6>
                                <ul class="list-group list-group-flush">
                                    @forelse ($sale->soldProducts as $item)
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0">
                                            <span>
                                                {{ $item->product->name ?? 'Unknown Product' }}
                                            </span>
                                            <span class="badge bg-info text-white">
                                                Qty: {{ $item->quantity }}
                                            </span>
                                        </li>
                                    @empty
                                        <li class="list-group-item px-0 text-muted">No products found for this sale.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    @empty
                        <p class="alert alert-info">No sales recorded for this customer yet.</p>
                    @endforelse

                    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary mt-3">Back</a>

                </div>
            </div>
            
            {{-- ADDED SPACER: This creates an empty space equal to roughly a large footer height --}}
            <div style="height: 80px;"></div> 
            
        </div>
    </div>
</div>

@endsection