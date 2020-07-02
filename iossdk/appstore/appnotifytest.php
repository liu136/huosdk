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
	//if (empty($urldata)) {
	//	$db->CloseConnection();
	//	return Response::show("-1", $rdata, "缺少参数");
	// }
	$urldata = get_object_vars(json_decode(Response::decompression($urldata)));
	$orderid = isset($urldata['a']) ? $urldata['a'] : '1483446705222800001'; // 订单号
	$apple_receipt = isset($urldata['b']) ? $urldata['b'] : 'MIIZIAYJKoZIhvcNAQcCoIIZETCCGQ0CAQExCzAJBgUrDgMCGgUAMIIIwQYJKoZIhvcNAQcBoIIIsgSCCK4xggiqMAoCAQgCAQEEAhYAMAoCARQCAQEEAgwAMAsCAQECAQEEAwIBADALAgEDAgEBBAMMATEwCwIBCwIBAQQDAgEAMAsCAQ4CAQEEAwIBajALAgEPAgEBBAMCAQAwCwIBEAIBAQQDAgEAMAsCARkCAQEEAwIBAzAMAgEKAgEBBAQWAjQrMA0CAQ0CAQEEBQIDAYaiMA0CARMCAQEEBQwDMS4wMA4CAQkCAQEEBgIEUDI0NzAYAgECAgEBBBAMDkRhTWFpYXNzaXN0YW50MBgCAQQCAQIEEBZ9eTz/0L9RG8dsMzxk1QAwGwIBAAIBAQQTDBFQcm9kdWN0aW9uU2FuZGJveDAcAgEFAgEBBBSRM2OP4TZ/lExDRTX7O6PxyvWxCzAeAgEMAgEBBBYWFDIwMTctMDEtMDNUMTI6MzE6NTFaMB4CARICAQEEFhYUMjAxMy0wOC0wMVQwNzowMDowMFowTgIBBwIBAQRGhkfiyD4oeL1q0MK6bia+NuBH6hsw+hHfebiMYB+zgNJQkXGg5RUZR2tOBCyFYhWYm+xIW+QX5QLHOT6ahy7+aSd5r8Bd7DBjAgEGAgEBBFvOkRXnaFFCErSBNXrgsjVkkgKAkor2i7CsbuU1z+UFUtJVOGT5hnw0kdNtKRWIE5E5gyEVO7LJLNGDnkVghEfFjRAZzXeZuKgWg+ByJTtlHGyWsg2ORTRPt/ZtMIIBTAIBEQIBAQSCAUIxggE+MAsCAgasAgEBBAIWADALAgIGrQIBAQQCDAAwCwICBrACAQEEAhYAMAsCAgayAgEBBAIMADALAgIGswIBAQQCDAAwCwICBrQCAQEEAgwAMAsCAga1AgEBBAIMADALAgIGtgIBAQQCDAAwDAICBqUCAQEEAwIBATAMAgI'; // 苹果内支付待验证信息
	$cidenty = isset($urldata['x']) ? $urldata['x'] : 'daoxian'; // 渠道标识
	$clientkey = isset($urldata['y']) ? $urldata['y'] : '1120e5cfa5447298bdc7905cc2afdddd'; // 渠道标识符
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
	echo "aaaa==".$orderid;
	$check_sql = "select `appid`  from ".$dbname['dbname'].".c_pay where orderid= :orderid";
	echo $check_sql;
    $db->bind("orderid",$orderid);
    $check_data = $db->row($check_sql);
	echo json_encode($check_data);
	exit;
	$url = 'https://sandbox.itunes.apple.com/verifyReceipt'; //测试验证地址
	if($check_sql['appid'] == '62244'){
		$url = 'https://buy.itunes.apple.com/verifyReceipt'; // 正式验证地址
	}
   
    $jsonData = array('receipt-data'=>$apple_receipt);//这里本来是需要base64加密的，我这里没有加密的原因是客户端返回服务器端之前，已经作加密处理
    $jsonData = json_encode($jsonData);
    //$url = 'https://buy.itunes.apple.com/verifyReceipt';  正式验证地址
   // $url = 'https://sandbox.itunes.apple.com/verifyReceipt'; //测试验证地址
	
    $response = http_post_datas($url,$jsonData);
    if($response->{'status'} == 0){
		 $receipt=$response->{'receipt'};
		 $in_app=$receipt->{'in_app'};
		
		 $in_app=(array)$in_app[0];
		 $transaction_id =$in_app->{'transaction_id'};
		 $product_id=$in_app['product_id'];
		 $amount=str_replace("COL_SDK","",$product_id);
		 
		$db->doPaynotify($orderid, $amount, $dbname['dbname']);
		$db->CloseConnection();
		 
         return Response::show("1", $amount, "请求成功");
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