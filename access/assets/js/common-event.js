$(document).ready(function() {
	document.body.scrollTop = 0;
	//搜索类型切换
	$(".addimghd .head-top .scope").click(function(){
		$(this).find("ol").stop(false,true).slideToggle("fast")
	})
		$(".addimghd .head-top .scope").mouseleave(function(){
		$(this).find("ol").stop(false,true).slideUp("fast")
	})
	
	$(".navList li").click(function() {
		$(".navList li").removeClass('active');
		$(this).addClass('active');
	})

	$(".openDown").click(function(){
		// var h1 = $(window).height();
		var h1 = window.screen.availHeight;
		// var h2 = $(".windows-div").height();
		var h2 = 260;
		var middleh = (h1 - h2) / 2;
		$(".windows-div").css("top",middleh - 100 + "px");
		$(".windows").fadeIn(500);
		$(".win-close").click(function(){
			$(".windows").fadeOut(100);
		})
		$(".win-img").click(function(){
			$(".windows").fadeOut(100);
		})
		$(".windows").click(function(){
			$(".windows").fadeOut(100);
		})
	})
	console.log($(".openDown").html())

	$(".windows-div").click(function(event) {
		event.stopPropagation();
	})
})