function Code() {
	var code = "";
	var codeLength = 4; //验证码的长度  
	var random = new Array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'y', 'z'); //随机数  
	for(var i = 0; i < codeLength; i++) { //循环操作  
		var index = Math.floor(Math.random() * 61); //取得随机数的索引（0~35）  
		code += random[index]; //根据索引取得随机数加到code上  
	}
	return code;
};;
(function($) {
	$.fn.firstDemo = function(opts) {
		opts = $.extend({}, $.fn.firstDemo.defaults, opts);
		return this.each(function() {
			var _this = $(this);
			switch(opts["funType"]) {
				case "content":
					var a = _this.html().substring(0, opts['limitCount']);
					_this.html(a + "...");
					break;
				case "option":
					_this.children().click(function() {
						var a = $(this).index();
						$(this).addClass(opts['addClass']).siblings().removeClass(opts['addClass']);
						$(opts['optionClass']).children().eq(a).removeClass(opts["addOptionClass"]).siblings().addClass(opts["addOptionClass"])
					});
					break;
				case "page":
					//分页
					var pageIndex = opts["pageIndex"]; //当前页码
					var pageCount = opts["pageCount"]; //总页码
					var i = 0;
					//显示非当前页
					pageNav.pHtml = function(pageNo, CP, showPageNo) {
						showPageNo = showPageNo || pageNo;
						var H = "<a href='javascript:pageNav.go(" + pageNo + "," + CP + ");' class='pageNum'>" + showPageNo + "</a>";
						return H;
					};
					//显示当前页
					pageNav.pHtml2 = function(pageNo) {
						var H = "<span class=" + opts["addClass"] + ">" + pageNo + "</span>";
						return H;
					};
					pageNav.go = function(IP, CP) {
						//$("#pagination").html(this.nav(IP, CP)); //如果使用jQuery可用此句
						_this.html(this.nav(IP, CP));
						if(this.fn != null) {
							this.fn(this.IP, this.CP);
						};
					};

					pageNav.pre = "&laquo;";
					pageNav.next = "&Raquo;";
					pageNav.fn = function(IP, CP) {
						_this.next().html("当前页:" + IP + " / " + CP);
					};
					//
					pageNav.go(pageIndex, pageCount);
					break;
				default:
					break;

			};

		});
	};

	$.fn.firstDemo.defaults = {
		funType: "", //功能类型：文字（content） 选项卡（option）分页(page)
		limitCount: 50, //限制的个数
		optionClass: "", //需要操作的类名
		addOptionClass: "", //需要添加删除的类名
		addClass: "", //需要添加删除的类名
		pageCount: 10, //总页数
		pageIndex: 1 //当前页码
	};
})(jQuery);
//
//分页
var pageNav = pageNav || {};
pageNav.fn = null;
//IndexPage当前页码--IP;
//CountPage总页数--CP;
pageNav.nav = function(IP, CP) {
	//只有一页，直接显示1
	if(CP <= 1) {
		this.IP = 1;
		this.CP = 1;
		return this.pHtml2(1);
	};
	var re = "";
	//第一页
	if(IP <= 1) {
		IP = 1;
	} else {
		//非第一页
		re += this.pHtml(IP - 1, CP, "&laquo");
		//总是显示第一页
		re += this.pHtml(1, CP, "1");
	};
	//校正页码
	this.IP = IP;
	this.CP = CP;
	//开始页码
	var start = 2;
	var end = (CP < 9) ? CP : 9;
	//是否显示前置省略号，即大于10的开始页码
	if(IP >= 7) {
		re += "<a href='javascript:;'>...</a>";
		start = IP - 4;
		var e = IP + 4;
		end = (CP < e) ? CP : e;
	};
	for(var i = start; i < IP; i++) {
		re += this.pHtml(i, CP);
	};
	re += this.pHtml2(IP);
	for(var i = IP + 1; i <= end; i++) {
		re += this.pHtml(i, CP);
	}
	if(end < CP) {
		//re += "<li><a href='javascript:;'>...</a></li>";
		re += "<a href='javascript:;'>...</a>";
		//显示最后一页页码,如不需要则去掉下面这一句
		re += this.pHtml(CP, CP);
	};
	if(IP < CP) {
		re += this.pHtml(IP + 1, CP, "&raquo");
	}
	return re;
};

//发送验证码
//a标签按钮
function sendCode(a) {
	var cT = true;
	var cTimer;
	a.click(function() {
		if(!cT)
			return false;
		cT = false;
		clearInterval(cTimer);
		var a = 60;
		var _this = $(this);
		cTimer = setInterval(function() {
			a--;
			if(a <= 0) {
				_this.html("重新发送验证码");
				cT = true;
			} else {
				_this.html(a);
			}
		}, 1000);
	});
};

function sendCode2(a) {
	var codeTimer;
	var codeT = true;
	a.click(function() {
		if(!codeT)
			return false;
		codeT = false;
		clearInterval(codeTimer);
		var a = 2;
		var _this = $(this);
		codeTimer = setInterval(function() {
			a--;
			if(a <= 0) {
				_this.val("重新发送验证码");
				codeT = true;
			} else {
				_this.val(a);
			}
		}, 1000);
	});
}

//判断input
function CheckInputName(obj, arr) {
	var WordStart = /^[a-zA-Z]/, //字母开头
		wordNum = /^[a-zA-Z][0-9a-zA-Z]{6,18}$/, //字母开头，含字母和数字，6-18位
		newPh = /^((\(\d{2,3}\))|(\d{3}\-))?13\d{9}$/, //手机号码
		NoSymbol = /^[\u4e00-\u9fa5a-zA-Z0-9]+$/, //中文、字母、数字
		reChinese = /^[\u4e00-\u9fa5]+$/, //中文
		price = /^(([1-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/, //金额
		Num = /^[0-9]+$/, //数字
		reEmail = /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/;
	obj.bind('click keyup blur', function() {
		var a = $(this).val();
		var b = $(this).parent().index();
		var c = $(this).attr('name');
		switch(c) {
			//数字
			case "Num":
				var re = Num.test(a);
				if(!re) {
					$(this).next('.in_fo').removeClass('hide');
					arr[b] = false;
				} else {
					$(this).next('.in_fo').addClass('hide');
					arr[b] = true;
				}
				break;
				//邮箱验证
			case "Email":
				var re = reEmail.test(a);
				if(!re) {
					$(this).next('.in_fo').removeClass('hide');
					arr[b] = false;
				} else {
					$(this).next('.in_fo').addClass('hide');
					arr[b] = true;
				}
				break;
				//金额验证
			case "money":
				var re = price.test(a);
				if(!re) {
					$(this).next('.in_fo').removeClass('hide');
					arr[b] = false;
				} else {
					$(this).next('.in_fo').addClass('hide');
					arr[b] = true;
				}
				break;
				//中文、字母、数字验证
			case "NoSymbol":
				var re = NoSymbol.test(a);
				if(!re) {
					$(this).next('.in_fo').removeClass('hide');
					arr[b] = false;
				} else {
					$(this).next('.in_fo').addClass('hide');
					arr[b] = true;
				}
				break;
				//中文验证
			case "ChineseName":
				var re = reChinese.test(a);
				if(!re) {
					$(this).next('.in_fo').removeClass('hide');
					arr[b] = false;
				} else {
					$(this).next('.in_fo').addClass('hide');
					arr[b] = true;
				}
				break;
				//开户人名称验证
			case "bankUser":
				var re = reChinese.test(a);
				if(!re) {
					$(this).next('.in_fo').removeClass('hide');
					arr[b] = false;
				} else {
					$(this).next('.in_fo').addClass('hide');
					arr[b] = true;
				}
				break;
				//开户地方
			case "bankAddress":
				var re = reChinese.test(a);
				console.log(re);
				if(!re) {
					$(this).next('.in_fo').removeClass('hide');
					arr[b] = false;
				} else {
					$(this).next('.in_fo').addClass('hide');
					arr[b] = true;
				}
				break;
				//判断密码格式条件
			case "newPass":
				var f = a.length;
				//									var g = $(this).parent().index();
				if(f >= 6 && f <= 18) {
					$('.newPass li').eq(2).children('i').removeClass().addClass('agree');
					arr[b] = true;
				} else {
					$('.newPass li').eq(2).children('i').removeClass().addClass('disagree');
					arr[b] = false;
				};
				if(WordStart.test(a)) {
					$('.newPass li').eq(1).children('i').removeClass().addClass('agree');
					arr[b] = true;
				} else {
					$('.newPass li').eq(1).children('i').removeClass().addClass('disagree');
					arr[b] = false;
				};
				if(wordNum.test(a)) {
					$('.newPass li').eq(0).children('i').removeClass().addClass('agree');
					$('.newPass li').eq(3).children('i').removeClass().addClass('agree');
					arr[b] = true;
				} else {
					$('.newPass li').eq(0).children('i').removeClass().addClass('disagree');
					$('.newPass li').eq(3).children('i').removeClass().addClass('disagree');
					arr[b] = false;
				};
				if(a != $('input[name=newPass2]').val()) {
					$('input[name=newPass2]').next('.in_fo').removeClass('hide')
				} else {
					$('input[name=newPass2]').next('.in_fo').addClass('hide');
				};
				break;
				//修改登录密   
				//判断新密码和确认密码是否相等
			case "newPass2":
				if(a != $('input[name=newPass]').val()) {
					$(this).next('.in_fo').removeClass('hide');
					arr[b] = false;
				} else {
					$(this).next('.in_fo').addClass('hide');
					arr[b] = true;
				};
				break;
				//忘记密码
				//判断新密码和确认密码是否相等
			case "pass1":
				if(a == "" || null || undefined) {
					$(this).next('.in_fo').removeClass('hide');
					arr[b] = false;
				} else {
					$(this).next('.in_fo').addClass('hide');
					if(a != $('input[name=pass2]').val()) {
						$('input[name=pass2]').next('.in_fo').removeClass('hide');
						arr[b] = false;
					} else {
						$('input[name=pass2]').next('.in_fo').addClass('hide');
						arr[b] = true;
					};
				};
				break;
			case "pass2":
				if(a != $('input[name=pass1]').val()) {
					$(this).next('.in_fo').removeClass('hide');
					arr[b] = false;
				} else {
					$(this).next('.in_fo').addClass('hide');
					arr[b] = true;
				};
				break;
				//手机验证
			case "newPhone":
				//验证手机时候存在
				if(newPh.test(a)) {
					$('.newBtn').removeAttr('disabled').addClass('active');
					$(this).next('.in_fo').addClass('hide');
					arr[b] = true;
				} else {
					$('.newBtn').attr('disabled', 'disabled').removeClass('active');
					$(this).next('.in_fo').removeClass('hide');
					arr[b] = false;
				}
				break;
				//判断是否为空
			default:
				if(a == "" || null || undefined) {
					$(this).next('.in_fo').removeClass('hide');
					arr[b] = false;
				} else {
					$(this).next('.in_fo').addClass('hide');
					arr[b] = true;
				};
				break;
		}
	});

};

//下拉框
function InputSelect() {
	$('select option').each(function() {
		$('.select_list').append('<li>' + $(this).html() + '</li>');
	});
	//				$('.select').html($('.select_list').children().eq(0).html());
	$('.select_btn').click(function() {
		$('.select_list').toggleClass('hide');
		$(this).toggleClass('select_up');
	});
	$('.select').click(function() {
		$('.select_btn').click();
	});
	$('.select_list li').click(function() {
		$('.select_btn').click();
		$(this).addClass('active').siblings().removeClass();
		var a = $(this).html();
		var b = $(this).index();
		$('.select').val(a);
		$('.select').blur();
		$('select').children().eq(b).attr('selected', 'selected').siblings().removeAttr('selected');
		$(this).parent().addClass('hide');
	});
};

//财务信息管理
//添加 删除
function finMessage(a, b) {
	var arr = new Array();
	//删除按钮
	$('.dete_list').click(function() {
		var _this = $(this);
		$('.mask,.m_new').removeClass('hide');
		$('.m_new').html(a);
		sendCode2($('.code_btn'));
		CheckInputName($('.m_new input[type=text],.m_new input[type=password]'), arr);
		document.documentElement.style.overflow = 'hidden';
		//绑定删除验证
		$('.new_sub_btn').click(function() {
			$('.m_new input[type=text]').blur();
			var ThisBtn = $(this);
			if($.inArray(false, arr) == -1) {
				alert("删除成功");
				_this.parent().remove();
				$('.mask,.m_new').addClass('hide');
				//表单提交则执行下面代码
				// ThisBtn.parent().submit();
			};
		});
	});
	//添加按钮
	$('.addList').click(function() {
		$('.mask,.m_new').removeClass('hide');
		document.documentElement.style.overflow = 'hidden';
		$('.m_new').html(b);
		InputSelect();
		///判断条件
		CheckInputName($('.m_new input[type=text]'), arr);
		//弹窗提交按钮
		$('.new_sub_btn').click(function() {
			$('.m_new input[type=text]').blur();
			if($.inArray(false, arr) == -1) {
				alert("添加成功");
				$('form').submit();
			};
		});
	});
	//弹窗关闭按钮
	$('.m_new').on('click', '.m_new_close', function() {
		$('.m_new,.mask').addClass('hide');
		document.documentElement.style.overflowY = 'scroll';
		//初始化新手机填写框
		$('.newBtn').attr('disabled', 'disabled').removeClass('active');
		$('.m_new .in_fo').addClass('hide');
		$('.m_new input[type=text]').val("");
	});
};

//日期筛选
function dateTitle() {
	$('.f_d_btn').click(function() {
		$(this).addClass('active').siblings('.f_d_btn').removeClass('active');
		//		$(this).children('input[type=radio]').attr('checked','checked');
		//		$(this).siblings().children('input[type=radio]').removeAttr('checked');
		if($('.c_d_btn').hasClass('active')) {
			$('.custom_date').removeClass('hide');
		} else {
			$('.custom_date').addClass('hide');
			$('.custom_date input[type=text]').val("");
		}
	});
	//账单日期选择——自定义
	$('.custom_date a').click(function() {
		//判断时间段是否为空
		if($('#d4311').val() != "" | null | undefined && $('#d4312').val() != "" | null | undefined) {
			console.log($('#d4311').val(), $('#d4312').val());
			$(this).parents('.c_d_btn').parents('form').submit();
		};
	});
}

//多条件筛选
function selectChoice() {
	$('select').each(function() {
		$(this).children().each(function() {
			var a = $(this).html();
			$(this).parent().siblings('.o_s_select').append("<li>" + a + "</li>");
		});
	});
	$('.o_s_select').on('click', 'li', function() {
		$(this).addClass('active').siblings().removeClass('active');
		var a = $(this).html();
		var b = $(this).index();
		$(this).parent().siblings('select').children().eq(b).attr('selected', 'selected');
		$(this).parent().siblings('select').children().eq(b).siblings().removeAttr('selected');
		$(this).parent().siblings('input').val(a);
		$(this).parent().addClass('hide');
	});
	$('.search_screen input[type=text]').click(function() {
		$(this).next().toggleClass('hide');
		$(this).parent().siblings().children('.o_s_select').addClass('hide');
		$(this).parents('.fl').siblings().find('.o_s_select').addClass('hide');
	});
};

//添加页面
//获取数据 得到option
//遍历option
function addSelect(obj) {
	$('select').each(function(i) {
		$(this).children().each(function(k) {
			$(this).parent().siblings('.select_list').append('<li>' + $(this).html() + '</li>');
		});
	});

	obj.on('click', '.select_btn', function() {
		$(this).toggleClass('select_up');
		$(this).parent().siblings().find('.select_btn').removeClass('select_up');
		$(this).siblings('.select_list').toggleClass('hide');
		$(this).parent().siblings().find('.select_list').addClass('hide');
	})
	obj.on('click', '.select', function() {
		$(this).siblings('.select_btn').click();
	});
	obj.on('click', '.select_list li', function() {
		var a = $(this).html();
		var b = $(this).index();
		$(this).parent().siblings('.select').val(a);
		$(this).parent().siblings('.select').blur();
		$(this).addClass('active').siblings().removeClass('active');
		$(this).parent().siblings('.select_btn').click();
		$(this).parent().siblings('select').children().eq(b).attr('selected', 'selected').siblings().removeAttr('selected');
	});
}

//转账、提现
function addCash(obj1, obj2, string, arr, htmlCon) {
	obj1.click(function() {
		$(this).addClass('active').siblings().removeClass('active');
		$(this).children('input[type=radio]').attr('checked', 'checked');
		$(this).siblings().children('input[type=radio]').removeAttr('checked');
		if($(this).hasClass(string)) {
			$('.mask').toggleClass('hide');
			obj2.toggleClass('hide').html(htmlCon);
			document.documentElement.style.overflow = 'hidden';
			obj2.find('.m_n_fl').each(function(i) {
				arr[i] = false;
			});
		};
		//下拉
		InputSelect();
		CheckInputName($('.m_new input[type=text]'), arr);
	});
};
///判断IE
var h = '<div id="zm"></div>';
h += '<div id="browser_list">'
h += '<div class="browser_word">';
h += '<p style="font-size: 18px;">很抱歉！您正在是使用的浏览器版本过低，无法正常使用我们的网站， 请升级后再尝试！</p>';
h += '<p style="font-size: 12px;margin: 20px 0 50px 0;">为获得最佳浏览体验，建议您升级或选用其他浏览器，我们列出了一些较受欢迎的浏览器供您升级，点击下列图标将转跳到相应下载页面:</p>';
h += '</div><div class="browser_logo">';
h += '<a href="https://support.microsoft.com/zh-cn/help/17621/internet-explorer-downloads" class="browser" target="_blank"><img src="img/IE_logo.png" alt="IE_logo" width="100%" /> Internet Explert 8+</a>';
h += '<a href="http://www.firefox.com.cn/" class="browser" target="_blank"><img src="img/FF_logo.png" alt="火狐" /> Mozilla Firefox</a>';
h += '<a href="http://www.google.cn/intl/zh-CN/chrome/browser/desktop/index.html" class="browser" target="_blank"><img src="img/g_logo.png" alt="谷歌" /> Google Chrome</a>';
h += '<a href="http://www.opera.com/zh-cn" class="browser" target="_blank"><img src="img/op_logo.png" alt=""> Opera</a>';
h += '<a href="https://support.apple.com/zh-cn/HT204416" class="browser" target="_blank"><img src="img/sar_logo.png" alt="safari"> Safari</a></div></div></div>';
var d = document.createElement('div');
d.setAttribute('id', 'mm');
d.innerHTML = h;

addLoadListener(function() {
	var dd = document.getElementsByTagName('body')[0];
	dd.appendChild(d);
	var v = 10;
	var ua = navigator.userAgent.toLowerCase();
	var isIE = ua.indexOf("msie") > -1;

	window.onresize = iMark;
	var a = document.getElementById('mm');
	if(isIE) {

		var s = ua.match(/msie ([\d.]+)/)[1];
		if(s < v) {

			a.style.display = 'block';
			document.documentElement.style.overflow = 'hidden';
			document.body.onkeydown = keyFunc;
			window.onscroll = scroll;
		} else {
			a.style.display = 'none';
			document.documentElement.style.overflowY = 'scroll';
			document.body.onkeydown = '';
			window.onscroll = '';
		}
	} else {

		a.style.display = 'none';
		document.documentElement.style.overflowY = 'scroll';
		document.body.onkeydown = '';
		window.onscroll = '';

	};

});
//
var st;
var scroll = function(e) {
	clearTimeout(st);
	st = setTimeout(function() {
		window.scrollTo(loc.scrollLeft, loc.scrollTop);
	}, 5);
};

var move = function(e) {
	e.preventDefault && e.preventDefault();
	e.returnValue = false;
	e.stopPropagation && e.stopPropagation();
	return false;
};
var keyFunc = function(e) {
	if(37 <= e.keyCode && e.keyCode <= 40) {
		return move(e);
	}
};

//
var winWidth = 0;
var winHeight = 0;

function iMark() {
	//
	if(document.documentElement && document.documentElement.clientHeight && document.documentElement.clientWidth) {
		winHeight = document.documentElement.clientHeight;
		winWidth = document.documentElement.clientWidth;
	}
	//
	document.getElementById('mm').style.height = document.body.clientHeight + 'px';
	$('.zm').css({
		height: $(window).height() + 'px'
	});
	document.getElementById('zm').style.height = document.body.clientHeight + 'px'
		//
	var a = document.getElementById('browser_list');
	a.style.left = (winWidth - a.offsetWidth) / 2 + 'px';
	a.style.top = (winHeight - a.offsetHeight) / 2 + 'px';
};
//绑定事件
function addLoadListener(fn) {
	if(typeof window.addEventListener != 'undefined') {
		window.addEventListener('load', fn, false);
	} else if(typeof document.addEventListener != 'undefined') {
		document.addEventListener('load', fn, false);
	} else if(typeof window.attachEvent != 'undefined') {
		window.attachEvent('onload', fn);
	} else {
		var oldfn = window.onload;
		if(typeof window.onload != 'function') {
			window.onload = fn;
		} else {
			window.onload = function() {
				lodfn();
				fn();
			};
		}
	}
};
//