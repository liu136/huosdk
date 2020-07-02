<?php
/**
 * config.php UTF-8
 * 微信原生支付配置文件
 *
 * @date    : 2017/6/7 15:44
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : wuyonghong <wyh@huosdk.com>
 * @version : HUOOA 1.0
 */
return array(
    'app_id'          => 'wx5725ab75fc85b47b', /* 绑定支付的APPID（必须配置，开户邮件中可查看）*/
    'mch_id'          => '1463645302', /* 商户号（必须配置，开户邮件中可查看） */
    'key'             => '3429c3enj7xxkx40923qd1ib3qjm9ipp', /* 商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置） */
    'app_secret'      => '68cd4d69ef46d5706449cd18246b554e', /* 公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），*/
    'curl_proxy_host' => '0.0.0.0',
    'curl_proxy_port' => '0',
    'report_levenl'   => '1',
    "6009"=>array(
    			    'app_id'          => 'wxda09c952acbd93a3', /* 绑定支付的APPID（必须配置，开户邮件中可查看）*/
				    'mch_id'          => '1501435371', /* 商户号（必须配置，开户邮件中可查看） */
				    'key'             => 'mDGDXpq3F2StyjwH6wMjoIafc5CxVzzt', /* 商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置） */
				    'app_secret'      => '9184b96c7c6d953d8f00d931b719fe11', /* 公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），*/
				    'curl_proxy_host' => '0.0.0.0',
				    'curl_proxy_port' => '0',
				    'report_levenl'   => '1',
    	)
);
