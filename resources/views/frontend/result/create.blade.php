@extends ('frontend.layouts.master')

@section ('title', 'Result Management | Create Result')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
       {{ _t('Result Management') }}
        <small>{{ _t('Create Result') }}</small>
    </h1>
@endsection

@section ('breadcrumbs')
     <li><a href="{!!route('frontend.dashboard')!!}"><i class="fa fa-dashboard"></i> {{ _t('Dashboard') }}</a></li>
     <li>{!! link_to_route('data.projects.index', _t('Project Management')) !!}</li>
     <li>{!! link_to_route('data.project.results.create', _t('Create ').$project->name. _t(' Results'), $project->id) !!}</li>
@stop

@section('content')
        <div class="row">
            <div class="col-xs-1 col-lg-12">
                @if($project->type == 'checklist')
                    <a href="{{route('data.project.status.index',[$project->id])}}" class="btn btn-success">{{ _t('Go to status list.') }}</a>
                @endif
                @if($project->type == 'incident')
                    <a href="{{route('data.project.results.index',[$project->id])}}" class="btn btn-success">{{ _t('Go to incident list.') }}</a>
                @endif
            </div>
        </div>
            <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title">
                                     Project Validation
                                </div>
                            </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="col-xs-2">
                            @if($project->type == 'incident' || $project->type == 'survey')
                                {!! Form::open(['route' => ['data.project.results.section.store', $project->id, 'incident'], 'class' => 'form-horizontal', 'result' => 'form', 'method' => 'post']) !!}
                                {!! Form::hidden('project_id', $project->id) !!}
                                {!! Form::hidden('org_id', $project->organization->id) !!}
                                {!! Form::hidden('validator_id', null,['class' => 'hidden-validator']) !!}
                                @if(isset($project->parent))
                                {!! Form::label('qnum', _('Checklist Question Number'), ['class'=>'control-label']) !!}
                                
                                {!! Form::select('qnum', $project->parent->questions->sortBy('sort', SORT_NATURAL)->lists('qnum','id'), null, ['class'=>'form-control']) !!}
                                @else
                                {!! Form::hidden('qnum', null) !!}
                                @endif
                            @endif
                            {!! Form::label('validator', 'Location Code', ['class'=>'control-label']) !!}
                            {!! Form::text('validator',null,['class'=>'form-control', 'placeholder'=>'PCODE', 'id'=>'validator']) !!}
                            {!! Form::button('check',['class'=>'form-control btn btn-default','id'=>'check']) !!}
                            
                            {!! Form::label('formnum', 'Form Number', ['class'=>'control-label']) !!}
                            {!! Form::select('formnum',[''=>'none','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9'],null,['class'=>'form-control', 'id'=>'formnum']) !!}
                        </div>
                        <div class="col-xs-5 col-lg-5">
                            @if(is_array($project->sections))
                                @foreach($project->sections as $section_key => $section)
                                <a href="#linkto{{$section_key}}">{!! _t($section->text) !!}</a><br>
                                @endforeach
                            @endif    
                        </div>
                        <div id="validated" class="col-xs-5 col-lg-5">
                            
                        </div>
                    </div>                    
                </div>
                <div class="panel-footer">
                    
                </div>
            </div>
        @if(is_array($project->sections))
            @foreach($project->sections as $section_key => $section)
            <div class="panel panel-default" id="linkto{{$section_key}}">
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
                    {!! Form::open(['route' => ['data.project.results.section.store', $project->id, $section_key], 'class' => 'form-horizontal', 'result' => 'form', 'method' => 'post']) !!}
                    {!! Form::hidden('project_id', $project->id) !!}
                    {!! Form::hidden('org_id', $project->organization->id) !!}
                    {!! Form::hidden('validator_id', '',['class' => 'hidden-validator']) !!}
                    <div class="row">
                        <div class="col-xs-1 col-lg-1 pull-right">
                        <input type="submit" class="btn btn-success" value="Save" />
                        </div>
                    </div>
                @endif
                @if(count($project->questions) > 0 )
                    @foreach($project->questions->sortBy('sort', SORT_NATURAL) as $question)
                    @if(!empty($question->related_data))
                        @if(empty($question->related_data->q) && $question->related_data->type != 'parent')                            
                        
                            @if($section_key == $question->section)

                            <div id="{!! $question->qnum !!}" class="form-group quest {!! aio()->section($section->column) !!}">
                                @if((isset($question->display->qnum) && $question->display->qnum == 0) || empty($question->display))
                                <label class="col-xs-1 col-lg-1 control-label">{!! $question->qnum !!}</label>
                                @endif
                                @if((isset($question->display->question) && $question->display->question == 0) || empty($question->display))
                                <div class="col-xs-1 col-lg-11">
                                    <div class="form-control-static">
                                    {!! _t($question->question) !!}
                                    </div>
                                </div>
                                @endif                            

                                    <label class="col-xs-1 col-lg-1 control-label"><span class=""><input type="button" class="reset btn btn-xs btn-warning" value="Reset"/></span></label>
                                    <div class="col-lg-11">
                                        @if($question->qanswers->count() > 0 )
                                        <?php $key = 0 ?>
                                            @foreach($question->qanswers->sortBy('akey', SORT_NATURAL) as $answer)
                                                @if($question->answer_view == 'two-column')
                                                    @if($key == 0)
                                                    <div class="col-xs-6 col-lg-6">
                                                    @endif    
                                                    @if($key >= 0 && $key < ceil(($question->qanswers->count() / 2)))
                                                    {!! Form::answerField($question, $answer, $question->qnum, $answer->key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key == ceil(($question->qanswers->count() / 2)))
                                                    </div>
                                                    <div class="col-xs-6 col-lg-6">
                                                    @endif
                                                    @if($key >= ceil(($question->qanswers->count() / 2)) && $key < $question->qanswers->count())
                                                    {!! Form::answerField($question, $answer, $question->qnum, $answer->key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key == ($question->qanswers->count() - 1) )
                                                    </div>
                                                    @endif
                                                @elseif($question->answer_view == 'three-column')
                                                    @if($key == 0)
                                                    <div class="col-xs-4 col-lg-4">
                                                    @endif   
                                                    @if($key < ceil($question->qanswers->count() / 3))
                                                        {!! Form::answerField($question, $answer, $question->qnum, $answer->key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key == ceil($question->qanswers->count() / 3))
                                                    </div>
                                                    <div class="col-xs-4 col-lg-4">
                                                    @endif
                                                    @if($key >= ceil($question->qanswers->count() / 3) && $key < (ceil($question->qanswers->count()/3) * 2))
                                                        {!! Form::answerField($question, $answer, $question->qnum, $answer->key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key == ceil($question->qanswers->count() / 3) * 2)
                                                    </div> 
                                                    <div class="col-xs-4 col-lg-4">
                                                    @endif
                                                    @if($key >= ceil($question->qanswers->count() / 3) * 2)
                                                        {!! Form::answerField($question, $answer, $question->qnum, $answer->key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key + 1 == ($question->qanswers->count()) )
                                                    </div>
                                                    @endif    
                                                @elseif($question->answer_view == 'horizontal')
                                                <div class="col-xs-{!! Aio()->getColNum($question->qanswers->count()) !!} col-lg-{!! Aio()->getColNum($question->qanswers->count()) !!}">
                                                {!! Form::answerField($question, $answer, $question->qnum, $answer->key, null,['class' => "form-control"]) !!} 
                                                </div>
                                                @else
                                                <div class="col-xs-12">
                                                {!! Form::answerField($question, $answer, $question->qnum, $answer->key, null,['class' => "form-control"]) !!} 
                                                </div>
                                                @endif
                                                <?php $key++ ?>
                                            @endforeach                        
                                        @endif
                                    </div>

                            </div>

                            @endif
                        @endif
                    @else                        
                         {{-- if no related or parent questions --}}
                            @if($section_key == $question->section)

                            <div id="{!! $question->qnum !!}" class="form-group quest {!! aio()->section($section->column) !!}">
                                @if((isset($question->display->qnum) && $question->display->qnum == 0) || empty($question->display))
                                <label class="col-xs-1 col-lg-1 control-label">{!! $question->qnum !!}</label>
                                @endif
                                @if((isset($question->display->question) && $question->display->question == 0) || empty($question->display))
                                <div class="col-xs-1 col-lg-11">
                                    <div class="form-control-static">
                                    {!! _t($question->question) !!}
                                    </div>
                                </div>
                                @endif                            

                                    <label class="col-xs-1 col-lg-1 control-label"><span class=""><input type="button" class="reset btn btn-xs btn-warning" value="Reset"/></span></label>
                                    <div class="col-xs-1 col-lg-11">
                                        @if($question->qanswers->count() > 0 )
                                           <?php $key = 0; ?> 
                                            @foreach($question->qanswers->sortBy('akey', SORT_NATURAL) as $answer)
                                                @if($question->answer_view == 'two-column')
                                                    @if($key == 0)
                                                    <div class="col-xs-6 col-lg-6">
                                                    @endif    
                                                    @if($key >= 0 && $key < ceil(($question->qanswers->count() / 2)))
                                                    {!! Form::answerField($question, $answer, $question->qnum, $answer->key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key == ceil(($question->qanswers->count() / 2)))
                                                    </div>
                                                    <div class="col-xs-6 col-lg-6">
                                                    @endif
                                                    @if($key >= ceil(($question->qanswers->count() / 2)) && $key < $question->qanswers->count())
                                                    {!! Form::answerField($question, $answer, $question->qnum, $answer->key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key == ($question->qanswers->count() - 1) )
                                                    </div>
                                                    @endif
                                                @elseif($question->answer_view == 'three-column')
                                                    @if($key == 0)
                                                    <div class="col-xs-4 col-lg-4">
                                                    @endif    
                                                    @if($key < ceil(($question->qanswers->count() / 3)))
                                                    {!! Form::answerField($question, $answer, $question->qnum, $answer->key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key == ceil(($question->qanswers->count() / 3)))
                                                    </div>
                                                    <div class="col-xs-4 col-lg-4">
                                                    @endif
                                                    @if($key >= ceil(($question->qanswers->count() / 3)) && $key < ceil(($question->qanswers->count() / 3) * 2))
                                                    {!! Form::answerField($question, $answer, $question->qnum, $answer->key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key == ceil(($question->qanswers->count() / 3) * 2))
                                                    </div>
                                                    <div class="col-xs-4 col-lg-4">
                                                    @endif
                                                    @if($key >= ceil(($question->qanswers->count() / 3) * 2))
                                                    {!! Form::answerField($question, $answer, $question->qnum, $answer->key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key + 1  == ($question->qanswers->count()) )
                                                    </div>
                                                    @endif    
                                                @elseif($question->answer_view == 'horizontal')
                                                <div class="col-xs-{!! Aio()->getColNum($question->qanswers->count()) !!}">
                                                {!! Form::answerField($question, $answer, $question->qnum, $answer->key, null,['class' => "form-control"]) !!} 
                                                </div>
                                                @else
                                                <div class="col-xs-12">
                                                {!! Form::answerField($question, $answer, $question->qnum, $answer->key, null,['class' => "form-control"]) !!} 
                                                </div>
                                                @endif
                                                <?php $key++ ?>
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
                        <div class="col-xs-1 col-lg-1 pull-left">                                        
                            <input type="reset" class="btn btn-warning" value="Reset All" />                
                        </div>
                        <div class="col-xs-1 col-lg-1 pull-right">
                            <input type="submit" class="btn btn-success" value="Save" />
                        </div>
                    </div>
                    {!! Form::close() !!}
                @endif
                @if($project->type == 'incident' || $project->type == 'survey')
                <div class="pull-left">                                        
                    <input type="reset" class="btn btn-warning" value="Reset All" />                
                </div>
                <div class="pull-right">
                    <input type="submit" class="btn btn-success" value="Save" />
                </div>
                    {!! Form::close() !!}
                @endif
                </div>
                </div>    
                <div class="panel-footer">
                    {!! $section->text !!} (Section End)
                </div>
            </div><!-- panel end -->    
            @endforeach
            
        @else
        {!! Form::open(['route' => ['data.project.results.store', $project->id], 'class' => 'form-horizontal', 'result' => 'form', 'method' => 'post']) !!}
    
            @if(count($project->questions) > 0 )
                @foreach($project->questions as $question)
                @if(!empty($question->related_data))
                    @if(empty($question->related_data->q) && $question->related_data->type != 'parent') 
                    <div class="form-group" id="{!! $question->qnum !!}">

                        <label class="col-xs-1 col-lg-1 control-label">{!! $question->qnum !!}</label>
                        <div class="col-xs-11 col-lg-11">
                            <div class="form-control-static">
                            {!! _t($question->question) !!}
                            </div>
                        </div>

                            <label class="col-xs-1 col-lg-1 control-label"><span class=""><input type="button" class="reset btn btn-xs btn-warning" value="Reset"/></span></label>
                            <div class="col-xs-11 col-lg-11">
                                <div class="form-control-static">
                                @if($question->qanswers->count() > 0 )
                                    @foreach($question->answers as $key => $answer)
                                        @if($question->answer_view == 'horizontal')
                                        <div class="col-xs-{!! Aio()->getColNum($question->qanswers->count()) !!}">
                                        {!! Form::answerField($question, $answer, $question->qnum, $answer->key, null,['class' => "form-control"]) !!} 
                                        </div>
                                        @else
                                        <div class="col-xs-12">
                                        {!! Form::answerField($question, $answer, $question->qnum, $answer->key, null,['class' => "form-control"]) !!} 
                                        </div>
                                        @endif
                                    @endforeach                        
                                @endif
                                </div>
                            </div>

                    </div>
                    @endif
                    @endif
                @endforeach        
            @endif
            <div class="pull-right">
                <input type="submit" class="btn btn-success" value="Save" />
            </div>
            {!! Form::close() !!}
        @endif

        
        <div class="clearfix"></div>

    
@stop
@include('frontend.result.includes.partials.footer-script') 
@push('scripts')
<script type="text/javascript">
(function ($) {
    $(document).ready(function() {
            function validate(url, replacement, output){
                if(typeof output === 'undefined'){
                    output = false;
                }
                $('#validated').html("");
                $.ajax({
                    url : url,
                    dataType: 'json',
                    statusCode: {
                      404: function() {
                        $('#validated').html("<span class='text-danger'>Record not found!</span>");
                      }
                    },
                }).success(function(data, status, response){
                    $('#validator').removeClass('alert-danger');
                    
                    
                    $.each(data, function (index, record) {
                        $('<dl />').attr('id', 'record').addClass('dl-horizontal').appendTo('#validated');
                        $('<dt />').text(index).appendTo('#record');
                        $('<dd />').text(record).appendTo('#record');
                        @if($project->validate == 'pcode')
                            if(index == 'Location ID'){
                                
                                if(output){                  
                                    $('#validator').val(valid);
                                }
                                $('.hidden-validator').val($('#validator').val() + {{'-'.$project->org_id}});
                            }
                       @elseif($project->validate == 'person')
                            if(index == 'Observer'){
                                
                                if(output){                            
                                    $('#validator').val(valid);
                                }
                                $('.hidden-validator').val($('#validator').val());
                            }
                       @endif
                    });
                }).error(function(){
                    $('#validator').addClass('alert-danger');
                }).fail(function () {
                    console.log('Data could not be loaded.');
                });
            }
          var elements = document.querySelectorAll("[data-expression]");
          $(elements).each(function( index ) {
              $(this).each(function(i){
                  
                  eval($(this).data('expression'));
                  
              });
            //console.log( index );
          });
          if( $('#validator').val() ) {                
                var valid = $('#validator').val();
                var urlstr = ems.url;
                var vurl = urlstr.replace("%7Bpcode%7D", valid );
                validate(vurl, valid, true);
            }
          $('#check').on('click',function(e){
              var str = ems.url;
                  //set replacement as global variable
                  replacement = $('#validator').val();
                  var url = str.replace("%7Bpcode%7D", replacement );
                  validate(url, replacement);
          });  
          $('#validator').on('keyup',function(e){
                if (e.shiftKey && e.which == 16) {
                    $(this).val(val.replace(/\#/,''));
                  }
                  console.log(e);
              $('#validator').removeClass('alert-danger');
              var value = $(this).val();
              if( value.length > 4 && value.length < 7){ 
                  var str = ems.url;
                  //set replacement as global variable
                  replacement = value;
                  var url = str.replace("%7Bpcode%7D", replacement );
                  validate(url, replacement);
              }
          }).keydown(function( event ) {
                  if ( event.which == 16 ) {
                    event.preventDefault();
                  }
                });
          $( "form" ).submit(function(e) {
          if( !$('.hidden-validator').val() ) {
            $('#validator').addClass('alert-danger').focus();
            
            e.preventDefault();
            }
          });
        });
    //$('<input>').attr('type','hidden').appendTo('form');    
}(jQuery));
</script>
@endpush
