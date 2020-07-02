<?php
/* *
 * 接口RSA函数 : RSA签名、验签
 * 说明：    以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,
 *        并非一定要使用该代码。该代码仅提供一个参考。
 */

class Signatory{

	/**
	 * RSA签名
	 * @param $data 待签名数据
	 * @param $private_key_path 商户私钥文件路径
	 * return 签名结果
	 */
	static function rsaSign($data, $rsaPrivateKey) {
	    $priKey=$rsaPrivateKey;
		$res = "-----BEGIN RSA PRIVATE KEY-----\n" .
			wordwrap($priKey, 64, "\n", true) .
			"\n-----END RSA PRIVATE KEY-----";
			
		($res) or die('您使用的私钥格式错误，请检查RSA私钥配置'); 
		
	    //$priKey = file_get_contents($private_key_path);
	   // $res = openssl_get_privatekey($priKey);
	    openssl_sign($data, $sign, $res, OPENSSL_ALGO_MD5);
	    openssl_free_key($res);
		//base64编码
	    $sign = base64_encode($sign);
	    return $sign;
	}
	
	
	/**
	 * RSA验签
	 * @param $data 待签名数据
	 * @param $ali_public_key_path 支付宝的公钥文件路径
	 * @param $sign 要校对的的签名结果
	 * return 验证结果
	 */
	static function rsaVerify($data, $ali_public_key_path, $sign)  {
		$pubKey= $ali_public_key_path;
		$res = "-----BEGIN PUBLIC KEY-----\n" .
				wordwrap($pubKey, 64, "\n", true) .
				"\n-----END PUBLIC KEY-----";
				
		($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');  
		//$pubKey = file_get_contents($ali_public_key_path);
	    //$res = openssl_get_publickey($pubKey);
	    $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_MD5);
	    openssl_free_key($res);    
	    return $result;
	} 
}

?>