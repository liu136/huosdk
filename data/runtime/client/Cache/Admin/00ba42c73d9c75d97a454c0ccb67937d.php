<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>手游运营管理后台</title>
<link href="/public/css/style2.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div class="top">
  <ul>
    <li><a href="#" class="hover" target="_blank">手游平台</a></li>
    <li><a href="#">商务合作</a></li>
    <li><a href="#">首页</a></li>
  </ul>
</div>

<div class="main">
  <div class="denglu">
   <form method="post" name="login" action="<?php echo U('public/dologin');?>" autoComplete="off" class="js-ajax-form">
    <div class="dlk">
      <table width="292" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="76" colspan="3"></td>
        </tr>
        <tr>
          <td width="65" style="font-size:14px;">账号：</td>
          <td colspan="2"><input id="js-admin-name" required name="username" type="text" placeholder="<?php echo L('USERNAME_OR_EMAIL');?>" title="<?php echo L('USERNAME_OR_EMAIL');?>" tabindex="1" autocomplete="off" class="dlinput" /></td>
        </tr>
        <tr>
          <td height="16" colspan="3"></td>
        </tr>
        <tr>
          <td>密码</td>
          <td colspan="2"><input id="admin_pwd" type="password" required name="password" placeholder="<?php echo L('PASSWORD');?>" title="<?php echo L('PASSWORD');?>" maxlength="16" tabindex="2" class="dlinput" /></td>
        </tr>
        <tr>
          <td height="16" colspan="3"></td>
        </tr>
        <tr>
          <td>验证码</td>
          <td width="100"><input type="text" name="verify" placeholder="<?php echo L('ENTER_VERIFY_CODE');?>" maxlength="5" id="code" tabindex="3" class="dlinput" style="width:90px;" /></td>
          <td width="127"><?php echo sp_verifycode_img('length=4&font_size=23&width=180&height=50&use_noise=1&use_curve=0','style="cursor: pointer;" title="点击获取"');?></td>
        </tr>
        <tr>
          <td height="16" colspan="3"></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2"><input type="submit" value="登 录" class="loginbtn" /></td>
        </tr>
        <!--<tr>
          <td>&nbsp;</td>
          <td colspan="2"><table width="224" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td height="24"><a href="#none" class="mm">忘记登录密码？</a></td>
              <td align="right"><a href="#none" class="zc" style="font-size:13px;">立即网上注册</a></td>
            </tr>
          </table></td>
        </tr>-->
      </table>
    </div>
	</form>
  </div>
</div>

<div class="footer">Copyright &copy; 2017  All Rights Reserved　版权所有 </div>
<script>
var GV = {
	DIMAUB: "",
	JS_ROOT: "/public/js/",//js版本号
	TOKEN : ''	//token ajax全局
};
</script>
<script src="/public/js/wind.js"></script>
<script src="/public/js/jquery.js"></script>
<script type="text/javascript" src="/public/js/common.js"></script>
<script>
;(function(){
	document.getElementById('js-admin-name').focus();
})();
</script>

</body>
</html>