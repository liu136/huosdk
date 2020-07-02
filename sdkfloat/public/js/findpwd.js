$(document).ready(function(){
		if(!window.name){
					window.name = 'test';
					window.location.reload();
		 }

		$("#find").click(function(){
			
			var usernameObj = $("#username");
			if(usernameObj.val() == null || "" == usernameObj.val()){
			    showmsg("账号必须填写");
				return false;
			}
			
			var posturl = $("#url").val();
			var form_data = {
				username: usernameObj.val(),
				type: "findpwd"
			};
			
			$.ajax({
				type: "POST",
				url: posturl,
				data: form_data,
				error : function(XMLHttpRequest, textStatus, errorThrown) {   
					showmsg('读取超时，网络错误'); 
				},
				dataType:"json",
				success: function(result)
				{
					if (result.success){
						$("#find-pwd").submit();
					}else{
						showmsg(result.msg);
					}
				}	
			});
			return false;
		});

	$("#findok").click(function(){
			
			var smsObj = $("#sms");
			var pwdObj = $("#pwd");
			var form_data = {
				sms: smsObj.val(),
				pwd: pwdObj.val(),
				type: "smspwd"
			};
			
			$.ajax({
				type: "POST",
				url: "ajax_login.php",
				data: form_data,
				error : function(XMLHttpRequest, textStatus, errorThrown) {   
					showmsg('读取超时，网络错误'); 
				},
				dataType:"json",
				success: function(result)
				{
					if (result.success){
						$("#ok-form").submit();
					}else{
						showmsg(result.msg);
					}
				}	
			});
			return true;
		});
	});

//
function showmsg(msg)
{
	$("#message").html('<li class="li_12"><span>'+msg+'</span></li>');
}
function checkPhone(phone){
	var phone_match=/^[1][3458][0-9]{9}$/;
	if(phone_match.test(phone)){
		return true;
	}else{
		return false;
	}
}


/*-------------------------------------------*/  
var InterValObj; //timer变量，控制时间  
var count = 120; //间隔函数，1秒执行  
var curCount;//当前剩余秒数  
var code = ""; //验证码  
var codeLength = 6;//验证码长度  
function sendMessage() {  
    curCount = count;  
    var phone=$("#smobile").val();//手机号码  
	
	if(!checkPhone(phone)){
		var tips ="手机获取失败,请返回重新提交";
		showmsg(tips);
		return false;
	}
    else{  
        //产生验证码  
       // for (var i = 0; i < codeLength; i++) {  
          //  code += parseInt(Math.random() * 9).toString();  
       // }  
         
		//向后台发送处理数据  
        $.ajax({  
            type: "POST", //用POST方式传输  
            dataType: "JSON", //数据格式:JSON  
            url: 'pc_ajax.php?do=safe', //目标地址  
            data: "phone=" + phone,  
            error: function (XMLHttpRequest, textStatus, errorThrown) { 
				showmsg('发送超时，网络错误');
			},  
            success: function (result){ 
				if(result.success){
					//设置button效果，开始计时 
					
					$("#btnSendCode").attr("disabled", "true");  
					$("#btnSendCode").val("请在" + curCount + "秒内输入验证码");  
					InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次 
				}else{
					showmsg(result.msg); 
				}
			}  
        });  
    }
}  
//timer处理函数  
function SetRemainTime() {  
    if (curCount == 0) {                  
        window.clearInterval(InterValObj);//停止计时器  
        $("#btnSendCode").removeAttr("disabled");//启用按钮  
        $("#btnSendCode").val("重新发送验证码");  
        code = ""; //清除验证码。如果不清除，过时间后，输入收到的验证码依然有效      
    }  
    else {  
        curCount--;  
        $("#btnSendCode").val("请在" + curCount + "秒内输入验证码");  
    }  
}  