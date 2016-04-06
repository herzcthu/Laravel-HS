                            
                                {!! Form::label('file','File',['id'=>'','class'=>'sr-only control-label']) !!}
                                {!! Form::file('file',[ 'class'=>'filestyle', 'data-icon'=>'true', 'data-iconName'=>'fa fa-upload']) !!}
                                
                            <div class="input-group">
                                    {!! Form::submit('Import', ['class'=>'btn btn-primary']) !!}                                
                            </div>
                            <div class="input-group">
                                <!-- reset buttons -->
                                    {!! Form::reset('Reset', ['class'=>'btn btn-default']) !!}
                            </div>
@section ('before-scripts-end')
{!! HTML::script('js/vendor/bootstrap-filestyle/bootstrap-filestyle.min.js') !!}
@stop