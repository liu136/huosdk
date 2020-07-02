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

$sql = "select tel as a, qq as b from ".$dbname['dbname'].".c_contact order by id desc limit 0,1";
$data = $db->row($sql);

$data['c'] = '';
if ($data) {
    $db->CloseConnection();
	return Response::show("1", $data, "查询成功");
}

$db->CloseConnection();
return Response::show("1", $rdata, "查询失败，请重试");

?>