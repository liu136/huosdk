/**
 * Created by zhangwenjia on 15-12-16.
 */
var mapp = mapp || {};
mapp.kefu = {
    initPage : function(htop,isvip){
        var rightnow=parseInt(document.body.clientWidth-1000)/2-130;
        var wright= rightnow>0?rightnow:0;

        var html='';
        html+='<a id="kefu" href="http://wpa.qq.com/msgrd?v=3&uin=3340261486" target="_blank" style="top:'+htop+'px;right:'+wright+'px"><img src="http://p2.qhimg.com/t016350858434d7b468.png" style="width: 80px;"><br/>工作日：10点-19点<br/>其他时间请留言</a>'
       $('body').append(html);
        this.loadcss('/resource/css/mod/comtest/kefu.css');
        this.htop = htop;
        this.caculatePosition();
    },
    loadcss:function(url){
        var fileref = document.createElement('link');
        fileref.setAttribute("rel","stylesheet");
        fileref.setAttribute("type","text/css");
        fileref.setAttribute("href",url);

        if(typeof fileref != "undefined"){
            document.getElementsByTagName("head")[0].appendChild(fileref);
        }
    },
    caculatePosition:function(){
        var $this = this;
        var width =$(window).width();
        var wright=0;

        if(width<=1260){
            var css = {
                'position':'absolute',
                'top':$(window).scrollTop()+400
            }
            $("#kefu").css(css);
            $('body').css('width','1260px');

        }else{
            $('body').css('width','100%');
            var rightnow=parseInt(document.body.clientWidth-1000)/2-130;
            wright= rightnow;
            var css = {
                'position':'fixed',
                'top':$this.htop
            }
            $("#kefu").css(css);

        }
        $("#kefu").css('right',wright);

    },
    initListeners : function(){
        var $this = this;
      /*  $(document.body).delegates({
            'body':{scroll:function(){
                $this.caculatePosition();
            }
          }
        });*/

    }

}
$(window).scroll(function(){
   mapp.kefu.caculatePosition();
});
$(window).resize(function(){
    mapp.kefu.caculatePosition();
});