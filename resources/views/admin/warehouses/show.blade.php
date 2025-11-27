@extends('admin.sub_layout')
@section('title', 'Warehouse Details')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            
            {{-- Page Header --}}
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Warehouse Details</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.warehouses.index') }}">Warehouses</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Details</li>
                </ol>
            </div>

            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">

                            {{-- Warehouse Info --}}
                            <h4 class="mb-4">Basic Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="20%">Name</th>
                                    <td>{{ $warehouse->name }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $warehouse->address }}</td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{{ $warehouse->description ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($warehouse->status)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Assigned Manager</th>
                                    <td>
                                        {{ $warehouse->user->name ?? 'N/A' }} ({{ $warehouse->user->email ?? '-' }})
                                    </td>
                                </tr>
                            </table>

                            {{-- Stock Info --}}
                            <h4 class="mt-5 mb-4">Stock Overview</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th>Product Name</th>
                                            <th width="20%">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($warehouse->stocks) && $warehouse->stocks)
                                        @forelse ($warehouse->stocks as $index => $stock)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $stock->product->name ?? '-' }}</td>
                                                <td>{{ $stock->product_quantity }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No stock data found.</td>
                                            </tr>
                                        @endforelse
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            {{-- Back Button --}}
                            <div class="text-right mt-4">
                                <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary">
                                    <i class="si si-arrow-left"></i> Back to List
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
