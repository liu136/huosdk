<?php
/**
 * 注册接口
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
$appid = isset($urldata['b']) ? $urldata['b'] : ''; // 
$type = isset($urldata['c']) ? $urldata['c'] : ''; // 
$gamedata['service'] = isset($urldata['d']) ? $urldata['d'] : ''; // 
$gamedata['role'] = isset($urldata['e']) ? $urldata['e'] : ''; // 
$gamedata['grade'] = isset($urldata['f']) ? $urldata['f'] : ''; // 
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
if (empty($username)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "用户名不能为空");
}

// 游戏不能为空
if (empty($gamedata)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "游戏信息不能为空");
}

// appid不能为空
if (empty($appid)) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "游戏ID不能为空");
}

// 更具用户名查找用户
$userdata['username'] = $username;
$sql = "select id, agentid from " . $dbname['dbname'] . ".c_members where username=:username";
$data = $db->row($sql, $userdata);

// 用户名不存在
if (empty($data['id'])) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "用户名不存在");
}

//$gamedata = get_object_vars(json_decode($gamedata));
$infodata['type'] = $type;
$infodata['cid'] = $dbname['cid'];
$infodata['userid'] = $data['id'];
$infodata['appid'] = $appid;
$infodata['service'] = $gamedata['service'];
$infodata['role'] = $gamedata['role'];
$infodata['grade'] = $gamedata['grade'];
$infodata['update_time'] = time();

// 查询角色信息
$gdata['appid'] = $appid;
$gdata['userid'] = $data['id'];
$gdata['service'] = $gamedata['service'];
$gdata['role'] = $gamedata['role'];

// 区服不能为空
if (empty($gdata['service'])) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "区服不能为空");
}

// 角色不能为空
if (empty($gdata['role'])) {
    $db->CloseConnection();
    return Response::show("-2", $rdata, "角色不能为空");
}

$sql = "select id from " . $dbname['dbname'] . ".c_mgameinfo where appid=:appid AND userid=:userid AND service=:service AND role=:role";
$data = $db->row($sql, $gdata);

// 角色存在
if (!empty($data['id'])) {
	
	$upsql= "UPDATE `".$dbname['dbname']."`.`c_mgameinfo` SET update_time=:update_time, grade=:grade where id=:id";
    $db->bind("update_time", time());
    $db->bind("grade", $infodata['grade']);
    $db->bind("id", $data['id']);
    $db->query($upsql);
    
	$rdata = array(
            'a' => $infodata['service'], 
            'b' => $infodata['role']
    );
    $db->CloseConnection();
    return Response::show("1", $rdata, "操作成功");
}else{

	$insql = " INSERT INTO `".$dbname['dbname']."`.c_mgameinfo";
    $insql .= " (`type`, `cid`,`userid`,`appid`,`service`,`role`,`grade`,`update_time`)";
    $insql .= " VALUES";
    $insql .= " (:type, :cid,:userid,:appid,:service,:role,:grade,:update_time)";
    
    $rs = $db->query($insql, $infodata);
    
	$rdata = array(
            'a' => $infodata['service'], 
            'b' => $infodata['role']
    );

    $db->CloseConnection();
    return Response::show("1", $rdata, "操作成功");
}

$db->CloseConnection();
return Response::show("-2", $rdata, "操作失败");
