<?php
/**
 * 获取客服电话qq信息
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

$appid = isset($urldata['a']) ? $urldata['a'] : ''; // appid
$agentgame=isset($urldata['b']) ? $urldata['b'] : '';
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


$nowtime=time();
$sql = "select *  from ".$dbname['dbname'].".c_proclamation  where appid='".$appid."' and popup =1 and start_time<'".$nowtime."' and end_time >'".$nowtime."' order by create_time desc limit 0,1";
$pmation = $db->row($sql);
$agentarray= explode(",", $pmation['agentname']); 

if($pmation){
	$data['a'] = $pmation['title'];
	$data['b'] = $pmation['content'];
	$data['c'] = $pmation['create_time'];
	$data['d'] = $pmation['start_time'];
	$data['e'] = $pmation['end_time'];
	if($agentgame != 'default'){
		$sql="select *  from ".$dbname['dbname'].".c_agentlist  where appid='".$appid."' and agentgame='".$agentgame."' limit 0,1 ";
		$agentdata = $db->row($sql);
		$agentname=$agentdata['agentname'];  
		$isin = in_array($agentname,$agentarray);
		if ($isin) {
		$db->CloseConnection();
		return Response::show("1", $data, "查询成功");
		}
	}else{
		$db->CloseConnection();
		return Response::show("1", $data, "查询成功");
	}
}

$db->CloseConnection();
return Response::show("1", $rdata, "查询失败，请重试");

?>