@extends ('frontend.layouts.master')

@section ('title', 'Result Management | Incident Report')

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
                <th id="pcode"># <br>
                    <input type="text" name="pcode" style="width:80px;" class="form-control" id="input-code">
                </th>
                <th id="incident">
                   {!! _t('Incident') !!}
                </th>
                <th id="cq">
                   {!! _t('Checklist Q') !!}
                </th>
                <th id="state">{!! _t('Region') !!}
                    <br />
                    <select id="region" name="region" class="dropdown form-control" style="max-width:135px;">
                        <option value="">-</option>
                        @foreach(array_unique($all_loc->lists('state')->toArray()) as $region)
                        <option value="{{ $region }}" @if($region == $request->get('region')) selected @endif>{!! _t(ucfirst($region)) !!}</option>
                        @endforeach
                    </select>
                </th>
                <th id="townshipcol">{!! _t('Township') !!}
                    <br />
                    <select id="township" name="township" class="dropdown form-control" style="width:135px;">
                        <option value="">-</option>
                        <?php if($request->get('region')){
                                $townships = array_unique($all_loc->where('state', $request->get('region'))->lists('township')->toArray());
                            } else {
                                $townships = array_unique($all_loc->lists('township')->toArray());
                            }
                        ?>
                        @foreach($townships as $township)
                        <option value="{{ $township }}" @if($township == $request->get('township')) selected @endif>{!! _t(ucfirst($township)) !!}</option>
                        @endforeach
                    </select>
                </th>
                <th id="village">{!! _t('Station') !!}
                </th>
                <th id="observers">{!! _t('Observers') !!}<input type="text" name="phone" style="width:80px;" class="form-control" id="phone"></th>
                </th>
                <!--th class="observers">{!! _t('Observers') !!}</th-->
                @foreach($project->questions as $k => $question)
                
                @if(array_key_exists($question->report, $project->reporting) && $project->reporting[$question->report]['text'] == 'Incident')
                <th class="{{ $question->qnum }}" id="{{ $question->qnum }}" title="{{ $question->question }}" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body">
                    <i>{{ $question->qnum }}</i>
                    <br />
                    <select id="question-{{ $question->id }}" name="{{ $question->id }}" class="dropdown form-control" style="max-width:135px;">
                        <option value="">-</option>
                        @foreach($question->qanswers->sortBy('akey', SORT_NATURAL) as $ans)
                        <option value="{{ $ans->akey }}" @if($request->get('answer') == $ans->akey) selected @endif>{{ $ans->text }} </option>
                        @endforeach
                    </select>
                </th>
                @endif
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