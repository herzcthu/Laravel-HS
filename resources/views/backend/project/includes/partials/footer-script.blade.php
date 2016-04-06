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
                
                <div id="sectFormTemplate" class="form-group hide">
                    <div class="col-xs-2">
                        {!! Form::text('sections[INDEX][text]', null, ['class' => 'form-control', 'placeholder' => 'Answer']) !!}                     
                    </div>
                    <div class="col-xs-1">
                        {!! Form::select('sections[INDEX][column]',[   '1' => '1',
                                                                '2' => '2',
                                                                '3' => '3',
                                                                '4' => '4',
                                                                '5' => '5',
                                                                '6' => '6'], null, ['class' => 'form-control']) !!}  
                    </div>
                    <div class="col-xs-2">
                        {!! Form::text("sections[INDEX][formula]", null, ['class' => 'form-control', 'placeholder' => 'DA>(BE+BF)']) !!}                     
                    </div>
                    <div class="col-xs-3">
                        {!! Form::textarea("sections[INDEX][desc]", null, ['rows' => '3', 'class' => 'form-control', 'placeholder' => 'Some text to display on the top of section.']) !!}
                    </div>
                    <div class="col-xs-1">
                        {!! Form::checkbox("sections[INDEX][report]", 1, null, ['class' => 'checkbox']) !!}
                    </div>
                    <div class="col-xs-1">
                        {!! Form::checkbox("sections[INDEX][submit]", 1, true, ['class' => 'checkbox']) !!}
                    </div>
                    <div class="col-xs-2">
                        <button type="button" class="btn btn-default removeSectButton"><i class="fa fa-minus"></i></button>                    
                    </div>
                </div>
                <div id="reportFormTemplate" class="form-group hide">
                    <div class="col-xs-2">
                        {!! Form::text('reporting[INDEX][text]', null, ['class' => 'form-control']) !!}                     
                    </div>
                    <div class="col-xs-2">
                        <button type="button" class="btn btn-default removeReportButton"><i class="fa fa-minus"></i></button>                    
                    </div>
                </div>
    </div>
<script type="text/javascript">
//console.log(ems.index);
(function ($) {
    $(document).ready(function() {
    var sectindex = ems.sectindex;

    $('#sectForm')
        .on('click', '.addSectButton', function() {
            sectindex++;
            
            var $template = $('#sectFormTemplate'),
                $clone    = $template
                                .clone()
                                .removeClass('hide')
                                .removeAttr('id')
                                .attr({"data-index": sectindex, "id": 'sect_' + sectindex})
                                .appendTo('#sectForm');
            
            $('#sect_'+sectindex).html($clone.html().replace(/INDEX/g, sectindex));
        })

        // Remove button click handler
        .on('click', '.removeSectButton', function() {
            var $row  = $(this).parents('.form-group'),
                //index = $row.attr('data-index');
            //find current element to remove
            $remove = $row.find('[data-index="'+ sectindex +'"]');
            sectindex--;
            $remove.remove();
        });
    
    var reportindex = ems.reportindex;
    $('#reportForm')
        .on('click', '.addReportButton', function() {
            reportindex++;
            
            var $template = $('#reportFormTemplate'),
                $clone    = $template
                                .clone()
                                .removeClass('hide')
                                .removeAttr('id')
                                .attr({"data-index": reportindex, "id": 'report_' + reportindex})
                                .appendTo('#reportForm');
                        
            $('#report_'+reportindex).html($clone.html().replace(/INDEX/g, reportindex));
        })

        // Remove button click handler
        .on('click', '.removeReportButton', function() {
            var $row  = $(this).parents('.form-group'),
                //reportindex = $row.attr('data-index');
            //find current element to remove
            $remove = $row.find('[data-index="'+ reportindex +'"]');
            reportindex--;
            $remove.remove();
        });
    });
       
}(jQuery)); 
</script>
@stop