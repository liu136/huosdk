(function(mapp,$){

    if(!$){
        return;
    }

    var hostname = (location.hostname =='dev.360.cn')||(location.hostname =='demo.dev.360.cn')?'':'http://dev.360.cn'

	mapp.util = mapp.util || {};

	mapp.util.loadCss = function(src, callback){
        var node = document.createElement('link')
            headNode = document.getElementsByTagName('head')[0],
            //callback = this.isFunction(callback) ? callback : function(){};
        node.rel = "stylesheet";
        node.type = 'text/css';
        if (node.addEventListener) {
            node.addEventListener('load', function(){
                callback();
            }, false);
            node.addEventListener('error', function (){
                callback();
            }, false);
        }
        else { // for IE6-8
            node.onreadystatechange = function () {
                var rs = node.readyState;
                if (rs === 'loaded' || rs === 'complete') {
                    node.onreadystatechange = null;
                    callback();
                }
            };
        }
        node.href = src;
        headNode.appendChild(node);
        
        return this;
    };

    mapp.util.loadJsonp = function(url,callback){
        var script = document.createElement('script');
        var isExecuted = false;
        script.type = 'text/javascript';
        script.defer = 'defer';
        document.body.appendChild(script);
        if(script.addEventListener){
            script.addEventListener('load', function(e){
                //callback.call();
                if(!isExecuted){
                    callback()
                }
                script.parentNode.removeChild(script);
            }, false);
            script.addEventListener('error',function(){
                callback.call()
            },false);
        }else if(script.readyState){
            script.onreadystatechange = function(){
                if(this.readyState == 'complete' || this.readyState == 'loaded'){
                    if(!isExecuted){
                        callback()
                    }
                }
            }
        }
        
        var callbackName = typeof callback == 'function' ?  random() : callback;
        callback = typeof callback == 'function' ? callback : window[callback];
        window[callbackName] = function(data){
            isExecuted = true;
            return callback(data);
        }
        script.src = url+'?callback='+callbackName+'&_='+ random();
        
    }

    function random(){
        return +new Date;
    }
    

    mapp.util.queryUrl = function (url, key) {
        url = url.replace(/^[^?=]*\?/ig, '').split('#')[0]; //去除网址与hash信息
        var json = {};
        //考虑到key中可能有特殊符号如“[].”等，而[]却有是否被编码的可能，所以，牺牲效率以求严谨，就算传了key参数，也是全部解析url。
        url.replace(/(^|&)([^&=]+)=([^&]*)/g, function (a, b, key , value){
            //对url这样不可信的内容进行decode，可能会抛异常，try一下；另外为了得到最合适的结果，这里要分别try
            try {
            key = decodeURIComponent(key);
            } catch(e) {}

            try {
            value = decodeURIComponent(value);
            } catch(e) {}

            if (!(key in json)) {
                json[key] = /\[\]$/.test(key) ? [value] : value; //如果参数名以[]结尾，则当作数组
            }
            else if (json[key] instanceof Array) {
                json[key].push(value);
            }
            else {
                json[key] = [json[key], value];
            }
        });
        return key ? json[key] : json;
    }


    window['onData'] =function onData(data){
		if(data){
    		$('.nloginWrap').hide();
			$('.loginWrap').show();
			$('.popUsername').text(data.name);
			data.msg && data.msg.length && ( $('.msg-num').show() ,$('.msg-num em').text(data.msg.length <=99? data.msg.length : '99+'));
			//首页管理中心入口
			$("#applybtn").html("\u7ba1\u7406\u4e2d\u5fc3").attr("href", "/mod/mobile/list");
    	}else{
    		$('.nloginWrap').show();
			$('.loginWrap').hide();
			//doLogin();
    	}
	}

    $(function(){
    	mapp.util.loadCss('http://s3.qhimg.com/static/7b2804c9226c1877.css', function(){
			var arrPage = jumpToPage();
			var tpl = '<div class="mapp-user-panel" >\
			        <div class="mapp-user-area clearfix" style="width:1000px;margin:0 auto;max-width:100%">\
			        	<div class="mapp-user-nav" style="float:left;">\
							<a href="http://www.360.cn" target="_blank">360首页</a><span class="col">|</span><a href="http://developer.360.cn/" style="font-family:Tahoma" target="_blank">English</a>\
						</div>\
						<div style="float:right">\
							<span class="nloginWrap" style="display:none">\
				                 <a class="btn-login-pop" href="javascript:void(0)" hidefocus="true">登录</a>\
				                 <a class="btn-reg-pop" href="javascript:void(0)" hidefocus="true">注册</a>\
				            </span>\
				            <span class="loginWrap" style="display:none" >\
				                 <a href="'+ hostname +'/mod/developer/?_='+ Math.ceil(Math.random()*10e8) +'&from=mobile" class="popUsername"></a><span class="col">|</span><a href="'+ hostname +'/mod/mobile/list?_='+ Math.ceil(Math.random()*10e8) +'" class="dashboard">管理中心</a><span class="col">|</span><a href="'+ hostname +'/message?_='+ Math.ceil(Math.random()*10e8) +'" class="msgcount"><span class="msg-txt">消息</span><span class="msg-num" style="display:none"><em>0</em></span></a><span class="col">|</span><a class="btn-logout-pop" href="http://login.360.cn/?op=logout&amp;crumb=810b94&amp;destUrl='+ arrPage +'" hidefocus="true">退出</a>\
				           </span>\
						</div>\
			        </div>\
			    </div>';
			$('.logo').parents('.header').before(tpl);
            mapp.util.loadJsonp(hostname + '/dev/getuser','onData');

            if($(document.body).outerWidth()<980){
                $('.nav li:nth-last-child(2)').hide();
            }

			$('.logo').parents('.header').find('.nav li a').each(function(){
                var curHref = location.href;
                (curHref == this.href)&&($(this).parents('li').siblings('li').find('a').css('background-color',''),this.style.backgroundColor = '#25863C');
            })
		})
    })

	function jumpToPage(){
		var currPath = location.href.replace(/^http:\/\/.*dev\.360\.cn/,'').split('/')[1],
            xssMap = {
                '<' : '&lt;',
                '>' : '&gt',
                '&' : '&amp;',
                '"' : '&quot;',
                ' ' : '&nbsp;',
                '\s' : '&nbsp;'
            },
            currUrl = location.href.replace(/[ \s\"<>&]/g,function(a,b){
                return xssMap[a];
            }),
			toPathSet = ['dev','Wiki','html'];
		return ~$.inArray(currPath,toPathSet) ?  currUrl : 'http://dev.360.cn';
	}
}(window.mapp||{},jQuery))