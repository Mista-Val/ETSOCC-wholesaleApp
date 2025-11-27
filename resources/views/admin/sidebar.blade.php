<!-- Sidemenu -->
<div class="main-sidebar main-sidebar-sticky side-menu" style="overflow: auto;">
    <div class="sidemenu-logo p-0">
        <a class="main-logo" href="{{ url('admin/dashboard') }}">
            <img src="{{ globalSetting('logo') }}" id="sidebar-logo" class="header-brand-img desktop-logo" alt="logo"
                height="64px">
            <img src="{{ globalSetting('logo') }}" class="header-brand-img icon-logo" alt="logo" id="logo_short"
                height="64px">
        </a>
    </div>

    <div class="main-sidebar-body">
        @php
            $currentRoute = request()->segment(2);

            $cmsRoutes = ['cms-page', 'banners', 'categories', 'faq', 'email-templates', 'testimonials', 'config'];
            $stockRoutes = ['stock-management', 'accross-outlets-returened', 'all-across-warehouse'];
            $reportRoutes = [
                'sales-report',
                'stock-movement-report',
                'cash-remittance-report',
                'down-payment-report',
                'debt-collection-report',
                'cash-handling-report',
            ];

            $stockTransferRoutes = ['admin.returned-requests-list', 'admin.returned-requests-view'];

        @endphp

        <ul class="nav">
            <!-- Dashboard -->
            <li class="nav-item {{ $currentRoute === 'dashboard' ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('/admin/dashboard') }}">
                    <span class="shape1"></span>
                    <span class="shape2"></span>
                    <i class="fa fa-home sidemenu-icon"></i>
                    <span class="sidemenu-label">Dashboard</span>
                </a>
            </li>

            <!-- CMS Menu -->
            <li class="nav-item {{ in_array($currentRoute, $cmsRoutes) ? 'active show' : '' }}">
                <a class="nav-link with-sub" href="#">
                    <span class="shape2"></span>
                    <span class="shape21"></span>
                    <i class="fa fa-cogs sidemenu-icon"></i>
                    <span class="sidemenu-label">CMS</span>
                    <i class="angle fe fe-chevron-right"></i>
                </a>
                <ul class="nav-sub" style="{{ in_array($currentRoute, $cmsRoutes) ? 'display:block;' : '' }}">
                    <li class="nav-sub-item {{ $currentRoute === 'cms-page' ? 'active' : '' }}">
                        <a class="nav-sub-link" href="{{ url('admin/cms-page') }}">Page</a>
                    </li>
                    <!-- <li
                        class="nav-sub-item {{ $currentRoute === 'banners' ? 'active' : '' }}">
                        <a class="nav-sub-link" href="{{ url('admin/banners') }}">Web Banners</a>
                    </li> -->
                    <!-- <li
                        class="nav-sub-item {{ $currentRoute === 'categories' ? 'active' : '' }}">
                        <a class="nav-sub-link" href="{{ url('admin/categories') }}">FAQ
                            Categories</a>
                    </li> -->
                    <!-- <li
                        class="nav-sub-item {{ $currentRoute === 'faq' ? 'active' : '' }}">
                        <a class="nav-sub-link" href="{{ url('admin/faq') }}">FAQ</a>
                    </li> -->
                    <li class="nav-sub-item {{ $currentRoute === 'email-templates' ? 'active' : '' }}">
                        <a class="nav-sub-link" href="{{ url('admin/email-templates') }}">Email
                            Templates</a>
                    </li>
                    <!-- <li
                        class="nav-sub-item {{ $currentRoute === 'testimonials' ? 'active' : '' }}">
                        <a class="nav-sub-link"
                            href="{{ url('admin/testimonials') }}">Testimonials</a>
                    </li> -->
                    <li class="nav-sub-item {{ $currentRoute === 'config' ? 'active' : '' }}">
                        <a class="nav-sub-link" href="{{ url('admin/config') }}">Global Setting</a>
                    </li>
                </ul>
            </li>

            <!-- Products -->

            <!-- User management -->
            <li class="nav-item {{ $currentRoute === 'users' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.users.index') }}">
                    <span class="shape1"></span>
                    <span class="shape2"></span>
                    <i class="fas fa-users sidemenu-icon"></i>
                    <span class="sidemenu-label">User Management</span>
                </a>
            </li>

            <!-- <li class="nav-item {{ $currentRoute === 'warehouses' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.warehouses.index') }}">
                    <span class="shape1"></span>
                    <span class="shape2"></span>
                    <i class="ti-package sidemenu-icon"></i>
                    <span class="sidemenu-label">Warehouse Management</span>
                </a>
            </li> -->
            <li class="nav-item {{ $currentRoute === 'warehouses' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.warehouses.index') }}">
                    <span class="shape1"></span>
                    <span class="shape2"></span>
                    <i class="fa fa-truck sidemenu-icon"></i>
                    <span class="sidemenu-label">Warehouse Management</span>
                </a>
            </li>
            <li class="nav-item {{ $currentRoute === 'outlets' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.outlets.index') }}">
                    <span class="shape1"></span>
                    <span class="shape2"></span>
                    <i class="fas fa-store sidemenu-icon"></i>
                    <span class="sidemenu-label">Outlet Management</span>
                </a>
            </li>

            <li class="nav-item {{ $currentRoute === 'customers' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.customers.index') }}">
                    <span class="shape1"></span>
                    <span class="shape2"></span>
                    <i class="fas fa-users sidemenu-icon"></i>
                    <span class="sidemenu-label">Customer Management</span>
                </a>
            </li>

            <li class="nav-item {{ in_array($currentRoute, $stockRoutes) ? 'active show' : '' }}">
                <a class="nav-link with-sub" href="#">
                    <span class="shape1"></span>
                    <span class="shape2"></span>
                    <i class="fa fa-pie-chart sidemenu-icon"></i>
                    <span class="sidemenu-label">Stock Management</span>
                    <i class="angle fe fe-chevron-right"></i>
                </a>
                <ul class="nav-sub" style="{{ in_array($currentRoute, $cmsRoutes) ? 'display:block;' : '' }}">
                    <li class="nav-sub-item {{ request()->segment(3) === 'stock' ? 'active' : '' }}">
                        <a class="nav-sub-link" href="{{ route('admin.stock.index') }}">
                            Stocks
                        </a>
                    </li>
                    <li class="nav-sub-item {{ request()->segment(3) === 'products' ? 'active' : '' }}">
                        <a class="nav-sub-link" href="{{ route('admin.products.index') }}">
                            Product Management
                        </a>
                    </li>
                    <li class="nav-sub-item {{ $currentRoute === 'accross-outlets-returened' ? 'active' : '' }}">
                        <a class="nav-sub-link" href="{{ route('admin.accross-outlets-returened') }}">
                            Across All Outlets
                        </a>
                    </li>

                    <li class="nav-sub-item {{ $currentRoute === 'all-across-warehouse' ? 'active' : '' }}">
                        <a class="nav-sub-link" href="{{ route('admin.all-across-warehouse') }}">
                            Across All Warehouse
                        </a>
                    </li>

                </ul>
            </li>

            {{-- <li class="nav-item {{ in_array(Route::currentRouteName(), $stockTransferRoutes) ? 'active show' : '' }}">
                <a class="nav-link with-sub" href="#">
                    <i class="fa fa-pie-chart sidemenu-icon"></i>
                    <span class="sidemenu-label">Stock Transfer </span>
                </a>
                <ul class="nav-sub"
                    style="{{ in_array(Route::currentRouteName(), $stockTransferRoutes) ? 'display:block;' : '' }}">
                    <li
                        class="nav-sub-item {{ in_array(Route::currentRouteName(), $stockTransferRoutes) ? 'active' : '' }}">
                        <a class="nav-sub-link" href="{{ route('admin.returned-requests-list') }}">
                            Returned Requests
                        </a>
                    </li>
                </ul>
            </li> --}}

            <li class="nav-item {{ $currentRoute === 'returned-requests-list' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.returned-requests-list') }}">
                    <span class="shape1"></span>
                    <span class="shape2"></span>
                    <i class="fas fa-coins sidemenu-icon"></i>
                    <span class="sidemenu-label"> Returned Requests</span>
                </a>
            </li>


            <li class="nav-item {{ $currentRoute === 'external-cash-inflow' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.external-cash-inflow.index') }}">
                    <span class="shape1"></span>
                    <span class="shape2"></span>
                    <i class="fas fa-coins sidemenu-icon"></i>
                    <span class="sidemenu-label">External Cash Inflow</span>
                </a>
            </li>

            <li class="nav-item {{ $currentRoute === 'external-cash-outflow' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.external-cash-outflow.index') }}">
                    <span class="shape1"></span>
                    <span class="shape2"></span>
               <i class="fas fa-wallet sidemenu-icon"></i>
                    <span class="sidemenu-label">External Cash Outflow</span>
                </a>
            </li>

            {{-- <li class="nav-item {{ $currentRoute === 'sales-report' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.sales-report.index') }}">
                    <span class="shape1"></span>
                    <span class="shape2"></span>
                  <i class="fas fa-receipt sidemenu-icon"></i>   
                    <span class="sidemenu-label">Sales Reports</span>
                </a>
            </li>

             <li class="nav-item {{ $currentRoute === 'stock-movement-report' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.stock-movement.index') }}">
                    <span class="shape1"></span>
                    <span class="shape2"></span>
                    <i class="fas fa-truck-loading sidemenu-icon"></i>  
                    <span class="sidemenu-label">Stock Movements Reports</span>
                </a>
            </li>

             <li class="nav-item {{ $currentRoute === 'cash-handling-report' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.cash-handling.index') }}">
                    <span class="shape1"></span>
                    <span class="shape2"></span>
                    <i class="fas fa-wallet sidemenu-icon"></i> 
                    <span class="sidemenu-label">Cash Handling Reports</span>
                </a>
            </li> --}}

            <li class="nav-item {{ in_array($currentRoute, $reportRoutes) ? 'active show' : '' }}">
                <a class="nav-link with-sub" href="#">
                    <span class="shape1"></span>
                    <span class="shape2"></span>
                    <i class="fas fa-receipt sidemenu-icon"></i>
                    <span class="sidemenu-label">Reports</span>
                    <i class="angle fe fe-chevron-right"></i>
                </a>
                <ul class="nav-sub" style="{{ in_array($currentRoute, $cmsRoutes) ? 'display:block;' : '' }}">
                    <li class="nav-sub-item {{ request()->segment(3) === 'sales-report' ? 'active' : '' }}">
                        <a class="nav-sub-link" href="{{ route('admin.sales-report.index') }}">
                            Sales Reports
                        </a>
                    </li>
                    <li class="nav-sub-item {{ request()->segment(3) === 'stock-movement-report' ? 'active' : '' }}">
                        <a class="nav-sub-link" href="{{ route('admin.stock-movement.index') }}">
                            Stock Movements Reports
                        </a>
                    </li>
                    <li class="nav-sub-item {{ request()->segment(3) === 'cash-remittance' ? 'active' : '' }}">
                        <a class="nav-sub-link" href="{{ route('admin.cash-remittance.index') }}">
                            Cash Remittance Reports
                        </a>
                    </li>
                    <li class="nav-sub-item {{ request()->segment(3) === 'down-payment-report' ? 'active' : '' }}">
                        <a class="nav-sub-link" href="{{ route('admin.down-payment.index') }}">
                            Down Payment Reports
                        </a>
                    </li>
                    <li class="nav-sub-item {{ request()->segment(3) === 'debt-collection-report' ? 'active' : '' }}">
                        <a class="nav-sub-link" href="{{ route('admin.debt-collection.index') }}">
                            Debt & Debtors Reports
                        </a>
                    </li>

                    {{-- <li class="nav-sub-item {{ request()->segment(3) === 'cash-handling-report' ? 'active' : '' }}">
                        <a class="nav-sub-link" href="{{ route('admin.cash-handling.index') }}">
                          Cash Handling Reports
                      </a>
                    </li> --}}

                </ul>
            </li>

            <li class="nav-item {{ $currentRoute === 'record-expense' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.record-expense.index') }}">
                    <span class="shape1"></span>
                    <span class="shape2"></span>
                    <i class="fas fa-plus-circle sidemenu-icon"></i>
                    <span class="sidemenu-label">Record Expense Request</span>
                </a>
            </li>
            <li class="nav-item {{ $currentRoute === 'cash-refund' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.cash-refund.index') }}">
                    <span class="shape1"></span>
                    <span class="shape2"></span>
                    <i class="fas fa-wallet sidemenu-icon"></i>
                    <span class="sidemenu-label">Cash Refund Request</span>
                </a>
            </li>


        </ul>
    </div>
</div>
<!-- End Sidemenu -->
