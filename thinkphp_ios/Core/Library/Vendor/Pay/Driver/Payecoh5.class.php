<?php

use Vendor\Pay\Pay;
use Vendor\Pay\PayVo;

require(dirname(dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'Payecoh5/tools/HttpClient.php');
require(dirname(dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'Payecoh5/tools/Log.php');
require(dirname(dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'Payecoh5/tools/Signatory.php');
require(dirname(dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'Payecoh5/tools/Tools.php');
require(dirname(dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'Payecoh5/tools/Xml.php');
require(dirname(dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'Payecoh5/client/ConstantsClient.php');
require(dirname(dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'Payecoh5/client/TransactionClientH5.php');
require(dirname(dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'Payecoh5/client/TransactionClient.php');

	
class Payecoh5 extends Pay {

    protected $gateway = '';
    protected $config  = array(
        'merchant_id'     => '',
        'merchant_rsa_private_key' => '',
		'payeco_rsa_public_key'     => ''
    );

    public function check() {
        if (!$this->config['merchant_id'] || !$this->config['merchant_rsa_private_key'] || !$this->config['payeco_rsa_public_key']) {
            E("易联设置有误！");
        }
        return true;
    }

    public function buildRequestForm(PayVo $vo) {
		if(Tools::checkAmount($vo->getFee()) == false){
		    $retMsgJson = "{\"RetCode\":\"E105\",\"RetMsg\":\"金额格式错!\"}";
		    echo $retMsgJson;
		    return; 
	    }

	    //下订单处理自动设置的参数
		$clientIp = $_SERVER["REMOTE_ADDR"]; //商户用户访问IP地址
	    $merchOrderId = $vo->getOrderNo();  //订单号；本例子按时间产生； 商户请按自己的规则产生
	    $merchantId = $this->config['merchant_id'];
	    $notifyUrl = $this->config['notify_url'];  //需要做URLEncode
	    $returnUrl = $this->config['return_url'];  //需要做URLEncode
	    $tradeTime =  Tools::getSysTime();
	    $expTime = ""; //采用系统默认的订单有效时间
	    $notifyFlag = "0";

	    // 调用下单接口
	    $retXml = new Xml();
	    $retMsgJson = "";
	    $bOK = true;
	    try {
		   Log::setLogFlag(true);
		   Log::logFile("--------商户下单接口测试---------------");
		   $ret = TransactionClientH5::MerchantOrderH5($merchantId,
				$merchOrderId, $vo->getFee(), $vo->getBody(), $tradeTime, $expTime,
				$notifyUrl, $returnUrl, $extData, $miscData, $notifyFlag, $clientIp, 
				$this->config['merchant_rsa_private_key'], $this->config['payeco_rsa_public_key'],
				$this->config['payeco_url'], $retXml);
		   if(strcmp("0000", $ret)){
			   $bOK=false;
			   $retMsgJson = "{\"RetCode\":\"".$ret."\",\"RetMsg\":\"下订单接口返回错误!\"}";
		    }
	    } catch (Exception $e) {
		    $bOK=false;
		    $errCode  = $e->getMessage();
		    if(strcmp("E101", $errCode) == 0){
			    $retMsgJson = "{\"RetCode\":\"E101\",\"RetMsg\":\"下订单接口无返回数据!\"}";
		    }else if(strcmp("E102", $errCode) == 0){
			    $retMsgJson = "{\"RetCode\":\"E102\",\"RetMsg\":\"验证签名失败!\"}";
		    }else if(strcmp("E103", $errCode) == 0){
			   $retMsgJson = "{\"RetCode\":\"E103\",\"RetMsg\":\"进行订单签名失败!\"}";
		    }else{
			   $retMsgJson = "{\"RetCode\":\"E100\",\"RetMsg\":\"下订单通讯失败!\"}";
		    }
	    }
	
	    //重定向到订单支付
	    if($bOK){
		    //根据返回的参数组织向易联支付平台提交支付申请的URL
		    $redirectUrl = TransactionClientH5::getPayInitRedirectUrl($this->config['payeco_url'], $retXml);
		    Log::logFile("PayURL : ".redirectUrl);
		
		   //针对【支付申请URL】，可以采用直接sendRedirect转跳的方式；也可以采用页面确认后再转跳的方式；
		   //商户根据自己的业务逻辑选择，建议在正式使用时采用sendRedirect转跳方式；
                echo "<script language='javascript' type='text/javascript'>";
           		echo "window.location.href='$redirectUrl'";  
           		echo "</script>";
		
		  //--页面确认后再转跳的方式
		  //$retMsgJson = "<html><head><title>易联支付H5测试-支付请求</title></head><body>支付请求URL:".$redirectUrl."<br/>"
			//	." <a href=\"".$redirectUrl."\">立即支付</a></body></html>";
		  
	    }else{
		    //输出数据
		   Log::logFile("retMsgJson=".$retMsgJson);
		   
	    }
		return ;
    }

    /**
     * 支付平台统一使用GBK/GB2312编码方式。
     * @param type $str
     * @return type
     */
    protected function toGbk($str, $from = "utf-8", $to = 'gbk') {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($str, $to, $from);
        } elseif (function_exists('iconv')) {
            return iconv($from, $to, $str);
        } else {
            return $str;
        }
    }

    /**
     * 创建签名
     * @param type $params
     */
    protected function createSign($params) {

        ksort($params);
        reset($params);
        $arg = '';
        foreach ($params as $value) {
            if (IS_POST) {
                $arg .= $value;
            } else {
                if (in_array($key, array('p1_MerId', 'r0_Cmd', 'r1_Code', 'r2_TrxId', 'r3_Amt', 'r4_Cur', 'r5_Pid', 'r6_Order', 'r7_Uid', 'r8_MP', 'r9_BType')) == true) {
                    $arg .= $value;
                }
            }
        }
        $key = $this->config['key'];

        $arg = $this->toGbk($arg, "gbk", "utf-8");

        $b = 64; // byte length for md5
        if (strlen($key) > $b) {
            $key = pack("H*", md5($key));
        }
        $key    = str_pad($key, $b, chr(0x00));
        $ipad   = str_pad('', $b, chr(0x36));
        $opad   = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;

        return md5($k_opad . pack("H*", md5($k_ipad . $arg)));
    }

    public function verifyNotify($notify) {
        // 结果通知参数，易联异步通知采用GET提交
		$ispost = $notify['ispost'];
		unset($notify['ispost']);
		
	    $version = $notify["Version"];
	    $merchantId = $notify["MerchantId"];
	    $merchOrderId = $notify["MerchOrderId"];
	    $amount = $notify["Amount"];
	    $extData = $notify["ExtData"];
	    $orderId = $notify["OrderId"];
	    $status = $notify["Status"];
	    $payTime = $notify["PayTime"];
	    $settleDate = $notify["SettleDate"];
	    $sign = $notify["Sign"];
        
		$isnumer = is_numeric($amount);
	    // 需要对必要输入的参数进行检查，本处省略...
        if(empty($merchantId) || empty($merchOrderId) || !$isnumer || $amount <= 0){
			
			return false;
		}
		
	    // 订单结果逻辑处理
	    $retMsgJson = "";
		$info = array();
	    try {
		   Log::setLogFlag(true);
		   //验证订单结果通知的签名
		   Log::logFile("------订单结果通知验证-----------------");
		   $b = TransactionClient::bCheckNotifySign($version, $merchantId, $merchOrderId, 
				$amount, $extData, $orderId, $status, $payTime, $settleDate, $sign, 
				$this->config['payeco_rsa_public_key']);
		    if (!$b) {
			   $retMsgJson = "{\"RetCode\":\"E101\",\"RetMsg\":\"验证签名失败!\"}";
			   Log::logFile("验证签名失败!");
		   }else{
			   // 签名验证成功后，需要对订单进行后续处理
			    if (strcmp("02", $status) == 0) { // 订单已支付
				   // 1、检查Amount和商户系统的订单金额是否一致
				   // 2、订单支付成功的业务逻辑处理请在本处增加（订单通知可能存在多次通知的情况，需要做多次通知的兼容处理）；
				   // 3、返回响应内容
				   
				   $out_trade_no = $merchOrderId;
	               $trade_no = $orderId;
	               //交易状态
	               $trade_status = $status;

                   $info['status']       = ($trade_status == '02') ? 1 : 0;
                   $info['trade_no']     = $trade_no;
				   $info['total_amount']     = $amount;
			       $info['out_trade_no'] = $out_trade_no;
			       
				   Log::logFile("订单已支付!");
				   return $info;
			    } else {
				   // 1、订单支付失败的业务逻辑处理请在本处增加（订单通知可能存在多次通知的情况，需要做多次通知的兼容处理，避免成功后又修改为失败）；
				   // 2、返回响应内容
				   
				   $retMsgJson = "{\"RetCode\":\"E102\",\"RetMsg\":\"订单支付失败".status."\"}";
				   Log::logFile("订单支付失败!status=".status);
			    }
				
		    }
	    } catch (Exception $e) {
		   $retMsgJson = "{\"RetCode\":\"E103\",\"RetMsg\":\"处理通知结果异常\"}";
		   Log::logFile("处理通知结果异常!e=".$e->getMessage());
	    }
	    Log::logFile("-----处理完成----");
	    //返回数据
	  
        return false;
    }

}
