<?php
/**
 * 获取充值记录
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

$pagesize = 8;
$username = isset($urldata['a']) ? $urldata['a'] : '';
$appid = isset($urldata['b']) ? $urldata['b'] : ''; // appid
$paystatus = isset($urldata['c']) ? $urldata['c'] : 1; // 支付状态
$page = isset($urldata['d']) ? $urldata['d'] : 1;
$cidenty = isset($urldata['x']) ? $urldata['x'] : ''; // 渠道标识
$clientkey = isset($urldata['y']) ? $urldata['y'] : ''; // 渠道标识符
                                                        
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

// appid不能为空
if (empty($appid)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "游戏ID不能为空");
}

$sql = "select * from `".MNG_DB_NAME."`.`".LDB_PREFIX."payway` where id in ";
$sql .= "(select paywayid FROM `".MNG_DB_NAME."`.`".LDB_PREFIX."payway_client` where cid=:cid)";
$db->bind('cid', $dbname['cid']);
$payway = $db->query($sql);

foreach ($payway as $k => $v) {
    $payarr[$v['payname']] = $v['realname'];
}
$payuser['username'] = $username;
$payuser['paystatus'] = $paystatus;
$paysql = "select count(*) as t from ".$dbname['dbname'].".c_pay where username =:username and status =:paystatus";
$numrows = $db->row($paysql, $payuser);

// 计算总页数
$pages = intval($numrows['t'] / $pagesize);

// 计算记录偏移量
$offset = $pagesize * ($page - 1);

$paysql = "select orderid as a, amount as b, paytype, create_time from c_pay where username = :username and status =:paystatus order by id desc limit {$offset},{$pagesize}";
$data = $db->query($paysql, $payuser);

foreach ($data as $k => $v) {
    $data[$k]['c'] = $payarr[$v['paytype']];
    $data[$k]['d'] = date('Y-m-d H:i:s', $v['create_time']);
}

if ($data) {
    $db->CloseConnection();
    return Response::show("1", $data, "查询记录成功");
} else {
    $db->CloseConnection();
    return Response::show("1", $rdata, "没有充值记录");
}

?>