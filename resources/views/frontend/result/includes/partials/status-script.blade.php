@section('after-scripts-end')
@if($project->validate == 'pcode')
<script type="text/javascript">
(function ($) {
    //
    // Pipelining function for DataTables. To be used to the `ajax` option of DataTables
    //
    $.fn.dataTable.pipeline = function ( opts ) {
        // Configuration options
        var conf = $.extend( {
            pages: 5,     // number of pages to cache
            url: '',      // script url
            data: null,   // function or object with parameters to send to the server
                          // matching how `ajax.data` works in DataTables
            method: 'GET' // Ajax HTTP method
        }, opts );

        // Private variables for storing the cache
        var cacheLower = -1;
        var cacheUpper = null;
        var cacheLastRequest = null;
        var cacheLastJson = null;

        return function ( request, drawCallback, settings ) {
            var ajax          = false;
            var requestStart  = request.start;
            var drawStart     = request.start;
            var requestLength = request.length;
            var requestEnd    = requestStart + requestLength;

            if ( settings.clearCache ) {
                // API requested that the cache be cleared
                ajax = true;
                settings.clearCache = false;
            }
            else if ( cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper ) {
                // outside cached data - need to make a request
                ajax = true;
            }
            else if ( JSON.stringify( request.order )   !== JSON.stringify( cacheLastRequest.order ) ||
                      JSON.stringify( request.columns ) !== JSON.stringify( cacheLastRequest.columns ) ||
                      JSON.stringify( request.search )  !== JSON.stringify( cacheLastRequest.search )
            ) {
                // properties changed (ordering, columns, searching)
                ajax = true;
            }

            // Store the request for checking next time around
            cacheLastRequest = $.extend( true, {}, request );

            if ( ajax ) {
                // Need data from the server
                if ( requestStart < cacheLower ) {
                    requestStart = requestStart - (requestLength*(conf.pages-1));

                    if ( requestStart < 0 ) {
                        requestStart = 0;
                    }
                }

                cacheLower = requestStart;
                cacheUpper = requestStart + (requestLength * conf.pages);

                request.start = requestStart;
                request.length = requestLength*conf.pages;

                // Provide the same `data` options as DataTables.
                if ( $.isFunction ( conf.data ) ) {
                    // As a function it is executed with the data object as an arg
                    // for manipulation. If an object is returned, it is used as the
                    // data object to submit
                    var d = conf.data( request );
                    if ( d ) {
                        $.extend( request, d );
                    }
                }
                else if ( $.isPlainObject( conf.data ) ) {
                    // As an object, the data given extends the default
                    $.extend( request, conf.data );
                }

                settings.jqXHR = $.ajax( {
                    "type":     conf.method,
                    "url":      conf.url,
                    "data":     request,
                    "dataType": "json",
                    "cache":    false,
                    "success":  function ( json ) {
                        cacheLastJson = $.extend(true, {}, json);

                        if ( cacheLower != drawStart ) {
                            json.data.splice( 0, drawStart-cacheLower );
                        }
                        json.data.splice( requestLength, json.data.length );

                        drawCallback( json );
                    }
                } );
            }
            else {
                json = $.extend( true, {}, cacheLastJson );
                json.draw = request.draw; // Update the echo for each response
                json.data.splice( 0, requestStart-cacheLower );
                json.data.splice( requestLength, json.data.length );

                drawCallback(json);
            }
        }
    };

    // Register an API method that will empty the pipelined data, forcing an Ajax
    // fetch on the next draw (i.e. `table.clearPipeline().draw()`)
    $.fn.dataTable.Api.register( 'clearPipeline()', function () {
        return this.iterator( 'table', function ( settings ) {
            settings.clearCache = true;
        } );
    } );
    $(document).ready(function() {
        function isEven(n) 
        {
           return isNumber(n) && (n % 2 == 0);
        }

        function isOdd(n)
        {
           return isNumber(n) && (Math.abs(n) % 2 == 1);
        }

        function isNumber(n)
        {
           return n == parseFloat(n);
        }
        var url = [location.protocol, '//', location.host, location.pathname].join('');
            @foreach($project->sections as $k => $section)
                $('#section{{$k}}').on('change', function(e){
                    var other = '';
                    if($('#region').val() != ''){
                        other += '&region=' + $('#region').val();
                    }
                    if($('#township').val() != ''){
                        other += '&township=' + $('#township').val();
                    }
                    window.location.href = url + "?section={{$k}}&status=" + $(this).val() + other;
                });
            @endforeach
            $('#region').on('change', function(e){
                window.location.href = url + "?region=" + $(this).val();
            });
            $('#township').on('change', function(e){
                var other1 = '';
                    if($('#region').val() != ''){
                        other1 += '&region=' + $('#region').val();
                    }
                window.location.href = url + "?township=" + $(this).val() + other1;
            });
            $('#station').on('change', function(e){
                window.location.href = url + "?station=" + $(this).val();
            });
            $('#input-code').keyup(function(e) { 
                if(e.which == 13) {
                    var code = $('#input-code').val();
                    window.location.href = url + "?pcode=" + $(this).val();
                }
            });
            $('#phone').keyup(function(e) { 
                if(e.which == 13) {
                    var code = $('#phone').val();
                    window.location.href = url + "?phone=" + $(this).val();
                }
            });
        var ajaxurl =  window.location.href;  
        ajax = ajaxurl.replace('data', 'ajax');
            $('#results-table').DataTable({
                lengthMenu: [10, 25, 50, 100, 150, 200, 250 ],
                processing: true,
                serverSide: true,
                searching: false,
                pageLength: 10,
                ajax: $.fn.dataTable.pipeline( {
                    url: ajax,
                    pages: 5 // number of pages to cache
                } ),
                deferRender: true,
                "columnDefs": [
                    { "orderable": false, "targets": 0 },
                    { "orderable": false, "targets": 1 },
                    { "orderable": false, "targets": 2 },
                    { "orderable": false, "targets": 3 },
                    { "orderable": false, "targets": [ 4 ], "width": "300px" },
                    { "orderable": false, "targets": 5 }
                  ],
                columns: [  
                    { data: 'pcode', name: 'pcode' },
                    { data: 'state', name: 'region' },
                    { data: 'township', name: 'township' },
                    { data: 'village', name: 'station' },
                    { data: 'observers', name: 'observers'},
                    @foreach($project->sections as $k => $section)
                    //{ data: 'results.{{$k}}.information', name: 'section{{$k}}'},
                    
                    { data: function(row, type, val, meta){
                        var status{{$k}};
                        var pc = row.participants.length;
                        var rc = row.results.length;
                        var i = 0;
                        var r = 0;
                            var sections = row.results.reduce(function(sec, cur){ 
                                    sec[cur.section_id] = sec[cur.section_id] || [];
                                    sec[cur.section_id].push(cur);
                                    return sec;
                                }, {});
                            
                            if(typeof sections[{{$k}}] != "undefined"){ //console.log(sections[{{$k}}][0].section_id);
                                $.each(sections[{{$k}}], function(section, results){
                                    if(typeof status{{$k}} == "undefined"){
                                        status{{$k}} = '';
                                    }
                                    //console.log(results);
                                    //if(typeof results[{{$k}}] == "undefined"){
                                    //    return;
                                    //}
                                    @if(isset($section->report))
                                        status{{$k}} += '<img src="{{ asset('img/') }}/' + results.information + '.png" title="'+ results.results.Note.Note_a1 +'" class="status-icon">';
                                    @else
                                        status{{$k}} += '<img src="{{ asset('img/') }}/' + results.information + '.png" title="'+ results.information +'" class="status-icon">';
                                    @endif
                                });
                            }else{
                                    if(typeof status{{$k}} == "undefined"){
                                        status{{$k}} = '';
                                    }
                                    status{{$k}} += '<img src="{{ asset('img/') }}/missing.png" title="missing" class="status-icon">';
                            }
                            
                        return status{{$k}};
                    }, name: 'section{{$k}}', defaultContent: "<i>Not set</i>"},
                    
                    @endforeach
                ]
            });
            
            $("body").tooltip({ selector: '.status-icon' });
        });   
}(jQuery)); 
</script>
@endif
@endsection
