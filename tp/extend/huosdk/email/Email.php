<?php
/**
 * Email.php UTF-8
 * 光奈网络邮件类
 *
 * @date    : 2017/6/22 11:58
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : wuyonghong <wyh@huosdk.com>
 * @version : HUOSDK 7.2
 */
namespace huosdk\email;

use huosdk\common\Commonfunc;
use think\Log;
use think\Session;

class Email {
    public function __construct($expire_diff = 1800) {
        $this->expaire_diff = $expire_diff;
    }

    /**
     * 自定义错误处理
     *
     * @param string $msg 输出的信息
     * @param string $level
     */
    private function _error($msg = '', $level = 'error') {
        $_info = 'email\Email Error:'.$msg;
        Log::record($_info, $level);
    }

    /**
     * 检查邮箱正确性
     *
     * @param string $email
     *
     *
     * @return bool
     */
    public function checkEmail($email) {
        $checkExpressions = "/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/i";
        if (false == preg_match($checkExpressions, $email)) {
            return false;
        }

        return true;
    }

    public function check($email, $code) {
        /* 1 校验邮箱是否正确 */
        $_rs = $this->checkEmail($email);
        if (false == $_rs || empty($code)) {
            $_rdata['code'] = '400';
            $_rdata['msg'] = '参数错误';

            return $_rdata;
        }
        $_sms_info = Session::get('user_email');
        /* 2 检查是否发送过邮件 */
        if (empty($_sms_info)
            || empty($_sms_info['email_code'])
            || empty($_sms_info['email'])
            || empty($_sms_info['expire_time'])
        ) {
            $_rdata['code'] = '416';
            $_rdata['msg'] = '请发送验证码';

            return $_rdata;
        }
        /* 3 检查邮件是否在有效期内 */
        $_rs = $this->hasSend($email, $_sms_info['email'], $_sms_info['expire_time']);
        if (false == $_rs) {
            $_rdata['code'] = '416';
            $_rdata['msg'] = '验证码已过期,请重新发送';

            return $_rdata;
        }
        if ($_sms_info['email_code'] != $code) {
            $_rdata['code'] = '416';
            $_rdata['msg'] = '验证码错误';

            return $_rdata;
        }
        Session::set('user_email', null);
        $_rdata['code'] = '200';
        $_rdata['msg'] = '验证通过';

        return $_rdata;
    }

    /**
     * 发送短信验证码
     *
     * @param string $post_email 接收的邮箱
     * @param INT    $type       1 注册 2 登陆 3 修改密码 4 信息变更 5 找回密码
     * @param string $username   用户名
     *
     * @return bool
     */
    public function send($post_email, $username, $type = 0) {
        /* 检查邮箱格式是否正确 */
        $_rs = $this->checkEmail($post_email);
        if (false == $_rs) {
            $_rdata['code'] = '413';
            $_rdata['msg'] = '邮箱格式不正确';

            return $_rdata;
        }
        $_session_email_time = 60 + Session::get('email_time', 'user_email');
        $_rs = $this->hasSend($post_email, '', $_session_email_time);
        if ($_rs) {
            $_rdata['code'] = '416';
            $_rdata['msg'] = '验证码已发送,请稍后再试';

            return $_rdata;
        }
        $_code = rand(1000, 9999);
        $_rs = $this->sendEmail($post_email, $_code, $username, $type);
        if (true == $_rs) {
            Session::set('email', $post_email, 'user_email');
            Session::set('email_code', $_code, 'user_email');
            Session::set('type', $type, 'user_email');
            Session::set('email_time', time(), 'user_email');
            $_expairte_time = time() + $this->expaire_diff;
            Session::set('expire_time', $_expairte_time, 'user_email');
            $_rdata['code'] = '200';
            $_rdata['msg'] = '邮件发送成功';
        } else {
            $_rdata['code'] = '400';
            $_rdata['msg'] = '邮件发送失败';
        }

        return $_rdata;
    }

    /**
     * 判断是否已发送过验证码
     *
     * @param string $email 邮箱
     * @param string $session_email
     * @param int    $session_ex_time
     *
     * @return bool 已发送返回true 未发送返回false
     */
    public function hasSend($email, $session_email = '', $session_ex_time = 0) {
        $_session_email = $session_email;
        if (empty($_session_email)) {
            $_session_email = Session::get('email', 'user_email');
        }
        $_session_ex_time = $session_ex_time;
        if (empty($_session_ex_time)) {
            $_session_ex_time = Session::get('expire_time', 'user_email');
        }
        if ($email == $_session_email && time() < $_session_ex_time) {
            return true;
        }

        return false;
    }

    /**
     * 发送邮箱验证码
     *
     * @param string $post_email 接收的邮箱
     * @param INT    $type       1 注册 2 登陆 3 修改密码 4 信息变更 5 找回密码
     * @param string $code       验证码
     * @param string $username   用户名
     *
     *
     * @return bool
     */
    public function sendEmail($post_email, $code = '', $username, $type = 0) {
        $subject = "官方邮箱验证";
        $_type_string = CommonFunc::getSmsTypeString($type);
        $_message = $this->getSendMsg($username, $code, $_type_string);
        $_email_info = sp_send_email($post_email, $subject, $_message);
        if (0 == $_email_info['error']) {
            return true;
        }
        $this->_error(json_encode($_email_info));

        return false;
    }

    /**
     * @param string $username    接收人玩家名称
     * @param string $code        接收人的code
     * @param string $type_string 接收人类型
     *
     * @return string 邮件内容
     */
    public function getSendMsg($username, $code, $type_string = '身份验证') {
        $_message
            = <<<  EOT
<html lang='zh-cn'>
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
    您正在进行{$type_string}，要完成该操作，请在<span class='red'>30分钟</span>内输入如下验证码:
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
    </html>
EOT;

        return $_message;
    }
}