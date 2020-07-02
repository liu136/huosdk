<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件
// 火树玩家进过加密返回
use think\Loader;
use think\Session;

function sp_password($pw, $auth_code = '') {
    if (empty($auth_code)) {
        $auth_code = \think\Config::get('config.HSAUTHCODE');
    }
    $result = "###".md5(md5($auth_code.$pw));
    return $result;
}

function member_password($pw, $auth_code = '') {
    if (empty($auth_code)) {
        $auth_code = \think\Config::get('config.HSAUTHCODE');
    }
    $result = md5(md5($auth_code.$pw).$pw);
    return $result;
}

/**
 * 判断是否是手机号码
 *
 * @param $mobile
 *
 * @return bool
 */
function isMobileNumber($mobile) {
    if (preg_match("/^1[34578][0-9]{9}$/", $mobile)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 判断是否是邮箱
 *
 * @param $email
 *
 * @return mixed
 */
function isEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function sendMsg($user_phone) {
    $limit_time = 120;  //设定超时时间 2min
    $checkExpressions = "/^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/";
    if (false == preg_match($checkExpressions, $user_phone)) {
        $result['status'] = 3;
        $result['msg'] = $user_phone."请填写正确手机号码";
        return $result;
    }
    $sess_mobile = Session::get('mobile');
    $sess_sms_time = Session::get('sms_time');
    if (isset($sess_mobile) && $sess_mobile == $user_phone
        && $sess_sms_time + $limit_time > time()
    ) {
        $result['status'] = 2;
        $result['msg'] = "已发送过验证码";
        return $result;
    }
    //$_SESSION['sms_time'] = time();
    //$_SESSION['mobile'] = $user_phone;
    Session::set('sms_time', time());
    Session::set('mobile', $user_phone);
    //$rsdata = alidayuSend($user_phone);
    $rsdata = julongSend($user_phone);
    //$rsdata = lanzsend($user_phone);
    if (0 == $rsdata['code']) {
        //if (0 != $rsdata['code']) {
        //发送成功
        $result['status'] = 1;
        $result['msg'] = $rsdata['msg'];
        return $result;
    } else {
        //短信发送失败
        $result['status'] = 4;
        $result['msg'] = $rsdata['msg'];
        return $result;
    }
}

/**
 * @param        $mobile
 * @param string $smstemp
 * @param string $product
 *
 * @return mixed
 */
function alidayuSend($mobile, $smstemp = '', $product = '') {
    Loader::import('taobao.TopSdk');
    Loader::import('taobao.top.TopClient');
    Loader::import('taobao.top.request.AlibabaAliqinFcSmsNumSendRequest');
    $alidayu = dirname(dirname(APP_PATH))."/conf/sms/alidayu.php";
    //获取阿里大鱼配置信息
    if (file_exists($alidayu)) {
        $dayuconfig = include $alidayu;
    } else {
        $dayuconfig = array();
    }
    if (empty($dayuconfig)) {
        return false;
    }
    if (empty($product)) {
        $product = $dayuconfig['PRODUCT'];
    }
    if (empty($smstemp)) {
        $smstemp = 'SMSTEMPAUTH';
    }
    $sms_code = rand(1000, 9999);   //获取随机码
    //$_SESSION['sms_code'] = $sms_code;
    Session::set('sms_code', $sms_code);
    $content = array(
        "code"    => "".$sms_code,
        "product" => $product
    );
    $c = new TopClient();
    $c->appkey = $dayuconfig['APPKEY'];
    $c->secretKey = $dayuconfig['APPSECRET'];
    $req = new AlibabaAliqinFcSmsNumSendRequest();
    $req->setExtend($dayuconfig['SETEXTEND']);
    $req->setSmsType($dayuconfig['SMSTYPE']);
    $req->setSmsFreeSignName($dayuconfig['SMSFREESIGNNAME']);
    $req->setSmsParam(json_encode($content));
    $req->setRecNum($mobile);
    $req->setSmsTemplateCode($dayuconfig[$smstemp]);
    $resp = $c->execute($req);
    $resp = (array)$resp;
    if (!empty($resp['result'])) {
        $result = (array)$resp['result'];
        $data['code'] = (int)$result['err_code'];
        $data['msg'] = '短信发送成功';
    } else {
        $data['code'] = (int)$resp['code'];
        if (15 == $data['code']) {
            $data['msg'] = "短信发送频繁,请稍后再试";
        } else {
            $data['msg'] = $resp['msg'].$resp['sub_code'];
        }
    }
    return $data;
}

function julongSend($mobile, $smstemp = '', $product = '') {
    //短信配置信息
    $_conf_file = dirname(dirname(APP_PATH))."/conf/sms/junlong.php";
    //获取阿里大鱼配置信息
    if (file_exists($_conf_file)) {
        $_config = include $_conf_file;
    } else {
        $_config = array();
    }
    if (empty($_config)) {
        return false;
    }
    $sms_code = rand(1000, 9999);   //获取随机码
    //$_SESSION['sms_code'] = $sms_code;
    Session::set('sms_code', $sms_code);
    $url = "http://hy.junlongtech.com:8086/getsms?";
    $content = "您的验证码是:".$sms_code;
    $param = array(
        "username" => $_config['username'],
        "password" => strtoupper(\md5($_config['password'])),
        "mobile"   => $mobile,
        "content"  => unescape($content),
        "extend"   => "1966",
        "level"    => "3"
    );
    $code_url = $url.http_build_query($param);
    $_result = file_get_contents($code_url);
    $_result = explode("&", $_result);
    $_result = explode("=", $_result[0]);
    if (0 == $_result['1']) {
        $_rdata['code'] = "0";
        $_rdata['msg'] = '短信发送成功';
    } else {
        $_rdata['code'] = "400";
        $_rdata['msg'] = '请求发送短信失败';
    }
    return $_rdata;
}

function unescape($str) {
    $str = rawurldecode($str);
    preg_match_all("/(?:%u.{4})|&#x.{4};|&#\d+;|.+/U", $str, $r);
    $ar = $r[0];
    //print_r($ar);
    foreach ($ar as $k => $v) {
        if (substr($v, 0, 2) == "%u") {
            $ar[$k] = iconv("UCS-2BE", "UTF-8", pack("H4", substr($v, -4)));
        } elseif (substr($v, 0, 3) == "&#x") {
            $ar[$k] = iconv("UCS-2BE", "UTF-8", pack("H4", substr($v, 3, -1)));
        } elseif (substr($v, 0, 2) == "&#") {
            $ar[$k] = iconv("UCS-2BE", "UTF-8", pack("n", substr($v, 2, -1)));
        }
    }
    return join("", $ar);
}

function sp_send_email($address, $subject, $message) {
    //Loader::import('phpEmail.class', EXTEND_PATH, '.phpmailer.php');
    $config = dirname(dirname(APP_PATH))."/conf/web/config.php";
    //获取阿里大鱼配置信息
    if (file_exists($config)) {
        $emailconfig = include $config;
    } else {
        $emailconfig = array();
    }
    $mail = new \email\phpmailer\Phpmailer();
    // 设置PHPMailer使用SMTP服务器发送Email
    $mail->IsSMTP();
    $mail->IsHTML(true);
    // 设置邮件的字符编码，若不指定，则为'UTF-8'
    $mail->CharSet = 'UTF-8';
    // 添加收件人地址，可以多次使用来添加多个收件人
    $mail->AddAddress($address);
    // 设置邮件正文
    $mail->Body = $message;
    // 设置邮件头的From字段。
    $mail->From = $emailconfig['SP_MAIL_ADDRESS'];
    // 设置发件人名字
    $mail->FromName = $emailconfig['SP_MAIL_SENDER'];
    // 设置邮件标题
    $mail->Subject = $subject;
    // 设置SMTP服务器。
    $mail->Host = $emailconfig['SP_MAIL_SMTP'];
    // 设置SMTP服务器端口。
    $port = $emailconfig['SP_MAIL_SMTP_PORT'];
    $mail->Port = empty($port) ? "25" : $port;
    // 设置为"需要验证"
    $mail->SMTPAuth = true;
    // 设置用户名和密码。
    $mail->Username = $emailconfig['SP_MAIL_LOGINNAME'];
    $mail->Password = $emailconfig['SP_MAIL_PASSWORD'];
    // 发送邮件。
    if (!$mail->Send()) {
        $mailerror = $mail->ErrorInfo;
        return array("error" => 1, "message" => $mailerror);
    } else {
        return array("error" => 0, "message" => "success");
    }
}

function hs_send_email($username, $code, $postemail) {
    $subject = "官方邮箱验证";
    $message
        = "<html lang='zh-cn'>
                    <head>
                    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
                    <style>
                    h1 {
                    	font-size: 16px;
                    }

                    p {
                    	font-size: 14px;
                    	line-height: 23px;
                    }

                    .red {
                    	color: #f30808;
                    	font-size: 14px;
                    }

                    .redNum {
                    	display: block;
                    	color: #fff;
                    	background: #aac5eb;
                    	font-size: 33px;
                    	margin: auto;
                    	width: 132px;
                    	text-align: center;
                    	border: 1px solid;
                    	height: 36px;
                    	line-height: 36px;
                    	position: relative;
                    }

                    .redNum:before {
                    	content: '';
                    	display: block;
                    	height: 100%;
                    	width: 3px;
                    	position: absolute;
                    	left: -4px;
                    	background: #aac5eb;
                    }

                    .redNum:after {
                    	content: '';
                    	display: block;
                    	height: 100%;
                    	width: 3px;
                    	position: absolute;
                    	top: 0;
                    	right: -4px;
                    	background: #aac5eb;
                    }

                    .wrap {
                    	width: 682px;
                    	height: 594px;
                    	no-repeat;
                    	padding: 60px 30px 50px 30px;
                    }

                    .content {
                    	width: 620px;
                    	height: 590px;
                    	word-break: break-all;
                    	padding-top: 20px;
                    	padding-left: 30px;
                    	padding-right: 30px;
                    }

                    .confirm-mail {
                    	display: block;
                    	width: 285px;
                    	height: 82px;

                    	margin-left: 134px;
                    	margin-bottom: 30px;
                    }
                    </style>
                    </head>
                    <body>
                    	<div class='wrap'>
                    		<div class='content'>
                    			<h1>尊敬的用户（ {$username} ）：</h1>
                    			<p>
                    				您正在进行身份认证，要完成该操作，请在<span class='red'>30分钟</span>内输入如下验证码:
                    			</p>
                    			<p>
                    				<span class='redNum'>{$code}</span>
                    			</p>

                    			<p>如果您输入验证码，或者点击上述链接，提示已过期，请重新发起设置申请，感谢您的配合与支持！</p>
                    			<p>（如非本人操作，请忽略此邮件）</p>
                    			<p>&nbsp;</p>
                    		</div>
                    	</div>
                    </body>
                    </html>";
    $emailinfo = sp_send_email($postemail, $subject, $message);
    return $emailinfo;
}

function sendEmail($email) {
    $limit_time = 120;  //设定超时时间 2min
    Session::set('email', $email);
    $sms_code = rand(1000, 9999);   //获取随机码
    $rsdata = hs_send_email(Session::get('email'), $sms_code, $email);
    if (0 != $rsdata['error']) {
        //邮件发送失败
        $result['status'] = $rsdata['error'];
        $result['msg'] = $rsdata['message'];
        return $result;
    } else {
        //发送成功
        $result['status'] = 1;
        $result['msg'] = "邮件发送成功,请到邮箱查看验证码";
        Session::set('sms_code', $sms_code);
        Session::set('sms_time', time());
        Session::set('email', $email);
        return $result;
    }
}

//生成订单号
function setorderid($mem_id) {
    list($usec, $sec) = explode(" ", microtime());
    // 取微秒前3位+再两位随机数+渠道ID后四位
    $orderid = $sec.substr($usec, 2, 3).rand(10, 99).sprintf("%04d", $mem_id % 10000);
    return $orderid;
}