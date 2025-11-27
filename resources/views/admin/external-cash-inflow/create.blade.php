@extends('admin.sub_layout')
@section('title', 'Add External Cash Inflow')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">

            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Add External Cash Inflow</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.external-cash-inflow.index') }}">External Cash Inflows</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Inflow</li>
                </ol>
            </div>

            <div class="row sidemenu-height">
                <div class="col-lg-12">
                    <div class="card custom-card">  
                        <div class="card-body">

                            {!! Form::open(['route' => 'admin.external-cash-inflow.store', 'method' => 'POST']) !!}

                            {{-- Row 1: Source & Amount --}}
                            <div class="row">
                                {{-- Source --}}
                                {{-- <div class="form-group col-md-6">
                                    {!! Form::label('source', 'Source') !!} <span class="text-danger">*</span>
                                    {!! Form::text('source', old('source'), ['class' => 'form-control', 'placeholder' => 'Enter Source']) !!}
                                    <div class="text-danger">{{ $errors->first('source') }}</div>
                                </div> --}}

                                 <div class="form-group col-md-6">
                                <label class="form-label" for="source">Source</label>
                                <select id="source" name="source"
                                    class="form-control">
                                    <option value="">Select Source</option>
                                    <option value="Cash from outlets/warehouse"
                                        {{ old('source') == 'Cash from outlets/warehouse' ? 'selected' : '' }}>
                                        Cash from outlets/warehouse
                                    </option>
                                    <option value="Cash withdraw from bank"
                                        {{ old('source') == 'Cash withdraw from bank' ? 'selected' : '' }}>
                                        Cash withdraw from bank
                                    </option>
                                    <option value="Cash from external source"
                                        {{ old('source') == 'Cash from external source' ? 'selected' : '' }}>
                                        Cash from external source
                                    </option>
                                </select>
                                @error('source')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                                {{-- Amount --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('amount', 'Amount') !!} <span class="text-danger">*</span>
                                    {!! Form::number('amount', old('amount'), ['class' => 'form-control', 'step' => '0.01', 'placeholder' => 'Enter Amount']) !!}
                                    <div class="text-danger">{{ $errors->first('amount') }}</div>
                                </div>
                            </div>
                            
                            {{-- NEW ROW: Assigned Supervisor --}}
                            <div class="row">
                                {{-- Supervisor Select Box --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('supervisor_id', 'Assigned Supervisor') !!} <span class="text-danger">*</span>
                                    <div class="select-down-arrow">
                                        {{-- NOTE: The $supervisors variable must be passed from the controller (User::where('role', 'supervisor')->pluck('name', 'id')) --}}
                                        {!! Form::select('supervisor_id', 
                                            $supervisors, 
                                            old('supervisor_id'), 
                                            ['class' => 'form-control', 'placeholder' => 'Select a Supervisor', 'required' => true]) 
                                        !!}
                                    </div>
                                    <div class="text-danger">{{ $errors->first('supervisor_id') }}</div>
                                </div>
                                
                                {{-- Empty Column to fill the row --}}
                                <div class="col-md-6">
                                    {{-- Intentionally blank --}}
                                </div>
                            </div>

                            {{-- Row 2: Received Date & Received From --}}
                            <div class="row">
                                {{-- Received Date --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('received_date', 'Received Date') !!} <span class="text-danger">*</span>
                                    {!! Form::date('received_date', old('received_date'), ['class' => 'form-control']) !!}
                                    <div class="text-danger">{{ $errors->first('received_date') }}</div>
                                </div>

                                {{-- Received From --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('received_from', 'Received From') !!} <span class="text-danger">*</span>
                                    {!! Form::text('received_from', old('received_from'), ['class' => 'form-control', 'placeholder' => 'Enter Name of Person/Organization']) !!}
                                    <div class="text-danger">{{ $errors->first('received_from') }}</div>
                                </div>
                            </div>

                            {{-- Row 3: Remarks --}}
                            <div class="row">
                                <div class="form-group col-md-12">
                                    {!! Form::label('remarks', 'Remarks') !!}
                                    {!! Form::textarea('remarks', old('remarks'), ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Enter Remarks (optional)']) !!}
                                    <div class="text-danger">{{ $errors->first('remarks') }}</div>
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