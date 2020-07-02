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

 <link rel="stylesheet" type="text/css" href="/public/css/css.css">
 <link rel="shortcut icon" href="favicon.ico"> 
 <link href="/public/h/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="/public/h/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="/public/h/css/plugins/chosen/chosen.css" rel="stylesheet">
    <link href="/public/h/css/plugins/cropper/cropper.min.css" rel="stylesheet">
    <link href="/public/h/css/plugins/datapicker/datepicker3.css" rel="stylesheet">

    <link href="/public/h/css/animate.min.css" rel="stylesheet">
    <link href="/public/h/css/style.min862f.css?v=4.1.0" rel="stylesheet">

<!--必要样式-->
<style type="text/css">
#BgDiv1{background-color:#000; position:absolute; z-index:9999;  display:none;left:0px; top:0px; width:100%; height:100%;opacity: 0.6; filter: alpha(opacity=60);}
.DialogDiv{position:absolute;z-index:99999;}/*配送公告*/
.U-user-login-btn{ display:block; border:none; font-size:1em; color:#efefef; line-height:49px; cursor:pointer; height:53px; font-weight:bold;
border-radius:3px;
-webkit-border-radius: 3px;
-moz-border-radius: 3px;
 width:100%; box-shadow: 0 1px 4px #cbcacf, 0 0 40px #cbcacf ;}
 .U-user-login-btn:hover, .U-user-login-btn:active{ display:block; border:none; font-size:1em; color:#efefef; line-height:49px; cursor:pointer; height:53px; font-weight:bold;
border-radius:3px;
-webkit-border-radius: 3px;
-moz-border-radius: 3px;
 width:100%; box-shadow: 0 1px 4px #cbcacf, 0 0 40px #cbcacf ;}
.U-user-login-btn2{ display:block; border:none; font-size:1em; color:#efefef; line-height:49px; cursor:pointer; font-weight:bold;
border-radius:3px;
-webkit-border-radius: 3px;
-moz-border-radius: 3px;
 width:100%; box-shadow: 0 1px 4px #cbcacf, 0 0 40px #cbcacf ;height:53px;}
.U-guodu-box { padding:5px 15px;  background:#3c3c3f; filter:alpha(opacity=90); -moz-opacity:0.9; -khtml-opacity: 0.9; opacity: 0.9;  min-heigh:200px; border-radius:10px;}
.U-guodu-box div{ color:#fff; line-height:20px; font-size:15px; margin:0px auto; height:100%; padding-top:10%; padding-bottom:10%;}

</style>

</head>
<body class="J_scroll_fixed">
	<div id="BgDiv1"></div>
	<div class="wrap J_check_wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a href="<?php echo U('Subiospackage/index');?>">推广链接</a></li>
			<?php if(sp_auth_check(sp_get_current_admin_id(),'user/addagent','and')): ?><li><a href="<?php echo U('user/addagent');?>" target="_self">添加渠道</a></li><?php endif; ?>
		</ul>
		
		<div class="ibox-content">
                        <form id="fm" role="form" class="form-inline" method="post">
							<div class="form-group">
                                 <label class="font-noraml">游戏：</label>
                                 <div class="input-group">
                                    <select data-placeholder="选择游戏..." class="chosen-select" name="appid" id="appid" style="width:175px;" tabindex="2">
                                       <?php if(is_array($games)): foreach($games as $k=>$vo): $gid_select=$k==$formget['appid'] ?"selected":""; ?>
								          <option value="<?php echo ($k); ?>"<?php echo ($gid_select); ?>><?php echo ($vo); ?></option><?php endforeach; endif; ?>
                                   </select>
                                  </div>
								 
                            </div>

							<div class="form-group">
                                 <label class="font-noraml">渠道账号：</label>
                                 <div class="input-group">
                                    <select data-placeholder="选择账号..." class="chosen-select" name="agent_id" id="agent_id" style="width:175px;" tabindex="2">
                                       <?php if(is_array($agents)): foreach($agents as $k=>$vo): $gid_select=$k==$formget['agent_id'] ?"selected":""; $index_str = strpos($vo, "@"); if($index_str){ $domain = substr($vo,0,strpos($vo, '@')); $domain = $domain."<span>@</span>".substr($vo,strpos($vo,'@')+1); }else{ $domain = $vo; } ?>
								          <option value="<?php echo ($k); ?>"<?php echo ($gid_select); ?>><?php echo ($domain); ?></option><?php endforeach; endif; ?>
                                   </select>
                                  </div>
								 
                            </div>
                             <input type="button" name='action' id='search' class="btn btn-primary" value="搜索" />
						     <input type="button" name='action' id='subag' class="btn btn-primary" value="生成链接" />
						     <div class="DialogDiv"  style="display:none; ">
								  <div class="U-guodu-box">
								   <div>
								    <table width="100%" cellpadding="0" cellspacing="0" border="0" >
									  <tr><td  align="center"><img src="/public/images/load.gif"></td></tr>
									  <tr><td  valign="middle" align="center" >提交中,提交完成前请勿做其它操作！</td></tr>
								   </table>
								  </div>
							     </div>
						   </div>
                     </form>
         </div>
		
		<form class="J_ajaxForm" action="" method="post">
	    <div class="ibox-content">
		<table class="table table-striped table-bordered table-hover dataTables-example" style="margin-bottom:5px">
				<thead>
					<tr>
						<th>游戏渠道号</th>
						<th>渠道账号</th>
						<th>渠道名称</th>
						<th>游戏</th>
						<th>渠道类型</th>						
						
						<th>时间</th>
						<th>推广地址</th>
						<th>管理操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($agentgames)): foreach($agentgames as $key=>$vo): ?><tr>
							<td><?php echo ($vo["agentgame"]); ?></td>
							<td><?php echo ($vo['agentname']); ?></td>
							<td><?php echo ($vo['agentnicename']); ?></td>
							<td><?php echo ($games[$vo['appid']]); ?></td>
							<td><?php echo ($roles[$vo['roleid']]); ?></td>
							
							<td>
								<?php if(!empty($vo['update_time'])): echo (date('Y-m-d H:i:s',$vo["update_time"])); endif; ?>
							</td>
							<td>
							    <?php if(!empty($vo['agentid'])): $key = base64_encode($vo['gid'] ."_".$vo['agentid']); ?>
								    <?php echo ($downurl); ?>?key=<?php echo ($key); endif; ?>
							</td>
						
							<td>
							    <?php if(sp_auth_check(sp_get_current_admin_id(),'Subiospackage/editagent','and')): ?><a href="<?php echo U('Subiospackage/editagent',array('id'=>$vo['id']));?>">修改</a> |<?php endif; ?>
								
							</td>
						</tr><?php endforeach; endif; ?>
				</tbody>
				
			</table>
			<div class="pagination"><?php echo ($Page); ?></div>
			</div>
			<?php if(empty($agentgames)): ?><div class="ibox-content" style="text-align:center;font-size:18px;font-weight:bold;border:none">暂无相关内容</div><?php endif; ?>
		</form>
		<div class="theme-popover">
			 <div class="theme-poptit">
				  <a href="javascript:;" title="关闭" class="close">×</a>
				  <h5>分包信息完善</h5>
			 </div>

			 <div class="theme-popbod dform">
				   <form class="theme-signin" name="spform" id="spform" action="" method="post">
						<ol>
							 <li><strong>游戏：</strong><input class="form-control" type="text" id="subgame" name="subgame" size="20" style="color:#000" readonly/></li>
							 <li><strong>渠道账号：</strong><input class="form-control" type="text" id="subuser" name="subuser" size="20" style="color:#000" readonly/></li>

							 <!--<li id="rateli"><strong>分成比例：</strong>--><input class="form-control" type="hidden" name="rate" value="0.5" size="20" /></li>
							 <!--<li id="priceli"><strong>CPA单价：</strong>--><input class="form-control" type="hidden" name="cpa_price" value="0" size="20" /></li>
							 <li><input name='sugameid' id='sugameid' type="hidden" /></li>
							 <li><input name='suagentid' id='suagentid' type="hidden"/></li>

							 <li><input class="btn btn-primary" name='action' id='action' type="button" value=" 提交 "/></li>
						</ol>
				   </form>
			 </div>
			<input name='ajaxurl' id='ajaxurl' type="hidden" value="<?php echo U('Subiospackage/ajaxAgentgame');?>"/>
			<input name='spurl' id='spurl' type="hidden" value="<?php echo U('Subiospackage/subpackage');?>"/>
		</div>

		<div class="theme-popover-mask"></div>
		
	</div>
	<script src="/public/js/common.js"></script>
	<script src="/public/js/jquery.form.js"></script>
	<script src="/public/h/js/plugins/layer/layer.min.js"></script>
     
	<script>

        //eg 2
       /* $('#aid').on('click', function(){
            var that = this;
			
            var index = layer.tips('<img src="/Subpackage/code_img/url/aHR0cDovL2QuYWIxei5jbi91L01Td3hNVE09.html" />', '#aid', {
              tips: 3,
			  time: 0
            });
        });*/
        
		
		$(document).ready(function () {
			$('#subag').click(function(){
				var appid = $("#appid").val();//取得选中的值
				var appname = $("#appid").find("option:selected").text();//取选中的文本。
				
				var agentid = $("#agent_id").val();//取得选中的值
				var agent = $("#agent_id").find("option:selected").text();//取选中的文本。
				
				var ajaxurl =  $("#ajaxurl").val();
				$.ajax({
					url: ajaxurl,
					type: 'post',
					dataType: 'json',
					data: "agent_id="+agentid+"&appid="+appid,
					success:function(data){
						if (data.status){
							if(data.agexist == 1){
								$("#fm").submit();
							}else{
								if(data.type == 4){
									$("#rateli").remove();
								}else{
									$("#priceli").remove();
								}	
								$("#sugameid").attr("value",appid);
								$("#subgame").attr("value",appname);//填充内容 

								$("#suagentid").attr("value",agentid);
								$("#subuser").attr("value",agent);//填充内容 

								$('.theme-popover-mask').fadeIn(100);
								$('.theme-popover').slideDown(200);
							}
							
						} else {
							alert("请求错误");
						}
					}
				});
			})
			$('.theme-poptit .close').click(function(){
				$('.theme-popover-mask').fadeOut(100);
				$('.theme-popover').slideUp(200);

				$("#sugameid").attr("value",'');//清空内容
				$("#subgame").attr("value",'');
				$("#suagentid").attr("value",'');
				$("#subuser").attr("value",'');

			})
			
			$('#search').click(function(){
				$("#fm").submit();
			})
			
			$('.agentblack').click(function(){
				var url = $(this).data('url');
				var id = $(this).data('aid');
				var appid = $(this).data('appid');
				var agentgame = $(this).data('agentgame');
				
				 //询问框
                 layer.confirm('确定要冻结该渠道游戏端吗', {
                    btn: ['确定','取消'] //按钮
                 }, function(){
					  layer.closeAll();
					  var ii = layer.load();
					  $.ajax({
                         url: url,
					     type: 'post',
					     data: {agentid:id,appid:appid,agentgame:agentgame},
					     dataType: 'json',
					     success:function(data){
						    layer.close(ii);
						    if (data.success){
							   alert(data.msg);
							   window.location.reload();
						    } else {
							   alert(data.msg);
							   window.location.reload();//刷新当前页面.
						    }
					     }
					  });
                 });
				
			}) 
			
			$('.noblack').click(function(){
				var url = $(this).data('url');
				var id = $(this).data('aid');
				var appid = $(this).data('appid');
				
				 //询问框
                 layer.confirm('确定要解封该渠道游戏端吗', {
                    btn: ['确定','取消'] //按钮
                 }, function(){
					  layer.closeAll();
					  var ii = layer.load();
					  $.ajax({
                         url: url,
					     type: 'post',
					     data: {agentid:id,appid:appid},
					     dataType: 'json',
					     success:function(data){
						    layer.close(ii);
						    if (data.success){
							   alert(data.msg);
							   window.location.reload();
						    } else {
							   alert(data.msg);
							   window.location.reload();//刷新当前页面.
						    }
					     }
					  });
                 });
				
			});
			
			$("#action").click(function () {
				$('.theme-popover-mask').fadeOut(100);
				$('.theme-popover').slideUp(200);

				$("#BgDiv1").css({ 
					display: "block", height: $(document).height() 
				});
				var yscroll = document.documentElement.scrollTop;
				var screenx=$(window).width();
				var screeny=$(window).height();
				$(".DialogDiv").css("display", "block");
				 $(".DialogDiv").css("top",yscroll+"px");
				 var DialogDiv_width=$(".DialogDiv").width();
				 var DialogDiv_height=$(".DialogDiv").height();
				  $(".DialogDiv").css("left",(screenx/2-DialogDiv_width/2)+"px")
				 $(".DialogDiv").css("top",(screeny/2-DialogDiv_height/2)+"px")
				 $("body").css("overflow","hidden");

				var spurl = $("#spurl").val();

				var options = {
					url: spurl,
					type: 'post',
					dataType: 'json',
					data: $("#spform").serialize(),
					success:function(data){
						$("#BgDiv1").css({ 
						display: "none", height: $(document).height() });
						$(".DialogDiv").css("display", "none");
						 $("body").css("overflow","visible");
						
						if (data.success){
								alert(data.msg);
								window.location.reload();
						} else {
							    alert(data.msg);
								window.location.reload();//刷新当前页面.
						} 
					}
				};
				$.ajax(options);
				return false;
			});
			

			Wind.use('artDialog', function () {
				$('.J_ajax_updatebag').on('click', function (e) {
					e.preventDefault();
					var $_this = this,
						$this = $($_this),
						href = $this.prop('href'),
						msg = $this.data('msg');
					art.dialog({
						title: false,
						icon: 'question',
						content: '确定要更新吗？',
						follow: $_this,
						close: function () {
							$_this.focus();; //关闭时让触发弹窗的元素获取焦点
							return true;
						},
						ok: function () {
							$("#BgDiv1").css({ 
							display: "block", height: $(document).height() });
							var yscroll = document.documentElement.scrollTop;
							var screenx=$(window).width();
							var screeny=$(window).height();
							$(".DialogDiv").css("display", "block");
							 $(".DialogDiv").css("top",yscroll+"px");
							 var DialogDiv_width=$(".DialogDiv").width();
							 var DialogDiv_height=$(".DialogDiv").height();
							  $(".DialogDiv").css("left",(screenx/2-DialogDiv_width/2)+"px")
							 $(".DialogDiv").css("top",(screeny/2-DialogDiv_height/2)+"px")
							 $("body").css("overflow","hidden");

							$.getJSON(href).done(function (data) {
								$("#BgDiv1").css({ 
								display: "none", height: $(document).height() });
								$(".DialogDiv").css("display", "none");
								 $("body").css("overflow","visible");

								if (data.success) {
									alert(data.msg);
									if (data.referer) {
										location.href = data.referer;
									} else {
										reloadPage(window);
									}
								} else {
									//art.dialog.alert(data.info);
									alert(data.info);//暂时处理方案
								}
							});
						},
						cancelVal: '关闭',
						cancel: true
					});
				});

			});
			
		});
	</script>

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