<?php

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

$payway = 'appstroe'; // 支付类型
$amount = isset($urldata['b']) ? intval($urldata['b']) : 0; // 交易金额
$username = isset($urldata['c']) ? trim($urldata['c']) : ''; // 用户名
$roleid = isset($urldata['d']) ? trim($urldata['d']) : ''; // 用户角色id
$serverid = isset($urldata['e']) ? trim($urldata['e']) : ''; // 服务器ID
$imei = isset($urldata['h']) ? trim($urldata['h']) : ''; // 其他支付身份信息
$appid = isset($urldata['j']) ? trim($urldata['j']) : ''; // appid
$agentgame = isset($urldata['k']) ? trim($urldata['k']) : ''; // 渠道ID
$productname = isset($urldata['l']) ? trim($urldata['l']) : ''; // 商品名称
$attach = isset($urldata['n']) ? trim($urldata['n']) : ''; // CP方的扩展参数

$ver = isset($urldata['o']) ? $urldata['o'] : '';
$prod_id = isset($urldata['g']) ? $urldata['g'] : '';

$cidenty = CIDENTY; // 渠道标识
$clientkey = CLIENTKEY; // 渠道标识符

$username = trim($username);                                                   
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
if (empty($agentgame)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "游戏渠道标识不能为空");
}



// appid不能为空
if (empty($appid)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "游戏ID不能为空");
}

// 服务器id不能为空
if (empty($serverid)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "服务器id不能为空");
}

// 角色id不能为空
if (empty($roleid)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "角色id不能为空");
}

//回调不能为空
$cpurl = $db->getCpurl($appid, $dbname['cid']);
if (empty($cpurl)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "无回调");
}

$orderid = setorderid($dbname['cid']);
$userip = get_client_ip(); // 用户支付时使用的网络终端IP
$transtime = time(); // 交易时间

$sql = "select id,agentid from ".DB_DATABASE.".c_members where username=:username";
$db->bind('username', $username);
$data = $db->row($sql);
$regagent = $data['agentid'];
$lm_username = $username;

$paydata['orderid'] = $orderid;
$paydata['amount'] = $amount;
$paydata['userid'] = $data['id'];
$paydata['roleid'] = $roleid;
$paydata['paytype'] = $payway;
$paydata['productname'] = $productname;
$paydata['agentgame'] = $agentgame;
$paydata['serverid'] = $serverid;
$paydata['appid'] = $appid;
$paydata['status'] = 0;
$paydata['ip'] = $userip;
$paydata['imei'] = $imei;
$paydata['create_time'] = $transtime;
$paydata['remark'] = $attach;
$paydata['regagent'] = $regagent;
$paydata['cid'] = $dbname['cid'];
$paydata['lm_username'] = $username;
$paydata['switchflag'] = $dbname['paystatus'];

$payurl="https://sandbox.itunes.apple.com/verifyReceipt";
$box = 0;
	
//if($appid == '2038' || ($appid == '2039' && $ver !="1.50.19") || $appid == '2051'
//	|| ($appid == '2054' && $ver !="4.7")){
//	$box = 1;
//	$payurl="https://buy.itunes.apple.com/verifyReceipt";
//}
	
$paydata['product_id'] = $prod_id;
$paydata['isbox'] = $box;


if ($db->doPay($dbname['dbname'], $cpurl, $paydata)) {
	
    $rdata = array(
            'a' => $orderid,
			'b' => $payurl
    );
	
    $db->CloseConnection();
    return Response::show("1", $rdata, "请求成功");
} 

$db->CloseConnection();
return Response::show("-2", $rdata, "内部服务器发生错误");