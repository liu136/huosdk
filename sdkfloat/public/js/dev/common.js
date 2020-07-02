// 底部通用js
function codeFixed() {
    var e = $("#code"),
        t = $(document).width(),
        n = $(".main").width(),
        r = e.width();
    if (244 > t - n) {
        e.hide();
        return
    }
    e.show().css({
        right: (t - n) / 2 - r - 2
    })
}
codeFixed();
$(window).resize(function(){
    codeFixed();
});


// 弹窗
$(".popup .js-closeBtn").click(function(e){
    e.preventDefault();
    closePop();
});


$(".searchForm").submit(function(){
    var target = $(this);
    var input = target.find(".inputText");
    if(!input.val() || input.val() == input.get(0).defaultValue){
        input.focus();
        input.val("");
        return false;
    }
});

// 新消息提醒关闭
var newMsgCountTip = $(".newMsgCountTip");
if(util.getCookie("mappMsged") != 1 && newMsgCountTip.length){
    $(".newMsgCountTip").show();
}
$(".newMsgCountTip .js-closeBtn").click(function(e){
    e.preventDefault();
    newMsgCountTip.hide();
    // 加cookie
    util.setCookie("mappMsged", "1", 1.0 / 24);
});


$(".js-login").mouseenter(function(event) {
    $(".newMsgCountTip").hide();
    $(".loginTip").show();
}).mouseleave(function(event) {
    $(".loginTip").hide();
});

// curToggler
$(".js-cur-toggler").hover(function(){
    var target = $(this);
    target.addClass("cur");
    target.siblings('.js-cur-toggler').removeClass('cur');
});



$(function(){
	


    //IE6下PNG处理
    var isIE=!!window.ActiveXObject;
	var isIE6=isIE&&!window.XMLHttpRequest;
	if(isIE6){
		//png图片处理
		DD_belatedPNG.fix('.slidesjs-pagination .slidesjs-pagination-item a, .ispeaker, .softpubicon, .gamepubicon, .tuiguangpanel ul li i, .policypanel ul li a, .tl_point, .dservice ul li em, .policypanel img, #faqpanel li a, .servicecontent ul li i, .leftpanel ul li i,.rightpanel ul li i, .intropanel .panel_1');
	}

    //关闭页面时检测表单是否已提交
    //userUnload 全局标记，默认表示当前窗口是用户触发的跳转或关闭

    if(window.userUnload==undefined){
        window.userUnload=true;
    }

    if(window.userUnload==true){
        window.onbeforeunload = function(){
            if($('form').length>0 && $('.Validform_checktip').length>0 && ($('.Validform_right').length>0 || $('.Validform_wrong').length>0) && userUnload){
                event.returnValue="已填写信息将无法保存";
            }
        }
    }



});