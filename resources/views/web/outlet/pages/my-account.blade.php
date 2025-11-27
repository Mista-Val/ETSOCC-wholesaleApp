@extends('web.auth.app')

@section('content')
@include('web.outlet.shared.header')

<main class="dashboard-screen-bg relative">
    <section class="dashboard-title-section bg-white border-b border-gry-50 bg-white">
        <div class="container-fluid">
            <div class="dashboard-inner flex items-center justify-between gap-[10px] flex-wrap py-[10px]">
                <h1 class="h6 text-gry-800">My Account</h1>
                <div class="breadcrumb flex items-center gap-[10px] flex-wrap">
                    {{-- @if($role == 'warehouse-manager')
                        <a href="{{ route('warehouse.dashboard') }}" class="text-gry-600 hover:underline">
                          <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                                            d="M8.39173 2.34954L2.61673 6.97453C1.96673 7.4912 1.55006 8.5829 1.69172 9.39956L2.80006 16.0329C3.00006 17.2162 4.13339 18.1745 5.33339 18.1745H14.6667C15.8584 18.1745 17.0001 17.2079 17.2001 16.0329L18.3084 9.39956C18.4417 8.5829 18.0251 7.4912 17.3834 6.97453L11.6084 2.35789C10.7167 1.64122 9.27506 1.64121 8.39173 2.34954Z"
                                            stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M10.0001 12.9167C11.1507 12.9167 12.0834 11.9839 12.0834 10.8333C12.0834 9.68274 11.1507 8.75 10.0001 8.75C8.84949 8.75 7.91675 9.68274 7.91675 10.8333C7.91675 11.9839 8.84949 12.9167 10.0001 12.9167Z"
                                            stroke="var(--color-gry-800)" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                        </a> --}}
                    @if($role == 'outlet-manager')
                        <a href="{{ route('outlet.outlet-dashboard') }}" class="text-gry-600 hover:underline">
                           <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    @endif

                    <span class="text-gry-300">/</span>
                    <span class="body-14 text-gry-800 bold">My Account</span>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-content py-[15px] md:py-[30px] flex-1">
        <div class="container">
            <div class="white-box p-[15px] md:p-[30px]">
                <form action="{{ route('outlet.updateAccount') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-[30px]">
                        <div class="relative w-[100px] mx-auto">
                            <figure class="w-[100px] h-[100px] rounded-full bg-pink-100 text-[15px] text-[--color-primary-600] overflow-hidden border">
                                @if($user->profile_image && file_exists(public_path($user->profile_image)))
                                    <img id="profile-preview" src="{{ asset($user->profile_image) }}" alt="avatar" class="w-full h-full object-cover">
                                @else
                                    <!-- Default Avatar -->
                                    <div id="default-avatar" class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-500">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="currentColor"/>
                                            <path d="M12 14C7.58172 14 4 17.5817 4 22H20C20 17.5817 16.4183 14 12 14Z" fill="currentColor"/>
                                        </svg>
                                    </div>
                                    <img id="profile-preview" src="" alt="avatar" class="w-full h-full object-cover hidden">
                                @endif
                            </figure>
                            <button type="button" class="absolute bottom-0 right-0 w-[42px] h-[42px] bg-primary-600 rounded-full flex items-center justify-center text-[24px] text-[--white] z-10 overflow-hidden cursor-pointer">
                                <input type="file" id="profile-input" name="profile_image" class="opacity-0 bg-none absolute top-0 left-0 w-full h-full cursor-pointer" accept="image/*">
                                <i class="iconsax" icon-name="edit-2"></i>
                            </button>
                        </div> 
                        @if ($errors->has('profile_image'))
                            <div class="text-red-500 text-center mt-2">{{ $errors->first('profile_image') }}</div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-[15px]">
                        <div class="form-group m-0 md:mb-[15px]">
                            <label>Name</label>
                            <input type="text" name="fullname" class="form-control" value="{{ old('fullname', $user ? ucwords($user->first_name . ' ' . $user->last_name) : '') }}" readonly>
                            @if ($errors->has('fullname'))
                                <div class="text-danger text-sm mt-1">{{ $errors->first('fullname') }}</div>
                            @endif
                        </div>

                        <div class="form-group m-0 md:mb-[15px]">
                            <label>Phone</label>
                            <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $user->mobile) }}">
                            @if ($errors->has('mobile'))
                                <div class="text-red-500">{{ $errors->first('mobile') }}</div>
                            @endif
                        </div>

                        <div class="form-group m-0 md:mb-[15px]">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}"readonly>
                            @if ($errors->has('email'))
                                <div class="text-red-500">{{ $errors->first('email') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-[15px] mt-4 md:mt-[0px]">
                        <button type="submit" class="btn btn-primary w-[100%] md:w-[200px]">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileInput = document.getElementById('profile-input');
    const profilePreview = document.getElementById('profile-preview');
    const defaultAvatar = document.getElementById('default-avatar');

    profileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Show the selected image
                profilePreview.src = e.target.result;
                profilePreview.classList.remove('hidden');
                
                // Hide default avatar if it exists
                if (defaultAvatar) {
                    defaultAvatar.style.display = 'none';
                }
            };
            
            reader.readAsDataURL(file);
        }
    });
});
</script>

@endsection