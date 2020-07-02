var Apple = {};
Apple.UA  = navigator.userAgent;

Apple.Device = false;
Apple.Types  = ["iPhone", "iPod", "iPad"];
for (var d = 0; d < Apple.Types.length; d ++) {
    var t        = Apple.Types[d];
    Apple[t]     = ! ! Apple.UA.match(new RegExp(t, "i"));
    Apple.Device = Apple.Device || Apple[t];
}
var ua = navigator.userAgent.toLowerCase();

function $(id) {
    return document.getElementById(id);
}

function popPrompt(imgurl) {
    if (/iphone|ipod|ipad/.test(ua)) {
        if (/weibo/.test(ua) || /micromessenger/.test(ua)) {
            $("weixinimg").innerHTML     = "<img src='/public/ios/images/wxins2.png' width='640' height='418' />";
            $("popweixin").style.display = "block";
        }
    }
}

function select_jump() {
    var kuandu         = document.documentElement.clientWidth;
    var gaodu          = document.documentElement.clientHeight;
    var gaodu1         = document.body.clientHeight;
    var zz             = document.getElementById("zz");
    var jump           = document.getElementById("baidu_select");
    zz.style.width     = kuandu + "px";
    zz.style.height    = gaodu + "px";
    var top            = Math.ceil((gaodu - 330) / 2);
    var left           = Math.ceil((kuandu - 496) / 2);
    jump.style.top     = top + "px";
    jump.style.left    = left + "px";
    zz.style.display   = "block";
    jump.style.display = "block";
    var closed         = document.getElementById("jump_closed1");
    closed.onclick     = function () {
        jump.style.display = "none";
        zz.style.display   = "none";
    };
    var url            = window.location.href;
    var btn            = document.getElementById('btn_copy');
    var test           = document.getElementById('select_url');
    test.innerHTML     = url;
    btn.addEventListener('click', function (evtnt) {
        foo();
    }, false);
    function foo() {
        var size = test.innerHTML.length;
        selectText(test, 0, size);  //选择所有文本
    }

    function selectText(textbox, startIndex, stopIndex) {
        if (textbox.setSelectionRange) {
            textbox.setSelectionRange(startIndex, stopIndex);
        } else if (textbox.createTextRange) {
            var range = textbox.createTextRange();
            range.collapse(true);
            range.moveStart('character', startIndex);
            range.moveEnd('character', stopIndex - startIndex);
            range.select();
        }

        textbox.focus();

    }
}