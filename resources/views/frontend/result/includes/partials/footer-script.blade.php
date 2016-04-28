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
        $('.quest').mouseenter(function(){
            var input = $(this).find('input');
            var select = $(this).find('select'); // need to implement later
            var textarea = $(this).find('textarea');
            $.each(input,function(index,value){
                if(typeof value.dataset.logic !== 'undefined' && value.dataset.logic !== 'null'){
                    var logic = JSON.parse(value.dataset.logic);
                    var operator = logic.operator;
                    if(operator === 'skip') {
                        $("#"+logic.lftans).on('click',function(e){
  
                           if(this.checked){
                                if(confirm('Skip to ' + logic.rftquess)){
                                    location.href = "#"+logic.rftquess;
                                }
                            }
                        });
                        
                    }
                }
            });
            //$(this).css("border-radius", "25px");
            
            //$(this).addClass('blue');
        }).on('mouseleave',function(){
            
            //$(this).removeClass('blue');
        });
        //reset input values
        $('.reset').on('click',function(){
            $(this).parents('.quest').find(':input').each(function() {console.log(this)
                if(this.type == 'button'){
                } else if(this.type == 'checkbox' || this.type == 'radio'){
                    this.checked = false;
                    $(this).removeAttr("disabled");
                } else {
                     this.value = '';
                }

            });
        });
        $('.none').on('click',function(){
            $(this).parents('.quest').find(':input').each(function() {//console.log(this)
                if(this.type == 'checkbox' && !$(this).hasClass('none')){
                    this.checked = false;
                    $(this).attr("disabled", "disabled");
                } else {
                     return;
                }

            });
        });
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