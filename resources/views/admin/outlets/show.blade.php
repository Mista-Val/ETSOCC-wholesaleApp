@extends('admin.sub_layout')
@section('title', 'Outlet Details')
@section('sub_content')

    <div class="main-content side-content pt-0">
        <div class="container-fluid">
            <div class="inner-body">

                {{-- Page Header --}}
                <div class="page-header d-block">
                    <h2 class="main-content-title tx-24 mg-b-5">Outlet Details</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.outlets.index') }}">Outlets</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </div>

                <div class="row sidemenu-height">
                    <div class="col-lg-12">
                        <div class="card custom-card">
                            <div class="card-body">

                                {{-- Product Info --}}
                                <h4 class="mb-4">Basic Information</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="20%">Name</th>
                                        <td>{{ $outlet->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td>{{ $outlet->address }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if ($outlet->status)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td>{{ $outlet->description ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ $outlet->created_at->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Updated At</th>
                                        <td>{{ $outlet->updated_at->format('d M Y') }}</td>
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
                                        @if(!empty($outlet->stocks) && $outlet->stocks)
                                        @forelse ($outlet->stocks as $index => $stock)
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
                                    <a href="{{ route('admin.outlets.index') }}" class="btn btn-secondary">
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
