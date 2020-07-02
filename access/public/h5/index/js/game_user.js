$(function () {
    var gchgOldPwdOper_FLAG = false;
    var glogoutOper_FLAG = false;
    var gbindPhoneOper_FLAG = false;
    var gbindPhoneCodeGet_FLAG = false;
    var gbindRealnameOper_FLAG = false;
    var setAccountAndPwd_FLAG = false;

    var w = $(window).height();
    // console.log(w);
    var a = $(".userInfo").height();
    var b = $(".tab_nav3").height();
    var h = w - a - b - 56;
    $(".wrapper").height(w);
    $(".lt_bar_main").height(h);

    //关闭支付窗口
    $('.public_popup_close').click(function(){
        $('#selectPayPop').fadeOut();
    });

//关闭眼睛

$(".see_icon").on("click",function(){
    var type = $(".login_pwd").attr("type");
    if(type == 'text'){
        $(".login_pwd").attr("type","password");
        $(this).removeClass("see_close_icon");
    }else{
        $(this).addClass("see_close_icon");
        $(".login_pwd").attr("type","text");
    }
});



//评论 :喜欢 吐槽
$('#comment_like ,#comment_shits').click(function(){

        window.indexLogin.commentOpen(window.commentUrl);
        $('#comment').hide();
        return false;
     
});

//评论 :喜欢
$('#comment_refuse').click(function(){
    $('#comment').fadeOut();
    
});

function getQueryVariable(variable)
{
       var query = window.location.search.substring(1);
       var vars = query.split("&");
       for (var i=0;i<vars.length;i++) {
               var pair = vars[i].split("=");
               if(pair[0] == variable){return pair[1];}
       }
       return(false);
}




    //点击退出页面推荐游戏图
    $(document).on("click", "#service_adv_img", function () {
        var url = $(this).data('gameurl');
        window.location.href = url;
    });
        //立即注册
    $("#signBtn").click(function () {
        $("#signPhoneWinpop").show();
        $("#loginWinpop").hide();
    });
    // 手机注册返回登录
    $(".BackLogin").click(function () {
        $("#signPhoneWinpop").hide();
        $("#loginWinpop").show();
    });

    // 用户注册
    $("#UserResgister").click(function () {
        $("#signPhoneWinpop").hide();
        $("#signUserWinpop").show();
    });

    //用户注册返回登录
    $(".BackLoginUser").click(function () {
        $("#signUserWinpop").hide();
        $("#loginWinpop").show();
    });

    //用户注册到手机注册
    $("#signPhoneBtn").click(function () {
        $("#signUserWinpop").hide();
        $("#signPhoneWinpop").show();
    });

     //找回密码
    $("#findPwdBtn").click(function () {
        $("#findPwdWinpop").show();
        $("#loginWinpop").hide();
    });
        // 找回密码后退
    $("#findPwdBack").click(function () {
        $("#findPwdWinpop").hide();
        $("#loginWinpop").show();
    });
 

    // 关闭支付界面
    $("#closepay").click(function () {
        $("#payframe iframe").prop('src',"");
        $("#payframe").hide();
        $(".gray").hide();
        $("#gameframe").show();
        window.parent.sdkModule.UserClosepay();
    });

    $("#weixinpay_close").click(function () {
        window.parent.sdkModule.WechatPay_close();
    });

    //非游戏窗口 (qq)
    $("#closeWindowView").click(function () {
        $("#Netframe iframe").prop('src',"");
        $("#gameframe").show();
        $("#userInfoDiv").show();
        $("#loginWinpop").show();
        $("#Netframe").css("z-index",'1000');
        $("#Netframe").hide();
    });



    // 打开客服弹窗
    $("#gzGzgA").click(function (e) {
        e.stopPropagation();
        $("#gz_winpop,.gray").show();
    });
    // 涮新游戏
    $(".rt_bar_refresh").click(function (e) {
        e.stopPropagation();
        location.reload();
    });
    //关闭客服弹窗
    $(".service_close").click(function (e) {
          e.stopPropagation();
        $("#gz_winpop,.gray").hide();
    });
    

    // 退出登录
    $(".rt_bar_home").click(function () {
         // e.stopPropagation();//阻止同区域其他绑定事件被执行
         window.parent.sdkModule.SDKLogout();
    });
    
    $(".service_close").click(function(e){
     e.stopPropagation();
	$(".service_winpop_new,.gray").hide();
	});


    // 铺满页面
    $('#frameGame').css('height', $(window).height());
    $('#frameGame').css('width', $(document.body).width());

    //倒计时
    var countdown = 60;

    function setTimes(obj,iscut) {
        if (countdown == 60 || iscut=='continue') {
            var html = '<span></span>';
            obj.hide().after(html);
        }
        countdown--;
        obj.next('span').text('重新获取 ' + countdown + ' 秒');
        var t = setTimeout(function () {
            setTimes(obj);
        }, 1000);
        if (countdown <= 0) {
            obj.show().next('span').remove();
            clearTimeout(t);
            countdown = 60;
        }
    }

    // 标签切换
    $('.no_tab').click(function () {
        var index = $('.no_tab').index($(this));
        $('.no_tab').removeClass('this');
        $(this).addClass('this');

        $('.no_con').hide();
        $('.no_con').eq(index).show();
    });


    // 取消操作
    $('#winpop3CancelJump').click(function () {
        $('#winpop3').hide();
    });

    // 2017-07-25 新添加
    // 新弹窗关闭
    $(".winpop_close").click(function () {
        $(this).parent(".user_winpop").hide();
        $(".gray").hide();
    });



    $("#closeGzBtn2").click(function () {
        // if ($("#gzGzgDiv").css('display') != "none") {
        $("#gzGzgDiv").hide();
        $(".pc_thisWinPop_close").hide();
        $(".gray").hide();
        // }
    });

    //关闭(知道了)弹框2
    $("#task_kown_btn").click(function () {
        var url = $(this).attr('data-url');
        if (url != '') {
            location.href = url;
        }
        $('#task_notice').hide();
        $('.gray').hide();
    });


    // 边框隐藏
    $('#rtBarImg').click(function () {
        $("#conDiv").removeClass("fadeInLeft").addClass("fadeOutLeft").fadeOut();
        $(".lt_bar").hide();
        $("#ballConDiv").show();
        SuspendedBall.ShowBall();
    });


    // 用户中心
    $("#ballUserInfoA").click(function () {
        $(".lt_bar").hide();
        $("#ballUserinfoDiv").show();
    });
    // 用户中心后退
    // goBackConA
    $("#goBackConA").click(function () {
        $("#ballUserinfoDiv").hide();
        $("#ballConDiv").show();

    });
    // 修改旧密码返回
    $('#chgOldPwdBackA').click(function () {
        $('#chgOldPwdDiv').hide();
        resetValue();
        $("#ballUserinfoDiv").show();
    });
    // 修改旧密码框显示
    $('#chgOldPwdA').click(function () {
        $.ajax({
            url: '/float.php/mobile/password/pwdconfirm_post',
            data: {
                action:sessionStorage.getItem('gn_h5_token_'+window.parent.sdkModule.appid)
            },
            type: 'POST',
            dataType: 'json',
            cache: false,
            success: function (ret) {
                console.log(ret);
                if(ret.status==0){
                    showNotice("网络错误请重新打开页面");
                    $('#chgOldPwdDiv').hide();
                    resetValue();
                    $("#ballUserinfoDiv").show();
                }else{
                    sessionStorage.setItem('gn_h5_uppwdtoken_'+window.parent.sdkModule.appid,ret.msg);
                }
            }, error: function () {
                showNotice("网络错误请重新打开页面");
                $('#chgOldPwdDiv').hide();
                resetValue();
                $("#ballUserinfoDiv").show();
            }
        });
        $('#ballUserinfoDiv').hide();
        $('#chgOldPwdDiv').show();
        resetValue();
        var chgoldpwd_vm = $.trim($('#chgOldPwd_oldpassword').attr('data-vm'));
        if (chgoldpwd_vm != '') {
            $('#chgOldPwd_oldpassword').val(chgoldpwd_vm).attr('type', 'text');
        }
    });

    // 清空input框
    function resetValue() {
        $('input[type="text"]').val('');
        $('input[type="password"]').val('');
        $('.error').html('');
    }

    // 修改旧密码操作
    $('#chgOldPwdOper').click(function () {

        if (true === gchgOldPwdOper_FLAG) {
            showNotice('操作频繁');
            return false;
        }

        var chgoldpwd_oldpassword_val = $.trim($('#chgOldPwd_oldpassword').val());
        var chgoldpwd_password_val = $.trim($('#chgOldPwd_password').val());
        var chgoldpwd_repassword_val = $.trim($('#chgOldPwd_repassword').val());

        if (chgoldpwd_oldpassword_val === '') {
            showNotice('旧密码未填写');
            return false;
        }
        if (chgoldpwd_password_val === '') {
            showNotice('新密码未填写');
            return false;
        }

        if (chgoldpwd_password_val.length < 6 || chgoldpwd_password_val.length > 16) {
            showNotice('密码最少6位，最长16位');
            return false;
        }
        if (new RegExp(/^[a-zA-Z0-9]{6,16}$/g).test(chgoldpwd_password_val)!=true) {
            showNotice('密码必须为数字或字母');
            return false;
        }
        var vChineseRules = /[\u4E00-\u9FA5]/i;
        if (vChineseRules.test(chgoldpwd_password_val)) {
            showNotice('不支持中文的密码哦 ');
            return false;
        }
        if (chgoldpwd_password_val != chgoldpwd_repassword_val) {
            showNotice('两次输入新密码不一致');
            return false;
        }


        gchgOldPwdOper_FLAG = true;
        $.ajax({
            url: '/float.php/mobile/password/pwduph5_post',
            data: {
                oldpwd: chgoldpwd_oldpassword_val,
                newpwd: chgoldpwd_password_val,
                verifypwd: chgoldpwd_repassword_val,
                action:sessionStorage.getItem('gn_h5_uppwdtoken_'+window.parent.sdkModule.appid)
            },
            type: 'POST',
            dataType: 'json',
            cache: false,
            success: function (obj) {
                // console.log(obj);
                gchgOldPwdOper_FLAG = false;
                if (obj.state == 'success') {
                    showNotice('修改成功,正在返回平台登录...');
                    setTimeout(window.parent.sdkModule.SDKLogout(), 1000);
                } else {
                    showNotice(obj.info);
                    location.reload();
                }
                
            }, error: function () {
                console.log("uppwd请求错误");
                showNotice("服务器错误,请稍后重试");
                gchgOldPwdOper_FLAG = false;
            }
        });
    });


//绑定账号（设置账号名称和密码）
    // $('#setAccountAndPwd').click(function () {
    //     if (true === setAccountAndPwd_FLAG) {
    //         showNotice('操作频繁');
    //         return false;
    //     }

    //     var set_account = $.trim($('#set_account').val());
    //     var set_password = $.trim($('#set_password').val());
    //     var set_repassword = $.trim($('#set_repassword').val());
    //     if (set_account === '') {
    //         showNotice('账号名称必须填写');
    //         return false;
    //     }
    //      if (set_account.length < 4 ) {
    //         showNotice('账号太短');
    //         return false;
    //     }
    //     if (set_password === '') {
    //         showNotice('新密码未填写');
    //         return false;
    //     }
    //     if (set_repassword === '') {
    //         showNotice('新密码未填写');
    //         return false;
    //     }
    //     if (set_password.length < 6 || set_password.length > 15) {
    //         showNotice('密码最少6位，最长15位');
    //         return false;
    //     }
    //     ;

    //     var vChineseRules = /[\u4E00-\u9FA5]/i;
    //     if (vChineseRules.test(set_password)) {
    //         showNotice('不支持中文的密码哦 ');
    //         return false;
    //     }
    //     ;
    //     if (set_password != set_repassword) {
    //         showNotice('两次输入新密码不一致');
    //         return false;
    //     }
    //     setAccountAndPwd_FLAG = true;
    //     $.ajax({
    //         url: '/set-account-pwd/index',
    //         data: {
    //             account: set_account,
    //             password: set_password,
    //             repassword: set_repassword
    //         },
    //         type: 'POST',
    //         dataType: 'json',
    //         cache: false,
    //         success: function (obj) {
    //             console.log(obj);
    //             setAccountAndPwd_FLAG = false;
    //             if (obj.status == 1) {
    //                 showNotice('修改成功,正在返回平台登录...');
    //                 setTimeout("$('#logoutOper').click();", 1000);
    //             } else {
    //                 // location.reload();
    //                 showNotice(obj.msg);
    //             }
    //         }, error: function () {
    //             setAccountAndPwd_FLAG = false;
    //         }
    //     });



    // });

    //领取礼包
    $(".user_all_gift_list").on('click','dl dd a',function () {

       if(!window.parent.sdkModule.appid || !window.parent.sdkModule.LoginUserid){
            console.log("初始化参数缺失");
            return ;
       }
       var obj = $(this);
       var gift_id =  obj.parent().parent().attr("gift-id");
       var appid = window.parent.sdkModule.appid;
       console.log("领取礼包"+appid+"-"+gift_id);
       $.ajax({
            url:"/float.php/mobile/gift/ajaxgifth5",
            type:'post',
            data:{'giftid':gift_id,'app_id':appid},
            async: false,
            success:function(ret){
                console.log(ret);
                if(ret.status=='1'){//领取成功
                    showNotice(ret.info);
                    obj.parent().parent().remove();
                    SuspendedBallContent.init();
                }else if(ret.status=='2'){//已领取
                    showNotice(ret.info);
                    obj.parent().parent().remove();
                    SuspendedBallContent.init();
                }else{
                    showNotice("请求异常");
                }
            },
            error:function(){
                showNotice("网络连接错误");
            }
       });
    });
    
    $(".user_my_gift_list").on('click','dl dd a',function (event) {
        var clipboard = new ClipboardJS(event.target);
        clipboard.on('success',function(e){
            console.log("复制成功");
            showNotice("复制成功");
        });
        clipboard.on('error',function(e){
            console.log("复制失败");
            showNotice("复制失败");
        });
    });

    $("#account").on('click','',function (event) {
        event.stopPropagation();
        var clipboard = new ClipboardJS(event.target);
        clipboard.on('success',function(e){
            console.log("复制账号成功");
            showNotice("复制账号成功");
        });
        clipboard.on('error',function(e){
            console.log("复制账号失败");
            showNotice("复制账号失败");
        });

    });

    $("#uid").on('click','span',function (event) {
        event.stopPropagation();
        var clipboard = new ClipboardJS(event.target);
        clipboard.on('success',function(e){
            console.log("复制UID成功");
            showNotice("复制UID成功");
        });
        clipboard.on('error',function(e){
            console.log("复制UID失败");
            showNotice("复制UID失败");
        });

    });
    //选择账号登录
    $("#loadAccountSelect").on('click','a',function (event) {
        var obj = $(this);
        var account = obj.html();
        var pwd = $("#login_password").val();
        account.length && pwd.length ? window.parent.sdkModule.sdkLogin(account,pwd) : showNotice("用户名或密码不能为空");
        $("#loadaccountPayDiv").hide();
        $(".gray").hide();
    });



    // //退出登录
    // $('#logoutOper').click(function () {
    //     $.ajax({
    //         url: '/login/out',
    //         async: false,
    //     });
    //     window.location.reload();
    // });

    // 绑定账号打开
    // $('#bindEmail').click(function () {
    //     $('#ballUserinfoDiv').hide();
    //     $('#bindUserDiv').show();
    //     resetValue();
    // });

    //绑定账号返回
    // $('#bindUserBackA').click(function () {
    //     $('#bindUserDiv').hide();
    //     resetValue();
    //     $('#ballConDiv').show();
    // });


    // 绑定手机框A打开
    $('#bindPhoneA').click(function () {
        $('#ballUserinfoDiv').hide();
        $('#bindPhoneDiv').show();
        resetValue();
    });
     // 绑定手机框B打开
    $('#bindPhoneB').click(function () {
        $('#ballUserinfoDiv').hide();
        $('#bindPhoneDiv').show();
        resetValue();
    });
    function cPhone(str) {
        var rules = /^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\d{8}/i;
        return rules.test(str);
    }

    // 绑定手机操作
    $('#bindPhoneOper').click(function () {

        if (true === gbindPhoneOper_FLAG) {
            return false;
        }

        var phone = $.trim($('#bindPhone_phone').val());
        var code = $.trim($('#bindPhone_code').val());
        var phone_old = $.trim($('#bindPhone_phone_old').val());
        var is_bind_phone = $("#bindPhone_phone_old").attr('is_bind_phone');
        if (phone === '') {
            showNotice('手机号未填写');
            return false;
        }

        if (!cPhone(phone) || phone.length != 11) {
            showNotice('请输入正确手机号');
            return false;
        }

        if(is_bind_phone==1){
             if (!cPhone(phone_old) || phone_old.length != 11) {
            showNotice('请输入正确的原手机号');
            return false;
            }
        }

        if (code === '') {
            showNotice('手机验证码未填');
            return false;
        }

        window.parent.sdkModule.update_bindmobile(phone,phone_old,code);

    });

    // 绑定手机返回
    $('#bindPhoneBackA').click(function () {
        $('#bindPhoneDiv').hide();
        resetValue();
        $('#ballConDiv').show();
    });
    //切换账号
    $("#changeUser").click(function () {
        $(".gray").css('z-index',"1000");
        $("#userInfoDiv").css('z-index',"1001");
        $("#userInfoDiv,.gray").show();
        $("#CloseUserIcon").addClass("close");
    });

    //关闭切换账号
    $("#CloseUserIcon").click(function () {
        $(".gray").css('z-index',"");
        $("#userInfoDiv").css('z-index',"");
        $("#userInfoDiv,.gray").hide();
        $("#CloseUserIcon").removeClass("close");
    });



    //登录框上的关闭
    $(".close").click(function () {
        $("#userInfoDiv,.gray").hide();
    });




    // 绑定手机获取验证码
    $('#bindPhoneCodeGet').click(function () {
        if (true === gbindPhoneCodeGet_FLAG) {
            showNotice('操作频繁');
            return false;
        }

        var phone = $.trim($('#bindPhone_phone').val());

        var old_phone = $.trim($('#bindPhone_phone_old').val());

        var is_bind_phone = $('#bindPhone_phone_old').attr('is_bind_phone');

        if (phone === '') {
            showNotice('手机号码未填写');
            return false;
        }
        if (!cPhone(phone) || phone.length != 11 ) {
            showNotice('手机号格式不正确');
            return false;
        }
        if(is_bind_phone==1){
            if (!cPhone(old_phone) || old_phone.length != 11 ) {
            showNotice('原手机号格式不正确');
            return false;
            }
        }
        var _this = $(this);
        gbindPhoneCodeGet_FLAG = true;

        // send_sms_ph(phone,old_phone);//使用绑定手机的验证码接口
        // console.log("发送短信到"+phone);
        var now = new Date();
        var tmp =Date.parse(now).toString().substr(0,10);
        var bindph_session_sms = phone+"_"+now.getFullYear()+now.getMonth()+now.getDate();
        var bindph_session_sms_val = sessionStorage.getItem(bindph_session_sms);
        console.log('bindmobile:'+bindph_session_sms+'|'+bindph_session_sms_val);
        if(bindph_session_sms_val!=null){
            if((tmp - bindph_session_sms_val)>=60){
                countdown = 60;
                sessionStorage.setItem(bindph_session_sms,tmp);
                window.parent.sdkModule.send_sms_ph(phone);//使用绑定手机的验证码接口
                console.log("发送短信到"+phone);
                setTimes(_this);
            }else{
                countdown = tmp - bindph_session_sms_val;
                console.log("短信未过期");
                showNotice("短信未过期");
                setTimes(_this,'continue');
            }
        }else{
            countdown = 60;
            sessionStorage.setItem(bindph_session_sms,tmp);
            send_sms_ph(phone);//使用绑定手机的验证码接口
            console.log("发送短信到"+phone);
            setTimes(_this);
        }
        
        
    });


    // 实名认证框打开
    $('#bindRealnameA').click(function () {
        $('#ballUserinfoDiv').hide();
        $('#bindRealnameDiv').show();
        resetValue();
    });

    // 实名认证操作
    $('#bindRealnameOper').click(function () {
        if (true === gbindRealnameOper_FLAG) {
            return false;
        }

        var realname = $.trim($('#bind_realname').val());
        var idcard = $.trim($('#bind_idcard').val());


        // 表单错误数
        var err_num = 0;
        if (false === check_realname()) {
            err_num++;
        } else if (false === check_idcard()) {
            err_num++;
        }
        if (err_num > 0) {
            return false;
        }

//      gbindRealnameOper_FLAG = true;
        $.ajax({
            url: '/bind-idcard/bind-idcard',
            data: {
                realname: realname,
                idcard: idcard
            },
            type: 'post',
            dataType: 'json',
            cache: false,
            success: function (obj) {
                gbindRealnameOper_FLAG = false;
                if (obj.status == 0) {
                    showNotice(obj.msg);
                }
                if (obj.status == 1) {
//                    taskNotice(obj.msg);
//                    $('.gray').show();
                    // 已实名认证
                      resetValue();
                     $('#bindRealnameLiNo').hide();
                    $('#bindRealnameLiOk').show();
                    $('#bindRealnameDiv').hide();
                    $('#ballUserinfoDiv').show();
                    $('.shiming').show();
                    showNotice('认证成功');
//                    $('#bindRealnameBackA').click();
                }
            }
        });
    });

    // 实名认证返回
    $('#bindRealnameBackA').click(function () {
        $('#bindRealnameDiv').hide();
        resetValue();
        $('#ballUserinfoDiv').show();
    });

    $('.tips_pay_close').click(function(){
        $('#pay-tip').hide();
    });

    function getParamValue(key) {
        var regex = new RegExp(key + "=([^&]*)", "i");
        var mat = location.href.match(regex);
        return mat == null ? '' : mat[1];
    }

    //退出登陆 
    $(document).on("click", "#logout_btn", function () {
       $.ajax({
            url: '/login/out',
            async: false,
        });
        window.location.reload();
    });


    // 弹出提示框
    function showNotice(msg) {
        $('#noticeDiv').html(msg);
        $('#noticeDiv').show();

        $('#noticeDiv').delay(2000).fadeOut();
    }


    function taskNotice(msg, score, url) {
        $('#task_notice .user_winpop_record').html(msg);
        $("#task_notice").show();
        if (url != '') {
            $("#task_kown_btn").attr('data-url', url);
        }

        if (score) {
            var new_score = parseInt($("#my_score").attr('score')) + parseInt(score);
            $("#my_score").attr('score', new_score);
            var score_str = new_score.toString();
            var re = /(?=(?!(\b))(\d{3})+$)/g;
            $("#my_score").text(score_str.replace(re, ","));
        }
    }


// 姓名检测
    function check_realname() {
        // 姓名值
        var realname_val = $.trim($("#bind_realname").val());
        if (realname_val == "") {
            showNotice('请输入真实姓名');
            return false;
        }
        return true;
    }

// 身份证检测
    function check_idcard() {
        var idcard_val = $.trim($("#bind_idcard").val());
        var res = checkCard(idcard_val);
        if (idcard_val == "" || !res) {
            showNotice('请使用真实身份信息认证');
            return false;
        }
        return true;
    }

    var vcity = {
        11: "北京", 12: "天津", 13: "河北", 14: "山西", 15: "内蒙古",
        21: "辽宁", 22: "吉林", 23: "黑龙江", 31: "上海", 32: "江苏",
        33: "浙江", 34: "安徽", 35: "福建", 36: "江西", 37: "山东", 41: "河南",
        42: "湖北", 43: "湖南", 44: "广东", 45: "广西", 46: "海南", 50: "重庆",
        51: "四川", 52: "贵州", 53: "云南", 54: "西藏", 61: "陕西", 62: "甘肃",
        63: "青海", 64: "宁夏", 65: "新疆", 71: "台湾", 81: "香港", 82: "澳门", 91: "国外"
    };

    checkCard = function (card) {
        //是否为空
        if (card === '') {
//                        	    alert('请输入身份证号，身份证号不能为空');
            return false;
        }
        //校验长度，类型
        if (isCardNo(card) === false) {
//                        	    alert('您输入的身份证号码不正确，请重新输入');
            return false;
        }
        //检查省份
        if (checkProvince(card) === false) {
//                        	    alert('您输入的身份证号码不正确,请重新输入');
            return false;
        }
        //校验生日
        if (checkBirthday(card) === false) {
//                        	    alert('您输入的身份证号码生日不正确,请重新输入');
            return false;
        }
        //检验位的检测
        if (checkParity(card) === false) {
//                        	    alert('您的身份证校验位不正确,请重新输入');
            return false;
        }
        return true;
    };


//检查号码是否符合规范，包括长度，类型
    isCardNo = function (card) {
        //身份证号码为15位或者18位，15位时全为数字，18位前17位为数字，最后一位是校验位，可能为数字或字符X
        var reg = /(^\d{15}$)|(^\d{17}(\d|X)$)/;
        if (reg.test(card) === false) {
            return false;
        }

        return true;
    };

//取身份证前两位,校验省份
    checkProvince = function (card) {
        var province = card.substr(0, 2);
        if (vcity[province] == undefined) {
            return false;
        }
        return true;
    };

//检查生日是否正确
    checkBirthday = function (card) {
        var len = card.length;
        //身份证15位时，次序为省（3位）市（3位）年（2位）月（2位）日（2位）校验位（3位），皆为数字
        if (len == '15') {
            var re_fifteen = /^(\d{6})(\d{2})(\d{2})(\d{2})(\d{3})$/;
            var arr_data = card.match(re_fifteen);
            var year = arr_data[2];
            var month = arr_data[3];
            var day = arr_data[4];
            var birthday = new Date('19' + year + '/' + month + '/' + day);
            return verifyBirthday('19' + year, month, day, birthday);
        }
        //身份证18位时，次序为省（3位）市（3位）年（4位）月（2位）日（2位）校验位（4位），校验位末尾可能为X
        if (len == '18') {
            var re_eighteen = /^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X)$/;
            var arr_data = card.match(re_eighteen);
            var year = arr_data[2];
            var month = arr_data[3];
            var day = arr_data[4];
            var birthday = new Date(year + '/' + month + '/' + day);
            return verifyBirthday(year, month, day, birthday);
        }
        return false;
    };

//校验日期
    verifyBirthday = function (year, month, day, birthday) {
        var now = new Date();
        var now_year = now.getFullYear();
        //年月日是否合理
        if (birthday.getFullYear() == year && (birthday.getMonth() + 1) == month && birthday.getDate() == day) {
            //判断年份的范围（3岁到100岁之间)
            var time = now_year - year;
            if (time >= 17 && time <= 100) {
                return true;
            }
            return false;
        }
        return false;
    };

//校验位的检测
    checkParity = function (card) {
        //15位转18位
        card = changeFivteenToEighteen(card);
        var len = card.length;
        if (len == '18') {
            var arrInt = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
            var arrCh = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
            var cardTemp = 0, i, valnum;
            for (i = 0; i < 17; i++) {
                cardTemp += card.substr(i, 1) * arrInt[i];
            }
            valnum = arrCh[cardTemp % 11];
            if (valnum == card.substr(17, 1)) {
                return true;
            }
            return false;
        }
        return false;
    };

//15位转18位身份证号
    changeFivteenToEighteen = function (card) {
        if (card.length == '15') {
            var arrInt = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
            var arrCh = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
            var cardTemp = 0, i;
            card = card.substr(0, 6) + '19' + card.substr(6, card.length - 6);
            for (i = 0; i < 17; i++) {
                cardTemp += card.substr(i, 1) * arrInt[i];
            }
            card += arrCh[cardTemp % 11];
            return card;
        }
        return card;
    };

});
