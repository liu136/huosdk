<?php
/**
 * searchUserBuImeil.php UTF-8
 * 通过IMEI码获取账号信息
 * @date: 2015年10月15日下午11:40:38
 * 
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author : wuyonghong <wyh@1tsdk.com>
 * @version : 1.0
 */
include ('include/common.inc.php');

$urldata = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : '';

$urldata = get_object_vars(json_decode($urldata));
$urldata = isset($urldata['a']) ? $urldata['a'] : ''; // 用户名

$rdata = array();
// 缺少参数
if (empty($urldata)) {
    $db->CloseConnection();
    return Response::show("-1", $rdata, "缺少参数");
}
$urldata = get_object_vars(json_decode(Response::decompression($urldata)));


$appid = isset($urldata['a']) ? $urldata['a'] : ''; // appid
$imei = isset($urldata['b']) ? $urldata['b'] : ''; // 手机imeil码


// appid不能为空
if (empty($appid)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "游戏ID不能为空");
}
if ($appid) {
	$sql = "SELECT appkey FROM l_game WHERE id=".$appid;
	$data = $db -> row($sql);
	
	
	$appkey = $data['appkey'];

	if (empty($data)) {
		$db->CloseConnection();
		return Response::show("1", $rdata, "秘钥错误");
	}
} else {
	$db->CloseConnection();
	return Response::show("1", $rdata, "秘钥错误");
}

if (empty($imei)) {
    $rdata[0] = array(
            'a' => '', 
            'b' => '' 
    );
    $db->CloseConnection();
    return Response::show("1", $rdata, "查询失败");
}

$sql = "select username a,password b from  c_members where imei=:imei and imei !='imei' AND imei !='00000000-0000-0000-0000-000000000000' order by id desc";
$db->bind("imei", $imei);
$rdata = $db->query($sql);

if ($rdata) {
    foreach ($rdata as $k => $v) {
		$password = auth_code($rdata[$k]['b'], "DECODE", AUTHCODE);
        $rdata[$k]['b'] = $password;
    }
    // 查询成功
    $db->CloseConnection();
	
    return Response::show("1", $rdata, "请求成功");
} else {
    // 查询失败
    $db->CloseConnection();
    return Response::show("-1", $rdata, "请求失败");
}
