<?php
/**
 * Wxpay.php UTF-8
 * 微信支付回调地址
 *
 * @date    : 2017/6/7 20:52
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : wuyonghong <wyh@huosdk.com>
 * @version : HUOOA 1.0
 */
namespace app\pay\controller;

use app\common\controller\Base;

class Wxpay extends Base {
    function _initialize() {
        parent::_initialize();
    }

    public function notifyurl() {
        $_class = new \huosdk\pay\Wxpay();
        $_class->notifyUrl();
    }


    public function notifyUrlTest() {
        $_class = new \huosdk\pay\Wxpay();
        $_class->notifyUrlTest();
    }

    /**
     * 校验订单是否OK
     *
     * @return mixed
     */
    public function checkurl() {
        $_order_id = $this->request->param('order_id');
        $_timestamp = $this->request->param('timestamp');
        if (empty($_order_id) || empty($_timestamp)) {
            return $this->fetch('wxpay/showurl');
        }
        /* 响应时间超过5s 超时 */
        if ($_timestamp < time() - 5) {
            return $this->fetch('wxpay/showurl');
        }
        $_status = \think\Db::name('pay')->where('order_id', $_order_id)->value('status');
        if (empty($_status) || PAYSTATUS_SUCCESS != $_status) {
            echo PAYSTATUS_NOPAY;
            exit;
        }
        echo PAYSTATUS_SUCCESS;
        exit;
    }

    /**
     * 前端通知地址
     *
     * @return mixed
     */
    public function returnurl() {
        $_is_android = $this->request->isAndroid();
        
        $_order_id = $this->request->param('order_id');
        $_token = $this->request->param('return_token');
        $_refer = $this->request->server('HTTP_REFERER');
        $_is_h5 = $this->request->param('is_h5',0);
        if ($_is_android && $_is_h5  == 0) {
            exit;
        }

        $this->assign('refer', $_refer);
        $_pay_info = \think\Db::name('pay')->where('order_id', $_order_id)->find();
        $_status = (!empty($_pay_info) && isset($_pay_info['status'])) ? $_pay_info['status'] : '';
        if (PAYSTATUS_SUCCESS == $_status) {
            $_msg = "您已充值成功 请手动返回游戏！";
        } else {
            $_msg = "亲，您尚未支付成功，请点击关闭！";
        }
        $_rs = $this->se_class->initSession($_token);
        if (false === $_rs) {
            $_msg = "亲，您支付失败了，请点击关闭按钮重试或返回游戏！";
            $this->assign('msg', $_msg);
            $this->assign('return_token', '');
            return $this->fetch();
        }
        $_return_token = urldecode(\think\Session::get('wxpay_return_token'));
        if (2 == $_status){
            $_bt = "CheckFileSucc";
        }else{
            $_bt = "CheckFileFailed";
        }
        $_return_token .= '&st='.$_status.'&bt='.$_bt;
        $_info['order_id'] = $_order_id;
        if (empty($_status) || PAYSTATUS_SUCCESS != $_status) {
            $_info['order_id'] = 'aa';
        }
        if (3 == \think\Session::get('pay_from', 'order')) {
            $_return_token = '';
        }
//        if ($_return_token&&1 == \think\Session::get('pay_sdk', 'order')&&\think\Session::get('packagename')) {
//            //旧接口进来的
//            $_return_token_a = \think\Session::get('packagename').substr($_return_token,strpos($_return_token,':'));
//            $_return_token=$_return_token_a;
//        }
        $this->assign('return_token', $_return_token);
        $this->assign('info', $_info);
        $this->assign('msg', $_msg);
        $this->assign('is_h5', $_is_h5);
        return $this->fetch();
    }

    public function gotoweixin() {
        $_is_android = $this->request->isAndroid();
        $_order_id = $this->request->param('order_id');
        $_now_token = $this->request->param('now_token');
        $_return_url = $this->request->param('return_url');
        $this->assign('token', $_now_token);
        $this->assign('return_url', $_return_url);
        $this->assign(
            'query_url',
            SDKSITE.url('Pay/Wxpay/checkurl', array('order_id' => $_order_id))
        );
        $_callpay = 2;
        if ($_is_android) {
            $_callpay = 1;
            $this->assign('_order_json', json_encode(array('order_id' => $_order_id, 'status' => 1)));
        } else {
            $this->assign('_order_json', '');
        }
        $this->assign('call_pay', $_callpay);
        $this->assign('order_id', $_order_id);

        return $this->fetch();
    }

        public function gotoh5weixin() {

        $_is_android = $this->request->isAndroid();
        $_order_id = $this->request->param('order_id');
        $_now_token = $this->request->param('now_token');
        $_return_url = $this->request->param('return_url');
        $this->assign('token', $_now_token);
        $this->assign('return_url', $_return_url);
        $this->assign(
            'query_url',
            SDKSITE.url('Pay/Wxpay/checkurl', array('order_id' => $_order_id))
        );
        $_callpay = 2;
        if ($_is_android) {
            $_callpay = 1;
            $this->assign('_order_json', json_encode(array('order_id' => $_order_id, 'status' => 1)));
        } else {
            $this->assign('_order_json', '');
        }
        $this->assign('call_pay', $_callpay);
        $this->assign('order_id', $_order_id);

        return $this->fetch();
    }
}