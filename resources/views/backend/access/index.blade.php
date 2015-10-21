@extends ('backend.layouts.master')

@section ('title', 'User Management')

@section('page-header')
    <h1>
        User Management
        <small>Active Users</small>
    </h1>
@endsection

@section ('breadcrumbs')
    <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li class="active">{!! link_to_route('admin.access.users.index', 'User Management') !!}</li>
@stop

@section('content')
    @include('backend.access.includes.partials.header-buttons')
<div class="pull-left" style="margin-bottom:10px">
        <div class="row">
        {!! Form::open(['route' => 'admin.access.users.search', 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'get']) !!}
        
        <div class='col-xs-12 col-sm-12 col-md-12 form-inline'>
            <div class="input-group">
                <input name="q" class="form-control" placeholder="{!! Input::get('q')? Input::get('q'):'Search' !!}" type="text">
                <span class="input-group-btn">
                    <button type="submit" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </div>
        
        {!! Form::close() !!}
        </div>
        
</div>    
{!! Form::open(['route' => 'admin.access.users.bulk', 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'post']) !!}

<div class='row' style='margin-bottom: 10px'>
<!--div class='col-xs-12 col-sm-12 col-md-12 form-inline'>
 {!! Form::label('role', 'Change to - ', ['class' => 'control-label']) !!}
 {!! Form::select('role', isset($roles) ? $roles->lists('name','id'):['none' => 'None'],false, ['class' => 'form-control']) !!}

 {!! Form::submit( 'Bulk Change', ['class' => 'btn btn-secondary']) !!}
</div-->
</div>
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><input type='checkbox' id='checkall' class='checkall checkbox'></th>
            <th>ID</th>
            <th>Name</th>
            <th>E-mail</th>
            <th>Confirmed</th>
            <th>Organizations</th>
            <th>Role</th>
            <th>Other Permissions</th>
            <th class="visible-lg">Created</th>
            <th class="visible-lg">Last Updated</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                @if(access()->user()->can('manage_organization') || access()->user()->organization == $user->organization)
                <tr>
                    <td><input type='checkbox' class='checkall checkbox' name='users[{!! $user->id !!}]'></td>
                    <td>{!! $user->id !!}</td>
                    <td>
                        <img width="30" height="30" src="{!! (!empty($user->avatar)? $user->avatar: asset('img/backend/user2-160x160.png')) !!}" alt="{!! $user->name !!}" title="{!! $user->name !!}"> {!! $user->name !!}
                    </td>
                    <td>{!! link_to("mailto:".$user->email, $user->email) !!}</td>
                    <td>{!! $user->confirmed_label !!}</td>
                    <td>
                        @if ($user->organization()->count() > 0)
                            {!! $user->organization->name !!}
                        @else
                            None
                        @endif
                    </td>
                    <td>
                        @if ($user->role()->count() > 0)
                            
                                {!! $user->role->name !!}<br/>
                        @else
                            None
                        @endif
                    </td>
                    <td>
                        @if ($user->permissions()->count() > 0)
                            @foreach ($user->permissions as $perm)
                                {!! $perm->display_name !!}<br/>
                            @endforeach
                        @else
                            None
                        @endif
                    </td>
                    <td class="visible-lg">{!! $user->created_at->diffForHumans() !!}</td>
                    <td class="visible-lg">{!! $user->updated_at->diffForHumans() !!}</td>
                    <td>{!! $user->action_buttons !!}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="pull-left">
        {!! $users->total() !!} user(s) total
    </div>

    <div class="pull-right">
        {!! $users->render() !!}
    </div>

    <div class="clearfix"></div>
    {!! Form::close() !!}
@stop