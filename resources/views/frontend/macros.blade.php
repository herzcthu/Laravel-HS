@extends('frontend.layouts.master')

@section('content')
	<div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">WELCOME to {{app_name()}}!</h3>
          <div class="box-tools pull-right">
              <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="form-group">
            <label>State</label>
            {!! Form::selectState('state', 'NY', ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
            <label>Country</label>
            {!! Form::selectCountry('country', 'US', ['class' => 'form-control']) !!}
            </div>
        </div><!-- /.box-body -->
    </div><!--box box-success-->
@endsection

@section('after-scripts-end')
	<script>
		//Being injected from FrontendController
		console.log(test);
	</script>
@stop