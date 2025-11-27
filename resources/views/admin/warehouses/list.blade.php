<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">

            {{-- Page Header --}}
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Warehouse</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.warehouses.index') }}">Warehouses</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Warehouses</li>
                </ol>
            </div>

            {{-- Content Row --}}
            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="main-content-label">warehouse</h6>
                                <div class="d-flex align-items-center">
                                    <div class="form-group mb-0 mr-3">
                                        <div class="form-group mb-0 table-filters"><i class="fa fa-search filter-icon"></i>
                                            <input type="search" class="form-control" placeholder="Search" wire:model.debounce.300ms="search"/>
                                        </div>
                                    </div>
                                    <div class="mr-3">
                                        <div class="form-group mb-0 table-filters"><i class="fa fa-filter mr-2 filter-icon"></i>
                                            <div class="select-down-arrow">
                                                <select class="form-control cp" wire:model="status">
                                                    <option class="text-capitalize" value="">User Status</option>
                                                    <option class="text-capitalize" value="0">Inactive</option>
                                                    <option class="text-capitalize" value="1">Active</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn ripple btn-main-primary mr-3" wire:click="resetFilters">Reset Filters</button>
                                    <a href="{{ route('admin.warehouses.create') }}" class="btn ripple btn-main-primary">Add New</a>
                                </div>
                            </div>
                            <div class="table-responsive"  wire:loading.remove wire:target="search">
                                <table class="table table-bordered card-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="5%">S.NO.</th>
                                            <th width="25%">Name</th>
                                            <th width="30%">Address</th>
                                            <th width="30%">Assign To</th>
                                            <th width="15%">Status</th>
                                            <th width="25%" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($warehouses as $item)
                                            <tr>
                                                <td>{{ ($warehouses->currentPage() - 1) * $warehouses->perPage() + $loop->iteration }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->address }}</td>
                                                <td>{{ $item->user ? ucfirst($item->user->name) : 'N/A' }}</td>
                                                <td>
                                                    @if($item->status)
                                                        <span class="badge badge-pill badge-success-light">Active</span>
                                                    @else
                                                        <span class="badge badge-pill badge-danger-light">Inactive</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-toolbar table-actions" role="toolbar" aria-label="Toolbar with button groups">
                                                        <div class="btn-group btn-group-sm" role="group" aria-label="Second group">
                                                            <a href="{{ route('admin.warehouses.show', $item->id) }}" type="button" class="btn view" data-toggle="tooltip" data-placement="top" data-html="true" title='<strong>View</strong>'>
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admin.warehouses.edit', $item->id) }}" type="button" class="btn edit" data-toggle="tooltip" data-placement="top" data-html="true" title='<strong>Edit</strong>'>
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>
                                                            <button type="button" class="btn delete" onclick="deleteRecord({{ $item->id }})" data-toggle="tooltip" data-placement="top" data-html="true" title='<strong>Delete</strong>'>
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            @include('admin.components.record-not-found', ['colspan' => 6])
                                        @endforelse
                                    </tbody>
                                </table>

                                {{-- Pagination --}}
                                @if ($warehouses->total() > 0)
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>Total Records : {{ $warehouses->total() }}</div>
                                        {{ $warehouses->links('vendor.pagination.custom') }}
                                    </div>
                                @endif

                                <div wire:loading.delay wire:target="search" class="w-100">
                                    @include('admin.components.skeleton-loader')
                                </div>

                            </div> {{-- table-responsive --}}
                        </div> {{-- card-body --}}
                    </div> {{-- card --}}
                </div>
            </div> {{-- row --}}
        </div>
    </div>
    {{-- Delete Confirmation Script --}}
    <script>
        function deleteRecord(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to delete this record?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('destroy', id);
                }
            });
        }
    </script>
</div>