@extends('admin.sub_layout')
@section('title', 'Cms page')
@section('sub_content')

   <div class="main-content side-content pt-0">
      <div class="container-fluid">
         <div class="inner-body">
            <div class="page-header d-block">
               <h2 class="main-content-title tx-24 mg-b-5">Cms Detail</h2>
               <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="{{ url("admin/cms-page") }}">Cms</a></li>
                     <li class="breadcrumb-item active" aria-current="page">Cms Detail</li>
               </ol>
            </div>
            <div class="row sidemenu-height">
               <div class="col-lg-12">
                  <div class="card custom-card">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-lg-12">
                              <table id="simple-table" class="table  table-bordered table-hover">
                                 <colgroup>
                                    <col width="20%">
                                    <col width="80%">
                                 </colgroup>
                                 <tbody>
                                    <tr>
                                       <td class="padding">Image</td>
                                       <td>        
                                           <div class='avatar avatar-xl'><img src="{{$data->image}}" /></div>
                                       </td>
                                    </tr>
                                    <tr>
                                       <th style="vertical-align:top">Title</th>
                                       <td>{{$data->title}}</td>
                                    </tr>
                                    <tr>
                                       <th style="vertical-align:top">Description</th>
                                       <td><div class=" span8">{{$data->content}}</div></td>
                                    </tr>
                                    <tr>
                                       <th style="vertical-align:top">Meta Description</th>
                                       <td>{{ $data->meta_description }}</td>
                                    </tr>
                                    <tr>
                                       <th style="vertical-align:top">Meta Keywords</th>
                                       <td>{{ $data->meta_keywords }}</td>
                                    </tr>
                                    <tr>
                                       <th>Slug</th>
                                       <td>{{ $data->slug }}</td>
                                    </tr>
                                    <tr>
                                       <th>Status</th>
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
      </div>
   </div>

@endsection

