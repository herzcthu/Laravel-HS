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


    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><input type='checkbox' id='checkall' class='checkall checkbox'></th>            
            <th>Name</th> 
            <th>Parent Project</th>
            <th>Organizations</th>
            <th class="visible-lg">Created</th>
            <th class="visible-lg">Last Updated</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                <tr>
                    <td><input type='checkbox' class='checkall checkbox' name='projects[{!! $project->id !!}]'></td>                    
                    <td>
                        <a href="{{ route('admin.project.analysis', $project->id) }}" data-toggle="tooltip" data-placement="top" data-html="true" title="<h5>{!! $project->name !!}</h5><p>Click here to go to analysis</p>" >{!! $project->name !!}</a><br>
                        @if($project->type == 'incident')
                            {!! $project->incident_action_buttons !!}
                        @else
                            {!! $project->checklist_action_buttons !!}
                        @endif
                    </td>
                    <td>
                        @if ($project->parent)
                            {!! $project->parent->name !!}
                        @endif
                    </td>
                    <td>
                        @if ($project->organization)
                            
                                {!! $project->organization->name !!}<br/>
                            
                        @else
                            None
                        @endif
                    </td>
                    
                    <td class="visible-lg">{!! $project->created_at->diffForHumans() !!}</td>
                    <td class="visible-lg">{!! $project->updated_at->diffForHumans() !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pull-left">
        {!! $projects->total() !!} project(s) total
    </div>

    <div class="pull-right">
        {!! $projects->render() !!}
    </div>

    <div class="clearfix"></div>
    {!! Form::close() !!}
@stop