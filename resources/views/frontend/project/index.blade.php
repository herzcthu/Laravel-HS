@extends ('frontend.layouts.master')

@section ('title', 'Project Management')

@section('page-header')
    <h1>
        Project Management
        <small>Active Projects</small>
    </h1>
@endsection

@section ('breadcrumbs')
    <li><a href="{!!route('frontend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li class="active">{!! link_to_route('data.projects.index', 'Project Management') !!}</li>
@stop

@section('content')
<div class="col-lg-12">
    <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>        
            <th>Name</th> 
            <th>Parent Project</th>
            <th>Organizations</th>
            <th class="visible-lg">Created</th>
            <th class="visible-lg">Last Updated</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                <tr>
                                    
                    <td>
                        {!! $project->name !!}
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
                    <td class="visible-lg">{!! $project->frontend_action_buttons !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
    <div class="pull-left">
        {!! $projects->total() !!} project(s) total
    </div>

    <div class="pull-right">
        {!! $projects->render() !!}
    </div>

    <div class="clearfix"></div>
@stop