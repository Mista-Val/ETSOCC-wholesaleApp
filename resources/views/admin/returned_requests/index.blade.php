@extends('admin.sub_layout')
@section('title', 'Returned Requests')
@section('sub_content')

    <div class="main-content side-content pt-0">
        <div class="container-fluid">
            <div class="inner-body">
                {{-- Page Header --}}
                <div class="page-header d-block">
                    <h2 class="main-content-title tx-24 mg-b-5">All Returned Requests</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">All Returned Requests</li>
                    </ol>
                </div>

                {{-- Content Row --}}
                <div class="row sidemenu-height">
                    <div class="col-lg-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                {{-- Header with Search --}}
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="main-content-label">Return Requests Management</h6>
                                </div>

                                {{-- Table --}}
                                <div class="table-responsive">
                                    <table class="table text-nowrap text-md-nowrap table-bordered mg-b-0">
                                        <thead>
                                            <tr>
                                                <th width="10%">STOCK ID</th>
                                                <th width="10%">OUTLET</th>
                                                <th width="10%">WAREHOUSE</th>
                                                <th width="10%">DATE</th>
                                                <th width="10%">STATUS</th>
                                                <th width="10%" class="text-center">ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($data as $item)
                                                <tr>
                                                    <td>#{{ $item->id }}</td>
                                                    <td>{{ ucwords($item->senderOutlet->name ?? '-') }}</td>
                                                    <td>{{ ucwords($item->outlet->name ?? '-') }}</td>
                                                    <td>{{ $item->created_at->format('Y-m-d') }}</td>
                                                    <td>
                                                        @php
                                                            $statusConfig = [
                                                                'pending' => [
                                                                    'class' => 'warning',
                                                                    'label' => 'Pending Warehouse',
                                                                ],
                                                                'accepted_by_warehouse' => [
                                                                    'class' => 'info',
                                                                    'label' => 'Pending Supervisor',
                                                                ],
                                                                'accepted_by_warehouse_supervisor' => [
                                                                    'class' => 'primary',
                                                                    'label' => 'Pending Admin Approval',
                                                                ],
                                                                'accepted_by_all' => [
                                                                    'class' => 'success',
                                                                    'label' => 'Accepted & Completed',
                                                                ],
                                                                'rejected_by_warehouse' => [
                                                                    'class' => 'danger',
                                                                    'label' => 'Rejected by Warehouse',
                                                                ],
                                                                'rejected_by_supervisor' => [
                                                                    'class' => 'danger',
                                                                    'label' => 'Rejected by Supervisor',
                                                                ],
                                                                'rejected_by_admin' => [
                                                                    'class' => 'danger',
                                                                    'label' => 'Rejected by Admin',
                                                                ],
                                                            ];
                                                            $currentStatus = $statusConfig[$item->status] ?? [
                                                                'class' => 'secondary',
                                                                'label' => ucfirst($item->status),
                                                            ];
                                                        @endphp
                                                        <span class="badge badge-pill badge-{{ $currentStatus['class'] }}">
                                                            {{ $currentStatus['label'] }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-icon-list justify-content-center">
                                                            {{-- View Button - Always visible --}}
                                                            <a class="btn ripple btn-primary btn-icon"
                                                                href="{{ route('admin.returned-requests-view', base64_encode($item->id)) }}"
                                                                data-toggle="tooltip" title="View Details">
                                                                <i class="si si-eye" aria-hidden="true"></i>
                                                            </a>

                                                            {{-- Admin can ONLY Accept when both warehouse & supervisor have accepted --}}
                                                            @if ($item->status == 'accepted_by_warehouse_supervisor')
                                                                <button type="button"
                                                                    class="btn ripple btn-success btn-icon"
                                                                    onclick="acceptRecord({{ $item->id }})"
                                                                    data-toggle="tooltip" title="Accept & Update Stock">
                                                                    <i class="si si-check" aria-hidden="true"></i>
                                                                </button>
                                                            @endif

                                                            {{-- Admin can Reject if NOT already rejected and NOT approved_by_all --}}
                                                            @if (
                                                                !in_array($item->status, [
                                                                    'rejected_by_warehouse',
                                                                    'rejected_by_supervisor',
                                                                    'rejected_by_admin',
                                                                    'accepted_by_all'
                                                                ]))
                                                                <button type="button"
                                                                    class="btn ripple btn-danger btn-icon"
                                                                    onclick="rejectRecord({{ $item->id }})"
                                                                    data-toggle="tooltip" title="Reject Request">
                                                                    <i class="si si-close" aria-hidden="true"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">
                                                        <div
                                                            class="d-flex flex-column align-items-center justify-content-center py-4">
                                                            <i class="si si-folder tx-100 text-muted mb-3"></i>
                                                            <h5 class="text-muted">No return requests found</h5>
                                                            <p class="text-muted">There are no return requests in the system
                                                                yet.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Pagination (if using paginate instead of get) --}}
                                @if (method_exists($data, 'links'))
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>Total Records: {{ $data->total() }}</div>
                                        {{ $data->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Accept/Reject Scripts --}}
    <script>
        function acceptRecord(id) {
            Swal.fire({
                title: 'Accept Return Request?',
                html: `
            <p>This will:</p>
            <ul style="text-align: left; display: inline-block;">
                <li>Mark the return as <strong>Accepted</strong></li>
                <li><strong>Increase</strong> warehouse stock</li>
                <li><strong>Decrease</strong> outlet stock</li>
            </ul>
            <p class="text-danger mt-2"><strong>This action cannot be undone!</strong></p>
        `,
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
                    form.action = `/admin/returned-requests-accept/${btoa(id)}`;

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
                title: 'Reject Return Request?',
                html: `
            <p>Are you sure you want to reject this return request?</p>
            <p class="text-warning mt-2"><strong>The request will be permanently rejected.</strong></p>
        `,
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
                    form.action = `/admin/returned-requests-reject/${btoa(id)}`;

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

        // Initialize tooltips
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>

@endsection