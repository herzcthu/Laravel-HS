@extends ('backend.layouts.master')

@section ('title', 'Role Management | Create Role')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
        Participant Management
        <small>Create Role</small>
    </h1>
@endsection

@section ('breadcrumbs')
    <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li>{!! link_to_route('admin.participants.index', 'Participant Management') !!}</li>
    <li>{!! link_to_route('admin.participants.proles.index', 'Role Management') !!}</li>
    <li>{!! link_to_route('admin.participants.proles.create', 'Create Role') !!}</li>
@stop

@section('content')
    @include('backend.participant.includes.partials.header-buttons')

    {!! Form::open(['route' => 'admin.participants.proles.store', 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'post']) !!}

        <div class="form-group">
            <label class="col-lg-2 control-label">Role Name</label>
            <div class="col-lg-10">
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Role Name']) !!}
            </div>
        </div><!--form control-->
        
        <div class="form-group">
            <label class="col-lg-2 control-label">Role Level</label>
            <div class="col-lg-10">
                {!! Form::select('level',['country' => 'Country', 
                '4' => 'State/Region', 
                '3' => 'District',
                '2' => 'Township',
                '1' => 'Villagetrack',
                '0' => 'Village'
                ], null, ['class' => 'form-control', 'placeholder' => 'Role Name']) !!}
            </div>
        </div><!--form control-->

        <div class="pull-left">
            <a href="{{route('admin.participants.proles.index')}}" class="btn btn-danger">Cancel</a>
        </div>

        <div class="pull-right">
            <input type="submit" class="btn btn-success" value="Save" />
        </div>
        <div class="clearfix"></div>

    {!! Form::close() !!}
@stop