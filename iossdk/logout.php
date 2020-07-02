<?php
/**
 * 退出登录接口
 */
include ('include/common.inc.php');

$urldata = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : '';

$rdata = array();
// 缺少参数
if (empty($urldata)) {
    $db->CloseConnection();
    return Response::show("-1", $rdata, "缺少参数");
}
$urldata = get_object_vars(json_decode($urldata));

$username = isset($urldata['a']) ? $urldata['a'] : ''; // 用户名
$device = isset($urldata['b']) ? $urldata['b'] : ''; // 设备
$imei = isset($urldata['d']) ? $urldata['d'] : ''; // 手机imei码
$agentgame = 0; // 渠道ID
$appid = isset($urldata['f']) ? $urldata['f'] : ''; // appid
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

// 用户名不能为空
if (empty($username)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "用户名不能为空");
}

// 游戏渠道标识不能为空
//if (empty($agentgame)) {
 //   $db->CloseConnection();
//    return Response::show("-2", $rdata, "游戏渠道标识不能为空");
//}

// 没有注册设备来源
if (empty($device)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "没有注册设备来源");
}

// appid不能为空
if (empty($appid)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "游戏ID不能为空");
}

$sql = "select id from ".$dbname['dbname'].".c_members where flag=0 and username=:username";
$db->bind("username", $username);
$userid = $db->single($sql);

$lm_username = addclheader($dbname['cid'], $username);

$logoutdata['cid'] = $dbname['cid'];
$logoutdata['userid'] = $userid;
$logoutdata['appid'] = $appid;
$logoutdata['agentgame'] = $agentgame;
$logoutdata['imei'] = $imei;
$logoutdata['device'] = $device;
$logoutdata['logout_time'] = time();

//更新总后台
$db->insertLelogout($lm_username,$logoutdata);

$logoutsql = " INSERT INTO `".$dbname['dbname']."`.`c_logoutinfo`";
$logoutsql .= " (`cid`, `userid`, `appid`, `agentgame`, `imei`, `device`, `logout_time`)";
$logoutsql .= " VALUES";
$logoutsql .= " (:cid, :userid, :appid, :agentgame, :imei, :device, :logout_time)";
$rs = $db->query($logoutsql,$logoutdata);

if ($rs) {
    $db->CloseConnection();
    return Response::show("1", $rdata, "退出成功");
}

$db->CloseConnection();
return Response::show("-2", $rdata, "服务器错误");
