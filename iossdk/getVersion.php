<?php
/**
 * 获取游戏版本信息
 */
include ('include/common.inc.php');

$urldata = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : '';
$urldata = get_object_vars(json_decode($urldata));

$rdata = NULL;
// 缺少参数
if (empty($urldata)) {
    return Response::show("-1", $rdata, "缺少参数");
}

$appid = isset($urldata['a']) ? $urldata['a'] : ''; // appid
$agent = isset($urldata['b']) ? $urldata['b'] : ''; // agent
$cidenty = isset($urldata['x']) ? $urldata['x'] : ''; // 渠道标识
$clientkey = isset($urldata['y']) ? $urldata['y'] : ''; // 渠道标识符
                                                        
// 校验渠道key
if (empty($cidenty) || empty($clientkey)) {
    return Response::show("-6", $rdata, "标识不能为空");
} else {
    if ($clientkey != CLIENTKEY) {
        return Response::show("-6", $rdata, "client密钥不对");
    }
}

if (empty($appid)) {
	return Response::show("-2", $rdata, "appid不能为空");
}

if (empty($agent)) {
	return Response::show("-2", $rdata, "渠道不能为空");
}

$verdata['gid'] = $appid;
$sql = "select newversions as b,size as c from c_game_version where status = 1 and appid=:gid";
$rs = $db->query($sql,$verdata);
$data = $rs[0];

if(empty($data)){
	return Response::show("-2", $rdata, "没有新版本信息");
    exit();
}

if($agent != "default"){
	$agentdata['appid'] = $appid;
	$agentdata['agentgame'] = $agent;
	$sql = "select g.pinyin,sg.filename from c_agentlist a
		 left join c_sdkgamelist sg on sg.agentid = a.id
		 left join c_game g on g.appid = a.appid
		 where g.appid =:appid and agentgame=:agentgame";

	$rs = $db->query($sql,$agentdata);
	$agentdata = $rs[0];

	if(empty($agentdata) || empty($agentdata['filename'])){
		return Response::show("-2", $rdata, "渠道信息查询失败");
		exit;
	}

	$downurl = DAMAIDOWNSITE . "/sdkgame/" . $agentdata['pinyin'] . "/" . $agentdata['filename'];
}else{
	$gamedata['appid'] = $appid;
	$sql = "select pinyin from c_game where appid =:appid";

	$rs = $db->query($sql,$gamedata);
	$downurl = DAMAIDOWNSITE . "/sdkgame/" . $rs[0]['pinyin'] . "/" . $rs[0]['pinyin']."_".$cidenty.".apk";
}


$data['a'] = $downurl;

if ($data) {
	return Response::show("1", $data, "查询成功");
	exit;
	
} else {
    return Response::show("1", $rdata, "查询失败，请重试");
    exit();
}
exit();

?>