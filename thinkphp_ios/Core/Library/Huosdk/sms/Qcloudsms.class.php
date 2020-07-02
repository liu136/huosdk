<?php
/**
 * Qcloudsms.php UTF-8
 *
 *
 * @date    : 2017/3/30 0:44
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : wuyonghong <wyh@huosdk.com>
 * @version : HUOSDK 7.0
 */

class Qcloudsms {

    public function __construct() {
    }

    public function send($mobile, $type, $sms_code) {
        include(SITE_PATH.'thinkphp/Core/Library/Vendor/qcloudsms/SmsSingleSender.php');
        // 读取短信配置信息
        if(file_exists(SITE_PATH."conf/sms/qcloudsms.php")){
            $qcsconfig = include SITE_PATH."conf/sms/qcloudsms.php";
        }else{
            $qcsconfig = array();
        }

        if (empty($qcsconfig)) {
            return false;
        }
        $_s_sender = new \sms\qcloudsms\SmsSingleSender($qcsconfig['appid'], $qcsconfig['appkey']);
        $params = array("$sms_code", "1");
        $result = $_s_sender->sendWithParam("86", $mobile, $qcsconfig['SMSFROM_AUTH'], $params, "", "", "");

        $rsp = json_decode($result, true);
        if ($rsp['result'] === 0) {
            $_rdata['code'] = 0;
            $_rdata['msg'] = '发送成功';
        } else {
            $_rdata['code'] = '0';
            $_rdata['msg'] = "短信发送失败";
        }
        return $_rdata;
    }
}