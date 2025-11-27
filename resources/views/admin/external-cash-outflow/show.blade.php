@extends('admin.sub_layout')
@section('title', 'External Cash Inflow Details')
@section('sub_content')

    <div class="main-content side-content pt-0">
        <div class="container-fluid">
            <div class="inner-body">

                {{-- Page Header --}}
                <div class="page-header d-block">
                    <h2 class="main-content-title tx-24 mg-b-5">External Cash Outflows Details</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.external-cash-outflow.index') }}">External Cash
                                Outflows</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </div>

                <div class="row sidemenu-height">
                    <div class="col-lg-12">
                        <div class="card custom-card">
                            <div class="card-body">

                                {{-- Inflow Info --}}
                                <h4 class="mb-4">Outflows Information</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="20%">Source</th>
                                        <td>{{ $data->source }}</td>
                                    </tr>
                                    <tr>
                                        <th>Amount</th>
                                        <td>${{ number_format($data->amount, 2) }}</td>
                                    </tr>
                                    {{-- <tr>
                                        <th>Received Date</th>
                                        <td>{{ $data->received_date->format('Y-m-d') }}</td>
                                    </tr> --}}
                                    <tr>
                                        <th>Send To</th>
                                        <td>{{ $data->send_to }}</td>
                                    </tr>

                                    {{-- ADDED: Assigned Supervisor --}}
                                    <tr>
                                        <th>Supervisor</th>
                                        {{-- This assumes your ExternalCashInflow model has a 'supervisor' relationship defined (e.g., $data->supervisor) --}}
                                        <td>{{ $data->supervisor->name ?? 'N/A' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Remarks</th>
                                        <td>{{ $data->remarks ?? '-' }}</td>
                                    </tr>
                                    {{-- Assuming a 'status' field exists on your model if you want to show it --}}
                                    {{-- <tr>
                                    <th>Status</th>
                                    <td>
                                        @if ($data->status)
                                            <span class="badge badge-success">Approved</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr> --}} 
                                </table>

                                 @if ($data->supervisor)
                                        <div class="row mb-4">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="card shadow-sm border-0"
                                                    style="background: linear-gradient(90deg, #00b09b, #96c93d); border-radius: 12px;">
                                                    <div class="card-body text-white d-flex align-items-center">
                                                        <div class="mr-3">
                                                            <i class="fa fa-wallet fa-2x"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">Available Balance
                                                                ({{ $data->supervisor->name }})</h6>
                                                            <h4 class="mb-0 font-weight-bold">
                                                                ${{ number_format($supervisorBalance, 2) }}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                {{-- Back Button --}}
                                <div class="text-right mt-4">
                                    <a href="{{ route('admin.external-cash-outflow.index') }}" class="btn btn-secondary">
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
