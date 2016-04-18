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
          var area = {};
          $(".location").each(function(index){
              var key = $(this).attr('name');
              var val = $(this).val();
              area[key] = val;
          });
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
          });   
    });   
}(jQuery)); 
</script>
