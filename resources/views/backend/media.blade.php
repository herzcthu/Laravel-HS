@extends ('backend.layouts.master')

@section ('title', 'Media Management | Media Library')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
        Media Management
        <small>Media Library</small>
    </h1>
@endsection

@section ('breadcrumbs')
    <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li>{!! link_to_route('admin.media.index', 'Media Management') !!}</li>
    <li class="active"></li>
@stop

@section('content')
{!! Form::open(['route' => 'admin.media.upload', 'class' => '', 'role' => 'form', 'method' => 'post',  'files' => true]) !!}

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

        
@include('includes.partials.medialist_table')    
@stop