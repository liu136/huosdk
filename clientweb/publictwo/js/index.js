$(function() {
	//banner
	lunbo();

	function lunbo() {
		var a = 0;
		var len = $('.banner').children('.banner_pic').children().length;
		var bannerTimer = setInterval(function() {
			$('.banner_btn_next').click();
		}, 5000);
		$('.banner').hover(function() {
			clearInterval(bannerTimer);
		}, function() {
			clearInterval(bannerTimer);
			bannerTimer = setInterval(function() {
				$('.banner_btn_next').click();
			}, 5000);
		});
		$('.banner').children('.banner_btn_next').click(function() {
			a++;
			if(a > len - 1) {
				a = 0;
			}
			$('.banner').children('.banner_pic').children('li').eq(a).addClass('active').siblings().removeClass('active');
			$('.banner').children('.banner_disc').children('span').eq(a).addClass('active').siblings().removeClass('active');
		});
		$('.banner').children('.banner_btn_pre').click(function() {
			a--;
			if(a < 0) {
				a = len - 1;
			}
			$('.banner').children('.banner_pic').children('li').eq(a).addClass('active').siblings().removeClass('active');
			$('.banner').children('.banner_disc').children('span').eq(a).addClass('active').siblings().removeClass('active');
		});
		for(var i = 0; i < len; i++) {
			$('.banner').children('.banner_disc').append("<span></span>")
		};
		$('.banner').children('.banner_disc').children().first().addClass('active');
		var dicWith = $('.banner_disc').width();
		$('.banner').children('.banner_disc').css('margin-left', (-dicWith / 2) + 'px');
		$('.banner').children('.banner_disc').on('click', 'span', function() {
			
			a = $(this).index();
			$(this).addClass('active').siblings().removeClass('active');
			$('.banner').children('.banner_pic').children('li').eq(a).addClass('active').siblings().removeClass('active');
		});
	};
	$('.m_r_con').on('mouseover', 'li', function() {
		$(this).addClass('active').siblings().removeClass('active');
	});
	
	
})