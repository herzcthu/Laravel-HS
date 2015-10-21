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
//console.log(ems.index);
(function ($) {
    $(document).ready(function() {
          $( "#location" ).autocomplete({
            source: "{!! route('ajax.locations.searchname') !!}",
            //minLength: 2,
            select: function( event, result ) {                
              $.each(result, function(key, value){
                  $('#location_id').val(value.id);
                    //$.each(value, function(index, value){
                        //console.log( index + ": " + value );
                    //});
                });
            }
          });
      });
    
    $(document).ready(function() {   
    //var index = ems.index;
    var index = 1;
    $('#duplicatedForm')
        .on('click', '.addButton', function() {
            index++;
            
            var $template = $('#formTemplate'),
                $clone    = $template
                                .clone()
                                .removeClass('hide')
                                .removeAttr('id')
                                .attr({"data-index": index, "id": 'answer_' + index})
                                .appendTo('#duplicatedForm');
                        
            // Update the name attributes
            //$clone
            //    .find('[name="text"]').attr('name', 'answer[' + index + '].text').end()
            //    .find('[name="type"]').attr('name', 'answer[' + index + '].type').end()
            //    .find('[name="value"]').attr('name', 'answer[' + index + '].value').end()
            //    .find('[name="remark"]').attr('name', 'answer[' + index + '].remark').end();
            //$html = $clone.html();
            $('#answer_'+index).html($clone.html().replace(/INDEX/g, index));
        })

        // Remove button click handler
        .on('click', '.removeButton', function() {
            var $row  = $(this).parents('.form-group'),
                index = $row.attr('data-index');
            //find current element to remove
            $remove = $row.find('[data-index="'+ index +'"]');
            
            $remove.remove();
        });
    });
    $(document).ready(function() {
                               $(document).dynamicForm('set', '#duplicated', '#formTemplate')
                                       .dynamicForm('init');
                           });    
}(jQuery)); 
</script>
