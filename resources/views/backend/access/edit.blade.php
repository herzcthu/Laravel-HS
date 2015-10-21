@extends ('backend.layouts.master')

@section ('title', 'User Management | Edit User')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
        User Management
        <small>Edit User</small>
    </h1>
@endsection

@section ('breadcrumbs')
    <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li>{!! link_to_route('admin.access.users.index', 'User Management') !!}</li>
    <li class="active">{!! link_to_route('admin.access.users.edit', "Edit ".$user->name, $user->id) !!}</li>
@stop

@section('content')
    @include('backend.access.includes.partials.header-buttons')

    {!! Form::model($user, ['route' => ['admin.access.users.update', $user->id], 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'PATCH']) !!}

        <div class="form-group">
            <label class="col-lg-2 control-label">Profile Image</label>
            <div class="col-lg-10 form-inline">
                
                {!! Form::hidden('avatar',null, ['class' => 'form-control', 'id' => 'avatar_url']) !!}
                <!-- compose message btn -->
                <a class="btn img-thumbnail" data-toggle="modal" data-target="#compose-modal">
                <img id="avatar" class="img-responsive profile-avatar" width="100" height="100" src="{!! (!empty($user->avatar)? $user->avatar: asset('img/backend/user2-160x160.png')) !!}">
                Select/Upload</a>
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">Name</label>
            <div class="col-lg-10">
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'First Name']) !!}
            </div>
        </div><!--form control-->

        <div class="form-group">
            <label class="col-lg-2 control-label">E-mail</label>
            <div class="col-lg-10">
                {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'E-mail Address']) !!}
            </div>
        </div><!--form control-->

        <div class="form-group">
            <label class="col-lg-2 control-label">Active</label>
            <div class="col-lg-1">
                <div class="sw-green create-permissions-switch">
                    <div class="onoffswitch">
                        <input type="checkbox" value="1" name="status" class="toggleBtn onoffswitch-checkbox" id="user-active" {{$user->status == 1 ? "checked='checked'" : ''}}>
                        <label for="user-active" class="onoffswitch-label">
                            <div class="onoffswitch-inner"></div>
                            <div class="onoffswitch-switch"></div>
                        </label>
                    </div>
                </div><!--green checkbox-->
            </div>
        </div><!--form control-->

        <div class="form-group">
            <label class="col-lg-2 control-label">Confirmed</label>
            <div class="col-lg-1">
                <div class="sw-green confirmation-switch">
                    <div class="onoffswitch">
                        <input type="checkbox" value="1" name="confirmed" class="toggleBtn onoffswitch-checkbox" id="confirm-active" {{$user->confirmed == 1 ? "checked='checked'" : ''}}>
                        <label for="confirm-active" class="onoffswitch-label">
                            <div class="onoffswitch-inner"></div>
                            <div class="onoffswitch-switch"></div>
                        </label>
                    </div>
                </div><!--green checkbox-->
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">Organizations</label>
            <div class="col-lg-3">
                @if (count($organizations) > 0)
                   
                        {!! Form::select('users_organization', aio()->addNone($organizations->lists('id','name')->toArray(), true), (isset($user->organization->id)? $user->organization->id:null), ['class' => 'form-control']) !!}
                            
                        <div class="clearfix"></div>

                    
                @else
                    No Organizations to set
                @endif
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">Associated Roles</label>
            <div class="col-lg-3">
                @if (count($roles) > 0)
                    
                        {!! Form::select('user_role', $roles->lists('name','id'), (isset($user->role->id)? $user->role->id:null), ['class' => 'form-control']) !!}
                                               
                        <div class="clearfix"></div>

                @else
                    No Roles to set
                @endif
            </div>
        </div><!--form control-->

        <div class="form-group">
            <label class="col-lg-2 control-label">Other Permissions</label>
            <div class="col-lg-3">
                @if (count($permissions))
                    @foreach ($permissions as $perm)
                        {!! $perm->display_name !!}
                        <div class="other-permissions-switch">
                            <div class="onoffswitch">
                                <input type="checkbox" value="{{$perm->id}}" name="permission_user[]" {{in_array($perm->id, $user_permissions) ? 'checked="checked"' : ""}} class="toggleBtn onoffswitch-checkbox" id="permission-{{$perm->id}}">
                                <label for="permission-{{$perm->id}}" class="onoffswitch-label">
                                    <div class="onoffswitch-inner"></div>
                                    <div class="onoffswitch-switch"></div>
                                </label>
                            </div>
                        </div><!--green checkbox-->
                        <div class="clearfix"></div>
                    @endforeach
                @else
                    No other permissions
                @endif
            </div><!--col 3-->
        </div><!--form control-->

        <div class="pull-left">
            <a href="{{route('admin.access.users.index')}}" class="btn btn-danger">Cancel</a>
        </div>

        <div class="pull-right">
            <input type="submit" class="btn btn-success" value="Save" />
        </div>
        <div class="clearfix"></div>

    {!! Form::close() !!}
     

@include('includes.partials.medialist_grid') 
@include('includes.partials.mediaUploadModel') 

@yield('model')
@stop