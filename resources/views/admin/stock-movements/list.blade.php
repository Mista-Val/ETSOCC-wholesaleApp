<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Stock Movements Reports</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.stock-movement.index') }}">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Stock Movements</li>
                </ol>
            </div>

            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="main-content-label">Stock Movements Reports</h6>
                                <div class="d-flex align-items-center">
                                    <div class="form-group mb-0 mr-3">
                                        <div class="form-group mb-0 table-filters"
                                            onclick="this.querySelector('input').showPicker()" style="cursor: pointer;">
                                            <i class="fa fa-calendar filter-icon" style="pointer-events: none;"></i>
                                            <input type="date" class="form-control" placeholder="Select Date"
                                                wire:model="date" style="cursor: pointer;" />
                                        </div>
                                    </div>
                                    {{-- Search Filter --}}
                                    <div class="form-group mb-0 mr-3">
                                        <div class="form-group mb-0 table-filters">
                                            <i class="fa fa-search filter-icon"></i>
                                            <input type="search" class="form-control"
                                                placeholder="Search Sender/Receiver/Remark"
                                                wire:model.debounce.300ms="search" />
                                        </div>
                                    </div>

                                    {{-- Transfer Type Filter (admin, warehouse, outlet) --}}
                                    <div class="mr-3">
                                        <div class="form-group mb-0 table-filters">
                                            <i class="fa fa-filter mr-2 filter-icon"></i>
                                            <div class="select-down-arrow">
                                                <select class="form-control cp" wire:model="type">
                                                    <option class="text-capitalize" value="">Sender Type</option>
                                                    <option class="text-capitalize" value="admin">Admin</option>
                                                    <option class="text-capitalize" value="warehouse">Warehouse</option>
                                                    <option class="text-capitalize" value="outlet">Outlet</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Status Filter (pending, transferred, received, rejected) --}}
                                    <div class="mr-3">
                                        <div class="form-group mb-0 table-filters">
                                            <i class="fa fa-info-circle mr-2 filter-icon"></i>
                                            <div class="select-down-arrow">
                                                <select class="form-control cp" wire:model="status">
                                                    <option class="text-capitalize" value="">Status</option>
                                                    <option class="text-capitalize" value="pending">Pending</option>
                                                    <option class="text-capitalize" value="created">Created</option>
                                                    <option class="text-capitalize" value="dispatched">Dispatched
                                                    </option>
                                                    <option class="text-capitalize" value="partially accepted">Partially
                                                        accepted</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="btn ripple btn-warning mr-3"
                                        wire:click="resetFilters">Reset Filters</button>

                                    {{-- EXPORT BUTTON --}}
                                    <button type="button" class="btn ripple btn-success" wire:click="exportCsv">
                                        <i class="fa fa-file-excel-o mr-1"></i> Export CSV
                                    </button>
                                </div>
                            </div>

                            {{-- Transfers Table --}}
                            <div class="table-responsive" wire:loading.remove wire:target="search, type, status">
                                <table class="table table-bordered card-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="5%">ID</th>
                                            <th width="15%">Date</th>
                                            <th width="15%">Sender Type</th>
                                            <th width="20%">Sender Location</th>
                                            <th width="20%">Receiver Location</th>
                                            <th width="10%">Status</th>
                                            <th width="10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($transfers as $transfer)
                                            {{-- Changed $sales to $transfers --}}
                                            <tr>
                                                <td>{{ ($transfers->currentPage() - 1) * $transfers->perPage() + $loop->iteration }}
                                                </td>
                                                <td>{{ $transfer->created_at->format('Y-m-d') }}</td>
                                                <td><span
                                                        class="badge badge-pill badge-info-light">{{ ucfirst($transfer->type) }}</span>
                                                </td>
                                                <td>
                                                    @if ($transfer->type === 'admin')
                                                        <span class="badge badge-pill badge-success-light"> Admin</span>
                                                    @else
                                                        <span class="badge badge-pill badge-warning-light">
                                                            {{ $transfer->warehouse->name ?? 'N/A' }} </span>
                                                    @endif
                                                </td>
                                                <td><span
                                                        class="badge badge-pill badge-danger-light">{{ $transfer->outlet->name ?? 'N/A' }}</span>
                                                </td>
                                                <td><span
                                                        class="badge badge-pill badge-{{ $transfer->status === 'received' ? 'success' : ($transfer->status === 'pending' ? 'warning' : 'primary') }}">{{ ucfirst($transfer->status) }}</span>
                                                </td>
                                                <td class="">
                                              <a href="{{ route('admin.stock-movement.show', $transfer->id) }}" type="button" class="btn view" data-toggle="tooltip" data-placement="top" data-html="true" title='<strong>View</strong>'>
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                            </td>
                                            </tr>
                                        @empty
                                            @include('admin.components.record-not-found', ['colspan' => 7])
                                        @endforelse
                                    </tbody>
                                </table>

                                @if ($transfers->total() > 0)
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>Total Records : {{ $transfers->total() }}</div>
                                        {{ $transfers->links('vendor.pagination.custom') }}
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

{{-- Removed the deleteRecord script as the destroy method was removed from the component --}}
