<?php

/**
 * 接口通讯的基础参数配置；
 * 这些参数正常情况不需要做调整
 */
class ConstantsClient {
  //----接口调用的一些固定常量----
  //签名数据编码
  private static $PAYECO_DATA_ENCODE = "UTF-8";

  //连接超时，10秒
  private static $CONNECT_TIME_OUT = 10;

  //响应超时时间，60秒
  private static $RESPONSE_TIME_OUT = 60;

  //接口版本
  private static $COMM_INTF_VERSION = "2.0.0";

  static function getPayecoDataEncode() {
  	return self::$PAYECO_DATA_ENCODE;
  }
  static function getConnectTimeOut() {
  	return self::$CONNECT_TIME_OUT;
  }
  static function getResponseTimeOut() {
  	return self::$RESPONSE_TIME_OUT;
  }
  static function getCommIntfVersion() {
  	return self::$COMM_INTF_VERSION;
  }
  static function getCaCertFileName() {
  	//return getcwd().'\\cacert.pem';   // 【\\】在在linux下使用可能会存在兼容性的问题
	return dirname(getcwd()).DIRECTORY_SEPARATOR."thinkphp".DIRECTORY_SEPARATOR."Core".DIRECTORY_SEPARATOR."Library".DIRECTORY_SEPARATOR."Vendor".DIRECTORY_SEPARATOR."Pay".DIRECTORY_SEPARATOR."Payecoh5".DIRECTORY_SEPARATOR.'cacert.pem';
  	//return dirname (getcwd()).DIRECTORY_SEPARATOR.'cacert.pem';
  }
}
?>

