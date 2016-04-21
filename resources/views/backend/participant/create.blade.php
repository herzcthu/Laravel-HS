@extends ('backend.layouts.master')

@section ('title', 'Participant Management | Create Participant')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop
@section ('after-styles-end')
    {!! HTML::style('css/vendor/jquery-ui/themes/smoothness/jquery-ui.min.css') !!}
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
        @if(access()->can('manage_organization'))
        <div class="form-group">
            <label class="col-lg-2 control-label">Organization</label>
            <div class="col-lg-10">
                {!! Form::select('org_id', $organizations->lists('name','id'),null, ['id' => 'org_id', 'class' => 'form-control', 'placeholder' => 'P117001']) !!}
            </div>
        </div><!--form control-->
        @else
        <div class="form-group">
            <label class="col-lg-2 control-label">Organization</label>
            <div class="col-lg-10">
                {!! Form::select('org_id',access()->user()->organizations->lists('name','id'), null, ['id' => 'org_id', 'class' => 'form-control', 'placeholder' => 'P117001']) !!}
            </div>
        </div><!--form control-->
        @endif
        <div class="form-group">
            <label class="col-lg-2 control-label">ID Code</label>
            <div class="col-lg-10">
                {!! Form::text('participant_id', null, ['class' => 'form-control', 'placeholder' => '117001A']) !!}
            </div>
        </div><!--form control-->
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
        
        <div class="form-group">
            <label class="col-lg-2 control-label">Date of Birth</label>
            <div class="col-lg-10">
                {!! Form::text('dob', null, ['class' => 'form-control', 'placeholder' => 'DD/MM/YYYY']) !!}
            </div>
        </div><!--form control-->
        
        <div class="form-group">
            <label class="col-lg-2 control-label">Gender</label>
            <div class="col-lg-10">
                {!! Form::select('gender',['Male'=>'Male', 'Female'=>'Female', 'Unspecified'=>'Unspecified'], 'Unspecified', ['class' => 'form-control']) !!}
            </div>
        </div><!--form control-->
        
        <div class="form-group">
            <label class="col-lg-2 control-label">Mobile Phones</label>
            <div class="col-lg-10">
                {!! Form::text('phones[mobile]', null, ['class' => 'form-control', 'placeholder' => '09796979696']) !!}
            </div>
        </div><!--form control-->
        
        <div class="form-group">
            <label class="col-lg-2 control-label">Home Phones</label>
            <div class="col-lg-10">
                {!! Form::text('phones[emergency]', null, ['class' => 'form-control', 'placeholder' => '09796979696']) !!}
            </div>
        </div><!--form control-->
        
        <div class="form-group">
            <label class="col-lg-2 control-label">Address</label>
            <div class="col-lg-10">
                {!! Form::text('address', null, ['class' => 'form-control', 'placeholder' => 'Full address here']) !!}
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
        @else
            <div class="alert alert-danger">
                Create at least one participant role. <a href="{{route('admin.participants.proles.create')}}">Create Participant Role</a>
            </div>
        @endif
        
        <div class="form-group">
            <label for="pcode" class="col-lg-2 control-label">Location Code</label>
            <div class="col-lg-10">
                {!! Form::text('pcode', null, ['id' => 'pcode', 'class' => 'form-control', 'placeholder' => '117001']) !!}
            </div>
        </div><!--form control-->
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