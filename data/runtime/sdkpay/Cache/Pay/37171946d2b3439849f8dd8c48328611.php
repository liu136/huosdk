<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>游戏充值</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<script src="/public/js/jquery-1.9.1.min.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript" src="/public/js/amazeui.min.js"></script>
		<link rel="stylesheet" href="/public/css/amazeui.min.css" type="text/css" />
		<link rel="stylesheet" href="/public/css/style.css" type="text/css" />
		
		<style type="text/css">

        .selectedCss{
        background-color: rgba(14, 144, 210, 1);
        
       }

      </style>


	</head>
	<body class="bgcolor">
		<header data-am-widget="header" class="am-header am-header-default jz">
		      <div class="am-header-left am-header-nav">
				<!--
		        <a href="javascript:void(0);" class="">
					<i class="am-icon-chevron-left"></i>
				</a>
				-->
		      </div>
		      <h1 class="am-header-title">
		          <a href="#title-link" class="">游戏充值</a>
		      </h1>
        </header>
		<h2 class="pay">充值金额：<span>￥<?php echo ($pay["amount"]); ?></span></h2>
		<input type="hidden" value="<?php echo ($pay["amount"]); ?>" />
		
		<form action="<?php echo U('Pay/Pay_post');?>" method="post" name="c">
            <ul class="pay-style" id="jquery_genr">
		      <?php if(is_array($payway)): foreach($payway as $k=>$vo): ?><li onclick='changeColor(this)'>
        		 <label class="am-radio-inline">
				    <input type="radio"  name="type" value="<?php echo ($vo["id"]); ?>" style="display:none" >
				 </label>
				 <img src="/public/images/<?php echo ($vo["id"]); ?>.png" width="25">
				 <span><?php echo ($vo["disc"]); ?></span>
        	   </li><?php endforeach; endif; ?>
	        </ul>
		<input type="hidden" name="amount" value="<?php echo ($pay["amount"]); ?>">
				<input type="hidden" name="username" value="<?php echo ($pay["username"]); ?>">
				<input type="hidden" name="roleid" value="<?php echo ($pay["roleid"]); ?>">
				<input type="hidden" name="serverid" value="<?php echo ($pay["serverid"]); ?>">
				<input type="hidden" name="appid" value="<?php echo ($pay["appid"]); ?>">
				<input type="hidden" name="productname" value="<?php echo ($pay["productname"]); ?>">
				<input type="hidden" name="orderdesc" value="<?php echo ($pay["orderdesc"]); ?>">
				<input type="hidden" name="imei" value="<?php echo ($pay["imei"]); ?>">
				<input type="hidden" name="agentgame" value="<?php echo ($pay["agentgame"]); ?>">
				<input type="hidden" name="attach" value="<?php echo ($pay["attach"]); ?>">
				<input type="hidden" name="cid" value="<?php echo ($pay["cid"]); ?>">
				<input type="hidden" name="reg_time" value="<?php echo ($pay["reg_time"]); ?>">
				<input type="hidden" name="paystatus" value="<?php echo ($pay["paystatus"]); ?>">
				<input type="hidden" name="token" value="<?php echo ($pay["paytoken"]); ?>">	
				<input type="hidden" name="typeid" value="<?php echo ($pay["typeid"]); ?>">	
		<!--<button id="savebtn" class="login-btn" type="button">立即充值</button>-->
        </form>

        <script src="/public/layer/layer.js"></script>
		<script type="application/javascript">
           $(document).ready(function(){  
              $(document).bind("contextmenu",function(e){   
                return false;   
             });
		     $(document).bind("selectstart",function(e){   
              return false;   
             });
           });
		   
		   //只修改指定ul下的li列表
      function changeColor(obj){
        //改变所有li样式，寻找id为jquery_genr的ul元素的所有li元素
        $("ul[id=jquery_genr] >li").removeClass("selectedCss");
        //改变当前li样式
        $(obj).addClass("selectedCss");
		var  $radio = $(obj).find("input[type=radio]");     
        if( !$radio.is(":checked") ){
              $radio.prop("checked",true);  
				var type = $radio.val();
			    if(type > 0){
				   $('form[name=c]').submit();
				}else{
				   layer.msg('请选择支付方式');
				}			  
        }

       }
      /*$('.login-btn').click(function(){
	
			    var type = $("input[name='type']:checked").val();
			    if(type > 0){
				   $('form[name=c]').submit();
				}else{
				   layer.msg('请选择支付方式');
				}
				
			});*/
     </script>
	
	</body>

</html>