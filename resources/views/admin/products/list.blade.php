<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Product</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Products</li>
                </ol>
            </div>

            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="main-content-label">Products</h6>
                                <div class="d-flex align-items-center">
                                    <div class="form-group mb-0 mr-3">
                                        <div class="form-group mb-0 table-filters">
                                            <i class="fa fa-search filter-icon"></i>
                                            <input type="search" class="form-control" placeholder="Search"
                                                wire:model.debounce.300ms="search" />
                                        </div>
                                    </div>

                                    <div class="mr-3">
                                        <div class="form-group mb-0 table-filters">
                                            <i class="fa fa-filter mr-2 filter-icon"></i>
                                            <div class="select-down-arrow">
                                                <select class="form-control cp" wire:model="status">
                                                    <option class="text-capitalize" value="">User status</option>
                                                    <option class="text-capitalize" value="0">Inactive</option>
                                                    <option class="text-capitalize" value="1">Active</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="btn ripple btn-main-primary mr-3"
                                        wire:click="resetFilters">Reset Filters</button>
                                    <a href="{{ route('admin.products.create') }}"
                                        class="btn ripple btn-main-primary">Add New</a>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered card-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="5%">S.NO.</th>
                                            <th width="15%">Image</th>
                                            <th width="30%">Name</th>
                                            <th width="25%">SKU</th>
                                            <th width="15%">Status</th>
                                            <th width="15%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $item)
                                            <tr>
                                                <td>{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}
                                                </td>
                                                <td>
                                                    @if ($item->image)
                                                        <img src="{{ asset('storage/' . $item->image) }}"
                                                            alt="Product Image" width="50" height="50"
                                                            class="rounded-circle">
                                                    @else
                                                        <div
                                                            style="width:50px; height:50px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-weight:bold; color:#aaa;">
                                                            <i class="fas fa-box"></i>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->sku }}</td>
                                                <td>
                                                    @if ($item->status)
                                                        <span class="badge badge-pill badge-success-light">Active</span>
                                                    @else
                                                        <span
                                                            class="badge badge-pill badge-danger-light">Inactive</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-toolbar table-actions" role="toolbar"
                                                        aria-label="Toolbar with button groups">
                                                        <div class="btn-group btn-group-sm" role="group"
                                                            aria-label="Second group">
                                                            <a href="{{ route('admin.products.show', $item->id) }}"
                                                                type="button" class="btn view" style="display: flex; align-items: center; justify-content: center; padding: 5px;" data-toggle="tooltip"
                                                                data-placement="top" data-html="true"
                                                                title='<strong>View</strong>'>
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            {{-- Edit Button --}}
                                                            <a href="{{ route('admin.products.edit', $item->id) }}"
                                                                class="btn edit"
                                                                style="display: flex; align-items: center; justify-content: center; padding: 5px;"
                                                                data-toggle="tooltip" data-placement="top"
                                                                data-html="true" title='<strong>Edit</strong>'>
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>

                                                            {{-- Delete Button --}}
                                                            {!! Form::open([
                                                                'route' => ['admin.products.destroy', $item->id],
                                                                'method' => 'DELETE',
                                                                'class' => 'd-inline',
                                                                'id' => 'delete-form-' . $item->id,
                                                            ]) !!}
                                                            <button type="button" class="btn delete delete-button"
                                                                onclick="deleteRecord({{ $item->id }})"
                                                                style="display: flex; align-items: center; justify-content: center; padding: 5px;"
                                                                data-toggle="tooltip" data-placement="top"
                                                                data-html="true" title='<strong>Delete</strong>'>
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach

                                        @forelse($products as $item)
                                            {{-- Already handled in foreach --}}
                                        @empty
                                            @include('admin.components.record-not-found', ['colspan' => 6])
                                        @endforelse
                                    </tbody>
                                </table>

                                @if ($products->total() > 0)
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>Total Records : {{ $products->total() }}</div>
                                        {{ $products->links('vendor.pagination.custom') }}
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

<!-- Import Excel Modal -->
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importExcelModalLabel">Upload Excel File</h5>

                    {{-- ✅ FIXED: Use Bootstrap 4 compatible modal close --}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="excelFile" class="form-label">Choose Excel File</label>
                        <input class="form-control" type="file" id="excelFile" name="file"
                            accept=".xlsx,.xls" required>
                        <a href="{{ asset('storage/products/sample.xlsx') }}" download class="btn btn-link">Download
                            Sample Excel</a>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-2" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>

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
