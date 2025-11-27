<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Customers</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Customers</li>
                </ol>
            </div>

            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="main-content-label">Customers</h6>
                                <div class="d-flex align-items-center">
                                    <div class="form-group mb-0 mr-3">
                                        <div class="form-group mb-0 table-filters"><i
                                                class="fa fa-search filter-icon"></i>
                                            <input type="search" class="form-control" placeholder="Search"
                                                wire:model.debounce.300ms="search" />
                                        </div>
                                    </div>
                                    {{-- Filter blocks removed as they are commented out in the component and original design --}}
                                    <button type="button" class="btn ripple btn-main-primary mr-3"
                                        wire:click="resetFilters">Reset Filters</button>
                                    {{-- <a href="{{ route('admin.users.create') }}" class="btn ripple btn-main-primary">Add New</a> --}}
                                </div>
                            </div>

                            <div class="table-responsive" wire:loading.remove wire:target="search">
                                <table class="table table-bordered card-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="5%">S.NO.</th>
                                            <th width="20%">Name</th>
                                            <th width="15%">Phone Number</th>
                                            <th width="15%">Balance</th>
                                            <th width="15%" class="text-center">Total Sales Count</th>
                                            {{-- NEW COLUMN --}}
                                            <th width="15%" class="text-right">Total Sales Amount</th>
                                            {{-- NEW COLUMN --}}
                                            <th width="15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($customers as $customer)
                                            <tr>
                                                <td>{{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}
                                                </td>
                                                <td>{{ ucfirst($customer->name) }}</td>
                                                <td>{{ $customer->phone_number }}</td>
                                                <td>$ {{ number_format($customer->balance, 2) }}</td>

                                                {{-- DISPLAYING SALES DATA --}}
                                                <td class="text-center">
                                                    <span class="badge badge-pill badge-primary-light">
                                                        {{ $customer->sales_count ?? 0 }}
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    ${{ number_format($customer->total_sales_amount ?? 0, 2) }}
                                                </td>
                                                {{-- END SALES DATA --}}

                                                <td class="text-center">
                                                    <div class="btn-toolbar table-actions" role="toolbar"
                                                        aria-label="Toolbar with button groups">
                                                        <div class="btn-group btn-group-sm" role="group"
                                                            aria-label="Second group">

                                                            {{-- CORRECTED LINK: Use the route name you provided --}}
                                                            <a href="{{ route('admin.customers.show', $customer->id) }}"
                                                                type="button" class="btn view" data-toggle="tooltip"
                                                                data-placement="top" data-html="true"
                                                                title='<strong>View Sales</strong>'>
                                                                <i class="fa fa-eye"></i>
                                                            </a>

                                                            {{-- The other actions (edit/delete) remain commented out --}}
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            @include('admin.components.record-not-found', ['colspan' => 7])
                                        @endforelse
                                    </tbody>
                                </table>
                                @if ($customers->total() > 0)
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>Total Records : {{ $customers->total() }}</div>
                                        {{ $customers->links('vendor.pagination.custom') }}
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
