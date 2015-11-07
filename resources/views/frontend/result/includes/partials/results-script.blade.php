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
        var ajaxurl;
            @foreach($project->questions as $k => $question)
                @if(array_key_exists($question->report, $project->reporting))
                $("#question-{!! $question->id !!}").on('change', function(e){
                    var other = '';
                    if($('#region').val() != ''){
                        other += '&region=' + $('#region').val();
                    }
                    if($('#township').val() != ''){
                        other += '&township=' + $('#township').val();
                    }
                    window.location.href = url + "?question={{$question->id}}&answer=" + $(this).val() + other;
                });
                @endif
            @endforeach
            $('#region').on('change', function(e){
                window.location.href = url + "?region=" + $(this).val();
                //ajaxurl = url + "?region=" + $(this).val();
                //e.preventDefault();
            });
            $('#township').on('change', function(e){
                var other1 = '';
                    if($('#region').val() != ''){
                        other1 += '&region=' + $('#region').val();
                    }
                window.location.href = url + "?township=" + $(this).val() + other1;
                //ajaxurl = url + "?district=" + $(this).val();
            });
            $('#station').on('change', function(e){
                window.location.href = url + "?station=" + $(this).val();
                //ajaxurl = url + "?station=" + $(this).val();
            });
            $('#input-code').keyup(function(e) { 
                if(e.which == 13) {
                    var code = $('#input-code').val();
                    window.location.href = url + "?pcode=" + code;
                    //ajaxurl = url + "?code=" + $(this).val();
                }
            });
            $('#phone').keyup(function(e) { 
                if(e.which == 13) {
                    var code = $('#phone').val();
                    window.location.href = url + "?phone=" + $(this).val();
                }
            });
        if(typeof ajaxurl == "undefined"){
        var ajaxurl =  window.location.href;  
         }
        ajax = ajaxurl.replace('data', 'ajax');
            $('#results-table').DataTable({
                lengthMenu: [ 50, 100, 150, 200, 250 ],
                //scrollX: true,
                processing: true,
                serverSide: true,
                searching: false,
                pageLength: 50,
                ajax: ajax,
                columns: [
                    { data: 'pcode', name: 'pcode',"orderable": false },
                    { data: 'incident_id', name: 'incident',"orderable": false},
                    { data: 'cq', name:'cq',"orderable": false},
                    { data: function(row, type, val, meta){ //console.log(row.resultable.state);
                        if(row.resultable_type == 'App\\PLocation'){
                                return row.resultable.state;
                        }else{
                            return '';
                        }
                    }, name: 'state',"orderable": false },
                    { data: function(row, type, val, meta){ //console.log(row.resultable.district);
                        if(row.resultable_type == 'App\\PLocation'){
                                return row.resultable.township;
                        }else{
                            return '';
                        }
                    }, name: 'township',"orderable": false },
                    { data: function(row, type, val, meta){ //console.log(row.resultable.village);
                        if(row.resultable_type == 'App\\PLocation'){
                                return row.resultable.village;
                        }else{
                            return '';
                        }
                    }, name: 'village',"orderable": false },
                    { data: 'observers', name: 'observers',"orderable": false},
                    @foreach($project->questions as $k => $question)
                    @if(array_key_exists($question->report, $project->reporting) && $project->reporting[$question->report]['text'] == 'Incident')
                    { data: function(row, type, val, meta){ //console.log(row.resultable.village);
                            var ans = '';
                        $.each(row.answers, function(i,v){ 
                            if(v.qid == '{{ $question->id }}'){
                                //console.log(v.question.answers[v.akey].text);
                                ans += v.question.answers[v.akey].text + "\n";
                            }
                        });
                        return ans;
                    }, name: '{{ $question->qnum }}',"orderable": false },
                    @endif
                    @endforeach
                    
                ]
            });
            
            $("body").tooltip({ selector: '.status-icon' });
        });   
}(jQuery)); 
</script>
@endif
@endsection
