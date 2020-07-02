<?php

// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------


use Vendor\Pay\Pay;
use Vendor\Pay\PayVo;

require(dirname(dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'Alipay/service/AlipayTradeService.php');
require(dirname(dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'Alipay/buildermodel/AlipayTradeWapPayContentBuilder.php');

class Aliwappay extends Pay {

    protected $gateway    = 'http://wappaygw.alipay.com/service/rest.htm?';
    protected $verify_url = 'http://notify.alipay.com/trade/notify_query.do';
    protected $config     = array(
        'app_id'     => '',
		'merchant_private_key'   => '',
		'alipay_public_key'   => ''
    );

    public function check() {
		
        if (!$this->config['app_id'] || !$this->config['merchant_private_key']  || !$this->config['alipay_public_key']) {
            E("支付宝设置有误！");
        }
        return true;
    }

    public function buildRequestForm(PayVo $vo) {
      
       //超时时间
       $timeout_express="1m";
      
       $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
       $payRequestBuilder->setBody($vo->getBody());
       $payRequestBuilder->setSubject('充值');
       $payRequestBuilder->setOutTradeNo($vo->getOrderNo());
       $payRequestBuilder->setTotalAmount($vo->getFee());
       $payRequestBuilder->setTimeExpress($timeout_express);
       
       $payResponse = new AlipayTradeService($this->config);
	  
       $result=$payResponse->wapPay($payRequestBuilder,$this->config['return_url'],$this->config['notify_url']);
	   
	   return ;
       
    }

    /**
     * 创建MD5签名
     * @param array $para
     * @return string
     */
    protected function createSign($para) {
        ksort($para);
        reset($para);
        $arg = "";
        while (list ($key, $val) = each($para)) {
            if ($key == "sign" || $key == "sign_type" || $val == "")
                continue;
            $arg.=$key . "=" . $val . "&";
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, -1);

        return md5($arg . $this->config['key']);
    }

    /**
     * 解析远程模拟提交后返回的信息
     * @param $str_text 要解析的字符串
     * @return 解析结果
     */
    protected function parseResponse($str_text) {
        //以“&”字符切割字符串
        $para_split = explode('&', $str_text);
        //把切割后的字符串数组变成变量与数值组合的数组
        foreach ($para_split as $item) {
            //获得第一个=字符的位置
            $nPos            = strpos($item, '=');
            //获得字符串长度
            $nLen            = strlen($item);
            //获得变量名
            $key             = substr($item, 0, $nPos);
            //获得数值
            $value           = substr($item, $nPos + 1, $nLen - $nPos - 1);
            //放入数组中
            $para_text[$key] = $value;
        }

        if (!empty($para_text['res_data'])) {

            //token从res_data中解析出来（也就是说res_data中已经包含token的内容）
            $doc                        = new \DOMDocument();
            $doc->loadXML($para_text['res_data']);
            $para_text['request_token'] = $doc->getElementsByTagName("request_token")->item(0)->nodeValue;
        }

        return $para_text;
    }

    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
    protected function getSignVeryfy($param, $sign, $isSort) {
        //除去待签名参数数组中的空值和签名参数
        $param_filter = array();
        while (list ($key, $val) = each($param)) {
            if ($key == "sign" || $key == "sign_type" || $val == "") {
                continue;
            } else {
                $param_filter[$key] = $param[$key];
            }
        }

        if ($isSort) {
            ksort($param_filter);
            reset($param_filter);
        } else {
            $para_sort                = array();
            $para_sort['service']     = $param_filter['service'];
            $para_sort['v']           = $param_filter['v'];
            $para_sort['sec_id']      = $param_filter['sec_id'];
            $para_sort['notify_data'] = $param_filter['notify_data'];
            $param_filter             = $para_sort;
        }

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = "";
        while (list ($key, $val) = each($param_filter)) {
            $prestr.=$key . "=" . $val . "&";
        }
        //去掉最后一个&字符
        $prestr = substr($prestr, 0, -1);

        $prestr = $prestr . $this->config['key'];
        $mysgin = md5($prestr);

        if ($mysgin == $sign) {
            return true;
        } else {
            return false;
        }
    }

    public function verifyNotify($notify) {
		
		$ispost = $notify['ispost'];
		unset($notify['ispost']);
		unset($notify['dmmethod']);
		unset($notify['apitype']);
		$arr = $notify;

        $alipaySevice = new AlipayTradeService($this->config); 
		
        $result = $alipaySevice->check($arr);
		

        if ($result) {
            $info = array();
            if ($ispost) {
			   $out_trade_no = $notify['out_trade_no'];
	           //支付宝交易号
	           $trade_no = $notify['trade_no'];
	           //交易状态
	           $trade_status = $notify['trade_status'];

               $info['status']       = ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') ? 1 : 0;
               $info['trade_no']     = $trade_no;
			   $info['out_trade_no'] = $out_trade_no;
			   $info['total_amount'] = $notify['total_amount'];
			   $info['ispost'] = 1;
            }elseif ($ispost == false) {
                //支付状态
                $info['status']       = 2;
                $info['out_trade_no'] = htmlspecialchars($notify['out_trade_no']);
				$info['ispost'] = 0;
            } 
			
            return $info;
        } else {
            return false;
        }
    }

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空 
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
    protected function getResponse($notify_id) {
        $partner     = $this->config['partner'];
        $veryfy_url  = $this->verify_url . "?partner=" . $partner . "&notify_id=" . $notify_id;
        $responseTxt = $this->fsockOpen($veryfy_url);
        return $responseTxt;
    }

}