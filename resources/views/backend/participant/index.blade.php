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
 
<div class='col-xs-12 col-sm-12 col-md-12 form-inline'>
 {!! Form::open(['route' => 'admin.participants.bulk', 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'post']) !!}
 {!! Form::label('role', 'Change to - ', ['class' => 'control-label']) !!}
 {!! Form::select('role', isset($roles) ? $roles->lists('name','id'):['none' => 'None'],false, ['class' => 'form-control']) !!}

 {!! Form::submit( 'Bulk Change', ['class' => 'btn btn-secondary']) !!}
</div>
</div>
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><input type='checkbox' id='checkall' class='checkall checkbox'></th>
            <th>ID</th>
            <th>Name</th>
            <th>E-mail</th>
            <th>Responsible Region</th>
            <th>Base Location</th>
            <th>Roles</th>
            <th class="visible-lg">Created</th>
            <th class="visible-lg">Last Updated</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($participants as $participant)
                <tr>
                    <td><input type='checkbox' class='checkall checkbox' name='participants[{!! $participant->id !!}]'></td>
                    <td>{!! $participant->participant_id !!}</td>
                    <td>
                        @if(!empty($participant->avatar))
                        <img width="30" height="30" src="{!! (!empty($participant->avatar)? $participant->avatar: asset('img/backend/participant2-160x160.png')) !!}" alt="{!! $participant->name !!}" title="{!! $participant->name !!}"> {!! $participant->name !!}
                        @else
                        {!! $participant->name !!} 
                        @endif
                    </td>
                    <td>{!! link_to("mailto:".$participant->email, $participant->email) !!}</td>
                    <td>
                        @if($participant->role->level == 4)
                            {!! $participant->pcode->state !!}
                        @elseif($participant->role->level == 3)
                            {!! $participant->pcode->district !!}
                        @elseif($participant->role->level == 2)
                            {!! $participant->pcode->township !!}
                        @elseif($participant->role->level == 1)
                            {!! $participant->pcode->village_tract !!}
                        @else
                            {!! $participant->pcode->village !!}
                        @endif
                    </td>
                    <td>{{ $participant->base }}</td>
                    <td>
                        {!! $participant->role->name !!}
                    </td>
                    
                    <td class="visible-lg">{!! $participant->created_at->diffForHumans() !!}</td>
                    <td class="visible-lg">{!! $participant->updated_at->diffForHumans() !!}</td>
                    <td>{!! $participant->action_buttons !!}</td>
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