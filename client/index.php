<?php
/**
 * 入口文件
 * 
 */
if (ini_get('magic_quotes_gpc')) {
	function stripslashesRecursive(array $array){
		foreach ($array as $k => $v) {
			if (is_string($v)){
				$array[$k] = stripslashes($v);
			} else if (is_array($v)){
				$array[$k] = stripslashesRecursive($v);
			}
		}
		return $array;
	}
	$_GET = stripslashesRecursive($_GET);
	$_POST = stripslashesRecursive($_POST);
}

//开启调试模式
define("APP_DEBUG", true);
//网站当前路径
define('SITE_PATH', substr(dirname(__FILE__), 0, -7)."/");

//项目路径，不可更改
define('APP_PATH', SITE_PATH . 'app/client/');
//项目相对路径，不可更改
define('DAMAITHINK_PATH',   SITE_PATH.'thinkphp/');
//
define('SPAPP',   '../app/client/');
//项目资源目录，不可更改
define('SPSTATIC',   SITE_PATH.'client/public/');
//定义缓存存放路径
define("RUNTIME_PATH", SITE_PATH . "data/runtime/client/");
//静态缓存目录
define("HTML_PATH", SITE_PATH . "data/runtime/client/Html/");

//版本号
define("DAMAI_VERSION", '2.0.0');

define("DAMAI_CORE_TAGLIBS", 'cx,Common\Lib\Taglib\TagLibSpadmin,Common\Lib\Taglib\TagLibHome');

define("UC_CLIENT_ROOT", '../api/uc_client/');

error_reporting(E_ALL); ini_set('display_errors', '1'); 

if(file_exists(UC_CLIENT_ROOT."config.inc.php")){
	include UC_CLIENT_ROOT."config.inc.php";
}

if(file_exists(SITE_PATH."conf/domain.inc.php")){
    include SITE_PATH."conf/domain.inc.php";
    ini_set('session.cookie_path', '/');
    ini_set('session.cookie_domain', DAMAI_DOMAIN);
}else{
    exit;
}

//载入框架核心文件
require DAMAITHINK_PATH.'Core/ThinkPHP.php';
//require SITE_PATH.'thinkphp_ios/Core/ThinkPHP.php';