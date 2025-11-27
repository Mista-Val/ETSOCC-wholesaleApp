<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">

            {{-- Page Header --}}
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">External Cash OutFlows</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All External Cash OutFlows</li>
                </ol>
            </div>

            {{-- Content Row --}}
            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="main-content-label">External Cash OutFlows</h6>
                                <div class="d-flex align-items-center">
                                    <div class="form-group mb-0 mr-3">
                                        <div class="form-group mb-0 table-filters">
                                            <i class="fa fa-search filter-icon"></i>
                                            <input type="search" class="form-control" placeholder="Search" wire:model.debounce.300ms="search"/>
                                        </div>
                                    </div>
                                    <button type="button" class="btn ripple btn-main-primary mr-3" wire:click="resetFilters">Reset Filters</button>
                                    {{-- <a href="{{ route('admin.external-cash-outflow.create') }}" class="btn ripple btn-main-primary">Add New</a> --}}
                                </div>
                            </div>

                            <div class="table-responsive" wire:loading.remove wire:target="search">
                                <table class="table table-bordered card-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="5%">S.NO.</th>
                                            <th width="20%">Supervisor Name</th>
                                            <th width="20%">Source</th>
                                            <th width="15%">Amount</th>
                                            {{-- <th width="15%">Received Date</th> --}}
                                            <th width="20%">Send To</th>
                                            {{-- <th width="20%">Remarks</th> --}}
                                            <th width="15%" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($externalCashOutFlow as $item)
                                            <tr>
                                                <td>{{ ($externalCashOutFlow->currentPage() - 1) * $externalCashOutFlow->perPage() + $loop->iteration }}</td>
                                                <td>{{ $item->supervisor->name }}</td>
                                                <td>{{ $item->source }}</td>
                                                <td>${{($item->amount) }}</td>
                                                {{-- <td>{{ $item->received_date->format('Y-m-d') }}</td> --}}
                                                {{-- <td>{{ $item->received_from }}</td> --}}
                                                 <td class="px-6 py-3">
                                                    {{ Str::words(ucwords($item->send_to ?? '-'), 5, '...') }}
                                            </td>
                                                {{-- <td>{{ $item->remarks ?? '-' }}</td> --}}
                                                <td class="text-center">
                                                    <div class="btn-toolbar table-actions" role="toolbar">
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <a href="{{ route('admin.external-cash-outflow.show', $item->id) }}" class="btn view" data-toggle="tooltip" title="View">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            {{-- <a href="{{ route('admin.external-cash-inflow.edit', $item->id) }}" class="btn edit" data-toggle="tooltip" title="Edit">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a> --}}
                                                            {{-- <button type="button" class="btn delete" onclick="deleteRecord({{ $item->id }})" data-toggle="tooltip" title="Delete">
                                                                <i class="fa fa-trash"></i>
                                                            </button> --}}
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            @include('admin.components.record-not-found', ['colspan' => 7])
                                        @endforelse
                                    </tbody>
                                </table>

                                {{-- Pagination --}}
                                @if ($externalCashOutFlow->total() > 0)
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>Total Records: {{ $externalCashOutFlow->total() }}</div>
                                        {{ $externalCashOutFlow->links('vendor.pagination.custom') }}
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
