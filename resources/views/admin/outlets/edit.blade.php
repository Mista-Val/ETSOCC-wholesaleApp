@extends('admin.sub_layout')
@section('title', 'Edit Outlet')
@section('sub_content')

<div class="main-content side-content pt-0">
  <div class="container-fluid">
    <div class="inner-body">

      <div class="page-header d-block">
        <h2 class="main-content-title tx-24 mg-b-5">Edit Outlet</h2>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url("admin/dashboard") }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('admin.outlets.index') }}">Outlet</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
      </div>

      <div class="row">
        <div class="col-lg-12">
          <div class="card custom-card">
            <div class="card-body">

              {{-- Validation Errors --}}
              @if($errors->any())
                <div class="alert alert-danger">
                  <ul class="mb-0">
                    @foreach($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              {{-- Edit Form --}}

              {!! Form::model($outlet, ['route' => ['admin.outlets.update', $outlet->id], 'method' => 'PUT']) !!}
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    {!! Form::label('name', 'Outlet Name') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'required' => true, 'placeholder' => 'Enter
                    outlet name']) !!}
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    {!! Form::label('status', 'Status') !!} <span class="text-danger">*</span>
                    <div class="select-down-arrow">
                      {!! Form::select('status', [1 => 'Active', 0 => 'Inactive'], null, ['class' => 'form-control'])
                      !!}
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    {!! Form::label('address', 'Address') !!}
                    {!! Form::textarea('address', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Enter
                    address']) !!}
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    {!! Form::label('description', 'Description') !!}
                    {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' =>
                    'Enter description']) !!}
                  </div>
                </div>
              </div>
              <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('user_id', 'Outlet Manager') !!} <span class="text-danger">*</span>
                  <div class="select-down-arrow">
                    {!! Form::select('user_id',
                    $outletManagers->pluck('name', 'id'),
                    old('user_id', $outlet->user_id),
                    ['class' => 'form-control', 'placeholder' => 'Select Manager'])
                    !!}
                    <div class="text-danger">{{ $errors->first('user_id') }}</div>
                  </div>
                </div>
              </div>
              </div>

              <div class="form-actions row mt-3">
                <div class="col-md-12">
                  <button class="btn btn-primary line-height-24" type="submit">
                    <i class="ace-icon fa fa-check bigger-110"></i> Update
                  </button>

                  <a class="btn btn-secondary line-height-24 ml-3"
                    href="{{ route('admin.products.index') }}">
                    <i class="ace-icon fa fa-undo bigger-110"></i> Cancel
                  </a>
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

@endsection