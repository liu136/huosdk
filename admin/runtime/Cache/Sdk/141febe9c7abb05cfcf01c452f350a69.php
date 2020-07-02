<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html >
<head >
    <meta charset="utf-8" >
    <!-- Set render engine for 360 browser -->
    <meta name="renderer" content="webkit" >
    <meta http-equiv="X-UA-Compatible" content="IE=edge" >
    <meta name="viewport" content="width=device-width, initial-scale=1" >

    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js" ></script >
    <![endif]-->

    <link href="/public/simpleboot/themes/<?php echo C('SP_ADMIN_STYLE');?>/theme.min.css" rel="stylesheet" >
    <link href="/public/simpleboot/css/simplebootadmin.css" rel="stylesheet" >
    <link href="/public/js/artDialog/skins/default.css" rel="stylesheet" />
    <link href="/public/simpleboot/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" >
    <style >
        .length_3 {
            width: 180px;
        }

        form .input-order {
            margin-bottom: 0px;
            padding: 3px;
            width: 40px;
        }

        .table-actions {
            margin-top: 5px;
            margin-bottom: 5px;
            padding: 0px;
        }

        .table-list {
            margin-bottom: 0px;
        }

        .select2-container .select2-dropdown {
            z-index: 99999999;
        }
    </style >
    <!--[if IE 7]>
    <link rel="stylesheet" href="/public/simpleboot/font-awesome/4.4.0/css/font-awesome-ie7.min.css" >
    <![endif]-->
    <script type="text/javascript" >
        //全局变量
        var GV = {
            DIMAUB : "/",
            JS_ROOT: "public/js/",
            TOKEN  : ""
        };
    </script >
    <!-- Le javascript
        ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/public/js/jquery.js" ></script >
    <script src="/public/js/wind.js" ></script >
    <script src="/public/simpleboot/bootstrap/js/bootstrap.min.js" ></script >
    <!--<script src="/public/3rd/datatable/js/jquery.dataTables.js" ></script >-->
    <!--<link href="/public/3rd/datatable/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" >-->
    <?php if(APP_DEBUG): ?><style >
            #think_page_trace_open {
                z-index: 9999;
            }
        </style ><?php endif; ?>

    <link rel="stylesheet" type="text/css" href="/public/select2/css/select2.min.css" />
    <script src="/public/select2/js/select2.min.js" ></script >
    <script type="text/javascript" src="/public/select2/js/i18n/zh-CN.js" ></script >
    <script >
        $(document).ready(function () {
            if($.isFunction($.select2)){
            $(".select_2").select2({
                language: "zh-CN"
            });
            }

//$('table').dataTable({"info": false,"paging": false,"lengthChange": false,"searching": false,"ordering": false});

            $(".form-search select").change(function () {
//        $("input[name='submit']").click();
                $("input[value='搜索']").click();
            });
        });

    </script >

    <script src="/public/3rd/layer/layer.js" ></script >
    <script src="/public/huoshu/share.js" ></script >
</head>
<body class="J_scroll_fixed" >
<div class="wrap js-check-wrap" >
    <ul class="nav nav-tabs" >
        <li class="active" ><a href="<?php echo U('Member/index');?>" >玩家管理</a ></li >
    </ul >

    <form class="well form-search" method="get" action="<?php echo U('Sdk/Member/index');?>" >
        <div class="search_type cc mb10" >
            <div class="mb10" >
                                <span class="mr20" >
                                    账号id：
                                    <input type="text" name="id" style="width: 150px;" value="<?php echo ($formget["id"]); ?>"
                                           placeholder="请输入帐号id..." >
                                    账号： 
                                    <input type="text" name="username" style="width: 150px;" value="<?php echo ($formget["username"]); ?>"
                                           placeholder="请输入帐号..." >
                                    Email： 
                                    <input type="text" name="email" style="width: 150px;" value="<?php echo ($formget["email"]); ?>"
                                           placeholder="请输入Email..." >
                                    手机号：
                                    <input type="text" name="mobile" style="width: 150px;" value="<?php echo ($formget["mobile"]); ?>"
                                           placeholder="请输入手机号..." >
                                    imei码：
                                    <input type="text" name="imei" style="width: 150px;" value="<?php echo ($formget["imei"]); ?>"
                                           placeholder="请输入IMEI码..." >
                                    映射游戏账号： 
                                    <input type="text" name="gameaccount" style="width: 150px;" value="<?php echo ($formget["gameaccount"]); ?>"
                                           placeholder="请输入帐号..." >
                                    查询映射账号：
                                    <input type="checkbox" name="is_account" value="1">
                                    <br /><br />                                
                                    注册游戏：
                                    <?php echo ($app_select); ?>
                                    注册渠道：
                                    <?php echo ($agent_select); ?>
                                    账号状态：
                                    <?php echo ($member_status_select); ?>
                                    <br /><br />
                                    注册时间：
                                    <input type="text" name="start_time" class="js-date" value="<?php echo ($formget["start_time"]); ?>"
                                           placeholder="开始时间..." style="width: 110px;" autocomplete="off" >-
                                    <input type="text" class="js-date" name="end_time" value="<?php echo ($formget["end_time"]); ?>"
                                           placeholder="结束时间..." style="width: 110px;" autocomplete="off" > &nbsp; &nbsp;
                                    <input type="submit" class="btn btn-primary" value="搜索" />
                                </span >
            </div >
        </div >
    </form >
    <form class="js-ajax-form" action="" method="post" >
        <table class="table table-hover table-bordered" >
            <thead >
            <tr >
                <th >账号id</th >
                <th >账号</th >
                <th >手机</th >
                <th >EMAIL</th >
                <th >注册IMEI码</th >
                <th >注册游戏</th >
                <th >注册渠道</th >
                <th >注册时间</th >
                <th >映射游戏账号</th >
                <th >状态</th >
                <?php switch($user_type): case "1": ?><th >管理操作</th ><?php break; endswitch;?>
            </tr >
            </thead >
            <tbody >

            <?php if(is_array($members)): foreach($members as $key=>$vo): ?><tr >
                    <td ><?php echo ($vo["id"]); ?></td >
                    <td ><?php echo ($vo["username"]); ?></td >
                    <td >
                        <?php if(empty($vo['mobile'])): ?>该用户还未绑定手机
                            <?php else: ?>
                            <?php echo ($vo["mobile"]); endif; ?>
                    </td >
                    <td >
                        <?php if(empty($vo['email'])): ?>该用户还未绑定邮箱
                            <?php else: ?>
                            <?php echo ($vo["email"]); endif; ?>
                    </td >
                    <td ><?php echo ($vo["imei"]); ?></td >

                    <td >
                        <?php if(0 == $vo['app_id']): ?>官网注册
                            <?php else: ?>
                            <?php echo ($games[$vo['app_id']]); endif; ?>
                    </td >
                    <td >
                        <?php if(0 == $vo['agent_id']): ?>官方注册
                            <?php else: ?>
                            <?php echo ($agents[$vo['agent_id']]); endif; ?>
                    </td >
                    <td ><?php echo (date('Y-m-d H:i:s',$vo["reg_time"])); ?></td >
                    <td ><?php echo ($vo["gameaccount"]); ?></td>
                    <td ><?php echo ($memstatus[$vo['status']]); ?></td >
                            <td >
                                <a href='<?php echo U("Sdk/Member/edit",array("id"=>$vo["id"]));?>' >详情</a > |
                                <?php if($vo['status'] == 2 || $vo['status'] == 1): ?><a href="<?php echo U('Sdk/Member/ban',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn"
                                       data-msg="您确定要冻结此用户吗？" >冻结</a >
                                    <?php else: ?>
                                    <a href="<?php echo U('Sdk/Member/cancelban',array('id'=>$vo['id']));?>"
                                       class="js-ajax-dialog-btn" data-msg="您确定要解封此用户吗？" >解封</a ><?php endif; ?>
                            </td >
                </tr ><?php endforeach; endif; ?>
            </tbody >
        </table >
        <div class="pagination" ><?php echo ($Page); ?></div >
        <div >共<?php echo ($total_rows); ?>个玩家</div >
    </form >
</div >
<script src="/public/js/common.js" ></script >
</body >
</html>