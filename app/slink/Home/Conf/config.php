<?php
return array(
    
    'MODULE_ALLOW_LIST' => array('Home'), // 允许访问的模块
    'DEFAULT_MODULT' => 'Home',
    'URL_ROUTER_ON' => true, // url路由开关
    /* 缩短路径 */
    'URL_ROUTE_RULES' => array(
        //'u/[:i' => 'Index/index',
        //'u/:i$' => 'Index/index',
        'u/:i' => 'Index/index', // 首页
        //'/^u\/(\w{2}$)'=> 'Index/index?i=:1'
    ),
);