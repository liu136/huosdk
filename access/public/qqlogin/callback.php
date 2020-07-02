<?php
require_once("./API/qqConnectAPI.php");

define('SITE_PATH', dirname(__DIR__)."/../../");
if (file_exists(SITE_PATH."conf/domain.inc.php")) {
    include SITE_PATH."conf/domain.inc.php";
} else {
    exit(SITE_PATH."conf/domain.inc.php 未找到!" );
}

if (file_exists(SITE_PATH."conf/config.inc.php")) {
    include SITE_PATH."conf/config.inc.php";
} else {
    exit(SITE_PATH."conf/config.inc.php 未找到!" );
}

$qc = new QC();
$callback=$qc->qq_callback();
$param=json_decode(base64_decode($callback['param']));
$openid=$qc->get_openid();
$app_id=$param->app_id;
$timestamp=time();
$data=array(	
	'access_token'=>$callback['access_token'],
	'openid'=>$openid,
	//'uname' =>$user_info['nickname'],
	'app_id'=>$app_id,
	'ut'=>$param->usertoken,
	'client_id'=>$param->client_id,
	'deviceType'=>$param->deviceType,
	'timestamp'=>$timestamp,
	'sign'=>md5(md5($openid.$app_id.$timestamp)."qqlogin123")
	);
$encodeData = openssl_encrypt(base64_encode(json_encode($data)),"aes-128-cbc",H5_DESRYPT_KEY,0,H5_IV);
$url=SDKSITE.'/api/v7/web/loginoauth?h5_data='.$encodeData;
header("location:{$url}");

