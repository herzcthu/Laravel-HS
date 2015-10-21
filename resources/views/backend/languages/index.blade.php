@extends ('backend.layouts.master')

@section ('title', 'Translation Management')
@section ('meta')
    <meta name="csrf-token" content="{!! csrf_token()!!}"/>
@endsection
@section('page-header')
    <h1>
        Translation Management
        <small>Translation strings</small>
    </h1>
@endsection

@section ('breadcrumbs')
    <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li class="active">{!! link_to_route('admin.language.index', 'Translation Management') !!}</li>
@stop

@section('content')


		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xs-12">
						@if (count($errors) > 0)
							<div class="alert alert-danger">
								<strong>Whoops!</strong> There were some problems with your input.<br><br>
								<ul>
									@foreach ($errors->all() as $error)
										<li>{{ $error }}</li>
									@endforeach
								</ul>
							</div>
						@endif

							@if (Session::has('flash_message'))
								<div class="alert alert-success">{{ Session::get('flash_message') }}</div>
							@endif
							@if (Session::has('user_delete_error'))
								<div class="alert alert-danger">{{ Session::get('user_delete_error') }}</div>
							@endif
							<div class="box">
								<div class="box-header">
									<h3 class="box-title">Translation Table</h3>
								</div>
								<!-- /.box-header -->
								<div class="box-body">
							<table id="datatable-allfeatures" class="table table-bordered table-striped">
								<thead>
								@foreach($locale_list as $lang)
									@if($lang->code == $default_locale)
										<th>{{ $lang->name }}</th>
									@endif
									@if($lang->code != $default_locale)
                                                                        <th lang="{!! $lang->code !!}">{{ _t($lang->name) }}</th>
									@endif
								@endforeach
								<th>Action</th>
								</thead>
								<tbody>
								

								@foreach($translation_list as $translation)
                                                                
                                                                    @if($translation->translation_id === null)
                                                                    <tr>
                                                                            <td>{!! $translation->translation !!}<span class="alert success text-green pull-right"></span></td>
                                                                        @if($translation->children !== null)
                                                                            @foreach($translation->children as $child)
                                                                            <td lang="{!! $child->locale->code !!}">
                                                                                {!! Form::text("lang_id[$child->translation_id]", $child->translation, ['class' => 'form-control']) !!}
                                                                            </td>
                                                                            @endforeach
                                                                        @else
                                                                            <td> No Translation</td>
                                                                        @endif    
                                                                            <td><a href="#" class="update">Update</a></td>
                                                                    </tr>

                                                                    @endif
								@endforeach

								</tbody>
							</table>
                                                                    @section('before-scripts-end')
                                                                    <script type="text/javascript">
                                                                        $.ajaxSetup({
											headers: {
												'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
											}
										});
                                                                    </script>            
                                                                    @endsection
                                                                    @push('scripts')
									<script type="text/javascript">
                                                                            $(document).ready(function () {
										
										$('tr').hover(function () {
											$(this).toggleClass('langupdate');
											$(this).find('span').toggleClass('flash');
											$(this).find('input').toggleClass('updateinput');
											$(this).find("a").unbind('click').on('click', function(e) {
												e.preventDefault();
												$.post( "{{ route('ajax.language') }}", $( ".updateinput" ).serialize() ).success(function( data ) {
													var json = JSON.parse(data);
													if(json.status == true){
														$( "span.flash" ).html( json.message );
														$( "span.flash" ).fadeIn( "slow" );

													}
													if(json.status == false){
														$( "span.flash" ).html( json.message );
														$( "span.flash" ).fadeIn( "slow" );
													}
												});
											});
										});
                                                                                
                                                                                $('#datatable-allfeatures').dataTable({
                                                                                    "scrollX":true,
                                                                                    "aoColumnDefs": [
                                                                                        { 'bSortable': false, 'aTargets': [ 0 ] }
                                                                                    ]});
                                                                            });



									</script>
                                                                    @endpush    
								</div>
								<!-- /.box-body -->
							</div>
							<!-- /.box -->
				</div>
	<!-- /.box-body -->
</div>
<!-- /.box -->
</div>
<!-- /.col -->
</div>
<!-- /.row -->


@endsection