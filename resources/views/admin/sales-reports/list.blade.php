<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Sales Reports</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sales-report.index') }}">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Sales</li>
                </ol>
            </div>

            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="main-content-label">Sales Reports</h6>
                                <div class="d-flex align-items-center flex-wrap">
                                    {{-- Date Filter --}}
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
                                            <input type="search" class="form-control" placeholder="Search Customer/Location" wire:model.debounce.300ms="search" />
                                        </div>
                                    </div>
                                    
                                    {{-- Payment Method Filter --}}
                                    <div class="mr-3">
                                        <div class="form-group mb-0 table-filters">
                                            <i class="fa fa-filter mr-2 filter-icon"></i>
                                            <div class="select-down-arrow">
                                                <select class="form-control cp" wire:model="payment_method">
                                                    <option class="text-capitalize" value="">Payment Method</option>
                                                    <option class="text-capitalize" value="cash">Cash</option>
                                                    <option class="text-capitalize" value="credit">Credit</option>
                                                    <option class="text-capitalize" value="down payment">Down Payment</option>
                                                    <option class="text-capitalize" value="bank transfer">Bank Transfer</option>
                                                    {{-- Add other payment methods as needed --}}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="button" class="btn ripple btn-warning mr-3" wire:click="resetFilters">Reset Filters</button>

                                    {{-- EXPORT BUTTON --}}
                                    <button type="button" class="btn ripple btn-success" wire:click="exportCsv">
                                        <i class="fa fa-file-excel-o mr-1"></i> Export CSV
                                    </button>
                                </div>
                            </div>

                            {{-- Sales Table --}}
                            <div class="table-responsive" wire:loading.remove wire:target="search, payment_method, date">
                                <table class="table table-bordered card-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="5%">S.NO.</th>
                                            <th width="15%">Sale Date</th>
                                            <th width="20%">Customer</th>
                                            <th width="20%">Location</th>
                                            <th width="15%">Payment Method</th>
                                            <th width="15%">Total Amount</th>
                                            <th width="10%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($sales as $sale)
                                        <tr>
                                            <td>{{ ($sales->currentPage() - 1) * $sales->perPage() + $loop->iteration }}</td>
                                            <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                                            <td>{{ ucwords($sale->customer->name) ?? 'N/A' }}</td>
                                            <td>{{ $sale->location->name ?? 'N/A' }}</td>
                                            <td><span class="badge badge-pill badge-primary-light">{{ ucfirst($sale->payment_method) }}</span></td>
                                            <td><strong>${{ number_format($sale->total_amount, 2) }}</strong></td>
                                            <td class="text-center">
                                              <a href="{{ route('admin.sales-report.show', $sale->id) }}" type="button" class="btn view" data-toggle="tooltip" data-placement="top" data-html="true" title='<strong>View</strong>'>
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                            </td>
                                        </tr>
                                        @empty
                                        @include('admin.components.record-not-found', ['colspan' => 7])
                                        @endforelse
                                    </tbody>
                                </table>

                                @if ($sales->total() > 0)
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>Total Records : {{ $sales->total() }}</div>
                                    {{ $sales->links('vendor.pagination.custom') }}
                                </div>
                                @endif

                                <div wire:loading.delay wire:target="search, payment_method, date" class="w-100">
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

<script>
    function deleteRecord(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you really want to delete this sales record?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Call the Livewire destroy method
                Livewire.emit('destroy', id); 
            }
        });
    }
</script>