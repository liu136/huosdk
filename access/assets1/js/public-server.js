//*****************************************公益服页面banner***********************************
$(document).ready(function() {
	
	$(".Find .port li").eq(0).addClass("default");
	var W = $(document).width();
	var len = $(".Find .port li").length;
	//给li排好位置
	for(var i = 0; i < len; i++) {
		$(".Find .img li").eq(i).css({
			"left": W * i + "px"
		});
	}
	//点击下面的小点，轮播切换
	$(".Find .port li").click(function() {
			$(this).addClass("default").siblings().removeClass("default");
			var index = $(this).index();
			$(".Find .img").stop(false, true).animate({
				"left": -W * index + "px"
			}, 700)
		})
		//点击左按钮，轮播切换
	$(".Find .prev").click(function() {
			left()
		})
		//点击右按钮，轮播切换
	$(".Find .next").click(function() {
			right()
		})
		//定时器+play()=自动轮播
	var autoPlay = function() {
		lun = setInterval(function() {
			right()

		}, 3000)
	}

	//鼠标放上banner图上，停止轮播
	$(".Find .banner").mouseover(function() {
			clearInterval(lun)
		})
		//鼠标离开，再次自动轮播
	$(".Find .banner").mouseout(function() {
		autoPlay()
	})
	autoPlay()
		//向左滑动
	$(".Find .banner").on("swiperight", function() {
		left()
	});
	//向右滑动
	$(".Find .banner").on("swipeleft", function() {
		right()
	});
	//*******************************封装的函数**************************************************				
	//右轮播
	var right = function() {
			var portIndex = $(".Find .port li.default").index();
			if(portIndex >= 2) {
				portIndex = 0;
				$(".Find .port li").eq(portIndex).addClass("default").siblings().removeClass("default");
				$(".Find .img").stop(false, true).animate({
					"left": -W * portIndex + "px"
				}, 700)
			} else {
				portIndex++;
				$(".Find .port li").eq(portIndex).addClass("default").siblings().removeClass("default");
				$(".Find .img").stop(false, true).animate({
					"left": -W * portIndex + "px"
				}, 700)
			}
		}
		//左轮播
	var left = function() {
		var portIndex = $(".Find .port li.default").index();
		if(portIndex <= 0) {
			portIndex = len - 1;
			$(".Find .port li").eq(portIndex).addClass("default").siblings().removeClass("default");
			$(".Find .img").stop(false, true).animate({
				"left": -W * portIndex + "px"
			}, 700)
		} else {
			portIndex--;
			$(".Find .port li").eq(portIndex).addClass("default").siblings().removeClass("default");
			$(".Find .img").stop(false, true).animate({
				"left": -W * portIndex + "px"
			}, 700)
		}
	}
})