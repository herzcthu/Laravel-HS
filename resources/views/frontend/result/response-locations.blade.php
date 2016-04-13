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
        
    <table id="results-total" class="table table-bordered table-inverse panel panel-default">
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
                <th id="total"></th>
                @foreach($sections as $k => $section)
                <th id='s{{$k}}complete'><img src="{{ asset('img/') }}/complete.png" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body" title="Complete" class="status-icon"></th>
                <th id='s{{$k}}incomplete'><img src="{{ asset('img/') }}/incomplete.png" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body" title="Incomplete" class="status-icon"></th>
                <th id='s{{$k}}error'><img src="{{ asset('img/') }}/error.png" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body" title="Error" class="status-icon"></th>
                <th id='s{{$k}}missing'><img src="{{ asset('img/') }}/missing.png" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body" title="Missing" class="status-icon"></th>
                @endforeach
                <th id='total'>Total</th>
            </tr>
        </thead>
        <tbody></tbody>
        
    </table>
    <table id="results-state" class="table table-bordered table-inverse panel panel-default">
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
                <th id="state">State</th>
                @foreach($sections as $k => $section)
                <th id='s{{$k}}complete'><img src="{{ asset('img/') }}/complete.png" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body" title="Complete" class="status-icon"></th>
                <th id='s{{$k}}incomplete'><img src="{{ asset('img/') }}/incomplete.png" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body" title="Incomplete" class="status-icon"></th>
                <th id='s{{$k}}error'><img src="{{ asset('img/') }}/error.png" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body" title="Error" class="status-icon"></th>
                <th id='s{{$k}}missing'><img src="{{ asset('img/') }}/missing.png" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body" title="Missing" class="status-icon"></th>
                @endforeach
                <th id='total'>Total</th>
            </tr>
        </thead>
        <tbody></tbody>
        
    </table>
    <table id="results-township" class="table table-bordered table-inverse panel panel-default">
        <thead>            
            <tr>
                <th id="township0">#
                </th>
                @foreach($sections as $k => $section)
                <th colspan="4" class="section{{ $k }}" title="{{ _t($section->text) }}" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body">
                    <i>{{ $k + 1}}</i>
                </th>
                @endforeach    
                <th></th>
            </tr>   
            <tr>
                <th id="township">Township</th>
                @foreach($sections as $k => $section)
                <th id='s{{$k}}complete'><img src="{{ asset('img/') }}/complete.png" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body" title="Complete" class="status-icon"></th>
                <th id='s{{$k}}incomplete'><img src="{{ asset('img/') }}/incomplete.png" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body" title="Incomplete" class="status-icon"></th>
                <th id='s{{$k}}error'><img src="{{ asset('img/') }}/error.png" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body" title="Error" class="status-icon"></th>
                <th id='s{{$k}}missing'><img src="{{ asset('img/') }}/missing.png" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body" title="Missing" class="status-icon"></th>
                @endforeach
                <th id='total'>Total</th>
            </tr>
        </thead>        
        <tbody></tbody>
        <tfoot id="township-foot"></tfoot>
        
    </table>
        
@stop   
  
@push('scripts')
<script>
$(function() {
    
});
</script>
@endpush
@include('frontend.result.includes.partials.response-script')  