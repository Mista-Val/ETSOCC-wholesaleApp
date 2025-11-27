<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
       
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Debt & Debtors Reports</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.debt-collection.index') }}">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Debt & Debtors Reports</li>
                </ol>
            </div>
            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="main-content-label">Debt & Debtors Reports</h6>
                                <div class="d-flex align-items-center">
                                <div class="form-group mb-0 mr-3">
                                        <div class="form-group mb-0 table-filters" onclick="this.querySelector('input').showPicker()" style="cursor: pointer;">
                                            <i class="fa fa-calendar filter-icon" style="pointer-events: none;"></i>
                                            <input type="date" class="form-control" placeholder="Select Date" wire:model="date" style="cursor: pointer;" />
                                        </div>
                                    </div>
                                    {{-- Search Filter --}}
                                    <div class="form-group mb-0 mr-3">
                                        <div class="form-group mb-0 table-filters">
                                            <i class="fa fa-search filter-icon"></i>
                                            <input type="search" class="form-control" placeholder="Search Customer/Location/Type" wire:model.debounce.300ms="search" />
                                        </div>
                                    </div>
                                    
                                    {{-- Type Filter (e.g., Cash, Credit, Online - adjust options as needed) --}}
                                    <div class="mr-3">
                                        <div class="form-group mb-0 table-filters">
                                            <i class="fa fa-filter mr-2 filter-icon"></i>
                                            <div class="select-down-arrow">
                                                <select class="form-control cp" wire:model="paymentMethod">
                                                    <option class="text-capitalize" value="">Payment Type</option>
                                                    <option class="text-capitalize" value="cash">Cash</option>
                                                    <option class="text-capitalize" value="bank">Bank</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Status Filter (pending, accepted, rejected) --}}
                                    {{-- <div class="mr-3">
                                        <div class="form-group mb-0 table-filters">
                                            <i class="fa fa-info-circle mr-2 filter-icon"></i>
                                            <div class="select-down-arrow">
                                                <select class="form-control cp" wire:model="status">
                                                    <option class="text-capitalize" value="">Status</option>
                                                    <option class="text-capitalize" value="pending">Pending</option>
                                                    <option class="text-capitalize" value="accepted">Accepted</option>
                                                    <option class="text-capitalize" value="rejected">Rejected</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div> --}}
                                    
                                    <button type="button" class="btn ripple btn-warning mr-3" wire:click="resetFilters">Reset Filters</button>

                                    {{-- EXPORT BUTTON --}}
                                    <button type="button" class="btn ripple btn-success" wire:click="exportCsv">
                                        <i class="fa fa-file-excel-o mr-1"></i> Export CSV
                                    </button>
                                </div>
                            </div>

                            {{-- Down Payment Table --}}
                            <div class="table-responsive" wire:loading.remove wire:target="search, type, status">
                                <table class="table table-bordered card-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="5%">ID</th>
                                            <th width="15%">Date</th>
                                            <th width="15%">Amount</th>
                                            <th width="15%">Payment Method</th> <th width="20%">Location</th> <th width="20%">Customer Name</th>
                                            <th width="10%">Action</th>
                                             {{-- <th width="10%">Status</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($payments as $payment) <tr>
                                            <td>{{ ($payments->currentPage() - 1) * $payments->perPage() + $loop->iteration }}</td>
                                            <td>{{ \Carbon\Carbon::parse($payment->date ?? $payment->created_at)->format('Y-m-d') }}</td>
                                            <td><span class="badge badge-pill badge-primary-light">$ {{ number_format($payment->amount, 2) }}</span></td>
                                            <td><span class="badge badge-pill badge-info-light">{{ ucfirst($payment->payment_method) }}</span></td>
                                            <td>
                                                <span class="badge badge-pill badge-warning-light"> {{ $payment->location->name ?? 'N/A' }} </span>
                                            </td>
                                            <td><span class="badge badge-pill badge-danger-light">{{ $payment->coustomer->name ?? 'N/A' }}</span></td>
                                            {{-- <td><span class="badge badge-pill badge-{{ $payment->status === 'accepted' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'primary') }}">{{ ucfirst($payment->status ?? 'N/A') }}</span></td> --}}
                                        <td class="">
                                              <a href="{{ route('admin.debt-collection.show', $payment->id) }}" type="button" class="btn view" data-toggle="tooltip" data-placement="top" data-html="true" title='<strong>View</strong>'>
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                            </td>
                                        </tr>
                                        @empty
                                        @include('admin.components.record-not-found', ['colspan' => 7])
                                        @endforelse
                                    </tbody>
                                </table>

                                @if ($payments->total() > 0)
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>Total Records : {{ $payments->total() }}</div>
                                    {{ $payments->links('vendor.pagination.custom') }}
                                </div>
                                @endif

                                <div wire:loading.delay wire:target="search, type, status" class="w-100">
                                    @include('admin.components.skeleton-loader')
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>