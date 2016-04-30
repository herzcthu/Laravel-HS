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
            <!--a href='{{ route('admin.project.questions.create', [$project->id])}}' class="btn btn-md btn-primary"><i class="fa fa-question"></i><i class="fa fa-plus" data-toggle="tooltip" data-placement="top" title="Add New Question"></i> Add Question</a-->
        <!-- Button trigger modal -->
        <button id="launch" type="button" data-type="create" class="btn btn-primary" data-toggle="modal" data-target="#formTemplate" data-backdrop="static">
          <i class="fa fa-question"></i><i class="fa fa-plus" data-toggle="tooltip" data-placement="top" title="Add New Question"></i> Create Question
        </button>
        </div>
        
    </div> 
    </div>
</div>    

    <div class="clearfix"></div>
    <div class="panel">
        <div class="panel-body">
    {!! Form::open(['route' => ['admin.project.questions.editall', $project->id], 'class' => 'form-horizontal', 'question' => 'form', 'method' => 'PATCH']) !!}
        @if(is_array($project->sections))
            @foreach($project->sections as $section_key => $section)
            <fieldset id="fieldset{{ $section_key }}">
                @if(!empty($section->text))
                <legend>{!! $section->text !!}</legend>
                @endif
                @if(!empty($section->desc))
                
                <p class="text-bold text-muted">{!! $section->desc !!}</p>
                
                @endif
                @if(count($project->questions) > 0 )
                <div class="panel-group" id="accordion{{ $section_key }}">
                    <span id="message"></span>
                    @foreach($project->questions->sortBy('sort', SORT_NATURAL) as $question)
                        @if(empty($question->related_data) || (empty($question->related_data->q) && $question->related_data->type != 'parent'))                          
                        
                            @if($section_key == $question->section)

                              <div class="panel panel-default sortable" id="listid-{{ $question->id }}">
                                <div class="panel-heading">
                                  <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion{{ $section_key }}" href="#collapse{{ $question->id}}">
                                    +</a>
                                      <div class="row">
                                        @if((isset($question->display->qnum) && $question->display->qnum == 0) || empty($question->display))
                                        <label class="col-xs-1 control-label">{!! $question->qnum !!}</label>
                                        @endif
                                        @if((isset($question->display->question) && $question->display->question == 0) || empty($question->display))
                                        <div class="col-xs-10">
                                            <div class="form-control-static">
                                            {!! $question->question !!}
                                            </div>
                                        </div>
                                        @endif
                                        
                                        <div class="col-xs-1" style="padding-left:0px; padding-right: 0px;">
                                        {!! $question->action_buttons !!}
                                        </div>
                                      </div>
                                    
                                  </h4>
                                </div>

                                <div id="collapse{{ $question->id}}" class="panel-collapse collapse">
                                  <div class="panel-body">
                                    <div class="form-group {!! Aio()->section($section->column) !!}">                                                            

                                    <label class="col-xs-1 control-label">&nbsp;</label>
                                    <div class="col-xs-11">
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
                                                    <div class="col-xs-4 col-xs-4">
                                                    @endif    
                                                    @if($key <= ceil(($question->qanswers->count() / 3))+1)
                                                    {!! Form::answerField($question, $answer, $question->qnum, $key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key == ceil(($question->qanswers->count() / 3))+1)
                                                    </div>
                                                    <div class="col-xs-4 col-xs-4">
                                                    @endif
                                                    @if($key > ceil(($question->qanswers->count() / 3)+1) && $key <= ceil(($question->qanswers->count() / 3) * 2)+1)
                                                    {!! Form::answerField($question, $answer, $question->qnum, $key, null,['class' => "form-control"], ['class' => 'form-inline', 'wrapper' => 'div']) !!}
                                                    @endif
                                                    @if($key == ceil(($question->qanswers->count() / 3) * 2)+1)
                                                    </div>
                                                    <div class="col-xs-4 col-xs-4">
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
                    @if(empty($question->related_data) || (empty($question->related_data->q) && $question->related_data->type != 'parent'))
                    <div class="panel panel-default sortable" id="listid-{{ $question->id }}">
                                <div class="panel-heading">
                                  <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $question->id}}">
                                    +</a>
                                      <div class="row">
                                      <label class="col-xs-1 control-label">{!! $question->qnum !!}</label>
                                        <div class="col-xs-11">
                                            <div class="form-control-static">
                                            {!! $question->question !!} 
                                                    <div class="col-xs-2 pull-right">
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

                            <label class="col-xs-1 control-label">&nbsp;</label>
                            <div class="col-xs-11">
                                <div class="form-control-static">
                                <!-- this code does not work for select box -->
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
            <!--input type="submit" class="btn btn-success" value="Save" /-->
        </div>
        

    {!! Form::close() !!}
            </div>
            </div>
    <div class="clearfix"></div>
    <div class="modal fade" id="formTemplate" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <H2 id="modal-title">Create Questions</H2>
                      Fill the form, add answers and click "Save" to add question to project.

            </div><!-- Modal header -->
              <div class="modal-body">
                  <div class="row">
                      <div class="col-xs-5">
                  <FORM id="inputForm" class="form-vertical" role="form" autocomplete="off">
                        @if(is_array($project->sections))
                        <div class="form-group">
                            <label class="control-label">Section</label>
                                {!! Form::select('inputsect', (Aio()->createSelectBoxEntryFromArray($project->sections, 'text')),null, ['id' => 'inputsect', 'class' => 'form-control', 'placeholder' => 'Section Number']) !!}
                        
                        </div><!--form control-->
                        @endif
                      <div class="form-group">
                      <LABEL class="control-label" for="num">Q No.: </LABEL>
                      <INPUT class="form-control" id="num" type="text" name="inputnum" value="" />
                      </div>
                      <div class="form-group">
                      <LABEL class="control-label" for="question">Question: </LABEL>
                      <INPUT class="form-control" type="text" name="inputq"  value="" />
                      </div>
                        <div class="form-group col-xs-6">
                            <label class="control-label">Answer View: </label>
                            <select id="ansview" class="form-control" name="answer_view">
                                <option value="none">None</option>
                                <option value="two-column">Two Column</option>
                                <option value="three-column">Three Column</option>
                                <option value="horizontal">Horizontal</option>
                                <option value="vertical">Vertical</option>
                                <option value="table-horizontal">Table Horizontal</option>
                                <option value="table-vertical">Table Vertical</option>
                            </select>
                        </div>
                      <div class="form-group">
                          <div class="row">
                              <div class="col-xs-12">
                            <LABEL  class="control-label" for="type">Answer: </LABEL>
                            <button class="btn btn-sm btn-success"  id="add" type="button" value="Add" data-toggle="tooltip" data-placement="top" title="Add New Answer"/><i class="fa fa-plus"></i></button>
                              </div>
                          </div>
                      </div>
                        <div class="form-group">
                          <div class="col-xs-12">
                              <div class="row">
                      <!--LABEL for="name">Name: </LABEL-->
                      <div class="col-xs-6">
                              <INPUT type="hidden" name="fprefix" />
                              <LABEL  class="control-label" for="type">Type: </LABEL>
                              <SELECT id="ftype" class="form-control" name="ftype">                                  
                                  <OPTION value="radio">Radio</OPTION>
                                  <OPTION value="text">Text</OPTION>
                                  <OPTION value="number">Number</OPTION>
                                  <OPTION value="textarea">Textarea</OPTION>
                                  <OPTION value="checkbox">Checkbox</OPTION>  
                                  <!-- select box need to fix to work in server side 
                                  <OPTION value="select">Select</OPTION>
                                  -->
                              </SELECT>
                      </div>
                      <div class="col-xs-6">
                        <LABEL class="control-label"  for="flabel">Label: </LABEL>
                        <INPUT class="form-control"  type="text" name="flabel" />
                      </div>
                      <div class="col-xs-6">
                        <LABEL class="control-label" for="fvalue">Value: </LABEL>
                        <INPUT class="form-control" type="text" name="fvalue" />
                      </div>
                      <div class="col-xs-6 hide" id="optionlabel">
                        <LABEL class="control-label"  for="foption">Option Label: </LABEL>
                        <INPUT class="form-control"  type="text" name="foption" />
                      </div>
                              </div>
                          </div>
                      </div>
                </FORM>
                      
                </div>
                <div class="col-xs-7">
                <form id="qForm" name="qForm" autocomplete="off">
                    <div id="qgroup" class="form-group">
                        <input type="hidden" value="" name="section" data-prefix="section" data-name="section" data-label="">
                        <input type="hidden" value="" name="answer_view" data-prefix="answer_view" data-name="answer_view" data-label="">
                        <label style="padding-left: 0px;padding-right: 0px;" class="control-label col-xs-2" for="qnum" id="qnum"></label>
                        <input type="hidden" value="" name="qnum" data-prefix="qnum" data-name="qnum" data-label="">
                        <label style="padding-left: 0px;"class="col-xs-10" for="question" id="question"></label>
                        <input type="hidden" value="" name="question" data-prefix="question" data-name="question" data-label="">
                    </div>
                    <div id="answers" class="col-xs-offset-2 form-group">
                    </div> 
                </form>
                </div>
                </div>

            </div><!-- Modal body -->
            <div class="modal-footer">
                <button class="btn btn-success pull-left" id="save" type="button" value="Save" data-dismiss="modal">Save</button>
                <button class="btn btn-danger pull-left" id="resetall" type="button" class="btn btn-primary btn-xs">reset</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div><!-- Modal footer -->
          </div><!-- Modal content -->
        </div>
    </div><!-- #formTemplate -->
    
    <div class="modal fade" id="logic" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <H2 id="modal-title">Add Logic</H2>
                      

            </div><!-- Modal header -->
              <div class="modal-body">
                  <div class="row">
                    <FORM name="logicForm" data-hash="" id="logicForm" class="form-vertical" role="form" autocomplete="off">
                        <input type="hidden" id="hash" name="_hash"></input>
                        <div class="col-xs-5">
                                <h3>Left Side</h3>
                            </div>
                        <div class="col-xs-2"><h3>Operator</h3></div>
                        <div class="col-xs-5"><h3>Right Side</h3>
                            <div class="checkbox">
                                <label for="rftdiff" class="control-label">
                                    <input id="rftdiff" type="checkbox" name="rftdiff"></input>
                                    Select Question
                                </label>
                            </div>
                        </div>
                            <div class="col-xs-5">
                                <label for="lftans" class="control-label">Answer</label>
                                <select id="lftselect" name="lftans" class="form-control"></select>
                                <label for="lftval" class="control-label diffques">Optional Value</label>
                                <input type="text" name="lftval" class="form-control diffques"></input>
                            </div>
                            <div class="col-xs-2">
                            <label for="operator" class="control-label">Select</label>
                            <select id="operator" name="operator" class="form-control">                                
                                <option value="=">Equal to (=)</option>
                                <option value="skip">Skip to</option>
                                <option value=">">Greater than (>)</option>
                                <option value="<">Less than (<)</option>
                                <option value="true">Selected</option>
                            </select>                                
                            </div>
                            <div class="col-xs-5">
                                
                                <label for="rftques" class="control-label diffques">Question</label>
                                <select id="rftquess" name="rftquess" class="form-control diffques"><option value="">None</option></select>                                
                                
                                <div class="checkbox diffques">
                                <label for="showans" class="control-label">
                                    <input id="showans" type="checkbox" name="showans"></input>
                                    Answer and Value
                                </label>
                                </div>
                                <label for="rftans" class="control-label showans">Answer</label>
                                <select id="rftans" name="rftans" class="form-control showans"><option value="">None</option></select>
                                
                                <label for="rftval" class="control-label">Value</label>
                                <input type="text" name="rftval" class="form-control"></input>
                                
                            </div>
                    </FORM>                      
                  </div>
            </div><!-- Modal body -->
            <div class="modal-footer">
                <button class="btn btn-success pull-left" id="savelogic" type="submit" value="Save" data-dismiss="modal">Save</button>
                <button class="btn btn-danger pull-left" id="resetlogic" type="button" class="btn btn-primary btn-xs">reset</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div><!-- Modal footer -->
          </div><!-- Modal content -->
        </div>
    </div><!-- #logic -->
    @push('scripts')
    <script type="text/javascript">
        {{-- 
            <!-- reassign javascript global object variable name from config file for later use -->
        --}}
        var ems = {{ config('javascript.js_namespace') }};
    </script>
    <script type="text/javascript">
        var index = 0;
        
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
        if (typeof projectURL === 'undefined') {
		var projectURL = ems.projecturl;
	}
    /**
     * type = input type
     * value = input value
     * prefix = input name prefix
     * labeltext = input label
     * foption = selectbox options
     * index = input index
     */
    function add(type, value, prefix, labeltext, foption, index) {
        //Create an input type dynamically.
        var element = document.createElement("input");

        //Assign different attributes to the element.
        element.setAttribute("type", type);
        element.setAttribute("value", value);
        element.setAttribute("name", prefix);
        
        element.setAttribute("data-prefix", prefix);
        element.setAttribute("data-name", prefix + index);
        element.setAttribute("data-label", labeltext);
        element.setAttribute("class", prefix + index);

        var label = document.createElement("label");
        label.setAttribute("for", prefix + index);
        label.setAttribute("data-label", prefix + index);
        label.setAttribute("class", prefix + index + " control-label ");
        
        var del = document.createElement("button");
        del.setAttribute("class", "del btn btn-sm btn-danger " + prefix + index);
        del.setAttribute("type", "button");
        del.setAttribute("data-del", prefix + index);
        del.innerHTML = "X";
        var deldiv = document.createElement("div");
            deldiv.setAttribute("class", " col-xs-1 ")
        var answerdiv = document.getElementById("answers");

        var br = document.createElement("br");
        
        var inputgroup = document.createElement("div");
            inputgroup.setAttribute("class", "form-group");
            
        var wrapper = document.createElement("div");
        
        var typewrap = document.createElement("div");
        
        //console.log(wrapper);

        //Append the element in page (in span).
        switch(type) {
            case "radio":
                // add inputgroup div to answer div
                answerdiv.appendChild(inputgroup);
                // add wrapper div to inputgroup
                inputgroup.appendChild(wrapper);
                wrapper.setAttribute("class", " col-xs-11");
                // add typewrap div to wrapper
                wrapper.appendChild(typewrap);
                // add label to typewrap
                typewrap.appendChild(label);
                typewrap.setAttribute("class", " radio ");
                // add element to label
                label.appendChild(element);
                label.innerHTML += " " + labeltext + " (" + value + ")";
                // add del to deldiv
                deldiv.appendChild(del);
                // add deldiv to inputgroup
                inputgroup.appendChild(deldiv);
                break;
            case "checkbox":
                answerdiv.appendChild(inputgroup);
                inputgroup.appendChild(wrapper);
                wrapper.setAttribute("class", type + " col-xs-11");
                wrapper.appendChild(typewrap);
                typewrap.appendChild(label);
                typewrap.setAttribute("class", " checkbox ");
                label.appendChild(element);
                label.innerHTML += " " + labeltext + " (" + value + ")";
                deldiv.appendChild(del);
                inputgroup.appendChild(deldiv);
                break;
            case "textarea":
                var textarea = document.createElement("textarea");
                textarea.setAttribute("name", prefix + index);
                textarea.setAttribute("data-prefix", prefix);
                textarea.setAttribute("data-label", labeltext);
                textarea.className += " form-control " + prefix + index;
                label.className += "  ";
                label.innerHTML += " " + labeltext + " ";
                answerdiv.appendChild(inputgroup);
                wrapper.appendChild(label);
                wrapper.appendChild(textarea);
                wrapper.setAttribute("class", " col-xs-11");
                inputgroup.appendChild(wrapper);
                deldiv.appendChild(del);
                inputgroup.appendChild(deldiv);

                break;
            case "select":
                // get select element by id.
                var selectbox = document.getElementById(prefix + "select");
                // get div for select element
                var selectdiv = document.getElementById(prefix + "div");

                // create new div if selectdiv is null and assign ID attribute and class
                if(null === selectdiv){
                    selectdiv = document.createElement("div");
                    selectdiv.setAttribute("class", "row form-group");
                    selectdiv.setAttribute("id", prefix + "div");
                }

                // create new select element if null             
                if(null === selectbox){                
                    selectbox = document.createElement("select");
                    selectbox.setAttribute("id", prefix + "select");
                    selectbox.setAttribute("name", prefix + index);
                    selectbox.setAttribute("data-prefix", prefix);                
                    selectbox.setAttribute("data-label", labeltext);                
                    selectbox.className += " input-sm col-xs-8";

                    label.className += " col-xs-3";
                    label.innerHTML += " " + labeltext + " ";

                    selectdiv.appendChild(label);
                    selectdiv.appendChild(selectbox);
                    deldiv.appendChild(del);
                    selectdiv.appendChild(deldiv);

                    answerdiv.appendChild(selectdiv);

                }


                var option = document.createElement("option");
                console.log(selectbox);
                option.setAttribute("value", value );
                option.setAttribute("data-label", foption );
                option.innerHTML = foption;
                selectbox.appendChild(option);
                break;
            case "hidden":
                answerdiv.appendChild(element);
                break;
            default:
                answerdiv.appendChild(inputgroup);
                inputgroup.appendChild(label);
                label.className += " col-xs-3";
                inputgroup.appendChild(wrapper);
                element.className += " form-control";
                wrapper.setAttribute("class", "col-xs-8");
                wrapper.appendChild(element);
                label.innerHTML += " " + labeltext + " ";
                deldiv.appendChild(del);
                inputgroup.appendChild(deldiv);
                break;
        }
        
        
        
       // foo.appendChild(br);

    }

    function saveQuestion(form, url = '', method = 'post', hash) {
        var qna = {}, answers = {}, container;
        $.ajaxSetup({
                headers: {
                        'X-URLHASH': hash // if empty this is ems.urlhash
                }
        });
        if (typeof ajaxQURL === 'undefined') {
                if(url == '') {
                    var ajaxQURL = ems.add_question_url;
                } else {
                    var ajaxQURL = url;
                }
	}       
            
        //console.log(form);
        var j = 0, op = {};
        for ( var i = 0; i < form.elements.length; i++ ) {
           var e = form.elements[i]; //console.log(e);
           var ename = e.getAttribute("data-prefix"); //console.log(e.type);
           switch(e.type) {
               case "hidden":
                   qna[ename] =  e.getAttribute("value");
                   break;
               case "radio":                   
                   e.setAttribute("name", ename + "radio");
                   e.setAttribute("data-name", ename + j);
                   answers[ename + j] = {   "type": e.getAttribute("type"),
                                        "name" : e.getAttribute("name"),
                                        "value" : e.getAttribute("value"),
                                        "data-name" : e.getAttribute("data-name"),
                                        "text" : e.getAttribute("data-label")
                                    };
                   j++;
                   break;
               //javascript form object is "select-one" for single selection selectbox
               case "select-one":
                  // console.log(e);
                   for(var o = 0; o < e.length; o++){
                       answers[ename + j + "#" +e.options[o].value] = {
                           "type" : "option",
                           "value" : e.options[o].value,
                           "name":e.options[o].innerHTML,
                           "text" : e.options[o].getAttribute("data-label"),
                           "optional" : e.getAttribute("data-label")
                       } 
                   }
                    j++;
                    break;
               case "button":
                    break;
               default:                   
                   e.setAttribute("name", ename + j);
                   e.setAttribute("data-name", ename + j);
                   //console.log(e.getAttribute("data-name"));
                   answers[ename + j] = {   "type": e.getAttribute("type"),
                                        "name": e.getAttribute("name"),
                                        "value": e.getAttribute("value"),
                                        "data-name": e.getAttribute("data-name"),
                                        "text": e.getAttribute("data-label")
                                    };
                   j++;
                   break;
           }
        }
        qna["answers"] = answers;
        qna["_method"] = method;
        //send ajax request
        $.ajax({
                url    : ajaxQURL,
                type   : 'POST',
                //async:false,
                dataType:"json",
                data   : qna,
                success: function (data) {
                        console.log(data);
                        //location.reload(); // To Do: need to remove this function.
                }

        });
        // if no section or section value is 0, get "accordion" container
        if(typeof qna.section == "undefined" || qna.section.value == ""){
            container = document.getElementById("accordion");

            // create new container if null.
            if(null === container){
                container = '<div class="panel-group" id="accordion"><span id="message"></span></div>';
            }
        }else{
            // if section value exist, get container by section value.
            container = document.getElementById("accordion" + qna.section.value);
        }
        //console.log(container);
        console.log(JSON.stringify(qna));
        
    }
    
    
    
    function getData(url, columns) {
        var mydata;
        $.ajax({
                url    : url,
                type: 'GET',
                async:false,
                data: {columns},
                success: function (data) {
                        mydata = data;
                }

        });
        return JSON.parse(mydata);
    }
    
	$(document).ready(function ($) {
            
            $( "#logic" )
            .on('click','#savelogic', function(e){
                
                var logicForm = $("#logicForm");                
                if(!$("#rftdiff").is(":checked")) {
                    $("#lftval").val('');
                    $("#rftquess").val('');
                    $("#rftans").val('');
                }
                var container,        
                url = logicForm.attr('action'),
                hash = logicForm.data('hash'),
                method = logicForm.data('type'),
                logicdata = logicForm.serializeArray();

                if (typeof ajaxLURL === 'undefined') {
                        if(url == '') {
                            return;
                        } else {
                            var ajaxLURL = url;
                        }
                }
                if(typeof method === 'undefined')
                    var method = 'PATCH';

                logicdata.push({name: '_method', value: method},{name: 'logicdata', value: true});
                //send ajax request
                console.log(logicdata);
                $.ajax({
                        url    : ajaxLURL,
                        type: 'POST',
                        async:false,
                        data: logicdata,
                        success: function (data) {
                                $("#logicForm")[0].reset();
                                //console.log(data);
                                location.reload(); // reload page after logic added.
                        }

                });
                e.preventDefault();
            })
            .on('show.bs.modal', function (event) {
              var button = $(event.relatedTarget); // Button that triggered the modal
              console.log(button);
              var dataid = button.data('modal'); // get id for data
              var ajaxurl = button.data('href');
              
              var data = $('#'+dataid);
              var content = getData(data.data('href'), ['urlhash','qanswers']);   console.log(content);  
              var type = data.data('type');
              var lftselect = document.getElementById('lftselect');
              var rftquess = document.getElementById('rftquess');
              var rftans = document.getElementById('rftans');
              var cols = ['id','qnum','question'];
              var questions = getData(projectURL+'/questions',cols); console.log(questions);
              
              //if(typeof urlhash != 'undefined') {
                    $("#logicForm").attr('data-hash', content[0].urlhash.logic);
                    $("#hash").val(content[0].urlhash.logic);
              //  }
              if(typeof ajaxurl != 'undefined') {
                    $("#logicForm").attr('action', ajaxurl);
                }
              // console.log(questions);
              // remove all options from select box
              while (lftselect.firstChild) {
                    lftselect.removeChild(lftselect.firstChild);
                }
              //console.log(content.qanswers);
              $.each(content[0].qanswers, function( index, answer ) {
                  var option = document.createElement("option");
                    option.setAttribute("value", answer.akey );
                    option.setAttribute("data-type", answer.type );
                    option.innerHTML = answer.text + ' (' + answer.akey + ' ' + answer.type + ')';
                    lftselect.appendChild(option);
              });
              
              
              if(typeof questions !== 'undefined') {
                  // remove all options from select box
                while (rftquess.firstChild) {
                    rftquess.removeChild(rftquess.firstChild);
                }
                  $.each(questions, function( index, question ) {
                      var option = document.createElement("option");
                        option.setAttribute("value", question.qnum );
                        option.setAttribute("data-qid", question.id );
                        option.innerHTML = question.qnum;
                        rftquess.appendChild(option);
                  });
                  var anscols = ['akey','text'];
                  var qid = rftquess.options[rftquess.selectedIndex].getAttribute("data-qid");
                  var answers = getData(projectURL+'/question/'+qid+'/answers',anscols);
                  if(typeof answers !== 'undefined') {
                      // remove all options from select box
                      while (rftans.firstChild) {
                      rftans.removeChild(rftans.firstChild);
                      }
                      $.each(answers, function( index, answer ) {
                          var option = document.createElement("option");
                            option.setAttribute("value", answer.akey );
                            option.innerHTML = answer.text;
                            rftans.appendChild(option);
                      });
                  }
                  $("#logicForm")[0].reset();
                }
            })
            .on('load click change','#rftquess', function(e){
                var anscols = ['akey','text'];
                  var qid = rftquess.options[rftquess.selectedIndex].getAttribute("data-qid");
                  var answers = getData(projectURL+'/question/'+qid+'/answers',anscols);
                  if(typeof answers !== 'undefined') {
                      // remove all options from select box
                    while (rftans.firstChild) {
                    rftans.removeChild(rftans.firstChild);
                    }
                  $.each(answers, function( index, answer ) {
                      var option = document.createElement("option");
                        option.setAttribute("value", answer.key );
                        option.innerHTML = answer.text;
                        rftans.appendChild(option);
                  });
                  }
            });
            $('#rftdiff').on('change', function(){
                $('.diffques').toggle(this.checked);
            }).change();
            
            $('#showans').on('change', function(){
                $('.showans').toggle(this.checked);
            }).change();
            
            $( "#formTemplate" ).on("click", "#add", function(){                
                var inputForm = document.getElementById("inputForm");
                var fnum = inputForm.inputnum.value;
                var fq = inputForm.inputq.value;
                var ftype = inputForm.ftype.value;
                var fprefix = inputForm.fprefix.value;
                var fvalue = inputForm.fvalue.value;
                var flabel = inputForm.flabel.value;
                var foption = inputForm.foption.value;
                
                if( fnum == '' || fq == '' ) return;
                add(ftype, fvalue, fprefix, flabel, foption, index);
                index++;
            }) 
            .on("click", "#save", function(){
                var inputForm = document.getElementById("inputForm");
                var fnum = inputForm.inputnum.value;
                var fq = inputForm.inputq.value;
                var qForm = document.forms["qForm"];
                var url = qForm.action;
                var hash = qForm.dataset.hash;
                var type = $(this).data('type'); //submit button method
                if( fnum == '' || fq == '' ) return;
                
                saveQuestion(qForm, url, type, hash);
            })
            .on("select click change", "#inputsect", function(){
                var sect = $(this).val();//console.log(sect);
                if(sect !== ''){
                    $("#qForm input[name='section']").val(sect);
                }else{
                    $("#qForm input[name='section']").val('');
                }
            })
            .on("select click change", "#ansview", function(){
                var view = $(this).val();//console.log(sect);
                if(view !== ''){
                    $("#qForm input[name='answer_view']").val(view);
                }else{
                    $("#qForm input[name='answer_view']").val('');
                }
            }) 
            .on("keyup change", "#inputForm input[name='inputnum']", function(){
                var num = $(this).val();
                if(num !== ''){
                    $("#qnum").text(num + ' : ');
                    $("#qForm input[name='qnum']").val(num);
                    $("#inputForm input[name='fprefix']").val(num + "_a");
                }else{
                    $("#qnum").text('');
                    $("#qForm input[name='qnum']").val('');
                    $("#inputForm input[name='fprefix']").val('');
                    //$("#inputForm").trigger("reset");
                }
            })            
            .on("keyup change", "#inputForm input[name='inputq']", function(){
                var question = $(this).val();
                $("#question").text(question);
                $("#qForm input[name='question']").val(question);
                console.log( $(this).val() );
            })
            .on('click', "#resetall", function(){
                $("#qnum").text('');
                $("#question").text('');
                $("#answers").html('');
                $("#inputForm").trigger("reset");
                $("#qForm").trigger("reset");
            })
            .on('change', '#ftype', function(){
                if($("#ftype").val() == "select"){
                    $("#optionlabel").removeClass("hide");
                }else{
                    $("#optionlabel").addClass("hide");
                }
            })
            .on("click", ".del", function(){
                //console.log("del clicked");
                var self = $(this);
                //element name to delete
                var deldata = $(this).data("del");
                //console.log(deldata);
                $("." + deldata).remove();
                //index--;
            })// edit form modal contant
            .on('show.bs.modal', function (event) {
              var button = $(event.relatedTarget); // Button that triggered the modal
              //console.log(button);
              var ajaxurl = button.data('href');
              //console.log(ajaxurl);
              var type = button.data('type');
              // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
              // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
              var modal = $(this);
                $("#qnum").text('');
                $("#qForm input[name='qnum']").val('');
                $("#question").text('');
                $("#answers").text('');
                $("#qForm input[name='question']").val('');
                $("#inputForm input[name='fprefix']").val('');
                
              if(typeof ajaxurl != 'undefined') {
                   /**
                    * get content using ajax
                    * return array
                    */
                   console.log(ajaxurl);
                  var content = getData(ajaxurl, ['urlhash','section','qnum','question','qanswers','answer_view']);
                  console.log(content);
                  var urlhash = content[0].urlhash.edit;
              }  
              //console.log(urlhash);
              if(typeof urlhash != 'undefined') {
                    $("#qForm").attr('data-hash', urlhash);
                }else{
                    $("#qForm").attr('data-hash', ems.urlhash);
                }
              if(typeof ajaxurl != 'undefined') {
                    $("#qForm").attr('action', ajaxurl);
                }else{
                    $("#qForm").attr('action', ems.add_question_url);
                }
              switch(type){
                  case "create":
                    $('#modal-title').text('Create Question');
                    $('#save').attr('data-type', 'post');
                    break;
                  case "edit":
                    $('#modal-title').text('Edit Question');
                    $('#save').attr('data-type', 'patch');
                    break;
                }
              
              
              if(typeof content == 'undefined') return;
              
              var answers = content[0].qanswers;
              
              $('#inputForm input[name=inputsect]').val(content[0].section);
              $('#inputForm input[name=inputnum]').val(content[0].qnum);
              $('#inputForm input[name=inputq]').val(content[0].question);
              $('#inputForm input[name=fprefix]').val(content[0].qnum + '_a');
              $('#inputForm select[name=answer_view]').val(content[0].answer_view);
              $("#qForm input[name='answer_view']").val(content[0].answer_view);
              $("#qForm input[name='section']").val(content[0].section);
              $("#qForm input[name='qnum']").val(content[0].qnum);
              $("#qForm input[name='question']").val(content[0].question);
              $("#qnum").text(content[0].qnum + ' : ');
              $("#question").text(content[0].question);
              //console.log(content.qanswers); return;
              $.each(content[0].qanswers, function( index, answer ) {
                  var prefix = $('#inputForm input[name=fprefix]').val();
                  add(answer.type, answer.value, prefix, answer.text, answer.optional, index);
                });
            });
            
            $( "#launch" ).on('click', function(){
                $("#inputForm").trigger("reset");
                $("#qForm").trigger("reset");
            });
            
            if (typeof sortURL === 'undefined') {
		var sortURL = ems.sorturl;
            }
            $("#accordion").sortable({
			cursor: 'move',
			axis: 'y',
			update: function (event, ui) {
				var order = $(this).sortable("serialize");


				//send ajax request
				$.ajax({
					url    : sortURL,
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
					url    : sortURL,
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