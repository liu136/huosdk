var head_url="";//api 域名 ,部署到线上填空
var app_id_value="6000";
var game_version_value="1.0";
var sms_type_register ="1";//smstype 短信类型 1 注册 2 登陆 3 修改密码 4 信息变更
var sms_type_updatePass="3";
var SystemTime = new Date();
var MyTime= SystemTime.getYear() +"_"+SystemTime.getMonth() +"_"+SystemTime.getDate();



var h5sdk_deviceType = 'pc';
var sUserAgent        = navigator.userAgent.toLowerCase();
var GameUrl = "";//游戏地址,空表示未初始化成功
var Isloaded = false;
var LoginSessionId = "";

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
    }

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


//连接失败时弹出提示
function preorder_err(status) {
    showMsg('网络连接错误');
}


function checkData(data){
	var userid =data.userid;
	if(userid == null){
		return "登陆错误,userid is null!";
	}
	return false;
}


//http 请求成功
function preorder_succ(result) {
    if ('success' == result.msg) {
        var txt = result.data;
      
        switch(txt.callback_type){
        	case 'init':
        		sessionStorage.setItem('gn_h5_token_'+app_id_value,txt.ut);
        		GameUrl = txt.game_url;
        		console.log("初始化成功.");
        		(txt.change == 1)?AutomaticLogin(true):localLogin();
	            return;
        		break;
        	case 'login':
        		console.log("登陆成功.");
				var ret =checkData(txt);
				if(ret){
					showMsg(ret);
					return;
				}
				showMsg("登陆成功");
				sessionStorage.setItem(LoginSessionId,joinUrl(GameUrl,txt));
				loadingGame(joinUrl(GameUrl,txt));
	            return ;
        		break;
        	case 'register':
        		console.log("账号注册成功.");
				showMsg("账号注册成功");
	            return ;
        		break;
        	case 'regMobile':
        		console.log("手机登陆成功.");
				showMsg("手机登陆成功");
	            return ;
				break;
        	default:
        }
       
        console.log(txt);
        return;
     
    }else if('automaticLoginFail' == result.msg){
    	showMsg('登陆失败请重新登录');
    }else {
		showMsg( result.msg);
        console.log(txt);
    }
}

//获取秒数时间戳
function GetTime(){
	var tmp =Date.parse(new Date()).toString();
	time_s=tmp.substr(0,10);//s 秒数
	return time_s;
}


 
//post 跳转页面
function  postData(URL,PARAMS){
	payloadpage();
	var data=JSON.stringify(PARAMS);
	var base=window.btoa(unescape(encodeURIComponent(data)));
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
				  ua:"web",
				  app_id : sessionStorage.getItem('gn_h5_appid_'+app_id_value),
				  ut :  sessionStorage.getItem('gn_h5_token_'+app_id_value), 
				  ag : '',
				  tt : data.timestamp,
				  df : 'web',
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
function initSdk(gameId){
	app_id_value=gameId;
	sessionStorage.setItem('gn_h5_appid_'+app_id_value, gameId);
	var URL = head_url+"/api/v1/web/open";
	var PARAMS ={
		  game_version :game_version_value,
		  app_id : gameId,
		  tt : GetTime(),
		  df : "web"
	};
	var data=JSON.stringify(PARAMS);
	var base=window.btoa(unescape(encodeURIComponent(data)));
 	ysSendData(URL,"h5_data="+base,false, preorder_succ, preorder_err);
}

 
//登陆
function  sdkLogin(Account,Pass){
 
	var URL = head_url+"/api/v1/web/login";
	var PARAMS ={
			  game_version :game_version_value,
			  sign : "",
			  app_id :sessionStorage.getItem('gn_h5_appid_'+app_id_value),
			  ag : "",
			  ua:"web",
			  tt : GetTime(),
			  df : "web",
			  ut :  sessionStorage.getItem('gn_h5_token_'+app_id_value),
			  deviceType:h5sdk_deviceType, 
			  uname :Account,
			  pwd : Pass
		};
	var data=JSON.stringify(PARAMS);
	var base=window.btoa(unescape(encodeURIComponent(data)));
 	ysSendData(URL,"h5_data="+base,false, preorder_succ, preorder_err);
}


//注册
function  register(Account,Password){
	var URL = head_url+"/api/v1/web/register";
	var PARAMS ={
			  game_version :game_version_value,
			  sign : "",
			  app_id : sessionStorage.getItem('gn_h5_appid_'+app_id_value),
			  ag : "",
			  tt : GetTime(),
			  df : "web",
			  uname : Account,
			  ua:"web",
			  pwd : Password
		};
	var data=JSON.stringify(PARAMS);
	var base=window.btoa(unescape(encodeURIComponent(data)));
 	 ysSendData(URL,"h5_data="+base,false, preorder_succ, preorder_err);
}

//更新密码
function  update_password(mobile,Password,code){
	var URL = head_url+"/api/v1/web/forgetpwd";
	var PARAMS ={
			  game_version :game_version_value,
			  sign : "",
			  app_id : sessionStorage.getItem('gn_h5_appid_'+app_id_value),
			  ag : "",
			  tt : GetTime(),
			  df : "web",
			  mb : mobile,
			  sc : code,
			  ut : sessionStorage.getItem('gn_h5_token_'+app_id_value),
			  st : sms_type_updatePass ,
			  ua:"web",
			  pwd : Password
		};
	var data=JSON.stringify(PARAMS);
	var base=window.btoa(unescape(encodeURIComponent(data)));
 	 ysSendData(URL,"h5_data="+base,false, preorder_succ, preorder_err);
}
//发送短信
function send_sms(mobile){
	var session_sms ='gn_h5_sms_'+MyTime ;
	var num =sessionStorage.getItem(session_sms);
	var URL = head_url+"/api/v1/web/sms/send";
	var PARAMS ={
			  //dd: "6FBCB6E7-763E-4CB5-9A49-B4055844390D",
			  game_version :game_version_value,
			  sign : "",
			  ua : "web",
			  app_id : sessionStorage.getItem('gn_h5_appid_'+app_id_value),
			  ut :  sessionStorage.getItem('gn_h5_token_'+app_id_value),
			  ag : "",
			  tt : GetTime(),
			  df : "web",
			  mb : mobile,
			  st : sms_type_register 
			};
	var data=JSON.stringify(PARAMS);
	var base=window.btoa(unescape(encodeURIComponent(data)));
 	ysSendData(URL,"h5_data="+base,false, preorder_succ, preorder_err);
	num++;
	sessionStorage.setItem(session_sms, num);
}

//发送短信
function send_sms_up(mobile){
	var session_sms ='gn_h5_sms_'+MyTime ;
	var num =sessionStorage.getItem(session_sms);
	var URL = head_url+"/api/v1/web/sms/send_up";
	var PARAMS ={
			  //dd: "6FBCB6E7-763E-4CB5-9A49-B4055844390D",
			  game_version :game_version_value,
			  sign : "",
			  ua : "web",
			  app_id : sessionStorage.getItem('gn_h5_appid'),
			  ut :  sessionStorage.getItem('gn_h5_token'),
			  ag : "",
			  tt : GetTime(),
			  df : "web",
			  mb : mobile,
			  st : sms_type_updatePass 
			};
	var data=JSON.stringify(PARAMS);
	var base=window.btoa(unescape(encodeURIComponent(data)));
 	ysSendData(URL,"h5_data="+base,false, preorder_succ, preorder_err);
	num++;
	sessionStorage.setItem(session_sms, num);
}

//qq自动登录
function AutomaticLogin(AutomaticApiLogin){
    if(AutomaticApiLogin){
    	console.log("自动登录.");
		var URL = head_url+"/api/v1/web/automaticLogin";
		var PARAMS ={
				  game_version :game_version_value,
				  sign : "",
				  app_id :sessionStorage.getItem('gn_h5_appid_'+app_id_value),
				  ag : "",
				  ua:"web",
				  rs:"1",
				  tt : GetTime(),
				  df : "web",
				  ut :  sessionStorage.getItem('gn_h5_token_'+app_id_value),
				  deviceType:h5sdk_deviceType, 
			};
		var data=JSON.stringify(PARAMS);
		var base=window.btoa(unescape(encodeURIComponent(data)));
		ysSendData(URL,"h5_data="+base,false, preorder_succ, preorder_err);
	}
}

function logout(){

		var URL = head_url+"/api/v1/web/logout";
		var PARAMS ={
				  game_version :game_version_value,
				  sign : "",
				  app_id :sessionStorage.getItem('gn_h5_appid_'+app_id_value),
				  ag : "",
				  ua:"web",
				  rs:"1",
				  tt : GetTime(),
				  df : "web",
				  ut :  sessionStorage.getItem('gn_h5_token_'+app_id_value),
				  deviceType:h5sdk_deviceType, 
			};
		var data=JSON.stringify(PARAMS);
		var base=window.btoa(unescape(encodeURIComponent(data)));
		ysSendData(URL,"h5_data="+base,false, preorder_succ, preorder_err);

}


//本地登录
function localLogin(){
	// LoginSessionId ='gn_h5_lognin_'+sessionStorage.getItem('gn_h5_appid_'+app_id_value);
	// var loginData=sessionStorage.getItem(LoginSessionId);
	// if(loginData){
	// 	showMsg("登陆成功");
	// 	loadingGame(loginData);
	// 	console.log("自动登录.");
	// }
}


//提交手机注册
function  mobile_register(mobile,Password,code){
	var URL = head_url+"/api/v1/web/regmobile";
	var PARAMS ={
				  tt :GetTime(),
				  df : "web",
				  st : sms_type_register,
				  mb : mobile,
				  sc : code,
                                  app_id : sessionStorage.getItem('gn_h5_appid_'+app_id_value),
				  //"dd" : "6FBCB6E7-763E-4CB5-9A49-B4055844390D",
				  ag : "",
				  game_version : "1.0",
				  ua :"web",
				  pwd:Password,
				  ut : sessionStorage.getItem('gn_h5_token_'+app_id_value),
				  sign: ""
			};
	var data=JSON.stringify(PARAMS);
	var base=window.btoa(unescape(encodeURIComponent(data)));
 	 ysSendData(URL,"h5_data="+base,false, preorder_succ, preorder_err);
}

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
	Isloaded = true ;
}

function joinUrl(url,data){
	var arr = [];
    for (var name in data) {
		if(name != 'game_url'){
			 arr.push(encodeURIComponent(name) + "=" + encodeURIComponent(data[name]));
		}
    }
    return url+"?"+arr.join("&");
}

function payloadpage(){
	$("#gameframe").hide();
	$("#payframe").show();
}

function SDKpaycomplete(){
	postMessage("onPayComplete","支付完成");
}

function SDKLogout(){
	//sessionStorage.removeItem(LoginSessionId);
	logout();
	$("#gameframe").hide();
	$(".wrapper").addClass("index_bg");
	$("#userInfoDiv").show();
	$("#loginWinpop").show();
}

function WechatPay(url){
	window.parent.document.getElementById("weixinPayDiv").style.display = "block";
	window.parent.document.getElementById("weixinpay_url").setAttribute("href",url);
	// $("#weixinPayDiv").show();
	// $("#weixinpay_url").prop("src",url);
}

function WechatPay_close(){
	$("weixinPayDiv").hide();
	$("#weixinpay_url").prop("src","");
}


var postMessage = function (event, data) {
      window.parent.postMessage({
        event: event,
        data: data
      }, '*');
    };


var receive = function (e) {
      var data = e.data;
      switch (data.event) {
        case 'ready':
          break;
        case 'fastPay':
          sdkPay(data.data);
          break; 
        case 'logout':
         SDKLogout();
          break;  
        default:     
      }
    };
window.addEventListener('message', receive, false);





























// 保存数据到sessionStorage
//sessionStorage.setItem('key', 'value');
 
// 从sessionStorage获取数据
//var data = sessionStorage.getItem('key');
 
// 从sessionStorage删除保存的数据
//sessionStorage.removeItem('key');
 
// 从sessionStorage删除所有保存的数据
//sessionStorage.clear();
//






