$(document).ready(function() {
	
	$(".newGame .port li").eq(0).addClass("default");
	var W = $(document).width();
	var len = $(".newGame .port li").length;
	//给li排好位置
	for(var i = 0; i < len; i++) {
		$(".newGame .img li").eq(i).css({
			"left": W * i + "px"
		});
	}
	//点击下面的小点，轮播切换
	$(".newGame .port li").click(function() {
			$(this).addClass("default").siblings().removeClass("default");
			var index = $(this).index();
			$(".newGame .img").stop(false, true).animate({
				"left": -W * index + "px"
			}, 700)
		})
		//点击左按钮，轮播切换
	$(".newGame .prev").click(function() {
			left()
		})
		//点击右按钮，轮播切换
	$(".newGame .next").click(function() {
			right()
		})
		//定时器+play()=自动轮播
	var autoPlay1 = function() {
		lun = setInterval(function() {
			// console.log($(".newGame .port li.default").index())
			right()
		}, 5000)
	}

	//鼠标放上banner图上，停止轮播
	$(".newGame .banner").mouseover(function() {
			clearInterval(lun)
		})
		//鼠标离开，再次自动轮播
	$(".newGame .banner").mouseout(function() {
		autoPlay1()
	})
	autoPlay1()
		//向左滑动
	$(".newGame .banner").on("swiperight", function() {
		left()
	});
	//向右滑动
	$(".newGame .banner").on("swipeleft", function() {
		right()
	});
	//*******************************封装的函数**************************************************				
	//右轮播
	var right = function() {
			var portIndex = $(".newGame .port li.default").index();
			if(portIndex >= 2) {
				portIndex = 0;
				$(".newGame .port li").eq(portIndex).addClass("default").siblings().removeClass("default");
				$(".newGame .img").stop(false, true).animate({
					"left": -W * portIndex + "px"
				}, 700)
			} else {
				portIndex++;
				$(".newGame .port li").eq(portIndex).addClass("default").siblings().removeClass("default");
				$(".newGame .img").stop(false, true).animate({
					"left": -W * portIndex + "px"
				}, 700)
			}
		}
		//左轮播
	var left = function() {
		var portIndex = $(".newGame .port li.default").index();
		if(portIndex <= 0) {
			portIndex = len - 1;
			$(".newGame .port li").eq(portIndex).addClass("default").siblings().removeClass("default");
			$(".newGame .img").stop(false, true).animate({
				"left": -W * portIndex + "px"
			}, 700)
		} else {
			portIndex--;
			$(".newGame .port li").eq(portIndex).addClass("default").siblings().removeClass("default");
			$(".newGame .img").stop(false, true).animate({
				"left": -W * portIndex + "px"
			}, 700)
		}
	}
})













