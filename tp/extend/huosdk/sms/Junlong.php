<?php
/**
 * Junlong.php UTF-8
 * 君隆行业短信
 *
 * @date    : 2017/7/5 18:23
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : ouzhongfu <ozf@huosdk.com>
 * @version : HUOSDK 7.2
 */

namespace huosdk\sms;

use think\helper\hash\Md5;

class Junlong {
    public function send($mobile, $type, $sms_code) {
        //短信配置信息
        if (file_exists(CONF_PATH."extra/sms/junlong.php")) {
            $_config = include CONF_PATH."extra/sms/junlong.php";
        } else {
            return false;
        }
        if (empty($_config)) {
            return false;
        }
        $url = "http://hy.junlongtech.com:8086/getsms?";
        $content = "您的验证码是:".$sms_code;
        $param = array(
            "username" => $_config['username'],
            "password" => strtoupper(\md5($_config['password'])),
            "mobile"   => $mobile,
            "content"  => $this->unescape($content),
            "extend"   => "3226",
            "level"    => "3"
        );
        $code_url = $url.http_build_query($param);
        $_result = file_get_contents($code_url);
        $_result = explode("&", $_result);
        $_result = explode("=", $_result[0]);
        if (0 == $_result['1']) {
            $_rdata['code'] = '200';
            $_rdata['msg'] = '发送成功';
        } else {
            $_rdata['code'] = '0';
            $_rdata['msg'] = "请求发送短信失败";
        }

        return $_rdata;
    }

    /**
     * 请求接口返回内容
     *
     * @param  string $url    [请求的URL地址]
     * @param  string $params [请求的参数]
     * @param  int    $ipost  [是否采用POST形式]
     *
     * @return  string
     */
    public function unescape($str) {
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
}