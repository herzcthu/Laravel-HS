@extends ('backend.layouts.master')

@section ('title', 'Question Management | Edit Question')
@section ('meta')
    <meta name="csrf-token" content="{!! csrf_token()!!}"/>
@endsection
@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section ('after-scripts-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
        Question Management
        <small>Edit Question</small>
    </h1>
@endsection

@section ('breadcrumbs')
     <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
     <li>{!! link_to_route('admin.projects.index', 'Project Management') !!}</li>
     <li>{!! link_to_route('admin.project.questions.index', 'Question Management', $project->id) !!}</li>
     <li>{!! link_to_route('admin.project.questions.editall', 'Edit '.substr($project->name,0,10). '...', $project->id) !!}</li>
@stop

@section('content')
<h4>{{$project->name}}</h4>
    <div class="row">
    <div class="col-md-12">
    <div class="pull-left" style="margin-bottom:10px">
        <div class="btn-group">
            <a href='{{ route('admin.project.questions.create', [$project->id])}}' class="btn btn-md btn-primary"><i class="fa fa-question"></i><i class="fa fa-plus" data-toggle="tooltip" data-placement="top" title="Add New Question"></i> Add Question</a>
        </div>
        
    </div> 
    </div>
</div>    

    <div class="clearfix"></div>   


    {!! Form::open(['route' => ['admin.project.questions.editall', $project->id], 'class' => 'form-horizontal', 'question' => 'form', 'method' => 'PATCH']) !!}
        @if(is_array($project->sections))
            @foreach($project->sections as $section_key => $section)
            <fieldset>
                <legend>{!! $section->text !!}</legend>
                @if(!empty($section->desc))
                
                <p class="text-bold text-muted">{!! $section->desc !!}</p>
                
                @endif
                @if(count($project->questions) > 0 )
                <div class="panel-group" id="accordion{{ $section_key }}">
                    <span id="message"></span>
                    @foreach($project->questions->sortBy('sort', SORT_NATURAL) as $question)
                        @if(empty($question->related_data->q) && $question->related_data->type != 'parent')                            
                        
                            @if($section_key == $question->section)

                              <div class="panel panel-default sortable" id="listid-{{ $question->id }}">
                                <div class="panel-heading">
                                  <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion{{ $section_key }}" href="#collapse{{ $question->id}}">
                                    +</a>
                                      <div class="row">
                                        <div class="col-lg-1 pull-right">
                                        {!! $question->action_buttons !!}
                                        </div>
                                        @if((isset($question->display->qnum) && $question->display->qnum == 0) || empty($question->display))
                                        <label class="col-lg-1 control-label">{!! $question->qnum !!}</label>
                                        @endif
                                        @if((isset($question->display->question) && $question->display->question == 0) || empty($question->display))
                                        <div class="col-lg-10">
                                            <div class="form-control-static">
                                            {!! $question->question !!}
                                            </div>
                                        </div>
                                        @endif
                                      </div>
                                    
                                  </h4>
                                </div>

                                <div id="collapse{{ $question->id}}" class="panel-collapse collapse">
                                  <div class="panel-body">
                                    <div class="form-group {!! Aio()->section($section->column) !!}">                                                            

                                    <label class="col-lg-1 control-label">&nbsp;</label>
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
                                                    @if($key <= ceil(($question->qanswers->count() / 3))+1)
                                                    {!! Form::answerField($question, $answer, $question->qnum, $key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key == ceil(($question->qanswers->count() / 3))+1)
                                                    </div>
                                                    <div class="col-xs-4 col-lg-4">
                                                    @endif
                                                    @if($key > ceil(($question->qanswers->count() / 3)+1) && $key <= ceil(($question->qanswers->count() / 3) * 2)+1)
                                                    {!! Form::answerField($question, $answer, $question->qnum, $key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key == ceil(($question->qanswers->count() / 3) * 2)+1)
                                                    </div>
                                                    <div class="col-xs-4 col-lg-4">
                                                    @endif
                                                    @if($key > ceil(($question->qanswers->count() / 3) * 2)+1 && $key < $question->qanswers->count())
                                                    {!! Form::answerField($question, $answer, $question->qnum, $key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key + 1 == ($question->qanswers->count()) )
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
                                </div>
                                </div>
                              </div>
                            @endif
                        @endif
                    @endforeach   
                    </div>
                @endif
            </fieldset>    
            @endforeach
        @else
            @if(count($project->questions) > 0 )
            <div class="panel-group" id="accordion">
                <span id="message"></span>
                @foreach($project->questions as $question)
                    @if(empty($question->related_data->q) && $question->related_data->type != 'parent') 
                    <div class="panel panel-default sortable" id="listid-{{ $question->id }}">
                                <div class="panel-heading">
                                  <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $question->id}}">
                                    +</a>
                                      <div class="row">
                                      <label class="col-lg-1 control-label">{!! $question->qnum !!}</label>
                                        <div class="col-lg-11">
                                            <div class="form-control-static">
                                            {!! $question->question !!} 
                                                    <div class="col-lg-1 pull-right">
                                                    {!! $question->action_buttons !!}
                                                    </div>
                                            </div>
                                        </div>
                                      </div>
                                  </h4>
                                </div>
                        
                    <div id="collapse{{ $question->id}}" class="panel-collapse collapse">
                        <div class="panel-body">
                    <div class="form-group">

                            <label class="col-lg-1 control-label">&nbsp;</label>
                            <div class="col-lg-11">
                                <div class="form-control-static">
                                @if($question->qanswers->count() > 0 )
                                    @foreach(Aio()->sortNatural($question->qanswers, 'akey') as $key => $answer)
                                        @if($question->answer_view == 'horizontal')
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
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach 
            </div>
            @endif
        @endif
        <div class="pull-left">
            <a href="{{route('admin.projects.index')}}" class="btn btn-danger">Cancel</a>
        </div>

        <div class="pull-right">
            <input type="submit" class="btn btn-success" value="Save" />
        </div>
        <div class="clearfix"></div>

    {!! Form::close() !!}
    @push('scripts')
    
    <script type="text/javascript">
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	if (typeof ajaxURL === 'undefined') {
		var ajaxURL = ems.url;
	}
	$(document).ready(function ($) {
            $("#accordion").sortable({
			cursor: 'move',
			axis: 'y',
			update: function (event, ui) {
				var order = $(this).sortable("serialize");


				//send ajax request
				$.ajax({
					url    : ajaxURL,
					type   : 'POST',
					data   : order,
					success: function (data) {
						if(data.success){
							$("#message").html('Sorted');
							$("#message").addClass('text-green');
						}else{
							$("#message").html('Something wrong');
							$("#message").addClass('text-red');
						}
					}

				});
			}
		});
            @foreach($project->sections as $section_key => $section)
		$("#accordion{{$section_key}}").sortable({
			cursor: 'move',
			axis: 'y',
			update: function (event, ui) {
				var order = $(this).sortable("serialize");


				//send ajax request
				$.ajax({
					url    : ajaxURL,
					type   : 'POST',
					data   : order,
					success: function (data) {
						if(data.success){
							$("#message").html('Sorted');
							$("#message").addClass('text-green');
						}else{
							$("#message").html('Something wrong');
							$("#message").addClass('text-red');
						}
					}

				});
			}
		});
            @endforeach
	});
</script>
    @endpush
@stop