@extends('backend.layouts.master')

@section('page-header')
    <h1>
        Election 2015 Myanmar
        <small>Reporting Dashboard</small>
    </h1>
@endsection

@section('breadcrumbs')
    <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li class="active">Here</li>
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">WELCOME {!! auth()->user()->name !!}!</h3>
          <div class="box-tools pull-right">
              <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <ul class="nav nav-tabs">
            @foreach($orgs as $k => $org)
                @if($k == 0)
                <li class="active"><a data-toggle="tab" href="#{{ $org->short }}">{{ $org->short }}</a></li>
                @else
                <li><a data-toggle="tab" href="#{{ $org->short }}">{{ $org->short }}</a></li>
                @endif
            @endforeach
            </ul>
            <div class="tab-content">
                @foreach($orgs as $k => $org)
                @if($k == 0)
                  <div id="{{ $org->short }}" class="tab-pane fade in active">
                    <h3>{{ $org->name }}</h3>
                    @foreach($org->projects as $project)
                    <div id="{{ $project->id }}" style="height: 300px; width: 100%;"></div>
                    @push('scripts')
                    <script type="text/javascript">
                    window.onload = function () {
                            var chart = new CanvasJS.Chart("{{ $project->id }}",
                            {
                                    title:{
                                            text: "{!! $project->name !!} Reporting status."             
                                    },
                                    axisY:{
                                            title: "percent"
                                    },
                                    animationEnabled: true,
                                    toolTip:{
                                            shared: true,
                                            content: "{name}: {y} - <strong>#percent%</strong>",
                                    },
                                    data:[
                                    {        
                                            type: "stackedBar100",
                                            showInLegend: true, 
                                            name: "Complete",
                                            dataPoints: [
                                                @foreach($project->sections as $section_key => $section)
                                                {{-- */$complete = $org->results()->where('section_id', $section_key)->where('information', 'complete')->has('answers')->with('answers')->get();$i=0;/* --}}
                                
                                                    {y: {{ $complete->count() }}, label: "{!! $section->text !!}" },
                                                @endforeach    
                                            ]
                                    },
                                    {        
                                            type: "stackedBar100",
                                            showInLegend: true, 
                                            name: "Incomplete",
                                            dataPoints: [
                                                @foreach($project->sections as $section_key => $section)
                                                {{-- */$incomplete = $org->results()->where('section_id', $section_key)->where('information', 'incomplete')->has('answers')->with('answers')->get();$i=0;/* --}}
                                
                                                    {y: {{ $incomplete->count() }}, label: "{!! $section->text !!}" },
                                                @endforeach
                                            ]
                                    }, 
                                    {        
                                            type: "stackedBar100",
                                            showInLegend: true, 
                                            name: "Error",
                                            dataPoints: [
                                                @foreach($project->sections as $section_key => $section)
                                                {{-- */$error = $org->results()->where('section_id', $section_key)->where('information', 'incomplete')->has('answers')->with('answers')->get();$i=0;/* --}}
                                
                                                    {y: {{ $error->count() }}, label: "{!! $section->text !!}" },
                                                @endforeach
                                            ]
                                    },
                                    {        
                                            type: "stackedBar100",
                                            showInLegend: true, 
                                            name: "Missing",
                                            dataPoints: [
                                                @foreach($project->sections as $section_key => $section)
                                                {{-- */$answers = $org->results()->where('section_id', $section_key)->has('answers')->with('answers')->get();$i=0;/* --}}
                                                
                                                    {y: {!! $org->pcode->count() - $answers->count() !!}, label: "{!! $section->text !!}" },
                                                @endforeach
                                            ]
                                    }
                                    ]
                            });
                            chart.render();
                    }
                    </script>
                    @endpush
                        @foreach($project->sections as $section_key => $section)
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title">
                                     {!! $section->text !!}
                                </div>

                            @if(!empty($section->desc))

                            <span class="text-bold text-muted">{!! $section->desc !!}</span>

                            @endif
                            </div>
                            <div class="panel-body">
                                {{-- */$answers = $org->results()->where('section_id', $section_key)->has('answers')->with('answers')->get();$i=0;/* --}}
                                {{ $answers->count() }}
                                {{ $org->pcode->count() }}
                            </div>
                        </div>
                        @endforeach
                    @endforeach
                  </div>
                @else
                  <div id="{{ $org->short }}" class="tab-pane fade">
                    <h3>{{ $org->name }}</h3>
                    @foreach($org->projects as $project)
                    @push('scripts')
                    <script type="text/javascript">
                    window.onload = function () {
                            var chart = new CanvasJS.Chart("chartContainer",
                            {
                                    title:{
                                            text: "Reporting status."             
                                    },
                                    axisY:{
                                            title: "percent"
                                    },
                                    animationEnabled: true,
                                    toolTip:{
                                            shared: true,
                                            content: "{name}: {y} - <strong>#percent%</strong>",
                                    },
                                    data:[
                                    {        
                                            type: "stackedBar100",
                                            showInLegend: true, 
                                            name: "Complete",
                                            dataPoints: [
                                                @foreach($project->sections as $section_key => $section)
                                                {{-- */$complete = $org->results()->where('section_id', $section_key)->where('information', 'complete')->has('answers')->with('answers')->get();$i=0;/* --}}
                                
                                                    {y: {{ $complete->count() }}, label: "{{$section->text}}" },
                                                @endforeach    
                                            ]
                                    },
                                    {        
                                            type: "stackedBar100",
                                            showInLegend: true, 
                                            name: "Incomplete",
                                            dataPoints: [
                                                @foreach($project->sections as $section_key => $section)
                                                {{-- */$incomplete = $org->results()->where('section_id', $section_key)->where('information', 'incomplete')->has('answers')->with('answers')->get();$i=0;/* --}}
                                
                                                    {y: {{ $incomplete->count() }}, label: "{{$section->text}}" },
                                                @endforeach
                                            ]
                                    }, 
                                    {        
                                            type: "stackedBar100",
                                            showInLegend: true, 
                                            name: "Error",
                                            dataPoints: [
                                                @foreach($project->sections as $section_key => $section)
                                                {{-- */$error = $org->results()->where('section_id', $section_key)->where('information', 'incomplete')->has('answers')->with('answers')->get();$i=0;/* --}}
                                
                                                    {y: {{ $error->count() }}, label: "{{$section->text}}" },
                                                @endforeach
                                            ]
                                    }  
                                    ]
                            });
                            chart.render();
                    }
                    </script>
                    @endpush
                        @foreach($project->sections as $section_key => $section)
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title">
                                     {!! $section->text !!}
                                </div>

                            @if(!empty($section->desc))

                            <span class="text-bold text-muted">{!! $section->desc !!}</span>

                            @endif
                            </div>
                            <div class="panel-body">
                                {{ $org->pcode->count() }}
                            </div>
                        </div>
                        @endforeach
                    @endforeach
                  </div>
                @endif
                @endforeach
            </div>
            
        </div><!-- /.box-body -->
    </div><!--box box-success-->
@endsection
@section('before-scripts-end')
{!! HTML::script('js/vendor/canvasjs/jquery.canvasjs.min.js') !!}
@endsection

@push('scripts')
<script type='text/javascript'>
    //2
</script>
@endpush