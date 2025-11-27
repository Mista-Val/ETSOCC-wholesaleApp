@extends('admin.sub_layout')
@section('title', 'Banner')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Add Banner</h2>
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url("admin/dashboard") }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ url("admin/banners") }}">Banners</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Banner</li>
                </ol>
            </div>
            <div class="row sidemenu-height">
                <div class="col-md-12">
                    <div class="card custom-card">
                        <div class="card-body">
                           
                            {!! Form::open(array('route' => ['admin.banners.update',base64_encode($data->id)],'method' => 'PUT','enctype'=>'multipart/form-data')) !!}
                                <div class="form-group row">
                                    {!! Form::label('title', 'Title',['class' => 'col-sm-2 control-label no-padding-right']) !!}
                                    <div class="col-sm-5">
                                    {!! Form::text('title', $data->title, ['class' => 'form-control' ,'placeholder' => 'Enter Title', 'required'=>"required"]) !!}
                                    <div class="text-danger">
                                        {{ $errors->first('title') }}
                                    </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    {!! Form::label('title', 'Sub Title',['class' => 'col-sm-2 control-label no-padding-right']) !!}
                                    <div class="col-sm-5">
                                    {!! Form::text('sub_title', $data->sub_title, ['class' => 'form-control' ,'placeholder' => 'Enter Sub Title', 'required'=>"required"]) !!}
                                    <div class="text-danger">
                                        {{ $errors->first('sub_title') }}
                                    </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    {!! Form::label('title', 'Banner Image',['class' => 'col-sm-2 control-label no-padding-right']) !!}
                                    <div class="col-sm-5">
                                    <div class="input-group file-browser">
                                        {!! Form::text('file',null, ['class' => 'form-control border-right-0 browse-file',"placeholder"=>"Choose","readonly" => "readonly"]) !!}
                                        <label class="input-group-btn m-0">
                                            <span class="btn btn-primary line-height-24">
                                                Browse 
                                                {!! Form::file('file', ['onchange'=>"loadFile(event,'current_img')",'accept'=>"image/gif, image/jpeg, image/jpg, image/png, image/svg","style"=>"display: none;"]) !!}
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-12 mt-2 p-0">
                                        <img src="@if(!empty($data->banner_image)){{$data->banner_image}}@else{{asset('img/logo.png')}}@endif"  id="current_img">
                                    </div>
                                    <div class="text-danger">
                                        {{ $errors->first('file') }}
                                    </div>
                                    </div>
                                    
                                </div>
                                <div class="form-group row">
                                    {!! Form::label('title','Status',['class' => 'col-sm-2 control-label no-padding-right']) !!}
                                    <div class="col-sm-5">
                                    {!! Form::select('status', config('global.status'), $data->status,['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-actions row mt-3">
                                    <label  class="col-sm-2 control-label no-padding-right"></label>
                                    <div class="col-md-8 pl-md-3">
                                        <button class="btn btn-primary line-height-24" type="submit">
                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                            Submit
                                        </button>
                                    </div>
                                </div>
                                <div class="hr hr-24"></div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

