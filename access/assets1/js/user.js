$(function() {

	//个人中心
	$(".user-tab  .navs li a").click(function(){
		var index=$(this).parent("li").index();
		if(index!=0){
			$(".user-tab .messages.hide").removeClass("block");
		}
		$(this).addClass("active").parent("li").siblings().find("a").removeClass("active");
		$("#user1 .tab").eq(index).addClass("active").siblings().removeClass("active")
		
		
		$(".myMessageList .listList.listList-01").click(function(){
			$("#user1 .tab .my-message").removeClass("active");
			$("#user1 .tab .list-message").addClass("active");
			
		})
		$(".user-tab #myMessage .messages.hide span.huodong").click(function(){
			$("#user1 .tab .list-message").siblings().removeClass("active");
			$("#user1 .tab .list-message").addClass("active");
			
		})
		$("#system").click(function(){
			$("#user1 .tab .list-system").siblings().removeClass("active");
			$("#user1 .tab .list-system").addClass("active");
			
		})
		
	})
	
	
	$("#user1 .change1").click(function(){
		$("#user1 .tab.active .content .text.changephone").addClass("active");
		$("#user1 .tab.active .content .text.changephone").siblings().removeClass("active")
	})
	
	// $("#user1  #next").click(function(){
	// 	$("#user1 .tab.active .content .text.text1").addClass("active");
	// 	$("#user1 .tab.active .content .text.text1").siblings().removeClass("active")
	// })
	
	$("#user1  .true1").click(function(){
		$("#user1 .tab.true-div").addClass("active");
		$("#user1 .tab.true-div").siblings().removeClass("active")
	})
	$("#user1  .true2").click(function(){
		$("#user1 .tab.true-div").addClass("active");
		$("#user1 .tab.true-div").siblings().removeClass("active")
	})
	$("#user1  .true3").click(function(){
		$("#user1 .tab.true-div").addClass("active");
		$("#user1 .tab.true-div").siblings().removeClass("active")
	})
	$("#user1  #last-change1").click(function(){
		$("#user1 .tab.active .change-password").addClass("active");
		$("#user1 .tab.active .change-password").siblings().removeClass("active")
	})
	$("#user1  #last-change2").click(function(){
		$("#user1 .tab.active .change-phone").addClass("active");
		$("#user1 .tab.active .change-phone").siblings().removeClass("active")
	})
	$("#user1  #last-change3").click(function(){
		$("#user1 .tab.active .true").addClass("active");
		$("#user1 .tab.active .true").siblings().removeClass("active")
	})
	$("#user1  #sub").click(function(){
		$("#user1 .tab.active .anquan").addClass("active");
		$("#user1 .tab.active .anquan").siblings().removeClass("active")
	})
	$("#user1  #last-change2").click(function(){
		$(this).parents('#user1 .tab.true-div').removeClass("active");
		$(this).parents('#user1 .tab.true-div').siblings("#user1 .tab.people").addClass("active");
		$(this).parents('#user1 .tab.true-div').siblings("#user1 .tab.people").find(".content .text.changephone").addClass("active");
		$(this).parents('#user1 .tab.true-div').siblings("#user1 .tab.people").find(".content .text.first1").removeClass("active");
	})

	$("#user1  #last-next").click(function(){
		$(this).parent(".stepOne").css({display:"none"})
		$(this).parent(".stepOne").siblings(".stepTwo").css({display:'block'})
	})

	$("#myMessage .select").click(function(){
		$(".user-tab .messages.hide").addClass("block");
		$("#user1 .tab .my-message").addClass("active");
		$("#user1 .tab .list-message").removeClass("active");
		$("#user1 .tab .list-system").removeClass("active");
		
	})

	$("#system, #system1").click(function(){
		$("#user1 .tab .list-system").siblings().removeClass("active");
		$("#user1 .tab .list-system").addClass("active");
		
	})

	$(".First").click(function(){
		$("#user1 .tab.active .text.first1").addClass("active").siblings().removeClass("active")
	})

}) 

/**
 * 下一步
 * @param  {[type]} mobileCode [description]
 * @return {[type]}            [description]
 */
function mobileUpNext(mobileCode) {
	if($.trim(mobileCode).length > 0) {
		$("#user1 .tab.active .content .text.text1").addClass("active");
		$("#user1 .tab.active .content .text.text1").siblings().removeClass("active");
	}
}