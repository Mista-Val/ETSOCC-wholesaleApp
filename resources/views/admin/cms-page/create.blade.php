@extends('admin.sub_layout')
@section('title', 'Cms page')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">All Page</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/cms-page') }}">Cms</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add</li>
                </ol>
            </div>
            <div class="row sidemenu-height">
                <div class="col-md-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            
                            {!! Form::open(array('route' => 'admin.cms-page.store','method' => 'POST','enctype'=>'multipart/form-data')) !!}
                                <div class="form-group row">
                                    {!! Form::label('title', 'Title',['class' => 'col-sm-2 control-label no-padding-right']) !!}
                                    <div class="col-sm-5">
                                        {!! Form::text('title', null, ['class' => 'form-control' ,'placeholder' => 'Enter Title', 'required'=>"required"]) !!}
                                        <div class="text-danger">
                                            {{ $errors->first('title') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    {!! Form::label('title', 'Meta Description',['class' => 'col-sm-2 control-label no-padding-right']) !!}
                                    <div class="col-sm-5">
                                        {!! Form::textarea('meta_description',null, ['class' => 'form-control text_editor' ,'placeholder' => 'Enter Meta Description','id'=>'editor1']) !!}
                                        <div class="text-danger">
                                            {{ $errors->first('meta_description') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    {!! Form::label('title', 'Meta Keywords',['class' => 'col-sm-2 control-label no-padding-right']) !!}
                                    <div class="col-sm-5">
                                        {!! Form::textarea('meta_keywords',null, ['class' => 'form-control text_editor' ,'placeholder' => 'Enter Meta Keywords','id'=>'editor1']) !!}
                                        <div class="text-danger">
                                        {{ $errors->first('meta_keywords') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    {!! Form::label('title', 'Image',['class' => 'col-sm-2 control-label no-padding-right']) !!}
                                    <div class="col-sm-5">
                                        <div class="input-group file-browser">
                                            {!! Form::text('image',null, ['class' => 'form-control border-right-0 browse-file',"placeholder"=>"Choose","readonly" => "readonly"]) !!}
                                            <label class="input-group-btn m-0">
                                                <span class="btn btn-primary line-height-24">
                                                    Browse 
                                                    {!! Form::file('image', ['onchange'=>"loadFile(event,'current_img')",'accept'=>"image/gif, image/jpeg, image/jpg, image/png, image/svg","style"=>"display: none;"]) !!}
                                                </span>
                                            </label>
                                        </div>
                                        <div class="text-danger">
                                            {{ $errors->first('image') }}
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <img src="{{asset('img/logo.png')}}" class='d-none' width="85" height="85" id="current_img" style="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    {!! Form::label('title', 'Content',['class' => 'col-sm-2 control-label no-padding-right']) !!}
                                    <div class="col-sm-5">
                                        {!! Form::textarea('content',null, ['class' => 'form-control text_editor' ,'placeholder' => 'Enter Content','id'=>'editor']) !!}
                                        <div class="text-danger">
                                            {{ $errors->first('content') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    {!! Form::label('title','Status',['class' => 'col-sm-2 control-label no-padding-right']) !!}
                                    <div class="col-sm-5">
                                    {!! Form::select('status', config('global.status'), 'Select Staus',['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-actions row mt-3">
                                    <label  class="col-sm-2 control-label no-padding-right"></label>
                                    <div class="col-md-8 pl-md-3">
                                        <button class="btn btn-primary line-height-24" type="submit">
                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                            Submit
                                        </button>

                                        <a class="btn btn-secondary line-height-24" href="{{ url()->current() }}">
                                            <i class="ace-icon fa fa-undo bigger-110"></i>
                                            Reset
                                        </a>
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

