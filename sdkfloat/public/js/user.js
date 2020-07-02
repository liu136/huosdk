$(document).ready(function(){
		if(!window.name){
				window.name = 'test';
				window.location.reload();
		 }
		$("#updatepwd").click(function(){
			
			var oldpwdObj = $("#oldpwd");
			var newpwdObj = $("#newpwd");
			var unewpwdObj = $("#unewpwd");
			var posturl = $("#url").val();
			var tips = "";

			if("" == oldpwdObj.val()  || "" == newpwdObj.val()  || "" ==  unewpwdObj.val()){
				tips ="请输入密码!";
			}else if( oldpwdObj.val() == newpwdObj.val()){
				tips = "新密码与原密码一样!";
			}else if( newpwdObj.val() != unewpwdObj.val()){
				tips = "两次输入的密码不一致!";
			}
           
			if(tips!=""){
				showmsg(tips);
				return false;
			}
            
			//var action = $("#lg-form").attr('action');

			var form_data = {
				oldpwd: oldpwdObj.val(),
				newpwd: newpwdObj.val(),
				unewpwd: unewpwdObj.val(),
				type: "updatepwd"
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
					if ('success' == result.state){
						$("#lg-form").submit();
					}else{
						showmsg(result.msg);
					}
				}	
			});
			return false;
		});
	});

//
function showmsg(msg)
{
	$("#message").html('<li class="li_12"><span>'+msg+'</span></li>');
}