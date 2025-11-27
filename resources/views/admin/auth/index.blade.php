@extends('admin.sub_layout')
@section('title', 'Profile')
@section('sub_content')
<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5"> Profile </h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">{{ 'Dashboard' }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ "Profile" }}</li>
                </ol>
            </div>
            <div class="row sidemenu-height">
                <div class="col-md-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="row row-sm">
                                <div class="col-lg-12 col-md-12">
                                    <div class="card custom-card main-content-body-profile">
                                        <div class="tab-content">
                                            <div class="main-content-body border-top-0">
                                                
                                                {!! Form::open(array('route' => 'admin.profile.update','method' => 'PUT','enctype'=>'multipart/form-data')) !!}

                                                {{-- Profile Image --}}
                                                {{-- <div class="form-group">
                                                    <div class="row row-sm">
                                                        <div class="col-md-2">
                                                            {!! Form::label('profile_image', 'Profile',['class' => 'form-label']) !!}
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="input-group file-browser">
                                                                {!! Form::text('profile_image_display',null, ['class' => 'form-control border-right-0 browse-file',"placeholder"=>"Choose file","readonly" => "readonly"]) !!}
                                                                <label class="input-group-btn m-0">
                                                                    <span class="btn btn-primary">
                                                                        Browse 
                                                                        {!! Form::file('profile_image', ['onchange'=>"loadFile(event,'current_img')",'accept'=>"image/gif, image/jpeg, image/jpg, image/png, image/svg","style"=>"display: none;",'id'=>'profile_image_input']) !!}
                                                                    </span>
                                                                </label>
                                                            </div>
                                                            
                                                            @error('profile_image')
                                                                <div class="text-danger mt-1">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                            
                                                            <div class="mt-2">
                                                                <img src="{{ asset('uploads/profile/' . $user->profile_image) }}" width="60px" height="60px" id="current_img" alt="Profile Image">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> --}}

                                                {{-- Profile Image --}}
<div class="form-group">
    <div class="row row-sm">
        <div class="col-md-2">
            {!! Form::label('profile_image', 'Profile',['class' => 'form-label']) !!}
        </div>
        <div class="col-md-5">
            <div class="input-group file-browser">
                {!! Form::text('profile_image_display',null, ['class' => 'form-control border-right-0 browse-file ' . ($errors->has('profile_image') ? 'is-invalid' : ''),"placeholder"=>"Choose file","readonly" => "readonly"]) !!}
                <label class="input-group-btn m-0">
                    <span class="btn btn-primary">
                        Browse 
                        {!! Form::file('profile_image', ['onchange'=>"loadFile(event,'current_img')",'accept'=>"image/gif, image/jpeg, image/jpg, image/png, image/svg","style"=>"display: none;",'id'=>'profile_image_input']) !!}
                    </span>
                </label>
            </div>
            
            {{-- Error message immediately after input --}}
            @error('profile_image')
                <div class="text-danger mt-1" style="font-size: 0.875rem;">
                    {{ $message }}
                </div>
            @enderror
            
            <div class="mt-2">
                @if(isset($user->profile_image) && $user->profile_image)
                    <img src="{{ asset('uploads/profile/' . $user->profile_image) }}" width="60px" height="60px" id="current_img" alt="Profile Image" class="img-thumbnail">
                @else
                    <img src="{{ asset('uploads/profile/default.png') }}" width="60px" height="60px" id="current_img" alt="Profile Image" class="img-thumbnail">
                @endif
            </div>
        </div>
    </div>
</div>

                                                {{-- First Name --}}
                                                <div class="form-group">
                                                    <div class="row row-sm">
                                                        <div class="col-md-2">
                                                            {!! Form::label('first_name', 'First Name',['class' => 'form-label']) !!}
                                                        </div>
                                                        <div class="col-md-5">
                                                            {!! Form::text('first_name', $user->first_name, ['class' => 'form-control' ,'placeholder' => 'Enter First Name', 'required'=>"required"]) !!}
                                                            @error('first_name')
                                                                <div class="text-danger mt-1">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Last Name --}}
                                                <div class="form-group">
                                                    <div class="row row-sm">
                                                        <div class="col-md-2">
                                                            {!! Form::label('last_name', 'Last Name',['class' => 'form-label']) !!}
                                                        </div>
                                                        <div class="col-md-5">
                                                            {!! Form::text('last_name', $user->last_name, ['class' => 'form-control' ,'placeholder' => 'Enter Last Name', 'required'=>"required"]) !!}
                                                            @error('last_name')
                                                                <div class="text-danger mt-1">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Mobile --}}
                                                <div class="form-group">
                                                    <div class="row row-sm">
                                                        <div class="col-md-2">
                                                            {!! Form::label('mobile', 'Mobile',['class' => 'form-label']) !!}
                                                        </div>
                                                        <div class="col-md-5">
                                                            {!! Form::text('mobile', $user->mobile, ['class' => 'form-control' ,'placeholder' => 'Enter Mobile', 'required'=>"required"]) !!}
                                                            @error('mobile')
                                                                <div class="text-danger mt-1">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Email (Disabled) --}}
                                                <div class="form-group">
                                                    <div class="row row-sm">
                                                        <div class="col-md-2">
                                                            {!! Form::label('email', 'Email',['class' => 'form-label']) !!}
                                                        </div>
                                                        <div class="col-md-5">
                                                            {!! Form::text('email_display', $user->email, ['class' => 'form-control' ,'placeholder' => 'Enter Email', 'disabled'=>"disabled",'style' => 'cursor: not-allowed; background-color: #e9ecef;']) !!}
                                                            @error('email')
                                                                <div class="text-danger mt-1">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Submit Button --}}
                                                <div class="form-actions row mt-3">
                                                    <label class="col-sm-2 control-label no-padding-right"></label>
                                                    <div class="col-md-8 pl-md-3">
                                                        {!! Form::submit('Submit', ['class' => 'btn btn-primary line-height-24']) !!}
                                                    </div>
                                                </div>

                                                {!! Form::close() !!}
                                               
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
    </div>
</div>

<script>
function loadFile(event, imgId) {
    var output = document.getElementById(imgId);
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
        URL.revokeObjectURL(output.src);
    }
    
    // Update the readonly input with filename
    var fileName = event.target.files[0].name;
    var textInput = event.target.closest('.file-browser').querySelector('.browse-file');
    if(textInput) {
        textInput.value = fileName;
    }
}
</script>

@endsection