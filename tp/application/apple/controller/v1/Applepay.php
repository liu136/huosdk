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
namespace app\apple\controller\v1;

use app\common\controller\Baseapple;
use think\Session;
use think\Cache;

class Applepay extends Baseapple {
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
            'role_name'
        );
        $_pay_data = $this->getParams($_key_arr);
        Session::set('order', $this->rq_data['orderinfo']);
        Session::set('order_time', time(), 'order');
        Session::set('role', $this->rq_data['roleinfo']);
        $_pay_class = new \huosdk\pay\Pay();
        //获取切换状态
        $_pay_switch = $_pay_class->getPayswitch($_pay_data['app_id'], $_pay_data['product_price']);
        $_rdata['pay_switch'] = $_pay_switch;
        $PayFunctionStr = $_pay_class->getPayFunctionStr($_pay_data['app_id']);
        $start_str=$PayFunctionStr['code_str_start']; //混淆代码的前缀
        $end_str=$PayFunctionStr['code_str_end'];//混淆代码的后缀
        $session_key=md5(uniqid(hs_random(6)));
        $SDK_STR= $end_str;


        $_rdata['apple_class_name'] = "{$start_str}StoreObserver{$end_str}";
        $_rdata['apple_method_name'] = "buyWith{$start_str}OrderID:Good{$start_str}Token{$end_str}:";
        $_rdata['apple_instace_name'] = "{$start_str}SdksharedInstance{$end_str}";
        
        //如果是需要切换，直接返回
        if (1 == $_pay_switch) {
            $cacheTime=120;
            Cache::set($session_key,$_pay_data,$cacheTime);
            Cache::set($session_key."_rq_data", $this->rq_data ,$cacheTime);
           // Cache::set($session_key."_key",$this->auth_key,$cacheTime);

            $_rdata['order_id'] = "http://".$_SERVER['SERVER_NAME']."/api/appstore/getupdate?uptoken=". $session_key;
            $_rdata['paytoken'] = md5(uniqid(hs_random(6)));
           // $_rdata['apple_class_name'] = "Game_MainViewController";
            $_rdata['apple_class_name'] = "{$start_str}SDkTool{$end_str}";
            $_rdata['apple_instace_name'] = "Instance{$start_str}SDkTool{$end_str}";
            $_rdata['apple_method_name'] = "{$start_str}OpenAppStoreUpdate:{$start_str}SdkMsg{$end_str}:";
            return $this->payReturn($_rdata['order_id'],$_rdata['pay_switch'],$_rdata['paytoken'],$_rdata['apple_class_name'],$_rdata['apple_method_name'],$_rdata['apple_instace_name'],$SDK_STR);

        }
        // sdk预下单
        $_rs = $_pay_class->sdkPreorder($_pay_data);
        if (false == $_rs) {
            return hs_api_responce('1000', '下单失败');
        }
        $_rdata['order_id'] = Session::get('order_id', 'order');
        $_rdata['paytoken'] = md5(uniqid(hs_random(6)));
        Session::set('paytoken', $_rdata['paytoken'], 'order');
        Session::set('order_time', time(), 'order');
        return $this->payReturn($_rdata['order_id'],$_rdata['pay_switch'],$_rdata['paytoken'],$_rdata['apple_class_name'],$_rdata['apple_method_name'],$_rdata['apple_instace_name'],$SDK_STR);
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
            'paytoken'
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
            $_payway = 'appletest';
        } else {
            $_payway = 'applepay';
            $_is_sandbox == 2;
        }
        /* 1更新支付方式 */
        $_p_class = new  \huosdk\pay\Pay();
        $_p_class->upPayway($_order_id, $_payway);
        /* 2 验证订单是否通过 */
        $_pay_class = new \huosdk\pay\Applepay($_is_sandbox);
        $_check_rs = $_pay_class->clientPay($_appverifystr, $_order_id);
        /* 3通知CP */
        if ($_check_rs) {
            /* 验证订单合法性 是否与产品对应上 */
            $_amount = $_pay_class->getProductprice($_check_rs['product_id'], $_order_id);
            if (false == $_amount || 0.01 > $_amount) {
                return hs_api_responce('434', '验单失败');
            }
            $_rs = $_p_class->sdkNotify($_order_id, $_amount, $_trans_id);
            if (false == $_rs) {
                return hs_api_responce('1000', '验单失败 内部错误');
            }
        }
        /* 4 返回信息给客户端 */
        $_rdata = $_p_class->queryOrder($_order_id);
        if (false == $_rdata) {
            return hs_api_responce('434', '验单失败');
        }
        return $this->qoReturn($_rdata["order_id"],$_rdata["status"],$_rdata["cpstatus"]);
    }
}