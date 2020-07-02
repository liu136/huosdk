<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:49:"/huosdk/tp/application/pay/view/sdkpay/index.html";i:1523447769;}*/ ?>
<!DOCTYPE html>
<html >
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
<head lang="en" >
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no" />
    <meta charset="UTF-8" >
    <title ><?php echo $title; ?></title >
    <link rel="stylesheet" href="/public/mobile/css/payment.css" />
</head >
<body >
<!-- <header>
    <div class="return_btn" onclick="window.history.back()">
        <img src="/api/static/mobile/images/btn_return_l.png" alt=""/>
    </div>
    <h3></h3>
    <div class="close_btn">
        <img src="/api/static/mobile/images/btn_cancle.png" alt=""/>
    </div>
</header> -->
<!--帐号信息-->
<div class="account_information" >
    <p class="account" >帐号：<i ><?php echo \think\Session::get('user.username'); ?></i ></p >
    <p class="balance" ><?php echo config('config.CURRENCY_NAME'); ?>余额：<span ><?php echo \think\Session::get('user.gmremain'); ?></span >元</p >
</div >
<!--购买信息-->
<div class="buy_information" >
    <h3 class="info gray_title" >购买信息：</h3 >
    <p class="prop" >购买道具：<span ><?php echo \think\Session::get('order.product_name'); ?></span ></p >
    <p class="prop_price" >道具价格：<span ><?php echo \think\Session::get('order.product_price'); ?></span >元</p >
    <p class="payed" ><?php echo config('config.CURRENCY_NAME'); ?>支付：<span ><?php echo \think\Session::get('order.gm_cnt'); ?></span >元</p >
    <?php if(\think\Session::get('order.real_amount') >= '0.01'): ?>
    <p class="canPay" >还需支付：<span ><?php echo \think\Session::get('order.real_amount'); ?></span >元
                       <?php switch(\think\Session::get('order.benefit_type')): case "1": if(\think\Session::get('order.mem_rate') != '1'): ?>
                       (<i ><?php echo \think\Session::get('order.mem_rate')*10; ?></i >折)
                       <?php endif; break; case "2": ?>
    <p class="rebate" >返利：<span ><?php echo \think\Session::get('order.rebate_cnt'); ?></span >元</p ><?php break; default: endswitch; ?>
    </p>

</div >

<!--选择支付方式-->
<div class="change_way" >
    <h3 class="gray_title" >选择支付方式：</h3 >
    <ul class="way" >
        <li data-way="alipay" >
            <div class="way_icon" ><img src="/public/mobile/images/alipay.png" alt="" /></div >
            <span >支付宝(推荐)</span >
            <div class="right_icon" ></div >
        </li >
        <?php if(\think\Session::get('device.from') != '4'): ?>
        <li data-way="wxpay" >
            <div class="way_icon" ><img src="/public/mobile/images/wxpay.png" alt="" /></div >
            <span >微信支付</span >
            <div class="right_icon" ></div >
        </li >
        <?php endif; ?>
        <!--<li data-way="spay" >
            <div class="way_icon" ><img src="/public/mobile/images/wxpay.png" alt="" /></div >
            <span >微信支付</span >
            <div class="right_icon" ></div >
        </li >
        <!--<li data-way="zwxpay" >
            <div class="way_icon" ><img src="/public/mobile/images/wxpay.png" alt="" /></div >
            <span >微信支付</span >
            <div class="right_icon" ></div >
        </li >
        <li data-way="unionpay" >
            <div class="way_icon" ><img src="/public/mobile/images/unionpay.png" alt="" /></div >
            <span >银联在线支付</span >
            <div class="right_icon" ></div >
        </li >
        <li data-way="now" >
            <div class="way_icon" ><img src="/public/mobile/images/wxpay.png" alt="" /></div >
            <span >微信支付</span >
            <div class="right_icon" ></div >
        </li >

        <li data-way="payeco"><div class="way_icon"><img src="/public/mobile/images/unionpay.png" alt=""/></div><span>银联在线支付</span><div class="right_icon"></div></li>-->
    </ul >
    <input type="hidden" id="gamepay" name="gamepay" value='' />
</div >
<?php else: ?>
<input type="hidden" id="gamepay" name="gamepay" value='gamepay' />
</div>
<?php endif; ?>
<!--客服QQ-->
<footer class="footer" >
    <a href="#" class="pay" >立即支付</a >
    <p >
        <?php if(!(empty($contactdata['qq']) || (($contactdata['qq'] instanceof \think\Collection || $contactdata['qq'] instanceof \think\Paginator ) && $contactdata['qq']->isEmpty()))): ?>
        <span >客服QQ：<a onclick="huosdk_openqq('<?php echo $contactdata['qq']; ?>');" ><?php echo $contactdata['qq']; ?></a ></span >
        <?php endif; if(!(empty($contactdata['qqgroup']) || (($contactdata['qqgroup'] instanceof \think\Collection || $contactdata['qqgroup'] instanceof \think\Paginator ) && $contactdata['qqgroup']->isEmpty()))): ?>
        <span >Q Q 群：<a href="#"
                        onclick="huosdk_openqqgroup('<?php echo $contactdata['qqgroup']; ?>','<?php echo $contactdata['qqgroupkey']; ?>');" ><?php echo $contactdata['qqgroup']; ?></a ></span >
        <?php endif; ?>
    </p >
</footer >
<!--支付操作-->
<form action="preorder" method="post" id="payform" style="display:none" >
    <input type="hidden" value="<?php echo \think\Session::get('order.order_id'); ?>" id="orderid" name="orderid" />
    <input type="hidden" id="paytype" name="paytype" />
    <input type="hidden" id="paytoken" name="paytoken" value="<?php echo \think\Session::get('order.paytoken'); ?>" />
</form >
</body >
<script src="/public/mobile/js/payment.js" ></script >
<script src="/public/mobile/js/fastclick.js" ></script >
<script >
    window.addEventListener("load", function () {
        FastClick.attach(document.body);
    }, false);
</script >
</html >