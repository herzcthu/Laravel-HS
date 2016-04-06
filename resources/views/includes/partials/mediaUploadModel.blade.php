@section('model') 
<!-- COMPOSE MESSAGE MODAL -->
        <div class="modal fade" id="compose-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-envelope-o"></i> Select or Upload</h4>
                    </div>
                    
                        <div class="modal-body">
                            <div class="row">
                            <div id="media" class="col-md-12">
                               
                            @yield('mediagrid')
                            </div>
                            </div>    
                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-12">
                                {!! Form::open(['route' => 'admin.media.upload', 'class' => '', 'role' => 'form', 'method' => 'post',  'files' => true]) !!}
                                {!! Form::hidden('user_id', (!empty($user) ? $user->id : '')) !!}
                                {!! Form::hidden('owner_id', (!empty(auth()) ? auth()->user()->id : '')) !!}
                                <div class="form-group"> 
                                    <div class="form-inline">
                                    <div class="btn btn-success btn-file col-md-3 col-xs-3 col-sm-3 pull-left">
                                        <i class="fa fa-paperclip"></i> Attachment
                                        <input id="fileupload" type="file" name="file"/>
                                    </div>
                                    <div id="progress" class="progress  col-md-9 col-xs-9 col-sm-9 pull-right" style="margin-top:7px; background: none;">
                                        <div class="progress-bar progress-bar-success"></div>
                                    </div>
                                    @if(access()->user()->can('upload_media'))  
                                    <p class="help-block">Max. 32MB</p>
                                    {{-- !! Form::submit( 'Upload', ['class' => 'btn btn-secondary']) !! --}}
                                    @endif
                                    </div>
                                </div>

                                {!! Form::close() !!}
                                </div><!-- col-md-12 -->
                            </div><!-- row -->
                            <!-- The global progress bar -->
                        </div>
                        <div class="modal-footer clearfix">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>

                            <button type="button" id="add-link" class="btn btn-primary pull-left" data-dismiss="modal"><i class="fa fa-link"></i> Add Link</button>
                        </div>
                    
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
@endsection