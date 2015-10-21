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

    <div class="hidden">
                
                <div id="formTemplate" class="form-group hide">
                    <div class="col-xs-1 form-control-static ansId">
                        _QN_aINDEX
                    </div>
                    <div class="col-xs-2">
                        {!! Form::text('answers[_QN_aINDEX][text]', null, ['id' => 'ans_text_INDEX', 'class' => 'form-control', 'placeholder' => 'Answer']) !!}                     
                    </div>
                    <div class="col-xs-1">
                        {!! Form::select('answers[_QN_aINDEX][type]',[   'text' => 'Text', 
                                                                    'radio' => 'Radio', 
                                                                    'checkbox' => 'Checkbox',
                                                                    'select' => 'Select',
                                                                    'question' => 'Question',
                                                                    'number' => 'Number', 
                                                                    'datetime' => 'Datetime', 
                                                                    'date' => 'Date', 
                                                                    'time' => 'Time', 
                                                                    'week' => 'Week', 
                                                                    'month' => 'Month'], null, ['id' => 'input_type_INDEX', 'class' => 'form-control', 'placeholder' => 'Input Type']) !!}  
                    </div>
                    <div class="col-xs-2">
                        {!! Form::text('answers[_QN_aINDEX][value]', null, ['id' => 'ans_value_INDEX', 'class' => 'form-control', 'placeholder' => 'Value']) !!}
                    </div>
                    <div class="col-xs-2">
                        {!! Form::text('answers[_QN_aINDEX][css]', null, ['id' => 'ans_value_INDEX', 'class' => 'form-control', 'placeholder' => 'validate']) !!}
                    </div>
                    <div class="col-xs-2">
                        {!! Form::text('answers[_QN_aINDEX][remark]', null, ['id' => 'remark_INDEX', 'class' => 'form-control', 'placeholder' => 'Remark']) !!}
                    </div>
                    <div class="col-xs-2">
                        <button type="button" class="btn btn-warning removeButton"><i class="fa fa-minus"></i></button>                    
                    </div>
                </div>
    </div>
<script type="text/javascript">
//console.log(ems.index);
(function ($) {
    $(document).ready(function() {
    var index = ems.index;
    var defaultqnum = $('#qnum').val();
    if(defaultqnum != ''){
        //$('#answersgroup').html($('#answersgroup').html().replace(/(Q[0-9]+)/g, defaultqnum));
        $('#formTemplate').html($('#formTemplate').html().replace(/[^\[>\'\"]*_a/g, defaultqnum+ '_a'));
    }
    $('#qnum').on('keyup change',function(){
        //alert($(this).val());
        //alert($('#answer_0').html());
        var qnum = $(this).val();
        $('#answersgroup #duplicatedForm').html($('#answersgroup #duplicatedForm').html().replace(/[^\[>\'\"]*_a/g, qnum + '_a'));
        $('#formTemplate').html($('#formTemplate').html().replace(/[^\[>\'\"]*_a/g, qnum + '_a'));
    });
    console.log(index);
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
            //alert($clone.html());
            var qnum = $(this).val();
            $('#answer_'+index).html($clone.html().replace(/INDEX/g, index));
        })

        // Remove button click handler
        .on('click', '.removeButton', function() {
            var $row  = $(this).parents('.form-group'),
                //index = $row.attr('data-index');
            //find current element to remove
            $remove = $row.find('[data-index="'+ index +'"]');
            index--;
            $remove.remove();
            
        });
    });
    $(document).ready(function() {
                               $(document).dynamicForm('set', '#duplicated', '#formTemplate')
                                       .dynamicForm('init');
                           });    
}(jQuery)); 
</script>
@stop