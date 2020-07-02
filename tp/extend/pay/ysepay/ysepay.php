<?php

/**
 * @类名：ysepay_service
 * @功能：银盛支付订单支付接口构造类
 * @详细：构造银盛支付各接口请求报文
 * @日期：2014-10-30
 *
 * @author      蒋波<alber_bob@hotmail.com>
 * @copyright   Copyright(C) 2014 深圳银盛电子支付科技有限公司
 * @version     1.0
 *              @说明：
 *              以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 *              该代码仅供学习和研究银盛支付接口使用，只是提供一个参考。
 */
class ysepay {
    /**
     * ***************配置部分（带#号部分商户需根据实际情况修改）*****************
     *#usercode 商户号
     *#merchantname 商户名
     *#pfxpath 商户私钥证书路径(发送交易签名使用)
     *#businessgatecerpath 银盛支付公钥证书路径(接收到银盛支付回执时验签使用)
     *#pfxpassword 商户私钥证书密码
     *#noticepg_url 前台通知地址:商户系统提供，支付成功跳转商户体统，为空不跳转。银盛支付平台在此URL后追加固定的参数向商户系统跳转:Msg=“订单号|金额（单位：分），然后对Msg做Base64编码”;Check=“Msg的签名后，再进行Base64”
     *#noticebg_url 后台通知地址:商户系统提供，支付成功后，银盛支付平台返回R3501报文
     *#host 银盛支付url
     * xmlpage_url 页面接口类银盛支付网关地址
     * xmlbackmsg_url 后台接口类银盛支付网关地址
     * filemsg_url 文件接口类银盛支付网关地址
     */
    function ysepay($params) {
//        $this->param = array();
        $this->param = $params;
//        $this->param['seller_id'] = "hbdzsw";
//        $this->param['usercode'] = "";
//        $this->param['merchantname'] = "";
//        $this->param['pfxpath'] = ROOT_PATH."includes/modules/payment/ysepay/key/hbdzsw.pfx";
//        $this->param['businessgatecerpath'] = ROOT_PATH."includes/modules/payment/ysepay/key/businessgate.cer";
//        $this->param['pfxpassword'] = "123123";
//        $this->param['noticepg_url'] = "http://paypg1.gandia88.top/Dev/payReturnService.php";
//        $this->param['noticebg_url'] = "http://paypg1.gandia88.top/Dev/payCheckService.php";
//        $this->param['host'] = "pay.ysepay.com"; //生产环境需更换为：pay.ysepay.com  TST:113.106.160.201:889
//        $this->param['xmlpage_url'] = $this->param['host']."/businessgate/yspay.do";
//        $this->param['xmlbackmsg_url'] = $this->param['host']."/businessgate/xmlbackmsg.do";
//        $this->param['filemsg_url'] = $this->param['host']."/businessgate/filemsg.do";
    }

    /**
     * 构造函数
     *
     * @access  public
     *
     * @param
     *
     * @return void
     */
    function __construct(array $params) {
        $this->ysepay($params);
    }

    /**
     * 生成支付代码
     *
     * @param array $order 订单信息
     * @param array $payment 支付方式信息
     */
    function get_code($order, $payment) {
        $myParams = array();
        $myParams['business_code'] = '01000010';
        $myParams['charset'] = 'utf-8';
        $myParams['method'] = 'ysepay.online.directpay.createbyuser';
        $myParams['notify_url'] = 'http://www.xxxx.com/respond_notify.php?code=ysepay';
        $myParams['out_trade_no'] = $this->get_order_id($order['order_sn']);
        $myParams['partner_id'] = 'xxxxx';
        $myParams['return_url'] = 'http://www.xxxx.com/respond.php?code=ysepay';
        $myParams['seller_id'] = 'xxxxx';
        $myParams['seller_name'] = '山东xxxxxxx有限公司';
        $myParams['sign_type'] = 'RSA';
        $myParams['subject'] = 'xxxxx';
        $myParams['timeout_express'] = '1d';
        $myParams['timestamp'] = local_date('Y-m-d H:i:s');
        $myParams['total_amount'] = number_format($order['order_amount'], 2, '.', '');
        $myParams['version'] = '3.0';
        ksort($myParams);
        $data = $myParams;
        $signStr = "";
        foreach ($myParams as $key => $val) {
            $signStr .= $key.'='.$val.'&';
        }
        $signStr = trim($signStr, '&');
        $sign = $this->sign_encrypt(array('data' => $signStr));
        $myParams['sign'] = trim($sign['check']);
        $action = "https://openapi.ysepay.com/gateway.do";
        $def_url = "<br /><form style='text-align:center;' method=post action='".$action."' target='_blank'>";
        while ($param = each($myParams)) {
            $def_url .= "<input type = 'hidden' id='".$param['key']."' name='".$param['key']."' value='".$param['value']
                        ."' />";
        }
        $def_url .= "<input type=submit value='".$GLOBALS['_LANG']['pay_button']."'>";
        $def_url .= "</form>";

        return $def_url;
    }

    /**
     * 同步响应操作
     */
    function respond() {
        //返回的数据处理
        $sign = trim($_POST['sign']);
        $result = $_POST;
        unset($result['sign']);
        ksort($result);
        $url = "";
        foreach ($result as $key => $val) {
            if ($val) {
                $url .= $key.'='.$val.'&';
            }
        }
        $data = trim($url, '&');
        /*写入日志*/
        file_put_contents(ROOT_PATH."z.txt", "\r\n", FILE_APPEND);
        file_put_contents(ROOT_PATH."z.txt", "return|data:".$data."|sign:".$sign, FILE_APPEND);
        /* 验证签名 */
        if ($this->sign_check($sign, $data) != true) {
            echo "验证签名失败！";
            exit;
        }
        if ($result['trade_status'] == 'TRADE_SUCCESS') {
            /* 改变订单状态 */
            order_paid($result['out_trade_no']);

            return true;
        } else {
            return false;
        }
    }

    /**
     * 异步响应操作
     */
    function respond_notify() {
        //返回的数据处理
        $sign = trim($_POST['sign']);
        $result = $_POST;
        unset($result['sign']);
        ksort($result);
        $url = "";
        foreach ($result as $key => $val) {
            /* 验证签名 */
            if ($val) {
                $url .= $key.'='.$val.'&';
            }
        }
        $data = trim($url, '&');
        /*写入日志*/
        file_put_contents(ROOT_PATH."z.txt", "\r\n", FILE_APPEND);
        file_put_contents(ROOT_PATH."z.txt", "notify|data:".$data."|sign:".$sign, FILE_APPEND);
        /* 验证签名 */
        if ($this->sign_check($sign, $data) != true) {
            echo "fail";
            exit;
        } else {
            if ($result['trade_status'] == 'TRADE_SUCCESS') {
                order_paid($result['out_trade_no']);
            }
            file_put_contents(ROOT_PATH."z.txt", "\r\n", FILE_APPEND);
            file_put_contents(ROOT_PATH."z.txt", 'success:1', FILE_APPEND);
            echo 'success';
            exit;
        }
    }

    /**
     *日期转字符
     *输入参数：yyyy-MM-dd HH:mm:ss
     *输出参数：yyyyMMddHHmmss
     */
    function datetime2string($datetime) {
        return preg_replace('/\-*\:*\s*/', '', $datetime);
    }

    /**
     * 验签转明码
     *
     * @param input check
     * @param input msg
     *
     * @return data
     * @return success
     */
    function sign_check($sign, $data) {
        $publickeyFile = $this->param['businessgatecerpath']; //公钥
        $certificateCAcerContent = file_get_contents($publickeyFile);
        $certificateCApemContent = '-----BEGIN CERTIFICATE-----'.PHP_EOL.chunk_split(
                base64_encode($certificateCAcerContent), 64, PHP_EOL
            ).'-----END CERTIFICATE-----'.PHP_EOL;
        // 签名验证
        $success = openssl_verify(
            $data, base64_decode($sign), openssl_get_publickey($certificateCApemContent), OPENSSL_ALGO_SHA1
        );

        return $success;
    }

    /**
     * 签名加密
     *
     * @param input data
     *
     * @return success
     * @return check
     * @return msg
     */
    function sign_encrypt($input) {
        $return = array('success' => 0, 'msg' => '', 'check' => '');
        $pkcs12 = file_get_contents($this->param['pfxpath']); //私钥
        if (openssl_pkcs12_read($pkcs12, $certs, $this->param['pfxpassword'])) {
            $privateKey = $certs['pkey'];
            $publicKey = $certs['cert'];
            $signedMsg = "";
            if (openssl_sign($input['data'], $signedMsg, $privateKey, OPENSSL_ALGO_SHA1)) {
                $return['success'] = 1;
                $return['check'] = base64_encode($signedMsg);
                $return['msg'] = base64_encode($input['data']);
            }
        }

        return $return;
    }
}