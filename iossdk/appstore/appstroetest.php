<?php
    /**
     * 随着苹果系统越来越强大，有种马上要开始胡来的节奏，个人认为强制添加内购就是其中之一，虽然很多人都特别鄙视这种行为，然并卵。
     * 具体的官方给出的验证规则，大家可以详细阅读看看：http://zengwu3915.blog.163.com/blog/static/2783489720137605156966/?suggestedreading
     * apple官方提供的文档地址：https://developer.apple.com/library/prerelease/ios/releasenotes/General/ValidateAppStoreReceipt/Chapters/ValidateRemotely.html
     **/
	include ('../include/common.inc.php');
	$urldata = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : '';
	$urldata = get_object_vars(json_decode($urldata));
	$apple_receipt		= isset($urldata['apple_receipt']) ? $urldata['apple_receipt'] : ''; // 用户名

   
    $jsonData = array('receipt-data'=>$apple_receipt);//这里本来是需要base64加密的，我这里没有加密的原因是客户端返回服务器端之前，已经作加密处理
    $jsonData = json_encode($jsonData);
    //$url = 'https://buy.itunes.apple.com/verifyReceipt';  正式验证地址
    $url = 'https://sandbox.itunes.apple.com/verifyReceipt'; //测试验证地址
    $response = http_post_datas($url,$jsonData);
	
	echo json_encode($response);
	exit;
    if($response->{'status'} == 0){
         return Response::show("1", $response, "请求成功");
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