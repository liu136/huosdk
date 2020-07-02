<?php
/**
 * 登录接口
 */
include ('include/common.inc.php');

//$urldata = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : '';
$urldata = file_get_contents("php://input");
$urldata=json_decode($urldata);
		
$urldata = (array)$urldata;

$verifyid = 0;

$rdata = array(
        'id' => $verifyid,
        'status' => 10,
        'data' => '请求参数错误'
);
// 缺少参数
if (empty($urldata)) {
    $db->CloseConnection();
    echo json_encode($rdata);
    exit;
}
//$urldata = get_object_vars(json_decode($urldata));

$verifyid = isset($urldata['id']) ? $urldata['id'] : '';    //验证ID
$token = isset($urldata['token']) ? $urldata['token'] : ''; //token 登陆时通过SDK客户端传送给游戏服务器
$appid = isset($urldata['appid']) ? $urldata['appid'] : ''; //appid
$lmusername = isset($urldata['username']) ? $urldata['username'] : ''; // 用户名
//$sign = isset($urldata['sign']) ? $urldata['sign'] : ''; // 签名

// 用户名不能为空
if (!is_numeric($verifyid) || empty($token) || empty($lmusername) || !is_numeric($appid)) {
    $db->CloseConnection();
    echo json_encode($rdata);
    exit();
}

$rdata['id'] = $verifyid;

//查询appkay
$db->bind('appid', $appid);
$sql = "SELECT appkey FROM l_game WHERE id=:appid";
$appkey = $db->single($sql);

if (empty($appkey)) {
    $rdata['data'] = 'appid有误';
    $db->CloseConnection();
    echo json_encode($rdata);
    exit();
}


$sql = "select memkey from c_members where flag=0 and username=:username";
$db->bind('username', $lmusername);

$memkey = $db->single($sql);

if ($token != $memkey) {
    $rdata['data'] = "用户未登陆";
    $rdata['status'] = 11;
    $db->CloseConnection();
    echo json_encode($rdata);    
    exit();
}

$rdata['status'] = 1;
$rdata['data'] = '用户已登陆';
$db->CloseConnection();
echo json_encode($rdata);
