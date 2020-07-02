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
<link rel="stylesheet" href="/public/mobile/css/bindPhone.css" />
<!--<header class="header">
    <ul class="main_layout">
        <li class="back_btn"><img src="/public/mobile/images/arrow_l.png" alt=""/></li>
        <li class="text">手机</li>
        <li class="close_btn"><img src="/public/mobile/images/closebtn.png" alt=""/></li>
    </ul>
</header>-->
<section >
    <div class="set" >
        <div >
            <div class="set1 gray" > 验证身份</div >
            <span class="set2" ></span >
            <div class="set1 orange" > 设置号码</div >
            <span class="set2" ></span >
            <div class="set1 gray" > 设置完成</div >
        </div >
    </div >
    <ul class="prompt_list" >
        <p class="textTitle" ><span >帐号：</span >
            <?php if($_SESSION['user']['status']== 1): ?>试玩账号<?php endif; ?>
            <?php echo ($_SESSION['user']['nickname']); ?>
        </p >
        <li >
            <div class="item" >
                <div class="icon" ><img src="/public/mobile/images/icon_phone1.png" alt="" /></div >
                <input type="text" id="phone" placeholder="请输入要绑定的手机号码" class="noBtn" />
            </div >
        </li >
        <li class="textTitle" >
            <div class="item" >
                <div class="icon" ><img src="/public/mobile/images/btn_getCode.png" alt="" /></div >
                <input placeholder="请输入短信验证码" id="code" type="text" />
                <button class="getCode" >获取验证码</button >
            </div >
        </li >
    </ul >
    <ul class="prompt_list" >
        <?php if($_SESSION['user']['status']== 1): ?><p class="textTitle" ><span >请设置密码</span ></p >
            <?php else: ?>
            <p class="textTitle" ><span >帐号密码</span ></p ><?php endif; ?>

        <li >
            <div class="item" >
                <div class="icon" ><img src="/public/mobile/images/btn_pwd.png" alt="" /></div >
                <input type="password" id="pwd" name="pwd" value="" placeholder="请输入平台帐号密码" class="noBtn" />
            </div >
        </li >
    </ul >
    <div class="error_box" ></div >
    <div class="confim_change" >
        <button >立即绑定</button >
    </div >
</section >
<input name="sendsms" id="sendsms" type="text" value="<?php echo U('Mobile/Security/mobile_send');?>" style="display:none" />
<input name="bindurl" id="bindurl" type="text" value="<?php echo U('Mobile/Security/mobile_checkcode');?>" style="display:none" />
<footer >
    <span >绑定手机可以用于找回支付密码和帐号密码</span >
</footer >
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
<script src="/public/mobile/js/huosdk_base.js" ></script >
<script src="/public/mobile/js/public.js" ></script >
<script src="/public/mobile/js/bindPhone.js" ></script >
</html>