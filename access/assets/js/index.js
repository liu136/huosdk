// $(function(){
// 	setTimeout(function() {
// 		$(".addimghd .recommend-content .tjTab span").click(function(){
// 		var index=$(this).index();
// 		$(this).addClass("on").siblings().removeClass("on");
// 		$(".addimghd  .recommend-content .tjContent .tj-c").eq(index).addClass("tj-cur").siblings().removeClass("tj-cur")
// 	})
// 	}, 2000)
// })

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

	//搜索类型切换
	// $(".addimghd .head-top .scope").click(function(){
	// 	$(this).find("ol").stop(false,true).slideToggle("fast")
	// })
	// 	$(".addimghd .head-top .scope").mouseleave(function(){
	// 	$(this).find("ol").stop(false,true).slideUp("fast")
	// })


	
	$(".Find .zui p.title a").hover(function(){
		var index=$(this).index();
		$(this).addClass("active").siblings().removeClass("active");
		$(".Find .zui .tabs .content").eq(index).addClass("active").siblings().removeClass("active")
	})


	$("#Gift-zone .zone .fullWidth .tab li").hover(function(){
		var index=$(this).index();
		$(this).addClass("active").siblings().removeClass("active");
		$("#Gift-zone .zone .fullWidth .tabs .content").eq(index).addClass("active").siblings().removeClass("active")
	})
	
	//开测表
	$(".Open .openTab span").hover(function(){
		var index=$(this).index();
		$(this).addClass("active").siblings().removeClass("active");
		$(".Open .tabs .open-tab").eq(index).addClass("active").siblings().removeClass("active")
	})
	//资讯中心
	$("#Info .indexContent .title a").hover(function(){
		var index=$(this).index();
		$(this).addClass("active").siblings().removeClass("active");
		$("#Info .main .info-tabs .info-list").eq(index).addClass("active").siblings().removeClass("active")
	})
	//个人中心
	$("#user .left li").hover(function(){
		var index=$(this).index();
		$(this).addClass("active").siblings().removeClass("active");
		$("#user .right .text").eq(index).addClass("active").siblings().removeClass("active")
	})
	//火币充值
	$("#pay p.p .span a").click(function(){
		$(this).addClass("active").siblings().removeClass("active");
//		$("#user .right .text").eq(index).addClass("active").siblings().removeClass("active")
	})
});
