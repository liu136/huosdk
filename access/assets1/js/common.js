// /**
//  * 加载JavaScript文件
//  * @param  {[type]} url [description]
//  * @return {[type]}     [description]
//  */
// function loadScript(url) {
//   removeScript(url)
//   var node = document.createElement('script');
//   node.src = url;
//   node.type = 'text/javascript';
//   node.async = true;
//   node.charset = 'utf-8';
//   document.getElementsByTagName('head')[0].appendChild(node);
// }

// /**
//  * 删除javascript文件
//  * @param  {[type]} url [description]
//  * @return {[type]}     [description]
//  */
// function removeScript(url) {
//   var name = getFileName(url)
//   var js= /js$/i.test(url);
//   var es=document.getElementsByTagName(js?'script':'link');
//   for(var i = 0; i < es.length; i ++) {
//     if(es[i][js ? 'src' : 'href'].indexOf(name) != -1) {
//       var _parentElement = es[i].parentNode;
//       if(_parentElement){
//         _parentElement.removeChild(es[i])
//       }
//     }
//   }
// }

// /**
//  * 获取文件名称
//  * @param  {[type]} path [description]
//  * @return {[type]}   [description]
//  */
// function getFileName(path){
//   var pos1 = path.lastIndexOf('/');
//   var pos2 = path.lastIndexOf('\\');
//   var pos  = Math.max(pos1, pos2)
//   if( pos<0 )
//     return path;
//   else
//     return path.substring(pos+1);
// }

// function navSelect(index) {
//  	if(document.readyState == "complete") {
//  		$(".navList li").eq(index).addClass('active')
//  	}
// }

/**
 * 收藏
 */
function addFavorite2() {
    var url = window.location;
    var title = document.title;
    var ua = navigator.userAgent.toLowerCase();
    if (ua.indexOf("360se") > -1) {
      // layer.msg("由于360浏览器功能限制，请按 Ctrl+D 手动收藏！")
      alert("由于360浏览器功能限制，请按 Ctrl+D 手动收藏！");
    }
    else if (ua.indexOf("msie 8") > -1) {
        window.external.AddToFavoritesBar(url, title); //IE8
    }
    else if (document.all) {
  try{
   window.external.addFavorite(url, title);
  }catch(e){
    // layer.msg("由于360浏览器功能限制，请按 Ctrl+D 手动收藏！")
    alert('您的浏览器不支持,请按 Ctrl+D 手动收藏!');
  }
    }
    else if (window.sidebar) {
        window.sidebar.addPanel(title, url, "");
    }
    else {
      // layer.msg("由于360浏览器功能限制，请按 Ctrl+D 手动收藏！")
      alert('您的浏览器不支持,请按 Ctrl+D 手动收藏!');
    }
}

/**
 * 倒计时
 * @param  {[type]} obj    [description]
 * @param  {[type]} second [description]
 * @return {[type]}        [description]
 */
function countDown(obj,second,phoneObj){
  var phoneReg = /^1[34578]\d{9}$/;
  var emailReg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
  phone = phoneObj.value || $(phoneObj).attr("ng-reflect-value");
  if(!phoneReg.test(phone) && !emailReg.test(phone)) {
    return false;
  }
  if(second>=0){
      if(typeof buttonDefaultValue === 'undefined' ){
        buttonDefaultValue =  obj.defaultValue;
      }
      obj.disabled = true; 
      obj.value = buttonDefaultValue+'('+second+')';
      obj.style.backgroundColor="#b5b4b3";
      second--;
      setTimeout(function(){
        countDown(obj,second,phoneObj);
      },1000);
  }else{
      obj.disabled = false;   
      obj.value = buttonDefaultValue;
      obj.style.backgroundColor="#f69e2e";
  }   
}

/**
 * 复制
 * @param  {[type]} txt [description]
 * @return {[type]}     [description]
 */
function copyToClipboard(obj) {
	if ( window.clipboardData ) {
             window.clipboardData.setData("Text", $(obj).prev('a').text());
             alert('复制成功！');
        } else {
            $(obj).zclip({
                path:'http://img3.job1001.com/js/ZeroClipboard/ZeroClipboard.swf',
                copy:function(){return $(obj).prev('a').text();
                },
                afterCopy:function(){alert('复制成功！');}
            });
        }

	/*var Url2 = $(obj).prev('a');
  //var Url2 = $(obj).siblings('a').eq(0);
  Url2.focus();
  Url2.select(); // 选择对象
  try{
		if(document.execCommand('copy', false, null)){
			 alert("内容已复制1");
		} else{
			alert("复制失败1");
		}
	} catch(err){
		alert("复制失败2");
	}*/
  
  //document.execCommand("Copy"); // 执行浏览器复制命令
 
}