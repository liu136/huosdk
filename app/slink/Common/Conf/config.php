<?php
if(file_exists(SITE_PATH."conf/db.php")){
    $db=include SITE_PATH."conf/db.php";
}else{
    $db=array();
}
if(file_exists(SITE_PATH."conf/slink/config.php")){
    $runtime_config=include SITE_PATH."conf/links/config.php";
}else{
    $runtime_config=array();
}

if (file_exists(SITE_PATH."conf/client/route.php")) {
    $routes = include SITE_PATH.'conf/client/route.php';
} else {
    $routes = array();
}

/* 引入公共配置  */
if (file_exists(SITE_PATH."conf/comconfig.php")) {
    $comconf = include SITE_PATH."conf/comconfig.php";
} else {
    $comconf = array();
}
return  array_merge($comconf, $db,$runtime_config);