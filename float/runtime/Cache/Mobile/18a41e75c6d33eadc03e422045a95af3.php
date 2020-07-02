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

<link rel="stylesheet" href="/public/mobile/css/changepwd.css" />
<div class="changepwd_box" >
    <form action="<?php echo U('Password/uppwd_post');?>" method="post" id="ajaxform" >
        <ul >
            <li ><p ><span >原密码</span ></p ><input type="password" name="old_pwd" id="old_pwd" placeholder="请输入原密码" />
            </li >
            <li ><p ><span >新密码</span ></p ><input type="password" name="new_pwd" id="new_pwd" placeholder="请输入新密码" />
            </li >
            <li ><p ><span >确认密码</span ></p ><input type="password" name="confirm_pwd" id="confirm_pwd"
                                                    placeholder="请输入确认密码" /></li >
        </ul >

        <!-- <input type="hidden" id="passwd_url" value="<?php echo U('Password/checkpwd');?>" /> -->
        <input type="hidden" id="uppwdtoken" name="uppwdtoken" value="<?php echo ($uppwdtoken); ?>" />
        <!-- <input type="submit" value="提交" /> -->
    </form >
</div >
<div class="error_msg" ></div >
<footer >
    <div class="confim_change" >
        <button >提交</button >
    </div >
</footer >

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
<script src="/public/mobile/js/huosdk_base.js" ></script >
<script src="/public/mobile/js/public.js" ></script >
<script src="/public/mobile/js/changepwd.js" ></script >
</html>