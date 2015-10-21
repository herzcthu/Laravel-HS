@section('after-scripts-end')
@if($project->validate == 'pcode')
<script type="text/javascript">
(function ($) {
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
            @foreach($sections as $k => $section)
                $('#section{{$k}}').on('change', function(e){
                    window.location.href = url + "?section={{$k}}&status=" + $(this).val();
                });
            @endforeach
            $('#region').on('change', function(e){
                window.location.href = url + "?region=" + $(this).val();
            });
            $('#district').on('change', function(e){
                window.location.href = url + "?district=" + $(this).val();
            });
            $('#station').on('change', function(e){
                window.location.href = url + "?station=" + $(this).val();
            });
            $('#input-code').keyup(function(e) { 
                if(e.which == 13) {
                    var code = $('#input-code').val();
                    window.location.href = url + "?code=" + $(this).val();
                }
            });
        var ajaxurl =  window.location.href;  
        ajax = ajaxurl.replace('data', 'ajax');
            $('#results-table').DataTable({
                lengthMenu: [ 50, 100, 150, 200, 250 ],
                processing: true,
                serverSide: true,
                searching: false,
                pageLength: 100,
                ajax: ajax,
                columns: [
                    { data: 'code', name: 'code' },
                    { data: 'state', name: 'region' },
                    { data: 'district', name: 'district' },
                    { data: 'village', name: 'station' },
                    { data: 'observers', name: 'observers'},
                    @foreach($sections as $k => $section)
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
                                        status{{$k}} += '<img src="{{ asset('img/') }}/' + results.information + '.png" title="'+ results.information +'" class="status-icon">';
                                    
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
