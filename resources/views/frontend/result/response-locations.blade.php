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
                <th>#
                </th>
                @foreach($sections as $k => $section)
                <th colspan="4" class="section{{ $k }}" title="{{ _t($section->text) }}" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body">
                    <i>{{ $k + 1}}</i>
                </th>
                @endforeach    
                <th></th>
            </tr>   
            <tr>
                <th id="group">Status</th>
                @foreach($sections as $k => $section)
                <th id='complete{{$k}}'><img src="{{ asset('img/') }}/complete.png" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body" title="Complete" class="status-icon"></th>
                <th id='incomplete{{$k}}'><img src="{{ asset('img/') }}/incomplete.png" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body" title="Incomplete" class="status-icon"></th>
                <th id='error{{$k}}'><img src="{{ asset('img/') }}/error.png" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body" title="Error" class="status-icon"></th>
                <th id='missing{{$k}}'><img src="{{ asset('img/') }}/missing.png" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body" title="Missing" class="status-icon"></th>
                @endforeach
                <th id='totalmissing'>Total Missing</th>
            </tr>
        </thead>
        <tbody></tbody>
        
    </table>
        
@stop   
  
@push('scripts')
<script>
$(function() {
    
});
</script>
@endpush
@include('frontend.result.includes.partials.response-script')  