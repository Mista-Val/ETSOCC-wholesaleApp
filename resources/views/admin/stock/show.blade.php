@extends('admin.sub_layout')
@section('title', 'View Stock')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">

            <!-- Header -->
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Stock Detail</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.stock.index') }}">All Stocks</a></li>
                    <li class="breadcrumb-item active" aria-current="page">View</li>
                </ol>
            </div>

            <!-- Stock Detail -->
            <div class="card custom-card">
                <div class="card-body">

                    <h4 class="mb-4">Stock Information</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th>Supplier Name</th>
                            <td>{{ ucfirst($stock->supplier_name) }}</td>
                        </tr>
                        <tr>
                            <th>Warehouse</th>
                            <td>{{ $stock->receiverWarehouse->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge badge-{{ $stock->status == 'created' ? 'info' : 'success' }}">
                                    {{ ucfirst($stock->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $stock->created_at->format('d M, Y') }}</td>
                        </tr>
                    </table>

                    <!-- Products -->
                    <h4 class="mt-5 mb-3">Products in Stock</h4>
                    @if($stock->items->count())
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stock->items as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                                            <td>{{ $item->set_quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>No products found in this stock.</p>
                    @endif

                    <a href="{{ route('admin.stock.index') }}" class="btn btn-secondary mt-3">Back</a>

                </div>
            </div>

        </div>
    </div>
</div>

@endsection
