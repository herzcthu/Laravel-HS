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
     <li><a href="{!! url('admin/projects') !!}">Project Management</a></li>
     <li class="active"><a href="{!! url('admin/projects/create') !!}">Create Project</a></li>
@stop

@section('content')
    @include('backend.project.includes.partials.header-buttons')

    {!! Form::model($project, ['route' => ['admin.project.update', $project->id], 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'PATCH']) !!}

        <div class="form-group">
            <label class="col-lg-2 control-label">Name</label>
            <div class="col-lg-10">
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Full Name']) !!}
            </div>
        </div><!--form control--> 
        <div class="form-group">
            <label class="col-lg-2 control-label">Description</label>
            <div class="col-lg-10">
                {!! Form::text('desc', null, ['class' => 'form-control', 'placeholder' => 'Description']) !!}
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
                    
                    @if($project->sections)
                        @foreach($project->sections as $key => $section)
                        <div class="form-group">
                        <div class="col-xs-2">
                            {!! Form::text("sections[$key][text]", (isset($section->text)? $section->text:null), ['class' => 'form-control', 'placeholder' => 'Answer']) !!}                     
                        </div>
                        <div class="col-xs-1">
                            {!! Form::select("sections[$key][column]",[   '1' => '1',
                                                                    '2' => '2',
                                                                    '3' => '3',
                                                                    '4' => '4',
                                                                    '5' => '5',
                                                                    '6' => '6'], (isset($section->column)? $section->column:null), ['class' => 'form-control', 'placeholder' => 'Input Type']) !!}  
                        </div>
                        <div class="col-xs-2">
                            {!! Form::text("sections[$key][formula]", (isset($section->formula)? $section->formula:null), ['class' => 'form-control', 'placeholder' => 'DA>(BE+BF)']) !!}                     
                        </div>
                        <div class="col-xs-3">
                            {!! Form::textarea("sections[$key][desc]", (isset($section->desc)? $section->desc:null), ['rows' => '3', 'class' => 'form-control', 'placeholder' => 'Some text to display on the top of section.']) !!}
                        </div>
                        <div class="col-xs-1">
                            {!! Form::checkbox("sections[$key][report]", 1, (isset($section->report)? true:null), ['class' => 'checkbox']) !!}
                        </div>
                        <div class="col-xs-1">
                            {!! Form::checkbox("sections[$key][submit]", 1, (isset($section->submit)? true:null), ['class' => 'checkbox']) !!}
                        </div>
                        @if($key == 0)
                        <div class="col-xs-2">
                            <button type="button" class="btn btn-default addSectButton"><i class="fa fa-plus"></i></button>                    
                        </div>
                        @else
                        <div class="col-xs-2">
                            <button type="button" class="btn btn-default removeSectButton"><i class="fa fa-minus"></i></button>                    
                        </div>
                        @endif
                    </div>
                        @endforeach
                    @else
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
                    @endif
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
                    
                    @if(is_array($project->reporting))
                        @foreach($project->reporting as $key => $report)
                        <div class="form-group">
                        <div class="col-xs-2">
                            {!! Form::text("reporting[$key][text]", (isset($report->text)? $report->text:null), ['class' => 'form-control', 'placeholder' => 'Answer']) !!}                     
                        </div>
                        @if($key == 0)
                        <div class="col-xs-2">
                            <button type="button" class="btn btn-default addReportButton"><i class="fa fa-plus"></i></button>                    
                        </div>
                        @else
                        <div class="col-xs-2">
                            <button type="button" class="btn btn-default removeReportButton"><i class="fa fa-minus"></i></button>                    
                        </div>
                        @endif
                        </div>
                        @endforeach
                    @else
                    <div class="form-group">
                    <div class="col-xs-2">
                        {!! Form::text('reporting[0][text]', null, ['class' => 'form-control', 'placeholder' => 'Answer']) !!}                     
                    </div>
                    <div class="col-xs-2">
                        <button type="button" class="btn btn-default addReportButton"><i class="fa fa-plus"></i></button>                    
                    </div>
                    </div>
                    @endif
                    
                </div>
            </div>
            
        </div><!--form control-->
        
        @if (count($projects) > 0)
        <div class="form-group">
            <label class="col-lg-2 control-label">Main Project</label>
            <div class="col-lg-10">
                <select name="project" class="form-control" id="porganization">
                    <option value="none" data-project="">None </option>
                    @foreach($projects as $pj)
                        @if($pj->id != $project->id)
                        <option value="{{ $pj->id }}" data-project="" @if(isset($project->parent) && $project->parent->id == $pj->id) selected="selected" @endif>{{ $pj->name }} </option>
                        @endif
                    @endforeach
                </select>
                
            </div>
        </div><!--form control-->
        @endif

        @if (count($organizations) > 0)
        <div class="form-group">
            <label class="col-lg-2 control-label">Organization</label>
            <div class="col-lg-10">
                {!! Form::select('organization', $organizations->lists('name', 'id'), (isset($project->organization->id)? $project->organization->id:null),['class'=>'form-control', 'id'=>'porganization']) !!}
            </div>
        </div><!--form control-->
        @endif
        <div class="form-group" id="ajax_insert">
            
        </div><!--form control-->
        

        <div class="pull-left">
            <a href="{{url('admin/projects')}}" class="btn btn-danger">Cancel</a>
        </div>

        <div class="pull-right">
            <input type="submit" class="btn btn-success" value="Save" />
        </div>
        <div class="clearfix"></div>

    {!! Form::close() !!}
@endsection
@include('backend.project.includes.partials.footer-script')