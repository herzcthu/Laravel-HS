@extends ('backend.layouts.master')

@section ('title', 'Participant Management | Edit Participant')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop
@section ('after-styles-end')
    {!! HTML::style('css/vendor/jquery-ui/themes/smoothness/jquery-ui.min.css') !!}
@stop
@section ('meta')
    <meta name="csrf-token" content="{!! csrf_token()!!}"/>
@endsection
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
        <div class="panel panel-default">
        <div class="panel-body">
        <div class="form-group">
            <label class="col-xs-2 control-label">Profile Image</label>
            <div class="col-xs-10 form-inline">
                
                {!! Form::hidden('avatar',null, ['class' => 'form-control', 'id' => 'avatar_url']) !!}
                <!-- compose message btn -->
                <a class="btn img-thumbnail" data-toggle="modal" data-target="#compose-modal">
                <img id="avatar" class="img-responsive profile-avatar" width="100" height="100" src="{!! (!empty($participant->avatar)? $participant->avatar: asset('img/backend/participant2-160x160.png')) !!}">
                Select/Upload</a>
            </div>
        </div><!--form control-->
        @if(access()->can('manage_organization'))
        <div class="form-group">
            <label class="col-xs-2 control-label">Organization</label>
            <div class="col-xs-10">
                {!! Form::select('org_id', $organizations->lists('name','id'),null, ['id' => 'org_id', 'class' => 'form-control', 'placeholder' => 'P117001']) !!}
            </div>
        </div><!--form control-->
        @else
        <div class="form-group">
            <label class="col-xs-2 control-label">Organization</label>
            <div class="col-xs-10">
                {!! Form::select('org_id',access()->user()->organizations->lists('name','id'), null, ['id' => 'org_id', 'class' => 'form-control', 'placeholder' => 'P117001']) !!}
            </div>
        </div><!--form control-->
        @endif
        <div class="form-group">
            <label class="col-xs-2 control-label">ID Code</label>
            <div class="col-xs-10">
                {!! Form::text('participant_id', null, ['class' => 'form-control', 'placeholder' => '117001A']) !!}
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-xs-2 control-label">Name</label>
            <div class="col-xs-10">
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Full Name']) !!}
            </div>
        </div><!--form control-->

        <div class="form-group">
            <label class="col-xs-2 control-label">E-mail</label>
            <div class="col-xs-10">
                {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'E-mail Address']) !!}
            </div>
        </div><!--form control-->

        <div class="form-group">
            <label class="col-xs-2 control-label">NRC ID</label>
            <div class="col-xs-10">
                {!! Form::text('nrc_id', null, ['class' => 'form-control', 'placeholder' => 'NRC ID']) !!}
            </div>
        </div><!--form control-->
        
        <div class="form-group">
            <label class="col-xs-2 control-label">Date of Birth</label>
            <div class="col-xs-10">
                {!! Form::text('dob', null, ['class' => 'form-control', 'placeholder' => 'DD/MM/YYYY']) !!}
            </div>
        </div><!--form control-->
        
        <div class="form-group">
            <label class="col-xs-2 control-label">Gender</label>
            <div class="col-xs-10">
                {!! Form::select('gender',['Male'=>'Male', 'Female'=>'Female', 'Unspecified'=>'Unspecified'], null, ['class' => 'form-control']) !!}
            </div>
        </div><!--form control-->
        
        <div class="form-group">
            <label class="col-xs-2 control-label">Mobile Phones</label>
            <div class="col-xs-10">
                {!! Form::text('phones[mobile]', null, ['class' => 'form-control', 'placeholder' => '09796979696']) !!}
            </div>
        </div><!--form control-->
        
        <div class="form-group">
            <label class="col-xs-2 control-label">Home Phones</label>
            <div class="col-xs-10">
                {!! Form::text('phones[emergency]', null, ['class' => 'form-control', 'placeholder' => '09796979696']) !!}
            </div>
        </div><!--form control-->
        
        <div class="form-group">
            <label class="col-xs-2 control-label">Address</label>
            <div class="col-xs-10">
                {!! Form::text('address', null, ['class' => 'form-control', 'placeholder' => 'Full address here']) !!}
            </div>
        </div><!--form control-->

        @if (count($roles) > 0)
        <div class="form-group">
            <label class="col-xs-2 control-label">Role</label>
            <div class="col-xs-10">
                <select name="role" class="form-control" id="prole">
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" data-role="{{ $role->level }}">{{ $role->name }} ({{ $area[$participant->role->level] }}) </option>
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
            <label class="col-xs-2 control-label">Locations</label>
            <div class="col-xs-10">
                <div class="row">
                    <span class="alert text-red flash"></span>
                    @foreach($locations as $pcode)
                    <div class="col-xs-1 {{ str_slug($pcode->{$area[$participant->role->level]}) }}"> 
                        <div class="input-group"> 
                            <p class="input-group-addon">{{ $pcode->{$area[$participant->role->level]} }}</p> 
                            <span class="input-group-btn"> 
                                <button data-href="{{ route('ajax.participants.delocate',['participant' => $participant->id, 'location' => $pcode->id]) }}" class="btn btn-danger btn-area" type="button" data-group="{{ str_slug($pcode->{$area[$participant->role->level]}) }}">X</button> 
                            </span> 
                        </div> 
                    </div>
                    @endforeach
                </div>
            </div>
        </div><!--form control-->
        </div>
        </div>
        <div class="panel panel-default">
        <div class="panel-heading">Associate new location</div>
        <div class="panel-body">
        <div class="form-group">
            <label class="col-xs-2 control-label">Location Code</label>
            <div class="col-xs-10">
                {!! Form::text('plcode', null, ['id' => 'pcode', 'class' => 'form-control', 'placeholder' => '117001']) !!}
            </div>
        </div><!--form control-->
        </div>
        </div>
        
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
@push('scripts')
<script type="text/javascript">
(function ($) {
    $.ajaxSetup({
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
    });
    $('.btn-area').on('click', function(e) {
            e.preventDefault();
            var self = $(this);
            $.ajax({ 
                url:$(this).data('href'),
                type: 'post',
                data: { _method: 'DELETE'}
            })
            .success(function( data ) {
                    var json = JSON.parse(data);
                    if(json.status == true){
                            var css = self.attr('data-group');
                            console.log(css);
                            $('.'+ css).remove();
                            $( "span.flash" ).html( 'location disassociated' );
                            $( "span.flash" ).fadeIn( "slow" );

                    }
                    if(json.status == false){
                            $( "span.flash" ).html( 'something wrong' );
                            $( "span.flash" ).fadeIn( "slow" );
                    }
            });
    });  
    
}(jQuery));
</script>
@endpush
@yield('model')
@endsection
@include('backend.participant.includes.partials.footer-script')