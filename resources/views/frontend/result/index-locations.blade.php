@extends ('frontend.layouts.master')

@section ('title', 'Result Management | Create Result')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
        Result Management
        <small>Add Incident</small>
    </h1>
@endsection

@section ('breadcrumbs')
     <li><a href="{!!route('frontend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
     <li>{!! link_to_route('data.projects.index', 'Project Management'); !!}</li>
     <li>{!! link_to_route('data.project.results.index', $project->name. ' Incident', $project->id) !!}</li>
@stop

@section('content')
<div class="pull-left">
        {!! link_to_route('data.project.results.index', 'Reset filters', $project->id, ['class'=>'btn btn-primary btn-xs']) !!}
</div>
<div class="pull-right">
        {!! link_to_route('data.project.results.create', 'Add Results', $project->id, ['class'=>'btn btn-primary btn-xs']) !!}
</div>
        
    <table id="results-table" class="table table-bordered table-inverse panel panel-default">
        <thead>
            <tr>
                <th id="code"># <br>
                    <input type="text" name="code" style="width:80px;" class="form-control" id="input-code">
                </th>
                <th id="incident">
                   {!! _t('Incident') !!}
                </th>
                <th id="cq">
                   {!! _t('Checklist Q') !!}
                </th>
                <th id="state">{!! _t('Region') !!}
                    <br />
                    <select id="region" name="region" class="dropdown" style="width:35px;">
                        <option value="">-</option>
                        @foreach(array_unique($all_loc->lists('state')->toArray()) as $region)
                        <option value="{{ $region }}">{!! _t(ucfirst($region)) !!}</option>
                        @endforeach
                    </select>
                </th>
                <th id="townshipcol">{!! _t('Township') !!}
                    <br />
                    <select id="township" name="township" class="dropdown" style="width:35px;">
                        <option value="">-</option>
                        @foreach(array_unique($all_loc->lists('township')->toArray()) as $township)
                        <option value="{{ $township }}">{!! _t(ucfirst($township)) !!}</option>
                        @endforeach
                    </select>
                </th>
                <th id="village">{!! _t('Station') !!}
                </th>
                <!--th class="observers">{!! _t('Observers') !!}</th-->
                @foreach($project->questions as $k => $question)
                <th class="{{ $question->qnum }}" id="{{ $question->qnum }}" title="{{ $question->question }}" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body">
                    <i>{{ $question->qnum }}</i>
                    <br />
                    <select id="question-{{ $question->id }}" name="{{ $question->id }}" class="dropdown" style="width:35px;">
                        @foreach($question->qanswers as $ans)
                        <option value="{{ $ans->akey }}">{{ $ans->text }} </option>
                        @endforeach
                    </select>
                </th>
                @endforeach                
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
@include('frontend.result.includes.partials.results-script')  