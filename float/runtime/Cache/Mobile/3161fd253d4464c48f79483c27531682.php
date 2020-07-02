<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en" >
<style >
    html {
        display: none;
    }
</style >
<script >
    if (self == top) {
        document.documentElement.style.display = 'block';
    } else {
        top.location = self.location;
    }
</script >
<head >
    <meta name="viewport"
          content="width=device-width,maximum-scale=1.0,initial-scale=1,user-scalable=no" />
    <meta charset="UTF-8" >
    <meta name="format-detection" content="telephone=no" />
    <title ><?php echo ($title); ?></title >
</head >
<link rel="stylesheet" href="/public/mobile/css/public.css" />
<body >

<!-- <header class="header">
<ul class="main_layout">
    <li class="back_btn" ><img src="/public/mobile/images/arrow_l.png" alt=""/></li>
    <li class="text"><?php echo ($title); ?></li>
    <li class="close_btn" ><img src="/public/mobile/images/closebtn.png" alt=""/></li>
</ul>
</header> -->
<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no" >
<link rel="stylesheet" href="/public/mobile/css/libao.css" />
<nav >
    <ul >
        <li class="active" ><a href="<?php echo U('Mobile/Gift/index', array('show'=>'list'));?>" >礼包列表</a ></li >
        <li ><a href="<?php echo U('Mobile/Gift/index', array('show'=>'mygift'));?>" >我的礼包</a ></li >
    </ul >
</nav >
<div class="libao_box" >
    <ul class="box get" >
        <?php if(is_array($gifts)): $i = 0; $__LIST__ = $gifts;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="item" >
                <div class="left" >
                    <a href="<?php echo U('Mobile/Gift/detail',array('giftid'=>$vo['giftid']));?>" >
                        <div class="img" >
                            <img src="<?php echo ($vo["icon"]); ?>" onerror="javascript:this.src='/public/mobile/images/lb.png'"
                                 alt="<?php echo ($vo["name"]); ?>" />
                        </div >
                        <div class="text" >
                            <h3 ><?php echo ($vo["giftname"]); ?></h3 >
                            <p class="count" >剩余：<span ><?php echo ($vo["remain"]); ?>/<?php echo ($vo["total"]); ?></span ></p >
                            <p class="progress" ><b
                                    style="width:<?php echo ($vo['total']+100-$vo['total']-$vo['remain']*100/$vo['total']); ?>%" ></b >
                            </p >
                        </div >
                    </a >
                </div >
                <div class="right" >
                    <input type="hidden" class="app_id" value="<?php echo ($vo["gameid"]); ?>" />
                    <input type="hidden" class="giftid" value="<?php echo ($vo["giftid"]); ?>" />
                    <input type="hidden" class="getGiftUrl" value="<?php echo U('Mobile/Gift/ajaxGift');?>" />
                    <a class="getGift" href="javascript:;" >领取</a >
                </div >
            </li ><?php endforeach; endif; else: echo "" ;endif; ?>
    </ul >

</div >
<div class="getBox" >
    <div class="msg_box" >
        <div class="top" >
            <h3 class="getstatus" >领取失败</h3 >
            <p class="tips" ></p >
            <p class="giftcode" >
            <p >
        </div >
        <p ><i id="activeCode" ></i ></p >
        <button class="close_btn" >关闭</button >
    </div >
</div >
<div class="loading_more" >
    <ul class="loadBtn loadBtn2" >
        <li class="line" ><b ></b ></li >
        <li class="text" >加载更多</li >
        <li class="line" ><b ></b ></li >
    </ul >
    <div class="btn_top" >
        <img src="/public/mobile/images/btn_top.png" alt="" />
    </div >
</div >
<!-- 加载底部 -->
<footer class="footer" >
    <!--<p style="cursor:pointer;" >
        <?php if(!empty($contactdata['qq'])): if(is_array($contactdata['qq'])): foreach($contactdata['qq'] as $key=>$vo): ?><a href="###" class="QQ" onclick="huosdk_openqq('<?php echo ($vo); ?>');" ><span >客服QQ：</span ><?php echo ($vo); ?></a ><?php endforeach; endif; ?>
            <!--<a href="###" class="QQ" onclick="huosdk_openqq('<?php echo ($contactdata['qq']); ?>');" ><span >客服QQ：</span ><?php echo ($contactdata['qq']); ?></a ><?php endif; ?>
		<br/>
        <?php if(!empty($contactdata['qqgroup'])): if(is_array($contactdata['qqgroup'])): foreach($contactdata['qqgroup'] as $k=>$vo): ?><a href="###" class="QQ"
               onclick="huosdk_openqqgroup('<?php echo ($vo); ?>','<?php echo ($contactdata['qqgroupkey'][k]); ?>');" ><span >	Q Q 群：</span ><?php echo ($vo); ?></a ><?php endforeach; endif; ?>
            <!--<a href="###" class="QQgroup"
               onclick="huosdk_openqqgroup('<?php echo ($contactdata['qqgroup']); ?>','<?php echo ($contactdata['qqgroupkey']); ?>');" ><span >	Q Q 群：</span ><?php echo ($contactdata['qqgroup']); ?></a ><?php endif; ?>
    </p >-->
</footer ><!--
<footer class="footer_nav">
	<ul>
		<li class="zh <?php echo ($useractive); ?>"><a href="<?php echo U('Mobile/User/index');?>"><b></b><p>帐户</p></a></li>
		<li class="lb <?php echo ($lbactive); ?>"><a href="<?php echo U('Mobile/Gift/index');?>"><b></b><p>礼包</p></a></li>
		 --><!-- <li class="msg <?php echo ($msgactive); ?>"><a href="<?php echo U('Mobile/Message/mymsg');?>"><b></b><p>消息</p></a></li> -->
<!-- <li class="msg <?php echo ($msgactive); ?>"><a href="<?php echo U('Mobile/Help/index');?>"><b></b><p>联系客服</p></a></li> -->
<!-- <li class="charge <?php echo ($chargeactive); ?>"><a href="<?php echo U('Mobile/Wallet/charge');?>"><b></b><p>充值</p></a></li> -->
<!-- <li class="dt <?php echo ($dtactive); ?>" onclick="huosdk_closeweb();" ><a ><b></b><p>返回游戏</p></a></li>
</ul>
</footer>-->
<div class="main_model_box" ></div >
</body>
<script src="/public/mobile/js/jquery.js" ></script >
<script src="/public/mobile/js/public.js" ></script >
<script src="/public/mobile/js/huosdk_base.js" ></script >
<script src="/public/mobile/js/libao.js" ></script >
</html>