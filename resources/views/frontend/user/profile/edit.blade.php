@extends('frontend.layouts.master')

@section('content')
	<div class="row">

		<div class="col-md-12">

			<div class="box box-default">
				<div class="box-header">Update Information</div>

				<div class="box-body">

                       {!! Form::model($user, ['route' => ['profile.update', $user->id], 'class' => 'form-horizontal', 'method' => 'PATCH']) !!}
                            <div class="form-group">
                                <label class="col-md-2 control-label">Profile Image</label>
                                <div class="col-md-10 form-inline">

                                    {!! Form::hidden('avatar',null, ['class' => 'form-control', 'id' => 'avatar_url']) !!}
                                    <!-- compose message btn -->
                                    <a class="btn img-thumbnail" data-toggle="modal" data-target="#compose-modal">
                                    <img id="avatar" class="img-responsive profile-avatar" width="100" height="100" src="{!! (!empty($user->avatar)? $user->avatar: asset('img/backend/user2-160x160.png')) !!}">
                                    Select/Upload</a>
                                </div>
                            </div><!--form control-->
                              <div class="form-group">
                                    <label class="col-md-2 control-label">Name</label>
                                    <div class="col-md-10">
                                        {!! Form::input('text', 'name', null, ['class' => 'form-control']) !!}
                                    </div>
                              </div>

                              @if ($user->canChangeEmail())
                                  <div class="form-group">
                                      <label class="col-md-2 control-label">E-mail Address</label>
                                      <div class="col-md-10">
                                          {!! Form::input('email', 'email', null, ['class' => 'form-control']) !!}
                                      </div>
                                  </div>
                              @else
                                  <div class="form-group">
                                      <label class="col-md-2 control-label">E-mail Address</label>
                                      <div class="col-md-10">
                                          <p class="form-control-static">{{ $user->email }}</p>
                                      </div>
                                  </div>
                              @endif

                              <div class="form-group">
                                  <div class="col-md-6 col-md-offset-4">
                                      {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                                  </div>
                              </div>

                       {!! Form::close() !!}

				</div><!--box body-->

			</div><!-- box -->

		</div><!-- col-md-10 -->

	</div><!-- row -->

@include('includes.partials.medialist_grid') 
@include('includes.partials.mediaUploadModel') 

@yield('model')        
@stop 