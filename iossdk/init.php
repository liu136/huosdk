<?php
/**
 * 登录接口
 */

include ('include/common.inc.php');

//$urldata = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : '';//php7无法使用
$urldata = file_get_contents("php://input");

 $urldata = get_object_vars(json_decode($urldata));
$urldata = isset($urldata['a']) ? $urldata['a'] : ''; 
$rdata = array();
// 缺少参数
if (empty($urldata)) {
    $db->CloseConnection();
    return Response::show("-1", $rdata, "缺少参数");
}

$urldata = get_object_vars(json_decode(Response::decompression($urldata)));

$appid = isset($urldata['a']) ? $urldata['a'] : ''; // appid
$ver = isset($urldata['b']) ? $urldata['b'] : ''; // 版本


// appid不能为空
if (empty($appid)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "游戏ID不能为空");
}
if ($appid) {
	$sql = "SELECT appkey FROM c_game WHERE id=".$appid;
	$data = $db -> row($sql);
	
	
	$appkey = $data['appkey'];

	if (empty($data)) {
		$db->CloseConnection();
	    return Response::show("-2", $rdata, "秘钥错误");
	}
} else {
	$db->CloseConnection();
	return Response::show("-2", $rdata, "秘钥错误");
}

// appid不能为空
if (empty($appid)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "游戏ID不能为空");
}

$fp = fopen("/alidata/data/html/iossdk/include/class/logs/test_log.txt","a");
	   flock($fp, LOCK_EX) ;
	   fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n aaa_".$appid."====".$ver."\n");
	   flock($fp, LOCK_UN);
	   fclose($fp);
	   
// 支付类型不能为空

$sql = "select logo ,float_img  from l_client where  id =1 ";
$clientdata = $db->row($sql);

$logo = ISDKSITE."/image.php?logo=".$clientdata['logo'];
$float_img=ISDKSITE."/image.php?logo=".$clientdata['float_img'];

$paydata = array("a"=>'appstroe', "b"=>'appstroe', "c"=>'appstroe');
$payinfo[16] = $paydata;

$rdata = array(
            'a' => $payinfo,
			'b' => $logo,
			'c' => $float_img,
			'f' => 1,
			'g' => "123456"
);

return Response::show("1", $rdata, "配置成功".$ver);

 
