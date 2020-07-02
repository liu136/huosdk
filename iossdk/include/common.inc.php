<?php

	header("Content-Type: text/html; charset=utf-8");
	define('SYS_ROOT',substr(dirname(__FILE__), 0, -7));
	define('CLASS_PATH','include/class/');
	define('IN_SYS',TRUE);
	
	require_once(SYS_ROOT.CLASS_PATH.'Db.class.php');
	require_once(SYS_ROOT.CLASS_PATH.'Cache.class.php');
	//require_once('./class/Library.class.php');	
	if(file_exists(SYS_ROOT.'include/ptbconfig.php')){
		require_once SYS_ROOT.'include/ptbconfig.php';
	}
	
	require_once SYS_ROOT.'include/config.inc.php';
	
	require_once SYS_ROOT.'include/sdkfunction.php';
	
	$db = new DB();
	
?>