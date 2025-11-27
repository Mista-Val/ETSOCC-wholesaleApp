<div class="main-content side-content pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <div class="page-header d-block">
                <div>
                  <h2 class="main-content-title tx-24 mg-b-5">Global Setting</h2>
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                  </ol>
                </div>
            </div>
            <div class="row sidemenu-height">
                <div class="col-lg-12">   
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="row ">
                                <div class="col-sm-12">
                                    <div class="col-sm-12">
                                        <div class="tabbable">
                                            <ul class="nav nav-tabs" id="myTab">
                                                <li class="{{$tab == 1?'active':''}}" wire:click="setTab(1)">
                                                    <a data-toggle="tab" class="btn btn-outline-light line-height-24 {{$tab == 1?'active':''}}" href="#EmailSettings" >
                                                    General Info
                                                    </a>
                                                </li>
                                                <li class='{{$tab == 2?'active':''}}' wire:click="setTab(2)">
                                                    <a data-toggle="tab" class="btn btn-outline-light line-height-24 {{$tab == 2?'active':''}}" href="#ContactInfo">
                                                    Contact Info
                                                    </a>
                                                </li>
                                                <!-- <li class='{{$tab == 3?'active':''}}' wire:click="setTab(3)">
                                                    <a data-toggle="tab" class="btn btn-outline-light line-height-24 {{$tab == 3?'active':''}}" href="#socialLinks">
                                                    Social Links
                                                    </a>
                                                </li> -->
                                            </ul>
                                            <div class="tab-content py-4 border border-2 border-light" style="margin-top: -2px">
                                                <div id="EmailSettings" class="tab-pane fade tab-pane fade in show {{$tab == 1?'active':''}}">
                            
                                                    @if(isset($data) && count($data)>0)
                                                        @foreach($data as $key => $value)
                            
                                                            @if(isset($value->category_type) && $value->category_type=='G'  )
                                                                @if (isset($value->type) && $value->type  == 'textarea')
                                                                    <div class="row">
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                            {!! Form::label('title',$value->title, ['class' => 'login2 pull-right pull-right-pro']) !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                            <div class="form-group">
                                                                                <textarea  placeholder="Enter {{$value->title}}" class="form-control" maxlength="255" wire:model.defer="{{$value->slug}}" name="{{$value->slug}}">{{$value->value}}</textarea>
                                                                              
                                                                                @if($slugName === $value->slug)
                                                                                    @error(`{{$value->slug}}`)
                                                                                        <p class="text-danger">{{ $message }}
                                                                                        </p>
                                                                                    @enderror
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                            <button class="btn ripple btn-main-primary submit-config line-height-24" wire:click="updateSetting('{{$value->slug}}','{{$value->title}}','{{$value->type}}')">Submit</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @elseif (isset($value->type) && $value->type  == 'select')
                                    
                                                                    <div class="row">
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                            {!! Form::label('author',$value->title, ['class' => 'login2 pull-right pull-right-pro']) !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                            <div class="form-group">
                                                                                {!! Form::select('cv',config('global.status'),$value->value, ['class' => '  form-control',
                                                                                'wire:model.defer' => $value->slug]) !!}
                                                                                @if($slugName === $value->slug)
                                                                                    @error(`{{$value->slug}}`)
                                                                                        <p class="text-danger">{{ $message }}
                                                                                        </p>
                                                                                    @enderror
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                <button class="btn ripple btn-main-primary submit-config line-height-24" wire:click="updateSetting('{{$value->slug}}','{{$value->title}}','{{$value->type}}')">Submit</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                    
                                                                @elseif(isset($value->type) && $value->type =='file')
                                                                    <div class="row">
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                {!! Form::label('author',$value->title, ['class' => 'login2 pull-right pull-right-pro']) !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                            <div class="form-group">
                                                                                <div class='avatar avatar-xs'><img src="{{$value->value}}" /></div>
                                                                                <input type="file" placeholder="{{ucfirst($value->slug)}}" value="{{$value->value}}" wire:model.defer="{{$value->slug}}">
                                                                                @if($slugName === $value->slug)
                                                                                    @error(`{{$value->slug}}`)
                                                                                        <p class="text-danger">{{ $message }}
                                                                                        </p>
                                                                                    @enderror
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                <button class="btn ripple btn-main-primary submit-config line-height-24" wire:click="updateSetting('{{$value->slug}}','{{$value->title}}','{{$value->type}}')">Submit</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="row">
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                {!! Form::label('author',$value->title, ['class' => 'login2 pull-right pull-right-pro']) !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                            <div class="form-group">
                                                                                <input type="text"  placeholder="Enter {{ucfirst($value->title)}}" value="{{$value->value}}"  class="  form-control" maxlength="50"  wire:model.defer="{{$value->slug}}">
                                                                                @if($slugName === $value->slug)
                                                                                    @error(`{{$value->slug}}`)
                                                                                        <p class="text-danger">{{ $message }}
                                                                                        </p>
                                                                                    @enderror
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                <button class="btn ripple btn-main-primary submit-config line-height-24"  wire:click="updateSetting('{{$value->slug}}','{{$value->title}}','{{$value->type}}')">Submit</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <div id="ContactInfo" class="tab-pane fade tab-pane fade in show {{$tab == 2?'active':''}}">
                                                    @if(isset($data) && count($data)>0)
                                                        @foreach($data as $key => $value)
                                                            @if(isset($value->category_type) &&$value->category_type=='A')
                                                                @if (isset($value->type) && $value->type  == 'textarea')
                                                                    <div class="row">
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                {!! Form::label('author',$value->title, ['class' => 'login2 pull-right pull-right-pro']) !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                            <div class="form-group">
                                                                                <textarea id='input-field-{{$value->id}}' placeholder="Enter {{ucfirst($value->slug)}}" class=" maxlength="250" wire:model.defer="{{$value->slug}}">{{$value->value}}</textarea>
                                                                                @if($slugName === $value->slug)
                                                                                    @error(`{{$value->slug}}`)
                                                                                        <p class="text-danger">{{ $message }}
                                                                                        </p>
                                                                                    @enderror
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                <button class="btn ripple btn-main-primary submit-config line-height-24" wire:click="updateSetting('{{$value->slug}}','{{$value->title}}','{{$value->type}}')" >Submit</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @elseif (isset($value->type) && $value->type  == 'select')
                                                                    <div class="row">
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                {!! Form::label('author',$value->title, ['class' => 'login2 pull-right pull-right-pro']) !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                            <div class="form-group">
                                                                                <div class='avatar avatar-xs'><img src="{{$value->value}}" /></div>
                                                                                <input type="file" placeholder="{{ucfirst($value->slug)}}" value="{{$value->value}}" wire:model.defer="{{$value->slug}}">
                                                                                @if($slugName === $value->slug)
                                                                                    @error(`{{$value->slug}}`)
                                                                                        <p class="text-danger">{{ $message }}
                                                                                        </p>
                                                                                    @enderror
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                <button class="btn ripple btn-main-primary submit-config line-height-24" wire:click="updateSetting('{{$value->slug}}','{{$value->title}}','{{$value->type}}')">Submit</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @elseif (isset($value->type) && $value->type  == 'select')
                                                                    <div class="row">
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                {!! Form::label('author',$value->title, ['class' => 'login2 pull-right pull-right-pro']) !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                            <div class="form-group">
                                                                                {!! Form::select('cv', config('global.status'), $value->value, [
                                                                                    'class' => 'form-control',
                                                                                    'wire:model.defer' => $value->slug
                                                                                ]) !!}
                                                                                @if($slugName === $value->slug)
                                                                                    @error(`{{$value->slug}}`)
                                                                                        <p class="text-danger">{{ $message }}
                                                                                        </p>
                                                                                    @enderror
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                <button class="btn ripple btn-main-primary submit-config line-height-24" wire:click="updateSetting('{{$value->slug}}','{{$value->title}}','{{$value->type}}')">Submit</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="row">
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                {!! Form::label('author',$value->title, ['class' => 'login2 pull-right pull-right-pro']) !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                            <div class="form-group">
                                                                            <input type="text" placeholder="Enter {{ucfirst($value->title)}}"  class="  form-control" wire:model.defer="{{$value->slug}}">
                                                                            @if($slugName === $value->slug)
                                                                                @error(`{{$value->slug}}`)
                                                                                    <p class="text-danger">{{ $message }}
                                                                                    </p>
                                                                                @enderror
                                                                            @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                <button class="btn ripple btn-main-primary submit-config line-height-24" wire:click="updateSetting('{{$value->slug}}','{{$value->title}}','{{$value->type}}')">Submit</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <div id="socialLinks" class="tab-pane fade tab-pane fade in show {{$tab == 3?'active':''}}">
                                                    @if(isset($data) && count($data)>0)
                                                        @foreach($data as $key => $value)
                                                            @if(isset($value->category_type) &&$value->category_type=='S')
                                                                @if (isset($value->type) && $value->type  == 'textarea')
                                                                    <div class="row">
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                {!! Form::label('author',$value->title, ['class' => 'login2 pull-right pull-right-pro']) !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                            <div class="form-group">
                                                                                <textarea id='input-field-{{$value->id}}' placeholder="{{ucfirst($value->slug)}}" class=" maxlength="250" wire:model.defer="{{$value->slug}}">{{$value->value}}</textarea>
                                                                                @if($slugName === $value->slug)
                                                                                    @error(`{{$value->slug}}`)
                                                                                        <p class="text-danger">{{ $message }}
                                                                                        </p>
                                                                                    @enderror
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                <button class="btn ripple btn-main-primary submit-config line-height-24" wire:click="updateSetting('{{$value->slug}}','{{$value->title}}','{{$value->type}}')" >Submit</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @elseif (isset($value->type) && $value->type  == 'select')
                                                                    <div class="row">
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                {!! Form::label('author',$value->title, ['class' => 'login2 pull-right pull-right-pro']) !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                            <div class="form-group">
                                                                                <div class='avatar avatar-xs'><img src="{{$value->value}}" /></div>
                                                                                <input type="file" placeholder="{{ucfirst($value->slug)}}" value="{{$value->value}}" wire:model.defer="{{$value->slug}}">
                                                                                @if($slugName === $value->slug)
                                                                                    @error(`{{$value->slug}}`)
                                                                                        <p class="text-danger">{{ $message }}
                                                                                        </p>
                                                                                    @enderror
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                <button class="btn ripple btn-main-primary submit-config line-height-24" wire:click="updateSetting('{{$value->slug}}','{{$value->title}}','{{$value->type}}')">Submit</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @elseif (isset($value->type) && $value->type  == 'select')
                                                                    <div class="row">
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                {!! Form::label('author',$value->title, ['class' => 'login2 pull-right pull-right-pro']) !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                            <div class="form-group">
                                                                                {!! Form::select('cv', config('global.status'), $value->value, [
                                                                                    'class' => 'form-control',
                                                                                    'wire:model.defer' => $value->slug
                                                                                ]) !!}
                                                                                @if($slugName === $value->slug)
                                                                                    @error(`{{$value->slug}}`)
                                                                                        <p class="text-danger">{{ $message }}
                                                                                        </p>
                                                                                    @enderror
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                <button class="btn ripple btn-main-primary submit-config line-height-24" wire:click="updateSetting('{{$value->slug}}','{{$value->title}}','{{$value->type}}')">Submit</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="row">
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                {!! Form::label('author',$value->title, ['class' => 'login2 pull-right pull-right-pro']) !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                            <div class="form-group">
                                                                            <input type="text" placeholder=" Enter {{ucfirst($value->title)}}"  class="  form-control" wire:model.defer="{{$value->slug}}">
                                                                            @if($slugName === $value->slug)
                                                                                @error(`{{$value->slug}}`)
                                                                                    <p class="text-danger">{{ $message }}
                                                                                    </p>
                                                                                @enderror
                                                                            @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                            <div class="form-group">
                                                                                <button class="btn ripple btn-main-primary submit-config line-height-24" wire:click="updateSetting('{{$value->slug}}','{{$value->title}}','{{$value->type}}')">Submit</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("name-updated", function(event){
        if(event.detail.slug == 'logo'){
            document.getElementById("sidebar-logo").src=event.detail.url;
        }else if(event.detail.slug == 'favicon'){
            document.getElementById("favicon").href=event.detail.url;
        }
    });
</script>