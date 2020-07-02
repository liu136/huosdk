$(function() {
	//轮播
	var flash2In = 0;
	var flash2T = setInterval(function() {
		flash2In++;
		if (flash2In >= $('.imagebox').children().length) {
			flash2In = 0;
		};
		$('.imagebox').children().eq(flash2In).click();

	}, 5000);
	$('.imagebox').children().click(function() {
		flash2In = $(this).index();
		$(this).removeClass('defimg').addClass('curimg').siblings().removeClass('curimg').addClass('defimg');
		$('.flashbox').children().eq(flash2In).removeClass('hide').siblings().addClass('hide');
		$('.flashbox').children().eq(flash2In).css('z-index','9');
		$('.flashbox').children().eq(flash2In).siblings().css('z-index','5');
	});
	$('.falsh_2').hover(function() {
		clearInterval(flash2T);
	}, function() {
		clearInterval(flash2T);
		flash2T = setInterval(function() {
			flash2In++;
			if (flash2In >= $('.imagebox').children().length) {
				flash2In = 0;
			};
			$('.imagebox').children().eq(flash2In).click();
		}, 5000);
	});
	//选项卡
	$('.tab').find('li').click(function() {
		var a = $(this).index();
		$(this).addClass('hd_index').siblings().removeClass();
		$(this).parents('.tab').siblings('.info_nr').children().eq(a).css('display', 'block');
		$(this).parents('.tab').siblings('.info_nr').children().eq(a).siblings().css('display', 'none');
	})
});