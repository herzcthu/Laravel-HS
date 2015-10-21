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
        
          var elements = document.querySelectorAll("[data-expression]");
          $(elements).each(function( index ) {
              $(this).each(function(i){
                  
                  eval($(this).data('expression'));
                  
              });
            console.log( index );
          });

        });
    //$('<input>').attr('type','hidden').appendTo('form');    
}(jQuery)); 
</script>
@endsection