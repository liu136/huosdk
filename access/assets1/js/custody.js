$(function(){
	$(".user_operate .content li a").click(function(){
		var index =$(this).parent("li").index();
		var i=index-1;
		$(this).parent("li").addClass("selected").siblings().removeClass("selected");
		$("#partent-Tabs .tab").eq(i).addClass("active").siblings().removeClass("active");
		
	})

	$(".left_menu ul li").click(function(){
		var index=$(this).index();
		$(this).addClass("hover").siblings().removeClass("hover");
		$(".user_right .tab").eq(index).addClass("active").siblings().removeClass("active")
	})
})