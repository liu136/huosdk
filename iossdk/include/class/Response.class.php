<?php
class Response{
	/**
	* 按综合通信方式输出数据
	* $code 状态码   200 成功    400 失败
	* $message 提示信息
	* $data 数据
	*/	
	const JSON = 'json';  //定义一个常亮
	public static function show($code, $data=array(), $message='',$type=self::JSON){
		if(!is_numeric($code)){
			return '';
		}
		$type = isset($_GET['format']) ? $_GET['format'] : self::JSON;
		$result = array(
			'code'=>$code,
			'data'=>$data,
			'msg'=>$message,
		);
		if($type == 'json'){
			self::json($code,$data,$message);
			exit;
		}elseif($type == 'array'){
			echo "这里仅是调试模式，不能进行数据传输使用<br/>++++++++++++++++++++++++++++++++++++++<pre>";
			print_r($result);
			echo "</pre>++++++++++++++++++++++++++++++++++++++";
		}elseif($type == 'xml'){
			self::xmlEncode($code,$data,$message);
			exit;
		}else{
			echo "抱歉，暂时未提供此种数据格式";
			//扩展对象或其他方式等
		}
	}
	/**
	* 按json格式封装数据
	* $code 状态码
	* $message 提示信息
	* $data 数据
	*/
	public static function json($code,$data=array(), $message='', $zip=0){
		if(!is_numeric($code)){
			return '';
		}	
		$result = array(
			'code'=>$code,			
			'data'=>$data,
			'msg'=>$message,
		);
		
     	echo self::compression(json_encode($result), $zip);
		//echo json_encode($result);
 		exit;
	}
	
	/**
	 * 压缩并加密
	 * $str 要压缩加密的字符串
	 */
	public static function compression($str,$zip=0) {
		$arr = self::fixedArr();
		$str = base64_encode($str);
		$str = self::encode($str,$arr);
		if(0 != $zip){
			return $str;
		}
		$result = array(
			'code'=>"a",			
			'data'=>$str
		);
		return json_encode($result);
		//return gzencode($str,9);
	}
	
	/**
	 * 解压并解密
	 * $str 要解压解密的字符串
	 */
	public static function decompression($str) {
		$arr = self::fixedArr();
		$tmp = gzinflate(substr($str,10,-8));
		$tmp = self::decode($str,$arr);
		return base64_decode($tmp);
	}
	
    //加密
	public static function encrypt($str){
	    $arr = self::fixedArr();	 
	    $str = self::encode($str,$arr);
	    $str = base64_encode($str);		    
	    return $str;
	}
	
	//解密
	public static function decrypt($str) {
		$arr = self::fixedArr();
		$tmp = base64_decode($str);
		return self::decode($tmp,$arr);
		
	}
	
	//加密的固定数组
	public static function fixedArr() {
		$arr = array('0', '1', '2', '3', '4', '5', '6', '7', '8','9',
				'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l',
				'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y',
				'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
				'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y',
				'Z', '*', '!', '/', '+', '=' 
		);
		
		return $arr;
	}
	
	/**
	 *
	 * 加密函数
	 *
	 * $str 加密的字符串
	 * $arr 固定数组
	 */
	public static function encode($str,$arr) {
		if ($str == null) {
			return "";
		}
	
		$rsstr = "s";
		$toarr = str_split($str);
		$arrlenght = count($arr);
		for ($i=0;$i<count($toarr);$i++) {
			$string = ord($toarr[$i]) + ord($arr[$i % $arrlenght]);
			$rsstr .= $string."_";
		}
	
		$rsstr = substr($rsstr,0,-1);
		$rsstr .= "k";
		return $rsstr;
	}
	
	/**
	 *
	 * 解密函数
	 *
	 * $str 解密的字符串
	 * $arr 固定数组
	 */
	public static function decode($str,$arr) {
		if ($str == '') {
			return '';
		}
	
		$first = substr($str,0,1);
		$end = substr($str,-1);
	
		if ($first == 's' && $end == 'k') {
			$str = substr($str,1,-1);
			$toarr = explode("_",$str);
			$arrlenght = count($arr);
			$rsstr = '';
			for ($i=0;$i<count($toarr);$i++) {
				$string = $toarr[$i] - ord($arr[$i % $arrlenght]);
				$rsstr .= chr($string);
			}
	
			return $rsstr;
		} else {
			return "";
		}
	}
	
	/**
	* 按xml格式封装数据
	* $code 状态码
	* $message 提示信息
	* $data 数据
	*/	
	public static function xmlEncode($code,$data,$message){
		if(!is_numeric($code)){
			return '';
		}
		$result = array(
			'code'=>$code,
			'data'=>$data,
			'msg'=>$message
		);
		header("Content-Type:text/xml");
		$xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
		$xml .= "<root>\n";
		$xml .= self::xmlToEncode($result);
		$xml .= "</root>";
		echo $xml;
	}
	//解析xmlEncode()方法里的$result数组，拼装成xml格式	
	public static function xmlToEncode($data){
		$xml = $attr = "";
		foreach($data as $key => $value){
			//因为xml节点不能为数字，如果$key是数字的话，就重新定义一个节点名，把该数字作为新节点的id名称
			if(is_numeric($key)){
				$attr = " id='{$key}'";
				$key = "item";
			}
			$xml .= "<{$key}{$attr}>\n";
			//递归方法处理$value数组，如果是数组继续解析，如果不是数组，就直接给值
			$xml .= is_array($value) ? self::xmlToEncode($value) : $value;
			$xml .= "</{$key}>";
		}
		return $xml;
	}
	
	public static function payback($Url, $Params, $Method='post'){
		$rs = 0;
		$Curl = curl_init();//初始化curl

		if ('get' == $Method){//以GET方式发送请求
			curl_setopt($Curl, CURLOPT_URL, "$Url?$Params");
		}else{//以POST方式发送请求
			curl_setopt($Curl, CURLOPT_URL, $Url);
			curl_setopt($Curl, CURLOPT_POST, 1);//post提交方式
			curl_setopt($Curl, CURLOPT_POSTFIELDS, $Params);//设置传送的参数
		}

		curl_setopt($Curl, CURLOPT_HEADER, false);//设置header
		curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);//要求结果为字符串且输出到屏幕上
		//curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 3);//设置等待时间

		$Res = curl_exec($Curl);//运行curl
		
		if ( $Res == 'success') {
			$rs = 1;
		} else {
			$rs = 0;
		}
		curl_close($Curl);//关闭curl
		
		return $rs;
	}

	public static function syback($Url, $Params, $Method='post'){
		$rs = 0;
		$Curl = curl_init();//初始化curl

		if ('get' == $Method){//以GET方式发送请求
			curl_setopt($Curl, CURLOPT_URL, "$Url?$Params");
		}else{//以POST方式发送请求
			curl_setopt($Curl, CURLOPT_URL, $Url);
			curl_setopt($Curl, CURLOPT_POST, 1);//post提交方式
			curl_setopt($Curl, CURLOPT_POSTFIELDS, $Params);//设置传送的参数
		}

		curl_setopt($Curl, CURLOPT_HEADER, false);//设置header
		curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);//要求结果为字符串且输出到屏幕上
		//curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 3);//设置等待时间

		$Res = curl_exec($Curl);//运行curl
		$data = json_decode($Res,true);

		curl_close($Curl);//关闭curl
		
		return $data;
	}
}
?>