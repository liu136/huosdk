<?php

/**
 * 【SDK标准版】和【纯SDK密码键盘版】的商户对接下单接口封装
 * 易联服务器交易接口调用API封装，分别对以下接口调用进行了封装；
 * 接口封装了参数的转码（中文base64转码）、签名和验证签名、通讯和通讯报文处理
 * 1、【SDK标准版】和【纯SDK密码键盘版】的商户订单下单接口
 */
class TransactionClientSmsApi {

	/**
	 * 短信验证发送接口(【短信+API接口版本】)
	 * @param merchantId:		商户代码
	 * @param smId			：      短信凭证号
	 * @param merchOrderId	:	商户订单号
	 * @param tradeTime		:	商户订单提交时间
	 * @param mobileNo		：	手机号码
	 * @param verifyTradeCode：	验证短信的交易码
	 * @param smParam		:	短信参数
	 * @param priKey		:	商户签名的私钥
	 * @param pubKey        :   易联签名验证公钥
	 * @param payecoUrl		：	易联服务器URL地址，只需要填写域名部分
	 * @param retXml        :   通讯返回数据；当不是通讯错误时，该对象返回数据
	 * @return 				: 处理状态码： 0000 : 处理成功， 其他： 处理失败
	 * @throws Exception    :  E101:通讯失败； E102：签名验证失败；  E103：签名失败；
	 */
	static function OrderSendSmCode($merchantId, $smId, $merchOrderId, 
			$tradeTime, $mobileNo, $verifyTradeCode, $smParam, 
			$priKey, $pubKey, $payecoUrl, Xml $retXml) {
		//交易参数
		$tradeCode = "SendSmCode";
		$version = ConstantsClient::getCommIntfVersion();
		
	    //进行数据签名
	    $signData = "Version=".$version."&MerchantId=".$merchantId."&SmId=".$smId 
	    		."&MerchOrderId=".$merchOrderId."&TradeTime=".$tradeTime."&MobileNo=" 
	    		.$mobileNo."&VerifyTradeCode=".$verifyTradeCode."&SmParam=".$smParam;
	    
	    // 私钥签名
		Log::logFile("PrivateKey=".$priKey);
		Log::logFile("data=".$signData);
		$sign = Signatory::rsaSign($signData, $priKey);
		if(Tools::isStrEmpty($sign)){
			throw new Exception("E103");
		}
		Log::logFile("sign=".$sign);

		//提交参数包含中文的需要做base64转码
		$smParam64 = base64_encode($smParam);
	    $data64 = "Version=".$version."&MerchantId=".$merchantId."&SmId=".$smId 
	    		."&MerchOrderId=".$merchOrderId."&TradeTime=".$tradeTime."&MobileNo=" 
	    		.$mobileNo."&VerifyTradeCode=".$verifyTradeCode."&SmParam=".$smParam64;

		//通讯报文
		$url= $payecoUrl."/ppi/merchant/itf.do"; //请求URL
		$url = $url."?TradeCode=".$tradeCode."&".$data64."&Sign=".$sign;
		Log::logFile("url=".$url);
		$retStr = HttpClient::getHttpResponseGET($url, ConstantsClient::getCaCertFileName());
		if(Tools::isStrEmpty($retStr)){
			throw new Exception("E101");
		}		

		//返回数据的返回码判断
		$retXml->setXmlData($retStr);
		$retCode = Tools::getXMLValue($retStr, "retCode");
		$retXml->setRetCode($retCode);
		$retXml->setRetMsg(Tools::getXMLValue($retStr, "retMsg"));
		if(strcmp("0000", $retCode)){
			return $retCode;
		}
		//获取返回数据
		$retVer = Tools::getXMLValue($retStr, "Version");
		$retMerchantId = Tools::getXMLValue($retStr, "MerchantId");
		$retSmId = Tools::getXMLValue($retStr, "SmId");
		$retMerchOrderId = Tools::getXMLValue($retStr, "MerchOrderId");
		$retTradeTime = Tools::getXMLValue($retStr, "TradeTime");
		$retComplated = Tools::getXMLValue($retStr, "Complated");
		$retRemain = Tools::getXMLValue($retStr, "Remain");
		$retExpTime = Tools::getXMLValue($retStr, "ExpTime");
		$retSign = Tools::getXMLValue($retStr, "Sign");  //该凭证号还可以再次发送的短信次数。无值为不限制
		//设置返回数据
		$retXml->setTradeCode($tradeCode);
		$retXml->setVersion($retVer);
		$retXml->setMerchantId($retMerchantId);
		$retXml->setSmId($retSmId);
		$retXml->setMerchOrderId($retMerchOrderId);
		$retXml->setTradeTime($retTradeTime);
		$retXml->setComplated($retComplated);
		$retXml->setRemain($retRemain);
		$retXml->setExpTime($retExpTime);
		$retXml->setSign($retSign);
		  
		//验证签名的字符串
		$backSign = "Version=".$retVer."&MerchantId=".$retMerchantId."&SmId=".$retSmId 
				."&MerchOrderId=".$retMerchOrderId."&TradeTime=".$retTradeTime 
				."&Complated=".$retComplated."&Remain=".$retRemain."&ExpTime=".$retExpTime;

		//验证签名
		$retSign = str_replace(" ", "+", $retSign);
		$b = Signatory::rsaVerify($backSign, $pubKey, $retSign);
		Log::logFile("PublicKey=".$pubKey);
		Log::logFile("data=".$backSign);
		Log::logFile("Sign=".$retSign);
		Log::logFile("验证结果=".$b);
		if($b==false){
			throw new Exception("E102");
		}
		return $retCode;
	}
		
    /**
     * 无磁无密交易接口(【短信+API接口版本】)
     * @param merchantId        商户代码
     * @param industryId        商户行业编号; 未上送此字段时，系统将使用商户配置中对应的行业
     * @param merchOrderId      商户订单号
     * @param amount            商户订单金额，单位为元，格式： nnnnnn.nn
     * @param orderDesc         商户订单描述    字符最大128，中文最多40个；参与签名：采用UTF-8编码
     * @param tradeTime         商户订单提交时间，格式：yyyyMMddHHmmss，超过订单超时时间未支付，订单作废；不提交该参数，采用系统的默认时间（从接收订单后超时时间为30分钟）
     * @param expTime           交易超时时间，格式：yyyyMMddHHmmss， 超过订单超时时间未支付，订单作废；不提交该参数，采用系统的默认时间（从接收订单后超时时间为30分钟）
     * @param notifyUrl         异步通知URL
     * @param extData           商户保留信息； 通知结果时，原样返回给商户；字符最大128，中文最多40个；参与签名：采用UTF-8编码
     * @param miscData          订单扩展信息   根据不同的行业，传送的信息不一样；参与签名：采用UTF-8编码
     * @param notifyFlag        订单通知标志    0：成功才通知，1：全部通知（成功或失败）  不填默认为“1：全部通知”
     * @param smId        		短信凭证号
     * @param smCode        	短信验证码
     * @param mercPriKey        商户签名的私钥
     * @param payecoPubKey      易联签名验证公钥
     * @param payecoUrl         易联服务器URL地址，只需要填写域名部分
     * @param retXml            通讯返回数据；当不是通讯错误时，该对象返回数据
     * @return  处理状态码： 0000 : 处理成功， 其他： 处理失败
     * @throws Exception        E101:通讯失败； E102：签名验证失败；  E103：签名失败；
     */
    static function MerchantOrderPayByAcc($merchantId, $industryId, 
    		$merchOrderId, $amount, $orderDesc, $tradeTime, 
    		$expTime, $notifyUrl, $extData, $miscData, 
    		$notifyFlag, $smId, $smCode,
            $mercPriKey, $payecoPubKey, $payecoUrl, Xml $retXml){
		//交易参数
		$tradeCode = "PayByAcc";
		$version = ConstantsClient::getCommIntfVersion();
		
		//进行数据签名  
		$signData = "Version=".$version."&MerchantId=".$merchantId."&IndustryId=".$industryId 
				."&MerchOrderId=".$merchOrderId."&Amount=".$amount."&OrderDesc=".$orderDesc 
				."&TradeTime=".$tradeTime."&ExpTime=".$expTime."&NotifyUrl=".$notifyUrl 
				."&ExtData=".$extData."&MiscData=".$miscData."&NotifyFlag=".$notifyFlag
				."&SmId=".$smId."&SmCode=".$smCode;
		
		// 私钥签名
		Log::logFile("PrivateKey = ".$mercPriKey);
		Log::logFile("SignData = ".$signData);
		$sign = Signatory::rsaSign($signData, $mercPriKey);
		if(Tools::isStrEmpty($sign)){
			throw new Exception("E103");
		}
		Log::logFile("sign=".$sign);

		//提交参数包含中文的需要做base64转码
		$orderDesc64 = base64_encode($orderDesc);
		$extData64 = base64_encode($extData);
		$miscData64 = base64_encode($miscData);
		//通知地址做URLEncoder处理
		$notifyUrlEn = urlencode($notifyUrl);
		$data64 = "Version=".$version."&MerchantId=".$merchantId."&IndustryId=".$industryId 
				    ."&MerchOrderId=".$merchOrderId."&Amount=".$amount."&OrderDesc=".$orderDesc64 
				    ."&TradeTime=".$tradeTime."&ExpTime=".$expTime."&NotifyUrl=".$notifyUrlEn 
				    ."&ExtData=".$extData64."&MiscData=".$miscData64."&NotifyFlag=".$notifyFlag
   				 	."&SmId=".$smId."&SmCode=".$smCode;

		//通讯报文
		$url= $payecoUrl."/ppi/merchant/itf.do"; //请求URL
		$url = $url."?TradeCode=".$tradeCode."&".$data64."&Sign=".$sign;
		Log::logFile("url=".$url);
		$retStr = HttpClient::getHttpResponseGET($url, ConstantsClient::getCaCertFileName());
		Log::logFile("retStr=".$retStr);
		if(Tools::isStrEmpty($retStr)){
			throw new Exception("E101");
		}

		//返回数据的返回码判断
		$retXml->setXmlData($retStr);
		$retCode = Tools::getXMLValue($retStr, "retCode");
		$retXml->setRetCode(retCode);
		$retXml->setRetMsg(Tools::getXMLValue($retStr, "retMsg"));
		if(strcmp("0000", $retCode)){
			return retCode;
		}
		//获取返回数据
		$retVer = Tools::getXMLValue($retStr, "Version");
		$retMerchantId = Tools::getXMLValue($retStr, "MerchantId");
		$retMerchOrderId = Tools::getXMLValue($retStr, "MerchOrderId");
		$retAmount = Tools::getXMLValue($retStr, "Amount");
		$retExtData = Tools::getXMLValue($retStr, "ExtData");
		if (retExtData != null){
			$retExtData = str_replace(" ", "+", $retExtData);
			$retExtData = base64_decode($retExtData);
		}
		$retOrderId = Tools::getXMLValue($retStr, "OrderId");
		$retStatus = Tools::getXMLValue($retStr, "Status");
		$retPayTime = Tools::getXMLValue($retStr, "PayTime");
		$retSettleDate = Tools::getXMLValue($retStr, "SettleDate");
		$retSign = Tools::getXMLValue($retStr, "Sign");
		//设置返回数据
		$retXml->setTradeCode($tradeCode);
		$retXml->setVersion($retVer);
		$retXml->setMerchantId($retMerchantId);
		$retXml->setMerchOrderId($retMerchOrderId);
		$retXml->setAmount($retAmount);
		$retXml->setExtData($retExtData);
		$retXml->setOrderId($retOrderId);
		$retXml->setStatus($retStatus);
		$retXml->setPayTime($retPayTime);
		$retXml->setSettleDate($retSettleDate);
		$retXml->setSign($retSign);
		  
		//验证签名的字符串
		$backSign = "Version=".$retVer."&MerchantId=".$retMerchantId."&MerchOrderId=".$retMerchOrderId 
		  ."&Amount=".$retAmount."&ExtData=".$retExtData."&OrderId=".$retOrderId
		  ."&Status=".$retStatus."&PayTime=".$retPayTime."&SettleDate=".$retSettleDate;

		//验证签名
		$retSign = str_replace(" ", "+", $retSign);
		$b = Signatory::rsaVerify($backSign, $payecoPubKey, $retSign);
		Log::logFile("PublicKey=".$payecoPubKey);
		Log::logFile("data=".$backSign);
		Log::logFile("Sign=".$retSign);
		Log::logFile("验证结果=".$b);
		if($b==false){
			throw new Exception("E102");
		}
		return $retCode;
	}
}

?>