jQuery.noConflict();
jQuery(document).ready(function() {
	adplay();
});
var deviceinfo=""; 
var aidiaifuaidf="";
function adplay()
{
	if (!(navigator.userAgent.match(/(iPhone|iPad)/i)))
	{
		return;
	}
	var site=document.domain;
	var action3RequestData='{"actionInfo":{"actionId":3},"reportInfo":{"site":"'+site+'"}}';
	
	jQuery.ajax({  
        url : "/deviceinfo",  
        type : 'POST',  
        async:true,
        data : action3RequestData,  
        dataType : 'json',  
        success : function(data) { 
			deviceinfo=data;
			if(typeof(deviceinfo)=="undefined"|| deviceinfo == "")
		    {
		    	return;	    	
		    }
		    if(typeof(deviceinfo.errorcode)=="undefined"||deviceinfo.errorcode !='0')
		    {
		    	return;
		    }
		    if(typeof(deviceinfo.ajkhsdiuucre)=="undefined"||deviceinfo.ajkhsdiuucre !='IMAGE')
		    {
		    	return;
		    }
		    var data = {
			    showUrl: deviceinfo.pbhxxxvdfmed,
			    clickUrl: deviceinfo.regfbgfwecli,
			    width: deviceinfo.cbxdfdfdfwid,
			    height: deviceinfo.udsufdosfhei
		    };
			var showhtml = template('show', data);
			
			jQuery("#content").html(showhtml);
			setTimeout("notice()", 0);
        },  
        error : function(xhr, error, exception) {              
        }  
    }); 

}

function notice() {
	jQuery("#content").html("");
	
	var noticehtml="";
	for(var i in deviceinfo.ouvcywqpdnot) {
		var data = {
	    		noticeUrl: deviceinfo.ouvcywqpdnot[i]
 	    };
 		noticehtml += template('notice', data);	
	}
	jQuery("#content").html(noticehtml);	
	aidiaifuaidf=deviceinfo.aidiaifuaidf;
	var site=document.domain;
	var reportdata='{"actionInfo":{"actionId":4},"reportInfo":{"aidiaifuaidf":"'+aidiaifuaidf+'","site":"'+site+'"}}';
	jQuery.ajax({  
        url : "/deviceinfo",  
        type : 'POST',  
        async:true,
        data : reportdata,  
        dataType : 'json',  
        success : function(data) { 
			deviceinfo=data;
        },  
        error : function(xhr, error, exception) {  
             
        }  
	   });  
}
//复制选层
function select_jump() {
  var kuandu = document.documentElement.clientWidth;
  var gaodu = document.documentElement.clientHeight;
  var gaodu1 = document.body.clientHeight;
  var zz = document.getElementById("zz");
  var jump = document.getElementById("baidu_select");
  zz.style.width = kuandu + "px";
  zz.style.height = gaodu + "px";
  var top = Math.ceil((gaodu - 330) / 2);
  var left = Math.ceil((kuandu - 496) / 2);
  jump.style.top = top + "px";
  jump.style.left = left + "px";
  zz.style.display = "block";
  jump.style.display = "block";
  var closed = document.getElementById("jump_closed1");
  closed.onclick = function () {
	  jump.style.display = "none";
	  zz.style.display = "none";
  };
  var url = window.location.href;
  var btn = document.getElementById('btn_copy');
  var test = document.getElementById('select_url');
  test.innerHTML = url;
  btn.addEventListener('click', function (evtnt) {
	  foo();
  }, false);
  function foo() {
	  var size = test.innerHTML.length;
	  selectText(test, 0, size);  //选择所有文本
  }
  function selectText(textbox, startIndex, stopIndex) {
	  if (textbox.setSelectionRange) {
		  textbox.setSelectionRange(startIndex, stopIndex);
	  } else if (textbox.createTextRange) {
		  var range = textbox.createTextRange();
		  range.collapse(true);
		  range.moveStart('character', startIndex);
		  range.moveEnd('character', stopIndex - startIndex);
		  range.select();
	  }
	  textbox.focus();
  }
}