
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
                                <h2 class="text-light">{{globalSetting('title') }}</h2>
                                <div class="clearfix"></div>
                                <img src="{{globalSetting('logo') }}" class="header-brand-img desktop-logo" alt="logo">
                                <h5 class="mt-4 text-white">Reset Your Account Password  </h5>
                                <div class="tx-white-6 tx-13 mb-5 mt-xl-0">Reset Password and Signin to discover and connect with the global community</div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-xl-7 col-xs-12 col-sm-12 login_form ">
                            <div class="container-fluid">
                                <div class="row row-sm" >
                                    <div class="card-body mt-2 mb-2">
                                        <h5 class="text-left mb-2">Verify OTP</h5>
                                        <p class="mb-4 text-muted tx-13 ml-0 text-left">Reset Password and Signin to discover and connect with the global community</p>
                                        {!! Form::open(array('route' => 'admin.verify-otp','method' => 'PUT')) !!}
                                            <div class="mt-4">
                                                {!! Form::label('title','OTP') !!}
                                                {!! Form::text('otp',null,['class' => 'form-control' ,'placeholder' => 'OTP', 'required'=>"required",'autocomplete'=> 'off','id' => 'otp']) !!}
                                            </div>
                                            <div class="flex items-center justify-end mt-4">
                                                {!! Form::submit('Verify OTP', ['class' => 'btn ripple btn-main-primary btn-block']) !!}
                                            </div>
                                        {!! Form::close() !!}
                                        <div class="d-flex justify-content-between mt-5">
                                            <div><a href="{{ route('admin.login') }}">Login</a></div>
                                            <div><a href="{{ route('admin.admin-resend-otp') }}">Resend OTP</a></div>
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
    
@endsection
