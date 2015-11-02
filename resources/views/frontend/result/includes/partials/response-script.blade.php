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
            
        var ajaxurl =  window.location.href;  
        ajax = ajaxurl.replace('data', 'ajax');
        filter = ajaxurl.replace('response', 'status');
            $('#results-table').DataTable({
                lengthMenu: [ 50, 100, 150, 200, 250 ],
                processing: true,
                serverSide: true,
                searching: true,
                pageLength: 100,
                deferRender: true,
                ajax: ajax,
                columns: [
                    { data: 'group', name: 'group'},
                    @foreach($sections as $k => $section)
                    { data: function(row, type, val, meta){
                            return '<a href="'+filter+'?region='+row.group+'&section={{$k}}&status=complete" target="_blank">'+row[{{$k}}].complete+'</a>';
                    }, name: 'complete{{$k}}', defaultContent: "<i>Not set</i>" },
                    { data: function(row, type, val, meta){
                            return '<a href="'+filter+'?region='+row.group+'&section={{$k}}&status=incomplete" target="_blank">'+row[{{$k}}].incomplete+'</a>';
                    }, name: 'incomplete{{$k}}', defaultContent: "<i>Not set</i>" },
                    { data: function(row, type, val, meta){
                        return '<a href="'+filter+'?region='+row.group+'&section={{$k}}&status=error" target="_blank">'+row[{{$k}}].error+'</a>';
                    }, name: 'error{{$k}}', defaultContent: "<i>Not set</i>" },
                    { data: function(row, type, val, meta){
                        return '<a href="'+filter+'?region='+row.group+'&section={{$k}}&status=missing" target="_blank">'+row[{{$k}}].missing+'</a>';
                    }, name: 'missing{{$k}}', defaultContent: "<i>Not set</i>" },
                    @endforeach
                    { data: 'totalmissing', name: 'totalmissing'},
                ]
            });
            
            $("body").tooltip({ selector: '.status-icon' });
        });   
}(jQuery)); 
</script>
@endif
@endsection
