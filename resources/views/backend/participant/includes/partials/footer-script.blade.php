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
//participant data
//console.log(ems.url);

(function ($) {
    'use strict';
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    // get participant role
      var datarole = $('select#prole option:selected').data('role');
      // show or hide location fields based on participant role.
      for(var i = 3; i >= datarole; i--){
          $('#level'+i).removeClass('hide');
        }
      $('#prole').on('change', function(){
        var datarole = $('select#prole option:selected').data('role');
        // show or hide location fields based on participant role.
        for(var i = 3; i >= datarole; i--){
          $('#level'+i).removeClass('hide');
        }
        for(var i = 0; i < datarole; i++){
          $('#level'+i).addClass('hide');
        }
      });
    $('#plocation').on("change", function (e) {
        var location = $(this).val();
        var role = $('#prole').find(":selected");
            e.preventDefault();
            getPosts(role.data('role'), location);
        });    
    

    function getPosts(level, location) {
        var str = ems.url[level];
        var url = str.replace("%7Bid%7D/", location+"/");
        $('#ajax_insert').html("");
        $.ajax({
            url : url,
            dataType: 'json',
	}).done(function (data) {
            $('<label />', {class:"col-lg-2 control-label", text: capitalizeFirstLetter(level)}).appendTo('#ajax_insert');
            $('<div />', {class:"col-lg-10", id:level+'div'}).appendTo('#ajax_insert');
            $('<select />', {class:"form-control", id: level, name: "locations["+level+"]"}).appendTo('#'+level+'div');
            $.each(data, function (index, location) {
                //console.log(data);
                //console.log(index);
                //console.log(location.name);
                $('<option />', {
                        value: location.id,
                        text: location.name + '(' + location.pcode + ')'
                    }).appendTo('#'+level);
                //$('<p/>').text(file.original_filedir).appendTo('#medialist');
            });
	}).fail(function () {
            console.log('Data could not be loaded.');
	});
    }
    
    $(document).ready(function() {
          var area = {};
          $( ".location" ).autocomplete({
            source: function (request, response)
            {
                var location = this.element.attr('name');
                var org = document.getElementById('org_id').value;
                $.ajax(
                {
                    url: "{!! route('ajax.locations.searchname') !!}",
                    dataType: "json",
                    data:
                    {
                        term: request.term,
                        area: area,
                        location: location,
                        org: org
                    },
                    success: function (data)
                    {
                        response(data);
                    }
                });
            },
            //minLength: 2,
            select: function( event, result ) {
                //console.log(result);
                var input = document.getElementsByName(result.item.key);
                input[0].value = result.item.value;
                
                //console.log(input);
            }
          }).each(function(index){
              var key = $(this).attr('name');
              var val = $(this).val();
              area[key] = val;
          });
          
      });
}(jQuery)); 
</script>
@stop
