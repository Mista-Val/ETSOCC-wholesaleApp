@extends('admin.sub_layout')
@section('title', 'Banner')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Banner Detail</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/banners') }}">Banner</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Banner Detail
                    </li>
                </ol>
            </div>
            <div class="row sidemenu-height">
                <div class="col-md-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <table id="simple-table" class="table  table-bordered table-hover">
                                <colgroup>
                                    <col width="20%">
                                    <col width="80%">
                                 </colgroup>
                                <tbody>
                                    <tr>
                                        <td>Title</td>
                                        <td> {{ $data->title }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Image</td>
                                        <td>        
                                            <div class='avatar avatar-xl'><img src="{{$data->banner_image}}" /></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Sub Title</td>
                                        <td> {{ $data->sub_title }}
                                        </td>
                                    </tr>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>{{config('global.status.'.$data->status)}}</td>
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

