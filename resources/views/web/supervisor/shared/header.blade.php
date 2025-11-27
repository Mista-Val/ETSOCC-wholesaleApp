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
    $cashMovementsItem = ['bank-deposit', 'cash-in-hand', 'bank-deposit-create','final-cash-create','final-cash'];
    $cashMovementsItems = in_array($currentSegment, $cashMovementsItem) ? 'active show' : '';
    $externalCashItem = [
        'external-source',
        'external-source-create',
        // 'final-cash-create',
        // 'final-cash',
        'external-cash-inflow',
        'external-cash-inflow-create',
        'external-cash-outFlow',
        'external-cash-outFlow-create',
        'record-expense-request',
        'cash-refund-request'
    ];
    $externalCashItems = in_array($currentSegment, $externalCashItem) ? 'active show' : '';

    $stockOverviewItem = ['warehouse-levels','outlet-levels','sales-list','sales-list-view','return-request'];
    $stockOverviewItems = in_array($currentSegment, $stockOverviewItem) ? 'active show' : '';

    $user = Auth::guard('supervisor')->user();

    // Get notification counts for supervisor
    // if (Auth::guard('supervisor')->check()) {
    //     $supervisor = $user->supervisor;
    //     $unreadCount = $supervisor ? $supervisor->unreadNotifications()->count() : 0;
    //     $totalNotifications = $supervisor ? $supervisor->notifications()->count() : 0;
    // }
    if (Auth::guard('supervisor')->check()) {
        $user = Auth::guard('supervisor')->user();
        $role = 'Supervisor';
        $supervisor = $user->supervisor;
        $unreadCount = $supervisor ? $supervisor->unreadNotifications()->count() : 0;
        $totalNotifications = $supervisor ? $supervisor->notifications()->count() : 0;
    }
@endphp

<style>
    .dropdown-content {
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
        display: flex;
        flex-direction: column;
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
        flex: 1;
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
                <a class="flex items-center" href="{{ route('supervisor.supervisor-dashboard') }}">
                    <img src="{{ asset('web/images/logo.svg') }}" alt="Logo" class=" h-[50px] md:h-[55px] w-auto" />
                </a>
                <div class="head-right flex-1 hidden lg:flex justify-between gap-[15px] flex-wrap">
                    <ul class="dropdown border-0 nav-item hidden md:flex items-center gap-[5px] menu-list">
                        <li class="{{ request()->is('supervisor/supervisor-dashboard*') ? 'active' : '' }}">
                            <a href="{{ route('supervisor.supervisor-dashboard') }}"
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

                        <li class="dropdown cash-movements-dropdown {{ $cashMovementsItems }}">
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
                                <span>Cash Movements</span>
                            </a>
                            <ul class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm">
                                <li>
                                    <a href="{{ route('supervisor.bankDeposit') }}" class="body-14 medium">
                                        <img src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" />
                                        Bank Deposit
                                    </a>
                                </li>
                                 {{-- <li>
                                    <a href="{{ route('supervisor.finalCashDestination') }}" class="body-14 medium">
                                        <img src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" />
                                        Final Cash Destination
                                    </a>
                                </li> --}}
                            </ul>
                        </li>

                           <li class="dropdown stock-operations-dropdown {{ $stockOverviewItems }}">
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
                                <span>Stock Overview</span>
                            </a>
                            <ul class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm">
                                <li>
                                    <a href="{{ route('supervisor.warehouse-levels') }}" class="body-14 medium">
                                        <img src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" />
                                        Warehouse Levels
                                    </a>
                                </li>
                                   <li>
                                    <a href="{{ route('supervisor.outlet-levels') }}" class="body-14 medium">
                                        <img src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" />
                                        Outlet Levels
                                    </a>
                                </li>
                                 <li>
                                    <a href="{{ route('supervisor.salesList') }}" class="body-14 medium">
                                        <img src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" />
                                       Sales Report
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('supervisor.return-request') }}" class="body-14 medium">
                                        <img src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" />
                                      Return Request
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ request()->is('supervisor/cashRemittance*') ? 'active' : '' }}">
                            <a href="{{ route('supervisor.cashRemittance') }}"
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
                                <span>Cash Remittances</span>
                            </a>
                        </li>

                        <li class="dropdown external-source-dropdown {{ $externalCashItems }}">
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
                                <span>External Cash</span>
                            </a>
                            <ul class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm">
                                <li>
                                    <a href="{{ route('supervisor.externalCashInflow') }}" class="body-14 medium">
                                        <img src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" />
                                        External Cash Inflow
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('supervisor.externalCashOutFlow') }}" class="body-14 medium">
                                        <img src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" />
                                        External Cash Outflow
                                    </a>
                                </li>
                                 <li>
                                    <a href="{{ route('supervisor.record-expense-request') }}" class="body-14 medium">
                                        <img src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" />
                                       Record Expense Request
                                    </a>
                                </li>
                                 <li>
                                    <a href="{{ route('supervisor.cash-refund-request') }}" class="body-14 medium">
                                        <img src="{{ asset('web/images/arrow01.svg') }}" alt="arrow" />
                                        Cash Refund Request
                                    </a>
                                </li>
                            </ul>
                        </li>
                           {{-- <li class="{{ request()->is('supervisor/record-expense-request*') ? 'active' : '' }}">
                            <a href="{{ route('supervisor.record-expense-request') }}"
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
                                <span>Record Expense Request</span>
                            </a>
                        </li> --}}
                          {{-- <li class="{{ request()->is('supervisor/cash-refund-request*') ? 'active' : '' }}">
                            <a href="{{ route('supervisor.cash-refund-request') }}"
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
                                <span>Cash Refund Request</span>
                            </a>
                        </li> --}}
                    </ul>

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

                                <!-- Notifications List (Scrollable) -->
                                <div class="notification-scroll" id="notificationsList">
                                    @php
                                        $unreadNotifications = $supervisor
                                            ? $supervisor->unreadNotifications()->latest()->take(5)->get()
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
                                    <div class="p-3 border-t text-center bg-white">
                                        <a href="{{ route('supervisor.notifications.all') }}"
                                            class="text-sm text-pink-600 hover:text-pink-800 font-medium">
                                            View all notifications ({{ $totalNotifications }})
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="dropdown profile-dropdown">
                            <div tabindex="0" role="button" class="flex items-center gap-[15px]">
                                <figure>
                                    @if ($user && $user->profile_image && file_exists(public_path($user->profile_image)))
                                        <img src="{{ asset($user->profile_image) }}" alt="Profile"
                                            class="h-[40px] w-[40px] rounded-full figure" />
                                    @else
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
                                        <h5 class="body-16 semibold text-gry-800 align-item-center">
                                            {{ ucfirst($user->first_name . ' ' . $user->last_name) }}
                                        </h5>
                                        <p class="flex items-center gap-[5px]">
                                            <span class="body-14 text-gry-800">Supervisor</span>
                                            <img src="{{ asset('web/images/arrow.svg') }}" alt="Arrow" />
                                        </p>
                                    @else
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
                                <li><a href="{{ route('supervisor.myAccount') }}">Edit Profile</a></li>
                                <li><a href="{{ route('supervisor.showChangePasswordForm') }}">Change Password</a>
                                </li>
                                <li><a class="logout-button">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Toggle -->
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
                {{-- <div class="drawer-side">
            <label for="my-drawer-3" aria-label="close sidebar" class="drawer-overlay"></label>
            <ul class="menu bg-base-200 min-h-full w-80 p-4">
               <!-- Sidebar content here -->
               <li><a class="semibold body-16">Dashboard</a></li>
               <li>
                  <details>
                     <summary class="semibold body-16">Stock Operations</summary>
                     <ul>
                        <li><a>Receive Stock</a></li>
                        <li><a>Transfer to Outlets</a></li>
                        <li><a>Returns</a></li>
                        <li><a>Outlet Levels</a></li>
                     </ul>
                  </details>
               </li>
               <li><a class="semibold body-16">Sales & orders</a></li>
               <li><a class="semibold body-16">Cash Handling</a></li>
               <li><a class="semibold body-16">Notifications</a></li>
               <li><a class="semibold body-16">Logout</a></li>
            </ul>
         </div> --}}
        </div>
    </div>
</header>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cashMovementsDropdown = document.querySelector('.cash-movements-dropdown');
        // cashHandlingDropdown and salesHandlingDropdown were commented out in the original JS, 
        // but included here for completeness if you add the classes back to the main menu:
        const externalSourceDropdown = document.querySelector('.external-source-dropdown');
        // const salesHandlingDropdown = document.querySelector('.sales-handling-dropdown');

        // ** FIX: Added selector for the new profile-dropdown class **
        const profileDropdown = document.querySelector('.profile-dropdown');


        const stockOperationDropdown = document.querySelector('.stock-operations-dropdown');

        const closeAllDropdowns = () => {
            if (cashMovementsDropdown) cashMovementsDropdown.classList.remove('open');
            if (externalSourceDropdown) externalSourceDropdown.classList.remove('open');
            if (stockOperationDropdown) stockOperationDropdown.classList.remove('open');
            // if (salesHandlingDropdown) salesHandlingDropdown.classList.remove('open');
            // ** FIX: Close the profile dropdown **
            if (profileDropdown) profileDropdown.classList.remove('open');
        };

        const setupDropdownToggle = (dropdownElement) => {
            if (dropdownElement) {
                // Target the element with role="button". This handles both <li><a> and <div><div role="button">
                const toggleButton = dropdownElement.querySelector('[role="button"]');
                if (toggleButton) {
                    toggleButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (!dropdownElement.classList.contains('open')) {
                            closeAllDropdowns();
                            dropdownElement.classList.add('open');
                        } else {
                            dropdownElement.classList.remove('open');
                        }
                    });
                }
            }
        };

        setupDropdownToggle(cashMovementsDropdown);
        setupDropdownToggle(externalSourceDropdown);
        setupDropdownToggle(stockOperationDropdown);

        // ** FIX: Setup the profile dropdown **
        setupDropdownToggle(profileDropdown);
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
        fetch(`/supervisor/notifications/${notificationId}/mark-as-read`, {
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
        fetch(`/supervisor/notifications/${notificationId}/mark-as-read`, {
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
        fetch('/supervisor/notifications/mark-all-read', {
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
        fetch('/supervisor/notifications/unread-count', {
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
