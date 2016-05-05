@extends ('backend.layouts.master')

@section ('title', 'Project Management')

@section('page-header')
    <h1>
        Project Management
        <small>Active Projects</small>
    </h1>
@endsection

@section ('breadcrumbs')
    <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li class="active">{!! link_to_route('admin.projects.index', 'Project Management') !!}</li>
@stop

@section('content')
    @include('backend.project.includes.partials.header-buttons')
{!! Form::open(['route' => 'admin.projects.bulk', 'class' => 'form-horizontal', 'organization' => 'form', 'method' => 'post']) !!}

            @foreach ($projects as $project)
            <div class="panel panel-info">
                      <div class="panel-heading">
                      <a class="text text-lg text-uppercase text-black" href="{{ route('admin.project.analysis', $project->id) }}" data-toggle="tooltip" data-placement="top" data-html="true" title="<h5>{!! $project->name !!}</h5><p>Click here to go to analysis</p>" >
                          {!! $project->name !!}
                      </a>
                      Created {!! $project->created_at->diffForHumans() !!} | 
                      Updated {!! $project->updated_at->diffForHumans() !!}
                      </div>
                      <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-6">
                        @if($project->type == 'incident')
                            {!! $project->incident_action_buttons !!}
                        @elseif($project->type == 'survey')
                            {!! $project->survey_action_buttons !!}
                        @else
                            {!! $project->checklist_action_buttons !!}                            
                        @endif
                            </div>
                            <div class="col-xs-6">
                                @if ($project->parent)
                                <p class="text text-uppercase"><span class="label label-info">Related Project :</span> {!! $project->parent->name !!}</p>
                                @endif
                                @if ($project->organization)                            
                                <p class="text text-uppercase"><span class="label label-info">Organizations :</span> {!! $project->organization->name !!}</p>
                                @else
                                    None
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                
                            </div>
                         </div>
                         <div class="row">
                            <div class="col-xs-12">
                                
                            </div>
                         </div>                      
                      </div>
            </div>
                
            @endforeach

    <div class="pull-left">
        {!! $projects->total() !!} project(s) total
    </div>

    <div class="pull-right">
        {!! $projects->render() !!}
    </div>

    <div class="clearfix"></div>
    {!! Form::close() !!}
@stop