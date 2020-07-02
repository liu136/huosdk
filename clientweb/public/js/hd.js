$(function () {
		$(".hot_tj ul li").hover(
			function () {
				$(this).find(".dask",".game_js").stop().delay(50).animate({"bottom":0},300)
				$(this).find(".game_js").stop().delay(50).animate({"bottom":0},300)
			 },
			function () {
				$(this).find(".dask").stop().animate({"bottom":-200},300)
				$(this).find(".game_js").stop().animate({"bottom":-200},300)
			}
			
		)
	});
	$(function(){
		$(".item1 li").hover(
			function(){
				var that=this;	
				item1Timer=setTimeout(function(){
					$(that).find("div").animate({"bottom":0,"height":90},300,function(){
						$(that).find("p").fadeIn(100);
					});
				},200);
			},
			function(){
				var that=this;	
				clearTimeout(item1Timer);
				$(that).find("p").fadeOut(200);
				$(that).find("div").animate({"bottom":0,"height":35},300);
			}
		)
});

function gTabs(thisObj,Num){
if(thisObj.className == "active")return;
var tabObj = thisObj.parentNode.id;
var tabList = document.getElementById(tabObj).getElementsByTagName("li");
for(i=0; i <tabList.length; i++)
{
  if (i == Num)
  {
   thisObj.className = "active"; 
      document.getElementById(tabObj+"_Content"+i).style.display = "block";
  }else{
   tabList[i].className = "normal"; 
   document.getElementById(tabObj+"_Content"+i).style.display = "none";
  }
} 
}