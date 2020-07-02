<?php

/**
 * Applepay.php UTF-8
 * 苹果支付下单 验单
 *
 * @date    : 2016年12月20日下午4:20:49
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : wuyonghong <wyh@huosdk.com>
 * @version : HUOSDK 7.0
 * @modified: 2016年12月20日下午4:20:49
 */



namespace app\pay\controller;

use app\common\controller\Baseplayer;
use think\Session;
use think\Cache;
class Applepay extends Baseplayer {
    function _initialize() {
        parent::_initialize();
    }

    /*
     * 玩家打开支付页面预下单
     */
    function preorder() {
        $_key_arr = array(
            'app_id',
            'client_id',
            'from',
            'user_token',
            'timestamp',
            'device_id',
            'userua',
            'cp_order_id',
            'product_price',
            'product_count',
            'product_id',
            'product_name',
            'product_desc',
            'role_type',
            'server_id',
            'server_name',
            'role_id',
            'role_name',
            'version'
        );
        $_pay_data = $this->getParams($_key_arr);
        Session::set('order', $this->rq_data['orderinfo']);
        Session::set('order_time', time(), 'order');
        Session::set('role', $this->rq_data['roleinfo']);
        $_pay_class = new \huosdk\pay\Pay();
        //订单数据转json缓存
        $session_key=md5(uniqid(hs_random(6)));
        
        //获取切换状态
        $_pay_switch = $_pay_class->getPayswitch($_pay_data['app_id'],$_pay_data['product_price'],$_pay_data['version']);

        $_rdata['userid'] = $session_key;
        // $_rdata['pay_switch'] = $_pay_switch;
        $_rdata['verification'] = $_pay_switch;//修改为另一个字段，避免出现查到切换支付
        //如果是需要切换，直接返回
        if (1 == $_pay_switch){
            $_rdata['order_id'] = "";
            $_rdata['paytoken'] = "";
            $cacheTime=120;
            Cache::set($session_key,$_pay_data,$cacheTime);
            Cache::set($session_key."_rq_data", $this->rq_data ,$cacheTime);
            Cache::set($session_key."_key",$this->auth_key,$cacheTime);
            //Session::flash($session_key,json_encode($_pay_data),'param_json');
            return hs_player_responce('1888', '请稍等！', $_rdata, $this->auth_key);
        }

        // sdk预下单
        $_rs = $_pay_class->sdkPreorder($_pay_data);
        if (false == $_rs) {
            return hs_player_responce('1000', '下单失败');
        }
        $_rdata['order_id'] = Session::get('order_id', 'order');
        $_rdata['paytoken'] = md5(uniqid(hs_random(6)));

        Session::set('paytoken', $_rdata['paytoken'], 'order');
        Session::set('order_time', time(), 'order');
        return hs_player_responce('200', '下单成功', $_rdata, $this->auth_key);
    }

    /*
     * 玩家选择支付方式 直接下单
     *
     * @return $this
     */
    public function checkorder() {
        $_key_arr = array(
            'app_id',
            'client_id',
            'from',
            'user_token',
            'timestamp',
            'device_id',
            'userua',
            'order_id',
            'trans_id',
            'appverifystr',
            'paytoken',
        );
        $_pay_data = $this->getParams($_key_arr);
        $_order_id = $_pay_data['order_id'];
        $_trans_id = $_pay_data['trans_id'];
        $_is_sandbox = isset($_pay_data['is_sandbox']) ? $_pay_data['is_sandbox'] : 2;
        $_appverifystr = $_pay_data['appverifystr'];
        $_payway = 'applepay';
        /* 验证paytoken */
//         $_s_paytoken = Session::get('paytoken', 'order');
//         if ( $_pay_token != $_s_paytoken) {
//             return hs_pay_responce(0,"非法请求,参数错误");
//         }
        if (1 == $_is_sandbox) {
            $_is_sandbox == 1;
            $_payway = 'applepay';
        } else {
            $_payway = 'appletest';
            $_is_sandbox == 2;
        }
        /* 1更新支付方式 */
        $_p_class = new  \huosdk\pay\Pay();
        $_p_class->upPayway($_order_id, $_payway);
        /* 2 验证订单是否通过 */
        $_pay_class = new \huosdk\pay\Applepay($_is_sandbox);
        $_check_rs = $_pay_class->clientPay($_appverifystr);
        /* 3通知CP */
        if ($_check_rs) {
            /* 验证订单合法性 是否与产品对应上 */
            $_amount = $_pay_class->getProductprice($_check_rs['product_id'], $_order_id);
            if (false == $_amount || 0.01 > $_amount) {
                return hs_player_responce('434', '验单失败');
            }
            $_rs = $_p_class->sdkNotify($_order_id, $_amount, $_trans_id);
            if (false == $_rs){
                return hs_player_responce('1000', '验单失败 内部错误');
            }
        }
        /* 4 返回信息给客户端 */
        $_rdata = $_p_class->queryOrder($_order_id);
        if (false == $_rdata) {
            return hs_player_responce('434', '验单失败');
        }

        return hs_player_responce('200', '查询成功', $_rdata, $this->auth_key);
    }
}