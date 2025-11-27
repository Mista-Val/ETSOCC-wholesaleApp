@extends('admin.sub_layout')
@section('title', 'Profile')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5"> Change Password </h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ url('admin/dashboard') }}">{{ 'Dashboard' }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ "Change Password" }}</li>
                </ol>
            </div>
            <div class="row sidemenu-height">
                <div class="col-md-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="row square">
                                <div class="col-lg-12 col-md-12">
                                    <div class="card custom-card main-content-body-profile">
                                        <div id="ContactInfo" class="tab-pane">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="card-box">
                                                        {!! Form::open(['route' => 'admin.profile.updatePassword', 'method' => 'PUT', 'autocomplete' => 'off']) !!}

                                                        <!-- Old Password -->
                                                        <div class="form-group row">
                                                            {!! Form::label('old_password', 'Old Password', ['class' => 'col-md-2 col-form-label']) !!}
                                                            <div class="col-md-5">
                                                                <div class="password-input-wrapper">
                                                                    {!! Form::password('old_password', [
                                                                        'class' => 'form-control password-input',
                                                                        'placeholder' => 'Enter Old Password',
                                                                        'required' => 'required',
                                                                        'autocomplete' => 'off',
                                                                        'id' => 'old_password'
                                                                    ]) !!}
                                                                    <span class="password-toggle" onclick="togglePassword('old_password', this)">
                                                                        <i class="far fa-eye-slash"></i>
                                                                    </span>
                                                                </div>
                                                                <div class="text-danger">
                                                                    {{ $errors->first('old_password') }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- New Password -->
                                                        <div class="form-group row">
                                                            {!! Form::label('password', 'New Password', ['class' => 'col-md-2 col-form-label']) !!}
                                                            <div class="col-md-5">
                                                                <div class="password-input-wrapper">
                                                                    {!! Form::password('password', [
                                                                        'class' => 'form-control password-input',
                                                                        'placeholder' => 'Enter New Password',
                                                                        'required' => 'required',
                                                                        'autocomplete' => 'off',
                                                                        'id' => 'password'
                                                                    ]) !!}
                                                                    <span class="password-toggle" onclick="togglePassword('password', this)">
                                                                        <i class="far fa-eye-slash"></i>
                                                                    </span>
                                                                </div>
                                                                <div class="text-danger">
                                                                    {{ $errors->first('password') }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Confirm Password -->
                                                        <div class="form-group row">
                                                            {!! Form::label('password_confirmation', 'Confirm Password', ['class' => 'col-md-2 col-form-label']) !!}
                                                            <div class="col-md-5">
                                                                <div class="password-input-wrapper">
                                                                    {!! Form::password('password_confirmation', [
                                                                        'class' => 'form-control password-input',
                                                                        'placeholder' => 'Enter Confirm Password',
                                                                        'required' => 'required',
                                                                        'autocomplete' => 'off',
                                                                        'id' => 'password_confirmation'
                                                                    ]) !!}
                                                                    <span class="password-toggle" onclick="togglePassword('password_confirmation', this)">
                                                                        <i class="far fa-eye-slash"></i>
                                                                    </span>
                                                                </div>
                                                                <div class="text-danger">
                                                                    {{ $errors->first('password_confirmation') }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-actions row mt-3">
                                                            <label class="col-sm-2 control-label no-padding-right"></label>
                                                            <div class="col-md-8 pl-md-3">
                                                                {!! Form::submit('Update', ['class' => 'btn btn-primary line-height-24']) !!}
                                                            </div>
                                                        </div>

                                                        {!! Form::close() !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div> <!-- end tab-pane -->
                                    </div> <!-- end main-content-body-profile -->
                                </div> <!-- end col -->
                            </div> <!-- end row -->
                        </div> <!-- end card-body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div> <!-- end inner-body -->
    </div> <!-- end container-fluid -->
</div> <!-- end main-content -->

<!-- JS for toggling password visibility -->
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

/* Ensure proper positioning */
.form-group {
    margin-bottom: 1rem;
}
</style>

@endsection