@extends('admin.sub_layout')
@section('title', 'Testimonial')
@section('sub_content')

<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
                <h2 class="main-content-title tx-24 mg-b-5">Testimonial Detail</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/testimonials') }}">Testimonials</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Testimonial Detail
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
                                        <td>Profile Image</td>
                                        <td>        
                                            <div class='avatar avatar-xl'><img src="{{$data->profile_image}}" /></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Name</td>
                                        <td> {{ $data->name }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Location</td>
                                        <td> {{ $data->location }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Feedback</td>
                                        <td> {!! $data->feedback !!}
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

