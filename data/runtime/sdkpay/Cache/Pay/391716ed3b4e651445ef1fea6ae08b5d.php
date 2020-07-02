<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
		<title></title>
		<link rel="stylesheet" type="text/css" href="/public/css/style2.css">
		<script src="/public/js/jquery-1.9.1.min.js" type="text/javascript" charset="utf-8"></script>
		<script>
		  function clow(){
              window.location.href = 'ndb://push';
		  }
			function sumToJava(number1, number2){
				
				window.ttw_w.goBackGameTWO('01')
			}

         </script>
	</head>

	<body>
		
		
	    <?php if($typeid == 1 ): ?><div class="con">
			<div class="su">
				<span class="info_pic"><img src="/public/img/sueccss.png"></span>
				<span class="info_word">充值成功</span>
				
				<a href="javascript:;" id="btn" onclick="sumToJava()" class="closeBtn">关闭</a>
			</div>
		</div>
		
		 <?php else: ?> 
			 <div class="con">
			<div class="su">
				<span class="info_pic"><img src="/public/img/sueccss.png"></span>
				<span class="info_word">充值成功</span>
				<a href="javascript:;" id="btn" onclick="clow()" class="closeBtn">关闭</a>
			</div>
		</div><?php endif; ?>
	</body>

</html>