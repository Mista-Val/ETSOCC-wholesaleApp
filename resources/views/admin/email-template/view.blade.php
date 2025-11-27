@extends('admin.sub_layout')
@section('title', 'Email Template')
@section('sub_content')

<div class="main-content side-content pt-0">
  <div class="container-fluid">
    <div class="inner-body">
      <div class="page-header d-block">
        <h2 class="main-content-title tx-24 mg-b-5">Email Template</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Email Template</a></li>
            <li class="breadcrumb-item active" aria-current="page">Template</li>
        </ol>
      </div>
      <div class="row sidemenu-height">
        <div class="col-lg-12">
          <div class="card custom-card">
            <div class="card-body">
              <table id="simple-table" class="table  table-bordered table-hover">
                <colgroup>
                  <col width="20%">
                  <col width="80%">
                </colgroup>
                <tbody>
                  <tr>
                    <td>Title </td>
                    <td>{{isset($data->title)?$data->title:''}}</td>
                  </tr>
                  <tr>
                    <td>Subject</td>
                    <td>{{isset($data->subject) && $data->subject !=''?$data->subject:''}}</td>
                  </tr>
                  <tr>
                    <td>Content</td>
                    <td><div class=" span8">{!! (isset($data->content)?$data->content:'') !!}</div></td>
                  </tr>
                  <tr>
                    <td>Status</td>
                    <td>{{config('global.status.'.$data->status)}} </td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection