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
        {!! Form::open(['route' => 'admin.locations.search', 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'get']) !!}
        <div class="form-inline col-xs-2">
            <div class="input-group hidden">
                <div class="form-inline">
                    <label class="control-label pull-left" for="search_by"> Search By -&nbsp;</label>
                {!! Form::select('search_by', ['village' => 'Village', 
                                            'township' => 'Township', 
                                            'district' => 'District', 
                                            'state' => 'State'], 
                                            Input::get('search_by')? Input::get('search_by'):'village', 
                                            ['class' => 'form-control disable', 'disable']) !!}
                </div>
            </div>
            <div class="input-group">
                
                <input name="q" class="form-control" placeholder="{!! Input::get('q')? Input::get('q'):'Search' !!}" type="text">
                <span class="input-group-btn">
                    <button type="submit" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
        
        </div>
        {!! Form::close() !!}
        <div class="button">
        <a href="{{route('admin.locations.import')}}" class="btn btn-primary">Import</a>
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
               
@include('includes.partials.locations_table')    
@stop