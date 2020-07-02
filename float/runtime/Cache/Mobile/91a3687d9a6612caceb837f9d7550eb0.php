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
<style >
    .red_dot {
        float: left;
        background-color: red;
        height: 10px;
        width: 10px;
        border-radius: 5px;
        margin-top: 12px;
    }
</style >
<link rel="stylesheet" href="/public/mobile/css/user.css" />
<section >
    <div class="tenGrayBox" style="height: 0px;" ></div >
    <div class="user_header" >
        <div class="img" >
            <!-- <span>个人信息</span> -->
            <p ><b ></b >帐号：
                <span >
                <?php if($_SESSION['user']['status']== 1): ?>试玩账号<?php endif; ?>
					<?php echo ($_SESSION['user']['username']); ?>
				</span >
            </p >
        </div >
        <a class="change" onclick="huosdk_changeaccount('logout');" >切换账号</a >
    </div >
    <div class="tenGrayBox" ></div >
    <ul class="funcList" >
        <li class="list qb" >
            <a href="#" >
                <div class="left" ><b ></b ><span ><?php echo C('CURRENCY_NAME');?>：</span ><i ><?php echo ((isset($gmremain) && ($gmremain !== ""))?($gmremain):0); ?></i ></div >
                <div class="right" ><i ></i ><!-- <s></s> --></div >
            </a >
        </li >
        <!-- <li class="list qb"><a href="<?php echo U('Mobile/Wallet/charge');?>"><div  class="left"><b></b><span><?php echo C('CURRENCY_NAME');?>：</span><i><?php echo ((isset($memptb["amount"]) && ($memptb["amount"] !== ""))?($memptb["amount"]):0); ?>元</i></div><div class="right"><i>充值</i><s></s></div></a></li> -->
        <!-- <li class="list phone"><a href="<?php echo U('Mobile/Security/mobile');?>"><div  class="left"><b style="background-position: 0px -24px;"></b><span>手机</span></div><div class="right"><i><?php echo ($mobileflag); ?></i><s></s></div></a></li>
                 -->
        <li class="list safe" >
            <a href="<?php echo U('Mobile/Security/index');?>" >
                <div class="left" ><b ></b ><span >密保</span ></div >
                <div class="right" ><i >手机/邮箱</i ><s ></s ></div >
            </a >
        </li >
        <li class="list pwd" >
            <a href="<?php echo U('Mobile/Password/uppwd');?>" >
                <div class="left" ><b ></b ><span >修改密码</span ></div >
                <div class="right" ><i >修改</i ><s ></s ></div >
            </a >
        </li >
        <!-- <li class="list "><a href="<?php echo U('User/Float/forgetPwd');?>"><div  class="left"><b class="forgetPwd"></b><span>忘记密码</span></div><div class="right"><s></s></div></a></li>  -->
    </ul >
    <div class="tenGrayBox" ></div >
    <ul class="funcList" >
        <li class="list lb" >
            <a href="<?php echo U('Mobile/Gift/index');?>" >
                <div class="left" ><b ></b ><span >我的礼包</span ></div >
                <div class="right" ><i ><b ><?php echo ((isset($giftnumber) && ($giftnumber !== ""))?($giftnumber):0); ?></b >个</i ><s ></s ></div >
            </a >
        </li >
        <!-- <li class="list xfmx"><a href="<?php echo U('Mobile/Message/mymsg');?>"><div class="left"><b style="background-position: 0px -307px;"></b><span>我的消息</span></div><div class="right"><s></s></div></a></li>
                -->
        <!-- 		<li class="list czmx"><a
                    href="<?php echo U('Mobile/Wallet/charge_detail');?>"><div class="left">
                            <b
                                style='background: url("/public/mobile/images/icon_recharges.png"); background-size: contain;'></b><span>充值明细</span>
                        </div>
                        <div class="right">
                            <s></s>
                        </div></a></li> -->
        <li class="list xfmx" >
            <a href="<?php echo U('Mobile/Wallet/pay_detail');?>" >
                <div class="left" ><b
                        style='background: url("/public/mobile/images/icon_xfmx.png"); background-size: contain;' ></b ><span >消费明细</span >
                </div >
                <div class="right" ><s ></s ></div >
            </a >
        </li >
        <?php if($_SESSION['device']['from']== 4): if($_SESSION['user']['agent_id']== 0): ?><li class="list sc" >
                    <a href="<?php echo U('Mobile/Code/index');?>" >
                        <div class="left" >
                            <b style='background: url("/public/mobile/images/icon_xfmx.png"); background-size: contain;' > </b >
                            <span style="float:left;" >邀请码</span >
                            <i class='red_dot' ></i >
                        </div >

                        <div class="right" style="color:red" >请输入邀请码<s ></s ></div >
                    </a >
                </li >
                <?php else: ?>
                <li class="list sc" >
                    <a href="#" >
                        <div class="left" ><b
                                style='background: url("/public/mobile/images/icon_xfmx.png"); background-size: contain;' > </b ><span >邀请码</span >
                        </div >
                        <div class="right" style="color:red" ><?php echo ($accode); ?></div >
                    </a >
                </li ><?php endif; endif; ?>
        <!-- <li class="list sc"><a href="<?php echo U('Mobile/Collection/myCollection');?>"><div  class="left"><b></b><span>我的收藏</span></div><div class="right"><i><?php echo ((isset($myCollercionCount) && ($myCollercionCount !== ""))?($myCollercionCount):0); ?>款</i><s></s></div></a></li>
            <li class="list wgdyx"><a href="<?php echo U('Mobile/Game/myPlayed');?>"><div  class="left"><b></b><span>玩过的游戏</span></div><div class="right"><s></s></div></a></li>
         -->
    </ul >
    <div class="tenGrayBox" ></div >
    <ul class="funcList" >
        <li class="list lxkf" style="height: 50px; line-height: 50px;" >
            <a href="<?php echo U('Mobile/Help/index');?>" >
                <div class="left" ><b ></b ><span >联系客服</span ></div >
                <div class="right" ><s ></s ></div >
            </a >
        </li >
    </ul >
</section >
<div class="main_model_box" ></div >
<!-- 		<footer class="footer">
			<a onclick="huosdk_changeaccount('logout');">退出账号</a>
		</footer> -->
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

</body>
<script src="/public/mobile/js/jquery.js" ></script >
<script src="/public/mobile/js/public.js" ></script >
<script src="/public/mobile/js/huosdk_base.js" ></script >
</html>