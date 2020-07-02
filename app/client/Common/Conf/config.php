<?php

if(file_exists(SITE_PATH."conf/db.php")){
	$db=include SITE_PATH."conf/db.php";
}else{
	$db=array();
}
if(file_exists(SITE_PATH."conf/client/config.php")){
    $runtime_config=include SITE_PATH."conf/client/config.php";
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

$configs= array(
		"LOAD_EXT_FILE"=>"extend",
		'UPLOADPATH' => 'data/upload/',
		//'SHOW_ERROR_MSG'        =>  true,    // 显示错误信息
		'SHOW_PAGE_TRACE'		=> false,
		'TMPL_STRIP_SPACE'		=> true,// 是否去除模板文件里面的html空格与换行
		'THIRD_UDER_ACCESS'		=> false, //第三方用户是否有全部权限，没有则需绑定本地账号
		/* 标签库 */
		'TAGLIB_BUILD_IN' => DAMAI_CORE_TAGLIBS,
		'MODULE_ALLOW_LIST'  => array('Admin'),
 		'TMPL_DETECT_THEME'     => false,       // 自动侦测模板主题
 		'TMPL_TEMPLATE_SUFFIX'  => '.html',     // 默认模板文件后缀
		'DEFAULT_M_LAYER'       =>  'Model', // 默认的模型层名称
		'DEFAULT_C_LAYER'       =>  'Controller', // 默认的控制器层名称
		
		'DEFAULT_FILTER'        =>  'htmlspecialchars', // 默认参数过滤方法 用于I函数...htmlspecialchars
		
		'LANG_SWITCH_ON'        =>  true,   // 开启语言包功能
		'DEFAULT_LANG'          =>  'zh-cn', // 默认语言
		'LANG_LIST'				=>  'zh-cn,en-us,zh-tw',
		'LANG_AUTO_DETECT'		=>  false,
		
		'VAR_MODULE'            =>  'g',     // 默认模块获取变量
		'VAR_CONTROLLER'        =>  'm',    // 默认控制器获取变量
		'VAR_ACTION'            =>  'a',    // 默认操作获取变量
		
		'APP_USE_NAMESPACE'     =>   true, // 关闭应用的命名空间定义
		'APP_AUTOLOAD_LAYER'    =>  'Controller,Model', // 模块自动加载的类库后缀
		
        'SP_DEV_TMPL_PATH'    => 'themes/',       // 各个项目后台模板文件根目录
        
		'SP_TMPL_PATH'     		=> 'themes/',       // 前台模板文件根目录
		'SP_DEFAULT_THEME'		=> 'wwweb',       // 前台模板文件
		'SP_HOME_DEFAULT_THEME'	=> 'homeweb',       // 用户注册登陆界面
		'SP_DEV_DEFAULT_THEME'	=> 'devweb',       // 前台模板文件
		'SP_TMPL_ACTION_ERROR' 	=> 'error', // 默认错误跳转对应的模板文件,注：相对于前台模板路径
		'SP_TMPL_ACTION_SUCCESS'=> 'success', // 默认成功跳转对应的模板文件,注：相对于前台模板路径
		'SP_ADMIN_STYLE'		=> 'flat',
		'SP_ADMIN_TMPL_PATH'    => 'themes/',       // 各个项目后台模板文件根目录
		'SP_ADMIN_DEFAULT_THEME'=> 'clientweb',       // 各个项目后台模板文件
		'SP_ADMIN_TMPL_ACTION_ERROR' 	=> 'Admin/error.html', // 默认错误跳转对应的模板文件,注：相对于后台模板路径
		'SP_ADMIN_TMPL_ACTION_SUCCESS' 	=> 'Admin/success.html', // 默认成功跳转对应的模板文件,注：相对于后台模板路径
		'SP_DEV_TMPL_ACTION_ERROR' 	=> 'Dev/error.html', // 默认错误跳转对应的模板文件,注：相对于后台模板路径
		'SP_DEV_TMPL_ACTION_SUCCESS' 	=> 'Dev/success.html', // 默认成功跳转对应的模板文件,注：相对于后台模板路径
		'TMPL_EXCEPTION_FILE'   => SITE_PATH.'public/exception.html',
		
		'AUTOLOAD_NAMESPACE' => array('plugins' => './plugins/'), //扩展模块列表
		
		'ERROR_PAGE'            =>'',//不要设置，否则会让404变302
		
		'VAR_SESSION_ID'        => 'session_id',
        'SESSION_OPTIONS'=>array('domain'=>DAMAI_DOMAIN),//session配置
        'COOKIE_DOMAIN'=>DAMAI_DOMAIN,//cookie域名
        
		"UCENTER_ENABLED"		=>0, //UCenter 开启1, 关闭0
		"COMMENT_NEED_CHECK"	=>0, //评论是否需审核 审核1，不审核0
		"COMMENT_TIME_INTERVAL"	=>60, //评论时间间隔 单位s
		
		/* URL设置 */
 		'URL_CASE_INSENSITIVE'  => false,   // 默认false 表示URL区分大小写 true则表示不区分大小写
 		'URL_MODEL'             => 2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
 		// 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式，提供最好的用户体验和SEO支持
 		'URL_PATHINFO_DEPR'     => '/',	// PATHINFO模式下，各参数之间的分割符号
 		'URL_HTML_SUFFIX'       => '',  // URL伪静态后缀设置

		'VAR_PAGE'				=>"p",
		
		'URL_ROUTER_ON'			=> true,
        'URL_ROUTE_RULES'       => $routes,
		
		/*性能优化*/
		'OUTPUT_ENCODE'			=>true,// 页面压缩输出
		
		'HTML_CACHE_ON'         =>    false, // 开启静态缓存
		'HTML_CACHE_TIME'       =>    60,   // 全局静态缓存有效期（秒）
		'HTML_FILE_SUFFIX'      =>    '.html', // 设置静态缓存文件后缀
		
        'APP_SUB_DOMAIN_DEPLOY'   =>    1, // 开启子域名配置
        'APP_SUB_DOMAIN_RULES'    =>    array(
                'iosadmin'.DAMAI_DOMAIN => 'Admin',  // 域名指向Admin模块
        ),
        
		'TMPL_PARSE_STRING'=>array(
		    '__PUBLIC__' => __ROOT__.'/public',
		    '/Public/upload'=>'/data/upload',
			'__UPLOAD__' => __ROOT__.'/data/upload/',
			'__STATICS__' => __ROOT__.'/statics/',
		)
);

return  array_merge($configs,$comconf, $db,$runtime_config);
