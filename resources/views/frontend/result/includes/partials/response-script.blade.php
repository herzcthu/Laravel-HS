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
            pages: 15,     // number of pages to cache
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
            
        var ajaxurl =  window.location.href;  
        ajax = ajaxurl.replace('data', 'ajax');
        filter = ajaxurl.replace('response', 'status');
            $('#results-total').DataTable({
                dom: 'ft<"bottom"lp><"clear">',
                lengthMenu: [ 50, 100, 150, 200, 250 ],
                processing: true,
                serverSide: true,
                paging: false,
                searching: false,
                stateSave: true,
                deferRender: true,
                ajax: ajax,
                columns: [
                    { data: 'state', name: 'township'},
                    @foreach($sections as $k => $section)
                    { data: 's{{$k}}complete', name: 's{{$k}}complete',"defaultContent": "0" },
                    { data: 's{{$k}}incomplete', name: 's{{$k}}incomplete',"defaultContent": "0" },
                    { data: 's{{$k}}error', name: 's{{$k}}error',"defaultContent": "0" },
                    { data: 's{{$k}}missing', name: 's{{$k}}missing',"defaultContent": "0" },
                    @endforeach
                    { data: 'total', name: 'total',"defaultContent": "0"},
                ]
            });
            
            $('#results-state').DataTable({
                dom: 'f<"pull-right"i>t<"bottom"lp><"clear">',
                lengthMenu: [ 50, 100, 150, 200, 250 ],
                processing: true,
                serverSide: true,
                paging: false,
                searching: false,                
                stateSave: true,
                deferRender: true,
                ajax: {
                    url:ajax,
                    data: {
                        "area":"state"
                    }
                },
                columns: [
                    { data: 'state', name: 'state'},
                    @foreach($sections as $k => $section)
                    { data: 's{{$k}}complete', name: 's{{$k}}complete',"defaultContent": "0" },
                    { data: 's{{$k}}incomplete', name: 's{{$k}}incomplete',"defaultContent": "0" },
                    { data: 's{{$k}}error', name: 's{{$k}}error',"defaultContent": "0" },
                    { data: 's{{$k}}missing', name: 's{{$k}}missing',"defaultContent": "0" },
                    @endforeach
                    { data: 'total', name: 'total',"defaultContent": "0"},
                ]
            });
            
            $('#results-township').DataTable({                
                dom: '<"row"<"col-xs-6"f><"col-xs-6 text-right"i>><"row"<"col-xs-12"t>><"row"<"col-xs-6"l><"col-xs-6 text-right"p>><"clear">',
                lengthMenu: [ 50, 100, 150, 200, 250 ],
                processing: true,
                serverSide: true,
                searching: true,
                stateSave: true,
                deferRender: true,
                ajax: {
                    url: ajax,
                    pages: 15,
                    data: {
                        area : "township"
                    }
                },
                columns: [
                    { data: 'township', name: 'township'},
                    @foreach($sections as $k => $section)
                    { searchable: false, data: 's{{$k}}complete', name: 's{{$k}}complete',"defaultContent": "0" },
                    { searchable: false, data: 's{{$k}}incomplete', name: 's{{$k}}incomplete',"defaultContent": "0" },
                    { searchable: false, data: 's{{$k}}error', name: 's{{$k}}error',"defaultContent": "0" },
                    { searchable: false, data: 's{{$k}}missing', name: 's{{$k}}missing',"defaultContent": "0" },
                    @endforeach
                    { searchable: false, data: 'total', name: 'total',"defaultContent": "0"},
                ]
            });
            
            
            $("body").tooltip({ selector: '.status-icon' });
        });   
}(jQuery)); 
</script>
@endif
@endsection
