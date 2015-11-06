@extends ('frontend.layouts.master')

@section ('title', 'Status Report')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
        Status
        <small>Data Entry Status</small>
    </h1>
@endsection

@section ('breadcrumbs')
     <li><a href="{!!route('frontend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
     <li>{!! link_to_route('data.projects.index', 'Project Management'); !!}</li>
     <li>{!! link_to_route('data.project.status.index', $project->name. ' Status', $project->id) !!}</li>
@stop

@section('content')
        {!! link_to_route('data.project.status.index', 'Reset filters', $project->id, ['class'=>'btn btn-primary btn-xs']) !!}
    <table id="results-table" class="table table-bordered table-inverse panel panel-default">
        <thead>
            <tr>
                <th id="code"># <br>
                    <input type="text" name="pcode" style="width:80px;" class="form-control" id="input-code">
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
                    <select id="township" name="township" class="dropdown form-control" style="max-width:135px;">
                        <option value="">-</option>
                        @foreach(array_unique($all_loc->lists('township')->toArray()) as $township)
                        <option value="{{ $township }}" @if($township == $request->get('township')) selected @endif>{!! _t(ucfirst($township)) !!}</option>
                        @endforeach
                    </select>
                </th>
                <th id="village">{!! _t('Station') !!}
                </th>
                <th class="observers">{!! _t('Observers') !!}
                    <input type="text" name="phone" style="width:80px;" class="form-control" id="phone"></th>
                @foreach($project->sections as $k => $section)
                <th class="section{{ $k }}" title="{{ _t($section->text) }}" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body">
                    <i>{{ $k + 1}}</i>
                    <br />
                    {!! Form::select('status',[''=>'-','complete'=>'Complete','incomplete'=>'Incomplete','error'=>'Error','missing'=>'Missing'
                    ],(($k == $request->get('section'))? $request->get('status'):null),['class'=>'dropdown form-control','id'=>"section$k"]) !!}
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
@include('frontend.result.includes.partials.status-script')  