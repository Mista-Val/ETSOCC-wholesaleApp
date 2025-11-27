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
         <!-- Heading -->
         <h2 class="h5 bold text-center text-gray-900 mb-[5px]">
            OTP Verification
         </h2>
         <p class="body-14 text-center text-gray-500">
            We have sent a verification code to your Email address
         </p>

         <!-- Form -->
         <form action="{{ route('otp.verify.submit') }}" class="mt-6" method="POST">
            @csrf

            <!-- Hidden Email Field -->
            <input type="hidden" name="email" value="{{ $email }}" />
            <!-- OTP Inputs -->
             <div class="form-group">
                <div class="flex-row flex justify-between gap-[15px]">
                   <input type="text" name="otp[]" inputmode="numeric" maxlength="1" placeholder="0"
                      class="form-control text-center !bold otp-input"
                      value="{{ old('otp.0') }}" />
                   <input type="text" name="otp[]" inputmode="numeric" maxlength="1" placeholder="0"
                      class="form-control text-center !bold otp-input"
                      value="{{ old('otp.1') }}" />
                   <input type="text" name="otp[]" inputmode="numeric" maxlength="1" placeholder="0"
                      class="form-control text-center !bold otp-input"
                      value="{{ old('otp.2') }}"  />
                   <input type="text" name="otp[]" inputmode="numeric" maxlength="1" placeholder="0"
                      class="form-control text-center !bold otp-input"
                      value="{{ old('otp.3') }}"  />
                </div>
    
                <!-- Show OTP errors -->
                 @error('otp')
                        <p class="validation-error text-center">{{ $message }}</p>
                    @enderror
             </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-full">
               Verify
            </button>

            <!-- Resend Link -->
            <p class="body-14 semibold text-color-gry-900 text-center mt-[15px]">
               Didn’t receive OTP?
               <a class="text-secondary-500 ml-[3px]" href="javascript:;">Resend OTP</a>
            </p>
         </form>
      </div>
   </div>
</div>
<script>
initOTPInput('.otp-input');
</script>
@endsection
