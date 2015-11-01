@extends ('backend.layouts.master')

@section ('title', 'Question Management | Create Question')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
        Project Management
        <small>Create Question</small>
    </h1>
@endsection

@section ('breadcrumbs')
    <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> {{ _t('Dashboard') }}</a></li>
    <li>{!! link_to_route('admin.projects.index', _t('Project Management')) !!}</li>
    <li>{!! link_to_route('admin.project.questions.index', _t('Question Management'), [$project->id]) !!}</li>
    <li>{!! link_to_route('admin.project.questions.create', _t('Create Question'), [$project->id]) !!}</li>
@stop

@section('content')
    @include('backend.project.includes.partials.header-buttons')
       
    {!! Form::open(['route' => ['admin.project.questions.store', $project->id], 'class' => 'form-horizontal', 'question' => 'form', 'method' => 'post']) !!}
    @if(!empty($project))
        {!! Form::hidden('project_id', $project->id) !!}
        {!! Form::hidden('organization', $project->organization->id) !!}
        <div class="form-group">
            <label class="col-lg-2 control-label">Project</label>
            <div class="col-lg-10">
                <label class="control-label">{{ $project->name }} </label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-2 control-label">Organization</label>
            <div class="col-lg-10">
                <label class="control-label">{{ $project->organization->name }} </label>
            </div>
        </div>
        @if($project->sections)
        <div class="form-group">
            <label class="col-lg-2 control-label">Section</label>
            <div class="col-lg-10">
                {!! Form::select('section', (Aio()->createSelectBoxEntryFromArray($project->sections, 'text')),null, ['class' => 'form-control', 'placeholder' => 'Question Number']) !!}
            </div>
        </div><!--form control-->
        @endif
    @else
        @if (count($projects) > 0)
        <div class="form-group">
            <label class="col-lg-2 control-label">Project</label>
            <div class="col-lg-10">
                <select name="project_id" class="form-control" id="porganization">
                    <option value="none" data-project="">None </option>
                    @foreach($projects as $project)
                    <option value="{{ $project->id }}" data-project="" {!! isset($project_id)? 'selected':'' !!}>{{ $project->name }} </option>
                    @endforeach
                </select>
                
            </div>
        </div><!--form control-->
        @endif
    @endif
        <div class="form-group">
            <label class="col-lg-2 control-label">Question Number</label>
            <div class="col-lg-10">
                {!! Form::text('qnum', null, ['id' => 'qnum','class' => 'form-control', 'placeholder' => 'Question Number']) !!}
                <div class="col-lg-4 checkbox">
                    <label class='control-label'>
                    {!! Form::checkbox('display[qnum]',1, null, ['class' => '']) !!}
                    Hide quesiton number in form</label>
                </div> 
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">Question</label>
            <div class="col-lg-10">
                {!! Form::text('question', null, ['class' => 'form-control', 'placeholder' => 'Question']) !!}
                <div class="col-lg-4 checkbox">
                    <label class='control-label'>
                    {!! Form::checkbox('display[question]',1, null, ['class' => '']) !!}
                    Hide quesiton in form</label>
                </div>
            </div>
        </div><!--form control-->
        @if(is_array($project->reporting))
        <div class="form-group">
            <label class="col-lg-2 control-label">Report to</label>
            <div class="col-lg-10">
                {!! Form::select('report', aio()->addNone(aio()->createSelectBoxEntryFromArray($project->reporting, 'text')), null, ['class' => 'form-control']) !!}
            </div>
        </div>
        @endif
        @if(count($questions) > 0 )
        <div class="form-group">
            <label class="col-lg-2 control-label">Question Relation</label>
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-xs-2">
                        <label class="control-label">Question Number</label>
                    </div>
                    <div class="col-xs-2">
                        <label class="control-label">Relation</label>
                    </div>
                    <div class="col-xs-2">
                        <label class="control-label">Answer Number</label>
                    </div>                    
                    <div class="col-xs-2">
                        <label class="control-label">Optional Value</label>
                    </div>
                </div>
                <div class="row">
                        <div class="col-xs-2">
                        {!! Form::select('related_data[q]', $questions->lists('qnum', 'id'),null, ['class' => 'form-control', 'placeholder' => 'Question Number']) !!}
                    </div>
                    <div class="col-xs-2">
                        {!! Form::select('related_data[type]',[ 'none' => 'None',
                                                                'parent' => 'Related Question', 
                                                                'yes' => 'Yes', 
                                                                'no' => 'No', 
                                                                'equal' => 'Equal', 
                                                                'greater' => 'Greater', 
                                                                'less' => 'Less'], null, ['class' => 'form-control', 'placeholder' => 'Relation Method']) !!}
                    </div>    
                    <div class="col-xs-2">
                        {!! Form::text('related_data[a]', null, ['class' => 'form-control', 'placeholder' => 'Answer Number']) !!}
                    </div>                    
                    <div class="col-xs-2">
                        {!! Form::text('related_data[option]', null, ['class' => 'form-control', 'placeholder' => 'Optional Value']) !!}
                    </div>
                    </div>
            </div>
        </div>
        @endif
        <div class="form-group" id="answersgroup">
            <label class="col-lg-2 control-label">Answers</label>
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-lg-4 checkbox">
                    <label class='control-label'>
                    {!! Form::checkbox('sameanswer', 1, null, ['class' => '']) !!}
                    Same Answer Allow</label>
                    </div>    
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <label class="control-label">Answer View</label>
                        {!! Form::select('answer_view', [
                        'none' => 'None',
                        'two-column' => 'Two Column',
                        'three-column' => 'Three Column',
                        'horizontal' => 'Horizontal',
                        'vertical' => 'Vertical',
                        'table-horizontal' => 'Table Horizontal',
                        'table-vertical' => 'Table Vertical',
                        ], null, ['class' => 'form-control']) !!}
                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-1"><label class="control-label">ID</label></div>
                    <div class="col-xs-2"><label class="control-label">Answer</label></div>
                    <div class="col-xs-2"><label class="control-label">Input Type</label></div>
                    <div class="col-xs-1"><label class="control-label">Value</label></div>
                    <div class="col-xs-2"><label class="control-label">CSS Class</label></div>
                    <div class="col-xs-2"><label class="control-label">Required Answer</label></div>
                    <div class="col-xs-2"></div>
                </div>
                <div id="duplicatedForm">
                <div class="form-group ansid" id="answer_0" data-index="0">
                    <div class="col-xs-1 form-control-static">
                        _QN_a0
                    </div>
                    <div class="col-xs-2">
                        {!! Form::text('answers[_QN_a0][text]', null, ['class' => 'form-control', 'placeholder' => 'Answer']) !!}                     
                    </div>
                    <div class="col-xs-2">
                        {!! Form::select('answers[_QN_a0][type]',['text' => 'Text', 
                                                            'radio' => 'Radio', 
                                                            'checkbox' => 'Checkbox',
                                                            'select' => 'Select',
                                                            'textarea' => 'Textarea',
                                                            'question' => 'Question (Not work)',
                                                            'number' => 'Number', 
                                                            'datetime' => 'Datetime', 
                                                            'date' => 'Date', 
                                                            'time' => 'Time', 
                                                            'week' => 'Week', 
                                                            'month' => 'Month',
                                                            'year' => 'Year'], null, ['class' => 'form-control', 'placeholder' => 'Input Type']) !!}  
                    </div>
                    <div class="col-xs-1">
                        {!! Form::text('answers[_QN_a0][value]', null, ['class' => 'form-control', 'placeholder' => 'Value']) !!}
                    </div>
                    <div class="col-xs-2">
                        {!! Form::text('answers[_QN_a0][css]', null, ['class' => 'form-control', 'placeholder' => 'validate']) !!}
                    </div>    
                    <div class="col-xs-2">
                        {!! Form::text('answers[_QN_a0][require]', null, ['class' => 'form-control', 'placeholder' => 'Required Answer']) !!}
                    </div>
                    <div class="col-xs-2">
                        <button type="button" class="btn btn-default addButton"><i class="fa fa-plus"></i></button>                    
                    </div>
                    </div>
                </div>
            </div>
            
        </div><!--form control-->
    

        <div class="pull-left">
            <a href="{{route('admin.project.questions.editall',[$project->id])}}" class="btn btn-danger">Cancel</a>
        </div>

        <div class="pull-right">
            <input type="submit" class="btn btn-success" value="Save" />
        </div>
        <div class="clearfix"></div>

    {!! Form::close() !!}
    
@endsection
@include('backend.project.questions.includes.partials.footer-script')    
@stop