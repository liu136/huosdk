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
<link href="/public/h/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="/public/h/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="/public/h/css/plugins/chosen/chosen.css" rel="stylesheet">
    <link href="/public/h/css/plugins/cropper/cropper.min.css" rel="stylesheet">

    <link href="/public/h/css/animate.min.css" rel="stylesheet">
    <link href="/public/h/css/style.min862f.css?v=4.1.0" rel="stylesheet">
	
    <link rel="stylesheet" href="/public/h/plugins/multiselect/lib/google-code-prettify/prettify.css" />
    <link rel="stylesheet" href="/public/h/plugins/multiselect/css/style.css" />
</head>
<body class="J_scroll_fixed">
    <ul class="nav nav-tabs">
		    <li ><a href="<?php echo U('Gameaccess/iospay');?>">版本支付列表</a></li>
			<li class="active"><a href="<?php echo U('Gameaccess/addiospay');?>">添加版本支付</a></li>
			
		</ul>
	    <div class="ibox-content">
            <form class="form-horizontal" method="post" id="fm" action="<?php echo U('Gameaccess/addiospay_post');?>">
		
			    <div class="form-group">
                            <label class="col-sm-3 control-label">游戏:</label>
                            <div class="col-sm-8">
                                    <select data-placeholder="选择游戏..." class="chosen-select" name="appid" tabindex="2">
                                      <?php if(is_array($games)): foreach($games as $k=>$vo): ?><option value="<?php echo ($k); ?>"><?php echo ($vo); ?></option><?php endforeach; endif; ?>
                                   </select>
                            </div>
								 
                </div>
				
				<div class="form-group">
                        <label class="col-sm-3 control-label">版本号:</label>

                        <div class="col-sm-8">
                                <input type="text" class="form-control" name="versions" placeholder="请输入版本" autocomplete="off">
                        </div>
                </div>
				
				<div class="form-actions">
				    <button type="submit"
						       class="btn btn-primary btn_submit J_ajax_submit_btn" id='save'>保存</button>
				    <a class="btn" href="<?php echo U('Gameaccess/iospay');?>"><?php echo L('BACK');?></a>
			    </div>
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