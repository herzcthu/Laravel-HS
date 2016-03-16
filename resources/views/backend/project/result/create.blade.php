@extends ('backend.layouts.master')

@section ('title', 'Result Management | Create Result')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
        Result Management
        <small>Create Result</small>
    </h1>
@endsection

@section ('breadcrumbs')
     <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
     <li>{!! link_to_route('admin.projects.index', 'Project Management') !!}</li>
     <li>{!! link_to_route('admin.project.questions.index', 'Create '.$project->name. ' Results', $project->id) !!}</li>
@stop

@section('content')
    @include('backend.project.includes.partials.header-buttons')
            <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title">
                                     Project Validation
                                </div>
                            </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="col-xs-2">
                            @if($project->type == 'incident')
                                {!! Form::open(['route' => ['data.project.results.section.store', $project->id, 'incident'], 'class' => 'form-horizontal', 'result' => 'form', 'method' => 'post']) !!}
                                {!! Form::hidden('project_id', $project->id) !!}
                                {!! Form::hidden('org_id', $project->organization->id) !!}
                                {!! Form::hidden('validator_id', null,['class' => 'hidden-validator']) !!}
                                {!! Form::label('qnum', _('Checklist Question Number'), ['class'=>'control-label']) !!}
                                {!! Form::select('qnum', $project->parent->questions->lists('qnum','id'), null, ['class'=>'form-control']) !!}
                                {!! Form::label('incident_id', _('Incident Number'), ['class'=>'control-label']) !!}
                                {!! Form::text('incident_id',null,['class'=>'form-control', 'placeholder'=>'Incident ID', 'id'=>'Incident ID']) !!}
                            @endif
                            {!! Form::label('validator', 'Location Code', ['class'=>'control-label']) !!}
                            {!! Form::text('validator',null,['class'=>'form-control', 'placeholder'=>'PCODE', 'id'=>'validator']) !!}
                        </div>
                        <div class="col-xs-5">
                            @if(is_array($project->sections))
                                @foreach($project->sections as $section_key => $section)
                                <a href="#{{$section_key}}">{!! _t($section->text) !!}</a><br>
                                @endforeach
                            @endif    
                        </div>
                        <div id="validated" class="col-xs-5">
                            
                        </div>
                    </div>                    
                </div>
                <div class="panel-footer">
                    
                </div>
            </div>
        @if(is_array($project->sections))
            @foreach($project->sections as $section_key => $section)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        {!! $section->text !!}
                    </div>
                    
                @if(!empty($section->desc))
                
                <span class="text-bold text-muted">{!! $section->desc !!}</span>
                
                @endif
                </div>
                <div class="panel-body">
                @if($project->type == 'checklist')
                    {!! Form::open(['route' => ['admin.project.results.section.store', $project->id, $section_key], 'class' => 'form-horizontal', 'result' => 'form', 'method' => 'post']) !!}
                    {!! Form::hidden('project_id', $project->id) !!}
                    {!! Form::hidden('org_id', $project->organization->id) !!}
                    {!! Form::hidden('validator_id', '',['class' => 'hidden-validator']) !!}
                    <div class="row">
                        <div class="col-xs-1 pull-right">
                        <input type="submit" class="btn btn-success" value="Save" />
                        </div>
                    </div>
                @endif
                @if(count($project->questions) > 0 )
                    @foreach(Aio()->sortNatural($project->questions, 'qnum') as $question)
                        @if(empty($question->related_data->q) && $question->related_data->type != 'parent')                            
                        
                            @if($section_key == $question->section)

                            <div class="form-group quest {!! aio()->section($section->column) !!}">
                                @if((isset($question->display->qnum) && $question->display->qnum == 0) || empty($question->display))
                                <label class="col-lg-1 control-label">{!! $question->qnum !!}</label>
                                @endif
                                @if((isset($question->display->question) && $question->display->question == 0) || empty($question->display))
                                <div class="col-lg-11">
                                    <div class="form-control-static">
                                    {!! $question->question !!}
                                    </div>
                                </div>
                                @endif                            
                                    
                                    <label class="col-lg-1 control-label"><span class=""><input type="button" class="reset btn btn-xs btn-warning" value="Reset"/></span></label>
                                    <div class="col-lg-11">
                                        @if($question->qanswers->count() > 0 )
                                            @foreach($question->qanswers->sortBy('akey', SORT_NATURAL) as $key => $answer)
                                                @if($question->answer_view == 'two-column')
                                                    @if($key == 0)
                                                    <div class="col-xs-6">
                                                    @endif    
                                                    @if($key >= 0 && $key < ceil(($question->qanswers->count() / 2)))
                                                    {!! Form::answerField($question, $answer, $question->qnum, $key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key == ceil(($question->qanswers->count() / 2)))
                                                    </div>
                                                    <div class="col-xs-6">
                                                    @endif
                                                    @if($key >= ceil(($question->qanswers->count() / 2)) && $key < $question->qanswers->count())
                                                    {!! Form::answerField($question, $answer, $question->qnum, $key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key == ($question->qanswers->count() - 1) )
                                                    </div>
                                                    @endif
                                                @elseif($question->answer_view == 'three-column')
                                                    @if($key == 0)
                                                    <div class="col-xs-4 col-lg-4">
                                                    @endif    
                                                    @if($key <= ceil(($question->qanswers->count() / 3)))
                                                    {!! Form::answerField($question, $answer, $question->qnum, $key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key == ceil(($question->qanswers->count() / 3)))
                                                    </div>
                                                    <div class="col-xs-4 col-lg-4">
                                                    @endif
                                                    @if($key > ceil(($question->qanswers->count() / 3)) && $key <= ceil($question->qanswers->count() / 3))
                                                    {!! Form::answerField($question, $answer, $question->qnum, $key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key == ceil($question->qanswers->count() / 3))
                                                    </div>
                                                    <div class="col-xs-4 col-lg-4">
                                                    @endif
                                                    @if($key > ceil($question->qanswers->count() / 3) && $key <= $question->qanswers->count())
                                                    {!! Form::answerField($question, $answer, $question->qnum, $key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key == ($question->qanswers->count()) )
                                                    </div>
                                                    @endif    
                                                @elseif($question->answer_view == 'horizontal')
                                                <div class="col-xs-{!! Aio()->getColNum($question->qanswers->count()) !!}">
                                                {!! Form::answerField($question, $answer, $question->qnum, $key, null,['class' => "form-control"]) !!} 
                                                </div>
                                                @else
                                                {!! Form::answerField($question, $answer, $question->qnum, $key, null,['class' => "form-control"]) !!} 
                                                @endif
                                            @endforeach                        
                                        @endif
                                    </div>

                            </div>

                            @endif
                        @endif
                    @endforeach        
                @endif
                @if($project->type == 'checklist')
                    <div class="row">
                        <div class="col-xs-1 pull-right">
                        <input type="reset" class="btn btn-warning" value="Reset All" />
                        <input type="submit" class="btn btn-success" value="Save" />
                        </div>
                    </div>
                    {!! Form::close() !!}
                @endif
                </div>
                <div class="panel-footer">
                    {!! $section->text !!} (Section End)
                </div>
            </div><!-- panel end -->    
            @endforeach
            @if($project->type == 'incident')
                
                    <div class="pull-right">                        
                        <input type="reset" class="btn btn-warning" value="Reset All" />
                        <input type="submit" class="btn btn-success" value="Save" />
                    </div>
                {!! Form::close() !!}
            @endif
        @else
        {!! Form::open(['route' => ['admin.project.results.store', $project->id], 'class' => 'form-horizontal', 'result' => 'form', 'method' => 'post']) !!}
    
            @if(count($project->questions) > 0 )
                @foreach($project->questions as $question)
                    @if(empty($question->related_data->q) && $question->related_data->type != 'parent') 
                    <div class="form-group">

                        <label class="col-lg-1 control-label">{!! $question->qnum !!}</label>
                        <div class="col-lg-11">
                            <div class="form-control-static">
                            {!! $question->question !!}
                            </div>
                        </div>

                            <label class="col-lg-1 control-label">&nbsp;</label>
                            <div class="col-lg-11">
                                <div class="form-control-static">
                                @if(count($question->answers) > 0 )
                                    @foreach($question->qanswers->sortBy('akey', SORT_NATURAL) as $key => $answer)
                                        @if($question->answer_view == 'horizontal')
                                        <div class="col-xs-{!! Aio()->getColNum(count($question->answers)) !!}">
                                        {!! Form::answerField($question, $answer, $question->qnum, $key, null,['class' => "form-control"]) !!} 
                                        </div>
                                        @else
                                        {!! Form::answerField($question, $answer, $question->qnum, $key, null,['class' => "form-control"]) !!} 
                                        @endif
                                    @endforeach                        
                                @endif
                                </div>
                            </div>

                    </div>
                    @endif
                @endforeach        
            @endif
            <div class="pull-right">
                <input type="submit" class="btn btn-success" value="Save" />
            </div>
            {!! Form::close() !!}
        @endif
        <div class="pull-left">
            <a href="{{route('admin.projects.index')}}" class="btn btn-danger">Cancel</a>
        </div>

        
        <div class="clearfix"></div>

    
@stop
@include('backend.project.result.includes.partials.footer-script')    
