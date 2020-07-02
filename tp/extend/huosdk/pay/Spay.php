<?php
/**
 * Spay.php UTF-8
 * 威富通处理函数
 *
 * @date    : 2016年11月18日下午4:26:40
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : wuyonghong <wyh@huosdk.com>
 * @version : HUOSDK 7.0
 * @modified: 2016年11月18日下午4:26:40
 */
namespace huosdk\pay;

use think\Db;
use think\Loader;
use think\Session;

class Spay extends Pay {
    private $mchid;
    private $key;
    private $url;
    private $version;
    private $mch_app_id;
    private $mch_app_name;

    /**
     * 构造函数
     *
     * @param $table_name string 数据库表名
     */
    public function __construct() {
        // 包含配置文件
        $_conf_file = CONF_PATH."extra/pay/spay/config.php";
        if (file_exists($_conf_file)) {
            $_spay_conf = include $_conf_file;
        } else {
            $_spay_conf = array();
        }
        $this->mchid = $_spay_conf["mchId"]; /* 微付通商户ID */
        $this->key = $_spay_conf["key"]; /* 微付通KEY */
        $this->url = $_spay_conf["url"]; /* 微付通URL */
        $this->version = $_spay_conf["version"]; /* 微付通版本 */
        $this->mch_app_id = $_spay_conf["mch_app_id"]; /* 应用名 */
        $this->mch_app_name = $_spay_conf["mch_app_name"]; /* 应用标识 */
    }

    /**
     * 移动APP支付函数
     */
    public function clientPay() {
        Loader::import('pay.spay.ClientResponseHandler', '', '.class.php');
        Loader::import('pay.spay.RequestHandler', '', '.class.php');
        Loader::import('pay.spay.PayHttpClient', '', '.class.php');
        Loader::import('pay.spay.Utils', '', '.class.php');
        $_res_handler = new \ClientResponseHandler();
        $_req_handler = new \RequestHandler();
        $_pay = new \PayHttpClient();
        $_req_handler->setGateUrl($this->url);
        $_req_handler->setKey($this->key);
        $_notify_url = config('domain.SDKSITE').url('Pay/Spay/notifyurl');
        $_req_handler->setReqParams($_POST, array('method'));
        $_req_handler->setParameter('service', 'unified.trade.pay'); // 接口类型：pay.weixin.native
        $_req_handler->setParameter('mch_id', $this->mchid); // 必填项，商户号，由威富通分配
        $_req_handler->setParameter('version', $this->version);
        // 接收威富通通知的 URL，需给绝对路径，255字符内格式如:http://wap.tenpay.com/tenpay.asp
        $_req_handler->setParameter('notify_url', $_notify_url);
        $_req_handler->setParameter('nonce_str', mt_rand(time(), time() + rand())); // 随机字符串，必填项，不长于 32 位
        $_req_handler->setParameter('out_trade_no', Session::get('order_id', 'order')); // 随机字符串，必填项，不长于 32 位
        $_req_handler->setParameter('body', Session::get('product_name', 'order')); // 随机字符串，必填项，不长于 32 位
        $_req_handler->setParameter(
            'total_fee', (int)(Session::get('real_amount', 'order') * 100)
        ); // 随机字符串，必填项，不长于 32 位
        $_req_handler->setParameter('mch_create_ip', Session::get('ip', 'device')); // 订单生成的机器 IP
        if (4 == Session::get('from', 'device')) {
            $device_info = 'iOS_WAP';
        } else {
            $device_info = 'AND_WAP';
        }
        $_req_handler->setParameter('device_info', $device_info); /* 应用类型  iOS_SDK,AND_SDK,iOS_WAP,AND_WAP */
        $_req_handler->setParameter('mch_app_id', $this->mch_app_id); /* WAP 首页 URL 地址,必须保证公网能正常访问 */
        $_req_handler->setParameter('mch_app_name', $this->mch_app_name); /* WAP 网站名(如：京东官网) */
        $_req_handler->createSign(); // 创建签名
        $_data = \Utils::toXml($_req_handler->getAllParameters());
        $_pay->setReqContent($_req_handler->getGateURL(), $_data);
        if ($_pay->call()) {
            $_res_handler->setContent($_pay->getResContent());
            $_res_handler->setKey($_req_handler->getKey());
            if ($_res_handler->isTenpaySign()) {
                // 当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                if ($_res_handler->getParameter('status') == 0 && $_res_handler->getParameter('result_code') == 0) {
                    return $this->clientAjax('spay', $_res_handler->getParameter('token_id'));
                } else {
//                     $rdata = array(
//                         'code' => $_res_handler->getParameter('err_code'),
//                         'info' => $_res_handler->getParameter('err_msg')
//                     );
                    return false;
                }
            }
//             $rdata = array(
//                 'code' => $_res_handler->getParameter('status'),
//                 'info' => $_res_handler->getParameter('message')
//             );
            return false;
        } else {
//             $rdata = array(
//                 'code' => $_pay->getResponseCode(),
//                 'info' => $_pay->getErrInfo()
//             );
            return false;
        }
    }

    /**
     * wap端下单
     */
    public function mobilePay() {
        Loader::import('pay.spay.ClientResponseHandler', '', '.class.php');
        Loader::import('pay.spay.RequestHandler', '', '.class.php');
        Loader::import('pay.spay.PayHttpClient', '', '.class.php');
        Loader::import('pay.spay.Utils', '', '.class.php');
        $_res_handler = new \ClientResponseHandler();
        $_req_handler = new \RequestHandler();
        $_pay = new \PayHttpClient();
        $_req_handler->setGateUrl($this->url);
        $_req_handler->setKey($this->key);
        $_notify_url = config('domain.SDKSITE').url('Pay/Spay/notifyurl');
        $_return_url = config('domain.SDKSITE').url(
                'Pay/Spay/returnurl', ['order_id' => Session::get('order_id', 'order')]
            );
//        $_req_handler->setParameter('attach', '平台币充值');
        $_req_handler->setParameter('service', 'pay.weixin.wappay');
        $_req_handler->setParameter('mch_id', $this->mchid);
        $_req_handler->setParameter('version', $this->version);
        // 接收威富通通知的 URL，需给绝对路径，255字符内格式如:http://wap.tenpay.com/tenpay.asp
        $_req_handler->setParameter('notify_url', $_notify_url);
        $_req_handler->setParameter('nonce_str', mt_rand(time(), time() + rand()));
        $_req_handler->setParameter('out_trade_no', Session::get('order_id', 'order'));
        $_req_handler->setParameter('body', Session::get('product_name', 'order'));
        $_req_handler->setParameter('total_fee', (int)(Session::get('real_amount', 'order') * 100));
        $_req_handler->setParameter('mch_create_ip', Session::get('ip', 'device'));
        if (4 == Session::get('from', 'device')) {
            $device_info = 'iOS_WAP';
        } else {
            $device_info = 'AND_WAP';
        }
        $_req_handler->setParameter('device_info', $device_info);
        $_req_handler->setParameter('mch_app_id', $this->mch_app_id);
        $_req_handler->setParameter('mch_app_name', $this->mch_app_name);
        $_req_handler->setParameter('callback_url', $_return_url);
        $_req_handler->setParameter('time_expire', time() + 60 * 60);
        $_req_handler->setParameter('time_start', time());
        $_req_handler->createSign(); // 创建签名
        $_data = \Utils::toXml($_req_handler->getAllParameters());
        $_pay->setReqContent($_req_handler->getGateURL(), $_data);
        if ($_pay->call()) {
            $_res_handler->setContent($_pay->getResContent());
            $_res_handler->setKey($_req_handler->getKey());
            if ($_res_handler->isTenpaySign()) {
                // 当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                if ($_res_handler->getParameter('status') == 0 && $_res_handler->getParameter('result_code') == 0) {
                    return $this->clientAjax('h5_spay', $_res_handler->getParameter('pay_info'));
                } else {
//                     $rdata = array(
//                         'code' => $_res_handler->getParameter('err_code'),
//                         'info' => $_res_handler->getParameter('err_msg')
//                     );
                    return false;
                }
            }
//             $rdata = array(
//                 'code' => $_res_handler->getParameter('status'),
//                 'info' => $_res_handler->getParameter('message')
//             );
            return false;
        } else {
//             $rdata = array(
//                 'code' => $_pay->getResponseCode(),
//                 'info' => $_pay->getErrInfo()
//             );
            return false;
        }
    }

    /**
     * PC端下单
     */
    public function pcPay() {
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
    public function notifyUrl($wallet = false) {
        Loader::import('pay.spay.ClientResponseHandler', '', '.class.php');
        Loader::import('pay.spay.RequestHandler', '', '.class.php');
        Loader::import('pay.spay.PayHttpClient', '', '.class.php');
        Loader::import('pay.spay.Utils', '', '.class.php');
        $_res_handler = new \ClientResponseHandler();
        $_xml = file_get_contents('php://input');
        $_res_handler->setContent($_xml);
        $_res_handler->setKey($this->key);
        // 判断签名结果
        $_sign_result = $_res_handler->isTenpaySign();
        if ($_sign_result) {
            if ($_res_handler->getParameter('status') == 0 && $_res_handler->getParameter('result_code') == 0) {
                // 更改订单状态
                $_out_trade_no = $_res_handler->getParameter('out_trade_no'); // 自己生成的订单状态
                $_amount = $_res_handler->getParameter('total_fee') / 100;
                $_trade_no = $_res_handler->getParameter('transaction_id').'|'.$_res_handler->getParameter(
                        'out_transaction_id'
                    );
                // 将订单状态写入数据表中
                // 支付成功后，修改支付表中支付状态，并将交易信息写入用户平台充值记录表ptb_charge。
                $this->selectNotify($_out_trade_no, $_amount, $_trade_no);
                echo 'success';
                exit();
            } else {
                echo 'failure1';
                exit();
            }
        } else {
            echo 'failure2';
        }
    }

    /*
     * 返回接收页面
     */
    public function returnUrl() {
        $_out_trade_no = request()->param('order_id');
        $_amount = Db::name('pay')->where('order_id', $_out_trade_no)->value('amount');
        $_status = Db::name('pay')->where('order_id', $_out_trade_no)->value('status');
        $_info = array();
        if (!empty($_amount)) {
            $_info['paytype'] = "spaywappay";
            $_info['order_id'] = $_out_trade_no;
            $_info['real_amount'] = $_amount;
            $_info['status'] = $_status;
            $_info = json_encode($_info);
        }

        return $_info;
    }

    /*
     * 组建订单数据
     */
    private function buildOrderdata(array $_paydata) {
        $_paydata = $_paydata;
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
    public function sdkPreorder(array $_paydata = array()) {
        /* 校验入参合法性 huosdktest */
        // $_paydata = checkParam($_paydata);
        // 组建订单数据
        $_order_data = $this->buildOrderdata($_paydata);
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
    private function insertPayext($_pay_id) {
        $_payext_data['pay_id'] = $_pay_id;
        $_payext_data['product_id'] = Session::get('product_id', 'order');
        $_payext_data['product_name'] = Session::get('product_name', 'order');
        $_payext_data['product_desc'] = Session::get('product_desc', 'order');
        $_payext_data['deviceinfo'] = Session::get('deviceinfo', 'device');
        $_payext_data['userua'] = Session::get('userua', 'device');
        $_payext_data['agentgame'] = Session::get('agentgame', 'user');
        $_payext_data['pay_ip'] = Session::get('ip', 'device');
        $_payext_data['imei'] = Session::get('device_id', 'device');
        // $_payext_data['cityid'] = Session::get('ipaddrid', 'device');
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

    protected function payAction($_pay_id) {
        /* 1 插入充值扩展表 */
        $this->insertPayext($_pay_id);
        /* 2 CP 回调组装 */
        $this->setCpparam($_pay_id);
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

    protected function setCpparam($_pay_id) {
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
        $_pc_data['pay_id'] = $_pay_id;
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
    public function getPaywayid($_payway) {
        if (empty($_payway)) {
            return 0;
        }
        $map['payname'] = $_payway;
        $pw_id = M('payway')->where($map)->getField('id');
        if (empty($pw_id)) {
            return 0;
        } else {
            return $pw_id;
        }
    }
}