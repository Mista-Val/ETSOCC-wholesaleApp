@extends('web.auth.app')
@section('content')
<div class="form-bg h-screen w-screen overflow-auto p-[15px] flex bg-[#F9F9F9]">
   <div
      class="m-auto bg-white form-box max-w-[450px] w-full rounded-[20px] p-[15px] md:p-[30px] flex flex-col gap-[20px] md:gap-[30px]">
      <div class="form-logo-box flex justify-center items-center">
         <a href="javascript:void(0)" class="max-w-[120px]"><img src="{{ asset('web/images/logo.svg') }}" alt="logo" /></a>
      </div>
      <div class="form-content">
         <!-- Heading -->
         <h2 class="h5 bold text-center text-gray-900 mb-[5px]">
            Reset Password
         </h2>
         <p class="body-14 text-center text-gray-500">
            Your new password must be different from previously used passwords
         </p>
         <!-- Form -->
         <form method="POST"  action="{{ route('reset-password.submit') }}" class="mt-6">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}" />

            <div class="form-group">
               <label for="password">
               New Password
               </label>
               <div class="password-input-wrapper">
                  <input type="password" name="password" id="password" placeholder="Enter new password" class="form-control password-input" />
                  <span class="password-toggle" onclick="togglePassword('password', this)">
                     <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z"
                              stroke="#6c757d" stroke-width="1.5" stroke-linecap="round"
                              stroke-linejoin="round" />
                        <circle cx="12" cy="12" r="3" stroke="#6c757d" stroke-width="1.5"
                              stroke-linecap="round" stroke-linejoin="round" />
                     </svg>
                  </span>
               </div>
               @error('password')
                  <span class="text-red-500 text-sm">{{ $message }}</span>
               @enderror
            </div>
            
            <div class="form-group">
               <label for="password_confirmation">
               Confirm Password
               </label>
               <div class="password-input-wrapper">
                  <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Enter confirm password" class="form-control password-input" />
                  <span class="password-toggle" onclick="togglePassword('password_confirmation', this)">
                     <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z"
                              stroke="#6c757d" stroke-width="1.5" stroke-linecap="round"
                              stroke-linejoin="round" />
                        <circle cx="12" cy="12" r="3" stroke="#6c757d" stroke-width="1.5"
                              stroke-linecap="round" stroke-linejoin="round" />
                     </svg>
                  </span>
               </div>
               @error('password_confirmation')
                  <span class="text-red-500 text-sm">{{ $message }}</span>
               @enderror
            </div>
 
            <!-- Button -->
            <button type="submit" class="btn btn-primary w-full">
            Submit
            </button>
         </form>

      </div>
   </div>
</div>

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