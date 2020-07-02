<?php
/**
 * 注册接口
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

$username = isset($urldata['a']) ? $urldata['a'] : ''; // 用户名
$password = isset($urldata['b']) ? $urldata['b'] : ''; // 密码
$device = '3'; // 设备
$imei = isset($urldata['e']) ? $urldata['e'] : ''; // 手机imei码
//$agentgame = isset($urldata['f']) ? $urldata['f'] : '0'; // 渠道ID
$agentgame = 0;
$appid = isset($urldata['g']) ? $urldata['g'] : ''; // appid
$deviceinfo = isset($urldata['h']) ? $urldata['h'] : ''; // 设备数据,包括手机号码、用户系统版本，以||隔开
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

// 用户名不能为空，并且在5-16位之间
if ((empty($username)) || (strlen($username) > 16) || (strlen($username) < 5)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "用户名不能为空");
}

// 密码不能为空，并且在6-16位之间
if (empty($password) || strlen($password) > 16 || strlen($password) < 6) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "密码长度不正确");
}

// 游戏渠道标识不能为空
//if (empty($agentgame)) {
//    $db->CloseConnection();
//    return Response::show("-2", $rdata, "游戏渠道标识不能为空");
//}

// 没有注册设备来源
if (empty($device)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "没有注册设备来源");
}

// 没有注册设备来源
if (empty($deviceinfo)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "没有注册设备来源");
}

// appid不能为空
if (empty($appid)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "游戏ID不能为空");
}

$username = strtolower($username);
$pattern = "/^[a-z0-9]+$/i";
if (!preg_match($pattern, $username)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "用户名必须全部由数字或者字母组成");
}

// 更具用户名查找用户
$userdata['username'] = $username;
$sql = "select id, agentid from  ".DB_DATABASE.".c_members where username=:username";

$data = $db->row($sql, $userdata);

$agentsql="select agentid  from ".DB_DATABASE.".c_agentlist where agentgame ='".$agentgame."'";
$agentdata=$db->row($agentsql);
if($agentdata){
	$agentid=$agentdata['agentid'];
}else{
	$agentid=0;
}

// 用户名已经存在
if (!empty($data['id'])) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "用户名已经存在");
}

$cppassword = $password;
$password = auth_code($password, "ENCODE", AUTHCODE);
$reg_time = time();
$lm_username = $username;
$userdata['cid'] = $dbname['cid'];
$userdata['lm_username'] = $username;
$userdata['password'] = $password;
$userdata['nickname'] = $username;
$userdata['ip'] = get_client_ip();
$userdata['device'] = $device;
$userdata['imei'] = $imei;
$userdata['agentid'] = $agentid;
$userdata['deviceinfo'] = $deviceinfo;
$userdata['reg_time'] = $reg_time;
$userdata['update_time'] = $reg_time;
$userdata['appid'] = $appid;

if(true){
    $userdata['last_login_time'] = $reg_time;
	
    $appkey = $db->getAppkey($appid);
	
    $rstr = "username={$username}&password={$cppassword}&appkey={$appkey}&logintime={$reg_time}";
    $token = md5($rstr);
    $userdata['memkey'] = $token;
    
    $insql = " INSERT INTO `".$dbname['dbname']."`.`c_members`";
    $insql .= " (`cid`, `username`,`lm_username`,`password`,`nickname`,`ip`,`device`,`imei`,`agentid`,`deviceinfo`,`reg_time`,`update_time`,`appid`,`last_login_time`,`memkey`)";
    $insql .= " VALUES";
    $insql .= " (:cid, :username,:lm_username,:password,:nickname,:ip,:device,:imei,:agentid,:deviceinfo,:reg_time,:update_time,:appid,:last_login_time, :memkey)";
    
    $rs = $db->query($insql, $userdata);
	unset($userdata['lm_username']);
	
    if (!empty($rs) && 0 < $rs) {
        $userid = $db->lastInsertId();
		
		$logindata['cid'] = $dbname['cid'];
        $logindata['userid'] = $userid;
		$logindata['appid'] = $appid;
		$logindata['agentgame'] = $agentid;
		$logindata['imei'] = $imei;
		$logindata['device'] = $device;
		$logindata['login_time'] = $reg_time;
		$logindata['reg_time'] = $reg_time;

		$loginsql = " INSERT INTO `".$dbname['dbname']."`.`c_logininfo`";
		$loginsql .= " (`cid`,`userid`,`appid`,`agentgame`,`imei`,`device`,`login_time`,`reg_time`)";
		$loginsql .= " VALUES ";
		$loginsql .= " (:cid,:userid,:appid,:agentgame,:imei,:device,:login_time,:reg_time)";    
		$db->query($loginsql, $logindata);
        
		$cppassword = base64_encode($cppassword);
        $password = Response::encode($cppassword, Response::fixedArr());
		
		$cidstr = $dbname['cid']+61803;
	 
		$paytoken = Response::encode($cidstr."_".$cidenty, Response::fixedArr());

        $rdata = array(
                'a' => $lm_username, 
                'b' => $password, 
                'c' => $token, 
                'd' => $reg_time,
		        'e' => $paytoken
        );
        $db->CloseConnection();
        return Response::show("1", $rdata, "注册成功");
    }
 }

$db->CloseConnection();
return Response::show("-2", $rdata, "注册失败");
