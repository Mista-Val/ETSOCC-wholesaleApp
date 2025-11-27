@extends('admin.layout')
@section('title', 'Home Page')
@section('content')

<div class="page main-signin-wrapper">
  <div class="row signpages">
    <div class="col-md-12">
      <div class="card">
        <div class="row row-sm">
          <div class="col-lg-6 col-xl-5 d-none d-lg-block text-center bg-primary details">
            <div class="pt-4 p-2 login-logo-container">
              <div class="clearfix"></div>
              <img src="{{ globalSetting('logo') }}" class="header-brand-img desktop-logo" alt="logo" width="180px">
              <h5 class="mt-4 text-white">Sign In To Your Account</h5>
            </div>
          </div>

          <div class="col-lg-6 col-xl-7 col-xs-12 col-sm-12 login_form">
            <div class="container-fluid">
              <div class="row row-sm">
                <div class="card-body mt-2 mb-2">

                  {{-- ✅ Flash Error Message --}}
                  @if (session('error'))
                      <div class="alert alert-danger alert-dismissible fade show" role="alert">
                          {{ session('error') }}
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                      </div>
                  @endif

                  <img src="{{ asset('theme_assets/img/brand/logo.png') }}" class="d-lg-none header-brand-img text-left float-left mb-4" alt="logo">

                  <div class="clearfix"></div>
                  <p class="mb-4 text-muted tx-13 ml-0 text-left">
                    Sign in to create, discover and connect with the global community
                  </p>

                  {!! Form::open(['route' => 'admin.do-login', 'method' => 'POST']) !!}
                    @csrf

                    {{-- Email Field --}}
                    {!! Form::label('email', 'Email address') !!}
                    {!! Form::text('email', old('email', $email ?? ''), [
                      'class' => 'form-control' . ($errors->has('emails') ? ' is-invalid' : ''),
                      'placeholder' => 'Enter email',
                     
                    ]) !!}
                    @error('email')
                      <div class="text-danger">{{ $message }}</div>
                    @enderror

                    {{-- Password Field --}}
                    <div class="mt-4">
                      {!! Form::label('password', 'Password') !!}
                      <div class="position-relative">
                        {!! Form::password('password', [
                          'class' => 'form-control' . ($errors->has('passwords') ? ' is-invalid' : ''),
                          'placeholder' => 'Enter Password',
                          
                          'autocomplete' => 'off',
                          'id' => 'password'
                        ]) !!}
                        <span class="show-password" onClick="showPassword()">
                          <i class="far fa-eye-slash"></i>
                        </span>
                      </div>
                      @error('password')
                        <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>

                    {{-- Remember Me & Forgot Password --}}
                    <div class="terms-box d-flex align-items-center justify-content-between mt-3">
                      <label class="custom-checkbox">
                        <input name="remember" type="checkbox" id="Signed" {{ old('remember') ? 'checked' : '' }}>
                        <p>Remember me</p>
                      </label>
                      <a href="{{ route('admin.password.request') }}" class="forget-password">Forgot password?</a>
                    </div>

                    {{-- Submit --}}
                    <div class="flex items-center justify-end mt-4">
                      {!! Form::submit('Sign In', ['class' => 'btn ripple btn-main-primary btn-block']) !!}
                    </div>

                  {!! Form::close() !!}

                  <div class="text-left mt-5 ml-0">
                    <div class="mb-1"></div>
                  </div>

                </div> <!-- /.card-body -->
              </div> <!-- /.row -->
            </div> <!-- /.container-fluid -->
          </div> <!-- /.login_form -->
        </div> <!-- /.row -->
      </div> <!-- /.card -->
    </div>
  </div>
</div>

{{-- Styles --}}
<style>
  input[type="checkbox"] {
    accent-color: #1d212f;
  }

  input[type="checkbox"]:checked {
    background-color: #1d212f;
  }

  .form-control.is-invalid {
    border-color: #dc3545 !important;
  }

  .show-password {
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    cursor: pointer;
    color: #999;
  }
</style>

{{-- Scripts --}}
<script>
  function showPassword() {
    const passwordField = $('#password');
    const icon = $('.show-password i');
    const isPassword = passwordField.attr('type') === 'password';

    passwordField.attr('type', isPassword ? 'text' : 'password');
    icon.toggleClass('fa-eye fa-eye-slash');
  }

  // Optional: auto-hide alerts after 4 seconds
  setTimeout(() => {
    $(".alert").fadeOut("slow");
  }, 4000);
</script>

@endsection
