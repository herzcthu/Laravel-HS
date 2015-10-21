@extends('backend.layouts.master')

@section('page-header')
    <h1>
        {{ _t('Election 2015 Myanmar') }}
        <small>{{ _t('Reporting Dashboard') }}</small>
    </h1>
@endsection

@section('breadcrumbs')
    <li><a href="{!!route('backend.dashboard')!!}"><i class="fa fa-dashboard"></i> {{ _t('Dashboard') }}</a></li>
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">{!! _t('WELCOME :user', ['user' => auth()->user()->name]) !!}!</h3>
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
                    <h3>{{ _t($org->name) }}</h3>
                    @foreach($org->projects as $project)
                    <div id="{{ $project->id }}" style="height: 300px; width: 100%;"></div>
                    @push('scripts')
                    <script type="text/javascript">
                    getTimeGraphData = {};
                    updateInterval = 5000;
                    $(document).ready(function () {

                        $.getJSON("{{route('ajax.project.status', $project->id)}}", function (result) {
                            var chart = new CanvasJS.Chart("{{ $project->id }}",
                            {
                                    colorSet: "statusColor",
                                    legend:{
                                            reversed: false
                                    },
                                    title:{
                                            text: "{!! _t($project->name.' Reporting status.') !!}"             
                                    },
                                    axisY:{
                                            title: "percent"
                                    },
                                    animationEnabled: true,
                                    toolTip:{
                                            shared: true,
                                            content: "<span style='\"'color: {color};'\"'>{name}</span>: {y} - <strong>#percent%</strong>",
                                    },
                                    data:[
                                    {        
                                            type: "stackedBar100",
                                            showInLegend: true, 
                                            name: "{{ _t('Complete') }}",
                                            dataPoints: result.complete.reverse() 
                                    },
                                    {        
                                            type: "stackedBar100",
                                            showInLegend: true, 
                                            name: "{{ _t('Incomplete') }}",
                                            dataPoints: result.incomplete.reverse()
                                    }, 
                                    {        
                                            type: "stackedBar100",
                                            showInLegend: true, 
                                            name: "{{ _t('Error') }}",
                                            dataPoints: result.error.reverse()
                                    },
                                    {        
                                            type: "stackedBar100",
                                            showInLegend: true, 
                                            name: "{{ _t('Missing') }}",
                                            dataPoints: result.missing.reverse()
                                    }
                                    ]
                            });
                            chart.render();
                            
                            var updateChart = function(){
                            $.getJSON("{{route('ajax.project.status', $project->id)}}", function (result) { 
                                chart.options.data[0].dataPoints = result.complete.reverse();
                                chart.options.data[1].dataPoints = result.incomplete.reverse();
                                chart.options.data[2].dataPoints = result.error.reverse();
                                chart.options.data[3].dataPoints = result.missing.reverse();
                                
                                chart.render(); 
                            });
                            
                            };
                            setInterval(function(){updateChart();}, updateInterval); 
                        });
                        
                          var updateTimeGraph = function(){  $.ajax({
                                    url: "{{route('ajax.project.timegraph', $project->id)}}",
                                    async: false,
                                    dataType: 'json',
                                    success: function(data) {
                                          getTimeGraphData = data;  
                                    }
                                });
                            };
                        updateTimeGraph(); 
                        setInterval(function(){updateTimeGraph()}, updateInterval);
                    });
                    </script>
                    @endpush
                        @foreach($project->sections as $section_key => $section)
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title">
                                     {!! _t($section->text) !!}
                                </div>

                            @if(!empty($section->desc))

                            <span class="text-bold text-muted">{!! _t($section->desc) !!}</span>

                            @endif
                            </div>
                            <div class="panel-body">
                                <div id="p{{$project->id}}s{{$section_key}}" style="height: 300px; width: 100%;"></div>
                                @push('scripts')
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        var chart = new CanvasJS.Chart("p{{$project->id}}s{{$section_key}}", {
                                                        title : {
                                                                text : "{!! _t($section->text) !!}"
                                                        },
                                                         animationEnabled: true,
                                                          axisX:{
                                                            minimum: getTimeGraphData.last   // change here
                                                      //    maximum: 610
                                                         },
                                                          axisY:{
                                                            includeZero: true,
                                                            minimum: 0,
                                                            interval: 5,
                                                          },
                                                        data : [{
                                                                        type : "line",
                                                                        xValueType: "dateTime",
                                                                        dataPoints : getTimeGraphData.p{{$project->id}}s{{$section_key}}
                                                                }
                                                        ]
                                                });
                                        chart.render();
                                        setInterval(function(){
                                            chart.options.data[0].dataPoints = getTimeGraphData.p{{$project->id}}s{{$section_key}};
                                            chart.render()
                                        }, updateInterval);
                                    });
                                </script>
                                @endpush
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
                    $(document).ready(function () {

                        $.getJSON("{{route('ajax.project.status', $project->id)}}", function (result) {
                            var chart = new CanvasJS.Chart("{{ $project->id }}",
                            {
                                    colorSet: "statusColor",
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
                                            dataPoints: result.complete.reverse() 
                                    },
                                    {        
                                            type: "stackedBar100",
                                            showInLegend: true, 
                                            name: "Incomplete",
                                            dataPoints: result.incomplete.reverse()
                                    }, 
                                    {        
                                            type: "stackedBar100",
                                            showInLegend: true, 
                                            name: "Error",
                                            dataPoints: result.error.reverse()
                                    },
                                    {        
                                            type: "stackedBar100",
                                            showInLegend: true, 
                                            name: "Missing",
                                            dataPoints: result.missing.reverse()
                                    }
                                    ]
                            });
                            chart.render();
                            var updateInterval = 1000;
                            var updateChart = function(){
                            result = $.getJSON("{{route('ajax.project.status', $project->id)}}", function (result) { return result });
                            chart.render();
                            };
                            setInterval(function(){updateChart()}, updateInterval); 
                        });
                        getTimeGraphData = $.getJSON("{{route('ajax.project.timegraph', $project->id)}}", function (tg) {
                                        return tg;
                                    });
                    });
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
                                <div id="p{{$project->id}}s{{$section_key}}" style="height: 300px; width: 100%;"></div>
                                @push('scripts')
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        var chart = new CanvasJS.Chart("p{{$project->id}}s{{$section_key}}", {
                                                        title : {
                                                                text : "Dynamic Data"
                                                        },
                                                        data : [{
                                                                        type : "line",
                                                                        dataPoints : getTimeGraphData.p{{$project->id}}s{{$section_key}}
                                                                }
                                                        ]
                                                });

                                        chart.render();
                                    });
                                </script>
                                @endpush
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
    $(document).ready(function () {
        CanvasJS.addColorSet("statusColor",
                [//colorSet Array

                "#36A406",
                "#D8D617",
                "#FF3D2E",
                "#B11361",
                "#90EE90"                
                ]);
    });
</script>
@endpush