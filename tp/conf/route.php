<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
return [
    /* 第一次请求接口 */
    ':version/install$'              => 'player/:version.System/install', /* 第一次请求接口  */
    /* 初始化接口 */
    ':version/system/startup'        => 'player/:version.System/open', /* 初始化接口  */
    /* 公告信息 */
    ':version/system/notice'         => 'player/:version.System/notice', /* 公告信息  */
    /* 短信 */
    ':version/sms/send'              => 'api/:version.Sms/send', /* 发送手机短信  */
    /*玩家接口*/
    ':version/user/registerone'      => 'player/:version.Userreg/regOne', /* 一键注册  */
    ':version/user/register'         => 'player/:version.Userreg/register', /* 普通注册  */
    ':version/user/registermobile'   => 'player/:version.Userreg/regMobile', /* 手机注册  */
    ':version/user/login'            => 'player/:version.Userlogin/login', /* 普通登陆  */
    ':version/web/login'             => 'web/:version.Userlogin/login', /* web 普通登陆  */
    ':version/web/loginoauth'        => 'web/v1.Userlogin/loginOauth', /* web 普通登陆  */
    ':version/web/automaticLogin'    => 'web/v1.Userlogin/automaticLogin', /* web 缓存登陆  */
    ':version/web/logout'    => 'web/v1.Userlogin/logout', /* web logout  */
    ':version/web/register'          => 'web/:version.Userreg/register', /* web 普通注册   */
    ':version/web/open'              => 'web/:version.System/open',     /* web 初始化接口  */
    ':version/web/sms/send'          => 'web/:version.Sms/send',        /* web 发送手机短信  */
    ':version/web/sms/send_up'       => 'web/:version.Sms/send_update_password',        /* web 发送手机短信 验证是否绑定手机号 */
    ':version/web/regmobile'         => 'web/:version.Userreg/regMobile', /* web  手机注册  */
    ':version/web/forgetpwd'         => 'web/:version.Forgetpwd/update', /* web  修改密码  */
    ':version/web/sms/send_bindphone'=> 'web/:version.Sms/send_sms_bindmobile', /* web  绑定手机 发送短信  */
    ':version/web/update_bindphone'  => 'web/:version.Userreg/update_bindmobile', /* web  绑定手机  更换绑定手机号*/
    ':version/user/loginmobile'      => 'player/:version.Userlogin/loginMobile', /* 手机登陆  */
    ':version/user/loginoauth'       => 'player/:version.Userlogin/loginOauth', /* 第三方登陆  */
    ':version/user/logout'           => 'player/:version.Userlogin/logout', /* 登出  */
    /*玩家游戏数据*/
    ':version/user/uproleinfo'       => 'player/:version.Userrole/set', /* 上传角色信息  */
    /*支付*/
    ':version/pay/sdkpay'            => 'pay/Sdkpay/index', /* 游戏预下单  */
    ':version/pay/webpay'            => 'pay/Webpay/index', /* 游戏预下单  */
    ':version/pay/qrcode'          => 'pay/Webpay/qrcode', /* 玩家支付  */
    ':version/pay/web_preorder'          => 'pay/Webpay/pay', /* 玩家支付  */
    ':version/get/goods'           => 'pay/Sdkpay/index', /* 游戏预下单  */
    ':version/pay/preorder'          => 'pay/Sdkpay/pay', /* 玩家支付  */
    'pay/preorder'                   => 'pay/Sdkpay/pay', /* 玩家支付  */
    'get/preorder'                 => 'pay/Sdkpay/pay', /* 玩家支付  */
    ':version/get/preorder'        => 'pay/Sdkpay/pay', /* 玩家支付  */
    'alipay/notify'                  => 'Pay/alipay/notifyurl', /* 支付宝支付回调地址  */
    'alipay/notify_test'                  => 'Pay/alipay/notifyurl_test', /* 支付宝支付回调地址  */
    'alipay/return'                  => 'Pay/alipay/returnurl', /* 支付宝支付通知地址  */
    'alipay/showurl'                 => 'Pay/alipay/showurl',
    'alipay/show'                    => 'Pay/alipay/showurl', /* 支付宝支付通知地址  */
    'alipay/outurl'                  => 'Pay/alipay/outweburl', /* 支付宝支付通知地址  */
    'now/notify'                     => 'Pay/Nowpay/notifyurl', /* 现在支付通知地址  */
    'now/h5notify'                   => 'Pay/Nowpay/notifyh5url', /* 现在支付通知地址  */
    'now/return'                     => 'Pay/Nowpay/returnurl', /* 现在支付通知地址  */
    'now/gotoweixin'                 => 'Pay/Nowpay/gotoweixin', /* 现在支付跳转微信  */
    'now/check'                      => 'Pay/Nowpay/checkurl', /* 现在支付校验订单是否OK */
    'spay/notify'                    => 'Pay/Spay/notifyurl', /* 威富通支付通知地址  */
    'spay/return'                    => 'Pay/spay/returnurl', /* 威富通支付通知地址  */
    'wxpay/notify'                   => 'Pay/Wxpay/notifyurl', /* 微信支付通知地址  */
    'wxpay/notifyUrlTest'            => 'Pay/Wxpay/notifyUrlTest', /* 微信支付通知地址  */
    'alipay/submit'                  => 'Pay/alipay/submit', /* 支付宝提交支付地址  */
    'payeco/notify'                  => 'Pay/Payeco/notifyurl', /* 易联支付通知地址  */
    'heepay/notify'                  => 'Pay/Heepay/notifyurl', /* 汇付宝通知地址  */
    ':version/pay/queryorder'        => 'player/:version.Order/queryOrder', /* 查询支付结果  */
    ':version/apppay/preorder'       => 'pay/Applepay/preorder', /* 非 web预下单  */
    ':version/apppay/checkorder'     => 'pay/Applepay/checkorder', /* 苹果原生支付验单  */
    'unionpay/notify'                => 'Pay/unionpay/notifyurl', /* 银联支付回调地址  */
    'unionpay/return'                => 'Pay/unionpay/returnurl', /* 银联支付通知地址  */
    'zwxpay/notify'                  => 'Pay/zwxpay/notifyurl', /* 梓微兴支付回调地址  */
    'zwxpay/return'                  => 'Pay/zwxpay/returnurl', /* 梓微兴支付通知地址  */
    /* 浮点 */
    'v7/web/user/index'        => 'wap/v7.User/index', /* 用户中心  */
    'v7/web/bbs/index'         => 'wap/v7.Bbs/index', /* 论坛  */
    'v7/web/gift/index'        => 'wap/v7.Gift/index', /* 礼包中心  */
    'v7/web/help/index'        => 'wap/v7.Help/index', /* 客服中心  */
    'v7/web/forgetpwd/index'         => 'wap/v7.Forgetpwd/index', /* 找回密码  */
    'v7/web/code/index'        => 'wap/v7.Code/index', /* 填写邀请码 20170105 wuyonghong */
    ':version/web/strategy/index'    => 'wap/:version.Strategy/index', /* 打开浮点-攻略 20170310 wuyonghong */
    ':version/web/h5/index'          => 'wap/:version.H5/index', /* 打开浮点-跳转h5 20170310 wuyonghong */
    /* CP 校验 */
    'cp/user/check'                  => 'cp/v7.Cp/check', /* CP用户校验  */
    ':version/cp/user/check'         => 'cp/:version.Cp/check', /* CP用户校验  */
    ':version/cp/payback/test$'      => 'cp/:version.Payback/notify', /* 支付回调测试  */
    'cp/payback/test$'               => 'cp/v7.Payback/notify', /* 支付回调测试  */
    /* 下载地址 */
    '[down]'                         => [
        'downid/:downid/gameid/:gameid$' => ['api/v7.Gamedown/down', [], ['downid' => '\d+', 'gameid' => '\d+']],
        'downid/:downid$'                => ['api/v7.Gamedown/down', [], ['downid' => '\d+']],
        'gameid/:gameid$'                => ['api/v7.Gamedown/down', [], ['gameid' => '\d+']],
        '__miss__'                       => 'api/v7.Gamedown/down',
    ],
    'downid/:downid/gameid/:gameid$' => 'api/v7.Gamedown/down', /* CP用户校验  */
    'downid/:downid$'                => 'api/v7.Gamedown/down', /* CP用户校验  */
    'gameid/:gameid$'                => 'api/v7.Gamedown/down', /* CP用户校验  */

        'a/start'                        => 'apple/v1.System/open', /* 苹果 初始化接口  */
    'a/user/rg'                      => 'apple/v1.Userreg/register', /* 苹果 玩家普通注册  */
    'a/user/rgm'                     => 'apple/v1.Userreg/regMobile', /* 苹果  手机注册  */
    'a/user/lga'                     => 'apple/v1.Userlogin/loginOauth', /* 苹果   第三方登陆  */
    'a/user/lg'                      => 'apple/v1.Userlogin/login', /*  苹果  普通登陆(login)  */
    'a/user/lgout'                   => 'apple/v1.Userlogin/logout', /* 苹果 登出  */
    'a/user/upinfo'                  => 'apple/v1.Userrole/set', /* 苹果 上传角色信息  */
    'a/sms/send'                     => 'apple/v1.Sms/send', /* 苹果 发送手机短信  */
    'a/gen'                          => 'apple/v1.Applepay/preorder', /* 苹果  预下单  */
    'a/co'                           => 'apple/v1.Applepay/checkorder', /* 苹果  验单  */
    'a/crd'                          => 'apple/v1.sdkpay/preorder', /* 苹果  WEB预下单  */
    'a/goodgen'                      => 'apple/v1.sdkpay/pay', /* 苹果  WEB支付  */
    'a/qo'                           => 'apple/v1.Order/queryOrder', /* 查询支付结果  */
    'appstore/getupdate'             => 'appstore/v1.Appstore/update',  
    'appstore/goodgen'             => 'appstore/v1.Appstore/pay',  
    'g/start'                        => 'apple/v1.System/open', /* 苹果 初始化接口  */
    'g/user/rg'                      => 'apple/v1.Userreg/register', /* 苹果 玩家普通注册  */
    'g/user/rgm'                     => 'apple/v1.Userreg/regMobile', /* 苹果  手机注册  */
    'g/user/lga'                     => 'apple/v1.Userlogin/loginOauth', /* 苹果   第三方登陆  */
    'g/user/lg'                      => 'apple/v1.Userlogin/login', /*  苹果  普通登陆(login)  */
    'g/user/lgout'                   => 'apple/v1.Userlogin/logout', /* 苹果 登出  */
    'g/user/upinfo'                  => 'apple/v1.Userrole/set', /* 苹果 上传角色信息  */
    'g/sms/send'                     => 'apple/v1.Sms/send', /* 苹果 发送手机短信  */
    'g/gen'                          => 'apple/v1.Applepay/preorder', /* 苹果  预下单  */
    'g/co'                           => 'apple/v1.Applepay/checkorder', /* 苹果  验单  */
    'g/crd'                          => 'apple/v1.sdkpay/preorder', /* 苹果  WEB预下单  */
    'g/goodgen'                      => 'apple/v1.sdkpay/pay', /* 苹果  WEB支付  */
    'g/qo'                           => 'apple/v1.Order/queryOrder', /* 查询支付结果  */
];
