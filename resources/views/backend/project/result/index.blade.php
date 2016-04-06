@extends ('backend.layouts.master')

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
     <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
     <li>{!! link_to_route('admin.projects.index', 'Project Management'); !!}</li>
     <li>{!! link_to_route('admin.project.results', $project->name. ' Results', $project->id) !!}</li>
@stop

@section('content')
    @include('backend.project.includes.partials.header-buttons')
    <div class="col-lg-12">
    <div class="table-responsive">
    <table id="results-table" class="table table-inverse panel panel-default">
        <thead class="row panel-heading panel-heading-btn">
            
            <tr class="row">
                <th>#</th>
                <th>Region</th>
                <th>Distric</th>
                <th>Station</th>
                <th>Observers</th>
                @foreach($sections as $k => $section)
                <th><i title="{{ $section->text }}" data-toggle="tooltip" data-placement="auto" data-html="true">{{ $k + 1}}</i></th>
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
                    <div>{{$participant->name}}</div>
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
            { data: 'primaryid', name: '#' },
            { data: 'state', name: 'region' },
            { data: 'village', name: 'station' },
            { data: 'created_at', name: 'created_at' },
            { data: 'updated_at', name: 'updated_at' }
        ]
    });
});
</script>
@endpush