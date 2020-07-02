$(function() {
	$("#search li").click(function(){
		var index=$(this).index();
		$(this).addClass("active").siblings().removeClass("active");
		$("#search .tabs .content").eq(index).addClass("active").siblings().removeClass("active");
	})
})