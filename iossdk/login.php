<?php
/**
 * 登录接口
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

$username = isset($urldata['a']) ? $urldata['a'] : ''; // 用户名
$password = isset($urldata['b']) ? $urldata['b'] : ''; // 密码
$device = isset($urldata['c']) ? $urldata['c'] : '3'; // 设备
$imei = isset($urldata['e']) ? $urldata['e'] : ''; // 手机imei码
//$agentgame = isset($urldata['f']) ? $urldata['f'] : '0'; // 渠道ID
$agentgame = 1;
$appid = isset($urldata['g']) ? $urldata['g'] : '2001'; // appid
$deviceinfo = isset($urldata['h']) ? $urldata['h'] : '1111'; // 设备
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

$device = 3;

// 用户名不能为空
if (empty($username)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "用户名不能为空");
}

// 密码不能为空
if (empty($password)) {
    $db->CloseConnection();    
    return Response::show("-2", $rdata, "密码不能为空");
}

// 游戏渠道标识不能为空
/*if ($agentgame < 0) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "游戏渠道标识不能为空");
}*/

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

$username = strtolower($username);

$cppassword = $password;
$password = auth_code($password, "ENCODE", AUTHCODE);
$login_time = time();

$sql = "select id,password,reg_time,fromflag from ".$dbname['dbname'].".c_members where flag=0 and username=:username";
$db->bind("username", $username);
$data = $db->row($sql);
 
if ($data) {

    if ($password != $data['password']) {
        $db->CloseConnection();
        return Response::show("-2", $rdata, "密码错误");
    }
   
    $logindata['cid'] = $dbname['cid'];
    $logindata['userid'] = $data['id'];
    $logindata['appid'] = $appid;
    $logindata['agentgame'] = $agentgame;
    $logindata['imei'] = $imei;
    $logindata['device'] = $device;
    $logindata['login_time'] = $login_time;
    $logindata['reg_time'] = $data['reg_time'];
   
    $loginsql = " INSERT INTO `".$dbname['dbname']."`.`c_logininfo`";
    $loginsql .= " (`cid`,`userid`,`appid`,`agentgame`,`imei`,`device`,`login_time`,`reg_time`)";
    $loginsql .= " VALUES ";
    $loginsql .= " (:cid, :userid,:appid,:agentgame,:imei,:device,:login_time,:reg_time)";    
    $db->query($loginsql, $logindata);
		
    $appkey = $db->getAppkey($appid);
    $rstr = "username={$username}&password={$cppassword}&appkey={$appkey}&logintime={$login_time}";
    $token = md5($rstr);
    $userdata['memkey'] = $token;
    
	$upsql= "UPDATE `".$dbname['dbname']."`.`c_members` SET last_login_time=:last_login_time, memkey=:memkey where id=:id";
    $db->bind("last_login_time", $login_time);
    $db->bind("memkey", $token);
    $db->bind("id", $data['id']);
    $db->query($upsql);
    
	$cppassword = base64_encode($cppassword);
    $password = Response::encode($cppassword, Response::fixedArr());
	
	$cidstr = $dbname['cid']+61803;
	 
	$paytoken = Response::encode($cidstr."_".$cidenty, Response::fixedArr());
	//$paytoken = base64_encode($paytoken);
    
    $rdata = array(
            'a' => $username, 
            'b' => $password, 
            'c' => $token, 
            'd' => $login_time,
			'e' => $paytoken
    );
    
    $db->CloseConnection();
    return Response::show("1", $rdata, "登录成功");
}
// 登录失败
$db->CloseConnection();
return Response::show("-2", $rdata, "账号不存在或者密码不正确");