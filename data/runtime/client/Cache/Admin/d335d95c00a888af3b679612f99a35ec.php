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

  <link rel="stylesheet" type="text/css" href="/public/oss-h5-upload-js-php/style.css"/>

<link href="/public/bootstrap-fileinput-master/css/fileinput.css" media="all" rel="stylesheet" type="text/css" />
        <script src="/public/bootstrap-fileinput-master/js/fileinput.js" type="text/javascript"></script>
        <script src="/public/bootstrap-fileinput-master/js/fileinput_locale_zh.js" type="text/javascript"></script>
        <script src="/public/bootstrap-fileinput-master/js/fileinput_locale_es.js" type="text/javascript"></script>

</head>
<body class="J_scroll_fixed">
	<div class="wrap jj">
		<div class="common-form">
		   <div class="ibox-content">
			<form method="post" class="form-horizontal J_ajaxForm" enctype="multipart/form-data" action="<?php echo U('Gameaccess/editGame_post');?>">
				<fieldset>
					<div class="control-group">
						<label class="control-label">游戏名称：</label>
						<div class="controls">
							<input type="text" class="input" value="<?php echo ($game["gamename"]); ?>" name="gamename">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">分类：</label>
						<div class="controls">
							<select name="game_class" >
								<?php if(is_array($classlist)): foreach($classlist as $key=>$vo): $class_id_selected = ($vo['id']==$game['class'])?"selected":""; ?>
									<option value="<?php echo ($vo["id"]); ?>" <?php echo ($class_id_selected); ?>><?php echo ($vo["classname"]); ?></option><?php endforeach; endif; ?>
							</select> 
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">游戏版本：</label>
						<div class="controls">
							<input type="text" id="version" name="version" value="<?php echo ($game["version"]); ?>"><span class="formtip">版本只能为数字和小数点组合,如1.0</span>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">下载路径：</label>
						<div class="controls">
							<input type="text" id="iosurl" name="iosurl" value="<?php echo ($game["iosurl"]); ?>"><span class="formtip"></span>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">一句话简介：</label>
						<div class="controls">
							<input type="text" id="intro" name="intro" value="<?php echo ($game["intro"]); ?>"><span class="formtip">13-22个字，简要说明游戏的特色和卖点</span>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">游戏简介：</label>
						<div class="controls">
							<textarea name="content"><?php echo ($game["content"]); ?></textarea>
						</div>
					</div>
					
                    
					<div class="control-group">
						<label class="control-label">游戏图标：</label>
						<div class="controls">
							<span class="formtip">要求与安装包中图标一致。尺寸：512*512PX，圆角半径弧度：70PX，图片格式：PNG。</span>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">状态：</label>
						<div class="controls">
							<select name="status" >
							   <?php if(is_array($geamestatus)): foreach($geamestatus as $k=>$vo): $gid_select=$k==$game['status'] ?"selected":""; ?>
								<option value='<?php echo ($k); ?>' <?php echo ($gid_select); ?>><?php echo ($vo); ?></option><?php endforeach; endif; ?>
							
							</select> 
						</div>
					</div>

					<div class="control-group">
						<label class="control-label"></label>
						<div class="controls">
							<div id="kv-avatar-errors" class="center-block" style="width:800px;display:none"></div>
					        <div class="uploadlogo uploadimg" style="width:200px">
						       <input id="avatar" name="logo" type="file" class="file-loading">
					        </div>
						</div>
					</div>
                    
					
				</fieldset>
				<div class="form-actions">
				    <input type="hidden" name="filename" id="filename">
				    <input type='hidden' name='id' value='<?php echo ($game["id"]); ?>'/>
					<button type="submit" class="btn btn-primary btn_submit J_ajax_submit_btn">保存</button>
					<a class="btn" href="/Gameaccess">返回</a>
				</div>
			</form>
			</div>
		</div>
	</div>
	<script src="/public/js/common.js"></script>

	<script>
       
	   $(function(){
			 $("input[name=show]").click(function(){
				 var selectedvalue = $("input[name='show']:checked").val();
				 if(selectedvalue == 1){
					$("#ratediv").show();
				 }else{
					$("#ratediv").hide();
				 }
				 $("#rate").val("");
			 });
		});

		var btnCust = ''; 
		$("#avatar").fileinput({
			overwriteInitial: true,
			maxFileSize: 1024,
			maxFileCount: 1,
			showClose: false,
			showCaption: false,
			browseLabel: '',
			removeLabel: '',
			browseIcon: '<i class="fa fa-folder-open-o"></i>添  加',
			removeIcon: '<i class="fa fa-trash-o"></i>清 除',
			removeTitle: 'Cancel or reset changes',
			elErrorContainer: '#kv-avatar-errors',
			msgErrorClass: 'alert alert-block alert-danger',
			defaultPreviewContent: '<img src="/public/bootstrap-fileinput-master/img/add_img.png" alt="添加" style="width:160px">',
			layoutTemplates: {main2: '{preview} ' +  btnCust + ' {remove} {browse}'},
			allowedFileExtensions: ["jpg", "png", "gif"]
		});
		
	</script>


<script type="text/javascript" src="/public/oss-h5-upload-js-php/lib/plupload-2.1.2/js/plupload.full.min.js"></script>
<script type="text/javascript" src="/public/oss-h5-upload-js-php/upload.js"></script>
	
</body>
</html>