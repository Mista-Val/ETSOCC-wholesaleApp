<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">

            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Stocks</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Stocks</li>
                </ol>
            </div>

            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">

                            <!-- filters -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="main-content-label">Stocks</h6>
                                <div class="d-flex align-items-center">
                                    <div class="form-group mb-0 mr-3">
                                        <div class="form-group mb-0 table-filters">
                                            <i class="fa fa-search filter-icon"></i>
                                            <input type="search" class="form-control" placeholder="Search" wire:model.debounce.300ms="search" />
                                        </div>
                                    </div>
                                    <button type="button" class="btn ripple btn-main-primary mr-3" wire:click="resetFilters">Reset Filters</button>
                                    <a href="{{ route('admin.stock.create') }}" class="btn ripple btn-main-primary">Add New</a>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered card-table border-top text-nowrap w-100" id="stockTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Supplier Name</th>
                                            <th>Warehouse</th>
                                            <th>Status</th>
                                            <th>Created Date</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($stocks as $index => $stock)
                                            <tr>
                                                <td>{{ ($stocks->currentPage() - 1) * $stocks->perPage() + $loop->iteration }}</td>
                                                <td>{{ ucfirst($stock->supplier_name) }}</td>
                                                <td>{{ $stock->receiverWarehouse->name ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <!-- Status Badge -->
                                                        <span class="badge badge-{{ $stock->status == 'created' ? 'info' : 'success' }}">
                                                            {{ ucfirst($stock->status) }}
                                                        </span>

                                                        <!-- Change Status Button (Dropdown) -->
                                                        @if ($stock->status != 'accepted' && $stock->status != 'partially accepted')
                                                        <div class="dropdown ml-2">
                                                            <button class="btn btn-sm btn-warning dropdown-toggle" type="button" id="statusDropdown-{{ $stock->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Change Status">
                                                                <i class="si si-settings"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="statusDropdown-{{ $stock->id }}">
                                                                <a class="dropdown-item change-status-option" href="#" data-id="{{ $stock->id }}" data-new-status="created">Created</a>
                                                                <a class="dropdown-item change-status-option" href="#" data-id="{{ $stock->id }}" data-new-status="dispatched">Dispatched</a>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>{{ $stock->created_at->format('d M, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="btn-toolbar table-actions" role="toolbar" aria-label="Toolbar with button groups">
                                                        <div class="btn-group btn-group-sm" role="group" aria-label="Action group">

                                                            {{-- View Button --}}
                                                            <a href="{{ route('admin.stock.show', $stock->id) }}"
                                                                type="button"
                                                                class="btn view"
                                                                data-toggle="tooltip"
                                                                data-placement="top"
                                                                data-html="true"
                                                                title='<strong>View</strong>'>
                                                                <i class="fa fa-eye"></i>
                                                            </a>

                                                            @if ($stock->status == 'created')
                                                                {{-- Edit Button --}}
                                                                <a href="{{ route('admin.stock.edit', $stock->id) }}"
                                                                    type="button"
                                                                    class="btn edit"
                                                                    data-toggle="tooltip"
                                                                    data-placement="top"
                                                                    data-html="true"
                                                                    title='<strong>Edit</strong>'>
                                                                    <i class="fa fa-pencil-square-o"></i>
                                                                </a>

                                                                {{-- Delete Button --}}
                                                                <button type="button"
                                                                    class="btn delete"
                                                                    onclick="deleteRecord({{ $stock->id }})"
                                                                    data-toggle="tooltip"
                                                                    data-placement="top"
                                                                    data-html="true"
                                                                    title='<strong>Delete</strong>'>
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            @endif

                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            @include('admin.components.record-not-found', ['colspan' => 6])
                                        @endforelse
                                    </tbody>
                                </table>

                                @if ($stocks->total() > 0)
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>Total Records : {{ $stocks->total() }}</div>
                                        {{ $stocks->links('vendor.pagination.custom') }}
                                    </div>
                                @endif

                                <div wire:loading.delay wire:target="search" class="w-100">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// $(document).ready(function () {
//     // Event listener for status change options in the dropdown
//     $('.change-status-option').on('click', function (e) {
//         e.preventDefault();

//         var stockId = $(this).data('id');
//         var newStatus = $(this).data('new-status');

//         // Send an AJAX POST request to update the stock status
//         $.ajax({
//             url: '{{ route('admin.stock.updateStatus') }}',
//             type: 'POST',
//             data: {
//                 stock_id: stockId,
//                 status: newStatus,
//                 _token: '{{ csrf_token() }}' // CSRF token included
//             },
//             success: function (response) {
//                 // On success, update the badge and dropdown with the new status
//                 var badge = $('#statusDropdown-' + stockId).closest('td').find('.badge');

//                 badge.removeClass('badge-info badge-success');
//                 badge.addClass(newStatus === 'created' ? 'badge-info' : 'badge-success');
//                 badge.text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));

//                 // Optionally, show a success message using SweetAlert2
//                 Swal.fire('Success', 'Stock status updated successfully!', 'success');
//                 location.reload();
//             },
//             error: function (error) {
//                 // Handle any error that occurs during the AJAX request
//                 Swal.fire('Error', 'Something went wrong while updating the status!', 'error');
//             }
//         });
//     });
// });
$(document).ready(function () {
    // Event listener for status change options in the dropdown
    $('.change-status-option').on('click', function (e) {
        e.preventDefault();

        var stockId = $(this).data('id');
        var newStatus = $(this).data('new-status');

        // Send an AJAX POST request to update the stock status
        $.ajax({
            url: '{{ route('admin.stock.updateStatus') }}',
            type: 'POST',
            data: {
                stock_id: stockId,
                status: newStatus,
                _token: '{{ csrf_token() }}' // CSRF token included
            },
            success: function (response) {
                // On success, update the badge and dropdown with the new status
                var badge = $('#statusDropdown-' + stockId).closest('td').find('.badge');

                badge.removeClass('badge-info badge-success');
                badge.addClass(newStatus === 'created' ? 'badge-info' : 'badge-success');
                badge.text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));

                // Show success message with timer and reload after it closes
                Swal.fire({
                    title: 'Success',
                    text: 'Stock status updated successfully!',
                    icon: 'success',
                    timer: 2000, // Show for 2 seconds
                    showConfirmButton: false,
                    timerProgressBar: true
                }).then(() => {
                    // Reload after the alert closes
                    location.reload();
                });
            },
            error: function (error) {
                // Handle any error that occurs during the AJAX request
                Swal.fire('Error', 'Something went wrong while updating the status!', 'error');
            }
        });
    });
});
</script>

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
