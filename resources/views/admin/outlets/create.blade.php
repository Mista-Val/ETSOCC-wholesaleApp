@extends('admin.sub_layout')
@section('title', 'Add Outlet')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">

            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Add Outlet</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url("admin/dashboard") }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.outlets.index') }}">Outlets</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Outlet</li>
                </ol>
            </div>

            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">  
                        <div class="card-body">

                            {!! Form::open(['route' => 'admin.outlets.store', 'method' => 'POST']) !!}

                            <div class="row">
                                {{-- Outlet Name --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('name', 'Outlet Name') !!} <span class="text-danger">*</span>
                                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => 'Enter Outlet Name']) !!}
                                    <div class="text-danger">{{ $errors->first('name') }}</div>
                                </div>

                                {{-- Status --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('status', 'Status') !!} <span class="text-danger">*</span>
                                    <div class="select-down-arrow">
                                    {!! Form::select('status', [1 => 'Active', 0 => 'Inactive'], old('status', 1), ['class' => 'form-control']) !!}
                                    <div class="text-danger">{{ $errors->first('status') }}</div>
                                </div>
                                </div>
                            </div>

                            {{-- Row 2: Address & Description --}}
                            <div class="row">
                                {{-- Address --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('address', 'Address') !!} <span class="text-danger">*</span>
                                    {!! Form::textarea('address', old('address'), ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Enter Address']) !!}
                                    <div class="text-danger">{{ $errors->first('address') }}</div>
                                </div>

                                {{-- Description --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('description', 'Description') !!}
                                    {!! Form::textarea('description', old('description'), ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Enter Description']) !!}
                                    <div class="text-danger">{{ $errors->first('description') }}</div>
                                </div>

                                {{-- Outlet Manager --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('user_id', 'Outlet Manager') !!} <span class="text-danger">*</span>
                                    <div class="select-down-arrow">
                                        {!! Form::select('user_id', 
                                            $outletManagers->pluck('name', 'id'), 
                                            old('user_id'), 
                                            ['class' => 'form-control', 'placeholder' => 'Select Manager']) 
                                        !!}
                                        <div class="text-danger">{{ $errors->first('user_id') }}</div>
                                    </div>
                                </div>



                            </div>

                            {{-- Submit Buttons --}}
                            <div class="form-actions mt-3">
                                <button class="btn btn-primary" type="submit">Submit</button>
                                <a class="btn btn-secondary ml-3" href="{{ url()->current() }}">Reset</a>
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
