$(function(){
	$(".Find ul.kind li div a").click(function(){
		if($(this).hasClass("all")){
			return;
		}else{
			$(this).addClass("all").siblings().removeClass("all")
		}
	})
})