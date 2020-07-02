<?php
/**
 * 注册接口
 */
include ('include/common.inc.php');



$urldata = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : '';

$arr = 	array(
		'code' => '',
		'data' => '',
		'msg' => ''	
);

// 缺少参数
if (empty($_POST)) {
	
    $db->CloseConnection();

	$arr['code'] = '0';
	$arr['data'] = '';
	$arr['msg'] = '缺少参数';

   echo json_encode($arr);
   exit;
}
//$urldata = get_object_vars(json_decode($urldata));

$username = isset($_POST['username']) ? $_POST['username'] : ''; // 用户名
$password = isset($_POST['password']) ? $_POST['password'] : ''; // 密码
$appid = isset($_POST['appid']) ? $_POST['appid'] : '1'; // appid
$time = isset($_POST['regtime']) ? $_POST['regtime'] : ''; //
$cid = isset($_POST['cid']) ? $_POST['cid'] : '1'; // appid

$sign = isset($_POST['sign']) ? $_POST['sign'] : ''; // 签名
    
// 校验渠道key
if (empty($cid) || empty($sign)) {
    $db->CloseConnection();
   
	$arr['code'] = '0';
	$arr['data'] = '';
	$arr['msg'] = '标识签名不能为空';
	echo json_encode($arr);
	exit;
} else {
    $dbname = $db->clientinfo($cid);
    if (empty($dbname)) {
        $db->CloseConnection();
       
		$arr['code'] = '0';
		$arr['data'] = '';
		$arr['msg'] = '标识错误';
		echo json_encode($arr);
		exit;
    }
}

$str = "username=".$username."&password=".$password."&regtime=".$time."&cid=".$cid."&appkey=".$dbname['clientkey'];
$signstr = md5($str);
if($signstr != $sign){
		$arr['code'] = '0';
		$arr['data'] = '';
		$arr['msg'] = '签名错误';
		echo json_encode($arr);
		exit;
}
// 用户名不能为空，并且在5-16位之间
if ((empty($username)) || (strlen($username) > 16) || (strlen($username) < 5)) {
		$db->CloseConnection();
		$arr['code'] = '0';
		$arr['data'] = '';
		$arr['msg'] = '用户名不能为空，长度应在5-16之间';
		echo json_encode($arr);
		exit;
}

// 密码不能为空，并且在6-16位之间
if (empty($password) || strlen($password) > 16 || strlen($password) < 6) {
    $db->CloseConnection();
    $arr['code'] = '0';
	$arr['data'] = '';
	$arr['msg'] = '密码不能为空，长度应在6-16之间';
	echo json_encode($arr);
	exit;
}

// appid不能为空
if (empty($appid)) {
		$db->CloseConnection();
		$arr['code'] = '0';
		$arr['data'] = '';
		$arr['msg'] = '游戏ID不能为空';
		echo json_encode($arr);
		exit;
}

$username = strtolower($username);
$pattern = "/^[a-z0-9]+$/i";
if (!preg_match($pattern, $username)) {
		$db->CloseConnection();
		
		$arr['code'] = '0';
		$arr['data'] = '';
		$arr['msg'] = '用户名必须全部由数字或者字母组成';
		echo json_encode($arr);
		exit;
}

// 更具用户名查找用户
$userdata['username'] = $username;
$sql = "select id, agentgame from " . $dbname['dbname'] . ".c_members where username=:username";
$data = $db->row($sql, $userdata);

// 用户名已经存在
if (!empty($data['id'])) {
    $db->CloseConnection();
    $arr['code'] = '0';
		$arr['data'] = '';
		$arr['msg'] = '用户名已存在';
		echo json_encode($arr);
		exit;
}
$clientkey=$dbname['clientkey'];
$cppassword = $password;
$password = auth_code($password, "ENCODE", $clientkey);
$reg_time = time();
$lm_username = addclheader($dbname['cid'], $username);
$userdata['cid'] = $dbname['cid'];
$userdata['lm_username'] = $lm_username;
$userdata['password'] = $password;
$userdata['nickname'] = $username;
$userdata['ip'] = get_client_ip();
$userdata['device'] = '1';
$userdata['imei'] = 'default';
$userdata['agentgame'] = 'default';
$userdata['deviceinfo'] = '';
$userdata['reg_time'] = $reg_time;
$userdata['update_time'] = $reg_time;
$userdata['appid'] = $appid;


// 插入总后台数据
    //unset($userdata['lm_username']);
    $userdata['last_login_time'] = $reg_time;
	
    $appkey = $db->getAppkey($appid);
	
    $rstr = "username={$lm_username}&password={$cppassword}&appkey={$appkey}&logintime={$reg_time}";
    $token = md5($rstr);
    $userdata['memkey'] = $token;
    
    $insql = " INSERT INTO `".$dbname['dbname']."`.`c_members`";
    $insql .= " (`cid`, `username`,`lm_username`,`password`,`nickname`,`ip`,`device`,`imei`,`agentgame`,`deviceinfo`,`reg_time`,`update_time`,`appid`,`last_login_time`,`memkey`)";
    $insql .= " VALUES";
    $insql .= " (:cid, :username,:lm_username,:password,:nickname,:ip,:device,:imei,:agentgame,:deviceinfo,:reg_time,:update_time,:appid,:last_login_time, :memkey)";
    
    $rs = $db->query($insql, $userdata);
	unset($userdata['lm_username']);
	
    if (!empty($rs) && 0 < $rs) {
        $userid = $db->lastInsertId();
		
		$logindata['cid'] = $dbname['cid'];
        $logindata['userid'] = $userid;
		$logindata['appid'] = $appid;
		$logindata['agentgame'] = 'default';
		$logindata['imei'] = 'default';
		$logindata['device'] = '1';
		$logindata['login_time'] = $reg_time;
		$logindata['reg_time'] = $reg_time;
		
		$loginsql = " INSERT INTO `".$dbname['dbname']."`.`c_logininfo`";
		$loginsql .= " (`cid`,`userid`,`appid`,`agentgame`,`imei`,`device`,`login_time`,`reg_time`)";
		$loginsql .= " VALUES ";
		$loginsql .= " (:cid,:userid,:appid,:agentgame,:imei,:device,:login_time,:reg_time)";    
		$db->query($loginsql, $logindata);
        
		$cppassword = base64_encode($cppassword);
        $password = Response::encode($cppassword, Response::fixedArr());

     
        $db->CloseConnection();
        $arr['code'] = '1';
		$arr['data'] = $lm_username;
		$arr['msg'] = '注册成功';
		echo json_encode($arr);
		exit;
    }
// }

		$db->CloseConnection();
        $arr['code'] = '0';
		$arr['data'] = '';
		$arr['msg'] = '注册失败';
		echo json_encode($arr);
		exit;
?>