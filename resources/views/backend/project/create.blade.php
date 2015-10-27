@extends ('backend.layouts.master')

@section ('title', 'Project Management | Create Project')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
        Project Management
        <small>Create Project</small>
    </h1>
@endsection

@section ('breadcrumbs')
     <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
     <li>{!! link_to_route('admin.projects.index', 'Project Management') !!}</li>
     <li class="active">{!! link_to_route('admin.projects.create', 'Project Management') !!}</li>
@stop

@section('content')
    @include('backend.project.includes.partials.header-buttons')
@if(count($organizations) <= 0) 
<div class="alert alert-danger">
    Create at least one organization before creating new project. <a href="{{route('admin.access.organizations.create')}}">Create Organization</a>
</div>
@endif
    {!! Form::open(['route' => 'admin.projects.store', 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'post']) !!}

        <div class="form-group">
            <label class="col-lg-2 control-label">Name</label>
            <div class="col-lg-10">
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Full Name']) !!}
            </div>
        </div><!--form control--> 
        <div class="form-group">
            <label class="col-lg-2 control-label">Description</label>
            <div class="col-lg-10">
                {!! Form::text('desc', null, ['class' => 'form-control', 'placeholder' => 'Full Name']) !!}
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">Project Type</label>
            <div class="col-lg-10">
                {!! Form::select('type', ['checklist' => 'Check List', 'incident' => 'Incident Form'], null, ['class' => 'form-control', 'placeholder' => 'Full Name']) !!}
            </div>
        </div><!--form control--> 
        <div class="form-group">
            <label class="col-lg-2 control-label">Validate Method</label>
            <div class="col-lg-10">
                {!! Form::select('validate', ['' => '', 'person' => 'Observer ID','pcode' => 'Custom Location Code'], null, ['class' => 'form-control', 'placeholder' => 'Full Name']) !!}
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">Form Sections</label>
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-xs-2"><label class="control-label">Section Name</label></div>
                    <div class="col-xs-1"><label class="control-label">Column</label></div>
                    <div class="col-xs-2"><label class="control-label">Formula</label></div>
                    <div class="col-xs-3"><label class="control-label">Descriptions</label></div>
                    <div class="col-xs-1"><label class="control-label">Report</label></div>
                    <div class="col-xs-1"><label class="control-label">Show Submit</label></div>
                    <div class="col-xs-2"></div>
                </div>
                <div id="sectForm">
                    <div class="form-group">
                    <div class="col-xs-2">
                        {!! Form::text('sections[0][text]', null, ['class' => 'form-control', 'placeholder' => 'Answer']) !!}                     
                    </div>
                    <div class="col-xs-1">
                        {!! Form::select('sections[0][column]',[   '1' => '1',
                                                                '2' => '2',
                                                                '3' => '3',
                                                                '4' => '4',
                                                                '5' => '5',
                                                                '6' => '6'], null, ['class' => 'form-control', 'placeholder' => 'Input Type']) !!}  
                    </div>
                    <div class="col-xs-2">
                        {!! Form::text("sections[0][formula]", (isset($section->formula)? $section->formula:null), ['class' => 'form-control', 'placeholder' => 'DA>(BE+BF)']) !!}                     
                    </div>
                    <div class="col-xs-3">
                        {!! Form::textarea("sections[0][desc]", null, ['rows' => '3', 'class' => 'form-control', 'placeholder' => 'Some text to display on the top of section.']) !!}
                    </div>
                    <div class="col-xs-1">
                        {!! Form::checkbox("sections[0][report]", 1, null, ['class' => 'checkbox']) !!}
                    </div>
                    <div class="col-xs-1">
                        {!! Form::checkbox("sections[0][submit]", 1, true, ['class' => 'checkbox']) !!}
                    </div>    
                    <div class="col-xs-2">
                        <button type="button" class="btn btn-default addSectButton"><i class="fa fa-plus"></i></button>                    
                    </div>
                    </div>
                </div>
            </div>
            
        </div><!--form control-->         
        <div class="form-group">
            <label class="col-lg-2 control-label">Reporting</label>
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-xs-2"><label class="control-label">Report Screen</label></div>
                    <div class="col-xs-2"><label class="control-label"></label></div>
                    <div class="col-xs-2"></div>
                </div>
                <div id="reportForm">
                    <div class="form-group">
                    <div class="col-xs-2">
                        {!! Form::text('reporting[0][text]', null, ['class' => 'form-control', 'placeholder' => 'Answer']) !!}                     
                    </div>
                    <div class="col-xs-2">
                        <button type="button" class="btn btn-default addReportButton"><i class="fa fa-plus"></i></button>                    
                    </div>
                    </div>
                </div>
            </div>
            
        </div><!--form control--> 
        @if (count($projects) > 0)
        <div class="form-group">
            <label class="col-lg-2 control-label">Main Project</label>
            <div class="col-lg-10">
                <select name="project" class="form-control" id="porganization">
                    <option value="none" data-project="">None </option>
                    @foreach($projects as $project)
                    <option value="{{ $project->id }}" data-project="">{{ $project->name }} </option>
                    @endforeach
                </select>
                
            </div>
        </div><!--form control-->
        @endif

        @if (count($organizations) > 0)
        <div class="form-group">
            <label class="col-lg-2 control-label">Organization</label>
            <div class="col-lg-10">
                <select name="organization" class="form-control" id="porganization">
                    @foreach($organizations as $organization)
                    <option value="{{ $organization->id }}" data-organization="">{{ $organization->name }} </option>
                    @endforeach
                </select>
                
            </div>
        </div><!--form control-->
        @endif
        <div class="form-group" id="ajax_insert">
            
        </div><!--form control-->
        

        <div class="pull-left">
            <a href="{{route('admin.projects.index')}}" class="btn btn-danger">Cancel</a>
        </div>

        <div class="pull-right">
            <input type="submit" class="btn btn-success" value="Save" />
        </div>
        <div class="clearfix"></div>

    {!! Form::close() !!}
@endsection
@include('backend.project.includes.partials.footer-script')