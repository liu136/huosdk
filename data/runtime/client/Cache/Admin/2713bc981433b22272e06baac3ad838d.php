<?php if (!defined('THINK_PATH')) exit();?>﻿<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<!-- Set render engine for 360 browser -->
	<meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->

	<!--<link rel="shortcut icon" href="favicon.ico">-->
	<link href="/public/simpleboot/themes/<?php echo C('SP_ADMIN_STYLE');?>/theme.min.css" rel="stylesheet">
    <link href="/public/h/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="/public/h/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="/public/h/css/animate.min.css" rel="stylesheet">
    <link href="/public/h/css/style.min862f.css?v=4.1.0" rel="stylesheet">
    <style>
		.length_3{width: 180px;}
		form .input-order{margin-bottom: 0px;padding:3px;width:40px;}
		.table-actions{margin-top: 5px; margin-bottom: 5px;padding:0px;}
		.table-list{margin-bottom: 0px;}
	</style>
	<!--[if IE 7]>
	<link rel="stylesheet" href="/public/simpleboot/font-awesome/4.4.0/css/font-awesome-ie7.min.css">
	<![endif]-->
<script type="text/javascript">
//全局变量
var GV = {
    DIMAUB: "/public/",
    JS_ROOT: "js/",
    TOKEN: ""
};
</script>
<!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/public/h/js/jquery.min.js?v=2.1.4"></script>
    <script src="/public/h/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="/public/h/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="/public/h/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="/public/h/js/plugins/layer/layer.min.js"></script>
    <script src="/public/h/js/hplus.min.js?v=4.1.0"></script>
    <script type="text/javascript" src="/public/h/js/contabs.min.js"></script>
    <script src="/public/h/js/plugins/pace/pace.min.js"></script>
<?php if(APP_DEBUG): ?><style>
		#think_page_trace_open{
			z-index:9999;
		}
	</style><?php endif; ?>

<link rel="shortcut icon" href="favicon.ico"> <link href="/public/h/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="/public/h/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="/public/h/css/plugins/chosen/chosen.css" rel="stylesheet">
    <link href="/public/h/css/plugins/cropper/cropper.min.css" rel="stylesheet">
    <link href="/public/h/css/plugins/datapicker/datepicker3.css" rel="stylesheet">

    <link href="/public/h/css/animate.min.css" rel="stylesheet">
    <link href="/public/h/css/style.min862f.css?v=4.1.0" rel="stylesheet">

	<!-- Data Tables -->
    <link href="/public/h/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <style type="text/css">
		.orders{display:inline-block;width:6px;height:10px;background:url("/public/h/img/123.png") no-repeat;}
	</style>

</head>
<body class="J_scroll_fixed">
	<div class="wrap J_check_wrap">
	  <div class="ibox-content">
		<form role="form" class="form-inline" method="post" action="<?php echo U('Pay/index');?>">	 	
		   <div class="form-group">
                                <label  class="font-noraml">玩家账号：</label>
                                <input type="text" placeholder="请输入玩家账号" id="exampleInputPassword2" name="username" value="<?php echo ($formget["username"]); ?>" class="form-control">
                            </div>
			<input type="submit" class="btn btn-warning" name='date_time' value="搜索" />
			
		</form>
		</div>
		<form class="J_ajaxForm" action="" method="post">
		<div class="ibox-content">
		     <table class="table table-striped table-bordered table-hover dataTables-example" style="margin-bottom:5px">
				<thead>
					<tr>
						<th>玩家账号</th>
						<th>总付费金额</th>
						<th>最高付费金额</th>
						<th>注册渠道</th>
					</tr>
				</thead>
				
				<?php if(is_array($pays)): foreach($pays as $key=>$vo): ?><tr> 
					    <td><?php echo ($vo['username']); ?></td>
						<td><?php echo ($vo['amount']); ?></td>
						<td><?php echo ($vo['mamount']); ?></td>
						<td>
						<?php if( $vo['agentid'] == 1): ?>官包
						<?php else: ?>
						   <?php echo ($vo['user_nicename']); endif; ?>
						</td>
					</tr><?php endforeach; endif; ?>
			</table>
			<div class="pagination"><?php echo ($Page); ?></div>
         </div>
         <?php if(empty($pays)): ?><div class="ibox-content" style="text-align:center;font-size:18px;font-weight:bold;border:none">暂无相关内容</div><?php endif; ?>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
	<script src="/public/h/js/plugins/chosen/chosen.jquery.js"></script>
    <script src="/public/h/js/content.min.js?v=1.0.0"></script>
    <script src="/public/h/js/plugins/chosen/chosen.jquery.js"></script>
    <script src="/public/h/js/plugins/datapicker/bootstrap-datepicker.js"></script>
    <script src="/public/h/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="/public/h/js/plugins/clockpicker/clockpicker.js"></script>
    <script src="/public/h/js/plugins/cropper/cropper.min.js"></script>
    <script src="/public/h/js/demo/form-advanced-demo.min.js"></script>

    <script src="/public/h/js/plugins/jeditable/jquery.jeditable.js"></script>
    <script src="/public/h/js/plugins/dataTables/jquery.dataTables.js"></script>
    <script src="/public/h/js/plugins/dataTables/dataTables.bootstrap.js"></script>
    <script src="/public/h/js/content.min.js?v=1.0.0"></script>
	
</body>
</html>