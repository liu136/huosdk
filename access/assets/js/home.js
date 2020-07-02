$(document).ready(function() {
	//首页类型游戏推荐切换
	$(".addimghd .recommend-content .tjTab span").hover(function(){
		var index=$(this).index();
		$(this).addClass("on").siblings().removeClass("on");
		$(".addimghd  .recommend-content .tjContent .tj-c").eq(index).addClass("tj-cur").siblings().removeClass("tj-cur")
	})

	//首页开服、开测列表切换
	$(".addimghd .startTest .titleHolder h2").hover(function(){
		var index=$(this).index();
		$(this).addClass("active").siblings().removeClass("active");
		$(".addimghd  .tab").eq(index).removeClass("hide").siblings().addClass("hide")
	})
});
