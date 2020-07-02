<?php
/**
 * 一键注册接口
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
$cidenty = CIDENTY; // 渠道标识
$clientkey = CLIENTKEY; // 渠道标识符
$basenum = 10000;    //五位数变化 

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

// appid不能为空
if (empty($appid)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "游戏ID不能为空");
}

$data['username'] = $db->setUsername($dbname['dbname']);

for ($i = 1; $i < 100; $i++) {
    $sql = "select id from `".$dbname['dbname']."`.`c_members` where username=:username";
    $id = $db->single($sql, $data);
    if ($id) {
        $data['username'] = $db->setUsername($dbname['dbname']);
    } else {
        break;
    }
}

if (empty($id)) {
    $rdata = array(
            'a' => $data['username'],
			'b' => rand(100000,999999)
    );
	
    $db->CloseConnection();
    return Response::show("1", $rdata, "注册成功");
}
$db->CloseConnection();
return Response::show("-2", $rdata, "注册失败");
?>