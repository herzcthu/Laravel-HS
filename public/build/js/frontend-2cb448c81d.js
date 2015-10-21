
//blueimp/jquery-file-upload
(function ($) {
    'use strict';
    var avatar;
    var decodeHtmlEntity = function(str) {
        return str.replace(/&#(\d+);/g, function(match, dec) {
          return String.fromCharCode(dec);
        });
      };

      var encodeHtmlEntity = function(str) {
        var buf = [];
        for (var i=str.length-1;i>=0;i--) {
          buf.unshift(['&#', str[i].charCodeAt(), ';'].join(''));
        }
        return buf.join('');
      };
    $('#fileupload').fileupload({
        dataType: 'json',
        done: function (e, data) {
            
            avatar = data.result.file.dimensions.square100.filedir;
            var html = data.result.file.original_filename;
            var img = $('<option>'); //Equivalent: $(document.createElement('img'))
                img.val(data.result.id);
                img.attr('data-img-src', '/'+ avatar);
                img.prop('selected', true);
                img.html(html);
                img.appendTo('#mediagrid');
                $('img#avatar').prop('src', '/'+ avatar);
                $("select#mediagrid").imagepicker({
                    hide_select : true,
                    show_label  : false
                  })
           //$('<p/>').text(data.result.original_filedir).appendTo('#medialist');
            $.each(data.result, function (index, file) {
                //console.log(data.result);
               // console.log(index);
               // console.log(file);
                //$('<p/>').text(file.original_filedir).appendTo('#medialist');
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
    
    $("img#avatar").on('change',function()
        {
           console.log($(this).attr('src'));
        });
        
    $("select#mediagrid").imagepicker({
          hide_select : true,
          show_label  : false
        })
    $("#add-link").click(function(){
        var img = $( "#mediagrid option:selected" ).attr('data-img-src');
        $("img#avatar").attr('src', img);
        $("img.profile-avatar").attr('src', img);
        $("#avatar_url").val(img);
    });
    $('#mediabox').on("click", ".pagination>li>a", function (e) {
            e.preventDefault();
            getPosts($(this).attr('href'));
        });
    

    function getPosts(page) {
        $.ajax({
            url : page,
            dataType: 'json',
	}).done(function (data) {
            $('#mediabox').html(data);
            $("select#mediagrid").imagepicker({
                hide_select : true,
                show_label  : false
              })
	}).fail(function () {
            alert('Images could not be loaded.');
	});
    }
}(jQuery));

// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeline', 'timelineEnd', 'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

// Place any jQuery/helper plugins in here.
$(function(){
    /*
     Allows you to add data-method="METHOD to links to automatically inject a form with the method on click
     Example: <a href="{{route('customers.destroy', $customer->id)}}" data-method="delete" name="delete_item">Delete</a>
     Injects a form with that's fired on click of the link with a DELETE request.
     Good because you don't have to dirty your HTML with delete forms everywhere.
     */
    $('[data-method]').append(function(){
        return "\n"+
        "<form action='"+$(this).attr('href')+"' method='POST' name='delete_item' style='display:none'>\n"+
        "   <input type='hidden' name='_method' value='"+$(this).attr('data-method')+"'>\n"+
        "   <input type='hidden' name='_token' value='"+$('meta[name="_token"]').attr('content')+"'>\n"+
        "</form>\n"
    })
        .removeAttr('href')
        .attr('style','cursor:pointer;')
        .attr('onclick','$(this).find("form").submit();');

    /*
     Generic are you sure dialog
     */
    $('form[name=delete_item]').submit(function(){
        return confirm("Are you sure you want to delete this item?");
    });

    /*
     Bind all bootstrap tooltips
     */
    $("[data-toggle=\"tooltip\"]").tooltip();
    $("[data-toggle=\"popover\"]").popover();
    //This closes the popover when its clicked away from
    $('body').on('click', function (e) {
        $('[data-toggle="popover"]').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });
});
$(function() {

   
});
//# sourceMappingURL=frontend.js.map