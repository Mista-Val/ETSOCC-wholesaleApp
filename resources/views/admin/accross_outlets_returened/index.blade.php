@extends('admin.sub_layout')
@section('title', 'Cms page')
@section('sub_content')


<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
              <h2 class="main-content-title tx-24 mg-b-5">Across all outlets</h2>
              <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ url("admin/dashboard") }}">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Across all outlets </li>
              </ol>
            </div>
            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">
                          <div class="py-2 text-right add-button">
                            <!-- <a class="btn ripple btn-main-primary ml-3 line-height-24" href="{{route('admin.cms-page.create')}}">Add New</a> -->
                          </div>
                          <div class="reset-button d-none">
                            <a href="{{url('admin/accross-outlets-returened')}}" class="btn ripple btn-secondary btn-icon"
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
                                        $outletTotals = [];
                                        
                                        foreach ($stocks as $stock) {
                                            if ($stock->location->type === 'outlet') {
                                                $stockLookup[$stock->product_id][$stock->location_id] = $stock->product_quantity;
                                                
                                                if (!isset($outletTotals[$stock->location_id])) {
                                                    $outletTotals[$stock->location_id] = 0;
                                                }
                                                $outletTotals[$stock->location_id] += $stock->product_quantity;
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
                                                $outletTotal = $outletTotals[$outlet->id] ?? 0;
                                                $grandTotal += $outletTotal;
                                            @endphp
                                            <td><strong>{{ $outletTotal }}</strong></td>
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