@extends('admin.sub_layout')
@section('title', 'Category')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Edit category</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Categories</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit category</li>
                </ol>
            </div>
            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            

                        
                            {!! Form::open(array('route' => ['admin.categories.update', base64_encode($data->id)],'method' => 'PUT')) !!}

                            {!! Form::hidden('id', base64_encode($data->id)) !!}

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