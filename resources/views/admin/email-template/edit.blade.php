@extends('admin.sub_layout')
@section('title', 'Email Template')
@section('sub_content')
 
<div class="main-content side-content pt-0">
  <div class="container-fluid">
      <div class="inner-body">
          <div class="page-header d-block">
            <h2 class="main-content-title tx-24 mg-b-5">Edit Email Template</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Email Templates</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Template</li>
            </ol>
          </div>
          <div class="row sidemenu-height">
              <div class="col-lg-12">
                  <div class="card custom-card">
                      <div class="card-body">
                                              
                          {!! Form::open(array('route' => ['admin.email-templates.update', base64_encode($data->id)],'method' => 'PUT')) !!}

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
                              {!! Form::label('title', 'Subject',['class' => 'col-sm-2 control-label no-padding-right']) !!}
                                <div class="col-sm-5">
                                     {!! Form::text('subject', $data->subject, ['class' => 'form-control' ,'placeholder' => 'Enter Subject', 'required'=>"required"]) !!}
                                  <div class="text-danger">
                                      {{ $errors->first('subject') }}
                                  </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                {!! Form::label('placeholders', 'Dynamic Options', ['class' => 'col-sm-2 control-label no-padding-right']) !!}
                                <div class="col-sm-5">
                                    @php
                                        $placeholders = explode(',', $data->options ?? '');
                                    @endphp

                                    @foreach($placeholders as $placeholder)
                                        @if(trim($placeholder) !== '')
                                            @php
                                                $tag = '{' . '{' . trim($placeholder) . '}' . '}';
                                            @endphp
                                            <span class="badge badge-pill badge-primary-light copy-placeholder p-2 cp"  title="Click to copy">{{ $tag }}</span>
                                            <!-- <span class="copy-placeholder" style="cursor:pointer;" title="Click to copy">
                                                {{ $tag }}
                                            </span> -->
                                            &nbsp;
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group row">
                                {!! Form::label('title','Mail Body',['class' => 'col-sm-2 control-label no-padding-right']) !!}
                                <div class="col-sm-5">
                                    {!! Form::textarea('content',$data->content, ['class' => 'form-control text_editor' ,'placeholder' => 'Enter Mail Body','id'=>'editor']) !!}
                                    <div class="text-danger" id="error_editor1">
                                      {{ $errors->first('content') }}
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
                                  {!! Form::token() !!}
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

<script>

$(document).ready(function(){
  $(".copy-placeholder").click(function(){
    const text = $(this).text();
    navigator.clipboard.writeText(text)
            .then(() => {
                 Swal.fire({
            title: 'Copied!',
            icon: 'success',
            toast:true,
            position:'top-end',
            showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
          })
            })
            .catch(() => {
                // alert('Failed to copy text.');
            });
  })
})
</script>
@endsection