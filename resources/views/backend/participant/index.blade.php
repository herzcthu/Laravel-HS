@extends ('backend.layouts.master')

@section ('title', 'Participant Management')

@section('page-header')
    <h1>
        Participant Management
        <small>Active Participants</small>
    </h1>
@endsection

@section ('breadcrumbs')
    <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li class="active">{!! link_to_route('admin.participants.index', 'Participant Management') !!}</li>
@stop

@section('content')
    @include('backend.participant.includes.partials.header-buttons')


<div class='row' style='margin-bottom: 10px'>
 
</div>
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><input type='checkbox' id='checkall' class='checkall checkbox'></th>
            <th>ID</th>
            <th>Name</th>
            <th>Phones</th>
            <th>Base Location</th>
            <th>Roles</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($participants as $participant)
                <tr>
                    <td><input type='checkbox' class='checkall checkbox' name='participants[{!! $participant->id !!}]'></td>
                    <td>{!! $participant->participant_id !!}</td>
                    {{-- Aio()->migrate($participant->id, $participant->participant_id, $participant->organization->id) --}}
                    <td>
                        @if(!empty($participant->avatar))
                        <img width="30" height="30" src="{!! (!empty($participant->avatar)? $participant->avatar: asset('img/backend/participant2-160x160.png')) !!}" alt="{!! $participant->name !!}" title="{!! $participant->name !!}"> {!! $participant->name !!}
                        @else
                        {!! $participant->name !!} 
                        @endif
                    </td>
                    <td>
                        @if($participant->phones)
                        M: {!! $participant->phones->mobile !!} <br>
                        E: {!! $participant->phones->emergency !!}
                        @endif
                    </td>
                    <td>{{ $participant->pcode->implode('pcode') }}</td>
                    <td>
                        @if(!is_null($participant->role))
                            {!! $participant->role->name !!}
                        @endif
                    </td>
                    
                    <td>{!! $participant->edit_button !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pull-left">
        {!! $participants->total() !!} participant(s) total
    </div>

    <div class="pull-right">
        {!! $participants->render() !!}
    </div>

    <div class="clearfix"></div>
    {!! Form::close() !!}
@stop