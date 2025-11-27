@extends('admin.sub_layout')
@section('title', 'Banner')
@section('sub_content')
    <div class="main-content side-content pt-0">
        <div class="container-fluid">
          <div class="inner-body">
            <div class="page-header d-block">
              <h2 class="main-content-title tx-24 mg-b-5">All Banners</h2>
              <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ url("admin/dashboard") }}">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page">All Banner</li>
              </ol>
            </div>
            <div class="row sidemenu-height">
              <div class="col-lg-12">
                <div class="card custom-card">
                  <div class="card-body">
                    <div class="py-2 text-right add-button">
                      <a class="btn ripple btn-main-primary ml-3 line-height-24" href="{{route('admin.banners.create')}}">Add New</a>
                    </div>
                    
                    <div class="reset-button d-none">
                      <a href="{{url('admin/banners')}}" class="btn ripple btn-secondary btn-icon"
                      data-original-title="Reset" title="Reset"><i class="si si-refresh" aria-hidden="true"></i></a>
                    </div>
                    <div class="table-responsive">
                        <table class="table tabel-striped table-bordered" id="myTable" class="display">
                            <thead>
                              <tr>
                                <th sort = 'id' width="3%">S.NO.</th>
                                <th width="21%">IMAGE</th>
                                <th sort = 'title' width="25%">BANNER TITLE</th>
                                <th sort = 'sub_title' width="22%">	BANNER SUB TITLE</th>
                                <th width="10%" class="text-center">STATUS</th>
                                <th width="20%" class="text-center">ACTION</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach ($data as $item)
                                <tr>
                                  <td>{{$loop->iteration}}</td>
                                  <td>  
                                      <div class='avatar avatar-xs'><img src="{{$item->banner_image}}" /></div>
                                  </td>
                                  <td>{{$item->title}}</td>
                                  <td>{{$item->sub_title}}</td>
                                  <td class="text-center">
                                    <a onclick="if (!confirm('Are you want to sure ?')) { event.preventDefault(); }" class="btn ripple btn-sm {{config('global.status-class.'.$item->status)}}" href="{{ route('admin.banners.status', base64_encode($item->id)) }}">{{config('global.status.'.$item->status)}} </a>
                                  </td>
                                  <td>
                                    <div class="btn-icon-list justify-content-center"> 
                                      <a href="{{route('admin.banners.edit',base64_encode($item->id))}}" class="btn ripple btn-success btn-icon"> 
                                        <i class="si si-pencil" aria-hidden="true" data-original-title="Edit" title="Edit"></i> 
                                      </a>
                                      <a class="btn ripple btn-primary btn-icon" href="{{route('admin.banners.show',base64_encode($item->id))}}"> 
                                        <i class="si si-eye" aria-hidden="true" data-original-title="View" title="View"></i>
                                      </a>
                                      <a href="#" onclick="event.preventDefault(); if (confirm('Are you want to sure ?')) { document.getElementById('delete-form-{{ $item->id }}').submit(); }" class="btn ripple btn-secondary btn-icon">
                                        <i class="si si-trash" aria-hidden="true"></i>
                                      </a>
                                      {!! Form::open(array('route' => ['admin.banners.update', base64_encode($item->id)],'method' => 'DELETE','id'=>"delete-form-$item->id")) !!}
                                      {!! Form::close() !!}
                                    </div>
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                        </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>

@endsection