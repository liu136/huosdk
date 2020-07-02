<?php
/**
 * julong.class.php UTF-8
 * 君隆科技行业短信
 *
 * @date    : 2017/7/5 22:22
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : ouzhongfu <ozf@huosdk.com>
 * @version : HUOSDK 7.2
 */
class junlong {
	public function sendSMS($mobile, $code, $needstatus = 'false', $extno = '') {
	    //短信配置信息
	    if(file_exists(SITE_PATH."conf/sms/julong.php")){
	        $config = include SITE_PATH."conf/sms/julong.php";
	    }else{
	        $config = array();
	    }
        if (empty($config)) {
            return false;
        }
        $url = "http://hy.junlongtech.com:8086/getsms?";
        $content = "您的验证码是:".$code;
        $param = array(
            "username" => $config['username'],
            "password" => strtoupper(\md5($config['password'])),
            "mobile"   => $mobile,
            "content"  => $this->unescape($content),
            "extend"   => "1966",
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
?>