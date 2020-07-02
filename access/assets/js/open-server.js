$(document).ready(function() {
	//开测表
	$(".Open .openTab span").click(function(){
		var index=$(this).index();
		$(this).addClass("active").siblings().removeClass("active");
		$(".Open .tabs .open-tab").eq(index).addClass("active").siblings().removeClass("active")
	})

	//找游戏，最新、最热切换
	$(".Find .zui p.title a").click(function(){
		var index=$(this).index();
		$(this).addClass("active").siblings().removeClass("active");
		$(".Find .zui .tabs .content").eq(index).addClass("active").siblings().removeClass("active")
	})

	//资讯中心
	//主模块切换
	$("#Info .indexContent .title a").click(function(){
		var index=$(this).index();
		$(this).addClass("active").siblings().removeClass("active");
		$("#Info .main .info-tabs .info-list").eq(index).addClass("active").siblings().removeClass("active")
	})

	$("#Info .gameList .rankContent .orderBy li").hover(function(){
		$(this).addClass("hover").siblings().removeClass("hover")
	})


	//周排行切换
	$(".gameList .titleHolder span").hover(function(){
		var index=$(this).index();
		$(this).addClass("current").siblings().removeClass("current");
		$(".gameList .rankContent").eq(index).addClass("tab-on").siblings().removeClass("tab-on")
	})

	$("#Gift-zone .zone .fullWidth .tab li").click(function(){
		var index=$(this).index();
		$(this).addClass("active").siblings().removeClass("active");
		$("#Gift-zone .zone .fullWidth .tabs .content").eq(index).addClass("active").siblings().removeClass("active")
	})
})



$(document).ready(function(){
//	var spe=0;
	var bool=true;
	var liLen=$("#box li").length;//li的个数
	var liWidth=$("#box li").eq(0).outerWidth(true);//一个li的宽度
	$("#box ul").css({"width":liLen*liWidth+"px"});//整个ul的宽度
	//给li排好位置
	for(var i=0;i<liLen;i++){
		$("#box li").eq(i).css({"left":i*liWidth+"px"});
	}
	$("#next").click(function(){
		show()
	})
	$("#prev").click(function(){
		if(bool){
			bool=false;
			if(liLen>2){
				for(var i=0;i<liLen-1;i++){
					$("#box li").eq(i).animate({"left":liWidth*(i+1)+"px"},function(){
						bool=true;
					})
				}
				$("#box li").eq(liLen-1).prependTo("#box ul").css({"left":-liWidth+"px"}).animate({"left":0+"px"},function(){
					bool=true;
				});;	
			}
		}
	})
	$("#prev,#next").mouseover(function(){
		clearInterval(lun)
	})
	$("#prev,#next").mouseout(function(){
		lun=setInterval(function(){
			show(1)
		},3000)
	})
	function show(){
		if(bool){
			bool=false;
			if(liLen>2){
				$("#box li").eq(0).animate({"left":-liWidth+"px"},function(){
					$("#box li").eq(0).css({"left":liWidth*(liLen-1)+"px"}).appendTo("#box ul");
					bool=true;
				});
				for(var i=1;i<liLen;i++){
					$("#box li").eq(i).animate({"left":liWidth*(i-1)+"px"},function(){
						bool=true;
					})
				}
			}
		}
	}
	var lun=setInterval(function(){
		show(1)
	},3000)

})

$(function(){
												
	$("#Gift-zone .setScore .setStar span a").mouseover(function(){
		var index=$(this).index();
		for(var i=0;i<=index;i++){
			$("#Gift-zone .setScore .setStar span a").eq(i).css({color:"red"})
		}
		$("#Gift-zone .setScore .setStar span a").mouseout(function(){
			$("#Gift-zone .setScore .setStar span a").css({color:"#cecece"})
		})
	})
	$("#Gift-zone .setScore .setStar span a").click(function(){
		var index=$(this).index();
		var index1=$("#Gift-zone .setScore .setStar span a").length;
		for(var i=0;i<=index;i++){
			$("#Gift-zone .setScore .setStar span a").eq(i).addClass("active");
		}
		for(var j=index+1; j<index1;j++){
			$("#Gift-zone .setScore .setStar span a").eq(j).removeClass("active");
			
		}
		$("#Gift-zone .setScore .setStar span b").css({display:"inline-block"})
	})
	
	
	
	
	$("#Gift-zone .tipContent span").click(function(){	
		if($(this).hasClass("active")){
			$(this).removeClass("active")
		}else{
			$(this).addClass("active")
		}
	})
})

