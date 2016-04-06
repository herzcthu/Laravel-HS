Q26_a3var = $("#Q26_a2").val();
$("input[name='answer[Q26][a_q26_radio]'").on("change",function(){
	Q26_a3this = $(this).is(:checked)? $(this).val():false;
	if(Q26_a3var==Q26_a3this){
	$("#Q26_a3").addClass("alert alert-danger");
	}else{
	$("#Q26_a3").removeClass("alert alert-danger");
	}
	});

$("#Q26_a3").on("click",function(){
	$(this).removeClass("alert alert-danger");
});

$("#Q26_a3").on("focusout",function(){
	Q26_a3this = $("input[name='answer[Q26][a_q26_radio]']:checked").val();
	if($(this).val() == "" && Q26_a3var==Q26_a3this){
		$(this).addClass("alert alert-danger");
	}else{
		$(this).removeClass("alert alert-danger");
	}
});

$( "form" ).submit(function(e) {
	Q26_a3this = $("input[name='answer[Q26][a_q26_radio]']:checked").val();
	alert(Q26_a3this);
	if($("#Q26_a3").val() == "" && Q26_a3var==Q26_a3this){
		$("#Q26_a3").addClass("alert alert-danger");
		e.preventDefault();
	}else{
		$("#Q26_a3").removeClass("alert alert-danger");
	}
});