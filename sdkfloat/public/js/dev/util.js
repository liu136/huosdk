
    var mapp = mapp || {};
    mapp.util = {
        addEvent: function(elem, type, handle, capture) {
            var capture = capture || false;
            try {
                window.addEventListener ? elem.addEventListener(type, handle, capture) : elem.attachEvent('on' + type, handle)
            } catch (e) {
                elem['on' + type] = handle;
            }
            return this;
        },
        removeEvent: function(elem, type, handle) {
            var capture = capture || false;
            try {
                window.removeEventListener ? elem.removeEventListener(type, handle, capture) : elem.detachEvent('on' + type, handle)
            } catch (e) {
                elem['on' + type] = null;
            }
            return this;
        },
        forEach: function(arr, func) {
            var len = arr.length,
                args = [].slice.call(arguments, 2),
                i = 0;
            for (; i < len; i++) {
                func.apply(arr[i], [i, arr[i]].concat(args));
            }
            return this;
        },
        isHasClass : function(a,b){
            if(!a){
                return;
            }
            var classString = a && a.className,
                rClass = new RegExp('\\s*('+ b + ')(?:\\s+|$)');
            return rClass.test(classString); 
        },

        is_ie6 : function(version){
	        version = parseInt(version, 10) || 6;
	        var u = navigator.userAgent.toLowerCase(),
	            v = (u.match( /.+?(?:rv|it|ra|ie)[\/: ]([\d.]+)/ ) || [0,'0'])[1];
	        return (/msie/.test(u) && parseInt(v, 10) == version);
	    },
        template: function(json, tmpl) {
	        for (var p in json) {
	            tmpl = tmpl.replace(new RegExp('<%=' + p + '%>', 'g'), json[p]);
	        }
	        return tmpl;
	    },
        doPlaceholder: function() {
            var isSupported = 'placeholder' in document.createElement('input'),
                inputNode = document.getElementsByTagName('input'),
                textareaNode = document.getElementsByTagName('textarea'),
                _this = this,
                inputNodeMap = function(index, elem) {
                    if (!elem.getAttribute('placeholder')) {
                        return;
                    }
                    elem.value = elem.getAttribute('placeholder');
                    _this.addEvent(elem, 'focus', function(event) {
                        var event = event || window.event,
                            target = event.target || event.srcElement;
                        target.value = (target.getAttribute('placeholder') == target.value) ? '' : target.value;
                    }).addEvent(elem, 'blur', function(event) {
                        var event = event || window.event,
                            target = event.target || event.srcElement;
                        target.value = ('' == target.value) ? target.getAttribute('placeholder') : target.value;
                    });
                };
            if (isSupported) {
                return this;
            }
            return this.forEach(inputNode, inputNodeMap).forEach(textareaNode, inputNodeMap);
        },
        getElementsByClassName: function(searchClass, tagName, context) {
        	var nodes = context || document,
        		tag = tagName || '*',
        		result = [];
            if (document.getElementsByClassName) {
                var nodes = nodes.getElementsByClassName(searchClass);
                for (var i = 0; node = nodes[i++];) {
                    if (tag !== "*" && node.tagName === tag.toUpperCase()) {
                        result.push(node)
                    }
                }
            } else {
                var classes = searchClass.split(" "),
                    elements = (tag === "*" && nodes.all) ? nodes.all : nodes.getElementsByTagName(tag),
                    patterns = [],
                    current,
                    match,
                	i = classes.length,
                	j = elements.length; 
                while (--i >= 0) {
                    patterns.push(new RegExp("(^|\\s)" + classes[i] + "(\\s|$)"));
                }
                while (--j >= 0) {
                    current = elements[j];
                    match = false;
                    for (var k = 0, kl = patterns.length; k < kl; k++) {
                        match = patterns[k].test(current.className);
                        if (!match) break;
                    }
                    if (match) result.push(current);
                }
                
            }
            return result.length>0 ? result : null;
        },
        type : function(a){
            var str = typeof a;
            return str ==='object' ? Object.prototype.toString.call(a).replace(/^\[\w+\s(\w*)\]$/, '$1').toLowerCase() : str;
        },
        //是否为纯对象，非DOM对象
        isPlainObject : function(a){
            return this.type(a)==='object' && !a.nodeType;
        },
        isNull : function(a){
            return this.type(a) === 'null' || this.type(a)==='undefined';
        },
		isFunction : function(a){
            return this.type(a) === 'function';
        },
        isArray : function(o){
            if(o != null && o.constructor != null){
                return  Object.prototype.toString.call(o).slice(8, -1) == 'Array';
            }else{
                return false;
            }
        },
        loadCss : function(src, callback){
            var node = document.createElement('link')
                headNode = document.getElementsByTagName('head')[0],
                callback = this.isFunction(callback) ? callback : function(){};
            node.rel = "stylesheet";
            node.type = 'text/css';
            if (node.addEventListener) {
                node.addEventListener('load', function(){
                    callback();
                }, false);
                node.addEventListener('error', function () {
                    //error function
                    //callback();
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
        },

        /*
         * srcArr : url 数组
         */
        loadScript : function(srcArr,callback){
        	var len = srcArr.length,
        		callback = this.isFunction(callback) ? callback : function(){},
        		_this = this;
        	this.forEach(srcArr,function(i,elem){
        		var script = document.createElement('script');
	            script.src = this;
	            script.type = 'text/javascript';
	            script.defer = 'defer';
	            document.body.appendChild(script);
	            if(len-1 == i){
	            	if(script.addEventListener){
		                script.addEventListener('load', function(){
		                	callback.call(null,_this,mapp);
		                }, false);
		                script.addEventListener('error',function(){
		                },false);
		            }else if(script.readyState){
		                script.onreadystatechange = function(){
		                    if(this.readyState == 'complete' || this.readyState == 'loaded'){
		                    	//script.onreadystatechange = null;
		                        callback.call(null,_this,mapp);
		                    }
		                }
		            }
	            }
	            
        	})
            
        },
        getCookie : function(name){
            var rName = new RegExp('(?:^| \\s*)'+ name.replace(/([\.\*\?\+\[\]\(\)])/g,'\\$1')+'=([^;]*?)(?:;|$)','gm');
            var result = rName.exec(document.cookie);
            return result && decodeURIComponent(result[1]);
        },
        setCookie : function(name, value, expires) {
            if(this.isNull(name) || this.isNull(value)){
                console.warn('cookie\'s name or value is undefined');
                return;
            }
            var path = 'path=/',
                domain = 'domain=' + document.domain.replace('www',''),
                expires = expires ? ('expires=' + new Date(expires).toUTCString()) : '',
                name = name +'='+ encodeURIComponent(value);
            document.cookie = [name, expires, path, domain].join(' ;');
            
        },
        keyboardMonitor: function(elem, callback) {
            callback = $.isFunction(callback) ? callback : function() {};
            var args = [].slice.call(arguments, 2);
            this.addEvent(elem, 'input', function(event) {
                var event = event || window.event;
                callback.apply(null, [event].concat(args));
            }).addEvent(elem, 'propertychange', function(event) {
                var event = event || window.event;
                if(event.target){
                	return false;
                }
                callback.apply(null, [event].concat(args));
            })
        },
        _getElem : function(selector){
        	var firstLetter = selector.indexOf(0);
        	switch(firstLetter)
			{
				case '.' :
					return this.getElementsByClassName(selector.slice(1));
				case '#' :
					return document.getElementById(selector.slice(1));
				default :
					return document.getElementsByTagName(selector);
			}
        },
        eventDelegate : function(configs){
        	var rSelector = /^([\.#]?\w+[\.#\w,]*)\s+(\w+)$/g,
        		_this = this;
        	if(this.isPlainObject(configs)){
        		for(var name in configs){
        			if(configs.hasOwnProperty(name) && this.isFunction(configs[name])){
        				var match = rSelector.exec(name),
        					selectorArr = match[1],
        					eventType = match[2];
        				this.forEach(selectorArr,function(i,selector){
        					var elem = _this._getElem(selector);
        					_this.addEvent(elem,eventType,configs[name],false)

        					

        				})
        			}
        		}
        	}
        },
        padNum : function(num){
			var num = ''+num;
			return num.length<2 ? '0'+num : num;
		},
        lazyload : function(url, callback){
            var head = document.getElementsByTagName('head')[0];
                
            var nodeArray = this.isArray(url) ? url : [url];
            for(var index=0,l=nodeArray.length; index<l; index++){
                var url = nodeArray[index],
                    exist = false,
                    type = 'script' ,
                    attrName = 'src';
                if(url.indexOf('.css')>=0){
                    type = 'link';
                    attrName = 'href';
                }
                var headChildren = head.getElementsByTagName(type);
                for(var i=0,len=headChildren.length; i<length; i++){
                    if(headChildren[i][attrName] && headChildren[i][attrName].indexOf(url) >= 0){
                        exist = true;
                        return false;
                    }
                }

                if(!exist){
                    var node = document.createElement(type);
                    if(node.addEventListener) {
                        if(typeof callback=='function'){
                            node.addEventListener('load', callback, false);
                            node.addEventListener('error', function(){
                                alert('加载失败，请重试！');
                            }, false);
                        }
                    }
                    else{ // for IE6-8
                        node.onreadystatechange = function () {
                            var rs = node.readyState;
                            if (!rs || rs === 'loaded' || rs === 'complete') {
                                node.onreadystatechange = null;
                                if(typeof callback == 'function')callback();
                            }
                        };
                    }

                    node[attrName] = url;
                    if(type=='script'){
                        node.type = 'text\/javascript';
                    }
                    else{
                        node.type = 'text\/css';
                        node.rel = 'stylesheet';
                    }
                    head.appendChild(node);
                }
            }
            
        }

           
    }
    window.mapp = mapp;


$.fn.delegates = function(configs) {
    var el = $(this[0]);
    for (var name in configs) {
        var value = configs[name];
        if (typeof value == 'function') {
            var obj = {};
            obj.click = value;
            value = obj;
        };
        if(typeof value == 'object'){
        	for (var type in value) {
	        	if(type == 'propertychange'){
	        		$(name).bind('propertychange',value[type]);
	        		break;
	        	}
	            el.delegate(name, type, value[type]);
	        }
        }
        
    }
    return this;
}
