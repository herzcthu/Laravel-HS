@extends ('backend.layouts.master')

@section ('title', 'Location Management | Create Location')

@section ('before-styles-end')
    {!! HTML::style('css/plugin/jquery.onoff.css') !!}
@stop
@section ('after-styles-end')
    {!! HTML::style('css/vendor/jquery-ui/themes/smoothness/jquery-ui.min.css') !!}
@stop

@section('page-header')
    <h1>
        Location Management
        <small>Create Location</small>
    </h1>
@endsection

@section ('breadcrumbs')
     <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
     <li>{!! link_to_route('admin.locations.index', 'Location Management') !!}</li>
     <li class="active">{!! link_to_route('admin.locations.create', 'Location Management') !!}</li>
@stop

@section('content') 

    {!! Form::model($locations,['route' => ['admin.locations.update', $locations->id], 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'PATCH']) !!}
        
        <div class="form-group">
            {!! Form::label('country', 'Country', ['class'=>'col-lg-2 control-label']) !!}
            <div class="col-lg-10">
            {!! Form::selectCountry('country', 'MM', ['class'=>'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-2 control-label">State</label>
            <div class="col-lg-10">
                {!! Form::text('state', null, ['id' => 'state', 'class' => 'form-control', 'placeholder' => 'Ayeyawaddy']) !!}
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">District</label>
            <div class="col-lg-10">
                {!! Form::text('district', null, ['id' => 'district', 'class' => 'form-control', 'placeholder' => 'Pathein']) !!}
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">Township</label>
            <div class="col-lg-10">
                {!! Form::text('township', null, ['id' => 'township', 'class' => 'form-control', 'placeholder' => 'Pathein']) !!}
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">Village Tract</label>
            <div class="col-lg-10">
                {!! Form::text('village_tract', null, ['id' => 'vtract', 'class' => 'form-control', 'placeholder' => 'Ah Lel']) !!}
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">Village</label>
            <div class="col-lg-10">
                {!! Form::text('village', null, ['id' => 'village', 'class' => 'form-control', 'placeholder' => 'Leik Inn Kone']) !!}
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">Custom location code</label>
            <div class="col-lg-10">
                {!! Form::text('pcode', null, ['class' => 'form-control', 'placeholder' => 'P117001']) !!}
            </div>
        </div><!--form control-->
        <div class="form-group">
            <label class="col-lg-2 control-label">UEC location code</label>
            <div class="col-lg-10">
                {!! Form::text('ueccode', null, ['class' => 'form-control', 'placeholder' => 'UEC1000']) !!}
            </div>
        </div><!--form control-->
        <div class="form-group" id="ajax_insert">
            
        </div><!--form control-->
        

        <div class="pull-left">
            <a href="{{route('admin.locations.index')}}" class="btn btn-danger">Cancel</a>
        </div>

        <div class="pull-right">
            <input type="submit" class="btn btn-success" value="Save" />
        </div>
        <div class="clearfix"></div>

    {!! Form::close() !!}
@endsection
@include('backend.location.includes.partials.footer-script')
@stop