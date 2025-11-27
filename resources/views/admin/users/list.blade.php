<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Users</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Users</li>
                </ol>
            </div>

            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <!-- filters -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="main-content-label">Users</h6>
                                <div class="d-flex align-items-center">
                                    <div class="form-group mb-0 mr-3">
                                        <div class="form-group mb-0 table-filters"><i class="fa fa-search filter-icon"></i>
                                            <input type="search" class="form-control" placeholder="Search" wire:model.debounce.300ms="search"/>
                                        </div>
                                    </div>
                                     <div class="mr-3">
                                        <div class="form-group mb-0 table-filters"><i class="fa fa-filter mr-2 filter-icon"></i>
                                            <div class="select-down-arrow">
                                                <select class="form-control cp" wire:model="role">
                                                    <option class="text-capitalize" value="">User Role</option>
                                                    <option class="text-capitalize" value="outlet-manager">Outlet Manager</option>
                                                    <option class="text-capitalize" value="warehouse-manager">Warehouse Manager</option>
                                                    <option class="text-capitalize" value="supervisor">Supervisor</option>
                                                </select>
                                            </div>
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
                                    <a href="{{ route('admin.users.create') }}" class="btn ripple btn-main-primary">Add New</a>
                                </div>
                            </div>
                                
                            <div class="table-responsive" wire:loading.remove wire:target="search">
                                <table class="table table-bordered card-table w-100" >
                                    <thead>
                                        <tr>
                                            <th width="5%">S.NO.</th>
                                            <th width="25%">Name</th>
                                            <th width="30%">Email</th>
                                            <th width="30%">Role</th>
                                            <th width="15%">Status</th>
                                            <th width="25%" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($users as $user)
                                            <tr>
                                                <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                                                <td>{{ ucfirst($user->first_name) }} {{ ucfirst($user->last_name) }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    @if ($user->role == 'outlet-manager')
                                                        <span class="badge badge-pill badge-secondary-light">Outlet Manager</span>
                                                    @elseif ($user->role == 'warehouse-manager')
                                                        <span class="badge badge-pill badge-info-light">Warehouse Manager</span>
                                                    @elseif ($user->role == 'supervisor')
                                                        <span class="badge badge-pill badge-primary-light">Supervisor</span>
                                                    @else
                                                        <span class="badge badge-pill badge-danger-light">Unknown</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($user->status)
                                                        <span class="badge badge-pill badge-success-light">Active</span>
                                                    @else
                                                        <span class="badge badge-pill badge-danger-light">Inactive</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-toolbar table-actions" role="toolbar" aria-label="Toolbar with button groups">
                                                        <div class="btn-group btn-group-sm" role="group" aria-label="Second group">
                                                            <!-- <button type="button" class="btn view" data-toggle="tooltip" data-placement="top" data-html="true" title='<strong>View</strong>'>
                                                                <i class="fa fa-eye"></i>
                                                            </button> -->
                                                            <a href="{{ route('admin.users.edit', $user->id) }}" type="button" class="btn edit" data-toggle="tooltip" data-placement="top" data-html="true" title='<strong>Edit</strong>'>
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>
                                                            <button type="button" class="btn delete" onclick="deleteRecord({{$user->id}})" data-toggle="tooltip" data-placement="top" data-html="true" title='<strong>Delete</strong>'>
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            @include('admin.components.record-not-found', ['colspan' => $users])
                                        @endforelse
                                    </tbody>
                                </table>
                                @if ($users->total() > 0)
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>Total Records : {{ $users->total() }}</div>
                                        {{ $users->links('vendor.pagination.custom') }}
                                    </div>
                                @endif

                            </div>
                            <div wire:loading.delay wire:target="search" class="w-100">
                                @include('admin.components.skeleton-loader')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        /** This function is used to delete a record */
        function deleteRecord(id){
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
