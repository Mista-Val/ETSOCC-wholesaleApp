@extends('admin.sub_layout')
@section('title', 'Product Details')
@section('sub_content')

    <div class="main-content side-content pt-0">
        <div class="container-fluid">
            <div class="inner-body">

                {{-- Page Header --}}
                <div class="page-header d-block">
                    <h2 class="main-content-title tx-24 mg-b-5">Product Details</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
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
                                        <th width="20%">Image</th>
                                        <td>
                                            @if ($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                    alt="{{ $product->name }}" width="100">
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th width="20%">Name</th>
                                        <td>{{ $product->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>SKU</th>
                                        <td>{{ $product->sku }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if ($product->status)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Minimum Price</th>
                                        <td>{{ $product->min_price ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Maximum Price</th>
                                        <td>{{ $product->max_price ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Category</th>
                                        <td>{{ ucwords($product->category) ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Product Package</th>
                                        <td>{{ ucwords($product->product_package) ?? 'N/A' }}</td>
                                    </tr>
                                      <tr>
                                        <th>Product Quantity</th>
                                        <td>{{($product->package_quantity) ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ $product->created_at->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Updated At</th>
                                        <td>{{ $product->updated_at->format('d M Y') }}</td>
                                    </tr>
                                </table>

                                {{-- Back Button --}}
                                <div class="text-right mt-4">
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
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
