@extends ('backend.layouts.master')

@section ('title', 'Participant Management | Create Participant')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
        Participant Management
        <small>Create Participant</small>
    </h1>
@endsection

@section ('breadcrumbs')
     <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
     <li>{!! link_to_route('admin.participants.index', 'Participant Management') !!}</li>
     <li class="active">{!! link_to_route('admin.participants.create', 'Participant Management') !!}</li>
@stop

@section('content')
    @include('backend.participant.includes.partials.header-buttons')

    {!! Form::open(['route' => 'admin.participants.store', 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'post']) !!}

        <div class="form-group">
            <label class="col-lg-2 control-label">Name</label>
            <div class="col-lg-10">
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Full Name']) !!}
            </div>
        </div><!--form control-->

        <div class="form-group">
            <label class="col-lg-2 control-label">E-mail</label>
            <div class="col-lg-10">
                {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'E-mail Address']) !!}
            </div>
        </div><!--form control-->

        <div class="form-group">
            <label class="col-lg-2 control-label">NRC ID</label>
            <div class="col-lg-10">
                {!! Form::text('nrc_id', null, ['class' => 'form-control', 'placeholder' => 'NRC ID']) !!}
            </div>
        </div><!--form control-->

        @if (count($roles) > 0)
        <div class="form-group">
            <label class="col-lg-2 control-label">Role</label>
            <div class="col-lg-10">
                <select name="role" class="form-control" id="prole">
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" data-role="{{ $role->level }}">{{ $role->name }} ({{ $role->level }}) </option>
                    @endforeach
                </select>
                
            </div>
        </div><!--form control-->
        @endif
        @if (count($locations) > 0)
        <div class="form-group">
            <label class="col-lg-2 control-label">State</label>
            <div class="col-lg-10">                
                {!! Form::select('locations[state]', $locations->toArray(), 'none', ['class' => 'form-control', 'id' => 'plocation']) !!}
            </div>
        </div><!--form control-->
        @endif
        <div class="form-group" id="ajax_insert">
            
        </div><!--form control-->
        

        <div class="pull-left">
            <a href="{{route('admin.participants.index')}}" class="btn btn-danger">Cancel</a>
        </div>

        <div class="pull-right">
            <input type="submit" class="btn btn-success" value="Save" />
        </div>
        <div class="clearfix"></div>

    {!! Form::close() !!}
@endsection
@include('backend.participant.includes.partials.footer-script')