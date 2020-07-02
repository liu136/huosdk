<!DOCTYPE html>
<html>
    <?php
    //true开启//false关闭随机参数
    $v = false ? '?v='.time() : '';
    ?>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0, width=device-width" />
        <meta name="format-detection" content="telephone=no">
        <meta name="renderer" content="webkit">
        <title>SDK</title>
        <!--[if !IE]>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <!--<![endif]-->
        <link rel="stylesheet" type="text/css" href="index/css/style0605.css<?php echo $v?>"  />
        <link rel="stylesheet" type="text/css" href="index/css/form0605.css<?php echo $v?>"  />
        <link rel="stylesheet" type="text/css" href="index/css/play0605.css<?php echo $v?>"  />
        <link rel="stylesheet" type="text/css" href="index/css/swiper.min.css<?php echo $v?>" />
        <link rel="stylesheet" href="index/css/SuspendedBall.h5.css<?php echo $v?>" type="text/css" media="screen" /><!-- 悬浮球 -->
        <script type="text/javascript" src="index/js_lib/JsLoader.js"></script>
        <link href="index/css/animate.min.css<?php echo $v?>" rel="stylesheet">
        <script src="index/js_lib/fontrem.js<?php echo $v?>" type="text/javascript" charset="utf-8"></script>

        <style type="text/css">
            .index_bg {
                background-image: url("index/img/img0605/index.png");
            }
        </style>
    </head>

    <body>
        <div class="wrapper index_bg">
            <div class="winpop_main" id="gameframe" style="display:none">
               <iframe src="" width="100%" height="100%"></iframe><!-- https://apizrzs.h5.91xy.com/bufan/login.php?token=6cda5d6812591381732423d0b046f9c7&appid=41&time=1538040280&sign=1d90876ba3434b1a7f393ada2cf19adb -->
            </div>
            <div class="winpop_main" id="userInfoDiv">
                <div id="info" style="display: none"></div>
                <div class="login_popup" id="loginWinpop">
                    <!--登录游戏-->
                    <div class="public_popup">
                        <div class="public_popup_body">
                            <div class="public_popup_title">用户登录 <div id="CloseUserIcon" class=" this_icon" style="margin-left:2.8rem"></div></div>
                            <div class="public_popup_main">
                                <div class="public_popup_form">
                                    <ul>
                                        <li class="public_this_input">
                                            <!-- <i class="phone_icon this_icon"></i> -->
                                            <input type="text" class="nopadding" placeholder="请输入手机号/账号" name="" id="login_username" value="" />
                                        </li>
                                        <li class="public_this_input">
                                           <!--  <i class="pwd_icon this_icon"></i> -->
                                            <input type="password" class="login_pwd nopadding" placeholder="请输入密码" name="" id="login_password" value="" />

                                            <div class="see_icon this_icon index_see"></div>

                                             <div class="forget_passwd" id="findPwdBtn">
	                                             <span class="forget_icon this_icon" style=""></span>
	                                             <span class="forget_text" >忘记密码</span>
                                             </div>
                                            
                                        </li>
                                    </ul>
                                    <!-- <div class="public_popup_other">
                                        <a href="javascript:;" class="sign_btn" id="signBtn">立即注册</a>
                                        <a href="javascript:;" id="findPwdBtn">找回密码</a>
                                    </div> -->
                                    <div class="public_popup_btn_box">
                                        <a href="javascript:" id="mlp_login" class="public_popup_btn">进入游戏</a>
                                      <!--   <a href="javascript:;" id="guestLoginBtn" class="public_popup_btn tourist_btn">游客登录</a> -->
                                    </div>
                                </div>
                                <div class="public_popup_footer">
                                    <div class="public_popup_icon_little" >
                                    	<span  id="qqLoginBtn" ><img src="index/img/img0605/qq.png"/></span>
	                                	<!-- <a href="javascript:;" id="lp_weibo_login"><img src="index/img/img0605/weixin.png" /></a> -->
                            			
                            		</div>
                            		<div class="public_popup_icon_back">
                            			<div id="signBtn">快速注册<div class="back this_icon" ></div></div>
                            		</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 游客 登陆账号信息-->
                <div class="public_popup user_popup" id="accountInfoPop" style="display: none;">
                    <div class="public_popup_body">
                        <div class="public_popup_title">你的账号信息</div>
                        <div class="public_popup_main">
                            <div class="public_popup_form">
                                <ul>
                                    <li class="public_this_input">
                                        <i class="phone_icon this_icon"></i>
                                        <input type="tel"  name="" id="aip_username" value="" readonly="readonly"/>
                                    </li>
                                    <li class="public_this_input">
                                        <i class="pwd_icon this_icon"></i>
                                        <input type="text"  name="" id="aip_password" value="" readonly="readonly"/>
                                    </li>
                                </ul>
                                <div class="public_popup_btn_box">
                                    <a href="javascript:" onClick="location.reload()" class="public_popup_btn public_popup_long_btn">进入游戏</a>
                                </div>
                            </div>
                            <div class="other_hint">
                                请截屏保留账号密码，
                                <a href="javascript:" id="aip_bindmobile">绑定手机</a>方便找回哦~
                            </div>
                        </div>
                    </div>
                    <div class="public_code_box">
                        <img src="index/img/img0605/code.jpg" tppabs="http://h5-img.binglue.com/v3/img/img0605/code.jpg" />
                    </div>
                    <div class="public_popup_footer">
                       <div>
	            			<span id="qqLoginBtn" ><img src="index/img/img0605/login_icon_qq.png"/></span>
	                    	<a href="javascript:" id="lp_weibo_login"><img src="index/img/img0605/login_icon_sina.png" /></a>
	            		</div>
	            		<div>
	            			返回登录<div class="back this_icon" id="phoneBack" ></div>
	            		</div>
                    </div>
                </div>

                <!--用户注册-->
                <div class="public_popup user_popup" id="signUserWinpop" style="display: none;">
                    <div class="public_popup_body">
                        <div class="public_popup_title">用户注册
                            
                        </div>
                        <div class="public_popup_main">
                            <div class="public_popup_form">
                                <ul>
                                    <li class="public_this_input">
                                        <i class="phone_icon this_icon"></i>
                                        <input type="tel" placeholder="账号" name="" id="reg_username_account" value="" />
                                    </li>
                                    <li class="public_this_input">
                                        <i class="pwd_icon this_icon"></i>
                                        <input type="password" class="login_pwd" placeholder="密码" name="" id="reg_password_account" value="" />
                                        <div class="see_icon this_icon"></div>
                                    </li>
                                </ul>
                                <div class="public_popup_btn_box">
                                    <a href="javascript:" id="rp_reg_ok" class="public_popup_btn public_popup_long_btn">注册</a>
                                </div>
                                <div class="public_popup_footer">
                            		<div>
                            			<span id="signPhoneBtn" >手机注册</span>
                            		</div>
                            		<div>
                            			<div class="BackLoginUser">返回登录<div class="back this_icon" ></div></div>
                            		</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--手机注册-->
                <div class="public_popup user_popup" id="signPhoneWinpop" style="display: none;">
                    <div class="public_popup_body">
                        <div class="public_popup_title">手机注册
                            
                        </div>
                        <div class="public_popup_main">
                            <div class="public_popup_form">
                                <ul>
                                    <li class="public_this_input">
                                        <i class="phone_icon this_icon"></i>
                                        <input type="tel" placeholder="手机号码" name="" id="reg_username" value="" />
                                    </li>
                                    <li class="public_this_input">
                                        <i class="pwd_icon this_icon"></i>
                                        <input type="password" class="login_pwd" placeholder="新密码" name="" id="reg_password" value="" />
                                        <div class="see_icon this_icon"></div>
                                    </li>
                                    <li class="public_this_input">
                                        <i class="code_icon this_icon"></i>
                                        <input type="tel" placeholder="验证码" name="" id="reg_code" value="" />
                                        <a href="javascript:" id="rp_getcode" class="public_code_btn">获取验证码</a>
                                    </li>
                                </ul>
                                <div class="public_popup_btn_box">
                                    <a href="javascript:" id="rp_reg_phone" class="public_popup_btn public_popup_long_btn">注册</a>
                                </div>
                                <div class="public_popup_footer">
                            		<div>
                            			<span id="UserResgister" >用户注册</span>
                            		</div>
                            		<div>
                            			<div class="BackLogin">返回登录<div class="back this_icon" ></div></div>
                            		</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--绑定手机-->
                <div class="public_popup user_popup" id="bindMobilePop" style="display: none;">
                    <div class="public_popup_body">
                        <div class="public_popup_title">绑定手机
                            <a href="javascript:" class="back this_icon" id="bindPhoneBack"></a>
                        </div>
                        <div class="public_popup_main">
                            <div class="public_popup_form">
                                <ul>
                                    <li class="public_this_input">
                                        <i class="phone_icon this_icon"></i>
                                        <input type="tel" placeholder="请输入手机号码" name="" id="bind_phone_num_tx" value="" />
                                    </li>
                                    <li class="public_this_input">
                                        <i class="code_icon this_icon"></i>
                                        <input type="tel" placeholder="验证码" name="" id="bind_phone_sms_tx" value="" />
                                        <a href="javascript:" id="bmp_getcode" class="public_code_btn">获取验证码</a>
                                    </li>
                                </ul>
                                <div class="public_popup_btn_box">
                                    <a href="javascript:" id="bmp_ok" class="public_popup_btn public_popup_long_btn">确认</a>
                                </div>
                                <div class="public_popup_footer">进入游戏意味着您已同意本公司的
                                    <a href="#">《用户条例》</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--找回密码-->
                <div class="public_popup user_popup" id="findPwdWinpop" style="display: none;">
                    <div class="public_popup_body">
                        <div class="public_popup_title">找回密码
                        </div>
                        <div class="public_popup_main">
                            <div class="public_popup_form">
                                <ul>
                                    <li class="public_this_input">
                                        <i class="phone_icon this_icon"></i>
                                        <input id="fwp_phone" type="tel" placeholder="请输入手机号码"   value="" />
                                    </li>
                                    <li class="public_this_input">
                                        <i class="pwd_icon this_icon"></i>
                                        <input id="fwp_pwd" type="password" class="login_pwd" placeholder="请输入新密码"   value="" />
                                        <div class="see_icon this_icon"></div>
                                    </li>
                                    <li class="public_this_input">
                                        <i class="code_icon this_icon"></i>
                                        <input id="fwp_code" type="tel" placeholder="验证码" name=""  value="" />
                                        <a id="fwp_getcode" href="javascript:" class="public_code_btn">获取验证码</a>
                                    </li>
                                </ul>
                                <div class="public_popup_btn_box">
                                    <a href="javascript:" id="fwp_ok" class="public_popup_btn public_popup_long_btn">确认</a>
                                </div>
                                <div class="public_popup_footer">
                                   <div></div>
                                   <div>
                                        <div id="findPwdBack" >返回登录<div class="back this_icon BackLogin"  ></div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <!--<div class="gray" style="display: none;"></div>-->
                <div class="my_code_save" id="visitorLoginDiv" style="display: none;">
                    <div class="my_code_save_img"><img src="index/img/save_tu.png" tppabs="http://h5-img.binglue.com/v3/img/save_tu.png" alt=""></div>
                    <p>游客提示：保存账号方便下次登录游戏。</p>
                    <a href="javascript:" id="visitorBackB">立即开玩</a>
                    <!--<div class="btn_con"><a href="javascript:;" class="login_btn01" id="copyVisitor" data-clipboard-text="">复制帐密</a><a href="javascript:;" id="visitorBackB" class="login_btn02">完成并登录</a></div>-->
                </div>

                <!-- 绑定手机提示 -->
                <div class="thisWinPop tips_winpop2" id="bangPhoneNoticeDiv" style="display: none;">
                    <div class="thisWinPop_close">
                        <a href="javascript:" id="bandohoneBackA"></a>
                    </div>
                    <div class="thisWinPop_tit">提示</div>
                    <div class="tips_con">
                        <p>您的帐号尚未绑定手机，忘记密码或者被盗时将无法通过手机找回密码！</p>
                    </div>
                    <div class="btn_con">
                        <a href="javascript:" id="bandohoneBackB" class="binding_btn01">稍后绑定</a>
                        <a href="javascript:" id="bindPhoneC" class="binding_btn02">立即绑定</a>
                    </div>
                    <div class="checkbox-box checkbox-box-1">
                        <input type="checkbox" id="notishiBindPhone">今天不再提示
                    </div>
                </div>

               

            </div>

            <div class="wrong" id="notice_wrong">此处显示提示信息</div>
            <div class="background-content" id="playGameIframe">
                <iframe id="frameGame" frameborder="1"></iframe>
            </div>
            <div class="winpop_main" id="payframe" style="display:none">
                <div style="height:0.96rem;width:7.5rem;background-color:#81b0f1"><div id="closepay" style="height:0.48rem;width:0.48rem;position:absolute;left:6.5rem;top:0.24rem;"></div></div>
                <iframe src="" name="payWindow"  id="payWindow" style="width:7.5rem;height:100%;"></iframe>
            </div>

            <!-- 加载网页 -->
            <div class="winpop_main" id="Netframe" style="display:none">
                <div id="WindowViewCloseMenu" style="height:0.96rem;auto;background-color:#81b0f1;display:none"><div id="closeWindowView" style="height:0.48rem;width:0.48rem;position:absolute;right:0.5rem;top:0.24rem;"></div></div>
                <iframe src="" name="payWindow"  id="payWindow" style="width:100%;height:100%;"></iframe>
            </div>


            <!-- 微信支付 -->
            <div class="thisWinPop tips_winpop2" id="weixinPayDiv" style="display: none;z-index:9999" >
                <div class="thisWinPop_close">
                    <a href="javascript:" id="weixinpay_close"></a>
                </div>
                <div class="thisWinPop_tit">提示</div>
                <div class="tips_con">
                    <p style="text-align:center">请点击确认按钮前往微信支付</p>
                </div>
                <div class="btn_con" >
                    <a href="" id="weixinpay_url" class="weixinpay_btn" >确认</a>
                </div>
            </div>

             <!-- 选择登录账号 -->
            <div class="thisWinPop tips_winpop3" id="loadaccountPayDiv" style="display: none;z-index:9999;height: auto;" >
                <div class="thisWinPop_close">
                    <a href="javascript:location.reload();"></a>
                </div>
                <div class="thisWinPop_tit">提示:</div>
                <div class="tips_con">
                    <p style="text-align:center">存在绑定多个账号,请选择账号登录</p>
                </div>
                <div class="btn_con_select" id="loadAccountSelect">
                    <!-- <a style="text-align:center">xxxx</a> -->
                </div>
            </div>
        </div>
        
        <!--  提示模板 -->
        <div class="gray" id="boxA2"></div>
        <div class="tips" style="display:none;" id="noticeDiv">验证失败</div>


         <!-- 隐藏悬浮窗区域 -->
        <div class="ball_close">拖到此处隐藏</div>

        <!-- 侧边栏 -->
        <div class="overlay fadeInLeft animated" id="conDiv" style="display:none">
            <!-- <div class="rt_bar_main" id="rtBarImg">
                <ul>
                    <li>
                        <a  href="javascript:;" target="_self" class="rt_bar_close">收起侧栏</a>
                    </li>
                    <li>
                        <a href="javascript:;"  target="_self" class="rt_bar_refresh">刷新游戏</a>
                    </li>
                    <li>
                        <a id="gzGzgA" target="_self" href="javascript:;" class="rt_bar_service">客服</a>
                    </li>
                    <li>
                        <a href="javascript:;" target="_self" class="rt_bar_home">退出游戏</a>
                    </li>
                </ul>
            </div> -->
            <div class="lt_bar" id="ballConDiv">
                <div class="userInfo">
                    <dl id="ballUserInfoA">
                        <!--头像-->
                        <dt>
                            <img src="index/img/img0605/ball.png">
                        </dt>
                        <dd>
                            <p  class="user_name">个人中心</p>
                            <p id='account' data-clipboard-text=""></p>
                            <p id='uid'><span  data-clipboard-text="">UID:</span></p>
                        </dd>
                    </dl>
                    <!-- <a target="_self" href="javascript:;" id="changeUser" class="userInfo_btn">切换账号</a> -->
                    <a target="_self" href="javascript:" id="rtBarImg" class="userInfo_btn">收起侧栏</a>
                    <div class="bind_box">
                        <ul>
                            <li class="rt_bar_refresh">
                                <a href="javascript:" ><i class="this_icon update_game_icon"></i><span>刷新游戏</span></a>
                            </li>
                            <li  id="changeUser">
                                <a href="javascript:" ><i class="this_icon change_user_icon"></i><span>切换账号</span></a>
                                
                            </li>
                        </ul>
                        <ul>
                            <li class="rt_bar_home">
                                <a href="javascript:" ><i class="this_icon logout_game_icon"></i><span>退出游戏</span></a>
                            </li>
                            <li id="bindPhoneA" >
                                <a href="javascript:" ><i class="this_icon bind_phone_icon"></i><span>绑定手机</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="userInfo_nav">
                    <ul>
                        <li class="tab_nav01">
                            <a href="javascript:" target="_self" class="this no_tab">客服</a>
                        </li>
                        <li class="tab_nav02">
                            <a href="javascript:" target="_self" class="no_tab">礼包</a>
                        </li>
                        <li class="tab_nav03">
                            <a href="javascript:" target="_self" class="no_tab">已领取礼包</a>
                        </li>
                    </ul>
                </div>
                <div class="lt_bar_main overlay-content">

                    <!-- 客服 -->
                    <div class="no_con">
                        <div class="user_game_list ugl">
                           <!-- <div class="kefu-title">客服反馈</div> -->
                           <div class="kefu-content">
                           问题请加客服QQ xxx
                           </div>
                           <div class="kefu-qq">
                                <div data-clipboard-text="">QQ:xxx</div>
                           </div>
                        </div>
                    </div>
                    <!-- 礼包 -->
                    <div class="no_con" style="display: none;">
                        <div class="user_lb_list clearfix user_all_gift_list">
                                <!-- <dl da-libao_name="烈火星辰微信加群礼" da-libao_id="271">
                                    <dt>
                                        <h4>烈火星辰微信加群礼</h4>
                                        <p></p>
                                        <p>礼包内容：羽毛*20、强化石*100、经脉丹*10金币*1000000、砖石200</p>
                                    </dt>
                                    <dd>
                                        <a href="javascript:;" target="_self" class="start gift_btn">领取</a>
                                    </dd>
                                </dl> -->
                        </div>
                    </div>
                    <!-- 已领取礼包 -->
                    <div class="no_con" style="display:none">
                        <div class="user_lb_list clearfix user_my_gift_list">
                            <!-- <dl>
                                <dt><a href="javascript:;" data-url="/wap/gameplay/fkdgs" target="_self"><img src=""></a></dt>
                                <dd>
                                    <h4><a href="javascript:;" data-url="/wap/gameplay/fkdgs" target="_self">疯狂打怪兽</a></h4>
                                    <p><span>205778人玩过</span><i>|</i><span>挂机放置</span></p>
                                </dd>
                                <dd>
                                    <a href="javascript:;" data-url="/wap/gameplay/fkdgs" target="_self" class="start">开始玩</a>
                                </dd>
                            </dl> -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="lt_bar" id="ballUserinfoDiv" style="display:none">
                <div class="user_header">
                    <p>个人中心</p>
                    <a href="javascript:" target="_self" class="back" id="goBackConA"></a>
                </div>
                <div class="user_main">
                    <ul>
                        <li>
                            <a href="javascript:" target="_self" id="chgOldPwdA">
                                <span class="modify">修改密码</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:" target="_self" id="bindPhoneB">
                                <span class="modify">绑定手机</span>
                            </a>
                        </li>
                       <!--  <li>
                            <a href="javascript:" target="_self" id="bindEmail">
                                <span class="modify">绑定邮箱</span>
                            </a>
                        </li> -->
                        <!-- <li class="no_bindrealname state1" id="bindRealnameLiNo">
                            <a id="bindRealnameA" href="javascript:" target="_self">
                                <span class="shiming" >实名认证<i>(未实名认证)</i></span>
                            </a>
                        </li>
                        <li class="no_bindrealname state2" id="bindRealnameLiOk" style="display:none">
                            <span class="shiming" >实名认证<i>(已实名认证)</i></span>
                        </li> -->
                    </ul>
                </div>
            </div>

            <!---修改密码-->
            <div class="lt_bar" id="chgOldPwdDiv" style="display:none">
                <div class="user_header">
                    <p>修改密码</p>
                    <a href="javascript:" id="chgOldPwdBackA" target="_self" class="back"></a>
                </div>
                <div class="user_main">
                    <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <input id="chgOldPwd_oldpassword"  type="password" placeholder="请输入原始密码" >
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input id="chgOldPwd_password"  type="password" placeholder="请输入新密码">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  id="chgOldPwd_repassword" type="password" value="" placeholder="请输入确认密码">
                            </td>
                        </tr>
                    </table>
                </div>
                <a id="chgOldPwdOper" target="_self" href="javascript:" class="cfm">确认</a>
                <div class="notice_tips" style="color:red">密码必须为6-16位数字、字母</div>
                <div class="notice_tips">注：修改密码后需要重新登录</div>
            </div>
            <div class="lt_bar" id="bindUserDiv" style="display:none">
                <div class="user_header">
                    <p>绑定邮箱</p>
                    <a href="javascript:" id="bindUserBackA" target="_self" class="back"></a>
                </div>
                <div class="user_tip">绑定成功后,可使用账号密码登录</div>
                <div class="user_main">
                    <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <input id="set_account" maxlength="11" type="text" placeholder="账号由4~20位数字、字母或下划线组成">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input id="set_password"  type="password" placeholder="设置登录密码（6~12个字符）">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input id="set_repassword"  type="password" placeholder="确认登录密码（6~12个字符）">
                            </td>
                        </tr>
                    </table>
                </div>
                <a id="setAccountAndPwd" href="javascript:" target="_self" class="cfm">确认</a>
            </div>
            <div class="lt_bar" id="bindPhoneDiv" style="display:none">
                <div class="user_header">
                    <p>绑定手机</p>
                    <a href="javascript:" id="bindPhoneBackA" target="_self" class="back"></a>
                </div>
                <div class="user_tip">绑定成功后,可使用手机密码登录</div>
                <div class="user_main">
                    <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <input  id="bindPhone_phone" maxlength="11"  type="text" placeholder="请输入手机号码">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  id="bindPhone_phone_old"  is_bind_phone="1" maxlength="11"  type="text" placeholder="请输入原手机号码(首次绑定请留空)">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="position:relative;">
                                    <input id="bindPhone_code" maxlength="6"  type="text" value="" placeholder="输入验证码">
                                    <div class="code">
                                        <a  id="bindPhoneCodeGet" target="_self" href="javascript:">获取验证码</a><span style="display:none;">重新获取验证码60s</span></div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <a id="bindPhoneOper" href="javascript:" target="_self" class="cfm">确认</a>
            </div>

            <div class="lt_bar" id="bindRealnameDiv" style="display:none">
                <div class="user_header">
                    <p>实名认证</p>
                    <a href="javascript:" id="bindRealnameBackA" target="_self" class="back"></a>
                </div>
                <div class="user_main">
                    <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <input maxlength="20"  type="text" id="bind_realname" placeholder="请输入真实姓名">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input maxlength="18"  type="text" id="bind_idcard" placeholder="请使用真实身份信息认证">
                            </td>
                        </tr>

                    </table>
                </div>
                <a id="bindRealnameOper" href="javascript:" target="_self" class="cfm">确认</a>
            </div>
        </div>



        <script type="text/javascript" src="index/js_lib/jquery-1.9.1.min.js" ></script>
        <script type="text/javascript" src="index/js_lib/PublicJS.js" ></script>
        <script src="index/js/gn_h5_sdk.js<?php echo $v?>"  charset="utf-8"></script>
        <script src="index/js/game_user.js<?php echo $v?>"  type="text/javascript"></script>
        <script src="index/js_lib/SuspendedBall.h5.js<?php echo $v?>"  type="text/javascript" charset="utf-8"></script>
        <script src="index/js_lib/clipboard.min.js"  type="text/javascript" charset="utf-8"></script>
        <!-- <script src="index/js/login.js-v=3"  type="text/javascript" charset="utf-8"></script> -->
        <script src="index/js/userlogin.js<?php echo $v?>" type="text/javascript" charset="utf-8"></script>   
    </body>
</html>
<script type="text/javascript">
   <?php
         $devicetype = base64_encode( 'web');
        if(isset($_GET['sign'])){
            if($_GET['sign'] == md5('a='.$_GET['appid'].'t='.$_GET['time'].'k=devicetype')){
                $devicetype = $_GET[$_GET['sign']];
            }else{
                echo 'alert("初始化错误，参数非法!")';
            }
        }

   ?>


   sdkModule.initGnSdk("<?=$_GET['appid']?>","<?=$devicetype?>");
</script>
