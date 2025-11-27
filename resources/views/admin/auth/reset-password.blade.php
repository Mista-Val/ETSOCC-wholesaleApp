@extends('admin.layout')
@section('title', 'Home Page')
@section('content')

    <div class="page main-signin-wrapper">
        <div class="row signpages">
            <div class="col-md-12">
                <div class="card">
                    <div class="row row-sm">
                        <div class="col-lg-6 col-xl-5 d-none d-lg-block text-center bg-primary details">
                            <div class="mt-5 pt-4 p-2 pos-absolute">
                                <h2 class="text-light">{{ globalSetting('title') }}</h2>
                                <div class="clearfix"></div>
                                  <img src="{{globalSetting('logo') }}" class="header-brand-img desktop-logo" alt="logo"  style="width: 180px !important">
                                <h5 class="mt-4 text-white">Reset Your Account Password </h5>
                                <div class="tx-white-6 tx-13 mb-5 mt-xl-0">Reset Password and Signin to discover and
                                    connect with the global community</div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-xl-7 col-xs-12 col-sm-12 login_form ">
                            <div class="container-fluid">
                                <div class="row row-sm">
                                    <div class="card-body mt-2 mb-2">
                                        <h5 class="text-left mb-2">Reset Password</h5>
                                        <p class="mb-4 text-muted tx-13 ml-0 text-left">Reset Password and Signin to
                                            discover and connect with the global community</p>
                                        {!! Form::open(['route' => 'admin.reset-password-update', 'method' => 'PUT']) !!}
                                        @csrf

                                        <div class="mt-4">
                                            {!! Form::label('password', 'New Password') !!}
                                            <div class="password-input-wrapper">
                                                {!! Form::password('password', [
                                                    'class' => 'form-control password-input' . ($errors->has('password') ? ' is-invalid' : ''),
                                                    'placeholder' => 'Enter new password',
                                                    'required' => 'required',
                                                    'autocomplete' => 'off',
                                                    'id' => 'password',
                                                ]) !!}
                                                <span class="password-toggle" onclick="togglePassword('password', this)">
                                                    <i class="far fa-eye-slash"></i>
                                                </span>
                                            </div>
                                            <div class="text-danger">
                                                {{ $errors->first('password') }}
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            {!! Form::label('confirmPassword', 'Confirm Password') !!}
                                            <div class="password-input-wrapper">
                                                {!! Form::password('confirmPassword', [
                                                    'class' => 'form-control password-input' . ($errors->has('confirmPassword') ? ' is-invalid' : ''),
                                                    'placeholder' => 'Re-enter your password',
                                                    'required' => 'required',
                                                    'autocomplete' => 'off',
                                                    'id' => 'confirmPassword',
                                                ]) !!}
                                                <span class="password-toggle" onclick="togglePassword('confirmPassword', this)">
                                                    <i class="far fa-eye-slash"></i>
                                                </span>
                                            </div>
                                            <div class="text-danger">
                                                {{ $errors->first('confirmPassword') }}
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-end mt-4">
                                            <button type="submit" class="btn ripple btn-main-primary btn-block">
                                                Change Password
                                            </button>
                                        </div>
                                        {!! Form::close() !!}
                                        <div class="text-left mt-5 ml-0">
                                            <div class="mb-1"><a href="{{ route('admin.login') }}">Login</a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- JavaScript -->
<script>
function togglePassword(fieldId, iconSpan) {
    const field = document.getElementById(fieldId);
    const icon = iconSpan.querySelector('i');
    if (field.type === "password") {
        field.type = "text";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        field.type = "password";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
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
    padding-right: 45px !important; /* Add space for the icon */
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

/* Ensure the icon works with Bootstrap validation states */
.is-invalid + .password-toggle {
    color: #dc3545;
}

.is-invalid + .password-toggle:hover {
    color: #a02622;
}
</style>

@endsection