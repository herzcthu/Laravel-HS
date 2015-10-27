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
                $("#question-{!! $question->id !!}").on('change', function(e){
                    window.location.href = url + "?question={{$question->id}}&answer=" + $(this).val();
                    //ajaxurl = url + "?question={{$question->id}}&answer=" + $(this).val();
                    //e.preventDefault();
                });
            @endforeach
            $('#region').on('change', function(e){
                window.location.href = url + "?region=" + $(this).val();
                //ajaxurl = url + "?region=" + $(this).val();
                //e.preventDefault();
            });
            $('#district').on('change', function(e){
                window.location.href = url + "?district=" + $(this).val();
                //ajaxurl = url + "?district=" + $(this).val();
            });
            $('#station').on('change', function(e){
                window.location.href = url + "?station=" + $(this).val();
                //ajaxurl = url + "?station=" + $(this).val();
            });
            $('#input-code').keyup(function(e) { 
                if(e.which == 13) {
                    var code = $('#input-code').val();
                    window.location.href = url + "?code=" + $(this).val();
                    //ajaxurl = url + "?code=" + $(this).val();
                }
            });
        if(typeof ajaxurl == "undefined"){
        var ajaxurl =  window.location.href;  
         }
        ajax = ajaxurl.replace('data', 'ajax');
            $('#results-table').DataTable({
                lengthMenu: [ 50, 100, 150, 200, 250 ],
                scrollX: true,
                processing: true,
                serverSide: true,
                searching: false,
                pageLength: 50,
                ajax: ajax,
                columns: [
                    { data: 'code', name: 'code' },
                    { data: 'incident_id', name: 'incident'},
                    { data: 'cq', name:'cq'},
                    { data: function(row, type, val, meta){ //console.log(row.resultable.state);
                        if(row.resultable_type == 'App\\PLocation'){
                                return row.resultable.state;
                        }else{
                            return '';
                        }
                    }, name: 'state' },
                    { data: function(row, type, val, meta){ //console.log(row.resultable.district);
                        if(row.resultable_type == 'App\\PLocation'){
                                return row.resultable.district;
                        }else{
                            return '';
                        }
                    }, name: 'district' },
                    { data: function(row, type, val, meta){ //console.log(row.resultable.village);
                        if(row.resultable_type == 'App\\PLocation'){
                                return row.resultable.village;
                        }else{
                            return '';
                        }
                    }, name: 'village' },
                    @foreach($project->questions as $k => $question)
                    { data: function(row, type, val, meta){ //console.log(row.resultable.village);
                            var ans = '';
                        $.each(row.answers, function(i,v){ 
                            if(v.qid == '{{ $question->id }}'){
                                console.log(v.question.answers[v.akey].text);
                                ans = v.question.answers[v.akey].text;
                            }
                        });
                        return ans;
                    }, name: '{{ $question->qnum }}' },
                    
                    @endforeach
                    
                ]
            });
            
            $("body").tooltip({ selector: '.status-icon' });
        });   
}(jQuery)); 
</script>
@endif
@endsection
