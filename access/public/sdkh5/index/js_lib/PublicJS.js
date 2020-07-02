var PublicJs = {};
PublicJs.IsPhone = function () {//判断是否是手机浏览器
    try {
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
            return true;
        } else {
            return false;
        }
    } catch (e) {
        return false;
    }
}
//鼠标事件
PublicJs.Mouse = {
    Down: (PublicJs.IsPhone() ? "touchstart" : "mousedown"),
    Move: (PublicJs.IsPhone() ? "touchmove" : "mousemove"),
    Up: (PublicJs.IsPhone() ? "touchend" : "mouseup"),
};
//鼠标移动
PublicJs.Move = function (e) {
    var move = { x: 0, y: 0 };
    var _e = e.touches ? e : window.event;
    if (PublicJs.IsPhone()) {
        try {
            // evt.preventDefault(); //阻止触摸时浏览器的缩放、滚动条滚动等
            var touch = _e.touches[0]; //获取第一个触点
            move.x = Number(touch.pageX); //页面触点X坐标
            move.y = Number(touch.pageY); //页面触点Y坐标
        } catch (e) {
            move.x = _e.screenX;
            move.y = _e.screenY;
        }
    }
    else {
        move.x = e.screenX;
        move.y = e.screenY;
    }
    return move;
};