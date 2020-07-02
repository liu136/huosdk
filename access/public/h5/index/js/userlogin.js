(function(){
	InitLogin = function(){
		function InitLogin(){
			var _this = this ;
			//登录
			$("#mlp_login").on("click",
            function() {
                _this.login()
            }),
            //获取手机注册验证码
            $("#rp_getcode").on("click",
            function() {
                _this.regSendSMS()
            }),
            //提交手机注册
            $("#rp_reg_phone").on("click",
            function() {
                _this.reg()
            }),
            //获取找回密码验证码
            $("#fwp_getcode").on ("click",
            function() {
                _this.finePwdSendSMS()
            }),
            //提交找回密码
            $("#fwp_ok").on("click",
            function() {
                _this.findPwd()
            }),
            //用户注册
            $("#rp_reg_ok").on("click",
            function() {
                _this.retuser()
            }),
            $("#qqLoginBtn").on("click",
            function() {
                $("#WindowViewCloseMenu").show();
                _this.qqLogin()
            })

		}
		return InitLogin.showNotice = function (msg){
			$("#noticeDiv").html(msg),
            $("#noticeDiv").show(),
            $("#noticeDiv").delay(2e3).fadeOut()
		},
		InitLogin.prototype.login = function() {
            var username = $("#login_username").val(),
            pwd = $("#login_password").val();
            username.length && pwd.length ? window.parent.sdkModule.sdkLogin(username,pwd) : InitLogin.showNotice("用户名或密码不能为空");

        },
        InitLogin.prototype.reg = function() {
            var username = $("#reg_username").val(),
            pwd = $("#reg_password").val(),
            code = $("#reg_code").val(); 
            (username = $.trim(username), pwd = $.trim(pwd), code = $.trim(code), username.length && pwd.length && code.length) ? new RegExp(/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\d{8}/i).test(username) ? pwd.length < 6 ? InitLogin.showNotice("密码不能少于6个字") :
            window.parent.sdkModule.mobile_register(username,pwd,code) : InitLogin.showNotice("手机号格式不正确") : InitLogin.showNotice("验证信息不能为空")
        },
        InitLogin.prototype.retuser = function() {
            var username = $("#reg_username_account").val(),
            pwd = $("#reg_password_account").val();
            username.length > 6 && RegExp(/^[a-z|A-Z|0-9]{6,16}$/g).test(username) ?  pwd.length > 5 &&  pwd.length < 17 ?  window.parent.sdkModule.register(username,pwd) : InitLogin.showNotice("密码必须6-16位,") : InitLogin.showNotice("账号为小写字母和1-9")
            
            
        },
        InitLogin.prototype.findPwd = function() {
            var username = $("#fwp_phone").val(),
            pwd = $("#fwp_pwd").val(),
            code = $("#fwp_code").val(); 
            (username = $.trim(username), pwd = $.trim(pwd), code = $.trim(code), username.length && pwd.length && code.length) ? new RegExp(/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\d{8}/i).test(username) ? pwd.length < 6 ? InitLogin.showNotice("密码不能少于6个字") : 
            window.parent.sdkModule.update_password(username,pwd,code) : InitLogin.showNotice("手机号格式不正确") : InitLogin.showNotice("手机号、密码、验证码不能为空")
        },
        InitLogin.prototype.regSendSMS = function() {
        	//手机注册验证码
            var _this = this;
            console.log("send-sms");
            var phone = $.trim($("#reg_username").val());
            if (console.log(phone), "" === phone) return InitLogin.showNotice("手机号码未填写"),
            !1;
            if (!/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\d{8}/i.test(phone) || 11 != phone.length) return InitLogin.showNotice("手机号格式不正确"),
            !1;
        	
        	var now = new Date();
            var tmp =Date.parse(now).toString().substr(0,10);
        	_this.session_sms = phone+"_"+now.getFullYear()+now.getMonth()+now.getDate();
        	console.log(_this.session_sms);
            var sel = "#rp_getcode";
            var session_sms_val = sessionStorage.getItem(_this.session_sms);
            console.log(session_sms_val);
            if(session_sms_val!=null){
            	 if((tmp - session_sms_val)>=60){
                    _this._sms_count = 60;
                    sessionStorage.setItem(_this.session_sms,tmp);
                    window.parent.sdkModule.send_sms(phone);
                    InitLogin.showNotice("发送成功");
                }else{
                    _this._sms_count = tmp - session_sms_val;
                    InitLogin.showNotice("验证码未过期");
                    $(sel).removeAttr("onclick");
                    $(sel).text(this._sms_count + "秒后重发");
                }
            }else{
            	_this._sms_count = 60;
            	sessionStorage.setItem(_this.session_sms,tmp);
                window.parent.sdkModule.send_sms(phone);
                InitLogin.showNotice("发送成功");
            	// console.log(sessionStorage.getItem(_this.session_sms))
            }
            
            $(sel).addClass("cur"),
            _this.countSms(sel)
        },

        InitLogin.prototype.finePwdSendSMS = function() {
        	//找回密码
            var _this = this;
            console.log("send-sms");
            var phone = $.trim($("#fwp_phone").val());
            if (console.log(phone), "" === phone) return InitLogin.showNotice("手机号码未填写"),
            !1;
            if (!/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\d{8}/i.test(phone) || 11 != phone.length) return InitLogin.showNotice("手机号格式不正确"),
            !1;
        	
        	var now = new Date();
            var tmp =Date.parse(now).toString().substr(0,10);
        	_this.session_sms = phone+"_"+now.getFullYear()+now.getMonth()+now.getDate();
        	console.log(_this.session_sms);
            var sel = "#fwp_getcode";
            var session_sms_val = sessionStorage.getItem(_this.session_sms);
            console.log(session_sms_val);
            if(session_sms_val!=null){
                if((tmp - session_sms_val)>=60){
                    _this._sms_count = 60,
                    sessionStorage.setItem(_this.session_sms,tmp);
                    window.parent.sdkModule.send_sms_up(phone);//使用找回密码的验证码接口
                    InitLogin.showNotice("发送成功");
                }else{
                    _this._sms_count = tmp - session_sms_val,
                    InitLogin.showNotice("验证码未过期"),
                    $(sel).removeAttr("onclick"),
                    $(sel).text(this._sms_count + "秒后重发")
                }
            }else{
            	_this._sms_count = 60,
            	sessionStorage.setItem(_this.session_sms,tmp);
                window.parent.sdkModule.send_sms_up(phone);//使用找回密码的验证码接口
                InitLogin.showNotice("发送成功");
            	// console.log(sessionStorage.getItem(_this.session_sms))
            }
            
            $(sel).addClass("cur"),
            _this.countSms(sel)
        },
        InitLogin.prototype.countSms = function(sel) {
            var _this = this;
            if (this._sms_count--, this._sms_count <= 0) return $(sel).removeClass("cur"),sessionStorage.removeItem(_this.session_sms),
            void $(sel).text("获取验证码");
        	// console.log(sessionStorage.getItem(_this.session_sms));
            $(sel).removeAttr("onclick"),
            $(sel).text(this._sms_count + "秒后重发"),
            setTimeout(function() {
                _this.countSms(sel)
            },
            1e3)
        },
         InitLogin.prototype.qqLogin = function() {
            //1.param json对象{app_id:"",client_id:"",deviceType:"qq"}client_id可不用
            //2.转base64传
            //3.将qq登录页传入游戏界面
            var param = {'app_id':window.parent.sdkModule.appid,"client_id":"","deviceType":"qq","usertoken":sessionStorage.getItem('gn_h5_token_'+window.parent.sdkModule.appid)};
            var param = window.btoa(JSON.stringify(param));
            $("#Netframe iframe").prop("src","/public/qqlogin/index.php?param=" + param);
            $("#gameframe").hide();
            $("#userInfoDiv").hide();
            $("#loginWinpop").hide();
            if(window.parent.sdkModule.Isloaded){
                $(".gray").css("z-index",'999');
            }
            $("#Netframe").css("z-index",'1000');
            $("#Netframe").show();
        },
		InitLogin
	}();
	window.InitLogin = new InitLogin;

})();