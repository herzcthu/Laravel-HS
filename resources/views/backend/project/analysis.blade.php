@extends ('backend.layouts.master')

@section ('title', 'Analysis')

@section('page-header')
    <h1>
        Analysis
        <small>{{ $project->name }} Projects</small>
    </h1>
@endsection

@section ('breadcrumbs')
    <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li class="active">{!! link_to_route('admin.projects.index', 'Project Management') !!}</li>
@stop

@section('content')
{!! Form::open(['route' => 'admin.projects.bulk', 'class' => 'form-horizontal', 'organization' => 'form', 'method' => 'post']) !!}

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
                    @foreach($questions->sortBy('sort', SORT_NATURAL) as $question)
                        @if($section_key == $question->section)
                        <table class="table table-bordered table-condensed table-striped">
                            <tbody>

                                <tr>
                                    <td rowspan="2">{{ $question->qnum }}</td>
                                    <td rowspan="2">{{ $question->question }}</td>
                                    @foreach($question->qanswers as $qk => $qv)
                                    <td>{{ $qv->text }}</td>
                                    @endforeach
                                    <td>Reported</td>
                                    <td>Missing</td>
                                </tr>
                                <tr>
                                    @foreach($question->qanswers as $qk => $qv)
                                    <td>
                                     @foreach($question->ans->groupBy('akey') as $k => $qa)
                                        @if($k == $qv->akey)
                                        {{ ceil(($qa->count() / $question->ans->count()) * 100 )}}% ({{$qa->count()}})
                                        @endif
                                     @endforeach
                                    </td>
                                    @endforeach
                                    <td>
                                        {{ ceil(( $question->ans->count() / $locations->count() ) * 100 )}}% ({{ $question->ans->count()}})
                                    </td>
                                    <td>
                                        {{ floor((( $locations->count() - $question->ans->count() ) / $locations->count() ) * 100) }}% ({{$locations->count() - $question->ans->count()}})
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                        @endif
                    @endforeach
                </div>
                <div class="panel-footer">
                    {!! $section->text !!} (Section End)
                </div>
            </div><!-- panel end -->    
            @endforeach
@endif            
    <div class="pull-left">
        
    </div>

    <div class="pull-right">
        
    </div>

    <div class="clearfix"></div>
    {!! Form::close() !!}
@stop