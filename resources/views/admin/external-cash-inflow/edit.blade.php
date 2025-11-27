@extends('admin.sub_layout')
@section('title', 'Edit External Cash Inflow')
@section('sub_content')

<div class="main-content side-content pt-0">
  <div class="container-fluid">
    <div class="inner-body">

      <div class="page-header d-block">
        <h2 class="main-content-title tx-24 mg-b-5">Edit External Cash Inflow</h2>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url("admin/dashboard") }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('admin.external-cash-inflow.index') }}">External Cash Inflows</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
      </div>

      <div class="row">
        <div class="col-lg-12">
          <div class="card custom-card">
            <div class="card-body">

              {{-- Edit Form --}}
              {!! Form::model($data, ['route' => ['admin.external-cash-inflow.update', $data->id], 'method' => 'PUT'])
              !!}
              
              {{-- Row 1: Source & Amount --}}
              <div class="row">
                
                {{-- Source --}}
                <div class="col-md-6">
                  <div class="form-group">
                    {!! Form::label('source', 'Source') !!} <span class="text-danger">*</span>
                    {!! Form::text('source', null, ['class' => 'form-control', 'required' => true, 'placeholder' => 'Enter source']) !!}
                    <div class="text-danger">{{ $errors->first('source') }}</div>
                  </div>
                </div>

                {{-- Amount --}}
                <div class="col-md-6">
                  <div class="form-group">
                    {!! Form::label('amount', 'Amount') !!} <span class="text-danger">*</span>
                    {!! Form::number('amount', null, ['class' => 'form-control', 'required' => true, 'step' => '0.01', 'placeholder' => 'Enter Amount']) !!}
                    <div class="text-danger">{{ $errors->first('amount') }}</div>
                  </div>
                </div>

              </div>
              
              {{-- NEW ROW: Assigned Supervisor (Added Here) --}}
              <div class="row">
                <div class="form-group col-md-6">
                  {!! Form::label('supervisor_id', 'Assigned Supervisor') !!} <span class="text-danger">*</span>
                  <div class="select-down-arrow">
                    {{-- Form::select automatically selects the value stored in $data->supervisor_id --}}
                    {!! Form::select('supervisor_id', 
                        $supervisors, 
                        null, 
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
                <div class="col-md-6">
                  <div class="form-group">
                    {!! Form::label('received_date', 'Received Date') !!} <span class="text-danger">*</span>
                    
                    {{-- FIX: Explicitly pass the date formatted as YYYY-MM-DD to correctly populate the date picker --}}
                    @php
                        $receivedDateValue = \Carbon\Carbon::parse($data->received_date)->format('Y-m-d');
                    @endphp
                    
                    {!! Form::date('received_date', $receivedDateValue, ['class' => 'form-control', 'required' => true]) !!}
                    <div class="text-danger">{{ $errors->first('received_date') }}</div>
                  </div>
                </div>

                {{-- Received From --}}
                <div class="col-md-6">
                  <div class="form-group">
                    {!! Form::label('received_from', 'Received From') !!} <span class="text-danger">*</span>
                    {!! Form::text('received_from', null, ['class' => 'form-control', 'required' => true, 'placeholder' => 'Enter Name of Person/Organization']) !!}
                    <div class="text-danger">{{ $errors->first('received_from') }}</div>
                  </div>
                </div>

              </div>
              
              {{-- Row 3: Remarks --}}
              <div class="row">
                {{-- Remarks --}}
                <div class="col-md-12">
                  <div class="form-group">
                    {!! Form::label('remarks', 'Remarks') !!}
                    {!! Form::textarea('remarks', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Enter Remarks (optional)']) !!}
                    <div class="text-danger">{{ $errors->first('remarks') }}</div>
                  </div>
                </div>
              </div>


              <div class="form-actions row mt-3">
                <div class="col-md-12">
                  <button class="btn btn-primary line-height-24" type="submit">
                    <i class="ace-icon fa fa-check bigger-110"></i> Update Inflow
                  </button>

                  <a class="btn btn-secondary line-height-24 ml-3"
                    href="{{ route('admin.external-cash-inflow.index') }}">
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