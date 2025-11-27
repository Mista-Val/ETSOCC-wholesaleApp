@extends('admin.layout')
@section('title', 'Forgot password')
@section('content')
    
<div class="page main-signin-wrapper">
    <div class="row signpages">
        <div class="col-md-12">
            <div class="card">
                <div class="row row-sm">
                    <div class="col-lg-6 col-xl-5 d-none d-lg-block text-center bg-primary details">
                        <div class="mt-6 pt-4 p-2 pos-absolute">
                            <div class="clearfix"></div>
                            <h2 class="text-light">{{globalSetting('title') }}</h2>
                            <img src="{{globalSetting('logo') }}" class="header-brand-img desktop-logo" alt="logo">
                            <h5 class="mt-4 text-white">Forgot Your Password</h5>
                            <div class="tx-white-6 tx-13 mb-5 mt-xl-0">Reset password and Signin to create, discover and connect with the global community </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xl-7 col-xs-12 col-sm-12 login_form">
                        <div class="container-fluid">
                            <div class="row row-sm" >
                                <div class="card-body mt-2 mb-2">
                                    <img src="{{ asset("theme_assets") }}/img/brand/logo.png" class=" d-lg-none header-brand-img text-left float-left mb-4" alt="logo">
                                    <div class="clearfix"></div>
                                    <h5 class="text-left mb-2">Forgot Your Password</h5>
                                    <p class="mb-4 text-muted tx-13 ml-0 text-left">Reset password and Signin to create, discover and connect with the global community</p>
                                    {!! Form::open(array('route' => 'admin.send-otp','method' => 'POST')) !!}
                                        @csrf
                                        
                                        {!! Form::label('title','Email') !!}
                                        {!! Form::text('email', null, ['class' => 'form-control' ,'placeholder' => 'Enter email', 'required'=>"required"]) !!}
                                        
                                        <div class="flex items-center justify-end mt-4">
                                            {!! Form::submit('Send', ['class' => 'btn ripple btn-main-primary btn-block']) !!}
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

@endsection