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

<link rel="stylesheet" href="/public/mobile/css/kefu.css" />
<div class="kefu" >
    <div class="kefuImg" >
        <?php if(!empty($contactdata['qq'])): ?><img src="/public/mobile/images/QQ1.jpg" />
            <!--<p><a class="QQ" onclick="huosdk_openqq('<?php echo ($contactdata['qq']); ?>');" href="###">客服QQ:<?php echo ($contactdata['qq']); ?></a></p>-->
            <a href="###" class="QQ" onclick="huosdk_openqq('<?php echo ($contactdata['qq']); ?>');" ><span >客服QQ：</span ><?php echo ($contactdata['qq']); ?></a ><?php endif; ?>

        <?php if(!empty($contactdata['qqgroup'])): ?><a href="###" class="qqGroup"
               onclick="huosdk_openqqgroup('<?php echo ($contactdata['qqgroup']); ?>','<?php echo ($contactdata['qqgroupkey']); ?>');" >客服QQ 群:<?php echo ($contactdata['qqgroup']); ?></a ><?php endif; ?>
        <?php if(!empty($contactdata['tel'])): ?><img src="/public/mobile/images/phone1.jpg" />
            <a href="###" onclick="huosdk_ringup('<?php echo ($contactdata["tel"]); ?>');" >客服电话:<?php echo ($contactdata["tel"]); ?></a ><?php endif; ?>
    </div >
</div >
<footer class="footer_info" >
    <p class="kefu_time" >客服时间：<?php echo ($contactdata["service_time"]); ?></p >
    <p >Copyright © 2017 <?php echo DOCDOMAIN_Rights;?>, All Rights Reserved</p >
    <p >版权所有：<?php echo C('COMPANY_NAME');?></p >
</footer >
<div class="main_model_box" ></div >
</body>
<script src="/public/mobile/js/jquery.js" ></script >
<script src="/public/mobile/js/huosdk_base.js" ></script >
<script src="/public/mobile/js/public.js" ></script >
</html>