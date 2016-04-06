@extends ('backend.layouts.master')

@section ('title', 'Participant Management | Deleted Participants')

@section('page-header')
    <h1>
        Participant Management
        <small>Deleted Participants</small>
    </h1>
@endsection

@section ('breadcrumbs')
    <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li>{!! link_to_route('admin.participants.index', 'Participant Management') !!}</li>
    <li class="active">{!! link_to_route('admin.participants.deleted', 'Deleted Participants') !!}</li>
@stop

@section('content')
    @include('backend.participant.includes.partials.header-buttons')

    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>E-mail</th>
            <th>Roles</th>
            <th class="visible-lg">Created</th>
            <th class="visible-lg">Last Updated</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
            @if ($participants->count())
                @foreach ($participants as $participant)
                    <tr>
                        <td>{!! $participant->id !!}</td>
                        <td>{!! $participant->name !!}</td>
                        <td>{!! link_to("mailto:".$participant->email, $participant->email) !!}</td>
                        <td>
                            @if ($participant->roles()->count() > 0)
                                @foreach ($participant->roles as $role)
                                    {!! $role->name !!}<br/>
                                @endforeach
                            @else
                                None
                            @endif
                        </td>
                        
                        <td class="visible-lg">{!! $participant->created_at->diffForHumans() !!}</td>
                        <td class="visible-lg">{!! $participant->updated_at->diffForHumans() !!}</td>
                        <td>
                            <a href="{{route('admin.participant.restore', $participant->id)}}" class="btn btn-xs btn-success" name="restore_participant"><i class="fa fa-refresh" data-toggle="tooltip" data-placement="top" title="Restore Participant"></i></a> <a href="{{route('admin.participant.delete-permanently', $participant->id)}}" class="btn btn-xs btn-danger" name="delete_participant_perm"><i class="fa fa-times" data-toggle="tooltip" data-placement="top" title="Delete Permanently"></i></a>
                        </td>
                    </tr>
                @endforeach
            @else
                <td colspan="9">No Deleted Participants</td>
            @endif
        </tbody>
    </table>

    <div class="pull-left">
        {!! $participants->total() !!} participant(s) total
    </div>

    <div class="pull-right">
        {!! $participants->render() !!}
    </div>

    <div class="clearfix"></div>
@stop

@section('after-scripts-end')
	<script>
		$(function() {
			$("a[name='delete_participant_perm']").click(function() {
				return confirm("Are you sure you want to delete this participant permanently? Anywhere in the application that references this participant's id will most likely error. Proceed at your own risk. This can not be un-done.");
			});

			$("a[name='restore_participant']").click(function() {
                return confirm("Restore this participant to its original state?");
            });
		});
	</script>
@stop