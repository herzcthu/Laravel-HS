@extends ('frontend.layouts.master')

@section ('title', 'Project Management')

@section('page-header')
    <h1>
        {{ _t('Project Management') }}
        <small>{{ _t('Projects List') }}</small>
    </h1>
@endsection

@section ('breadcrumbs')
    <li><a href="{!!route('frontend.dashboard')!!}"><i class="fa fa-dashboard"></i> {{ _t('Dashboard') }}</a></li>
    <li class="active">{!! link_to_route('data.projects.index', _t('Project Management')) !!}</li>
@stop

@section('content')
<div class="col-lg-12">
    <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>        
            <th>{!! _t('Name') !!}</th> 
            <th>{!! _t('Parent Project') !!}</th>
            <th>{!! _t('Organizations') !!}</th>
            <th class="visible-lg">{!! _t('Created') !!}</th>
            <th class="visible-lg">{!! _t('Last Updated') !!}</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                <tr>
                                    
                    <td>
                        {!! _t($project->name) !!} <br>
                        @if($project->type == 'incident')
                        {!! $project->frontend_incident_action_buttons !!}
                        @else
                        {!! $project->frontend_action_buttons !!}
                        @endif
                    </td>
                    <td>
                        @if ($project->parent)
                            {!! _t($project->parent->name) !!}
                        @endif
                    </td>
                    <td>
                        @if ($project->organization)
                            
                                {!! _t($project->organization->name) !!}<br/>
                            
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
    </div>
</div>
    <div class="pull-left">
        {!! _t(':total project(s) total',['total' => $projects->total()]) !!}
    </div>

    <div class="pull-right">
        {!! $projects->render() !!}
    </div>

    <div class="clearfix"></div>
@stop