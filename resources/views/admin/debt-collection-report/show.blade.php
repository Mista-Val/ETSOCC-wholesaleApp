@extends('admin.sub_layout')
@section('title', 'Debt Collection Report Details')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            
            {{-- Page Header --}}
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Debt & Debtors Report Details</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.debt-collection.index') }}">Debt & Debtors Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Details</li>
                </ol>
            </div>

            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">

                            {{-- Payment Info --}}
                            <h4 class="mb-4">Payment Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="20%">Payment ID</th>
                                    <td>#{{ $payment->id }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Date</th>
                                    <td>{{ $payment->date ? \Carbon\Carbon::parse($payment->date)->format('d M, Y') : $payment->created_at->format('d M, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Location</th>
                                    <td>{{ $payment->location->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Method</th>
                                    <td>
                                        <span class="badge badge-pill badge-primary-light">
                                            {{ ucfirst($payment->payment_method) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Amount Collected</th>
                                    <td>
                                        <strong class="text-success">
                                            ${{ number_format($payment->amount, 2) }}
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>
                                        <span class="badge badge-pill badge-info-light">
                                            {{ ucwords(str_replace('_', ' ', $payment->type)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Remarks</th>
                                    <td>{{ $payment->remarks ?? '-' }}</td>
                                </tr>
                            </table>

                            {{-- Customer Info --}}
                            <h4 class="mt-5 mb-4">Customer Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="20%">Customer Name</th>
                                    <td>{{ $payment->coustomer->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone Number</th>
                                    <td>{{ $payment->coustomer->phone_number ?? 'N/A' }}</td>
                                </tr>
                                {{-- <tr>
                                    <th>Email</th>
                                    <td>{{ $payment->coustomer->email ?? 'N/A' }}</td>
                                </tr> --}}
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $payment->coustomer->address ?? 'N/A' }}</td>
                                </tr>
                            </table>

                            {{-- Location Balance Info --}}
                            <h4 class="mt-5 mb-4">Customer Balance at {{ $payment->location->name ?? 'This Location' }}</h4>
                            
                            @if($locationBalance)
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="20%">Location Name</th>
                                        <td>{{ $locationBalance->location->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total Available Balance</th>
                                        <td>
                                            <span class="badge badge-pill {{ $locationBalance->balance > 0 ? 'badge-warning' : 'badge-success' }}" style="font-size: 16px; padding: 8px 15px;">
                                                ${{ number_format($locationBalance->balance, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Credit Balance (Available Credit)</th>
                                        <td>
                                            <span class="badge badge-pill badge-info" style="font-size: 16px; padding: 8px 15px;">
                                                ${{ number_format($locationBalance->credit_balance, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated</th>
                                        <td>{{ $locationBalance->updated_at->format('d M, Y') }}</td>
                                    </tr>
                                </table>
                            @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> No balance record found for this customer at this location.
                                </div>
                            @endif

                            {{-- Back Button --}}
                            <div class="text-right mt-4">
                                <a href="{{ route('admin.debt-collection.index') }}" class="btn btn-secondary">
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