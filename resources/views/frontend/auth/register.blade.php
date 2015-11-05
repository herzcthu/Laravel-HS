@extends('frontend.layouts.master')

@section('content')
<div class="row">

    <div class="col-md-8 col-lg-8 col-md-offset-2">

        <div class="panel panel-default">
            <div class="panel-heading">Register</div>

            <div class="panel-body">

                <div class="form">
                    {!! form_start($form) !!}
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            {!! form_row($form->name) !!}
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            {!! form_row($form->email) !!}
                        </div>                          
                    </div>
                    
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            {!! form_row($form->password) !!}
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            {!! form_row($form->password_confirmation) !!}
                        </div>                          
                    </div>
                    
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            {!! form_row($form->first_name) !!}
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            {!! form_row($form->last_name) !!}
                        </div>                          
                    </div>
                    
                    <div class"row">
                         {!! form_row($form->organization) !!}
                    </div>
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            {!! form_row($form->save) !!}
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            {!! form_row($form->clear) !!}
                        </div>                          
                    </div>                                                            

                    {!! form_end($form, $renderRest = true) !!}
                </div>

            </div><!-- panel body -->

        </div><!-- panel -->

    </div><!-- col-md-8 col-lg-8 -->

</div><!-- row -->
@endsection