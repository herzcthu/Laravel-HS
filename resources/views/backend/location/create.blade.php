@extends ('backend.layouts.master')

@section ('title', 'Location Management | Create Location')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop
@section ('after-styles-end')
    {!! HTML::style('css/vendor/jquery-ui/themes/smoothness/jquery-ui.min.css') !!}
@stop

@section('page-header')
    <h1>
        Location Management
        <small>Create Location</small>
    </h1>
@endsection

@section ('breadcrumbs')
     <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
     <li>{!! link_to_route('admin.locations.index', 'Location Management') !!}</li>
     <li class="active">{!! link_to_route('admin.locations.create', 'Location Management') !!}</li>
@stop

@section('content')
@if(count($organizations) <= 0) 
<div class="alert alert-danger">
    Create at least one organization before creating new project. <a href="{{route('admin.access.organizations.create')}}">Create Organization</a>
</div>
@endif   

    {!! Form::open(['route' => 'admin.locations.store', 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'post']) !!}
        @if(access()->can('manage_organization'))
        <div class="form-group">
            <label class="col-lg-2 control-label">Organization</label>
            <div class="col-lg-10">
                {!! Form::select('org_id', $organizations->lists('name','id'),null, ['class' => 'form-control', 'placeholder' => 'P117001']) !!}
            </div>
        </div><!--form control-->
        @else
        <div class="form-group">
            <label class="col-lg-2 control-label">Organization</label>
            <div class="col-lg-10">
                {!! Form::select('org_id',access()->user()->organizations->lists('name','id'), null, ['class' => 'form-control', 'placeholder' => 'P117001']) !!}
            </div>
        </div><!--form control-->
        @endif
        <div class="form-group">
            <label class="col-lg-2 control-label">Location Name</label>
            <div class="col-lg-10">
                {!! Form::text('location', null, ['id' => 'location', 'class' => 'form-control', 'placeholder' => 'Ayeyawaddy']) !!}
                {!! Form::hidden('location_id', null, ['id' => 'location_id']) !!}
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">Custom location code</label>
            <div class="col-lg-10">
                {!! Form::text('pcode', null, ['class' => 'form-control', 'placeholder' => 'P117001']) !!}
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">UEC location code</label>
            <div class="col-lg-10">
                {!! Form::text('uec_code', null, ['class' => 'form-control', 'placeholder' => 'UEC1000']) !!}
            </div>
        </div><!--form control-->
        <div class="form-group" id="ajax_insert">
            
        </div><!--form control-->
        

        <div class="pull-left">
            <a href="{{route('admin.locations.index')}}" class="btn btn-danger">Cancel</a>
        </div>

        <div class="pull-right">
            <input type="submit" class="btn btn-success" value="Save" />
        </div>
        <div class="clearfix"></div>

    {!! Form::close() !!}
@endsection
@include('backend.location.includes.partials.footer-script')
@stop