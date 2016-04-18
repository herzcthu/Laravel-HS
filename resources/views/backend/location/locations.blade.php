@extends ('backend.layouts.master')

@section ('title', 'Locations Management | Locations Library')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
        Locations Management
        <small>Locations Library</small>
    </h1>
@endsection

@section ('breadcrumbs')
    <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li>{!! link_to_route('admin.locations.index', 'Locations Management') !!}</li>
    <li class="active"></li>
@stop

@section('content')
<div class="row">
    <div class='col-xs-12 col-sm-12 col-md-12'>
        {!! Form::open(['route' => 'admin.locations.search', 'class' => 'form-horizontal col-xs-4', 'role' => 'form', 'method' => 'get']) !!}
        <div class="form-inline">
            <div class="form-group">
                {!! Form::select('search_by', ['village' => 'Village', 
                                            'township' => 'Township', 
                                            'district' => 'District', 
                                            'state' => 'State'], 
                                            Input::get('search_by')? Input::get('search_by'):'village', 
                                            ['class' => 'hidden form-control col-xs-2 disable', 'disable']) !!}
            
                
                <input name="q" class="form-control col-xs-2 " placeholder="{!! Input::get('q')? Input::get('q'):'Search' !!}" type="text">
                
                    <button type="submit" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
            </div>
        
        </div>
        {!! Form::close() !!}
        {!! Form::open(['route' => 'admin.locations.deleteall', 'name'=>'delete_all' ,'class' => 'form-horizontal col-xs-4', 'role' => 'form', 'method' => 'get']) !!}
        <div class="form-inline">
            <div class="form-group">
                @if(access()->user()->role->level < 2)
                <button type="submit" id="search-btn" class="btn btn-flat btn-danger"><i class="fa fa-remove" data-toggle="tooltip" data-placement="top" title="Delete"></i> Delete All</button>
                @endif                      
            </div>
        
        </div>
        {!! Form::close() !!}
        
        <div class="button col-xs-4">
        <a href="{{route('admin.locations.import')}}" class="btn btn-primary">Import</a>
        <a href="{{route('admin.locations.create')}}" class="btn btn-primary">Create</a>
        </div>
    </div>
</div>


<!--
{!! Form::open(['route' => 'admin.locations.import', 'class' => '', 'role' => 'form', 'method' => 'post',  'files' => true]) !!}

{!! Form::hidden('owner_id', (!empty(auth()) ? auth()->user()->id : '')) !!}
<div class="form-group">                                
    <div class="btn btn-success btn-file">
    <i class="fa fa-paperclip"></i> Attachment
    <input type="file" name="file"/>
    </div>
    <p class="help-block">Max. 32MB</p>
{!! Form::submit( 'Upload', ['class' => 'btn btn-secondary']) !!}
</div>
{!! Form::close() !!}
-->
@push('scripts')
<script type="text/javascript">
(function ($) {
/*
     Generic are you sure dialog
     */
    $('form[name=delete_all]').submit(function(){
        return confirm("Are you sure you want to delete all?\nThis will reset all data except projects for the whole database.\nIncluding answers, status and translations.");
    });    
    
}(jQuery));
</script>
@endpush
@include('includes.partials.locations_table')    
@stop