

var MiniSite=new Object();
/**
 * �ж������
 */
MiniSite.Browser={   
    ie:/msie/.test(window.navigator.userAgent.toLowerCase()),   
    moz:/gecko/.test(window.navigator.userAgent.toLowerCase()),   
    opera:/opera/.test(window.navigator.userAgent.toLowerCase()),   
    safari:/safari/.test(window.navigator.userAgent.toLowerCase())   
};
/**
 * JsLoader�������������ⲿ��js�ļ�
 */
MiniSite.JsLoader={
    /**
     * �����ⲿ��js�ļ�
     * @param sUrl Ҫ���ص�js��url��ַ
     * @fCallback js�������֮��Ĵ�����
     */
    load:function(sUrl,fCallback){   
        var _script=document.createElement('script');   
        _script.setAttribute('charset','gbk');   
        _script.setAttribute('type','text/javascript');   
        _script.setAttribute('src',sUrl);   
        document.getElementsByTagName('head')[0].appendChild(_script);   
        if(MiniSite.Browser.ie){   
            _script.onreadystatechange=function(){   
                if(this.readyState=='loaded'||this.readyStaate=='complete'){ 
                    //fCallback();
                    if(fCallback!=undefined){
                         fCallback(); 
                    }
                     
                }   
            };   
        }else if(MiniSite.Browser.moz){   
            _script.onload=function(){   
                //fCallback(); 
                if(fCallback!=undefined){
                        fCallback(); 
                }
            };   
        }else{   
            //fCallback();
            if(fCallback!=undefined){
                    fCallback(); 
            }
        }   
    }   
};