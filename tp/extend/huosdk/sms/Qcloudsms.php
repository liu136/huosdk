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
namespace huosdk\sms;

use think\Log;

class Qcloudsms {
    /**
     * 自定义错误处理
     *
     * @param string $msg
     * @param string $level
     *
     * @internal param 输出的文件 $msg
     */
    private function _error($msg, $level = 'error') {
        $_info = 'sms\Qcloudsms Error:'.$msg;
        Log::record($_info, $level);
    }

    public function __construct() {
    }

    public function send($mobile, $type, $sms_code) {
        // 商讯短信配置信息
        if (file_exists(CONF_PATH."extra/sms/qcloudsms.php")) {
            $qcsconfig = include CONF_PATH."extra/sms/qcloudsms.php";
        } else {
            $this->_error('file_exists(CONF_PATH."extra/sms/qcloudsms.php")');

            return false;
        }
        if (empty($qcsconfig)) {
            $this->_error('empty($qcsconfig)');

            return false;
        }
        $_s_sender = new \sms\qcloudsms\SmsSingleSender($qcsconfig['appid'], $qcsconfig['appkey']);
        $params = array("$sms_code", "1");
        $result = $_s_sender->sendWithParam("86", $mobile, $qcsconfig['SMSFROM_AUTH'], $params, "", "", "");

        $rsp = json_decode($result, true);
        if ($rsp['result'] === 0) {
            $_rdata['code'] = '200';
            $_rdata['msg'] = '发送成功';
        } else {
            $this->_error($result);
            $_rdata['code'] = '0';
            $_rdata['msg'] = "短信发送失败";
        }

        return $_rdata;
    }
}