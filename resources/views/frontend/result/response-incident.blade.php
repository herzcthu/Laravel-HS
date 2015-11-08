@extends ('frontend.layouts.master')

@section ('title', 'Response Rate')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
        Response
        <small>Response Rate</small>
    </h1>
@endsection

@section ('breadcrumbs')
     <li><a href="{!!route('frontend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
     <li>{!! link_to_route('data.projects.index', 'Project Management'); !!}</li>
     <li>{!! link_to_route('data.project.status.index', $project->name. ' response rate', $project->id) !!}</li>
@stop

@section('content')
        
    <table id="results-table" class="table table-bordered table-inverse panel panel-default">
        <thead>
            <tr>
                <th>Group
                </th>
                @foreach($question->qanswers->sortBy('akey', SORT_NATURAL) as $qans)
                <th>{!! $qans->text !!}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($locations->get() as $kl => $location)
            <tr>
                <td>{!! $location->state !!}</td>
                @foreach($question->qanswers->sortBy('akey', SORT_NATURAL) as $qans)
                <td>
                    {!! $dbraw->where('state',$location->state)->where('akey', $qans->akey)->count() !!}
                </td>
                @endforeach
            </tr>
            @endforeach
            <tr>
                <td>{!! _t('Total') !!}</td>
                @foreach($question->qanswers->sortBy('akey', SORT_NATURAL) as $qans)
                <td>
                    {!! $dbraw->where('akey', $qans->akey)->count() !!}
                </td>
                @endforeach
            </tr>
        </tbody>
        
    </table>
        
@stop   
  
@push('scripts')
<script>
$(function() {
    
});
</script>
@endpush
