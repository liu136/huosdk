console.log(1)
var verifyCode = new GVerify("v_container");
console.log(verifyCode)
$(function() {
	var discount = $('#discount').val();

	$('body').css({'backgroundColor':'#fff'});
	//火币充值
	$("#pay p.p .span a").click(function(){
		$(this).addClass("active").siblings().removeClass("active");
	//		$("#user .right .text").eq(index).addClass("active").siblings().removeClass("active")
		var number = parseInt($(this).html());
		money = accMul(number, discount);
		$('#money').html(money)
		$('#number').html(number)
		$('#realMoney').val(money)
	})
	$("#pay p.p1 img").click(function(){
		$(this).addClass("active").siblings().removeClass("active")
	})

	$('#numberText').keyup(function() {
		var money = 0;
		var number = 0;
		this.value = this.value.replace(/[^0-9]+/,'');
		if(this.value != '') {
			number = this.value;
		} else {
			number = parseInt($("#pay p.p .span a.active").html());
		}
		money = accMul(number, discount);
		$('#money').html(money)
		$('#number').html(number)
		$('#realMoney').val(money)
	})
	$('#numberText').blur(function() {
		var money = 0;
		var number = 0;
		this.value = this.value.replace(/[^0-9]+/,'');
		if(this.value != '') {
			number = this.value;
		} else {
			number = parseInt($("#pay p.p .span a.active").html());
		}
		money = accMul(number, discount);
		$('#money').html(money)
		$('#number').html(number)
		$('#realMoney').val(money)
	})
})

//乘法函数，用来得到精确的乘法结果
function accMul(arg1, arg2) { 
	var m=0,s1=arg1.toString(),s2=arg2.toString(); 
	try{
		m+=s1.split(".")[1].length
	}catch(e){
		
	} 
	try{
		m+=s2.split(".")[1].length
	}catch(e){
		
	} 
	return  Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, m) 
} 
//给Number类型增加一个mul方法，调用起来更加方便。 
Number.prototype.mul = function (arg){ 
	return accMul(arg, this); 
}

/**
* 用户充值
* @return {[type]} [description]
*/
function userRecahrge() {
	if($("#username").val() == '' || $("#username").val() == null){
		alert('请先登录');
		window.location.href="/#/login";
		return false;
	}
    if($("#verifyCodeValue").val() == '') {
      alert('验证码必须填写')
    } else {
      if(verifyCode.validate($("#verifyCodeValue").val())) {
        $("#userRecharge").submit();
      } else {
        alert('验证码有误')
        $("#verifyCodeValue").val('')
      }
    }
    verifyCode.refresh();
  }

