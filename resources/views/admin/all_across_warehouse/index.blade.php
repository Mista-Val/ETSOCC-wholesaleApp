@extends('admin.sub_layout')
@section('title', 'Cms page')
@section('sub_content')


<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
              <h2 class="main-content-title tx-24 mg-b-5">Across all warehouse</h2>
              <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ url("admin/dashboard") }}">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Across all warehouse </li>
              </ol>
            </div>
            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">
                          <div class="py-2 text-right add-button">
                          </div>
                          <div class="reset-button d-none">
                            <a href="{{url('admin/all-across-warehouse')}}" class="btn ripple btn-secondary btn-icon"
                            data-original-title="Reset" title="Reset"><i class="si si-refresh" aria-hidden="true"></i></a>
                          </div>
                          <div class="table-responsive">
                            <table class="table text-nowrap text-md-nowrap table-bordered mg-b-0" id="myTable">
                                <thead>
                                    <tr>
                                        <th width="20%">Product</th>
                                        <th width="20%">SKU</th>
                                        @foreach ($outlets as $outlet)
                                            <th>{{ $outlet->name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $stockLookup = [];
                                        $warehouseTotals = [];
                                        
                                        foreach ($stocks as $stock) {
                                            if ($stock->location->type === 'warehouse') {
                                                $stockLookup[$stock->product_id][$stock->location_id] = $stock->product_quantity;
                                                
                                                if (!isset($warehouseTotals[$stock->location_id])) {
                                                    $warehouseTotals[$stock->location_id] = 0;
                                                }
                                                $warehouseTotals[$stock->location_id] += $stock->product_quantity;
                                            }
                                        }

                                        $products = $stocks->pluck('product')->unique('id');
                                    @endphp
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->sku }}</td>
                                            @foreach ($outlets as $outlet)
                                                <td>{{ $stockLookup[$product->id][$outlet->id] ?? 0 }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2"><strong>Grand Total</strong></td>
                                        @php
                                            $grandTotal = 0;
                                        @endphp
                                        @foreach ($outlets as $outlet)
                                            @php
                                                $warehouseTotal = $warehouseTotals[$outlet->id] ?? 0;
                                                $grandTotal += $warehouseTotal;
                                            @endphp
                                            <td><strong>{{ $warehouseTotal }}</strong></td>
                                        @endforeach
                                    </tr>
                                </tfoot>
                            </table>

                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection