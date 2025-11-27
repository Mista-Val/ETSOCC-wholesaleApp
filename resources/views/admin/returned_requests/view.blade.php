@extends('admin.sub_layout')
@section('title', 'View Stock')
@section('sub_content')

    <div class="main-content side-content pt-0">
        <div class="container-fluid">
            <div class="inner-body">

                <!-- Header -->
                <div class="page-header d-block">
                    <h2 class="main-content-title tx-24 mg-b-5">Returned Requests Detail</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.returned-requests-list') }}">All Returned
                                Requests</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View</li>
                    </ol>
                </div>

                <!-- Stock Detail -->
                <div class="card custom-card">
                    <div class="card-body">

                        <h4 class="mb-4">Returned Requests Detail</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th>Stock ID</th>
                                <td>#{{ $data->id }}</td>
                            </tr>
                            <tr>
                                <th>Outlet</th>
                                <td>{{ ucfirst($data->senderOutlet->name) ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>Warehouse</th>
                                <td>{{ ucfirst($data->outlet->name) ?? '-' }}</td>
                            </tr>
                               <tr>
                                <th>Status</th>
                            <td>
                                @php
                                    $statusConfig = [
                                        'pending' => ['class' => 'warning', 'label' => 'Pending Warehouse'],
                                        'accepted_by_warehouse' => ['class' => 'info', 'label' => 'Pending Supervisor'],
                                        'accepted_by_supervisor' => [
                                            'class' => 'primary',
                                            'label' => 'Pending Admin Approval',
                                        ],
                                        'accepted_by_all' => [
                                            'class' => 'success',
                                            'label' => 'Accepted & Completed',
                                        ],
                                        'rejected_by_warehouse' => [
                                            'class' => 'danger',
                                            'label' => 'Rejected by Warehouse',
                                        ],
                                        'rejected_by_supervisor' => [
                                            'class' => 'danger',
                                            'label' => 'Rejected by Supervisor',
                                        ],
                                        'rejected_by_admin' => ['class' => 'danger', 'label' => 'Rejected by Admin'],
                                    ];
                                    $currentStatus = $statusConfig[$data->status] ?? [
                                        'class' => 'secondary',
                                        'label' => ucfirst($data->status),
                                    ];
                                @endphp
                                <span class="badge badge-pill badge-{{ $currentStatus['class'] }}">
                                    {{ $currentStatus['label'] }}
                                </span>
                            </td>
                               </tr>

                            <tr>
                                <th>Date</th>
                                <td>{{ $data->created_at->format('d M, Y') }}</td>
                            </tr>
                        </table>

                        <!-- Products -->
                        <h4 class="mt-5 mb-3">Products in Stock</h4>
                        @if ($data->items->count())
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>S.NO.</th>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data->items as $index => $item)
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
                            <p>No products found in this data.</p>
                        @endif

                        <!-- <a href="{{ route('admin.stock.index') }}" class="btn btn-secondary mt-3">Back</a> -->

                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
