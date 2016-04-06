@extends ('backend.layouts.master')

@section ('title', 'Organization Management | Create Organization')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
        User Management
        <small>Create Organization</small>
    </h1>
@endsection

@section ('breadcrumbs')
    <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li>{!! link_to_route('admin.access.users.index', 'User Management') !!}</li>
    <li>{!! link_to_route('admin.access.organizations.index', 'Organization Management') !!}</li>
    <li>{!! link_to_route('admin.access.organizations.create', 'Create Organization') !!}</li>
@stop

@section('content')
    @include('backend.access.includes.partials.header-buttons')

    {!! Form::open(['route' => 'admin.access.organizations.store', 'class' => 'form-horizontal', 'organization' => 'form', 'method' => 'post']) !!}

        <div class="form-group">
            <label class="col-lg-2 control-label">Organization Name</label>
            <div class="col-lg-10">
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Organization Name']) !!}
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">Organization Short Name</label>
            <div class="col-lg-10">
                {!! Form::text('short', null, ['class' => 'form-control', 'placeholder' => 'Short Name']) !!}
            </div>
        </div><!--form control-->

        <div class="pull-left">
            <a href="{{route('admin.access.organizations.index')}}" class="btn btn-danger">Cancel</a>
        </div>

        <div class="pull-right">
            <input type="submit" class="btn btn-success" value="Save" />
        </div>
        <div class="clearfix"></div>

    {!! Form::close() !!}
@stop