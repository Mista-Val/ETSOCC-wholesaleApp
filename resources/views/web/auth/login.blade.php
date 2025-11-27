@extends('web.auth.app')

@section('content')
<div class="form-bg h-screen w-screen overflow-auto p-[15px] flex bg-[#F9F9F9]">
    <div class="m-auto bg-white form-box max-w-[450px] w-full rounded-[20px] p-[15px] md:p-[30px] flex flex-col gap-[20px] md:gap-[30px]">
        <div class="form-logo-box flex justify-center items-center">
            <a href="javascript:void(0)" class="max-w-[120px]">
                <img src="{{ asset('web/images/logo.svg') }}" alt="logo" />
            </a>
        </div>

        <div class="form-content">
            <h2 class="h5 bold text-center text-gray-900 mb-[5px]">
                Log in to your account
            </h2>
            <p class="body-14 text-center text-gray-500">
                Please enter your information to login
            </p>

            {{-- Show login error at the top --}}
            @if ($errors->has('login'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <p class="text-sm">{{ $errors->first('login') }}</p>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="mt-6">
                @csrf
                <input type="hidden" name="role" value="{{ $role }}">

                <div class="form-group">
                    <label for="email">Email ID</label>
                    <input id="email" type="email" name="email" placeholder="Enter email ID"
                        class="form-control" value="{{ old('email') }}" />
                    @error('email')
                        <p class="validation-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" placeholder="Enter your password"
                            class="form-control !pr-12" />
                        <button type="button" 
                            onclick="togglePassword()" 
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none z-10">
                            <!-- Eye Slash Icon (Password Hidden - Default) -->
                            <svg id="eyeIconHide" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                            <!-- Eye Icon (Password Visible) -->
                            <svg id="eyeIconShow" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="validation-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-full">Login</button>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('forgot-password', ['role' => $role]) }}"
                   class="body-14 semibold text-secondary-500 hover:underline">
                    Forgot Password?
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIconShow = document.getElementById('eyeIconShow');
    const eyeIconHide = document.getElementById('eyeIconHide');
    
    if (passwordInput.type === 'password') {
        // Password VISIBLE karo
        passwordInput.type = 'text';
        eyeIconHide.classList.add('hidden');  // Slash icon hide
        eyeIconShow.classList.remove('hidden'); // Open eye show
    } else {
        // Password HIDE karo
        passwordInput.type = 'password';
        eyeIconHide.classList.remove('hidden'); // Slash icon show
        eyeIconShow.classList.add('hidden');    // Open eye hide
    }
}

// Role validation
$(document).ready(function(){
    const params = new URLSearchParams(window.location.search);
    const role = params.get('role');
    console.log("role", role);
    if (!role) {
        window.location.href = "/";
    }
});
</script>
@endsection