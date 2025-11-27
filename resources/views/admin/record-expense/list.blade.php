<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">

            {{-- Page Header --}}
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Record Expense Request</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Record Expense Request</li>
                </ol>
            </div>

            {{-- Content Row --}}
            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="main-content-label">Record Expense Request</h6>
                                <div class="d-flex align-items-center">
                                    <div class="form-group mb-0 mr-3">
                                        <div class="form-group mb-0 table-filters">
                                            <i class="fa fa-search filter-icon"></i>
                                            <input type="search" class="form-control" placeholder="Search"
                                                wire:model.debounce.300ms="search" />
                                        </div>
                                    </div>
                                    <button type="button" class="btn ripple btn-main-primary mr-3"
                                        wire:click="resetFilters">Reset Filters</button>
                                </div>
                            </div>

                            <div class="table-responsive" wire:loading.remove wire:target="search">
                                <table class="table table-bordered card-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="5%">S.NO.</th>
                                            <th width="12%">Amount</th>
                                            <th width="12%">Date</th>
                                            <th width="15%">Received From</th>
                                            <th width="15%">Purpose</th>
                                            <th width="15%">Remarks</th>
                                            <th width="12%">Status</th>
                                            <th width="14%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($recordExpense as $item)
                                            <tr>
                                                <td>{{ ($recordExpense->currentPage() - 1) * $recordExpense->perPage() + $loop->iteration }}</td>
                                                <td>${{ number_format($item->amount, 2) }}</td>
                                                <td>{{ $item->created_at->format('Y-m-d') }}</td>
                                                <td>{{ $item->location->name ?? 'N/A' }}</td>
                                                <td>{{ ucwords($item->purpose) ?? '-' }}</td>
                                                <td>{{ ucwords($item->remarks) ?? '-' }}</td>
                                                <td>
                                                    @if($item->approval_status == 'accepted_by_supervisor')
                                                        <span class="badge badge-pill badge-info">Pending Admin Approval</span>
                                                    @elseif($item->approval_status == 'accepted_by_admin')
                                                        <span class="badge badge-pill badge-success">Accepted by Admin & Supervisor</span>
                                                    @elseif($item->approval_status == 'rejected_by_supervisor')
                                                        <span class="badge badge-pill badge-danger">Rejected by Supervisor</span>
                                                    @elseif($item->approval_status == 'rejected_by_admin')
                                                        <span class="badge badge-pill badge-danger">Rejected by Admin</span>
                                                    @else
                                                        <span class="badge badge-pill badge-warning">Pending Supervisor</span>
                                                    @endif
                                                </td>
                                                {{-- <td class="text-center">
                                                    @if($item->approval_status == 'accepted_by_supervisor')
                                                        <div class="btn-toolbar table-actions" role="toolbar">
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                <button type="button" class="btn btn-success"
                                                                    onclick="acceptRecord({{ $item->id }})"
                                                                    data-toggle="tooltip" title="Accept">
                                                                    <i class="fa fa-check"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-danger"
                                                                    onclick="rejectRecord({{ $item->id }})"
                                                                    data-toggle="tooltip" title="Reject">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    
                                                    @elseif($item->approval_status == 'pending' || $item->approval_status == null)
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="rejectRecord({{ $item->id }})"
                                                            data-toggle="tooltip" title="Reject">
                                                            <i class="fa fa-times"></i> Reject
                                                        </button>
                                        
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td> --}}
<td class="text-center">
    {{-- Admin can Accept & Reject if supervisor accepted --}}
    @if($item->approval_status == 'accepted_by_supervisor')
        <button type="button" class="btn btn-success btn-sm mr-1"
            onclick="acceptRecord({{ $item->id }})"
            data-toggle="tooltip" title="Accept">
            <i class="fa fa-check"></i> Accept
        </button>
        <button type="button" class="btn btn-danger btn-sm"
            onclick="rejectRecord({{ $item->id }})"
            data-toggle="tooltip" title="Reject">
            <i class="fa fa-times"></i> Reject
        </button>
    
    {{-- Admin can only Reject if supervisor hasn't acted (pending) --}}
    @elseif($item->approval_status == 'pending' || $item->approval_status == null)
        <button type="button" class="btn btn-danger btn-sm"
            onclick="rejectRecord({{ $item->id }})"
            data-toggle="tooltip" title="Reject">
            <i class="fa fa-times"></i> Reject
        </button>
    
    {{-- No action if supervisor rejected or admin already acted --}}
    @else
        <span class="">-</span>
    @endif
</td>
                                            </tr>
                                        @empty
                                            @include('admin.components.record-not-found', ['colspan' => 8])
                                        @endforelse
                                    </tbody>
                                </table>

                                {{-- Pagination --}}
                                @if ($recordExpense->total() > 0)
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>Total Records: {{ $recordExpense->total() }}</div>
                                        {{ $recordExpense->links('vendor.pagination.custom') }}
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

    {{-- Accept/Reject Scripts --}}
    <script>
    function acceptRecord(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to accept this expense request?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Accept it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/record-expense/${id}/accept`; 
                
                let csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);
                
                let methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PATCH';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    
    function rejectRecord(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to reject this expense request?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Reject it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/record-expense/${id}/reject`;
                
                let csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);
                
                let methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PATCH';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    </script>
</div>