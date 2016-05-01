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
@section('footer')
    <div class="modal fade" id="notice" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
          <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <H2 id="modal-title">Notice</H2>
                      

            </div><!-- Modal header -->
            <div class="modal-body">
                <div id="logicmessage">
                    This is message
                </div>  
            </div><!-- Modal body -->
            <div class="modal-footer">
                <button id="mdok" class="btn btn-success pull-left" type="button" data-dismiss="modal">Ok</button>
                <button id="mdcl" type="button" class="btn btn-default" data-dismiss="modal">Cancle</button>
            </div><!-- Modal footer -->
          </div><!-- Modal content -->
        </div>
    </div><!-- #logic -->
@endsection
@section('after-scripts-end')
<script type="text/javascript">
(function ($) {
    $(document).ready(function() {
        $('.quest').mouseenter(function(e){
            var input = $(this).find('input');
            var select = $(this).find('select'); // need to implement later
            var textarea = $(this).find('textarea');
            
            $.each(input,function(index,value){
                if(typeof value.dataset.logic !== 'undefined' && value.dataset.logic !== 'null'){
                    var logic = JSON.parse(value.dataset.logic);
                    
                    if(logic.operator == 'skip') {
                        $("#"+logic.lftans).on('click',function(e){
                           //e.preventDefault();
                           if(this.checked){
                               var that = this;
                               var qnum = $("#"+logic.rftquess+" label:first").text();
                                $("#logicmessage").html("Skip to "+qnum);
                                $("#notice").modal('show');
                                $("#mdok").click(function(){
                                    location.href = "#"+logic.rftquess;
                                });
                                $("#mdcl").click(function(){                                    
                                    that.checked = false;
                                    $("#notice").modal('hide');
                                });                                    
                            } else {
                                return false;
                            }
                        });                     
                    }
                }
            });
        }).on('mouseleave',function(){
            
        }).on('focusout',function(){
            var input = $(this).find('input');
            var select = $(this).find('select'); // need to implement later
            var textarea = $(this).find('textarea');
            console.log('focusout fired');
            
            $.each(input,function(index,value){
                if(typeof value.dataset.logic !== 'undefined' && value.dataset.logic !== 'null'){
                    var logic = JSON.parse(value.dataset.logic);
                    
                    var lftval = $("#"+logic.lftans).val();
                    var rftval = $("#"+logic.rftans).val();
                    var message = ((typeof logic.message != 'undefined')? logic.message:'Logic Error!');
                    switch(logic.operator) {
                        case '=':
                            if(lftval != rftval){
                               $("#logicmessage").html(message);
                               $("#notice").modal('show');
                            }
                            break;
                        case '>':
                            if(lftval < rftval){
                               $("#logicmessage").html(message);
                               $("#notice").modal('show');
                            }
                            break;
                        case '<':
                            if(lftval > rftval){
                               $("#logicmessage").html(message);
                               $("#notice").modal('show');
                            }
                            break;
                        case 'between':
                            if(lftval < logic.minval || lftval > logic.maxval){
                               $("#logicmessage").html(message);
                               $("#notice").modal('show');
                            }
                            break;
                    }
                }
            });
            
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