// 检测两个div是否碰撞
function check(obj1, obj2) {
    //以obj1作为固定的参照物，使用时注意div是否有top与left，如果没有设置的话会是空值  
    //obj2在obj1的上面 obj2.top+obj2.height<obj1.top   
    //obj2在obj1的下面 obj2.top>obj1.top+obj1.height   
    //obj2在obj1的左面 obj2.left+obj2.width<obj1.left   
    //obj2在obj1的右面 obj2.left>obj1.left+obj1.width  
    var obj1top = parseInt($(obj1).offset().top);
    var obj1left = parseInt($(obj1).offset().left);
    var obj1width = parseInt($(obj1).width());
    var obj1height = parseInt($(obj1).height());
    var obj2top = parseInt($(obj2).offset().top);
    var obj2left = parseInt($(obj2).offset().left);
    var obj2width = parseInt($(obj2).width());
    var obj2height = parseInt($(obj2).height());
    if ((obj2top + obj2height < obj1top) || (obj2top > obj1top + obj1height) || (obj2left + obj2width < obj1left) || (obj2left > obj1left + obj1width)) {
        return true;
    } else {
        return false;
    }
}

//悬浮球
var SuspendedBall = {
    ShowWidth: 500, //显示悬浮球的页面宽度
    //添加悬浮球 参数集合可中包含有 top、left、scc、class四个属性
    Add: function (obj) {
        if ($(".SuspendedBall").length == 0) {
            $("body").append("<div class=\"SuspendedBall\"><div></div></div>");
            $('#conDiv').hide();
        }
        if (obj) {
            var _this = $(".SuspendedBall");
            if (typeof (obj.top) != "undefined") {
                _this.offset({
                    top: obj.top
                });
            }
            if (typeof (obj.left) != "undefined") {
                _this.offset({
                    left: obj.left
                });
            }
            if (typeof (obj.css) != "undefined") {
                _this.css(obj.css);
            }
            if (typeof (obj.class) != "undefined") {
                _this.addClass(obj.class);
            }
        }
        SuspendedBallContent.init();//悬浮球窗口信息,在悬浮球添加的时候调用,悬浮球在登录后添加
    },
    //控制悬浮球移动，显示的方法 其中UpFun是指的当触摸或鼠标抬起的时候的执行的方法
    Move: function (UpFun) { //第一个参数是鼠标抬起的事件触发，第二个参数是创建的时候添加的其他事件
        var x = 0,
                y = 0;
        var sx = 0,
                sy = 0;
        var mx = 0,
                my = 0;
        var isDown = false;
        var isMove = false;
        $(window).resize(function () {
            if ($(window).width() < SuspendedBall.ShowWidth) {
                // $(".SuspendedBall").show();
            } else {
                //$(".SuspendedBall").hide();
            }
        });
        /*
         $("body").bind(PublicJs.Mouse.Down, function (e) {
         if ($(window).width() < SuspendedBall.ShowWidth) {
         $(".SuspendedBall").show();
         $("#conDiv").hide();
         }
         });*/
        /*$(".BallBox").bind(PublicJs.Mouse.Down, function (e) {
         if ($(window).width() < SuspendedBall.ShowWidth) {
         $(".SuspendedBall").show();
         $(".BallBox").hide();
         }
         return false;//取消元素事件向下冒泡
         });*/
        $(".SuspendedBall").bind(PublicJs.Mouse.Down, function (e) {
            //#region 去除原有的动画样式
            var right = $(window).width() - $(this).width();
            var bottom = $(window).height() - $(this).height();
            var ballW = $(".SuspendedBall").width();
            if ($(this).hasClass("ToLeft")) {
                $(this).removeClass("ToLeft").offset({
                    left: 0 - ballW / 2
                });
            } else if ($(this).hasClass("ToTop")) {
                $(this).removeClass("ToTop").offset({
                    top: 0
                });
            } else if ($(this).hasClass("ToBottom")) {
                $(this).removeClass("ToBottom").offset({
                    top: bottom
                });
            } else if ($(this).hasClass("ToRight")) {
                $(this).removeClass("ToRight").offset({
                    left: right + ballW / 2
                });
            }
            //#endregion-----
            isDown = true;
            x = $(this).offset().left;
            y = $(this).offset().top;
            var move = PublicJs.Move(e);
            sx = move.x;
            sy = move.y;
            return false; //取消元素事件向下冒泡
        });
        // 移动事件
        $(".SuspendedBall").bind(PublicJs.Mouse.Move, function (e) {
            if (isDown) {
                var move = PublicJs.Move(e);
                mx = move.x - sx; //获取鼠标移动了多少
                my = move.y - sy; //获取鼠标移动了多少

                var movemunber = 5; //当触摸的时候移动像素小于这个值的时候代表着不移动
                if ((mx) > movemunber || (0 - mx) > movemunber || (my) > movemunber || (0 - my) > movemunber) {
                    isMove = true;
                }
                var _top = (y + my),
                        _left = (x + mx);
                var maxtop = $(window).height() - $(this).height(); //获取最小的高度
                var maxleft = $(window).width() - $(this).width(); //获取最大的宽度
                _top = _top < 0 ? 0 : (_top > maxtop ? maxtop : _top); //避免小球移除移出去
                _left = _left > 0 ? _left : 0; //避免小球移除移出去
                $(this).offset({
                    top: _top,
                    left: _left
                });
                mx = move.x;
                my = move.y;
                // isMove = true;
            }
            var checkd = check(".ball_close", ".SuspendedBall");
            window.top.sdkModule.deviceType !=  "pc" && $(".ball_close").show();
            if (!checkd) {
                $(".ball_close").addClass("cur");
            } else {
                $(".ball_close").removeClass("cur");
            }
            return false; //取消元素事件向下冒泡
        });
        //移动抬起事件
        $(".SuspendedBall").bind(PublicJs.Mouse.Up, function (e) {
            var _this = this;
            ///添加定时器，是因为有的时候move事件还没运行完就运行了这个事件，为了给这个时间添加一个缓冲时间这里定义了10毫秒
            setTimeout(function () {
                if (isMove) { //如果移动了执行移动方法
                    var move = {
                        x: $(_this).offset().left,
                        y: $(_this).offset().top
                    };
                    var width = $(window).width() / 2;
                    var height = $(window).height() / 2;
                    var ToLeftOrRight = "left";
                    var ToTopOrBottom = "top";
                    var MoveTo = "x";
                    if (move.x > width) {
                        ToLeftOrRight = "right";
                        move.x = 2 * width - move.x; //左右边距
                    }

                    $(_this).removeClass("ToLeft").removeClass("ToTop").removeClass("ToBottom").removeClass("ToRight"); //去除原样式
                    if (MoveTo == "x") {
                        if (ToLeftOrRight == "left") {
                            $(_this).addClass("ToLeft");
                        } else {
                            $(_this).addClass("ToRight");
                        }
                    }
                } else {
                    if (typeof (UpFun) == "function") {
                        UpFun();
                    }
                    $(".SuspendedBall").hide();
                    $("#conDiv").removeClass("fadeOutLeft").addClass("fadeInLeft").show();
                }
                isDown = false;
                isMove = false;
            }, 10);
            var checkd = check(".ball_close", ".SuspendedBall");
            $(".ball_close").hide();
            if (!checkd) {
                $(".ball_close,.SuspendedBall").hide();
            }
            return false; //取消元素事件向下冒泡
        })
    },
    //这个方法是显示页面上面的悬浮球方法
    ShowBall: function () {
        $(".SuspendedBall").show();
        $("#conDiv").removeClass("fadeInLeft").addClass("fadeOutLeft").fadeOut();
    },
    //这个方法是隐藏悬浮球(退出登录时使用)
    HideBall: function () {
        $("#conDiv").hide();
        $(".SuspendedBall").hide();
    },
    //这个方法是显示页面上面的悬浮球菜单的方法
    ShowBox: function () {
        $(".SuspendedBall").hide();
        $("#conDiv").removeClass("fadeOutLeft").addClass("fadeInLeft").show();
    },
    //这个方法是给悬浮球菜单添加内容的方法
    BoxHtml: function (html) {
        //$(".BallBoxInfo").html(html);
    },
    //这个方法是获取到父级页面的悬浮球的方法
    Partent: function () {
        if (typeof (window.parent.SuspendedBall) != "undefind") {
            return window.parent.SuspendedBall;
        }
        return null;
    }
};

//frame页面点击隐藏顶级父页面悬浮球菜单的方法
function FrameBodyClick() {
    var topWin = (function (p, c) {
        while (p != c) {
            c = p;
            p = p.parent
        }
        return c
    })(window.parent, window);
    $("body").bind(PublicJs.Mouse.Down, function (e) {
        if (topWin.SuspendedBall) {
            if ($(window).width() < topWin.SuspendedBall.ShowWidth) {
                topWin.SuspendedBall.ShowBall();
            }
        }
    });
}


var SuspendedBallContent = {
    _cookie_name: 'ltBarData5',
    content_url: function () {
        // var _getParamValue = function (url, key) {
        //     var regex = new RegExp(key + "=([^&]*)", "i");
        //     return url.match(regex)[1];
        // };
        // //var regex = new RegExp("id=([^&]*)", "i");

        // //var mat = location.href.match(regex); //[1]
        // var id = _getParamValue(location.href, 'id');
        if(window.parent.sdkModule.appid){
            return "/float.php/Mobile/User/getuserinfo?appid=" + window.parent.sdkModule.appid;
        }else{
            return null;
        }
    },
    expiresDate: (function () {
        var d = new Date();
        return d.setTime(d.getTime() + (30 * 60 * 1000));
    })(),
    _ajaxData: {},
    _UserData:  function () {
        var val = {
            appid:window.parent.sdkModule.appid
        };
        return val;
    },
    init: function () {
        // $('#ballUserinfoDiv').remove();
        var ajax_url = this.content_url();
        console.log(ajax_url);
        if (ajax_url == null) {
            console.log("悬浮球用户数据url获取失败");
            return;
        }
        var _d;
        $.ajax({
            url: ajax_url,
            async: false,
            success: function (res) {
                _d = res;
                console.log(_d);
               
                
            }
        });
        if(_d && _d.status == 1){
            // this._ajaxData = JSON.parse(_d);
            this._ajaxData = _d ;
            console.log(this._ajaxData);
            this.get_userinfo();
            this.get_custom();
            this.get_packagelist();
            this.get_mypackage();

        }else{
            console.log("悬浮球用户数据获取失败");
        }
       
        //  if (_d && _d.status == 1) {
        //     this.get_custom();//客服信息
        //     this.get_userinfo();//用户信息
        //     this.get_bindmsg();//绑定信息
        //     this.get_packs();//礼包信息
        //     this.get_recommend();//推荐消息
        // }

    },
    get_userinfo:function(){
        var userData = this._ajaxData.userinfo;
        $("#account").html("账号:"+userData.account);
        $("#account").attr('data-clipboard-text',userData.account);
        $("#uid span").html("UID:"+userData.uid);
        $("#uid span").attr('data-clipboard-text',userData.uid);
        if(userData.bind_mail==1){
            $("#bindEmail span").html("绑定邮箱(已绑定)");
            $("#bindEmail span").css("color","#66CC66");
        }else{
            $("#bindEmail span").html("绑定邮箱())");
            $("#bindEmail span").css("color","#FF0000");
        }
        if(userData.bind_phone==1){
            $("#bindPhone_phone_old").attr('is_bind_phone','1');
            $("#bindPhone_phone_old").parent().parent().show();
            $("#bindPhoneB span").html("绑定手机(已绑定)");
            $("#bindPhoneB span").css("color","#66CC66");
        }else{
            $("#bindPhone_phone_old").attr('is_bind_phone','0');
            $("#bindPhone_phone_old").parent().parent().hide();
            $("#bindPhoneB span").html("绑定手机(未绑定)");
            $("#bindPhoneB span").css("color","#FF0000");
        }
        //实名认证
        // if(userData.bind_real==1){
        //     $("#bindRealnameLiNo").hide();
        //     $("#bindRealnameLiOk").show();
        // }else if(userData.bind_real==0){
        //     $("#bindRealnameLiNo").show();
        //     $("#bindRealnameLiOk").hide();
        // }else{
        //     $("#bindRealnameLiNo").remove();
        //     $("#bindRealnameLiOk").remove();
        // }
    },
    get_custom:function(){
        var contactData = this._ajaxData.contact;
        var qqnum = contactData[0].qq;
        var insert_html="";
        if(qqnum!=null){
            if(window.parent.sdkModule.deviceType==undefined){
                    insert_html = "QQ:<span class=\"insert_qq\">"+qqnum+"</span>";
                var clipboard = new Clipboard(".insert_qq",{
                    text:function(){
                        return qqnum;
                    }
                });
                clipboard.on('success',function(e){
                    showNotice("号码复制成功");
                });
                clipboard.on('error',function(e){
                    console.log("复制失败");
                });
            }else{
                switch(window.parent.sdkModule.deviceType){
                    case 'pc':
                        insert_html = "QQ:"+"<a style=\"color:#000\" href=\"tencent://message?uin="+qqnum+"\&Site=sc.chinaz.com\&Menu=yes\">"+qqnum+"</a>" ;
                        break;
                    case 'android':
                        insert_html = "QQ:"+"<a style=\"color:#000\" href=\"mqqwpa://im/chat?chat_type=wpa\&uin="+qqnum+"\&version=1\&src_type=web\&web_src=oicqzone.com\">"+qqnum+"</a>" ;
                        break;
                    case 'ipad':
                        insert_html = "QQ:"+"<a style=\"color:#000\" href=\"mqq://im/chat?chat_type=wpa\&uin="+qqnum+"\&version=1\&src_type=web\">"+qqnum+"</a>" ;
                        break;
                    case 'iphone':
                        insert_html = "QQ:"+"<a style=\"color:#000\" href=\"mqq://im/chat?chat_type=wpa\&uin="+qqnum+"\&version=1\&src_type=web\">"+qqnum+"</a>" ;
                        break;
                    default:
                        insert_html = "QQ:<span class=\"insert_qq\">"+qqnum+"</span>";
                }   
            }
            $(".kefu-qq div").html(insert_html);
        }else{
            $(".kefu-qq div").html("暂无联系方式");    
        }
        if(contactData[0].content!=null){
            $(".kefu-content").html(""+contactData[0].content);
        }else{
            $(".kefu-scontent").html("请添加客服QQ解答疑问");
        }
        
    },
    get_packagelist:function(){
        var allgift = this._ajaxData.allgift;
        if(allgift==null || allgift.length==0){
            return ;
        }
        var html = '';
        for (var i = 0; i < allgift.length; i++) {
            var giftListData = allgift[i];
            html += "<dl gift-id="+giftListData.giftid+"><dt><h4>"+giftListData.giftname+"</h4><p style=\"color:red\">礼包剩余: "+giftListData.remain+"/"+giftListData.total+"</p><p>"+giftListData.content+"</p></dt><dd><a href=\"javascript:;\" target=\"_self\" class=\"start gift_btn own_gift\">领取</a></dd></dl>";
        }
        $(".user_all_gift_list").html(html);        
    },
    get_mypackage:function(){
        var mygift = this._ajaxData.mygift;
        if(mygift==null || mygift.length==0){
            return ;
        }
        var html = '';
        for (var i = 0; i < mygift.length; i++) {
            var giftListData = mygift[i];
            html += "<dl><dt><h4>"+giftListData.giftname+"</h4><p style=\"color:red\">礼包码: "+giftListData.code+"</p><p>"+giftListData.content+"</p></dt><dd><a href=\"javascript:;\" target=\"_self\" data-clipboard-text=\""+giftListData.code+"\" class=\"start gift_btn\">复制</a></dd></dl>";

        }
        $(".user_my_gift_list").html(html);   
    },
    get_recommend:function(){

    },


    // gzh:function(){
    //     var gzhData = this._ajaxData.gzh;
    //       $('.service_tit').html('公众号:'+gzhData.title); 
      
    //      $("#gzh_kefu").attr("src", gzhData.wx_img);
    //      $("#gzh_quit").attr("src", gzhData.wx_img);
    // },
    
    // //退出时推荐游戏
    // quitRecommend: function () {
    //      var quitData = this._ajaxData.quit_recommend;
    //      $("#service_adv_img").attr("src", quitData.img);
    //       $("#service_adv_img").attr("data-gameurl", quitData.url);
    // },

    // //最近在玩
    // playHistory: function () {
    //     if (this._ajaxData.rec_game_list == undefined)
    //         return;
    //     var originalData = this._ajaxData.play_history;
    //     var html = '';
    //     for (var i = 0; i < originalData.length; i++) {
    //         var playListData = originalData[i];
    //         html += '<dl>'
    //                 + '<dt><a href="javascript:;" data-url=' + playListData.link + ' target="_self"><img src=' + playListData.logo + '></a></dt>'
    //                 + '<dd>'
    //                 + '<h4><a href="javascript:;" data-url=' + playListData.link + ' target="_self">' + playListData.game_name + '</a></h4>'
    //                 + '<p><span>' + playListData.play_times + '人玩过</span><i>|</i><span>' + playListData.type_name + '</span></p>'
    //                 + '</dd>'
    //                 + '<dd>'
    //                 + '<a href=' + playListData.link + ' data-url=' + playListData.link + ' target="_self" class="start">开始玩</a>'
    //                 + '</dd>'
    //                 + '</dl>';
    //     }

    //     $(".uph").html(html);
    // },

    // //礼包列表渲染
    // drawGiftList: function () {
    //     if (this._ajaxData.gift_list == undefined)
    //         return;

    //     var originalData = this._ajaxData.gift_list;
    //     var html = '';
    //     for (var i = 0; i < originalData.length; i++) {
    //         var giftListData = originalData[i];

    //         if (giftListData.is_got == 0) {
    //             html += '<dl data-title="' + giftListData.title + '"  data-guide="' + giftListData.guide + '"  data-url="' + giftListData.url + '"><dt><h4>' + giftListData.title + '</h4><p title="' + giftListData.summary + '">礼包内容：' + giftListData.summary + '</p><dd><a href="javascript:;" class="start gift_btn">领取</a></dl>';
    //         } else {
    //             html += '<dl data-title="' + giftListData.title + '"  data-guide="' + giftListData.guide + '"  data-url="' + giftListData.url + '"><dt><h4>' + giftListData.title + '</h4><p title="' + giftListData.summary + '">礼包内容：' + giftListData.summary + '</p><dd><a href="javascript:;" class="start gift_btn">查看</a></dl>';
    //         }
    //     }
    //     $(".user_lb_list").html(html);
    // },
    // //推荐游戏渲染
    // drawRecGameList: function () {
    //     if (this._ajaxData.rec_game_list == undefined)
    //         return;
    //     var originalData = this._ajaxData.rec_game_list;
    //     var html = '';
    //     for (var i = 0; i < originalData.length; i++) {
    //         var recGameListData = originalData[i];
    //         html += '<dl><dt><a href="' + recGameListData.link + '"><img src="' + recGameListData.logo + '"></a><dd><h4><a href="' + recGameListData.link + '">' + recGameListData.title + '</a></h4><p><span>' + recGameListData.player_count + '人玩过</span><i>|</i><span>' + recGameListData.type + '</span></p><dd><a href="' + recGameListData.link + '" class=start>开始玩</a></dl>';
    //     }
    //     $(".ugl").html(html);
    // },
    // //用户信息渲染
    // drawUserInfo: function () {
    //     if (this._ajaxData.player_info == undefined)
    //         return;
    //     var userInfoListData = this._ajaxData.player_info;
    //    $("#ballUserInfoA dt img").attr("src", userInfoListData.logo);
        
    //     $("#uid").append(userInfoListData.id);

    //     if (userInfoListData.third_login == 1) {
    //         //第三方登陆, 隐藏修改密码
    //         $('#chgOldPwdA').unbind('click').parent('li').addClass('state2');
    //     }
    //     //已经绑定手机（或手机注册登陆的）, 则隐藏绑定操作
    //     if (userInfoListData.is_bind_phone) {
    //     $("#ballUserInfoA .user_name").prepend(userInfoListData.bind_phone);
    //         $('#binduserLi').remove();
    //         $('#bindPhone').html('<a href="#" style="color:#d9ad60" ><i class="this_icon bind_phone_icon"></i><span>'+userInfoListData.bind_phone+'</span></a>');
        
    //     }else{
    //           $("#ballUserInfoA .user_name").prepend(userInfoListData.nick_name);
    //         var first_str =   userInfoListData.nick_name.indexOf('_');
            
    //           if(first_str == -1){
              
    //                 $('#binduserLi').remove();
    //           }
    //     }
      
    //     //已经绑定身份证,则隐藏身份证操作
    //     if (userInfoListData.is_bind_idcard) {
    //         $('.shiming').show();
    //         $('#bindRealnameLiNo').hide();
    //         $('#bindRealnameLiOk').show(); 
    //     }

    // },

};

