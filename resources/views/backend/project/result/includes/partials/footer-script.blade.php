<?php

/* 
 * Copyright (C) 2015 sithu
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
@section('after-scripts-end')
<script type="text/javascript">
(function ($) {
    $(document).ready(function() {
            function validate(url, replacement, output = false){
                $('#validated').html("");
                $.ajax({
                    url : url,
                    dataType: 'json',
                    statusCode: {
                      404: function() {
                        $('#validated').html("<span class='text-danger'>Record not found!</span>");
                      }
                    },
                }).done(function (data) {
                    
                        //console.log(data);
                        //console.log(index);
                        //console.log(location.name);
                        //$('<option />', {
                          //      value: location.id,
                            //    text: location.name + '(' + location.pcode + ')'
                            //}).appendTo('#'+level);
                        //$('<p/>').text(file.original_filedir).appendTo('#medialist');
                    //});
                }).success(function(data, status, response){
                    $('#validator').removeClass('alert-danger');
                    
                    
                    $.each(data, function (index, record) {
                        $('<dl />').attr('id', 'record').addClass('dl-horizontal').appendTo('#validated');
                        $('<dt />').text(index).appendTo('#record');
                        $('<dd />').text(record).appendTo('#record');
                        @if($project->validate == 'pcode')
                            if(index == 'Location ID'){
                                
                                if(output){                            
                                    $('#validator').val($('.hidden-validator').val() + {{'-'.$project->org_id}});
                                }
                                $('.hidden-validator').val($('#validator').val() + {{'-'.$project->org_id}});
                            }
                       @elseif($project->validate == 'person')
                            if(index == 'Observer'){
                                
                                if(output){                            
                                    $('#validator').val($('.hidden-validator').val());
                                }
                                $('.hidden-validator').val($('#validator').val());
                            }
                       @endif
                    });
                }).error(function(){
                    $('#validator').addClass('alert-danger');
                }).fail(function () {
                    console.log('Data could not be loaded.');
                });
            }
          var elements = document.querySelectorAll("[data-expression]");
          $(elements).each(function( index ) {
              $(this).each(function(i){
                  
                  eval($(this).data('expression'));
                  
              });
            console.log( index );
          });
          if( $('.hidden-validator').val() ) {                
                var valid = $('.hidden-validator').val();
                var urlstr = ems.url;
                
                var vurl = urlstr.replace("%7Bpcode%7D", valid );
                validate(vurl, valid, true);
            }
          $('#validator').on('keyup',function(e){
                if (e.shiftKey && e.which == 16) {
                    $(this).val(val.replace(/\#/,''));
                  }
                  console.log(e);
              $('#validator').removeClass('alert-danger');
              var value = $(this).val();
              if( value.length > 4 && value.length < 7){ 
                  var str = ems.url;
                  //set replacement as global variable
                  replacement = value;
                  var url = str.replace("%7Bpcode%7D", replacement );
                  validate(url, replacement);
              }
          }).keydown(function( event ) {
                  if ( event.which == 16 ) {
                    event.preventDefault();
                  }
                });
          $( "form" ).submit(function(e) {
          if( !$('.hidden-validator').val() ) {
            $('#validator').addClass('alert-danger').focus();
            
            e.preventDefault();
            }
          });
        });
    //$('<input>').attr('type','hidden').appendTo('form');    
}(jQuery)); 
</script>
@endsection