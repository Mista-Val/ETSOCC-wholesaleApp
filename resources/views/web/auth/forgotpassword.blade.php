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
            Forgot Password
         </h2>
         <p class="body-14 text-center text-gray-500">
            Enter your email address, and we will send you an OTP to reset your password
         </p>
         <!-- Form -->
         <form action="{{ route('send-otp') }}" method="POST" class="mt-6">
            @csrf
            <input type="hidden" name="role" value="{{ request('role') }}">
            <!-- Email -->
            <div class="form-group">
               <label for="email">
               Email ID
               </label>
               <input id="email" type="email" name="email" placeholder="Enter email ID" class="form-control" />
                @error('email')
                  <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
               @enderror
            </div>
 
            <!-- Button -->
            <input type="submit" value="Submit" class="btn btn-primary w-full" />
         </form>

      </div>
   </div>
</div>
@endsection
