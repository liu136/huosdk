<?php

/**
 * 商户对接通用接口封装
 * 易联服务器交易接口调用API封装，分别对以下接口调用进行了封装；
 * 接口封装了参数的转码（中文base64转码）、签名和验证签名、通讯和通讯报文处理
 * 1、商户订单查询接口 ： 		适用于所有对接方式
 * 2、商户订单冲正接口 ：		 除【互联网金融】行业外的所有接入方式
 * 3、商户订单退款申请接口 ：	除【互联网金融】行业外的所有接入方式
 * 4、商户订单退款结果查询接口 : 		除【互联网金融】行业外的所有接入方式
 * 5、验证订单结果通知签名 ：	适用于所有对接方式
 * 6、互联网金融行业银行卡解除绑定接口 ： 仅适合于【互联网金融】行业的商户
 */
class TransactionClient {
	/**
	 * 商户订单查询接口
	 * @param merchantId:		商户代码
	 * @param merchOrderId	:	商户订单号
	 * @param tradeTime		:	商户订单提交时间
	 * @param priKey		:	商户签名的私钥
	 * @param pubKey        :   易联签名验证公钥
	 * @param payecoUrl		：	易联服务器URL地址，只需要填写域名部分
	 * @param retXml        :   通讯返回数据；当不是通讯错误时，该对象返回数据
	 * @return 				: 处理状态码： 0000 : 处理成功， 其他： 处理失败
	 * @throws Exception    :  E101:通讯失败； E102：签名验证失败；  E103：签名失败；
	 */
	static function  OrderQuery($merchantId, $merchOrderId, $tradeTime, 
			$priKey, $pubKey, $payecoUrl, Xml $retXml) {
		//交易参数
		$tradeCode = "QueryOrder";
		$version = ConstantsClient::getCommIntfVersion();
		
	    //进行数据签名
	    $signData = "Version=".$version."&MerchantId=".$merchantId."&MerchOrderId=".$merchOrderId."&TradeTime=".$tradeTime;
	    
	    // 私钥签名
		Log::logFile("PrivateKey=".$priKey);
		Log::logFile("data=".$signData);
		$sign = Signatory::rsaSign($signData, $priKey);
		if(Tools::isStrEmpty($sign)){
			throw new Exception("E103");
		}
		Log::logFile("sign=".$sign);

		//通讯报文
	    $url= $payecoUrl."/ppi/merchant/itf.do?TradeCode=".$tradeCode; //请求URL
	    $url = $url."&".$signData."&Sign=".$sign;
	    Log::logFile("url=".$url);
	    $retStr = HttpClient::getHttpResponseGET($url, ConstantsClient::getCaCertFileName());
		Log::logFile("retStr=".$retStr);
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
		$retMerchOrderId = Tools::getXMLValue($retStr, "MerchOrderId");
		$retAmount = Tools::getXMLValue($retStr, "Amount");
		$retExtData = Tools::getXMLValue($retStr, "ExtData");
		if($retExtData != null){
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
		$b = Signatory::rsaVerify($backSign, $pubKey, $retSign);
		Log::logFile("PublicKey=".$priKey);
		Log::logFile("data=".$backSign);
		Log::logFile("Sign=".$retSign);
		Log::logFile("验证结果=".$b);
		if($b==false){
			throw new Exception("E102");
		}
		return $retCode;
	}
	
	
	/**
	 * 商户订单冲正接口
	 * @param merchantId:		商户代码
	 * @param merchOrderId	:	商户订单号
	 * @param amount        :   订单金额
	 * @param tradeTime		:	商户订单提交时间
	 * @param priKey		:	商户签名的私钥
	 * @param pubKey        :   易联签名验证公钥
	 * @param payecoUrl		：	易联服务器URL地址，只需要填写域名部分
	 * @param retXml        :   通讯返回数据；当不是通讯错误时，该对象返回数据
	 * @return 				: 处理状态码： 0000 : 处理成功， 其他： 处理失败
	 * @throws Exception    :  E101:通讯失败； E102：签名验证失败；  E103：签名失败；
	 */
	static function  OrderReverse($merchantId, $merchOrderId, $amount, $tradeTime, 
			$priKey, $pubKey, $payecoUrl, Xml $retXml) {
		//交易参数
		$tradeCode = "QuashOrder";
		$version = ConstantsClient::getCommIntfVersion();
		
	    //进行数据签名
	    $signData = "Version=".$version."&MerchantId=".$merchantId."&MerchOrderId=".$merchOrderId 
	            ."&Amount=".$amount."&TradeTime=".$tradeTime;
	    
	    // 私钥签名
		Log::logFile("PrivateKey=".$priKey);
		Log::logFile("data=".$signData);
		$sign = Signatory::rsaSign($signData, $priKey);
		if(Tools::isStrEmpty($sign)){
			throw new Exception("E103");
		}
		Log::logFile("sign=".$sign);

		//通讯报文
	    $url= $payecoUrl."/ppi/merchant/itf.do?TradeCode=".$tradeCode; //请求URL
	    $url = $url."&".$signData."&Sign=".$sign;
	    Log::logFile("url=".$url);
	    $retStr = HttpClient::getHttpResponseGET($url, ConstantsClient::getCaCertFileName());
		Log::logFile("retStr=".$retStr);
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
		$retMerchOrderId = Tools::getXMLValue($retStr, "MerchOrderId");
		$retAmount = Tools::getXMLValue($retStr, "Amount");
		$retStatus = Tools::getXMLValue($retStr, "Status");
		$retTradeTime = Tools::getXMLValue($retStr, "TradeTime");
		$retSign = Tools::getXMLValue($retStr, "Sign");
		
		//设置返回数据
		$retXml->setTradeCode($tradeCode);
		$retXml->setVersion($retVer);
		$retXml->setMerchantId($retMerchantId);
		$retXml->setMerchOrderId($retMerchOrderId);
		$retXml->setAmount($retAmount);
		$retXml->setStatus($retStatus);
		$retXml->setSign($retSign);
  
		//验证签名的字符串
		$backSign = "Version=".$retVer."&MerchantId=".$retMerchantId."&MerchOrderId=".$retMerchOrderId 
				 ."&Amount=".$retAmount."&Status=".$retStatus."&TradeTime=".$retTradeTime;
		
		//验证签名
		$retSign = str_replace(" ", "+", $retSign);
		$b = Signatory::rsaVerify($backSign, $pubKey, $retSign);
		Log::logFile("PublicKey=".$priKey);
		Log::logFile("data=".$backSign);
		Log::logFile("Sign=".$retSign);
		Log::logFile("验证结果=".$b);
		if($b==false){
			throw new Exception("E102");
		}
		return $retCode;
	}

	/**
	 * 商户订单退款申请接口
	 * @param merchantId:		商户代码
	 * @param merchOrderId	:	商户订单号
	 * @param merchRefundId	:   商户退款申请号
	 * @param amount        :   商户退款金额
	 * @param tradeTime		:	商户订单提交时间
	 * @param priKey		:	商户签名的私钥
	 * @param pubKey        :   易联签名验证公钥
	 * @param payecoUrl		：	易联服务器URL地址，只需要填写域名部分
	 * @param retXml        :   通讯返回数据；当不是通讯错误时，该对象返回数据
	 * @return 				: 处理状态码： 0000 : 处理成功， 其他： 处理失败
	 * @throws Exception    :  E101:通讯失败； E102：签名验证失败；  E103：签名失败；
	 */
	static function OrderRefundReq($merchantId, $merchOrderId, $merchRefundId,
			$amount, $tradeTime, $priKey, $pubKey, $payecoUrl, Xml $retXml){
		//交易参数
		$tradeCode = "RefundOrder";
		$version = ConstantsClient::getCommIntfVersion();
	
		//进行数据签名
		$signData = "Version=".$version."&MerchantId=".$merchantId."&MerchOrderId=".$merchOrderId
					."&MerchRefundId=".$merchRefundId."&Amount=".$amount."&TradeTime=".$tradeTime;
		 
		// 私钥签名
		Log::logFile("PrivateKey=".$priKey);
		Log::logFile("data=".$signData);
		$sign = Signatory::rsaSign($signData, $priKey);
		if(Tools::isStrEmpty($sign)){
			throw new Exception("E103");
		}
		Log::logFile("sign=".$sign);
	
		//通讯报文
		$url= $payecoUrl."/ppi/merchant/itf.do?TradeCode=".$tradeCode; //请求URL
		$url = $url."&".$signData."&Sign=".$sign;
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
		$retMerchOrderId = Tools::getXMLValue($retStr, "MerchOrderId");
		$retMerchRefundId = Tools::getXMLValue($retStr, "MerchRefundId");
		$retAmount = Tools::getXMLValue($retStr, "Amount");
		$retTsNo = Tools::getXMLValue($retStr, "TsNo");
		$retTradeTime = Tools::getXMLValue($retStr, "TradeTime");
		$retSign = Tools::getXMLValue($retStr, "Sign");
		//设置返回数据
		$retXml->setTradeCode($tradeCode);
		$retXml->setVersion($retVer);
		$retXml->setMerchantId($retMerchantId);
		$retXml->setMerchOrderId($retMerchOrderId);
		$retXml->setMerchRefundId($retMerchRefundId);
		$retXml->setAmount($retAmount);
		$retXml->setTsNo($retTsNo);
		$retXml->setTradeTime($retTradeTime);
		$retXml->setSign($retSign);
	
		//验证签名的字符串
		$backSign = "Version=".$retVer."&MerchantId=" .$retMerchantId
					."&MerchOrderId=".$retMerchOrderId."&MerchRefundId=".$retMerchRefundId
					."&Amount=".$retAmount."&TsNo=".$retTsNo."&TradeTime=".$retTradeTime;
	
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
	 * 商户订单退款结果查询接口
	 * @param merchantId:		商户代码
	 * @param merchOrderId	:	商户订单号
	 * @param merchRefundId	:   商户退款申请号
	 * @param tradeTime		:	商户订单提交时间
	 * @param priKey		:	商户签名的私钥
	 * @param pubKey        :   易联签名验证公钥
	 * @param payecoUrl		：	易联服务器URL地址，只需要填写域名部分
	 * @param retXml        :   通讯返回数据；当不是通讯错误时，该对象返回数据
	 * @return 				: 处理状态码： 0000 : 处理成功， 其他： 处理失败
	 * @throws Exception    :  E101:通讯失败； E102：签名验证失败；  E103：签名失败；
	 */
	static function OrderRefundQuery($merchantId, $merchOrderId, $merchRefundId,
			$tradeTime, $priKey, $pubKey, $payecoUrl, Xml $retXml){
		//交易参数
		$tradeCode = "QueryRefund";
		$version = ConstantsClient::getCommIntfVersion();
	
		//进行数据签名 
		$signData = "Version=".$version."&MerchantId=".$merchantId."&MerchOrderId=".$merchOrderId
					."&MerchRefundId=".$merchRefundId."&TradeTime=".$tradeTime;
		  
		// 私钥签名
		Log::logFile("PrivateKey=".$priKey);
		Log::logFile("data=".$signData);
		$sign = Signatory::rsaSign($signData, $priKey);
		if(Tools::isStrEmpty($sign)){
			throw new Exception("E103");
		}
		Log::logFile("sign=".$sign);
	
		//通讯报文
		$url= $payecoUrl."/ppi/merchant/itf.do?TradeCode=".$tradeCode; //请求URL
		$url = $url."&".$signData."&Sign=".$sign;
		Log::logFile("url=".$url);
		$retStr = HttpClient::getHttpResponseGET($url, ConstantsClient::getCaCertFileName());
		Log::logFile("retStr=".$retStr);
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
		$retMerchOrderId = Tools::getXMLValue($retStr, "MerchOrderId");
		$retMerchRefundId = Tools::getXMLValue($retStr, "MerchRefundId");
		$retAmount = Tools::getXMLValue($retStr, "Amount");
		$retTsNo = Tools::getXMLValue($retStr, "TsNo");
		$retStatus = Tools::getXMLValue($retStr, "Status");
		$retRefundTime = Tools::getXMLValue($retStr, "RefundTime");
		$retSettleDate = Tools::getXMLValue($retStr, "SettleDate");
	
		$retSign = Tools::getXMLValue($retStr, "Sign");
		//设置返回数据
		$retXml->setTradeCode($tradeCode);
		$retXml->setVersion($retVer);
		$retXml->setMerchantId($retMerchantId);
		$retXml->setMerchOrderId($retMerchOrderId);
		$retXml->setMerchRefundId($retMerchRefundId);
		$retXml->setAmount($retAmount);
		$retXml->setTsNo($retTsNo);
		$retXml->setStatus($retStatus);
		$retXml->setRefundTime($retRefundTime);
		$retXml->setSettleDate($retSettleDate);
		$retXml->setSign($retSign);
	
		//验证签名的字符串
		$backSign = "Version=".$retVer."&MerchantId=".$retMerchantId."&MerchOrderId=".$retMerchOrderId
					."&MerchRefundId=".$retMerchRefundId."&Amount=".$retAmount."&TsNo=".$retTsNo
					."&Status=".$retStatus."&RefundTime=".$retRefundTime."&SettleDate=".$retSettleDate;
	
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
	 * 验证订单结果通知签名
	 * @param version       ： 通讯版本号
	 * @param merchantId    ： 商户代码
	 * @param merchOrderId  ：商户订单号
	 * @param amount		： 商户订单金额
	 * @param extData		：商户保留信息； 通知结果时，原样返回给商户；字符最大128，中文最多40个；参与签名：采用UTF-8编码 ； 提交参数：采用UTF-8的base64格式编码
	 * @param orderId		：易联订单号
	 * @param status		：订单状态
	 * @param payTime		：订单支付时间
	 * @param settleDate	：订单结算日期
	 * @param sign			：签名数据
	 * @param pubKey		：易联签名验证公钥
	 * @return				： true：验证通过； false：验证不通过
	 * @throws Exception
	 */
	static function  bCheckNotifySign($version, $merchantId, 
			$merchOrderId, $amount, $extData, $orderId, 
			$status, $payTime, $settleDate, $sign, $pubKey) {
		// 对extData进行转码处理: base64转码
		if ($extData != null) {
			$extData = str_replace(" ", "+", $extData);
			$extData = base64_decode($extData);
			Log::logFile("extData=".$extData); // 日志输出，检查转码是否正确
		}
		 
		// 进行数据签名
		$data = "Version=".$version."&MerchantId=".$merchantId
				."&MerchOrderId=".$merchOrderId."&Amount=".$amount
				."&ExtData=".$extData."&OrderId=".$orderId."&Status="
				.$status."&PayTime=".$payTime."&SettleDate=".$settleDate;

		// 验证签名
		$sign = str_replace(" ", "+", $sign);
		$b = Signatory::rsaVerify($data, $pubKey, $sign);
		Log::logFile("PublicKey=".$pubKey);
		Log::logFile("data=".$data);
		Log::logFile("Sign=".$sign);
		Log::logFile("验证结果=".$b);
		return $b;
	}

	/**
	 * 互联网金融行业银行卡解除绑定接口
	 * @param merchantId:		商户代码
	 * @param bankAccNo  	:	解除绑定的银行卡账号
	 * @param tradeTime		:	商户提交时间
	 * @param priKey		:	商户签名的私钥
	 * @param pubKey        :   易联签名验证公钥
	 * @param payecoUrl		：	易联服务器URL地址，只需要填写域名部分
	 * @param retXml        :   通讯返回数据；当不是通讯错误时，该对象返回数据
	 * @return 				:   处理状态码： 0000 : 处理成功， 其他： 处理失败
	 * @throws Exception    :  E101:通讯失败； E102：签名验证失败；  E103：签名失败；
	 */
	static function UnboundBankCard($merchantId, $bankAccNo, $tradeTime,
			$priKey, $pubKey, $payecoUrl, Xml $retXml){
		//交易参数
		$tradeCode = "UnboundBankCard";
		$version = ConstantsClient::getCommIntfVersion();
	
		//进行数据签名
		$signData = "Version=".$version."&MerchantId=".$merchantId."&BankAccNo=".$bankAccNo
					."&TradeTime=".$tradeTime;
		 
		// 私钥签名
		Log::logFile("PrivateKey=".$priKey);
		Log::logFile("data=".$signData);
		$sign = Signatory::rsaSign($signData, $priKey);
		if(Tools::isStrEmpty($sign)){
			throw new Exception("E103");
		}
		Log::logFile("sign=".$sign);
	
		//通讯报文
		$url= $payecoUrl."/ppi/merchant/itf.do?TradeCode=".$tradeCode; //解除绑定URL
		$url = $url."&".$signData."&Sign=".$sign;
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
			return $retCode;
		}
		//获取返回数据
		$retVer = Tools::getXMLValue($retStr, "Version");
		$retMerchantId = Tools::getXMLValue($retStr, "MerchantId");
		$retBankAccNo = Tools::getXMLValue($retStr, "BankAccNo");
		$retTradeTime = Tools::getXMLValue($retStr, "TradeTime");
		$retStatus = Tools::getXMLValue($retStr, "Status");
		$retSign = Tools::getXMLValue($retStr, "Sign");
		//设置返回数据
		$retXml->setTradeCode($tradeCode);
		$retXml->setVersion($retVer);
		$retXml->setMerchantId($retMerchantId);
		$retXml->setBankAccNo($retBankAccNo);
		$retXml->setStatus($retStatus);
		$retXml->setTradeTime($retTradeTime);
		$retXml->setSign($retSign);
	
		//验证签名的字符串
		$backSign = "Version=".$retVer."&MerchantId=".$retMerchantId."&BankAccNo=".$retBankAccNo
					."&TradeTime=".$retTradeTime."&Status=".$retStatus;
	
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
}

?>