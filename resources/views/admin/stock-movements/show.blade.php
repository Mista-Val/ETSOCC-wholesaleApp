@extends('admin.sub_layout')
@section('title', 'Stock Movement Report Details')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            
            {{-- Page Header --}}
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Stock Movement Report Details</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.stock-movement.index') }}">Stock Movements Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Details</li>
                </ol>
            </div>

            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">

                            {{-- Stock Movement Info --}}
                            <h4 class="mb-4">Stock Movement Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="20%">Transfer ID</th>
                                    <td>#{{ $transfer->id }}</td>
                                </tr>
                                <tr>
                                    <th>Sender Type</th>
                                    <td>
                                        <span class="badge badge-pill badge-info-light">
                                            {{ ucfirst($transfer->type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Sender Location</th>
                                    <td>
                                        @if($transfer->type === 'admin')
                                            Admin
                                        @else
                                            {{ $transfer->warehouse->name ?? 'N/A' }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Receiver Location</th>
                                    <td>{{ $transfer->outlet->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Transfer Type</th>
                                    <td>
                                        <span class="badge badge-pill badge-secondary-light">
                                            {{ ucwords(str_replace('_', ' ', $transfer->transfer_type)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($transfer->status === 'pending')
                                            <span class="badge badge-pill badge-warning-light">Pending</span>
                                        @elseif($transfer->status === 'transferred')
                                            <span class="badge badge-pill badge-info-light">Transferred</span>
                                        @elseif($transfer->status === 'received')
                                            <span class="badge badge-pill badge-success-light">Received</span>
                                        @elseif($transfer->status === 'rejected')
                                            <span class="badge badge-pill badge-danger-light">Rejected</span>
                                        @else
                                            <span class="badge badge-pill badge-secondary-light">{{ ucfirst($transfer->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Transfer Date</th>
                                    <td>{{ $transfer->created_at->format('d M, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Remark</th>
                                    <td>{{ $transfer->remark ?? '-' }}</td>
                                </tr>
                            </table>

                            {{-- Products Info --}}
                            <h4 class="mt-5 mb-4">Products Transferred</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th>Product Name</th>
                                            <th width="12%">Set Quantity</th>
                                            <th width="12%">Received Quantity</th>
                                            <th width="10%">Type</th>
                                            {{-- <th>Remarks</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($transfer->items as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->product->name ?? '-' }}</td>
                                                <td>{{ $item->set_quantity }}</td>
                                                <td>{{ $item->received_quantity ?? '-' }}</td>
                                                <td>
                                                    <span class="badge badge-pill badge-primary-light">
                                                        {{ ucfirst($item->type ?? '-') }}
                                                    </span>
                                                </td>
                                                {{-- <td>{{ $item->remarks ?? '-' }}</td> --}}
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No products found in this transfer.</td>
                                            </tr>
                                        @endforelse
                                        
                                        @if($transfer->items->isNotEmpty())
                                            <tr class="table-active">
                                                <td colspan="2" class="text-right"><strong>Total Set Quantity:</strong></td>
                                                <td><strong>{{ $transfer->items->sum('set_quantity') }}</strong></td>
                                                <td colspan="3"></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            {{-- Back Button --}}
                            <div class="text-right mt-4">
                                <a href="{{ route('admin.stock-movement.index') }}" class="btn btn-secondary">
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