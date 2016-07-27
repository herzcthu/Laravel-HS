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
    <div class="panel">
        <div class="panel-body">
    {!! Form::open(['route' => 'admin.locations.import','files'=>true, 'class' => 'form-horizontal', 'organization' => 'form', 'method' => 'post']) !!}
    
        <div class="form-group">
            
            {!! Form::label('file','File',['id'=>'','class'=>'col-xs-3 sr-only control-label']) !!}
            
            <div class="col-xs-6">
            {!! Form::file('file',[ 'class'=>'filestyle', 'data-icon'=>'true', 'data-iconName'=>'fa fa-upload']) !!}
            </div>
            <div class="col-xs-3"></div>
        </div>
    <!--div class="form-group">
        <div class="col-xs-3"></div>
        <div class="col-xs-6">
            <div class="checkbox col-xs-offset-2">
                <label for="customfields">
                    <input id="cf" type="checkbox" name="customfields"></input>
            Custom Fields Name
                </label>
            </div>
        </div>
        <div class="col-xs-3"></div>
    </div>
    <div class="form-group hide" id="fields">
        <div class="col-xs-3"></div>
            <div class="col-xs-1">
            {!! Form::label('columns', 'Fields', ['class'=>'control-label']) !!}
            </div>
        <div class="col-xs-5">
            <div class="row">
                <div class="col-xs-12">
                    {!! Form::label('state', 'State : ', ['class'=>'control-label']) !!}
                    {!! Form::text('fields[state]',null,['class'=>'form-control']) !!}
                    {!! Form::label('district', 'District : ', ['class'=>'control-label']) !!}
                    {!! Form::text('fields[district]',null,['class'=>'form-control']) !!}
                    {!! Form::label('township', 'Township : ', ['class'=>'control-label']) !!}
                    {!! Form::text('fields[township]',null,['class'=>'form-control']) !!}
                    {!! Form::label('villatetract', 'Village Tract : ', ['class'=>'control-label']) !!}
                    {!! Form::text('fields[villagetract]',null,['class'=>'form-control']) !!}
                    {!! Form::label('village', 'Village : ', ['class'=>'control-label']) !!}
                    {!! Form::text('fields[village]',null,['class'=>'form-control']) !!}
                    {!! Form::label('pfields', 'Participants (comma seperated) : ', ['class'=>'control-label']) !!}
                    {!! Form::text('fields[pfields]',null,['class'=>'form-control', 'placeholder' => 'Observer A,Observer B,Supervisor']) !!}
                </div>
            </div>            
        </div>
        <div class="col-xs-3">
            
        </div>
    </div-->
        <div class="form-group">
            <div class="col-xs-3"></div>
            <div class="col-xs-1">
            {!! Form::label('country', 'Country', ['class'=>'control-label']) !!}
            </div>
            <div class="col-xs-5">
            {!! Form::selectCountry('country', 'MM', ['class'=>'form-control']) !!}
            </div>
            <div class="col-xs-3"></div>
        </div>
        
    @if (count($organizations) > 0)
        
        <div class="form-group">
            <div class="col-xs-3"></div>
            <div class="col-xs-1">
            {!! Form::label('organization','Organization',['class'=>'control-label']) !!}
            </div>
            <div class="col-xs-5">
            {!! Form::select('organization',$organizations->lists('name','id'), null,['class'=>'form-control']) !!}
            </div>
            <div class="col-xs-3"></div>
        </div>
    @else
        <div class="alert alert-danger">
            Create at least one organization before creating new project. <a href="{{route('admin.access.organizations.create')}}">Create Organization</a>
        </div>
    @endif
        <div class="form-group">
            <div class="col-xs-3"></div>
            <div class="col-xs-3">
            </div>
            <div class="col-xs-3">
                <div class="pull-right">
                {!! Form::submit('Import', ['class'=>'btn btn-success']) !!}
                </div>
            </div>
            <div class="col-xs-3"></div>
        </div>
        <div class="pull-left">
            <a href="{{route('admin.locations.index')}}" class="btn btn-danger">Cancel</a>
        </div>
        <div class="clearfix"></div>
    {!! Form::close() !!}
        </div>
    </div>
@endsection
@section ('before-scripts-end')
{!! HTML::script('js/vendor/bootstrap-filestyle/bootstrap-filestyle.min.js') !!}
@stop
@push('scripts')
<script type="text/javascript">
(function ($) {
    $(document).ready(function ($) {
        $('#cf').on('change', function(){
            $('#fields').toggle(this.checked).removeClass('hide');
        }).change();
    });
}(jQuery));
</script>
@endpush
@include('backend.location.includes.partials.footer-script')