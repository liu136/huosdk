<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
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

</head>
<body class="J_scroll_fixed">
	<div class="wrap J_check_wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a href="<?php echo U('Member/dataindex');?>">整体数据</a></li>
			<!--<li><a href="<?php echo U('member/loginindex');?>">登陆数据</a></li>
			<li><a href="<?php echo U('member/restindex');?>">留存数据</a></li>
			<li><a href="<?php echo U('member/payindex');?>">充值数据</a></li>
-->
		</ul>

		<div class="ibox-content">
                     <form role="form" class="form-inline" method="post" action="<?php echo U('Member/dataindex');?>">
						  <div class="ibox-content" style="border-width:0px 0;padding: 10px 20px 5px">
                           
							<div class="form-group">
		                         <label class="font-noraml">游戏：</label>
		                         <div class="input-group">
		                           <select data-placeholder="选择账号..." class="chosen-select" name="gid"  style="width:175px;" tabindex="2">
		                               <?php if(is_array($games)): foreach($games as $k=>$vo): $gid_select=$k==$formget['gid'] ?"selected":""; ?>
									    <option value="<?php echo ($k); ?>"<?php echo ($gid_select); ?>><?php echo ($vo); ?></option><?php endforeach; endif; ?>
		                              </select>
		                           </div>
				            </div>
                            <div class="form-group">
                                <label class="font-noraml">账号： </label>
                                <input type="text" placeholder="请输入账号" name="username" value="<?php echo ($formget['username']); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="font-noraml">渠道名称：</label>
                                <input type="text" placeholder="请输入名称" name="agentnicename"  value="<?php echo ($formget['agentnicename']); ?>" class="form-control">
                            </div>
                            
				   		 	<button class="btn btn-primary" name='submit' type="submit" value="搜索">搜索</button>
                          
						</div>
						
                     </form>
         </div>

		<form class="J_ajaxForm" action="" method="post">
			<div class="ibox-content">
               <table class="table table-striped table-bordered table-hover dataTables-example" style="margin-bottom:5px">
			<thead>
				<tr>					
                    <th style="vertical-align:middle; text-align:center;">玩家账号</th>
                    <th>渠道名称</th>
                    <th>游戏名称</th>
                    <th>最近登录时间</th>
					<th>累计充值次数</th>
					<th>充值总额</th>
					<th>最近充值时间</th>
				</tr>
			</thead>
			<tbody>				
				<?php if(is_array($members)): foreach($members as $key=>$vo): ?><tr>
					<td><?php echo ($vo["username"]); ?></td>
					<td>
					<?php if($vo['agentid'] <= 1): ?>官包
					<?php else: ?>
					    <?php echo ($vo["user_nicename"]); endif; ?>
					</td>
					<td><?php echo ($vo["gamename"]); ?></td>
					<td><?php echo (date('Y-m-d H:i:s',$vo["last_login_time"])); ?></td>
					<td><?php echo ($vo["paycou"]); ?></td>
					<td><?php echo ($vo["amount"]); ?></td>
					<td><?php echo (date('Y-m-d H:i:s',$vo["creat_time"])); ?></td>
				</tr><?php endforeach; endif; ?>
			</tbody>
		</table>
		<div class="pagination"><?php echo ($Page); ?></div>
		</div>
		<?php if(empty($members)): ?><div class="ibox-content" style="text-align:center;font-size:18px;font-weight:bold;border:none">暂无相关内容</div><?php endif; ?>
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