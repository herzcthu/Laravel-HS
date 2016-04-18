@extends ('backend.layouts.master')

@section ('title', 'Organization Management | Edit Organization')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
        User Management
        <small>Edit Organization</small>
    </h1>
@endsection

@section ('breadcrumbs')
     <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
     <li>{!! link_to_route('admin.access.users.index', 'User Management') !!}</li>
     <li>{!! link_to_route('admin.access.organizations.index', 'Organization Management') !!}</li>
     <li>{!! link_to_route('admin.access.organizations.edit', 'Edit '.$organization->name, $organization->id) !!}</li>
@stop

@section('content')
    @include('backend.access.includes.partials.header-buttons')

    {!! Form::model($organization, ['route' => ['admin.access.organizations.update', $organization->id], 'class' => 'form-horizontal', 'organization' => 'form', 'method' => 'PATCH']) !!}

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
        <div class="form-group">
            {!! Form::label('country', 'Country', ['class'=>'col-xs-2 control-label']) !!}
            <div class="col-lg-10">
            {!! Form::selectCountry('country', 'MM', ['class'=>'form-control']) !!}
            </div>
        </div>
        <div class="pull-left">
            <a href="{{route('admin.access.organizations.index')}}" class="btn btn-danger">Cancel</a>
        </div>

        <div class="pull-right">
            <input type="submit" class="btn btn-success" value="Save" />
        </div>
        <div class="clearfix"></div>

    {!! Form::close() !!}
@stop