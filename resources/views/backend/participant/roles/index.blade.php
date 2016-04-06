@extends ('backend.layouts.master')

@section ('title', 'Role Management')

@section('page-header')
    <h1>
        Participant Management
        <small>Role Management</small>
    </h1>
@endsection

@section ('breadcrumbs')
    <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li>{!! link_to_route('admin.participants.index', 'Participant Management') !!}</li>
    <li>{!! link_to_route('admin.participants.proles.index', 'Role Management') !!}</li>
@stop

@section('content')
    @include('backend.participant.includes.partials.header-buttons')

    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th>Role</th>
            <th>Level</th>
            <th># Participants</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($roles as $role)
                <tr>
                    <td>{!! $role->name !!}</td>
                    <td>{!! ucfirst($role->level) !!}</td>
                    <td>{!! $role->participants()->count() !!}</td>
                    <td>{!! $role->action_buttons !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pull-left">
        {{ $roles->total() }} roles(s) total
    </div>

    <div class="pull-right">
        {{ $roles->render() }}
    </div>

    <div class="clearfix"></div>
@stop