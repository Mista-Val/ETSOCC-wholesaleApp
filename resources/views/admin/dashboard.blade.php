@extends('admin.sub_layout')
@section('title', 'Home Page')
@section('sub_content')

<div class="main-content side-content pt-0">
  <div class="container-fluid">

    <div class="inner-body mt-4">

      <!-- Dashboard Cards Row -->
      <div class="row row-sm">

        <!-- Total Products Card -->
        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4 mb-4">
            <a href="{{ route('admin.products.index') }}" style="text-decoration: none; color: inherit;">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <div class="card-item-icon card-icon text-success">
                                <i class="ti-package sidemenu-icon"></i>
                            </div>
                            <div class="card-item-title mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">Total Products</label>
                                <span class="d-block tx-12 mb-0 text-muted">All products in system</span>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item-stat">
                                    <h4 class="font-weight-bold">{{ \App\Models\Product::count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Users Card -->
        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4 mb-4">
            <a href="{{ route('admin.users.index') }}" style="text-decoration: none; color: inherit;">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <div class="card-item-icon card-icon text-primary">
                                <i class="ti-user sidemenu-icon"></i>
                            </div>
                            <div class="card-item-title mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">Total Users</label>
                                <span class="d-block tx-12 mb-0 text-muted">Registered users in system</span>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item-stat">
                                   <h4 class="font-weight-bold">{{ \App\Models\User::where('role', '!=', 'admin')->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Warehouse Card -->
        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4 mb-4">
            <a href="{{ route('admin.warehouses.index') }}" style="text-decoration: none; color: inherit;">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <div class="card-item-icon card-icon text-info">
                                 <i class="ti-truck sidemenu-icon"></i>
                            </div>
                            <div class="card-item-title mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">Total Warehouse</label>
                                <span class="d-block tx-12 mb-0 text-muted">Registered warehouses in system</span>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item-stat">
                                   <h4 class="font-weight-bold">{{ \App\Models\Location::where('type', '=', 'warehouse')->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Outlets Card -->
         <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4 mb-4">
            <a href="{{ route('admin.outlets.index') }}" style="text-decoration: none; color: inherit;">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <div class="card-item-icon card-icon text-warning">
                                <i class="ti-bag sidemenu-icon"></i>
                            </div>
                            <div class="card-item-title mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">Total Outlets</label>
                                <span class="d-block tx-12 mb-0 text-muted">Registered outlets in system</span>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item-stat">
                                   <h4 class="font-weight-bold">{{ \App\Models\Location::where('type', '=', 'outlet')->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Return Requests Card -->
        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4 mb-4">
            <a href="{{ route('admin.returned-requests-list') }}" style="text-decoration: none; color: inherit;">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <div class="card-item-icon card-icon text-danger">
                                <i class="ti-back-left sidemenu-icon"></i>
                            </div>
                            <div class="card-item-title mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">Total Return Requests</label>
                                <span class="d-block tx-12 mb-0 text-muted">Registered return requests in system</span>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item-stat">
                                   <h4 class="font-weight-bold">{{ \App\Models\StockTransferRequest::where('transfer_type', '=', 'return')->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Sales Revenue Card -->
         <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4 mb-4">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="card-item">
                        <div class="card-item-icon card-icon text-success">
                            <i class="ti-money sidemenu-icon"></i>
                        </div>
                        <div class="card-item-title mb-2">
                            <label class="main-content-label tx-13 font-weight-bold mb-1">Total Sales Revenue</label>
                            <span class="d-block tx-12 mb-0 text-muted">Total sales revenue</span>
                        </div>
                        <div class="card-item-body">
                            <div class="card-item-stat">
                              <h4 class="font-weight-bold">$ {{ number_format(\App\Models\Sale::sum('total_amount'), 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

      </div>

      <div class="row row-sm mb-5">

        <!-- Weekly Warehouse Sales Chart -->
        <div class="col-sm-12 col-lg-6 mb-4">
          <div class="card custom-card" style="height: 400px;">
            <div class="card-header border-bottom">
              <div>
                <label class="main-content-label tx-13 font-weight-bold mb-1">
                  <i class="ti-truck text-info mr-2"></i>Weekly Warehouse Sales
                </label>
                <p class="text-muted tx-11 mb-0">Sales from warehouses grouped by week</p>
              </div>
            </div>
            <div class="card-body" style="height: calc(100% - 70px);">
              <canvas id="warehouseSalesChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Weekly Outlet Sales Chart -->
        <div class="col-sm-12 col-lg-6 mb-4">
          <div class="card custom-card" style="height: 400px;">
            <div class="card-header border-bottom">
              <div>
                <label class="main-content-label tx-13 font-weight-bold mb-1">
                  <i class="ti-bag text-warning mr-2"></i>Weekly Outlet Sales
                </label>
                <p class="text-muted tx-11 mb-0">Sales from outlets grouped by week</p>
              </div>
            </div>
            <div class="card-body" style="height: calc(100% - 70px);">
              <canvas id="outletSalesChart"></canvas>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
window.addEventListener('load', function() {
    const warehouseSalesData = @json($warehouseSales ?? []);
    const outletSalesData = @json($outletSales ?? []);

    // Warehouse chart
    const warehouseCanvas = document.getElementById('warehouseSalesChart');
    if (warehouseSalesData.length && warehouseCanvas) {
        const ctx = warehouseCanvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(23,162,184,0.5)');
        gradient.addColorStop(1, 'rgba(23,162,184,0.05)');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: warehouseSalesData.map(item => item.week),
                datasets: [{
                    label: 'Warehouse Sales',
                    data: warehouseSalesData.map(item => item.total_sales),
                    backgroundColor: gradient,
                    borderColor: 'rgb(23,162,184)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: 'rgb(23,162,184)',
                    pointBorderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: val => '$' + val.toLocaleString()
                        }
                    }
                }
            }
        });
    } else {
        warehouseCanvas.parentElement.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100"><div class="text-center"><i class="ti-truck tx-50 text-muted mb-2 d-block"></i><p class="text-muted mb-0">No warehouse sales data available</p></div></div>';
    }

    // Outlet chart
    const outletCanvas = document.getElementById('outletSalesChart');
    if (outletSalesData.length && outletCanvas) {
        const ctx2 = outletCanvas.getContext('2d');
        const gradient2 = ctx2.createLinearGradient(0, 0, 0, 300);
        gradient2.addColorStop(0, 'rgba(255,193,7,0.5)');
        gradient2.addColorStop(1, 'rgba(255,193,7,0.05)');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: outletSalesData.map(item => item.week),
                datasets: [{
                    label: 'Outlet Sales',
                    data: outletSalesData.map(item => item.total_sales),
                    backgroundColor: gradient2,
                    borderColor: 'rgb(255,193,7)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: 'rgb(255,193,7)',
                    pointBorderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: val => '$' + val.toLocaleString()
                        }
                    }
                }
            }
        });
    } else {
        outletCanvas.parentElement.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100"><div class="text-center"><i class="ti-bag tx-50 text-muted mb-2 d-block"></i><p class="text-muted mb-0">No outlet sales data available</p></div></div>';
    }
});
</script>
@endpush
