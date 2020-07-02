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

	<!-- Data Tables -->
    <link href="/public/h/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    
    <link id="bscss" href="/public/simpleboot/css/bootstrap.min.css" rel="stylesheet">

</head>
<body class="J_scroll_fixed">
	<div class="wrap J_check_wrap">
	    <div class="ibox-content">
                        <form role="form" class="form-inline" method="post" action="<?php echo U('Admin/Pay/orderindex');?>">
						  <div class="ibox-content" style="border-width:0px 0;padding: 10px 20px 5px">
                            <div class="form-group">
                                <label class="font-noraml">订单号：</label>
                                <input type="text" placeholder="请输入订单号" id="exampleInputEmail2" name="orderid" value="<?php echo ($formget["orderid"]); ?>" class="form-control">
                            </div>
                            <!-- <div class="form-group">
                            	<label class="font-noraml">游戏：</label>
	                            <select class="select_2 chosen-select" name="gid" id="selected_id">
									<?php if(is_array($games)): foreach($games as $k=>$vo): $gid_select=$k==$formget['gid'] ?"selected":""; ?>
									<option value="<?php echo ($k); ?>"<?php echo ($gid_select); ?>><?php echo ($vo); ?></option><?php endforeach; endif; ?>
								</select>
							</div> -->
							
							<div class="form-group">
                                 <label class="font-noraml">游戏：</label>
                                 <div class="input-group">
                                    <select data-placeholder="选择游戏..." class="chosen-select" name="gid" id="gid" style="width:175px;" tabindex="2">
                                       <?php if(is_array($games)): foreach($games as $k=>$vo): $gid_select=$k==$formget['gid'] ?"selected":""; ?>
								          <option value="<?php echo ($k); ?>"<?php echo ($gid_select); ?>><?php echo ($vo); ?></option><?php endforeach; endif; ?>
                                   </select>
                                  </div>
                            </div>
					       

							<div class="form-group">
                                <label class="font-noraml">渠道号：</label>
                                <input type="text" placeholder="请输入渠道号" id="agentname" name="agentname" value="<?php echo ($formget["agentname"]); ?>" class="form-control">
                            </div>

							<div class="form-group">
                                <label class="font-noraml">渠道名称：</label>
                                <input type="text" placeholder="请输入渠道名称" id="agentnickname" name="agentnickname" value="<?php echo ($formget["agentnickname"]); ?>" class="form-control">
                            </div>
                          </div>
							
							<div class="ibox-content" style="border-width:0px 0;padding: 10px 20px 5px">
                                 <div class="form-group">
                                   <label class="font-noraml">渠道专员：</label>
                                 <div class="input-group">
                                    <select data-placeholder="选择账号..." class="chosen-select" name="parentid" id="selected_id" style="width:175px;" tabindex="2">
                                       <?php if(is_array($agents)): foreach($agents as $k=>$vo): $aid_select=$k==$formget['parentid']?"selected":""; ?>
							             <option value="<?php echo ($k); ?>" hassubinfo="true" <?php echo ($aid_select); ?>><?php echo ($vo); ?></option><?php endforeach; endif; ?>
                                     
                                    </select>
                                  </div>
                               </div>
                               
                            <div class="form-group">
                                <label  class="font-noraml">充值账号：</label>
                                <input type="text" placeholder="请输入充值账号" id="exampleInputPassword2" name="username" value="<?php echo ($formget["username"]); ?>" class="form-control">
                            </div>
                            
							  <div class="form-group">
                                 <label class="font-noraml">充值方式：</label>
                                 <div class="input-group">
                                    <select data-placeholder="选择充值方式..." class="chosen-select" name="payway" style="width:175px;" tabindex="2">
                                       <?php if(is_array($payways)): foreach($payways as $k=>$vo): $pw_select=$k===$formget['payway'] ?"selected":""; ?>
						                  <option value="<?php echo ($k); ?>"<?php echo ($pw_select); ?>><?php echo ($vo); ?></option><?php endforeach; endif; ?>
                                     
                                   </select>
                                 </div>
                              </div>
                     
					           <div class="form-group">
                                 <label class="font-noraml">充值状态：</label>
                                 <div class="input-group">
                                    <select data-placeholder="选择状态..." class="chosen-select" name="paystatus" style="width:175px;" tabindex="2">
                                       <?php if(is_array($paystatuss)): foreach($paystatuss as $k=>$vo): $ps_select=$k==$formget['paystatus'] ?"selected":""; ?>
						                  <option value="<?php echo ($k); ?>"<?php echo ($ps_select); ?>><?php echo ($vo); ?></option><?php endforeach; endif; ?>
                                   </select>
                                 </div>
                               </div>

							<div>
                            
							<div class="ibox-content" style="border-width:0px 0;padding: 10px 20px 5px">
							    <div class="form-group">
                                  <label  class="font-noraml">区服：</label>
                                  <input type="text" placeholder="请输入充值区服"  name="serverid" value="<?php echo ($formget["serverid"]); ?>" class="form-control">
                               </div>
	                            <div class="form-group" id="data_5">
	                               <label class="font-noraml">时间：</label>
	                               <div class="input-daterange input-group" id="datepicker">
	                                  <input type="text" class="input-sm form-control" name="start_time" value="<?php echo ((isset($formget["start_time"]) && ($formget["start_time"] !== ""))?($formget["start_time"]):''); ?>" />
	                                  <span class="input-group-addon">到</span>
	                                  <input type="text" class="input-sm form-control" name="end_time" value="<?php echo ($formget["end_time"]); ?>" />
	                               </div>
	                            </div>
							    <button class="btn btn-primary" name='submit' type="submit" value="七日">七日</button>
						        <button class="btn btn-primary" name='submit' type="submit" value="本月">本月</button>
						        <button class="btn btn-primary" name='submit' type="submit" value="搜索">搜索</button>
						        <button class="btn btn-primary" name='submit' type="submit" value="导出xls">导出xls</button>
							</div>
							
                        </form>
                    </div>
		
		<form class="J_ajaxForm" action="" method="post">
			<div class="ibox-content">
               <table class="table table-striped table-bordered table-hover dataTables-example" style="margin-bottom:5px">
				<thead>
					<tr>
						<th>订单号</th>
						<th>时间</th>
						<th>账号</th>
						<th>游戏</th>
						<th>游戏区服</th>	
						<th>角色</th>
						<th>金额</th>
                        <?php if( $israte == 1): ?><th>实际金额</th><?php endif; ?>
						<th>状态</th>								
						<th>充值方式</th>
						<th>渠道号</th>					
						<th>渠道名称</th>					
						<th>回调状态</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
				   <tr>
				      <td><span style='color:#f00'>汇总</span> </td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>	
						<td></td>
						<td><span style='color:#f00'><?php echo ($paysum["amount"]); ?></span> </td>
						
						<td></td>								
						<td></td>
						<td></td>					
						<td></td>					
						<td></td>
						<td></td>
				   </tr>
				   <?php if(is_array($orders)): foreach($orders as $key=>$vo): ?><tr>
				   <td><?php echo ($vo["orderid"]); ?></td>
				   <td><?php echo (date('Y-m-d  H:i:s',$vo["create_time"])); ?></td>
				   <td><?php echo ($vo["username"]); ?></td>
				   <td>
				   <?php if($vo['type'] == 1): echo ($vo["gamename"]); ?>(android)
				   <?php elseif($vo['type'] == 2): ?>
				   <?php echo ($vo["gamename"]); ?>(IOS)<?php endif; ?>
				   </td>
				   <td><?php echo ($vo["serverid"]); ?></td>	
				   <td><?php echo ($vo["roleid"]); ?></td>
				   <td><?php echo ($vo["amount"]); ?></td>
				  
				   <td>				
				      <?php if( 1 == $vo["status"] ): ?><span style='color:#f00'>成功</span> 
				      <?php elseif( 2 == $vo.status): ?>
				    	<span style='color:#00f'>失败</span>
				      <?php else: ?>
				    	<span style='color:#000'>待支付</span><?php endif; ?> 
				   </td>
							
				   <td><?php echo ($payways[$vo['paytype']]); ?></td>
				   <td>        
				            <?php if(empty($vo['agentid'])): ?>官包
							<?php else: ?>
								<?php echo ($vo["agentname"]); endif; ?>
				    </td>
				   <td><?php echo ($vo["agentnicename"]); ?></td>
				   <td>
				   <?php if( ( 1 == $vo["status"] ) and ( 1 == $vo["cptatus"] ) ): ?><span style='color:#f00'>回调成功</span> 
				    <?php elseif( ( 1 == $vo["status"] ) and ( 0 == $vo["cptatus"] ) ): ?>
				    	<span style='color:#00f'>回调失败</span>
				    <?php else: ?>
				    	<span style='color:#000'>待支付</span><?php endif; ?> 
				   <td>
					<?php if( ( 1 == $vo["status"] ) and ( 0 == $vo["cptatus"] ) ): ?><a href="<?php echo U('Pay/repairorder', array('orderid'=>$vo['orderid']));?>"
						class="J_ajax_dialog_btn" data-msg="您确定要补单吗？">补单</a></td><?php endif; ?> 
				   </td>					
				   </tr><?php endforeach; endif; ?>
                </tbody>
			</table>
			<div class="pagination"><?php echo ($Page); ?></div>
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
    
    <script src="/public/bootstrap-suggest/bootstrap-suggest.js"></script>
	<!-- <script src="/public/bootstrap-suggest/list.js"></script> -->
	<script src="/public/h/list.js"></script>
</body>
</html>