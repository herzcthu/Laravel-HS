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
                    <select id="region" name="region" class="dropdown" style="width:35px;">
                        <option value="">-</option>
                        @foreach(array_unique($all_loc->lists('state')->toArray()) as $region)
                        <option value="{{ $region }}">{!! _t(ucfirst($region)) !!}</option>
                        @endforeach
                    </select>
                </th>
                <th id="districtcol">{!! _t('District') !!}
                    <br />
                    <select id="district" name="district" class="dropdown" style="width:35px;">
                        <option value="">-</option>
                        @foreach(array_unique($all_loc->lists('district')->toArray()) as $district)
                        <option value="{{ $district }}">{!! _t(ucfirst($district)) !!}</option>
                        @endforeach
                    </select>
                </th>
                <th id="village">{!! _t('Station') !!}
                    <br />
                    <select id="station" name="station" class="dropdown" style="width:35px;">
                        <option value="">-</option>
                        @foreach(array_unique($all_loc->lists('village')->toArray()) as $village)
                        <option value="{{ $village }}">{!! _t(ucfirst($village)) !!}</option>
                        @endforeach
                    </select>
                </th>
                <th class="observers">{!! _t('Observers') !!}</th>
                @foreach($project->sections as $k => $section)
                <th class="section{{ $k }}" title="{{ _t($section->text) }}" data-toggle="tooltip" data-placement="auto" data-html="true" data-container="body">
                    <i>{{ $k + 1}}</i>
                    <br />
                    <select id="section{{ $k }}" name="status" class="dropdown" style="width:35px;">
                        <option value="">-</option>
                        <option value="complete">{!! _t('Complete') !!}</option>
                        <option value="incomplete">{!! _t('Incomplete') !!}</option>
                        <option value="error">{!! _t('Error') !!}</option>
                        <option value="missing">{!! _t('Missing') !!}</option>
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
@include('frontend.result.includes.partials.status-script')  