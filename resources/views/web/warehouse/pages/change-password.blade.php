@extends('web.auth.app')

@section('content')
    @include('web.warehouse.shared.header')

    <main class="dashboard-screen-bg relative">
        <section class="dashboard-title-section bg-white border-b border-gry-50 bg-white">
            <div class="container-fluid">
                <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                    <h1 class="h6 text-gry-800">Change Password</h1>
                    <div class="breadcrumb flex items-center gap-[10px] flex-wrap">

                        @if ($role == 'warehouse-manager')
                            <a href="{{ route('warehouse.dashboard') }}" class="text-gry-600 hover:underline">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M8.39 2.35L2.62 6.97C1.97 7.49 1.55 8.58 1.69 9.40L2.80 16.03C3.00 17.22 4.13 18.17 5.33 18.17H14.67C15.86 18.17 17.00 17.21 17.20 16.03L18.31 9.40C18.44 8.58 18.03 7.49 17.38 6.97L11.61 2.36C10.72 1.64 9.28 1.64 8.39 2.35Z"
                                        stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M10.00 12.92C11.15 12.92 12.08 11.98 12.08 10.83C12.08 9.68 11.15 8.75 10.00 8.75C8.85 8.75 7.92 9.68 7.92 10.83C7.92 11.98 8.85 12.92 10.00 12.92Z"
                                        stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                        @elseif($role == 'outlet-manager')
                            <a href="{{ route('outlet.outlet-dashboard') }}" class="text-gry-600 hover:underline">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M8.39 2.35L2.62 6.97C1.97 7.49 1.55 8.58 1.69 9.40L2.80 16.03C3.00 17.22 4.13 18.17 5.33 18.17H14.67C15.86 18.17 17.00 17.21 17.20 16.03L18.31 9.40C18.44 8.58 18.03 7.49 17.38 6.97L11.61 2.36C10.72 1.64 9.28 1.64 8.39 2.35Z"
                                        stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M10.00 12.92C11.15 12.92 12.08 11.98 12.08 10.83C12.08 9.68 11.15 8.75 10.00 8.75C8.85 8.75 7.92 9.68 7.92 10.83C7.92 11.98 8.85 12.92 10.00 12.92Z"
                                        stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                        @endif

                        <span class="text-gry-300">/</span>
                        <span class="body-14 text-gry-800 bold">Change Password</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
            <div class="container">
                <div class="white-box p-[15px] md:p-[30px]">

                    {{-- Success message --}}
                    @if (session('success'))
                        <p class="text-green-600 text-sm mb-4">{{ session('success') }}</p>
                    @endif

                    <form action="{{ route('warehouse.updatePassword') }}" method="POST">
                        @csrf

                        <div class="relative w-[100px] mx-auto mb-[30px]">
                            <figure
                                class="w-[100px] h-[100px] rounded-full bg-pink-100 text-[15px] text-[--color-primary-600] overflow-hidden border">
                                @if ($user->profile_image && file_exists(public_path($user->profile_image)))
                                    <img src="{{ asset($user->profile_image) }}" alt="avatar"
                                        class="w-full h-full object-cover">
                                @else
                                    <!-- Default Avatar -->
                                    <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-500">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z"
                                                fill="currentColor" />
                                            <path d="M12 14C7.58172 14 4 17.5817 4 22H20C20 17.5817 16.4183 14 12 14Z"
                                                fill="currentColor" />
                                        </svg>
                                    </div>
                                @endif
                            </figure>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-[15px]">
                            {{-- Old Password --}}
                            <div class="form-group m-0 md:mb-[15px]">
                                <label>Old Password</label>
                                <div class="password-input-wrapper">
                                    <input type="password" name="old_password" id="old_password"
                                        class="form-control password-input">
                                    <span class="password-toggle" onclick="togglePassword('old_password', this)">
                                        <svg id="eye_icon_old" width="20" height="20" viewBox="0 0 24 24"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z"
                                                stroke="#6c757d" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <circle cx="12" cy="12" r="3" stroke="#6c757d" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>
                                @error('old_password')
                                    <p class="validation-error text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- New Password --}}
                            <div class="form-group m-0 md:mb-[15px]">
                                <label>New Password</label>
                                <div class="password-input-wrapper">
                                    <input type="password" name="new_password" id="new_password"
                                        class="form-control password-input">
                                    <span class="password-toggle" onclick="togglePassword('new_password', this)">
                                        <svg id="eye_icon_old" width="20" height="20" viewBox="0 0 24 24"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z"
                                                stroke="#6c757d" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <circle cx="12" cy="12" r="3" stroke="#6c757d"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>
                                @error('new_password')
                                    <p class="validation-error text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div class="form-group m-0 md:mb-[15px]">
                                <label>Confirm Password</label>
                                <div class="password-input-wrapper">
                                    <input type="password" name="new_password_confirmation"
                                        id="new_password_confirmation" class="form-control password-input">
                                    <span class="password-toggle"
                                        onclick="togglePassword('new_password_confirmation', this)">
                                        <svg id="eye_icon_old" width="20" height="20" viewBox="0 0 24 24"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z"
                                                stroke="#6c757d" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <circle cx="12" cy="12" r="3" stroke="#6c757d"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-[15px] mt-4 md:mt-[0px]">
                            <button type="submit" class="btn btn-primary w-[100%] md:w-[200px]">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <!-- JavaScript for password toggle -->
    <script>
  function togglePassword(fieldId, iconSpan) {
    const field = document.getElementById(fieldId);
    const circle = iconSpan.querySelector('circle'); // pupil

    if (field.type === "password") {
        field.type = "text";       // Show password
        if (circle) circle.setAttribute('r', '3'); // show pupil → eye open
    } else {
        field.type = "password";   // Hide password
        if (circle) circle.setAttribute('r', '0'); // hide pupil → eye closed
    }
}


    </script>

    <style>
        /* Password input wrapper */
        .password-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        /* Password input field */
        .password-input {
            width: 100%;
            padding-right: 45px !important;
            /* Add space for the icon */
        }

        /* Eye icon positioning */
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
        }

        .password-toggle:hover {
            color: #495057;
        }

        .password-toggle i {
            font-size: 16px;
        }

        /* Ensure proper spacing */
        .form-group {
            margin-bottom: 1rem;
        }
    </style>
@endsection
