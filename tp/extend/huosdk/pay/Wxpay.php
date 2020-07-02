<?php
/**
 * Wxpay.php UTF-8
 * 微信原生支付
 *
 * @date    : 2017/6/7 16:01
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : wuyonghong <wyh@huosdk.com>
 * @version : HUOOA 1.0
 */
namespace huosdk\pay;

use think\Db;
use think\Session;
require_once EXTEND_PATH."pay/wxpay/WxPay.Data.php";
require_once EXTEND_PATH."phpqrcode/phpqrcode.php";

class Wxpay extends Pay {
    /**
     * 构造函数
     *
     */
    public function __construct() {
    }

    /**
     * 移动APP支付函数
     */
    public function clientPay() {
        $_class = new WxNotifyCallBack();
        $_token = $_class->unifiedOrder();
         return $this->clientAjax('wxapppay', $_token);
    }


    public function WapPay($gotoweixin_html='gotoweixin'){
        $_class = new WxNotifyCallBack();
        $_token = $_class->unifiedOrder('WAP');//需要开通 wap 支付权限
        $weixinData=json_decode($_token ,true);
        $prepayid=$weixinData['prepayid'];
        $package=$weixinData['package'];
        $noncestr=$weixinData['noncestr'];
        $sign=$weixinData['sign'];
        $url="weixin://wap/pay?prepayid=".urlencode($prepayid)."&package=".urlencode($package)."&noncestr=".urlencode($noncestr)."&sign=".urlencode($sign);
        $_param = [
            'order_id'   => Session::get('order_id', 'order'),
            'timestamp'  => time(),
            'now_token'  => $url,
            'return_url' => "",
        ];
        $_goto_weixin = config('domain.SDKSITE').url('Pay/Wxpay/'.$gotoweixin_html).'?'.http_build_query($_param);
        return $this->clientAjax('wxh5pay', $_goto_weixin);
    }



    /**
     * wap H5网页支付
     */
    public function mobilePay($payfrom = '',$gotoweixin_html='gotoweixin') {
        $_class = new WxNotifyCallBack();

        $_token = $_class->unifiedOrder('MWEB');
        if (!empty($_token) && !empty($_token['mweb_url'])) {

            Session::set('wxpay_return_token', $this->getReturnToken());
            $_return_token = \huosdk\common\Simplesec::encode(session_id(), \think\Config::get('config.HSAUTHCODE'));

           
            if($gotoweixin_html=='gotoh5weixin'){//h5的不需要跳转
                $return_url = config('domain.SDKSITE').url(
                        'Pay/Wxpay/returnurl', [
                                                 'order_id'     => Session::get('order_id', 'order'),
                                                 'return_token' =>$_return_token,
                                                 'is_h5' =>1,
                                             ]
                    );
                $_token = $_token['mweb_url'];
            }else{
                $return_url = config('domain.SDKSITE').url(
                        'Pay/Wxpay/returnurl', [
                                                 'order_id'     => Session::get('order_id', 'order'),
                                                 'return_token' =>$_return_token
                                             ]
                    );
                $_token = $_token['mweb_url'].'&redirect_url='.urlencode($return_url);
            }
             
            $_param = [
                'order_id'   => Session::get('order_id', 'order'),
                'timestamp'  => time(),
                'now_token'  => $_token,
                'return_url' => $return_url,
            ];
            $_goto_weixin = config('domain.SDKSITE').url('Pay/Wxpay/'.$gotoweixin_html).'?'.http_build_query($_param);

            return $this->clientAjax('wxh5pay', $_goto_weixin);
        }

        return false;
    }

    /**
     * PC端下单
     */
    public function pcPay() {
        $_class = new WxNotifyCallBack();
        $_token = $_class->bizpayurl('NATIVE');
        if (!empty($_token)&& !empty($_token['code_url'])) {
            $_goto_weixin =  $_token['code_url']; 
            Session::set('wxpay_return_token', $this->getReturnToken());
             Session::set('code_url', $_goto_weixin, 'order');
            $_return_token = \huosdk\common\Simplesec::encode(session_id(), \think\Config::get('config.HSAUTHCODE'));
            $return_url = config('domain.SDKSITE').url(
                    'Pay/Wxpay/returnurl', [
                                             'order_id'     => Session::get('order_id', 'order'),
                                             'return_token' =>$_return_token
                                         ]
                );
            $_token =$_token['code_url'].'&redirect_url='.urlencode($return_url);;
            $_param = [
                'order_id'   => Session::get('order_id', 'order'),
                'timestamp'  => time(),
                'now_token'  => $_token,
                'return_url' => $return_url,
            ];
            

            return $this->clientAjax('wxpcpay',  $_goto_weixin);
        } 

        return false;



    }

    /**
     * 钱包充值回调函数
     */
    public function walletNotify() {
    }

    /**
     * 游戏币充值回调
     */
    public function gmNotify() {
    }

    /*
     * 异步回调函数
     */
    public function notifyUrl() {
              
        $notify = new WxNotifyCallBack();
        $xml = file_get_contents("php://input");
        libxml_disable_entity_loader(true);
        $WxPayResults =json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if ($WxPayResults['return_code'] == 'SUCCESS') {
                $out_trade_no=$WxPayResults['out_trade_no'];
                $arr=explode("_", $out_trade_no);
                if(count($arr) >= 2){
                    $appid=$arr[0];
                    $notify->updateParam($appid);
                }
        }   
        $notify->selfHandle(false);
    }


    /*
     * 异步回调函数
     */
    public function notifyUrlTest() {
              
        $notify = new WxNotifyCallBack();

        /**根据订单号的前缀获取相应的游戏微信参数**/
        $xml = file_get_contents("php://input");
        libxml_disable_entity_loader(true);
        $WxPayResults =json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if ($WxPayResults['return_code'] == 'SUCCESS') {
                $out_trade_no=$WxPayResults['out_trade_no'];
                $arr=explode("_", $out_trade_no);
                if(count($arr) >= 2){
                    $appid=$arr[0];
                    $notify->updateParam($appid);
                }
        }   
        $notify->selfHandle(false);
    }
    /*
     * 返回接收页面
     */
    public function returnUrl() {
    }

    /*
     * 组建订单数据
     */
    private function buildOrderdata(array $paydata) {
        $_paydata = $paydata;
        if (empty($_paydata['product_price']) || $_paydata['product_price'] < 0) {
            return false;
        }
        $_order_data['mem_id'] = $this->gameaccount(Session::get('id', 'user'));
        $_order_data['order_id'] = \huosdk\common\Commonfunc::setOrderid($_order_data['mem_id']);
        $_order_data['agent_id'] = Session::get('agent_id', 'user');
        $_order_data['app_id'] = Session::get('app_id', 'app');
        $_order_data['amount'] = $_paydata['product_price'];
        $_order_data['gm_cnt'] = 0;
        $_order_data['real_amount'] = $_order_data['amount'];
        $_order_data['rebate_cnt'] = 0;
        $_order_data['from'] = Session::get('from', 'device');
        $_order_data['status'] = 1;
        $_order_data['cpstatus'] = 1;
        $_order_data['payway'] = 0;
        $_order_data['create_time'] = time();
        $_order_data['update_time'] = $_order_data['create_time'];
        $_order_data['attach'] = isset($_paydata['ext']) ? $_paydata['ext'] : '';
        $_order_data['remark'] = '';

        return $_order_data;
    }

    /*
     * SDK预下单
     */
    public function sdkPreorder(array $paydata = array()) {
        /* 校验入参合法性 huosdktest */
        // $_paydata = checkParam($paydata);
        // 组建订单数据
        $_order_data = $this->buildOrderdata($paydata);
        Session::set('order_id', $_order_data['order_id'], 'order');
        /* 1 查询余额 */
        $_wallet_remain = \huosdk\wallet\Wallet::getRemain($_order_data['mem_id'], $_order_data['app_id']);
        $_wallet_rate = \huosdk\wallet\Wallet::getRate(); /* 钱包与实际价格比例 */
        $_wallet_real_price = number_format($_wallet_remain / abs($_wallet_rate), 2, '.', ''); /* 钱包实际价值 */
        $_product_price = number_format($_order_data['amount'], 2, '.', '');
        $_no_wallet_amount = $_product_price; /* 非wallet支付金额 */
        if (0 < $_wallet_real_price && $_wallet_real_price <= $_product_price) {
            /* 实际余额少于商品价格 */
            $_order_data['gm_cnt'] = $_wallet_remain;
            $_no_wallet_amount = $_product_price - $_wallet_real_price;
        } else if ($_wallet_real_price > $_product_price) {
            /* 余额大于商品价格 */
            $_order_data['gm_cnt'] = number_format($_product_price * abs($_wallet_rate), 2, '.', '');
            $_no_wallet_amount = 0;
        }
        if ($_no_wallet_amount > 0) {
            // 除去游戏币或平台币支付 后需要支付的金额
            // 去除游戏币计算折扣
            $this->setRate($_order_data, $_no_wallet_amount);
        }
        $_pay_id = $this->insertPay($_order_data);
        if ($_pay_id) {
            return true;
        }

        return false;
    }

    /*
     * 折扣计算金额
     * huosdktest
     */
    private function setRate(&$order_data, $_no_wallet_amount) {
        $order_data['real_amount'] = $_no_wallet_amount;
        $order_data['rebate_cnt'] = 0;

        return;
    }

    /*
     * 请求数据
     */
    private function insertPayext($pay_id) {
        $_payext_data['pay_id'] = $pay_id;
        $_payext_data['product_id'] = Session::get('product_id', 'order');
        $_payext_data['product_name'] = Session::get('product_name', 'order');
        $_payext_data['product_desc'] = Session::get('product_desc', 'order');
        $_payext_data['deviceinfo'] = Session::get('deviceinfo', 'device');
        $_payext_data['userua'] = Session::get('userua', 'device');
        $_payext_data['agentgame'] = Session::get('agentgame', 'user');
        $_payext_data['pay_ip'] = Session::get('ip', 'device');
        $_payext_data['imei'] = Session::get('device_id', 'device');
//         $_payext_data['cityid'] = Session::get('ipaddrid', 'device');
        $_payext_data['cp_order_id'] = Session::get('cp_order_id', 'order');
        $_payext_data['product_count'] = Session::get('product_count', 'order');
        $_payext_data['exchange_rate'] = Session::get('exchange_rate', 'order');
        $_payext_data['currency_name'] = Session::get('currency_name', 'order');
        $_payext_data['server_id'] = Session::get('server_id', 'role');
        $_payext_data['server_name'] = Session::get('server_name', 'role');
        $_payext_data['role_id'] = Session::get('role_id', 'role');
        $_payext_data['role_name'] = Session::get('role_name', 'role');
        $_payext_data['party_name'] = Session::get('party_name', 'role');
        $_payext_data['role_level'] = Session::get('role_level', 'role');
        $_payext_data['role_vip'] = Session::get('role_vip', 'role');
        $_payext_data['role_balence'] = Session::get('role_balence', 'role');
        Db::name('pay_ext')->insert($_payext_data);

        return;
    }

    protected function payAction($pay_id) {
        /* 1 插入充值扩展表 */
        $this->insertPayext($pay_id);
        /* 2 CP 回调组装 */
        $this->setCpparam($pay_id);
        /* 3 角色数据插入 huosdktest */
        $_r_class = new \huosdk\log\Memrolelog('mg_role_log');
        $_data['money'] = 0;
        $_data['type'] = 5;
        $_r_class->insert($_data);

        return;
    }

    protected function insertPay(array $order_data) {
        // 插入充值表
        $_pay_id = Db::name('pay')->insertGetid($order_data);
        if ($_pay_id) {
            // 异步操作其他数据
            $this->payAction($_pay_id);
            Session::set('gm_cnt', $order_data['gm_cnt'], 'order');
            Session::set('real_amount', $order_data['real_amount'], 'order');
            Session::set('rebate_cnt', $order_data['rebate_cnt'], 'order');
        }

        return $_pay_id;
    }

    protected function setCpparam($pay_id) {
        $_param['app_id'] = Session::get('app_id', 'app');
        $_param['cp_order_id'] = Session::get('cp_order_id', 'order');
        $_param['ext'] = Session::get('ext', 'order');
        $_param['mem_id'] = $this->gameaccount(Session::get('id', 'user'));
        $_param['order_id'] = Session::get('order_id', 'order');
        $_param['order_status'] = 2;
        $_param['pay_time'] = time();
        $_param['product_id'] = Session::get('product_id', 'order');
        $_param['product_name'] = Session::get('product_name', 'order');
        $_param['product_price'] = Session::get('product_price', 'order');
        $_param = \huosdk\common\Commonfunc::argSort($_param);
        $_signstr = \huosdk\common\Commonfunc::createLinkstring($_param);
        /* 获取游戏信息 */
        $_g_class = new \huosdk\game\Game($_param['app_id']);
        $_g_info = $_g_class->getGameinfo($_param['app_id']);
        if (empty($_g_info['cpurl']) || empty($_g_info['app_key'])) {
            return false;
        }
        $_sign = md5($_signstr."&app_key=".$_g_info['app_key']);
        $_pc_data['pay_id'] = $pay_id;
        $_pc_data['order_id'] = $_param['order_id'];
        $_pc_data['cp_order_id'] = Session::get('cp_order_id', 'order');
        $_pc_data['params'] = $_signstr."&sign=".$_sign;
        $_pc_data['cpurl'] = $_g_info['cpurl'];
        $_pc_data['status'] = 1;
        $_pc_data['cpstatus'] = 1;
        $_pc_data['create_time'] = $_param['pay_time'];
        $_pc_data['update_time'] = 0;
        $_pc_data['cnt'] = 0;
        $_rs = DB::name('pay_cpinfo')->insert($_pc_data);
        if ($_rs) {
            return true;
        }

        return false;
    }

    // 根据支付方式获取支付方式ID
    public function getPaywayid($payway) {
        if (empty($payway)) {
            return 0;
        }
        $map['payname'] = $payway;
        $pw_id = M('payway')->where($map)->getField('id');
        if (empty($pw_id)) {
            return 0;
        } else {
            return $pw_id;
        }
    }
}