<?php
 
  $logo = $_GET['logo'];

  function payback($Url, $Params, $Method='post'){
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
		$Res = curl_exec($Curl);//运行curl
		curl_close($Curl);//关闭curl
		
		return $Res;
	}
    
    echo payback('http://img.128xy.com/'.$logo,'','get');
?>

