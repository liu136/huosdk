var tipsTimer = 0;
$.tips = function(message, success, time) {
    time = typeof time !== 'undefined' ? time : 3000;
    var html = ['<div id="tips" class="tips clearfix">',
        '<i class="tips-icon"></i>',
        '<span class="tips-txt"></span>',
        '</div>'
    ].join("");
    if ($('#tips').length == 0) {
        $(html).appendTo(document.body);
    };
    $('#tips .tips-txt').html(message);
    var type = success ? "tips-success" : "tips-warning";
    $('#tips').show().removeClass('tips-success tips-warning').addClass(type);
    //iframe内
    if (top !== self) {
        var width =  Math.min($(document.body).width() - 30, 500);
        $('#tips').css({
            top: 30,
            width: width,
            marginLeft: -width/2
        });
        $('#tips .tips-txt').css({
            width: (width - 50)
        });
    }else{
        var topValue = Math.max(document.body.scrollTop, document.documentElement.scrollTop, 105);
        $('#tips').css('top', topValue);
    }
    clearTimeout(tipsTimer);
    tipsTimer = setTimeout(function() {
        $('#tips').hide(300);
    }, time);
}