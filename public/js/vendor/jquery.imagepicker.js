jQuery.fn.imagePicker = function () {

	var startImg = this.children("option:selected").val();
	var startCaption = this.children("option:selected").text();
	var img = $.parseHTML("<img src=\""+startImg+"\" alt=\""+startCaption+"\" title=\""+startCaption+"\" />"); 
	var dropDownObj = this;
	
	$(img).css("cursor","pointer");
	
	var modalIconPicker;
	
	$(img).click(function(){
	
		var modalIconPicker = $("<ol></ol>").attr("id","modalIconPicker");
		
		$(dropDownObj).children().each(function() {
		
			var imageToPick = $("<img />").attr("src", this.value).attr("title",this.text).attr("alt", this.text);
			var listElem = $("<li></li>");
			
			$(imageToPick).click(function(){
				var pickedSrc = $(this).attr("src");
				$(img).attr("src", $(this).attr("src"));
				$(img).attr("alt", $(this).attr("alt"));
				$(img).attr("title", $(this).attr("title"));
				$(this).closest('.ui-dialog-content').dialog('close');
				$(dropDownObj).children().each(function(){
					if($(this).val() == pickedSrc){
						$(this).prop("selected", true);
					} else {
						$(this).removeAttr("selected");
					}
				});
			});
			
			$(listElem).append(imageToPick);
			$(modalIconPicker).append(listElem);
		});

		$(modalIconPicker).dialog( {
										  width: 500,
										  height: 500
										} );
	});
	
	this.after(img);
	this.css("display","none");
    return this;
}