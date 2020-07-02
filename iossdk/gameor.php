<?php
/**
 * 登录接口
 */

include ('include/common.inc.php');

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

$appid = isset($urldata['a']) ? $urldata['a'] : ''; // appid
$ver = isset($urldata['b']) ? $urldata['b'] : ''; // 版本

// appid不能为空
if (empty($appid)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "游戏ID不能为空");
}

if (empty($ver)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "版本不存在");
}

if ($appid) {
	$sql = "SELECT appkey FROM l_game WHERE id=".$appid;
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


$sql = "select paytype from l_gameiospay where gid =:appid AND versions=:versions";
$db->bind("appid", $appid);
$db->bind("versions", $ver);
$payios = $db->row($sql);

$ios_pay = 2;

$g = "a";
$p = "a";
if($payios['paytype'] == 2){
	$ios_pay = 2;
	$pay_url = "";
	$g = "a";
	$p = "a";
}else if($payios['paytype'] == 1){
	$ios_pay = 1;
	$pay_url = base64_encode(SDKPAYSITE);
	$g = base64_encode("alipay://");
	$p = base64_encode("weixin://");

}else{
	$pay_url = "";
	$g = "a";
	$p = "a";
}

if(empty($payios)){
	$ios_pay = 2;
	$pay_url = "";
	$g = "a";
	$p = "a";
}

$db->CloseConnection();

$rdata = array(
			'a' => $ios_pay,
			'b' => $pay_url,
			'c' => $g,
			'd' => $p
);

return Response::show("1", $rdata, "配置成功");

 
