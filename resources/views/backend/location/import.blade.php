@extends ('backend.layouts.master')

@section ('title', 'Location Management | Import Location')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
        Location Management
        <small>Import Location</small>
    </h1>
@endsection

@section ('breadcrumbs')
     <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
     <li>{!! link_to_route('admin.locations.index', 'Location Management') !!}</li>
     <li class="active">{!! link_to_route('admin.locations.import', 'Location Management') !!}</li>
@stop

@section('content')
    @include('backend.location.includes.partials.header-buttons')

    {!! Form::open(['route' => 'admin.locations.import','files'=>true, 'class' => 'form-horizontal', 'organization' => 'form', 'method' => 'post']) !!}
    
        <div class="form-group">
            
            {!! Form::label('file','File',['id'=>'','class'=>'col-lg-3 sr-only control-label']) !!}
            
            <div class="col-lg-6">
            {!! Form::file('file',[ 'class'=>'filestyle', 'data-icon'=>'true', 'data-iconName'=>'fa fa-upload']) !!}
            </div>
            <div class="col-lg-3"></div>
        </div>
        <div class="form-group">
            <div class="col-lg-3"></div>
            <div class="col-lg-1">
            {!! Form::label('country', 'Country', ['class'=>'control-label']) !!}
            </div>
            <div class="col-lg-5">
            {!! Form::selectCountry('country', 'MM', ['class'=>'form-control']) !!}
            </div>
            <div class="col-lg-3"></div>
        </div>
        @if (count($proles) > 0)
        
            <div class="form-group">
                <div class="col-lg-3"></div>
                <div class="col-lg-1">
                {!! Form::label('prole','Participant Role',['class'=>'control-label']) !!}
                </div>
                <div class="col-lg-5">
                {!! Form::select('prole',$proles->lists('name','id'), null,['class'=>'form-control']) !!}
                </div>
                <div class="col-lg-3"></div>
            </div>
        @else
            <div class="alert alert-danger">
                Create at least one participant role before importing locations. <a href="{{route('admin.participants.proles.create')}}">Create Participant Role</a>
            </div>
        @endif
    @if (count($organizations) > 0)
        
        <div class="form-group">
            <div class="col-lg-3"></div>
            <div class="col-lg-1">
            {!! Form::label('organization','Organization',['class'=>'control-label']) !!}
            </div>
            <div class="col-lg-5">
            {!! Form::select('organization',$organizations->lists('name','id'), null,['class'=>'form-control']) !!}
            </div>
            <div class="col-lg-3"></div>
        </div>
    @else
        <div class="alert alert-danger">
            Create at least one organization before creating new project. <a href="{{route('admin.access.organizations.create')}}">Create Organization</a>
        </div>
    @endif
        <div class="form-group">
            <div class="col-lg-3"></div>
            <div class="col-lg-3">
            <div class="input-group">
                <!-- reset buttons -->
                {!! Form::reset('Reset', ['class'=>'btn btn-default']) !!}
            </div>
            </div>
            <div class="col-lg-3">
                <div class="pull-right">
                {!! Form::submit('Import', ['class'=>'btn btn-success']) !!}
                </div>
            </div>
            <div class="col-lg-3"></div>
        </div>
        <div class="pull-left">
            <a href="{{route('admin.locations.index')}}" class="btn btn-danger">Cancel</a>
        </div>
        <div class="clearfix"></div>
    {!! Form::close() !!}

@endsection
@section ('before-scripts-end')
{!! HTML::script('js/vendor/bootstrap-filestyle/bootstrap-filestyle.min.js') !!}
@stop
@include('backend.location.includes.partials.footer-script')