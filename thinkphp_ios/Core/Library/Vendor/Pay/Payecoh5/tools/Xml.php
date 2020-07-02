<?php

/**
 * 描述： 返回报文的数据元素基础类，只要接口调用有返回报文，就会把报文元素分解到该类中
 * 1、若交易通讯返回失败（RetCode!="0000"），该类只有XmlData、RetCode、RetMsg元素；
 * 2、若报文正常返回：该类的元素请参考接口报文返回数据的元素订单；
 */
class Xml{
	private $XmlData="";	    //响应XML数据
	private $RetCode="";	    //响应码
	private $RetMsg="";	    //响应描述
	private $TradeCode="";	//交易码
	private $Version="";		//通讯协议版本号
	private $MerchantId="";	//商户代码
	private $MerchOrderId=""; //商户订单号
	private $Amount="";		//商户订单金额
	private $TradeTime="";	//商户订单提交时间
	private $OrderId="";		//易联订单号
	private $ExtData="";		//商户保留信息
	private $Status="";		//订单状态
	private $PayTime="";		//支付成功时间
	private $SettleDate="";	//清算日期
	private $Sign="";			//签名
	private $VerifyTime="";	//针对配置了防钓鱼的商户返回该参数，采用易联内部算法进行加密处理；验证时间戳30秒有效
	private $BankAccNo="";	//银行卡卡号
	private $SendNum="";	    //短信发送次数
	private $SmId="";         //短信凭证号
	private $Complated="";    //短信已发送次数
	private $Remain="";       //短信剩余发送次数
	private $ExpTime="";      //短信码有效时间; 单位：分钟
	private $MerchRefundId="";//商户退款申请号
	private $TsNo="";      	//退款申请流水号
	private $RefundTime="";   //退款成功时间	
	
	/**
	 * @return : 响应XML数据
	 */
	function getXmlData() {
		return $this->XmlData;
	}

	/**
	 * @param retCode : 响应XML数据
	 */
	function setXmlData($xmlData) {
		$this->XmlData = $xmlData;
	}
	
	/**
	 * @return : 响应码
	 */
	function getRetCode() {
		return $this->RetCode;
	}

	/**
	 * @param retCode : 响应码
	 */
	function setRetCode($retCode) {
		$this->RetCode = $retCode;
	}
	
	/**
	 * @return : 响应描述
	 */
	function getRetMsg() {
		return $this->RetMsg;
	}

	/**
	 * @param tradeCode : 响应描述
	 */
	function setRetMsg($retMsg) {
		$this->RetMsg = $retMsg;
	}	
	/**
	 * @return : 交易码
	 */
	function getTradeCode() {
		return $this->TradeCode;
	}

	/**
	 * @param tradeCode : 交易码
	 */
	function setTradeCode($tradeCode) {
		$this->TradeCode = $tradeCode;
	}

	/**
	 * @return : 通讯协议版本号
	 */
	function getVersion() {
		return $this->Version;
	}

	/**
	 * @param version : 通讯协议版本号
	 */
	function setVersion($version) {
		$this->Version = $version;
	}

	/**
	 * @return : 商户代码
	 */
	function getMerchantId() {
		return $this->MerchantId;
	}

	/**
	 * @param merchantId : 商户代码
	 */
	function setMerchantId($merchantId) {
		$this->MerchantId = $merchantId;
	}

	/**
	 * @return : 商户订单号
	 */
	function getMerchOrderId() {
		return $this->MerchOrderId;
	}

	/**
	 * @param merchOrderId : 商户订单号
	 */
	function setMerchOrderId($merchOrderId) {
		$this->MerchOrderId = $merchOrderId;
	}

	/**
	 * @return : 商户订单金额
	 */
	function getAmount() {
		return $this->Amount;
	}

	/**
	 * @param amount : 商户订单金额
	 */
	function setAmount($amount) {
		$this->Amount = $amount;
	}

	/**
	 * @return : 商户订单提交时间
	 */
	function getTradeTime() {
		return $this->TradeTime;
	}

	/**
	 * @param tradeTime : 商户订单提交时间
	 */
	function setTradeTime($tradeTime) {
		$this->TradeTime = $tradeTime;
	}

	/**
	 * @return : 易联订单号
	 */
	function getOrderId() {
		return $this->OrderId;
	}

	/**
	 * @param orderId : 易联订单号
	 */
	function setOrderId($orderId) {
		$this->OrderId = $orderId;
	}

	/**
	 * @return : 商户保留信息
	 */
	function getExtData() {
		return $this->ExtData;
	}

	/**
	 * @param extData : 商户保留信息
	 */
	function setExtData($extData) {
		$this->ExtData = $extData;
	}	

	/**
	 * @return : 订单状态
	 */
	function getStatus() {
		return $this->Status;
	}

	/**
	 * @param status : 订单状态
	 */
	function setStatus($status) {
		$this->Status = $status;
	}	
	
	/**
	 * @return : 支付成功时间
	 */
	function getPayTime() {
		return $this->PayTime;
	}

	/**
	 * @param payTime : 支付成功时间
	 */
	function setPayTime($payTime) {
		$this->PayTime = $payTime;
	}	


	/**
	 * @return : 清算日期
	 */
	function getSettleDate() {
		return $this->SettleDate;
	}

	/**
	 * @param settleDate : 清算日期
	 */
	function setSettleDate($settleDate) {
		$this->SettleDate = $settleDate;
	}	

	/**
	 * @return : 签名
	 */
	function getSign() {
		return $this->Sign;
	}

	/**
	 * @param settleDate : 签名
	 */
	function setSign($sign) {
		$this->Sign = $sign;
	}		
	
	/**
	 * @return 防钓鱼验证时间戳
	 */
	function getVerifyTime() {
		return $this->VerifyTime;
	}
	
	/**
	 * @param verifyTime 防钓鱼验证时间戳
	 */
	function setVerifyTime($verifyTime) {
		$this->VerifyTime = $verifyTime;
	}
	
	/**
	 * @return 银行卡卡号
	 */
	function getBankAccNo() {
		return $this->BankAccNo;
	}
	
	/**
	 * @param bankAccNo 银行卡卡号
	 */
	function setBankAccNo($bankAccNo) {
		$this->BankAccNo = $bankAccNo;
	}
	
	/**
	 * @return 短信发送次数
	 */
	function getSendNum() {
		return $this->SendNum;
	}
	
	/**
	 * @param sendNum 短信发送次数
	 */
	function setSendNum($sendNum) {
		$this->SendNum = $sendNum;
	}
	
	/**
	 * @return 短信凭证号
	 */
	function getSmId() {
		return $this->SmId;
	}
	
	/**
	 * @param smId 短信凭证号
	 */
	function setSmId($smId) {
		$this->SmId = $smId;
	}
	
	/**
	 * @return 短信已发送次数
	 */
	function getComplated() {
		return $this->Complated;
	}
	
	/**
	 * @param complated 短信已发送次数
	 */
	function setComplated($complated) {
		$this->Complated = $complated;
	}
	
	/**
	 * @return 短信剩余发送次数
	 */
	function getRemain() {
		return $this->Remain;
	}
	
	/**
	 * @param remain 短信剩余发送次数
	 */
	function setRemain($remain) {
		$this->Remain = $remain;
	}
	
	/**
	 * @return 短信码有效时间; 单位：分钟
	 */
	function getExpTime() {
		return $this->ExpTime;
	}
	
	/**
	 * @param expTime 短信码有效时间; 单位：分钟
	 */
	function setExpTime($expTime) {
		$this->ExpTime = $expTime;
	}
	
	/**
	 * @return 商户退款申请号
	 */
	function getMerchRefundId() {
		return $this->MerchRefundId;
	}
	
	/**
	 * @param merchRefundId 商户退款申请号
	 */
	function setMerchRefundId($merchRefundId) {
		$this->MerchRefundId = $merchRefundId;
	}
	
	/**
	 * @return 退款申请流水号
	 */
	function getTsNo() {
		return $this->TsNo;
	}
	
	/**
	 * @param tsNo 退款申请流水号
	 */
	function setTsNo($tsNo) {
		$this->TsNo = $tsNo;
	}
	
	/**
	 * @return 退款成功时间
	 */
	function getRefundTime() {
		return $this->RefundTime;
	}
	
	/**
	 * @param refundTime 退款成功时间
	 */
	function setRefundTime($refundTime) {
		$this->RefundTime = $refundTime;
	}	
	
}
?>
