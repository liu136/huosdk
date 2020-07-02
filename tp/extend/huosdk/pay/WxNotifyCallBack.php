<?php
namespace huosdk\pay;

use think\Session;

require_once EXTEND_PATH."pay/wxpay/WxPay.Api.php";
require_once EXTEND_PATH."pay/wxpay/WxPay.Notify.php";

class WxNotifyCallBack extends \WxPayNotify {
    private $config;

    function __construct() {
        $_conf_file = CONF_PATH."extra/pay/wxpay/config.php";
        if (file_exists($_conf_file)) {
            $_wx_conf_arr = include $_conf_file;
        } else {
            $_wx_conf_arr = array();
        }
        $appid= Session::get('app_id', 'app');
        if(isset($_wx_conf_arr[$appid])){
            $_wx_conf=$_wx_conf_arr[$appid];
        }else{
            $_wx_conf=$_wx_conf_arr;
        }
        $sslcert_path=CONF_PATH.'extra/pay/wxpay/cert/'.$appid.'/apiclient_cert.pem';
        $sslkey_path=CONF_PATH.'extra/pay/wxpay/cert/'.$appid.'/apiclient_key.pem';
        if(!file_exists($sslcert_path)){
            $sslcert_path=CONF_PATH.'extra/pay/wxpay/cert/apiclient_cert.pem';
        }

        if(!file_exists($sslkey_path)){
            $sslkey_path=CONF_PATH.'extra/pay/wxpay/cert/apiclient_key.pem';
        }


        $this->config = array(
            'app_id'          => $_wx_conf['app_id'],  /* 绑定支付的APPID（必须配置，开户邮件中可查看）*/
            'mch_id'          => $_wx_conf['mch_id'],   /* 商户号（必须配置，开户邮件中可查看）*/
            'key'             => $_wx_conf['key'],  /* 商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置） */
            'app_secret'      => $_wx_conf['app_secret'],  /* 公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），*/
            'curl_proxy_host' => $_wx_conf['curl_proxy_host'],   /* 这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0 */
            'curl_proxy_port' => $_wx_conf['curl_proxy_port'],   /* CURL_PROXY_PORT=0 */
            'report_levenl'   => $_wx_conf['report_levenl'], /* 上报信息配置 */
            'device_info'     => 'WEB', /* 终端设备号(门店号或收银设备ID)，默认请传"WEB" */
            'sslcert_path'    => $sslcert_path,
            'sslkey_path'     => $sslkey_path
        );
    }


    public function updateParam($appid){
         $_conf_file = CONF_PATH."extra/pay/wxpay/config.php";
        if (file_exists($_conf_file)) {
            $_wx_conf_arr = include $_conf_file;
        } else {
            $_wx_conf_arr = array();
        }
        if(isset($_wx_conf_arr[$appid])){
            $_wx_conf=$_wx_conf_arr[$appid];
        }else{
            $_wx_conf=$_wx_conf_arr;
        }
        $sslcert_path=CONF_PATH.'extra/pay/wxpay/cert/'.$appid.'/apiclient_cert.pem';
        $sslkey_path=CONF_PATH.'extra/pay/wxpay/cert/'.$appid.'/apiclient_key.pem';
        if(!file_exists($sslcert_path)){
            $sslcert_path=CONF_PATH.'extra/pay/wxpay/cert/apiclient_cert.pem';
        }

        if(!file_exists($sslkey_path)){
            $sslkey_path=CONF_PATH.'extra/pay/wxpay/cert/apiclient_key.pem';
        }


        $this->config = array(
            'app_id'          => $_wx_conf['app_id'],  /* 绑定支付的APPID（必须配置，开户邮件中可查看）*/
            'mch_id'          => $_wx_conf['mch_id'],   /* 商户号（必须配置，开户邮件中可查看）*/
            'key'             => $_wx_conf['key'],  /* 商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置） */
            'app_secret'      => $_wx_conf['app_secret'],  /* 公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），*/
            'curl_proxy_host' => $_wx_conf['curl_proxy_host'],   /* 这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0 */
            'curl_proxy_port' => $_wx_conf['curl_proxy_port'],   /* CURL_PROXY_PORT=0 */
            'report_levenl'   => $_wx_conf['report_levenl'], /* 上报信息配置 */
            'device_info'     => 'WEB', /* 终端设备号(门店号或收银设备ID)，默认请传"WEB" */
            'sslcert_path'    => $sslcert_path,
            'sslkey_path'     => $sslkey_path
        );
    }

    public function unifiedOrder($trade_type = 'APP') {
        //统一下单
        $_input = new \WxPayUnifiedOrder($this->config);
        $_input->SetAppid($this->config['app_id']);//公众账号ID
        $_input->SetMch_id($this->config['mch_id']);//商户号
        $_input->SetDevice_info($this->config['device_info']);//设备号
        $_input->SetBody(Session::get('product_desc', 'order')); /* 商品描述 */
        $_input->SetOut_trade_no(Session::get('order_id', 'order')); /* 商户订单号 */
        $_input->SetTotal_fee((int)(Session::get('real_amount', 'order') * 100)); /* 总金额 */
        $_input->SetSpbill_create_ip(Session::get('ip', 'device')); /* 终端ip */
        //  $_input->SetSpbill_create_ip($_SERVER['HTTP_ALI_CDN_REAL_IP']); /* 终端ip */
        if ('MWEB' == $trade_type) {
        } else {
            //  $_input->SetTime_start(date("YmdHis")); /* 交易起始时间 */
            //  $_input->SetTime_expire(date("YmdHis", time() + 600)); /* 交易结束时间 */
        }
        $_input->SetNotify_url(config('domain.SDKSITE').url('Pay/Wxpay/notifyurl')); /* 通知地址 */
        $_input->SetTrade_type($trade_type); /* 交易类型 */
        $_input->setKey($this->config['key']); /* 商户支付密钥 */
        $result = \WxPayApi::unifiedOrder($_input, 6, $this->config);
        if ('APP' == $trade_type) {
            return $this->setRData($result);
        } else if ('MWEB' == $trade_type) {
            return $result;
        }

        return false;
//        $result['sign'] = $_input->MakeSign();
    }


     public function bizpayurl($trade_type = 'APP') {


        $_input = new \WxPayUnifiedOrder($this->config);
 
        $_input->SetAppid($this->config['app_id']);//公众账号ID
        $_input->SetMch_id($this->config['mch_id']);//商户号
       
        $_input->SetBody(Session::get('product_desc', 'order')); /* 商品描述 */
        $_input->SetOut_trade_no(Session::get('order_id', 'order')); /* 商户订单号 */
        $_input->SetTotal_fee((int)(Session::get('real_amount', 'order') * 100)); /* 总金额 */
        $_input->SetSpbill_create_ip(Session::get('ip', 'device')); /* 终端ip */
        //  $_input->SetSpbill_create_ip($_SERVER['HTTP_ALI_CDN_REAL_IP']); /* 终端ip */
       if('NATIVE' == $trade_type){//扫码支付
            $_input->SetTime_start(date("YmdHis")); /* 交易起始时间 */
            $_input->SetTime_expire(date("YmdHis", time() + 600*5)); /* 交易结束时间 */
            $_input->SetProduct_id(Session::get('product_id', 'order'));
        }
        $_input->SetNotify_url(config('domain.SDKSITE').url('Pay/Wxpay/notifyurl')); /* 通知地址 */
        $_input->SetTrade_type($trade_type); /* 交易类型 */
        $_input->setKey($this->config['key']); /* 商户支付密钥 */
        if ('NATIVE' == $trade_type) {
            $result = \WxPayApi::unifiedOrder($_input, 6, $this->config);
            return $result;
        }

        return false;

    }



    public function getVal($arr = array(), $key = '', $auto_value = '') {
        if (isset($arr[$key])) {
            return $arr[$key];
        }

        return $auto_value;
    }

    public function setRData($result) {
        $_input = new \WxPayUnifiedOrder($this->config);
        $_token['appid'] = $this->getVal($result, 'appid', '');
        $_token['partnerid'] = $this->getVal($result, 'mch_id', '');
        $_token['prepayid'] = $this->getVal($result, 'prepay_id', '');
        $_token['package'] = 'Sign=WXPay';
        $_token['noncestr'] = $this->getVal($result, 'nonce_str', '');
        $_token['timestamp'] = time();
        $_input->setKey($this->config['key']);
        $_input->FromArray($_token);
        $_token['sign'] = $_input->SetSign();

        return json_encode($_token);
    }

    public function NotifyProcess($data, &$msg) {
        $_class = new \huosdk\pay\Pay();
        /* 商户订单号 */
        $out_trade_no = $data['out_trade_no'];
        /* 微信支付订单号 */
        $trade_no = $data['transaction_id'];
        /* 交易金额 */
        $amount = number_format($data['total_fee'] / 100, 2, '.', '');

        return $_class->selectNotify($out_trade_no, $amount, $trade_no);
    }

    public function selfHandle($needSign = true) {
        $this->Handle($needSign, $this->config);
    }

    public function test(){
        return $this->config;
    }
}
