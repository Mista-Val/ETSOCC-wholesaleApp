@extends('web.auth.app')

@section('content')
    @include('web.outlet.shared.header')

    {{-- Add Customer Modal --}}
    <div id="add-customer-modal"
        class="fixed inset-0 z-50 {{ $errors->any() && !session('edit_customer') ? 'flex' : 'hidden' }} items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="modal-box bg-white rounded-xl shadow-lg w-full max-w-md p-0 relative" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between py-3 px-4 border-b border-gray-200">
                <h2 class="h6 text-gray-800 font-semibold">Add new customer</h2>
                <button type="button" onclick="closeAddModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="41" height="42" viewBox="0 0 41 42" fill="none">
                        <ellipse opacity="0.15" cx="20.5" cy="21" rx="20.5" ry="21"
                            fill="#01ABEC" />
                        <path
                            d="M20.4998 31.9375C26.3722 31.9375 31.1769 27.0156 31.1769 21C31.1769 14.9844 26.3722 10.0625 20.4998 10.0625C14.6274 10.0625 9.82275 14.9844 9.82275 21C9.82275 27.0156 14.6274 31.9375 20.4998 31.9375Z"
                            stroke="#01ABEC" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M17.4785 24.0949L23.5217 17.9043" stroke="#01ABEC" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M23.5217 24.0949L17.4785 17.9043" stroke="#01ABEC" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <div class="modal-content p-4">
                <form method="POST" action="{{ route('outlet.customerStore') }}" id="add-customer-form">
                    @csrf
                    <div class="form-group mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Phone number</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                            class="form-control w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter phone number" />
                        @error('phone_number')
                            <small class="text-red-500 text-xs mt-1 block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Customer name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="form-control w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter Name" />
                        @error('name')
                            <small class="text-red-500 text-xs mt-1 block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Address</label>
                        <textarea name="address" rows="3"
                            class="form-control w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter Address">{{ old('address') }}</textarea>
                        @error('address')
                            <small class="text-red-500 text-xs mt-1 block">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-pink-500 hover:bg-pink-600 text-white font-semibold text-base py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <span>Create</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Customer Modal --}}
    <div id="edit-customer-modal"
        class="fixed inset-0 z-50 {{ session('edit_customer') && $errors->any() ? 'flex' : 'hidden' }} items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="modal-box bg-white rounded-xl shadow-lg w-full max-w-md p-0 relative" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between py-3 px-4 border-b border-gray-200">
                <h2 class="h6 text-gray-800 font-semibold">Edit customer</h2>
                <button type="button" onclick="closeEditModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="41" height="42" viewBox="0 0 41 42"
                        fill="none">
                        <ellipse opacity="0.15" cx="20.5" cy="21" rx="20.5" ry="21"
                            fill="#01ABEC" />
                        <path
                            d="M20.4998 31.9375C26.3722 31.9375 31.1769 27.0156 31.1769 21C31.1769 14.9844 26.3722 10.0625 20.4998 10.0625C14.6274 10.0625 9.82275 14.9844 9.82275 21C9.82275 27.0156 14.6274 31.9375 20.4998 31.9375Z"
                            stroke="#01ABEC" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M17.4785 24.0949L23.5217 17.9043" stroke="#01ABEC" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M23.5217 24.0949L17.4785 17.9043" stroke="#01ABEC" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <div class="modal-content p-4">
                <form method="POST" action="" id="edit-customer-form">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Phone number</label>
                        <input type="text" name="phone_number" id="edit-phone-number"
                            class="form-control w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter phone number" />
                        @if (session('edit_customer'))
                            @error('phone_number')
                                <small class="text-red-500 text-xs mt-1 block">{{ $message }}</small>
                            @enderror
                        @endif
                    </div>

                    <div class="form-group mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Customer name</label>
                        <input type="text" name="name" id="edit-name"
                            class="form-control w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter Name" />
                        @if (session('edit_customer'))
                            @error('name')
                                <small class="text-red-500 text-xs mt-1 block">{{ $message }}</small>
                            @enderror
                        @endif
                    </div>

                    <div class="form-group mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Address</label>
                        <textarea name="address" id="edit-address" rows="3"
                            class="form-control w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter Address"></textarea>
                        @if (session('edit_customer'))
                            @error('address')
                                <small class="text-red-500 text-xs mt-1 block">{{ $message }}</small>
                            @enderror
                        @endif
                    </div>

                    <button type="submit"
                        class="w-full bg-pink-500 hover:bg-pink-600 text-white font-semibold text-base py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <span>Update</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Customer Modal --}}
    <div id="delete-customer-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="modal-box bg-white rounded-xl shadow-lg w-full max-w-md p-0 relative"
            onclick="event.stopPropagation()">
            <div class="flex items-center justify-between py-3 px-4 border-b border-gray-200">
                <h2 class="h6 text-gray-800 font-semibold">Delete Customer</h2>
                <button type="button" onclick="closeDeleteModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="41" height="42" viewBox="0 0 41 42"
                        fill="none">
                        <ellipse opacity="0.15" cx="20.5" cy="21" rx="20.5" ry="21"
                            fill="#01ABEC" />
                        <path
                            d="M20.4998 31.9375C26.3722 31.9375 31.1769 27.0156 31.1769 21C31.1769 14.9844 26.3722 10.0625 20.4998 10.0625C14.6274 10.0625 9.82275 14.9844 9.82275 21C9.82275 27.0156 14.6274 31.9375 20.4998 31.9375Z"
                            stroke="#01ABEC" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M17.4785 24.0949L23.5217 17.9043" stroke="#01ABEC" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M23.5217 24.0949L17.4785 17.9043" stroke="#01ABEC" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <div class="modal-content p-4">
                <p class="text-gray-600 mb-6">Are you sure you want to delete this customer? This action cannot be undone.
                </p>

                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold text-base py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <span>Cancel</span>
                    </button>

                    <form method="POST" action="" id="delete-customer-form" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full bg-pink-500 hover:bg-pink-600 text-white font-semibold text-base py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <span>Delete</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <main class="dashboard-screen-bg relative">
        {{-- Title Section --}}
        <section class="dashboard-title-section bg-white border-b border-gray-200">
            <div class="container-fluid">
                <div class="dashboard-inner flex items-center justify-between gap-2 flex-wrap py-2">
                    <h1 class="h6 text-gray-800">Customers</h1>
                    <div class="breadcrumb flex items-center gap-[10px] flex-wrap">
                        <a href="{{ route('outlet.outlet-dashboard') }}">
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
                        <span class="body-14 text-gry-800 bold">Customers</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="container-fluid mt-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
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
                <form method="GET" action="{{ route('outlet.customerList') }}"
                    class="search-record-box flex items-center justify-between flex-wrap gap-[15px] mb-[15px]">
                    <div class="search-box relative max-w-[390px] w-full">
                        <input type="text" name="search" placeholder="Search" value="{{ request('search') }}"
                            class="form-control !pr-[50px]" />
                        <button type="submit" class="absolute top-1/2 right-[15px] translate-y-[-50%]">
                            <img src="{{ asset('web/images/search.svg') }}" alt="search" />
                        </button>
                    </div>

                    <div class="date-record-btn w-full md:w-auto flex items-center gap-[15px] flex-wrap md:flex-nowrap">
                        <a href="{{ route('outlet.customerList') }}" class="btn btn-secondary">Clear Filter</a>
                        <button class="btn btn-primary" onclick="openAddModal()" type="button">Add Customer</button>
                    </div>
                </form>

                {{-- Customers Table --}}
                <div class="white-box p-0 rounded-lg shadow">
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap text-sm text-left text-gray-600">
                            <thead class="text-gray-900 font-semibold uppercase border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3">ID</th>
                                    <th class="px-6 py-3">Name</th>
                                    <th class="px-6 py-3">Phone Number</th>
                                    <th class="px-6 py-3">Address</th>
                                    <th class="px-6 py-3">Created Date</th>
                                    <th class="px-6 py-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($customers as $customer)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3">#{{ $customer->id }}</td>
                                        <td class="px-6 py-3">{{ ucwords($customer->name) }}</td>
                                        <td class="px-6 py-3">{{ $customer->phone_number }}</td>
                                        <td class="px-6 py-3">
                                            <span class="max-w-xs truncate block"
                                                title="{{ $customer->address ?? 'N/A' }}">
                                                {{ $customer->address ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3">{{ $customer->created_at->format('d M Y') }}</td>
                                        <td class="px-6 py-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button
                                                    onclick='openEditModal({{ $customer->id }}, "{{ addslashes($customer->name) }}", "{{ $customer->phone_number }}", "{{ addslashes($customer->address ?? '') }}")'
                                                    class="p-2 rounded-full bg-pink-100 text-pink-500 hover:bg-blue-200 inline-flex items-center justify-center"
                                                    title="Edit Customer">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>

                                                <button
                                                    onclick="openDeleteModal({{ $customer->id }}, '{{ addslashes($customer->name) }}')"
                                                    class="p-2 rounded-full bg-pink-100 text-pink-500 hover:bg-red-200 inline-flex items-center justify-center"
                                                    title="Delete Customer">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-3 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" class="w-16 h-16">
                                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                                    <circle cx="12" cy="7" r="4" />
                                                </svg>
                                                <h3><strong>No customers found.</strong></h3>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    {{-- <div class="flex flex-wrap gap-4 items-center justify-between mt-4 border-t border-gray-200 p-4">
                        {{ $customers->links() }}
                    </div> --}}
                     @if ($customers->total() > 0)
                        <div class="border-t border-[#E9E9E9] p-[15px]">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of
                                    {{ $customers->total() }} results
                                </div>
                                <div>
                                    {{ $customers->withQueryString()->links('vendor.pagination.custom-new') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>

    <script>
        /**
         * Clear validation error messages from a modal
         */
        function clearValidationErrors(modalId) {
            const errorMessages = document.querySelectorAll(`#${modalId} .text-red-500`);
            errorMessages.forEach(msg => msg.parentNode?.removeChild(msg));
        }

        /**
         * Open Add Customer Modal
         */
        function openAddModal() {
            const modal = document.getElementById('add-customer-modal');
            const form = document.getElementById('add-customer-form');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            form.reset();
            form.querySelectorAll('input[type="text"], textarea').forEach(input => {
                input.value = '';
                input.setAttribute('value', '');
            });

            clearValidationErrors('add-customer-modal');
        }

        /**
         * Close Add Customer Modal
         */
        function closeAddModal() {
            const modal = document.getElementById('add-customer-modal');
            const form = document.getElementById('add-customer-form');

            modal.classList.add('hidden');
            modal.classList.remove('flex');

            form.reset();
            form.querySelectorAll('input[type="text"], textarea').forEach(input => {
                input.value = '';
                input.setAttribute('value', '');
            });

            clearValidationErrors('add-customer-modal');
        }

        /**
         * Open Edit Customer Modal
         */
        function openEditModal(customerId, customerName, customerPhone, customerAddress) {
            const modal = document.getElementById('edit-customer-modal');
            const form = document.getElementById('edit-customer-form');
            const nameInput = document.getElementById('edit-name');
            const phoneInput = document.getElementById('edit-phone-number');
            const addressInput = document.getElementById('edit-address');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            const updateUrlBase = "{{ route('outlet.customerUpdate', ['customer' => 'PLACEHOLDER']) }}";
            form.action = updateUrlBase.replace('PLACEHOLDER', customerId);

            @if (session('edit_customer') && $errors->any())
                nameInput.value = "{{ old('name', session('edit_customer_name')) }}";
                phoneInput.value = "{{ old('phone_number', session('edit_customer_phone')) }}";
                addressInput.value = "{{ old('address', session('edit_customer_address')) }}";
            @else
                nameInput.value = customerName;
                phoneInput.value = customerPhone;
                addressInput.value = customerAddress || '';
                clearValidationErrors('edit-customer-modal');
            @endif
        }

        /**
         * Close Edit Customer Modal
         */
        function closeEditModal() {
            const modal = document.getElementById('edit-customer-modal');
            const form = document.getElementById('edit-customer-form');
            const nameInput = document.getElementById('edit-name');
            const phoneInput = document.getElementById('edit-phone-number');
            const addressInput = document.getElementById('edit-address');

            modal.classList.add('hidden');
            modal.classList.remove('flex');

            if (nameInput) nameInput.value = '';
            if (phoneInput) phoneInput.value = '';
            if (addressInput) addressInput.value = '';

            form.action = '';
            clearValidationErrors('edit-customer-modal');
        }

        /**
         * Open Delete Customer Modal
         */
        function openDeleteModal(customerId, customerName) {
            const modal = document.getElementById('delete-customer-modal');
            const form = document.getElementById('delete-customer-form');
            const modalText = modal.querySelector('.modal-content p');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            const deleteUrlBase = "{{ route('outlet.customerDestroy', ['customer' => 'PLACEHOLDER']) }}";
            form.action = deleteUrlBase.replace('PLACEHOLDER', customerId);

            if (modalText) {
                modalText.innerHTML =
                    `Are you sure you want to delete customer <b>${customerName}</b>? This action cannot be undone.`;
            }
        }

        /**
         * Close Delete Customer Modal
         */
        function closeDeleteModal() {
            const modal = document.getElementById('delete-customer-modal');
            const form = document.getElementById('delete-customer-form');
            const modalText = modal.querySelector('.modal-content p');

            modal.classList.add('hidden');
            modal.classList.remove('flex');

            form.action = '';

            if (modalText) {
                modalText.innerHTML = 'Are you sure you want to delete this customer? This action cannot be undone.';
            }
        }

        /**
         * Initialize on page load
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Reopen edit modal if validation errors exist
            @if (session('edit_customer') && $errors->any())
                openEditModal(
                    {{ session('edit_customer_id') }},
                    '{{ session('edit_customer_name') }}',
                    '{{ session('edit_customer_phone') }}',
                    '{{ session('edit_customer_address') }}'
                );
            @endif
        });

        /**
         * Handle Escape key to close modals
         */
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
                closeDeleteModal();
            }
        });
    </script>
@endsection
