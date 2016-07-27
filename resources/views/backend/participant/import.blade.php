@extends ('backend.layouts.master')

@section ('title', 'Participant Management | Import Participant')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
        Participant Management
        <small>Import Participant</small>
    </h1>
@endsection

@section ('breadcrumbs')
     <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
     <li>{!! link_to_route('admin.participants.index', 'Participant Management') !!}</li>
     <li class="active">{!! link_to_route('admin.participants.import', 'Participant Management') !!}</li>
@stop

@section('content')
    @include('backend.participant.includes.partials.header-buttons')

    {!! Form::open(['route' => 'admin.participants.import','files'=>true, 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'post']) !!}
    
        <div class="form-group">
            
            {!! Form::label('file','File',['id'=>'','class'=>'col-lg-3 sr-only control-label']) !!}
            
            <div class="col-lg-6">
            {!! Form::file('file',[ 'class'=>'filestyle', 'data-icon'=>'true', 'data-iconName'=>'fa fa-upload']) !!}
            </div>
            <div class="col-lg-3"></div>
        </div>
    @if (count($roles) > 0)
        
        <div class="form-group">
            <div class="col-lg-3"></div>
            <div class="col-lg-1">
            {!! Form::label('role','Role',['class'=>'control-label']) !!}
            </div>
            <div class="col-lg-5">
                <select name="role" class="form-control" id="prole">
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" data-role="{{ $role->level }}">{{ $role->name }} ({!! ucfirst($role->level) !!} Level) </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3"></div>
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
            <a href="{{route('admin.participants.index')}}" class="btn btn-danger">Cancel</a>
        </div>
        <div class="clearfix"></div>
    {!! Form::close() !!}

@endsection
@section ('before-scripts-end')
{!! HTML::script('js/vendor/bootstrap-filestyle/bootstrap-filestyle.min.js') !!}
@stop
@include('backend.participant.includes.partials.footer-script')