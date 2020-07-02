<?php
    /**
     * 随着苹果系统越来越强大，有种马上要开始胡来的节奏，个人认为强制添加内购就是其中之一，虽然很多人都特别鄙视这种行为，然并卵。
     * 具体的官方给出的验证规则，大家可以详细阅读看看：http://zengwu3915.blog.163.com/blog/static/2783489720137605156966/?suggestedreading
     * apple官方提供的文档地址：https://developer.apple.com/library/prerelease/ios/releasenotes/General/ValidateAppStoreReceipt/Chapters/ValidateRemotely.html
     **/
	include ('../include/common.inc.php');
	$urldata = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : '';
	
	$urldata = get_object_vars(json_decode($urldata));
	$urldata = isset($urldata['a']) ? $urldata['a'] : '';
	$rdata = array();
	 // 缺少参数
	if (empty($urldata)) {
		$db->CloseConnection();
		return Response::show("-1", $rdata, "缺少参数");
	 }
	$urldata = get_object_vars(json_decode(Response::decompression($urldata)));
	$orderid = isset($urldata['a']) ? $urldata['a'] : ''; // 订单号
	$apple_receipt = isset($urldata['b']) ? $urldata['b'] : ''; // 苹果内支付待验证信息
	
	$ver = isset($urldata['o']) ? $urldata['o'] : '';

	$cidenty = CIDENTY; // 渠道标识
	$clientkey = CLIENTKEY; // 渠道标识符
	
	// 校验渠道key
	if (empty($cidenty) || empty($clientkey)) {
		$db->CloseConnection();
		return Response::show("-2", $rdata, "标识不能为空");
	} else {
		$dbname = $db->getDbtable($cidenty, $clientkey);

		if (empty($dbname)) {
			$db->CloseConnection();
			return Response::show("-3", $rdata, "标识错误");
		}
	}
	// 订单号不能为空
	if (empty($orderid)) {
		$db->CloseConnection();
		return Response::show("-2", $rdata, "订单号不能为空");
	}
	$check_sql = "select `appid`  from ".$dbname['dbname'].".c_pay where orderid= :orderid"; 
    $db->bind("orderid",$orderid);
    $check_data = $db->row($check_sql);
	
	$appid =$check_data['appid'];
	
	$box = 0;
	$url = 'https://sandbox.itunes.apple.com/verifyReceipt'; //测试验证地址
	
    $jsonData = array('receipt-data'=>$apple_receipt);//这里本来是需要base64加密的，我这里没有加密的原因是客户端返回服务器端之前，已经作加密处理
    $jsonData = json_encode($jsonData);
  
    $response = http_post_datas($url,$jsonData);
	
	if($response->{'status'} == '21007'){
		$box = 1;
		$url = 'https://buy.itunes.apple.com/verifyReceipt'; // 正式验证地址
        // 请求验证
        $jsonData = array('receipt-data'=>$apple_receipt);//
		$jsonData = json_encode($jsonData);
	  
		$response = http_post_datas($url,$jsonData);
		$myfile = fopen("test.txt", "a");
		$txt = $orderid.'===AAA==='.json_encode($response)." \n ==2==".$jsonData."=2=  \n  ";
		fputs($myfile, $txt);
		fclose($myfile);
    }
	
    if($response->{'status'} == 0){
		 $in_app=$response->{'receipt'};
		 
		 $in_app = (array)$in_app;
		
		 $transaction_id =$in_app['transaction_id'];
		 $product_id=$in_app['product_id'];
		 if(!empty($product_id)){
			 
			$check_sql = "select `appid`  from ".$dbname['dbname'].".c_pay where paymark=:paymark"; 
			$db->bind("paymark",$transaction_id);
			$paymark_data = $db->row($check_sql);
			
			$check_sql = "select amount,product_id  from ".$dbname['dbname'].".c_pay where orderid=:orderid"; 
			$db->bind("orderid",$orderid);
			$amount_data = $db->row($check_sql);
			
			if($amount_data['product_id'] != $product_id){
				return Response::show("-1", $response, "请求失败");
				exit;
			}
			if(empty($paymark_data)){
				$myfile = fopen("test1.txt", "a");
				$txt = $orderid.'===AAA==='.json_encode($amount_data)."  \n  ";
				fputs($myfile, $txt);
				fclose($myfile);
	
				$db->doPaynotify($orderid, $amount_data['amount'], $dbname['dbname'],$transaction_id);
				$db->CloseConnection();
				return Response::show("1", $amount, "请求成功");
			}
		 }
		 return Response::show("-1", $response, "请求失败");
			//}
          //return Response::show("-1", $response, "请求失败");
    }else{
         return Response::show("-1", $response, "请求失败");
    }
//curl请求苹果app_store验证地址
function http_post_datas($url, $data_string) {
    $curl_handle=curl_init();
    curl_setopt($curl_handle,CURLOPT_URL, $url);
    curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_handle,CURLOPT_HEADER, 0);
    curl_setopt($curl_handle,CURLOPT_POST, true);
    curl_setopt($curl_handle,CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($curl_handle,CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl_handle,CURLOPT_SSL_VERIFYPEER, 0);
    $response_json =curl_exec($curl_handle);
    $response =json_decode($response_json);
    curl_close($curl_handle);
    return $response;
}
?>