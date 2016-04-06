@extends ('backend.layouts.master')

@section ('title', 'Organization Management')

@section('page-header')
    <h1>
        User Management
        <small>Organization Management</small>
    </h1>
@endsection

@section ('breadcrumbs')
    <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li>{!! link_to_route('admin.access.users.index', 'User Management') !!}</li>
    <li>{!! link_to_route('admin.access.organizations.index', 'Organization Management') !!}</li>
@stop

@section('content')
    @include('backend.access.includes.partials.header-buttons')

    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th>Organization</th>
            <th>Short Name</th>
            <th># Users</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($organizations as $organization)
                <tr>
                    <td>{!! $organization->name !!}</td>
                    <td>
                        {!! $organization->short !!}
                    </td>
                    <td>{!! $organization->users()->count() !!}</td>
                    <td>{!! $organization->action_buttons !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pull-left">
        {{ $organizations->total() }} organizations(s) total
    </div>

    <div class="pull-right">
        {{ $organizations->render() }}
    </div>

    <div class="clearfix"></div>
@stop