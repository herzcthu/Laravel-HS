@extends ('layouts.master')

@section ('title', 'Result Management | Create Result')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop

@section('page-header')
    <h1>
        Result Management
        <small>Create Result</small>
    </h1>
@endsection

@section ('breadcrumbs')
     <li><a href="{!!route('frontend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
     <li>{!! link_to_route('data.projects.index', 'Project Management'); !!}</li>
     <li>{!! link_to_route('data.project.results.index', $project->name. ' Results', $project->id) !!}</li>
@stop

@section('content')
    <div class="col-lg-12">
    <div class="table-responsive">
        {!! link_to_route('data.project.results.index', 'Reset filters', $project->id, ['class'=>'btn btn-primary btn-xs']) !!}
    <table class="table table-inverse panel panel-default">
        <thead class="row panel-heading panel-heading-btn">
            
            <tr class="row">
                <th># </th>
                <th>Region
                    <br />
                    <select id="region" name="region" class="dropdown" style="width:35px;">
                        <option value="">-</option>
                        @foreach(array_unique($all_loc->lists('state')->toArray()) as $region)
                        <option value="{{ $region }}">{!! ucfirst($region) !!}</option>
                        @endforeach
                    </select>
                </th>
                <th>District
                    <br />
                    <select id="district" name="district" class="dropdown" style="width:35px;">
                        <option value="">-</option>
                        @foreach(array_unique($all_loc->lists('district')->toArray()) as $district)
                        <option value="{{ $district }}">{!! ucfirst($district) !!}</option>
                        @endforeach
                    </select>
                </th>
                <th>Station
                    <br />
                    <select id="station" name="station" class="dropdown" style="width:35px;">
                        <option value="">-</option>
                        @foreach(array_unique($all_loc->lists('village')->toArray()) as $village)
                        <option value="{{ $village }}">{!! ucfirst($village) !!}</option>
                        @endforeach
                    </select>
                </th>
                <th>Observers</th>
                @foreach($sections as $k => $section)
                <th>
                    <i title="{{ $section->text }}" data-toggle="tooltip" data-placement="auto" data-html="true">{{ $k + 1}}</i>
                    <br />
                    <select id="section{{ $k }}" name="status" class="dropdown" style="width:35px;">
                        <option value="">-</option>
                        <option value="complete">Complete</option>
                        <option value="incomplete">Incomplete</option>
                        <option value="error">Error</option>
                        <option value="missing">Missing</option>
                    </select>
                </th>
                @endforeach
                
            </tr>
            
        </thead>
        <tbody class="panel-body panel-collapse">
            @foreach($locations as $location)
            <tr class="row">
                <th scope="row">{{ $location->pcode }}</th>
                <td>{{ $location->state }}</td>
                <td>{{ $location->district }}</td>
                <td>{{ $location->village }}</td>
                <td>
                    @foreach($location->participants as $participant)
                    <div>{{$participant->name}} ({{$participant->participant_id}})</div>
                    @endforeach
                </td>
                @foreach($sections as $k => $section)
                
                <td>
                    @foreach($location->participants as $participant)
                    <div>
                        @if($participant->results->where('section_id', $k)->first()['information'])
                        <img src="{{ asset('img/'.$participant->results->where('section_id', $k)->first()['information'].'.png') }}" data-toggle="tooltip" data-placement="auto" data-html="true" title="{{ $participant->results->where('section_id', $k)->first()['information'] }}">
                        @else
                        <img src="{{ asset('img/unknown.png') }}" data-toggle="tooltip" data-placement="auto" data-html="true" title="Unknown">
                        @endif
                    </div>
                    @endforeach
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
    </div>
        
        <div class="clearfix"></div>

    
@stop   
  
@push('scripts')
<script>
$(function() {
    $('#results-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{!! route('ajax.project.results', $project->id) !!}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'created_at', name: 'created_at' },
            { data: 'updated_at', name: 'updated_at' }
        ]
    });
});
</script>
@endpush
@include('frontend.result.includes.partials.results-script')  