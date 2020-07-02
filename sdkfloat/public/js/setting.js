;(function($){
    var $loginPass = $('#old-password');
    var $newPass = $('#new-password');
    var $okPass = $('#ok-password');

    function changePass(url,data){
        $.post(url,data, function(response) {
            if (response.result && response.result == 'success') {
                swal({
                    title: '温馨提示',
                    text: '恭喜您，密码修改成功！',
                    type: 'success'
                }, function() {
                    window.location.href = '/account'
                });
            }
            if (response.error) {
                swal({
                    title: '温馨提示',
                    text: response.error.message,
                    type: 'error'
                });
                return false;
            }
        },'json');
    }

    //修改支付密码 登录密码 实名认证 表单校验通用处理函数
    $(window).on('input propertychange',function(ev){
        var flag = false,
            inpVal = $(ev.target).val();
        $_submit = $('.login-button');
        $(ev.target).val() ? flag = true : flag = false;

        //修改登陆密码校验处理
        if ($loginPass.val() && $newPass.val() && $okPass.val() ){
            var flag2 = '',
                intFlag = '',
                $targetVal = $(ev.target).val();
            ($targetVal.length >= 6 && $targetVal.length <= 16) ? flag2 =true : flag2 = false;
            ($loginPass.val().length >= 6 && $newPass.val().length >=6 && $okPass.val().length >= 6) ? intFlag = true : intFlag = false;
            (flag2 && intFlag) ? $_submit.removeClass('default').addClass('btn-submit') : $_submit.addClass('default').removeClass('btn-submit');


        } else if ( $('.input-small').val() && $('#set-new-pay').val() && $('#new-pay-pass').val() ) {
            var flag1 = '',
                intFlag1 = '',
                $targetVal = $(ev.target).val();

            ($targetVal.length >= 6 && $targetVal.length <= 32) ? flag1 = true : flag1 = false;
            ($('.set-new-pay').val().length >=6 && $('.new-pay-pass').val().length >= 6) ? intFlag1 = true : intFlag1 = false;

            (flag1 && intFlag1) ? $_submit.removeClass('default').addClass('btn-submit') : $_submit.addClass('default').removeClass('btn-submit');

        } else if ( $('.rel-name').val() && $('.card-id').val() ) {
            if ( $(ev.target).val() ) {
                $_submit.removeClass('default').addClass('btn-submit');
            }
        } else {
            $_submit.addClass('default').removeClass('btn-submit');
        }

        //实名认证input验证
        if ( $(ev.target).is('.rel-name') || $(ev.target).is('.card-id') ) {
            if ($(ev.target).is('.rel-name')) {
                var reg = /^[\u4e00-\u9fa5]+$/i;
                if ( !reg.test(inpVal) ){
                    $('.error').remove();
                    $(ev.target).parent().append('<span class="error">请输入中文名字</span>');
                    return false;
                } else {
                    $('.error').remove();
                }
            }
            if ($(ev.target).is('.card-id')) {
                if ( inpVal.length > 18 ) {
                    $('.error').remove();
                    $(ev.target).parent().append('<span class="error">身份证号15-18位</span>');
                    return false;
                } else {
                    $('.error').remove();
                }
            }

        } else if ($(ev.target).is('.set-new-pay') || $(ev.target).is('.new-pay-pass') ) {
             if ( inpVal.length > 32 || inpVal.length < 6) {
                    $('.error').remove();
                    $(ev.target).parent().append('<span class="error">密码为6-32位</span>');
                    return false;
                } else {
                    $('.error').remove();
                }
        } else {
            if ( (inpVal.length < 6 || inpVal.length > 16) && flag) {
                if (!$(ev.target).is('.phone')) {
                    $('.error').remove();
                    $(ev.target).parent().append('<span class="error">密码为6-16位</span>');
                    return false;
                }
            } else {
                $('.error').remove();
            }
        }
    });

    //修改登陆密码提交
    $('#set-password').on('click','.btn-submit',function(){
        if ( $newPass.val() != $okPass.val() ) {
            $('.error').remove();
            $okPass.parent().append('<span class="error">设置密码与确认密码不符</span>');
            return false;
        }
        var url = '/account/setting/set-login-pwds';
        var data = {
            '_csrf': $('input[name=_csrf]').val(),
            'old-password': $.trim($loginPass.val()),
            'new-password': $.trim($newPass.val())
        };

        changePass(url,data);
    });

    //获取验证码操作
    $('.form-inline').on('click','.sms-button',function(){
        var data = {
            '_csrf': $('input[name=_csrf]').val()
        };
        if ( countDownWait !== 60 ) {
            return false;
        }
        countDown($(this));
        $.post('/account/setting/set-pay-pwd/sms-captcha',data, function(response) {
            if (response.error) {
                swal({
                    title: '温馨提示',
                    text: response.error.message,
                    type: 'error'
                });
            }
        },'json');
    });

    //修改支付密码操作
    $('#login-form').on('click','.btn-submit',function(){
        var $newPay = $('#set-new-pay');
        var $okNewPay = $('#new-pay-pass');

        if ( $newPay.val() != $okNewPay.val() ) {
            $('.error').remove();
            $okNewPay.parent().append('<span class="error">设置密码与确认密码不符</span>');
            return false;
        }
        var data = {
            '_csrf': $('input[name=_csrf]').val(),
            'sms-captcha':$('.input-small').val(),
            'new-pay-password':$('#new-pay-pass').val()
        };

        $.post('/account/setting/set-pay-pwd', data, function(response) {
            if (response.error) {
                swal({
                    title: '温馨提示',
                    text: response.error.message,
                    type: 'error'
                });
                return false;
            }

            if (response.result) {
                swal({
                    title: '温馨提示',
                    text: '恭喜您，密码修改成功！',
                    type: 'success'
                }, function() {
                    window.location.href = '/account';
                });
            }
        });
    });

    //倒计时
    var countDownWait = 60;
    var countDown = function($obj) {
        if (countDownWait == 0) {
            $obj.css({
                'color': '#fff',
                'background-color': '#e95524',
                'cursor': 'pointer'
            }).html('点击获取');
            countDownWait = 60;
        } else {
            $obj.css({
                'color': '#fff',
                'background-color': '##e95524',
                'cursor': 'not-allowed'
            }).html(countDownWait + "秒后获取");
            countDownWait--;
            setTimeout(function() {
                countDown($obj);
            }, 1000);
        }
    };

    //实名认证页面处理
    $('#set-real-name').on('click','.btn-submit',function(e){
        if($(e.target).hasClass('default')) return false;
        var data = {
            '_csrf': $('input[name=_csrf]').val(),
            'real-name': $.trim($('input[name=real-name]').val()),
            'card-id': $.trim($('input[name=card-id]').val())
        };
        $.post('/account/setting/set-real-name',data , function(response) {
            if (response.error) {
                swal({
                    title: '温馨提示',
                    text: response.error.message,
                    type: 'error'
                }, function(){
                    if(response.error.code == 1009) {
                        window.location.href = response.redirect;
                    } else{
                        return ;
                    }
                });
            } else if ( response.real_status == 'no' ) {
                var realCount = response.real_count;
                var verifyFail = function () {//认证失败，统计失败次数的处理方式
                    $('.fail-content').remove();$('.connect-num').remove();
                    if( realCount == 2 ) {
                        $('#btn-real-name').removeClass('default').addClass('default');
                        $('.card-id').attr('disabled', 'disabled');
                        $('.rel-name').attr('disabled', 'disabled');
                        var failStr = "<p class='text-center ui-fs-12 fail-content'>若您无法通过该方式实名认证，请联系客服电话</p>"+
                            "<p class='text-center telephone connect-num'><a href='tel:400-090-1268'>400-090-1268</a></p>";
                        $('.wrapper .container-fluid').append(failStr);
                    } else {
                        return ;
                    }
                };
                swal({
                    title: '温馨提示',
                    text: response.idcard_message,
                    type: 'error'
                },verifyFail);
            } else if (response.result.idcard_verify_status == '2') {
                var message = "您的实名认证申请已经成功提交";
                if (location.href.indexOf('18c2fbacfea04ad0') > 0) {
                    message = "认证完毕，安全升级。";
                }
                swal({
                    title: '温馨提示',
                    text: message,
                    type: 'success'
                }, function() {
                    window.location.href = response.redirect;
                });
            }
        });
    });
})(jQuery);
