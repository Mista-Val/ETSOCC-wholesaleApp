@php
    $currentRoute = url()->full();
    $currentRouteArray = explode('/', $currentRoute);
    $activeUrl = '';

    foreach ($currentRouteArray as $value) {
        if ($value === 'password-change') {
            $activeUrl = $value;
            break;
        }
    }
    $currentSegment = request()->segment(2);
    $stockItem = [
        'receive-stock',
        'receive-show',
        'transfer-to-outlets',
        'outlet-transfer-request',
        'returned-requests',
        'transfer-to-outlets-create',
        'transfer-to-outlets-detail',
        'outlet-create-request',
        'outlet-view-request',
        'returned-requests-create',
        'returned-requests-view',
        'outlet-levels',
        'warehouse-levels',
        'outlet-request',
        'outlet-request-show',
    ];
    $stockItems = in_array($currentSegment, $stockItem) ? 'active show' : '';
    $cashHandling = [
        'daily-sales-summary',
        'debtCollections',
        'recordExpenses',
        'cashRemittance',
        'debtCollections-create',
        'recordExpenses-create',
        'cashRemittance-create',
        'cashRemittance-view',
    ];
    $cashHandlings = in_array($currentSegment, $cashHandling) ? 'active show' : '';
    $salesItem = ['sales-orders', 'sales-details', 'create-sales', 'customer', 'salesInvoice','product-management','waybill'];
    $salesItems = in_array($currentSegment, $salesItem) ? 'active show' : '';
    $user = Auth::guard('outlet')->user();

@endphp
<style>
    .dropmenu {
        display: none;
    }

    .dropdown.open .dropdown-content {
        display: block;
    }

    /* Notification Dropdown Styles */
    .notification-dropdown {
        position: absolute;
        right: 0;
        margin-top: 0.5rem;
        width: 350px;
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        z-index: 9999;
        max-height: 400px;
        overflow: hidden;
    }

    .notification-dropdown.hidden {
        display: none;
    }

    .notification-item {
        transition: background-color 0.2s;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-item:hover {
        background-color: #f9fafb;
    }

    .notification-scroll {
        overflow-y: auto;
        max-height: 320px;
    }

    .notification-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .notification-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .notification-scroll::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .notification-scroll::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

<header class="py-[10px] bg-white sticky top-0 left-0 z-50 border-b border-1 border-gry-50">
    <div class="container-fluid">
        <div class="drawer">
            <input id="my-drawer-3" type="checkbox" class="drawer-toggle" />
            <div class="navbar p-0 bg-white flex items-center gap-[40px] flex-wrap drawer-content">
                <a class="flex items-center" href="{{ route('outlet.outlet-dashboard') }}">
                    <img src="{{ asset('web/images/logo.svg') }}" alt="Logo" class=" h-[50px] md:h-[55px] w-auto" />
                </a>
                <div class="head-right flex-1 hidden lg:flex justify-between gap-[15px] flex-wrap">
                    <!-- Nav links (desktop) -->
                    <ul class="dropdown border-0 nav-item hidden md:flex items-center gap-[5px] menu-list">
                        <li class="{{ request()->is('outlet/outlet-dashboard*') ? 'active' : '' }}">
                            <a href="{{ route('outlet.outlet-dashboard') }}"
                                class="flex items-center gap-[10px] px-[15px] py-[10px] rounded-[8px] body-14 semibold">
                                <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4.16675 8.83366H5.83341C7.50008 8.83366 8.33341 8.00033 8.33341 6.33366V4.66699C8.33341 3.00033 7.50008 2.16699 5.83341 2.16699H4.16675C2.50008 2.16699 1.66675 3.00033 1.66675 4.66699V6.33366C1.66675 8.00033 2.50008 8.83366 4.16675 8.83366Z"
                                        stroke="#4D4D4D" stroke-width="1.5" stroke-miterlimit="10"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M14.1667 8.83366H15.8334C17.5001 8.83366 18.3334 8.00033 18.3334 6.33366V4.66699C18.3334 3.00033 17.5001 2.16699 15.8334 2.16699H14.1667C12.5001 2.16699 11.6667 3.00033 11.6667 4.66699V6.33366C11.6667 8.00033 12.5001 8.83366 14.1667 8.83366Z"
                                        stroke="#4D4D4D" stroke-width="1.5" stroke-miterlimit="10"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M14.1667 18.8337H15.8334C17.5001 18.8337 18.3334 18.0003 18.3334 16.3337V14.667C18.3334 13.0003 17.5001 12.167 15.8334 12.167H14.1667C12.5001 12.167 11.6667 13.0003 11.6667 14.667V16.3337C11.6667 18.0003 12.5001 18.8337 14.1667 18.8337Z"
                                        stroke="#4D4D4D" stroke-width="1.5" stroke-miterlimit="10"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M4.16675 18.8337H5.83341C7.50008 18.8337 8.33341 18.0003 8.33341 16.3337V14.667C8.33341 13.0003 7.50008 12.167 5.83341 12.167H4.16675C2.50008 12.167 1.66675 13.0003 1.66675 14.667V16.3337C1.66675 18.0003 2.50008 18.8337 4.16675 18.8337Z"
                                        stroke="#4D4D4D" stroke-width="1.5" stroke-miterlimit="10"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="dropdown stock-transfer-dropdown {{ $stockItems }}">
                            <a class="flex border-0 focus:outline-none outline-none items-center gap-[10px] px-[15px] py-[10px] rounded-[8px] body-14 semibold"
                                tabindex="0" role="button">
                                <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M18.3334 11.3337V8.00033C18.3334 3.83366 16.6667 2.16699 12.5001 2.16699H7.50008C3.33341 2.16699 1.66675 3.83366 1.66675 8.00033V13.0003C1.66675 17.167 3.33341 18.8337 7.50008 18.8337H10.8334"
                                        stroke="#4D4D4D" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M6.1084 12.5748L8.09174 9.9998C8.37507 9.63314 8.90007 9.56648 9.26674 9.84981L10.7918 11.0498C11.1584 11.3331 11.6834 11.2665 11.9667 10.9081L13.8918 8.4248"
                                        stroke="#4D4D4D" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M16.2334 13.6832L16.4668 14.1582C16.5834 14.3915 16.8751 14.6082 17.1334 14.6582L17.4501 14.7082C18.4001 14.8665 18.6251 15.5665 17.9418 16.2582L17.6501 16.5499C17.4584 16.7499 17.3501 17.1332 17.4084 17.3999L17.4501 17.5749C17.7084 18.7249 17.1001 19.1665 16.1001 18.5665L15.8834 18.4415C15.6251 18.2915 15.2084 18.2915 14.9501 18.4415L14.7334 18.5665C13.7251 19.1749 13.1168 18.7249 13.3834 17.5749L13.4251 17.3999C13.4834 17.1332 13.3751 16.7499 13.1834 16.5499L12.8918 16.2582C12.2084 15.5665 12.4334 14.8665 13.3834 14.7082L13.7001 14.6582C13.9501 14.6165 14.2501 14.3915 14.3668 14.1582L14.6001 13.6832C15.0501 12.7749 15.7834 12.7749 16.2334 13.6832Z"
                                        stroke="#4D4D4D" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                <span>Stock Transfer</span>
                            </a>
                            <ul class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm dropmenu">
                                <li>
                                    <a href="{{ route('outlet.receive-stock') }}" class="body-14 medium">
                                        <img src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" />
                                        Transfer In
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('outlet.transferoutlets') }}" class="body-14 medium"><img
                                            src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" /> Transfer
                                        Out</a>
                                </li>
                                <li>
                                    <a class="body-14 medium" href="{{ route('outlet.outlet-transfer-request') }}"><img
                                            src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" />Request from
                                        Outlets</a>
                                </li>
                                <li>
                                    <a href="{{ route('outlet.returned-requests') }}" class="body-14 medium">
                                        <img src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" /> Returned
                                        Requests
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('outlet.outlet-levels') }}" class="body-14 medium">
                                        <img src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" /> Outlet
                                        Levels
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('outlet.warehouse-levels') }}" class="body-14 medium">
                                        <img src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" /> Warehouse
                                        Levels
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('outlet.outlet-request') }}" class="body-14 medium">
                                        <img src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" /> Outlet Stock
                                        Requests
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="dropdown sales-handling-dropdown {{ $salesItems }}">
                            <a class="flex border-0 focus:outline-none outline-none items-center gap-[10px] px-[15px] py-[10px] rounded-[8px] body-14 semibold"
                                tabindex="0" role="button">
                                <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M18.3334 11.3337V8.00033C18.3334 3.83366 16.6667 2.16699 12.5001 2.16699H7.50008C3.33341 2.16699 1.66675 3.83366 1.66675 8.00033V13.0003C1.66675 17.167 3.33341 18.8337 7.50008 18.8337H10.8334"
                                        stroke="#4D4D4D" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M6.1084 12.5748L8.09174 9.9998C8.37507 9.63314 8.90007 9.56648 9.26674 9.84981L10.7918 11.0498C11.1584 11.3331 11.6834 11.2665 11.9667 10.9081L13.8918 8.4248"
                                        stroke="#4D4D4D" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M16.2334 13.6832L16.4668 14.1582C16.5834 14.3915 16.8751 14.6082 17.1334 14.6582L17.4501 14.7082C18.4001 14.8665 18.6251 15.5665 17.9418 16.2582L17.6501 16.5499C17.4584 16.7499 17.3501 17.1332 17.4084 17.3999L17.4501 17.5749C17.7084 18.7249 17.1001 19.1665 16.1001 18.5665L15.8834 18.4415C15.6251 18.2915 15.2084 18.2915 14.9501 18.4415L14.7334 18.5665C13.7251 19.1749 13.1168 18.7249 13.3834 17.5749L13.4251 17.3999C13.4834 17.1332 13.3751 16.7499 13.1834 16.5499L12.8918 16.2582C12.2084 15.5665 12.4334 14.8665 13.3834 14.7082L13.7001 14.6582C13.9501 14.6165 14.2501 14.3915 14.3668 14.1582L14.6001 13.6832C15.0501 12.7749 15.7834 12.7749 16.2334 13.6832Z"
                                        stroke="#4D4D4D" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                <span>Sales & Orders</span>
                            </a>
                            <ul tabindex="0"
                                class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm dropmenu">
                                <li>
                                    <a href="{{ route('outlet.salesOrders') }}" class="body-14 medium">
                                        <img src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" />
                                        Sales & Orders
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('outlet.customerList') }}" class="body-14 medium">
                                        <img src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" />
                                        Customers
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('outlet.productManagementList') }}" class="body-14 medium">
                                        <img src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" />
                                        Product Price Management
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ request()->is('outlet/downPayment*') ? 'active' : '' }}">
                            <a href="{{ route('outlet.downPayment') }}"
                                class="flex items-center gap-[10px] px-[15px] py-[10px] rounded-[8px] body-14 semibold">
                                <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4.16675 8.83366H5.83341C7.50008 8.83366 8.33341 8.00033 8.33341 6.33366V4.66699C8.33341 3.00033 7.50008 2.16699 5.83341 2.16699H4.16675C2.50008 2.16699 1.66675 3.00033 1.66675 4.66699V6.33366C1.66675 8.00033 2.50008 8.83366 4.16675 8.83366Z"
                                        stroke="#4D4D4D" stroke-width="1.5" stroke-miterlimit="10"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M14.1667 8.83366H15.8334C17.5001 8.83366 18.3334 8.00033 18.3334 6.33366V4.66699C18.3334 3.00033 17.5001 2.16699 15.8334 2.16699H14.1667C12.5001 2.16699 11.6667 3.00033 11.6667 4.66699V6.33366C11.6667 8.00033 12.5001 8.83366 14.1667 8.83366Z"
                                        stroke="#4D4D4D" stroke-width="1.5" stroke-miterlimit="10"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M14.1667 18.8337H15.8334C17.5001 18.8337 18.3334 18.0003 18.3334 16.3337V14.667C18.3334 13.0003 17.5001 12.167 15.8334 12.167H14.1667C12.5001 12.167 11.6667 13.0003 11.6667 14.667V16.3337C11.6667 18.0003 12.5001 18.8337 14.1667 18.8337Z"
                                        stroke="#4D4D4D" stroke-width="1.5" stroke-miterlimit="10"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M4.16675 18.8337H5.83341C7.50008 18.8337 8.33341 18.0003 8.33341 16.3337V14.667C8.33341 13.0003 7.50008 12.167 5.83341 12.167H4.16675C2.50008 12.167 1.66675 13.0003 1.66675 14.667V16.3337C1.66675 18.0003 2.50008 18.8337 4.16675 18.8337Z"
                                        stroke="#4D4D4D" stroke-width="1.5" stroke-miterlimit="10"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span>Down Payment</span>
                            </a>
                        </li>

                        <li class="dropdown cash-handling-dropdown {{ $cashHandlings }}">
                            <a class="flex border-0 focus:outline-none outline-none items-center gap-[10px] px-[15px] py-[10px] rounded-[8px] body-14 semibold"
                                tabindex="0" role="button">
                                <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.1666 15.4252C19.1833 16.0502 19.0166 16.6419 18.7166 17.1502C18.55 17.4502 18.325 17.7252 18.075 17.9502C17.5 18.4836 16.7416 18.8086 15.9 18.8336C14.6833 18.8586 13.6083 18.2336 13.0166 17.2752C12.7 16.7836 12.5083 16.1919 12.5 15.5669C12.475 14.5169 12.9416 13.5669 13.6916 12.9419C14.2583 12.4752 14.975 12.1836 15.7583 12.1669C17.6 12.1252 19.125 13.5835 19.1666 15.4252Z"
                                        stroke="#4D4D4D" stroke-width="1.5" stroke-miterlimit="10"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M14.5332 15.525L15.3749 16.325L17.1165 14.6416" stroke="#4D4D4D"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M2.6416 6.7002L9.99992 10.9585L17.3082 6.72517" stroke="#4D4D4D"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10 18.5085V10.9502" stroke="#4D4D4D" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M18.0083 8.14199V12.8587C18.0083 12.9004 18.0084 12.9337 18 12.9753C17.4167 12.467 16.6667 12.167 15.8334 12.167C15.05 12.167 14.325 12.442 13.75 12.9003C12.9834 13.5087 12.5 14.4503 12.5 15.5003C12.5 16.1253 12.675 16.717 12.9833 17.217C13.0583 17.3503 13.15 17.4753 13.25 17.592L11.725 18.4337C10.775 18.967 9.22501 18.967 8.27501 18.4337L3.82502 15.967C2.81668 15.4087 1.9917 14.0087 1.9917 12.8587V8.14199C1.9917 6.99199 2.81668 5.59201 3.82502 5.03368L8.27501 2.56699C9.22501 2.03366 10.775 2.03366 11.725 2.56699L16.175 5.03368C17.1834 5.59201 18.0083 6.99199 18.0083 8.14199Z"
                                        stroke="#4D4D4D" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                <span>Cash Handling</span>
                            </a>
                            <ul class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm dropmenu">
                                <li><a href="{{ route('outlet.daily-sales-summary') }}" class="body-14 medium"><img
                                            src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" /> Daily Sales
                                        Summary</a></li>
                                <li><a href="{{ route('outlet.debtCollections') }}" class="body-14 medium"><img
                                            src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" /> Debt
                                        Collections</a></li>
                                <li><a href="{{ route('outlet.recordExpenses') }}" class="body-14 medium"><img
                                            src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" /> Record
                                        Expenses</a></li>
                                <li><a href="{{ route('outlet.cashRemittance') }}" class="body-14 medium"><img
                                            src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" /> Cash
                                        Remittance</a></li>
                            </ul>
                        </li>
                    </ul>
                    @php
                        $unreadCount = 0;
                        $totalNotifications = 0;
                        if (Auth::guard('warehouse')->check()) {
                            $user = Auth::guard('warehouse')->user();
                            $role = 'Warehouse Manager';
                        } elseif (Auth::guard('outlet')->check()) {
                            $user = Auth::guard('outlet')->user();
                            $role = 'Outlet Manager';
                            $outlets = $user->outlet;
                            $unreadCount = $outlets ? $outlets->unreadNotifications()->count() : 0;
                            $totalNotifications = $outlets ? $outlets->notifications()->count() : 0;
                        } else {
                            $user = null;
                            $role = '';
                        }
                    @endphp
                    <!-- Right section: Icons + Profile -->
                    <div class="ml-auto flex gap-[15px] flex-wrap items-center">
                        <!-- Notification Box with Dropdown -->
                        <div class="notification-box relative">
                            <button type="button" onclick="toggleNotificationDropdown()"
                                class="w-[41px] h-[41px] rounded-full flex items-center justify-center bg-secondary-100 relative">
                                <img src="{{ asset('web/images/notification.svg') }}" alt="Notification" />
                                @if ($unreadCount > 0)
                                    <span id="notification-badge"
                                        class="absolute -top-1 -right-1 bg-[#ec188b] text-white text-[12px] font-semibold rounded-full w-[18px] h-[18px] flex items-center justify-center">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </button>

                            <!-- Notification Dropdown -->
                            <div id="notificationDropdown" class="notification-dropdown hidden">
                                <!-- Header -->
                                <div class="flex justify-between items-center p-4 border-b">
                                    <h3 class="font-semibold text-gray-800">Notifications</h3>
                                    @if ($unreadCount > 0)
                                        <button onclick="markAllAsRead()"
                                            class="text-sm text-pink-600 hover:text-pink-800">
                                            Mark all as read
                                        </button>
                                    @endif
                                </div>

                                <!-- Notifications List -->
                                <div class="notification-scroll" id="notificationsList">
                                    @php
                                        $unreadNotifications = $outlets
                                            ? $outlets->unreadNotifications()->latest()->take(5)->get()
                                            : collect();
                                    @endphp
                                    @forelse ($unreadNotifications as $notification)
                                        <div class="notification-item p-4 border-b bg-pink-50"
                                            data-notification-id="{{ $notification->id }}">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <h4 class="font-medium text-sm text-gray-800">
                                                        {{ $notification->data['title'] ?? 'Notification' }}
                                                    </h4>
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        {{ $notification->data['message'] ?? '' }}
                                                    </p>
                                                    <div class="flex items-center justify-between mt-2">
                                                        <p class="text-xs text-gray-400">
                                                            {{ $notification->created_at->diffForHumans() }}
                                                        </p>
                                                        <button onclick="markAsReadOnly('{{ $notification->id }}')"
                                                            class="text-xs text-pink-600 hover:text-pink-800 font-medium">
                                                            Mark as read
                                                        </button>
                                                    </div>
                                                </div>
                                                <span class="w-2 h-2 bg-pink-600 rounded-full mt-1 ml-2"></span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-8 text-center text-gray-500" id="noNotificationsMessage">
                                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                                </path>
                                            </svg>
                                            <p>No unread notifications</p>
                                        </div>
                                    @endforelse
                                </div>

                                <!-- Footer -->
                                @if ($totalNotifications > 0)
                                    <div class="p-3 border-t text-center">
                                        <a href="{{ route('outlet.notifications.all') }}"
                                            class="text-sm text-pink-600 hover:text-pink-800 font-medium">
                                            View all notifications ({{ $totalNotifications }})
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="dropdown">
                            <div tabindex="0" role="button" class="flex items-center gap-[15px]">
                                <figure>
                                    @if ($user && $user->profile_image && file_exists(public_path($user->profile_image)))
                                        <img src="{{ asset($user->profile_image) }}" alt="Profile"
                                            class="h-[40px] w-[40px] rounded-full figure" />
                                    @else
                                        <!-- Default Profile Image -->
                                        <div
                                            class="h-[40px] w-[40px] rounded-full bg-gray-200 flex items-center justify-center figure">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg" class="text-gray-500">
                                                <path
                                                    d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M12 14C7.58172 14 4 17.5817 4 22H20C20 17.5817 16.4183 14 12 14Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </div>
                                    @endif
                                </figure>
                                <figcaption>
                                    @if ($user)
                                        <h5 class="text-sm semibold text-gry-800">
                                            {{ $outlets ? ucfirst($outlets->name) : '' }}</h5>
                                        <h5 class="body-16 semibold text-gry-800 flex align-item-center">
                                            {{ $user ? ucfirst($user->first_name . ' ' . $user->last_name) : '' }}
                                            <img src="{{ asset('web/images/arrow.svg') }}" alt="Arrow" />
                                        </h5>
                                    @else
                                        <!-- Optional fallback -->
                                        <h5 class="body-16 semibold text-gry-800">Guest</h5>
                                        <p class="flex items-center gap-[5px]">
                                            <span class="body-14 text-gry-800">Visitor</span>
                                            <img src="{{ asset('web/images/arrow.svg') }}" alt="Arrow" />
                                        </p>
                                    @endif
                                </figcaption>
                            </div>
                            <ul tabindex="0"
                                class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm right-0">
                                <li><a href="{{ route('outlet.myAccount') }}">Edit Profile</a></li>
                                <li><a href="{{ route('outlet.showChangePasswordForm') }}">Change Password</a></li>
                                <li><a class="logout-button">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="flex-none lg:hidden ml-auto">
                    <label for="my-drawer-3" aria-label="open sidebar"
                        class="btn btn-primary p-[0] w-[45px] h-[45px] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            class="inline-block h-6 w-6 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </label>
                </div>
            </div>
        </div>
    </div>
</header>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all dropdown elements
        const stockTransferDropdown = document.querySelector('.stock-transfer-dropdown');
        const cashHandlingDropdown = document.querySelector('.cash-handling-dropdown');
        const salesHandlingDropdown = document.querySelector('.sales-handling-dropdown');

        // Function to close all dropdowns
        const closeAllDropdowns = () => {
            if (stockTransferDropdown) stockTransferDropdown.classList.remove('open');
            if (cashHandlingDropdown) cashHandlingDropdown.classList.remove('open');
            if (salesHandlingDropdown) salesHandlingDropdown.classList.remove('open');
        };

        // Function to toggle Stock Transfer dropdown
        if (stockTransferDropdown) {
            stockTransferDropdown.addEventListener('click', function() {
                if (!stockTransferDropdown.classList.contains('open')) {
                    closeAllDropdowns();
                    stockTransferDropdown.classList.add('open');
                } else {
                    stockTransferDropdown.classList.remove('open');
                }
            });
        }

        // Function to toggle Sales Handling dropdown
        if (salesHandlingDropdown) {
            salesHandlingDropdown.addEventListener('click', function() {
                if (!salesHandlingDropdown.classList.contains('open')) {
                    closeAllDropdowns();
                    salesHandlingDropdown.classList.add('open');
                } else {
                    salesHandlingDropdown.classList.remove('open');
                }
            });
        }

        // Function to toggle Cash Handling dropdown
        if (cashHandlingDropdown) {
            cashHandlingDropdown.addEventListener('click', function() {
                if (!cashHandlingDropdown.classList.contains('open')) {
                    closeAllDropdowns();
                    cashHandlingDropdown.classList.add('open');
                } else {
                    cashHandlingDropdown.classList.remove('open');
                }
            });
        }
    });

    // Notification Dropdown Functions
    function toggleNotificationDropdown() {
        const dropdown = document.getElementById('notificationDropdown');
        dropdown.classList.toggle('hidden');
    }

    // Close notification dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const notificationBox = document.querySelector('.notification-box');
        const dropdown = document.getElementById('notificationDropdown');

        if (notificationBox && dropdown && !notificationBox.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Mark single notification as read
    function markAsRead(notificationId, redirectUrl = null) {
        fetch(`/outlet/notifications/${notificationId}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update badge count
                    updateBadgeCount();

                    // Redirect if URL provided
                    if (redirectUrl && redirectUrl !== '#') {
                        window.location.href = redirectUrl;
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Mark notification as read without redirect
    function markAsReadOnly(notificationId) {
        fetch(`/outlet/notifications/${notificationId}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the notification item from the dropdown
                    const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    if (notificationItem) {
                        notificationItem.remove();
                    }

                    // Check if there are any notifications left
                    const notificationsList = document.getElementById('notificationsList');
                    const remainingNotifications = notificationsList.querySelectorAll('.notification-item');

                    if (remainingNotifications.length === 0) {
                        // Show "No unread notifications" message
                        notificationsList.innerHTML = `
                        <div class="p-8 text-center text-gray-500" id="noNotificationsMessage">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <p>No unread notifications</p>
                        </div>
                    `;
                    }

                    // Update badge count
                    updateBadgeCount();
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Mark all notifications as read
    function markAllAsRead() {
        fetch('/outlet/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear all notification items
                    const notificationsList = document.getElementById('notificationsList');
                    notificationsList.innerHTML = `
                    <div class="p-8 text-center text-gray-500" id="noNotificationsMessage">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <p>No unread notifications</p>
                    </div>
                `;

                    // Update badge count
                    updateBadgeCount();
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Update badge count
    function updateBadgeCount() {
        fetch('/outlet/notifications/unread-count', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notification-badge');
                if (data.count > 0) {
                    if (badge) {
                        badge.textContent = data.count;
                    } else {
                        // Create badge if it doesn't exist
                        const notificationBtn = document.querySelector('.notification-box button');
                        const newBadge = document.createElement('span');
                        newBadge.id = 'notification-badge';
                        newBadge.className =
                            'absolute -top-1 -right-1 bg-[#ec188b] text-white text-[12px] font-semibold rounded-full w-[18px] h-[18px] flex items-center justify-center';
                        newBadge.textContent = data.count;
                        notificationBtn.appendChild(newBadge);
                    }
                } else {
                    if (badge) badge.remove();
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logoutButton = document.querySelector('.logout-button');

        if (logoutButton) {
            logoutButton.addEventListener('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to log out?",
                    icon: 'warning',
                    showCancelButton: true,
                    customClass: {
                        popup: "theme-confirm-popup"
                    },
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, log out!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('logout-form').submit();
                    }
                });
            });
        }
    });
</script>
