!function(modules) {
    var installedModules = {};
    function __webpack_require__(moduleId) {
        if (installedModules[moduleId]) return installedModules[moduleId].exports;
        var module = installedModules[moduleId] = {
            i: moduleId,
            l: !1,
            exports: {}
        };
        return modules[moduleId].call(module.exports, module, module.exports, __webpack_require__),
        module.l = !0,
        module.exports
    }
    __webpack_require__.m = modules,
    __webpack_require__.c = installedModules,
    __webpack_require__.d = function(exports, name, getter) {
        __webpack_require__.o(exports, name) || Object.defineProperty(exports, name, {
            configurable: !1,
            enumerable: !0,
            get: getter
        })
    },
    __webpack_require__.r = function(exports) {
        Object.defineProperty(exports, "__esModule", {
            value: !0
        })
    },
    __webpack_require__.n = function(module) {
        var getter = module && module.__esModule ?
        function() {
            return module.
        default
        }:
        function() {
            return module
        };
        return __webpack_require__.d(getter, "a", getter),
        getter
    },
    __webpack_require__.o = function(object, property) {
        return Object.prototype.hasOwnProperty.call(object, property)
    },
    __webpack_require__.p = "",
    __webpack_require__(__webpack_require__.s = 2)
} ([function(module, exports) {
    module.exports = window.$
},
function(module, exports, __webpack_require__) {
    "use strict";
    Object.defineProperty(exports, "__esModule", {
        value: !0
    });
    var $ = __webpack_require__(0),
    IndexLogin = function() {
        function IndexLogin() {
            var _this = this;
            this.ch_id = IndexLogin.getParamValue("ch_id"),
            this.plat = IndexLogin.getParamValue("plat"),
            $("#guestLoginBtn").on("click",
            function() {
                _this.guestLogin()
            }),
            $("#aip_bindmobile").on("click", IndexLogin.popBindMobile),
            $("#bindPhoneBack").on("click", IndexLogin.backBindMobile),
            $("#bmp_getcode").on("click",
            function() {
                _this.bindMobileSms()
            }),
            $("#bmp_ok").on("click", IndexLogin.bindMobile),
            $("#wechat_login_btn").on("click", IndexLogin.wechat_login),
            $("#lp_qq_login").on("click", IndexLogin.qqLogin),
            $("#lp_weibo_login").on("click", IndexLogin.weiboLogin),
            $("#lp_mobileLoginBtn").on("click", IndexLogin.popAccountLogin),
            $("#mlp_reg").on("click", IndexLogin.popReg),
            $("#mlp_back").on("click", IndexLogin.popLogin),
            $("#mlp_login").on("click",
            function() {
                _this.login()
            }),
            $("#rp_back").on("click", IndexLogin.popAccountLogin),
            $("#rp_getcode").on("click",
            function() {
                _this.regSendSMS()
            }),
            $("#rp_reg_ok").on("click",
            function() {
                _this.reg()
            }),
            $("#mlp_findpwd").on("click", IndexLogin.popFindPwd),
            $("#fwp_reg").on("click", IndexLogin.popReg),
            $("#fwp_backlogin").on("click", IndexLogin.popLogin),
            $("#fwp_getcode").on("click",
            function() {
                _this.findPwdSms()
            }),
            $("#fwp_ok").on("click", IndexLogin.findPwd),
            IndexLogin.popLogin()
        }
        return IndexLogin.showNotice = function(msg) {
            $("#noticeDiv").html(msg),
            $("#noticeDiv").show(),
            $("#noticeDiv").delay(2e3).fadeOut()
        },
        IndexLogin.getParamValue = function(key) {
            var url = location.href,
            regex = new RegExp("[&?]" + key + "=([^&#]*)", "i"),
            ret = url.match(regex);
            return ret && ret.length > 1 ? ret[1] : ""
        },
        IndexLogin.popLogin = function() {
            for (var _i = 0,
            _a = IndexLogin.pop_list; _i < _a.length; _i++) {
                var id = _a[_i];
                $(id).hide()
            }
            $("#loginPop").show()
        },
        IndexLogin.popAccountInfo = function(account, password) {
            for (var _i = 0,
            _a = IndexLogin.pop_list; _i < _a.length; _i++) {
                var id = _a[_i];
                $(id).hide()
            }
            $("#accountInfoPop").show(),
            $("#aip_username").val(account),
            $("#aip_password").val(password)
        },
        IndexLogin.prototype.guestLogin = function() {
            $.ajax({
                url: "/auto-login/ajax",
                data: {
                    ch_id: this.ch_id,
                    plat: this.plat
                },
                dataType: "json",
                success: function(response) {
                    1 != response.status ? (IndexLogin.showNotice(response.msg), setTimeout(function() {
                        location.reload()
                    },
                    1e3)) : IndexLogin.popAccountInfo(response.username, response.password)
                },
                error: function() {}
            })
        },
        IndexLogin.popBindMobile = function() {
            for (var _i = 0,
            _a = IndexLogin.pop_list; _i < _a.length; _i++) {
                var id = _a[_i];
                $(id).hide()
            }
            $("#loginWinpop").hide(),
            $("#bindMobilePop").show()
        },
        IndexLogin.backBindMobile = function() {
            $("#bindMobilePop").hide(),
            $("#accountInfoPop").show()
        },
        IndexLogin.prototype.bindMobileSms = function() {
            var _this = this,
            phone = $("#bind_phone_num_tx").val();
            if (phone = $.trim(phone), console.log(phone), "" === phone) return IndexLogin.showNotice("手机号码未填写"),
            !1;
            if (!/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\d{8}/i.test(phone) || 11 != phone.length) return IndexLogin.showNotice("手机号格式不正确"),
            !1;
            $.ajax({
                url: "/bind-phone/send-sms",
                dataType: "json",
                method: "post",
                async: !1,
                data: {
                    phone_num: phone
                },
                success: function(response) {
                    if (1 == response.status) {
                        IndexLogin.showNotice("发送成功"),
                        _this._sms_count = 60;
                        var sel = "#bmp_getcode";
                        $(sel).addClass("cur"),
                        _this.countSms(sel)
                    } else IndexLogin.showNotice(response.msg)
                },
                error: function() {
                    IndexLogin.showNotice("绑定失败, 稍后再试")
                }
            })
        },
        IndexLogin.bindMobile = function() {
            var phone = $("#bind_phone_num_tx").val();
            phone = $.trim(phone);
            var sms = $("#bind_phone_sms_tx").val();
            if (sms = $.trim(sms), "" === phone) return IndexLogin.showNotice("手机号码未填写"),
            !1;
            return /^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\d{8}/i.test(phone) && 11 == phone.length ? "" === sms ? (IndexLogin.showNotice("手机验证码未填"), !1) : void $.ajax({
                url: "/bind-phone/index",
                dataType: "json",
                method: "post",
                async: !1,
                data: {
                    phone_num: phone,
                    sms: sms
                },
                success: function(response) {
                    1 == response.status ? (IndexLogin.showNotice("绑定成功"), setTimeout(function() {
                        location.reload()
                    },
                    1e3)) : IndexLogin.showNotice(response.msg)
                },
                error: function() {
                    IndexLogin.showNotice("绑定失败, 稍后再试")
                }
            }) : (IndexLogin.showNotice("手机号格式不正确"), !1)
        },
        IndexLogin.wechat_login = function() {
            var redirect_url = window.location.href;
            window.location.href = "/wechat-login/index?redirect_url=" + encodeURIComponent(redirect_url)
        },
        IndexLogin.qqLogin = function() {
            var param = '项目id';
            window.location.href = "/public/qqlogin/index.php?param=" + param;
        IndexLogin.weiboLogin = function() {
            var redirect_url = window.location.href;
            window.location.href = "/weibo-login/index?redirect_url=" + encodeURIComponent(redirect_url)
        },
        IndexLogin.popAccountLogin = function() {
            $("#findPwdWinpop").hide(),
            $("#loginWinpop").show()
        },
        IndexLogin.popReg = function() {
            for (var _i = 0,
            _a = IndexLogin.pop_list; _i < _a.length; _i++) {
                var id = _a[_i];
                $(id).hide()
            }
            $("#regPop").show()
        },
        IndexLogin.prototype.login = function() {
            var username = $("#login_username").val(),
            pwd = $("#login_password").val();
            // username.length && pwd.length ? $.ajax({
            //     url: "/login/ajax",
            //     data: {
            //         lname: username,
            //         lpwd: pwd,
            //         lgid: "",
            //         lcid: this.ch_id
            //     },
            //     dataType: "json",
            //     success: function(response) {
            //         0 != response.status ? location.reload() : IndexLogin.showNotice(response.msg)
            //     },
            //     error: function() {
            //         IndexLogin.showNotice("服务器错误，请稍后重试")
            //     }
            // }) : IndexLogin.showNotice("用户名或密码不能为空")
            username.length && pwd.length ? window.parent.sdkModule.sdkLogin(username,pwd) : IndexLogin.showNotice("用户名或密码不能为空");
        },
        IndexLogin.prototype.regSendSMS = function() {
            var _this = this;
            console.log("send-sms");
            var phone = $.trim($("#reg_username").val());
            if (console.log(phone), "" === phone) return IndexLogin.showNotice("手机号码未填写"),
            !1;
            if (!/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\d{8}/i.test(phone) || 11 != phone.length) return IndexLogin.showNotice("手机号格式不正确"),
            !1;
            $.ajax({
                url: "/reg/send-sms",
                dataType: "json",
                method: "post",
                async: !1,
                data: {
                    phone_num: phone
                },
                success: function(response) {
                    if (1 == response.status) {
                        IndexLogin.showNotice("发送成功"),
                        _this._sms_count = 60;
                        var sel = "#rp_getcode";
                        $(sel).addClass("cur"),
                        _this.countSms(sel)
                    } else IndexLogin.showNotice(response.msg)
                },
                error: function() {
                    IndexLogin.showNotice("发送失败，请稍后重试")
                }
            })
        },
        IndexLogin.prototype.reg = function() {
            var username = $("#reg_username").val(),
            pwd = $("#reg_password").val(),
            code = $("#reg_code").val(); (username = $.trim(username), pwd = $.trim(pwd), code = $.trim(code), username.length && pwd.length && code.length) ? new RegExp(/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\d{8}/i).test(username) ? pwd.length < 6 ? IndexLogin.showNotice("密码不能少于6个字") : $.ajax({
                url: "/reg/ajax2",
                data: {
                    rname: username,
                    rpwd: pwd,
                    rchannel: this.ch_id,
                    rgid: "",
                    rcode: code
                },
                dataType: "json",
                success: function(response) {
                    console.log(response),
                    0 != response.status ? location.reload() : IndexLogin.showNotice(response.msg)
                },
                error: function() {
                    IndexLogin.showNotice("服务器错误，请稍后重试")
                }
            }) : IndexLogin.showNotice("手机号格式不正确") : IndexLogin.showNotice("手机号、密码、验证码不能为空")
        },
        IndexLogin.popFindPwd = function() {
            for (var _i = 0,
            _a = IndexLogin.pop_list; _i < _a.length; _i++) {
                var id = _a[_i];
                $(id).hide()
            }
            $("#findPwdPop").show()
        },
        IndexLogin.prototype.findPwdSms = function() {
            var _this = this;
            if (this._sms_count > 0) IndexLogin.showNotice("请等待" + this._sms_count + "秒后再试");
            else {
                var phone = $.trim($("#fwp_phone").val());
                phone ? $.ajax({
                    url: "/phone-reset-pwd/send-sms",
                    data: {
                        phone_number: phone
                    },
                    dataType: "json",
                    success: function(response) {
                        if (1 == response.status) {
                            _this._sms_count = 60;
                            var sel = "#fwp_getcode";
                            $(sel).addClass("cur"),
                            _this.countSms(sel)
                        } else IndexLogin.showNotice(response.msg)
                    },
                    error: function() {
                        IndexLogin.showNotice("系统错误, 请稍后重试")
                    }
                }) : IndexLogin.showNotice("手机号不能为空")
            }
        },
        IndexLogin.prototype.countSms = function(sel) {
            var _this = this;
            if (this._sms_count--, this._sms_count <= 0) return $(sel).removeClass("cur"),
            void $(sel).text("获取验证码");
            $(sel).removeAttr("onclick"),
            $(sel).text(this._sms_count + "秒后重发"),
            setTimeout(function() {
                _this.countSms(sel)
            },
            1e3)
        },
        IndexLogin.findPwd = function() {
            var username = $.trim($("#fwp_phone").val()),
            code = $.trim($("#fwp_code").val()),
            pwd = $.trim($("#fwp_pwd").val());
            username && code && pwd ? $.ajax({
                url: "/phone-reset-pwd/index2",
                method: "post",
                data: {
                    phone_number: username,
                    sms: code,
                    pwd: pwd
                },
                dataType: "json",
                success: function(response) {
                    1 == response.status ? (IndexLogin.showNotice("修改成功"), setTimeout(IndexLogin.popAccountLogin, 2e3)) : IndexLogin.showNotice(response.msg)
                },
                error: function() {
                    IndexLogin.showNotice("系统错误，请稍后重试")
                }
            }) : IndexLogin.showNotice("用户名、密码、验证码不能为空")
        },
        IndexLogin.prototype.commentOpen = function(url) {
            var ifra = document.createElement("iframe");
            ifra.style.display = "none",
            ifra.src = "dtezxtbb://" + encodeURIComponent(url),
            document.documentElement.appendChild(ifra),
            setTimeout(function() {
                document.documentElement.removeChild(ifra)
            },
            0)
        },
        IndexLogin.pop_list = ["#regPop", "#bindMobilePop", "#manualLoginPop", "#loginPop", "#accountInfoPop", "#selectPayPop", "#findPwdPop"],
        IndexLogin
    } ();
    exports.IndexLogin = IndexLogin,
    window.indexLogin = new IndexLogin
},
function;(module, exports, __webpack_require__); {
    module.exports = __webpack_require__(1)
}]);