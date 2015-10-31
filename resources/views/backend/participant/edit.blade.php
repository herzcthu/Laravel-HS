@extends ('backend.layouts.master')

@section ('title', 'Participant Management | Edit Participant')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
        Participant Management
        <small>Edit Participant</small>
    </h1>
@endsection

@section ('breadcrumbs')
    <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li>{!! link_to_route('admin.participants.index', 'Participant Management') !!}</li>
    <li class="active">{!! link_to_route('admin.participants.edit', "Edit ".$participant->name, $participant->id) !!}</li>
@stop

@section('content')
    @include('backend.participant.includes.partials.header-buttons')

    {!! Form::model($participant, ['route' => ['admin.participants.update', $participant->id], 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'PATCH']) !!}

        <div class="form-group">
            <label class="col-lg-2 control-label">Profile Image</label>
            <div class="col-lg-10 form-inline">
                
                {!! Form::hidden('avatar',null, ['class' => 'form-control', 'id' => 'avatar_url']) !!}
                <!-- compose message btn -->
                <a class="btn img-thumbnail" data-toggle="modal" data-target="#compose-modal">
                <img id="avatar" class="img-responsive profile-avatar" width="100" height="100" src="{!! (!empty($participant->avatar)? $participant->avatar: asset('img/backend/participant2-160x160.png')) !!}">
                Select/Upload</a>
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">Name</label>
            <div class="col-lg-10">
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'First Name']) !!}
            </div>
        </div><!--form control-->

        <div class="form-group">
            <label class="col-lg-2 control-label">NRC ID</label>
            <div class="col-lg-10">
                {!! Form::text('nrc_id', null, ['class' => 'form-control', 'placeholder' => '5/SaKaNa(N)010203']) !!}
            </div>
        </div><!--form control-->
        
        <div class="form-group">
            <label class="col-lg-2 control-label">E-mail</label>
            <div class="col-lg-10">
                {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'E-mail Address']) !!}
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">Gender</label>
            <div class="col-lg-10">
                {!! Form::select('gender',['male'=>'Male','female'=>'Female','unspecified'=>'Unspecified'], null, ['class' => 'form-control', 'placeholder' => 'Male/Female']) !!}
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">Mobile Phone</label>
            <div class="col-lg-10">
                {!! Form::text('phone[mobile]', (isset($participant->phones->mobile)? $participant->phones->mobile:null), ['class' => 'form-control', 'placeholder' => 'Mobile']) !!}
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">Emergency Phone</label>
            <div class="col-lg-10">
                {!! Form::text('phone[emergency]', (isset($participant->phones->emergency)? $participant->phones->emergency:null), ['class' => 'form-control', 'placeholder' => 'Emergency Phone']) !!}
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">Address</label>
            <div class="col-lg-10">
                {!! Form::textarea('address', null, ['class' => 'form-control', 'placeholder' => 'Address']) !!}
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
     

@include('includes.partials.medialist_grid') 
@include('includes.partials.mediaUploadModel') 

@yield('model')
@endsection
@include('backend.participant.includes.partials.footer-script')