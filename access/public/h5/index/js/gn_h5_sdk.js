var head_url="";//api 域名 ,部署到线上填空
var app_id_value="";
var game_version_value="1.0";
var sms_type_register ="1";//smstype 短信类型 1 注册 2 登陆 3 修改密码 4 信息变更
var sms_type_updatePass="3";
var sms_type_bindphone="4";
var SystemTime = new Date();
var MyTime= SystemTime.getYear() +"_"+SystemTime.getMonth() +"_"+SystemTime.getDate();



var h5sdk_deviceType = 'pc';
var sUserAgent        = navigator.userAgent.toLowerCase();
var GameUrl = "";//游戏地址,空表示未初始化成功
var _Isloaded = false;
var LoginSessionId = "";


var UserDevicetype ="web";//用户的设备
var Debug =true; //调试输出日志



var _LoginUserid ="";



function  deviceType (){
	h5sdk_deviceType     = sUserAgent.match(/ipad/i) == "ipad" ? 'ipad' : 'pc';
	if(h5sdk_deviceType == 'ipad'){
		return h5sdk_deviceType;
	}
	h5sdk_deviceType     = sUserAgent.match(/iphone/i) == "iphone" ? 'iphone' : 'pc';
	if(h5sdk_deviceType == 'iphone'){
		return h5sdk_deviceType;
	}
	h5sdk_deviceType     = sUserAgent.match(/android/i) == "android" ? 'android' : 'pc';
	
}
deviceType();
function ysSendData(url, data,isFormatParams, succ, err,callback, type, dataType, conentType) {
    if (! type) {
        type = "POST"
    }
    if (! url) {
        throw new Error("url is not find...");
    }
    if (! dataType) { 
        dataType = "JSON"
    }
    if (! conentType) {
        conentType = "application/x-www-form-urlencoded"
    }

    data ="h5_data="+encodeParam(data);



	if(isFormatParams == true){
		 var params = formatParams(data);
	}else{
		var params = data;
	}
   

    //创建 - 非IE6 - 第一步
    if (window.XMLHttpRequest) {
        var xhr = new XMLHttpRequest();
    } else { //IE6及其以下版本浏览器
        var xhr = new ActiveXObject('Microsoft.XMLHTTP');
    }

    //接收 - 第三步
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            var status = xhr.status;
            if (status >= 200 && status < 300) {
                var responsedata = JSON.parse(xhr.responseText);
                succ(responsedata, callback,xhr.responseXML);
            } else {
                err(status);
            }
        }
    };

    //连接 和 发送 - 第二步
    if (type == "GET") {
        xhr.open("GET", url + "?" + params, true);
        xhr.send(null);
    } else if (type == "POST") {
        xhr.open("POST", url, true);

        //设置表单提交时的内容类型
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send(params);
    }
}

//格式化参数
function formatParams(data) {
    var arr = [];
    for (var name in data) {
        arr.push(encodeURIComponent(name) + "=" + encodeURIComponent(data[name]));
    }
    arr.push(("v=" + Math.random()).replace(".", ""));
    return arr.join("&");
}


function encodeParam(data){
    data.ut =  sessionStorage.getItem('gn_h5_token_'+app_id_value);
    var post_data=JSON.stringify(data);
    var basese64=window.btoa(unescape(encodeURIComponent(post_data)));
    return GnCrypto.encrypt(basese64,pk,iv);
}


//连接失败时弹出提示
function preorder_err(status) {
    showMsg('网络连接错误');
}


function checkData(data){
	var userid =data.userid;
	if(userid == null){
		return "登陆错误,userid is null!";
	}
    window.sdkModule.LoginUserid  = userid;
	return false;
}


function manyAccount(data){

    var userlist =data.userlist;
    if(userlist!= ''){
    	var accountlist = "" ;
        for (var account_key in userlist){
        	var JsonData = userlist[account_key];
        	var account= JsonData.username;
            SdkLog(account);
            accountlist += "<a href=\"javascript:;\" >"+account+"</a>"
        }

        $("#loadAccountSelect").html(accountlist);
        $(".gray").show();

        // $("#loginWinpop").hide();
        $("#userInfoDiv").hide();
        $("#loadaccountPayDiv").show();

        return true;
	}else{
    	return false;
	}

}


//http 请求成功
function preorder_succ(result) {
    if ('success' == result.msg) {
        var txt = result.data;
      	
        switch(txt.callback_type){
        	case 'init':
        		sessionStorage.setItem('gn_h5_token_'+app_id_value,txt.ut);
        		GameUrl = txt.game_url;
        		SdkLog("初始化成功.");
        		(txt.change == 1)?AutomaticLogin(true):localLogin();
	            return;
        		break;
        	case 'login':
        		SdkLog("登录成功");
                if (manyAccount(txt)) {
                    SdkLog("手机下多个账号");
                    return;
                }
                var ret =checkData(txt);
                if(ret){
                    showMsg(ret);
                    return;
                }
				SuspendedBall.Add();//添加悬浮球,初始化后初始化悬浮球参数以获取侧边栏的数据
			    SuspendedBall.Move();
			    SdkLog("悬浮球初始化成功");
				showMsg("登录成功!");
				SuspendedBall.ShowBall();

				sessionStorage.setItem(LoginSessionId,joinUrl(GameUrl,txt));
				loadingGame(joinUrl(GameUrl,txt));
                if(_Isloaded){
                    $("#CloseUserIcon").click();
                    SuspendedBallContent.init();
                }
	            return ;
        		break;
        	case 'register':
        		SdkLog("账号注册成功.");
				showMsg("账号注册成功");
	            return ;
        		break;
        	case 'regMobile':
        		SdkLog("手机注册成功.");
				showMsg("手机注册成功");
	            return ;
				break;
			case 'update':
				SdkLog("修改密码成功");
				$("#findPwdWinpop").hide();
        		$("#loginWinpop").show();
        	case 'bindmobile':
        		SdkLog("绑定成功");
				 $('.no_bindphone').hide();
                 $('#bindPhoneBackA').click();
        	default:
        }
       
        SdkLog(txt);

     
    }else if('automaticLoginFail' == result.msg){
    	showMsg('登陆失败请重新登录');
    }else {
		showMsg( result.msg);
		if(result.msg=='用户名已存在'){$("#reg_code").val('')}
        SdkLog(result);
    }
}
var pk = "G$a*/a%9#2D^cfOj";//param key 加密密钥16位
var iv ='GNwl$%^&X#4Df!@#';//加密向量16位
function SdkLog(Msg){
	if(Debug){ console.log(Msg);}
}

//获取秒数时间戳
function GetTime(){
	var tmp =Date.parse(new Date()).toString();
	time_s=tmp.substr(0,10);//s 秒数
	return time_s;
}





var GnCrypto ={


    /**
     * 接口数据加密函数
     * @param str string 需加密的json字符串
     * @param key string 加密key(16位)
     * @param iv string 加密向量(16位)
     * @return string 加密密文字符串
     */
	encrypt:function(str, key, iv) {
    //密钥16位
    var key = CryptoJS.enc.Utf8.parse(key);
    //加密向量16位
    var iv = CryptoJS.enc.Utf8.parse(iv);
    var encrypted = CryptoJS.AES.encrypt(str, key, {
        iv: iv,
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.ZeroPadding
    });
    return encrypted;
	},

    /**
     * 接口数据解密函数
     * @param str string 已加密密文
     * @param key string 加密key(16位)
     * @param iv string 加密向量(16位)
     * @returns {*|string} 解密之后的json字符串
     */
    decrypt:function (str, key, iv) {
        //密钥16位
        var key = CryptoJS.enc.Utf8.parse(key);
        //加密向量16位
        var iv = CryptoJS.enc.Utf8.parse(iv);
        var decrypted = CryptoJS.AES.decrypt(str, key, {
            iv: iv,
            mode: CryptoJS.mode.CBC,
            padding: CryptoJS.pad.ZeroPadding
        });
        return decrypted.toString(CryptoJS.enc.Utf8);
    }
};







 
//post 跳转页面
function  postData(URL,PARAMS){
	payloadpage();
    var base =encodeParam(PARAMS);
    var temp = document.createElement("form");
    temp.action = URL;
    temp.method = "post";
	temp.target="payWindow";
    temp.style.display = "none";
	var opt = document.createElement("textarea");
    opt.name = "h5_data";
    opt.value = base;
    temp.appendChild(opt);
    document.body.appendChild(temp);
    temp.submit();
    return temp;

}



/**
*  game_version 游戏版本
*  oi	orderinfo 订单数据
	*  pn     product_name 	   商品名字
	*  pi     product_id  	   商品id
	*  pd     product_desc     商品描述
	*  rt     exchange_rate    虚拟币兑换比例（例如100，表示1元购买100虚拟币） 默认为0。
	*  ext    ext    CP自定义扩展字段 透传信息 默认为””
	*  crn    currency_name    虚拟币名称（如金币、元宝）默认为””
	*  pc     product_count    商品数量(除非游戏需要支持一次购买多份商品),默认为1
	*  pp     product_price    价格(元),留两位小数
	*  coi    cp_order_id    虚拟币兑换比例（例如100，表示1元购买100虚拟币） 默认为0。
* app_id  游戏id
* ut  user_token 用户登录的token
* tt  timestamp  客户端时间戳
* df  device_info 设备信息,web 接口默认web
* ri  roleinfo  角色数据
	* pn  party_name  工会、帮派名称，如果没有，请填空字符串””
	* rt  role_type  数据类型,默认1，(1为进入游戏，2为创建角色，3为角色升级 5 其他)（如果游戏无法区分，默认填1）
	* rv  role_vip  玩家vip等级，如果没有，请填0。
	* rb  role_balence  玩家游戏中游戏币余额，留两位小数;如果没有账户余额，请填0。
	* rct  rolelevel_ctime  玩家创建角色的时间 时间戳(11位的整数，单位秒)，默认0
	* rmt  rolelevel_mtime  玩家角色等级变化时间 时间戳(11位的整数，单位秒)，默认0
	* sn  server_name  游戏服务器名称
	* rn  role_name  玩家角色名称
	* sd  server_id  游戏服务器id
	* rd  role_id  玩家角色id
	* rl  role_level  玩家角色等级，如果没有，请填0。
* sign 需要cp加密验证 MD5(MD5(ext.pi.pp.rd.sd.tt).client_key)  开发上线注释记得去掉
*/
function sdkPay(data){
	var URL = head_url+"/api/v7/pay/webpay";
	var PARAMS ={
				game_version : game_version_value,
				oi : {
					pn :  data.orderinfo.product_name,
					pi :  data.orderinfo.product_id,
					pd :  data.orderinfo.product_desc,
					rt :  data.orderinfo.exchange_rate,
					crn : data.orderinfo.currency_name,
					ext : data.orderinfo.ext,
					pc :  data.orderinfo.product_count,
					pp :  data.orderinfo.product_price,
					coi : data.orderinfo.cp_order_id
				  },
				  ua:UserDevicetype,
				  app_id : sessionStorage.getItem('gn_h5_appid_'+app_id_value),
				  ut :  sessionStorage.getItem('gn_h5_token_'+app_id_value),
				  ag : '',
				  tt : data.timestamp,
				  df : UserDevicetype,
				  ri : {
					pn :data.roleinfo.party_name,
					rt : '5',
					rv : data.roleinfo.role_vip,
					rb : data.roleinfo.role_balence,
					rct: data.roleinfo.rolelevel_ctime,
					rmt: data.roleinfo.rolelevel_mtime,
					sn : data.roleinfo.server_name,
					rn : data.roleinfo.role_name,
					sd : data.roleinfo.server_id,
					rd : data.roleinfo.role_id,
					rl : data.roleinfo.role_level
				  },
				  sign : data.sign
				};
		postData(URL,PARAMS);//跳转支付面
}



//初始化sdk
var initSdk = function (gameId,devicetype){
    window.sdkModule.appid=gameId;
    UserDevicetype=window.atob(devicetype);
    MiniSite.JsLoader.load("index/js_lib/aes.js",function(){

        MiniSite.JsLoader.load("index/js_lib/pad-zeropadding.js",function(){

            MiniSite.JsLoader.load("index/js_lib/crypto-js.js",function(){

                app_id_value=gameId;
                sessionStorage.setItem('gn_h5_appid_'+app_id_value, gameId);
                var URL = head_url+"/api/v1/web/open";
                var PARAMS ={
                    game_version :game_version_value,
                    app_id : gameId,
                    tt : GetTime(),
                    df : UserDevicetype
                };

                ysSendData(URL,PARAMS,false, preorder_succ, preorder_err);

			});

		});

	});

};


//登陆
var _sdkLogin=function  (Account,Pass){
	var URL = head_url+"/api/v1/web/login";
	var PARAMS ={
			  game_version :game_version_value,
			  sign : "",
			  app_id :sessionStorage.getItem('gn_h5_appid_'+app_id_value),
			  ag : "",
			  ua:UserDevicetype,
			  tt : GetTime(),
			  df : UserDevicetype,
			  ut :  sessionStorage.getItem('gn_h5_token_'+app_id_value),
			  deviceType:h5sdk_deviceType,
			  uname :Account,
			  pwd : Pass
		};

 	ysSendData(URL,PARAMS,false, preorder_succ, preorder_err);
};


//注册

var _register =function  (Account,Password){
	var URL = head_url+"/api/v1/web/register";
	var PARAMS ={
			  game_version :game_version_value,
			  sign : "",
			  app_id : sessionStorage.getItem('gn_h5_appid_'+app_id_value),
			  ag : "",
			  tt : GetTime(),
			  df : UserDevicetype,
			  uname : Account,
			  ua:UserDevicetype,
			  pwd : Password
		};

 	 ysSendData(URL,PARAMS,false, preorder_succ, preorder_err);
};

//更新密码
var _update_password=function  (mobile,Password,code){
	var URL = head_url+"/api/v1/web/forgetpwd";
	var PARAMS ={
			  game_version :game_version_value,
			  sign : "",
			  app_id : sessionStorage.getItem('gn_h5_appid_'+app_id_value),
			  ag : "",
			  tt : GetTime(),
			  df : UserDevicetype,
			  mb : mobile,
			  sc : code,
			  ut : sessionStorage.getItem('gn_h5_token_'+app_id_value),
			  st : sms_type_updatePass ,
			  ua:UserDevicetype,
			  pwd : Password
		};

 	 ysSendData(URL,PARAMS,false, preorder_succ, preorder_err);
};
//发送短信
var  _send_sms =function (mobile){
	var session_sms ='gn_h5_sms_'+MyTime ;
	var num =sessionStorage.getItem(session_sms);
	var URL = head_url+"/api/v1/web/sms/send";
	var PARAMS ={
			  //dd: "6FBCB6E7-763E-4CB5-9A49-B4055844390D",
			  game_version :game_version_value,
			  sign : "",
			  ua : UserDevicetype,
			  app_id : sessionStorage.getItem('gn_h5_appid_'+app_id_value),
			  ut :  sessionStorage.getItem('gn_h5_token_'+app_id_value),
			  ag : "",
			  tt : GetTime(),
			  df : UserDevicetype,
			  mb : mobile,
			  st : sms_type_register
			};

 	ysSendData(URL,PARAMS,false, preorder_succ, preorder_err);
	num++;
	sessionStorage.setItem(session_sms, num);
};

//发送短信
var _send_sms_up=function (mobile){
	var session_sms ='gn_h5_sms_'+MyTime ;
	var num =sessionStorage.getItem(session_sms);
	var URL = head_url+"/api/v1/web/sms/send_up";
	var PARAMS ={
			  //dd: "6FBCB6E7-763E-4CB5-9A49-B4055844390D",
			  game_version :game_version_value,
			  sign : "",
			  ua : UserDevicetype,
			  app_id : sessionStorage.getItem('gn_h5_appid_'+app_id_value),
			  ut :  sessionStorage.getItem('gn_h5_token_'+app_id_value),
			  ag : "",
			  tt : GetTime(),
			  df : UserDevicetype,
			  mb : mobile,
			  st : sms_type_updatePass
			};

 	ysSendData(URL,PARAMS,false, preorder_succ, preorder_err);
	num++;
	sessionStorage.setItem(session_sms, num);
};

//发送短信
var _send_sms_ph = function (mobile){
	var session_sms ='gn_h5_sms_'+MyTime ;
	var num =sessionStorage.getItem(session_sms);
	var URL = head_url+"/api/v1/web/sms/send_bindphone";
	var PARAMS ={
			  //dd: "6FBCB6E7-763E-4CB5-9A49-B4055844390D",
			  game_version :game_version_value,
			  sign : "",
			  ua : UserDevicetype,
			  app_id : sessionStorage.getItem('gn_h5_appid_'+app_id_value),
			  ut :  sessionStorage.getItem('gn_h5_token_'+app_id_value),
			  ag : "",
			  tt : GetTime(),
			  df : UserDevicetype,
			  mb : mobile,
			};


 	ysSendData(URL,PARAMS,false, preorder_succ, preorder_err);
	num++;
	sessionStorage.setItem(session_sms, num);
};

//提交绑定注册
var _update_bindmobile=function  (mobile,mobile_old,code){
	var URL = head_url+"/api/v1/web/update_bindphone";
	var PARAMS ={
			  game_version :game_version_value,
			  sign : "",
			  app_id : sessionStorage.getItem('gn_h5_appid_'+app_id_value),
			  ag : "",
			  tt : GetTime(),
			  df : UserDevicetype,
			  mb : mobile,
			  sc : code,
			  ut : sessionStorage.getItem('gn_h5_token_'+app_id_value),
			  st : sms_type_updatePass ,
			  ua:UserDevicetype,
			  mbd : mobile_old
		};

 	 ysSendData(URL,PARAMS,false, preorder_succ, preorder_err);
};


//qq自动登录
function AutomaticLogin(AutomaticApiLogin){
    if(AutomaticApiLogin){
    	SdkLog("自动登录.");
		var URL = head_url+"/api/v1/web/automaticLogin";
		var PARAMS ={
				  game_version :game_version_value,
				  sign : "",
				  app_id :sessionStorage.getItem('gn_h5_appid_'+app_id_value),
				  ag : "",
				  ua:UserDevicetype,
				  rs:"1",
				  tt : GetTime(),
				  df : UserDevicetype,
				  ut :  sessionStorage.getItem('gn_h5_token_'+app_id_value),
				  deviceType:h5sdk_deviceType,
			};

		ysSendData(URL,PARAMS,false, preorder_succ, preorder_err);
	}
}

function logout(){

		var URL = head_url+"/api/v1/web/logout";
		var PARAMS ={
				  game_version :game_version_value,
				  sign : "",
				  app_id :sessionStorage.getItem('gn_h5_appid_'+app_id_value),
				  ag : "",
				  ua:UserDevicetype,
				  rs:"1",
				  tt : GetTime(),
				  df : UserDevicetype,
				  ut :  sessionStorage.getItem('gn_h5_token_'+app_id_value),
				  deviceType:h5sdk_deviceType,
			};

		ysSendData(URL,PARAMS,false, preorder_succ, preorder_err);

}


//本地登录
function localLogin(){
	// LoginSessionId ='gn_h5_lognin_'+sessionStorage.getItem('gn_h5_appid_'+app_id_value);
	// var loginData=sessionStorage.getItem(LoginSessionId);
	// if(loginData){
	// 	showMsg("登录成功");
	// 	loadingGame(loginData);
	// 	SdkLog("自动登录.");
	// }
}


//提交手机注册
var _mobile_register=function  (mobile,Password,code){
	var URL = head_url+"/api/v1/web/regmobile";
	var PARAMS ={
				  tt :GetTime(),
				  df : UserDevicetype,
				  st : sms_type_register,
				  mb : mobile,
				  sc : code,
                                  app_id : sessionStorage.getItem('gn_h5_appid_'+app_id_value),
				  //"dd" : "6FBCB6E7-763E-4CB5-9A49-B4055844390D",
				  ag : "",
				  game_version : "1.0",
				  ua :UserDevicetype,
				  pwd:Password,
				  ut : sessionStorage.getItem('gn_h5_token_'+app_id_value),
				  sign: ""
			};

 	 ysSendData(URL,PARAMS,false, preorder_succ, preorder_err);
};

function showMsg(msg){
	$("#noticeDiv").html(msg),
    $("#noticeDiv").show(),
    $("#noticeDiv").delay(2e3).fadeOut()
}

function loadingGame(url){
	$(".wrapper").removeClass('index_bg');
	// $("#loginWinpop").hide();
	$("#userInfoDiv").hide();
	$("#gameframe iframe").prop('src',url);
	$("#gameframe").show();
    window.sdkModule.Isloaded = _Isloaded = true ;
}

function joinUrl(url,data){
	var arr = [];
    for (var name in data) {
		if(name != 'game_url' && name != 'userlist'){
			 arr.push(encodeURIComponent(name) + "=" + encodeURIComponent(data[name]));
		}
    }
    return url+"?"+arr.join("&");
}

function payloadpage(){
	(h5sdk_deviceType == 'pc') &&  $(".gray").show();
	$("#gameframe").hide();
	$("#payframe").show();
}

function SDKpaycomplete(){
	_postMessage("onPayComplete","支付完成");
}

var _SDKLogout =function (){
	//sessionStorage.removeItem(LoginSessionId);
	logout();
	// $("#gameframe").hide();
	// SuspendedBall.HideBall();
	// $(".wrapper").addClass("index_bg");
	// $("#userInfoDiv").show();
	// $("#loginWinpop").show();
	location.reload();
};

var _WechatPay=function (url){
	window.parent.document.getElementById("weixinPayDiv").style.display = "block";
	window.parent.document.getElementById("weixinpay_url").setAttribute("href",url);
	// $("#weixinPayDiv").show();
	// $("#weixinpay_url").prop("src",url);
};

var _WechatPay_close = function (){
	$("weixinPayDiv").hide();
	$("#weixinpay_url").prop("src","");
};


var _postMessage = function (event, data) {
		if(event != null && event != ''){
		  window.frames[0].postMessage({
			event: event,
			data: data
		  }, '*');
        }
};

var _UserClosepay = function () {
    $("#payframe iframe").prop('src',"");
    $("#payframe").hide();
    $(".gray").hide();
    $("#gameframe").show();
    SdkLog("支付完成");
    _postMessage("onPayComplete","PayCompleted");
};

var sdk_receive = function (e) {
      var data = e.data;
      switch (data.event) {
        case 'ready':
          break;
        case 'fastPay':
          sdkPay(data.data);
          break; 
        case 'logout':
         _SDKLogout();
          break;  
        default:     
      }
    };
window.addEventListener('message', sdk_receive, false);



window.sdkModule ={};

window.sdkModule.deviceType=h5sdk_deviceType;
window.sdkModule.initGnSdk = initSdk;
window.sdkModule.UserClosepay = _UserClosepay;
window.sdkModule.send_sms_ph =_send_sms_ph;
window.sdkModule.sdkLogin =_sdkLogin;
window.sdkModule.register = _register;
window.sdkModule.update_password=_update_password;
window.sdkModule.send_sms =_send_sms;
window.sdkModule.send_sms_up =_send_sms_up;
window.sdkModule.update_bindmobile =_update_bindmobile;
window.sdkModule.mobile_register =_mobile_register;
window.sdkModule.SDKLogout =_SDKLogout;
window.sdkModule.WechatPay=_WechatPay;
window.sdkModule.WechatPay_close=_WechatPay_close;














// 保存数据到sessionStorage
//sessionStorage.setItem('key', 'value');
 
// 从sessionStorage获取数据
//var data = sessionStorage.getItem('key');
 
// 从sessionStorage删除保存的数据
//sessionStorage.removeItem('key');
 
// 从sessionStorage删除所有保存的数据
//sessionStorage.clear();
//






