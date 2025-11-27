@extends('web.auth.app')

@section('content')
    @include('web.warehouse.shared.header')

    <main class="dashboard-screen-bg relative">
        {{-- Title Section --}}
        <section class="dashboard-title-section bg-white border-b border-gray-200">
            <div class="container-fluid">
                <div class="dashboard-inner flex items-center justify-between gap-2 flex-wrap py-2">
                    <h1 class="h6 text-gray-800">Notifications</h1>
                    <div class="breadcrumb flex items-center gap-[10px] flex-wrap">
                        <a href="{{ route('warehouse.dashboard') }}">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8.39173 2.34954L2.61673 6.97453C1.96673 7.4912 1.55006 8.5829 1.69172 9.39956L2.80006 16.0329C3.00006 17.2162 4.13339 18.1745 5.33339 18.1745H14.6667C15.8584 18.1745 17.0001 17.2079 17.2001 16.0329L18.3084 9.39956C18.4417 8.5829 18.0251 7.4912 17.3834 6.97453L11.6084 2.35789C10.7167 1.64122 9.27506 1.64121 8.39173 2.34954Z"
                                    stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M10.0001 12.9167C11.1507 12.9167 12.0834 11.9839 12.0834 10.8333C12.0834 9.68274 11.1507 8.75 10.0001 8.75C8.84949 8.75 7.91675 9.68274 7.91675 10.8333C7.91675 11.9839 8.84949 12.9167 10.0001 12.9167Z"
                                    stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </a>
                        <span class="text-gry-300">/</span>
                        <span class="body-14 text-gry-800 bold">Notifications</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="container-fluid mt-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="container-fluid mt-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Content Section --}}
        <section class="dashboard-content py-4 md:py-8 flex-1">
            <div class="container-fluid">
                {{-- Search and Filter Form --}}
                {{-- <form method="GET" action="{{ route('outlet.notifications.all') }}"
                    class="search-record-box flex items-center justify-between flex-wrap gap-[15px] mb-[15px]">
                    <div class="search-box relative max-w-[390px] w-full">
                        <input type="text" name="search" placeholder="Search" value="{{ request('search') }}"
                            class="form-control !pr-[50px]" />
                        <button type="submit" class="absolute top-1/2 right-[15px] translate-y-[-50%]">
                            <img src="{{ asset('web/images/search.svg') }}" alt="search" />
                        </button>
                    </div>

                    <div class="date-record-btn w-full md:w-auto flex items-center gap-[15px] flex-wrap md:flex-nowrap">
                        <a href="{{ route('outlet.notifications.all') }}" class="btn btn-secondary">Clear Filter</a>
                    </div>
                </form> --}}

                {{-- Notifications Table --}}
                <div class="white-box p-0 rounded-lg shadow">
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap text-sm text-left text-gray-600">
                            <thead class="text-gray-900 font-semibold uppercase border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3">ID</th>
                                    <th class="px-6 py-3">Title</th>
                                    <th class="px-6 py-3">Message</th>
                                    <th class="px-6 py-3">Date</th>
                                     {{-- <th class="px-6 py-3">Time</th> --}}
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($notifications as $notification)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3">#{{ $notification->id }}</td>
                                        <td class="px-6 py-3">{{ $notification->data['title'] }}</td>
                                        <td class="px-6 py-3">{{ $notification->data['message'] }}</td>
                                        <td class="px-6 py-3">{{ $notification->created_at->format('d M Y') }}</td>
                                        {{-- <td class="px-6 py-3">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </td> --}}
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-3 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" class="w-16 h-16">
                                                    <path
                                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                </svg>
                                                <h3><strong>No notifications found.</strong></h3>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($notifications->hasPages())
                        <div class="flex flex-wrap gap-4 items-center justify-between mt-4 border-t border-gray-200 p-4">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>

    <script>
        /**
         * Auto-hide success/error messages after 5 seconds
         */
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
    </script>
@endsection
