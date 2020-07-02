$(function() {
	var Rsa = 0;
	var rBpic = $('.rslides1').children().length;
	var rT = true;
	for (var i = 0; i < rBpic; i++) {
		$('.rslides1_tabs').append("<li></li>");
	};
	$('.rslides1_tabs').children().first().addClass('ind');
	$('.prev').click(function() {
		if (!rT)
			return false;
		rT = false;
		Rsa--;
		if (Rsa < 0) {
			Rsa = (rBpic - 1);
		};
		$('.rslides1_tabs').children().eq(Rsa).addClass('ind').siblings().removeClass();
		bPic(1000);
		console.log(Rsa);
	});
	$('.next').click(function() {
		if (!rT)
			return false;
		rT = false;
		Rsa++;
		if (Rsa >= rBpic) {
			Rsa = 0;
		};
		$('.rslides1_tabs').children().eq(Rsa).addClass('ind').siblings().removeClass();
		bPic(1000);
	});
	$('.rslides1_tabs').children().click(function() {
		if (!rT)
			return false;
		rT = false;
		Rsa = $(this).index();
		$(this).addClass('ind').siblings().removeClass();
		bPic(1000);
	});
	var rTimer = setInterval(function() {
		$('.next').click();
	}, 5000);
	$('.rslides1').hover(function() {
		clearInterval(rTimer);
	}, function() {
		clearInterval(rTimer);
		rTimer = setInterval(function() {
			$('.next').click();
		}, 5000);
	});

	function bPic(speed) {
		$('.rslides1').children().eq(Rsa).css('z-index','99');
		$('.rslides1').children().eq(Rsa).siblings().css('z-index','1');
		$('.rslides1').children().eq(Rsa).animate({
			opacity: 1
		}, speed);
		$('.rslides1').children().eq(Rsa).siblings().animate({
			opacity: 0
		}, speed, function() {
			rT = true;
		});
	};
})