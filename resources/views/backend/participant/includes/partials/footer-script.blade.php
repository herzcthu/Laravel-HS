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
    $('#prole').on("change", function (e) {
        var role = $(this).find(":selected");
        
        var location = $('#plocation').val();
            e.preventDefault();
            getPosts(role.data('role'), location);
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
}(jQuery)); 
</script>
@stop
