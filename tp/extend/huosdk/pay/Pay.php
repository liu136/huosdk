<?php
/**
 * Pay.php UTF-8
 * 支付函数
 *
 * @date    : 2016年11月16日下午3:13:29
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : wuyonghong <wyh@huosdk.com>
 * @version : HUOSDK 7.0
 * @modified: 2016年11月16日下午3:13:29
 */
namespace huosdk\pay;

use think\Db;
use think\Log;
use think\Session;

class Pay {
    /**
     * 自定义错误处理
     *
     * @param msg 输出的文件
     */
    private function _error($msg, $level = 'error') {
        $_info = 'pay\Pay Error:'.$msg;
        Log::record($_info, 'error');
    }

    /**
     * 构造函数
     *
     * @param $table_name string 数据库表名
     */
    public function __construct() {
    }

    /**
     * 移动APP支付函数
     */
    public function clientPay() {
    }

    /**
     * wap端下单
     */
    public function mobilePay() {
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

    private function setGm(array $paydata) {
        /* 扣除相应游戏币 */
        \huosdk\wallet\Wallet::pay($paydata, $paydata['app_id']);
        /* 增加返利游戏币 到玩家账户 */
        \huosdk\wallet\Wallet::rebate($paydata, $paydata['app_id']);
    }

    /*
     * 更新用户扩展支付信息
     */
    private function updateMeminfo(array $paydata) {
        $me_model = DB::name('mem_ext');
        $map['mem_id'] = $paydata['mem_id'];
        $ext_data = $me_model->where($map)->find();
        if (empty($ext_data)) {
            $ext_data['mem_id'] = $paydata['mem_id'];
            $ext_data['order_cnt'] = 1;
            $ext_data['sum_money'] = $paydata['amount'];
            $ext_data['last_pay_time'] = $paydata['create_time'];
            $ext_data['last_money'] = $paydata['amount'];
            $me_model->insert($ext_data);
        } else {
            $ext_data['order_cnt'] += 1;
            $ext_data['sum_money'] += $paydata['amount'];
            $ext_data['last_pay_time'] = $paydata['create_time'];
            $ext_data['last_money'] = $paydata['amount'];
            $me_model->update($ext_data);
        }
    }

    public function selectNotify($order_id, $amount, $paymark) {
        $_pay_map['order_id'] = $order_id;
        $_o_data = DB::name('pay')->where($_pay_map)->find();
        if ($_o_data) {
            return $this->sdkNotify($order_id, $amount, $paymark);
        }
        $_wallet_class = \huosdk\wallet\Wallet::init();
        return $_wallet_class->walletNotify($order_id, $amount, $paymark);
    }

    /*
     * 异步回调函数
     */
    public function sdkNotify($orderid, $amount, $paymark = '') {
        // 查询订单
        $_pay_map['order_id'] = $orderid;
        $_o_data = DB::name('pay')->where($_pay_map)->find();
        if (empty($_o_data)) {
            //Log::record('支付宝回调订单号：'.$orderid, 'error');
            return false;
        }
        $_o_amount = number_format($_o_data['real_amount'], 2, '.', '');
        $_amount = number_format($amount, 2, '.', '');
        // 2 判断充值金额与回调中是否一致，且状态不为2，即待支付状态
        if (($_o_amount <= $_amount) && 2 != $_o_data['status']) {
            $_o_data['status'] = 2;
            $_o_data['remark'] = $paymark;
            $_o_data['update_time'] = time();
            // 3 将订单信息写入pay表中，并修改订单状态为2，即支付成功状态
            $_rs = DB::name('pay')->update($_o_data);
            /* 异步调起oa统计 */
            //$oa_class=new \huosdk\agent\Agentoa($_o_data['agent_id']);
            //$agent_oa_info=$oa_class->request_pay_agent_oa($_o_data);
          //  Log::write($agent_oa_info, 'debug');
            //修改mg_role_log中money
            $_mg_data['money'] = $_o_data['amount'];
            $this->insetRolemoney($_o_data);
            // 判断订单信息是否修改成功
            if (false !== $_rs) {
                /* 游戏币错误  游戏币有支付 则扣除游戏币  返利游戏币 则增加游戏币  */
                $this->setGm($_o_data);
                $this->updateMeminfo($_o_data);
                /* 计算渠道收益 */
                \huosdk\finance\Agentincome::income($orderid, PAYFROM_SDK);
                $_pc_map['pay_id'] = $_o_data['id'];
                $_paycp_info = Db::name('pay_cpinfo')->where($_pc_map)->find();
                if (empty($_paycp_info)){
                    return false;
                }
                $_paycp_info['status'] = 2;
                $_cpurl = $_paycp_info['cpurl'];
                $_param = $_paycp_info['params'];
                if($this->isTestPay($_o_data['payway']) == 1){
                        $_param = $_paycp_info['params']."&is_test=1";
                }else{
                        $_param = $_paycp_info['params']."&is_test=0";
                }

                //Log::record('支付宝回调订单号：'.$_cpurl.$_param, 'error');

                $i = 0;
                /* 2.2.3 通知CP */
                if (2 != $_o_data['cpstatus']) {
                    $i = 0;
                    while (1) {
                        // 异步回调CP信息 huosdktest
                        $_cp_rs = \huosdk\request\Request::cpPayback($_cpurl, $_param);
                        if ($_cp_rs > 0) {
                            $_paycp_info['cpstatus'] = 2;
                            break;
                        } else {
                            $_paycp_info['cpstatus'] = $_cp_rs;
                            $i++;
                            sleep(1);
                        }
                        if ($i == 3) {
                            break;
                        }
                    }
                }
                if ($_paycp_info['cpstatus'] != $_o_data['cpstatus']) {
                    $_o_data['cpstatus'] = $_paycp_info['cpstatus'];
                    $_o_map['id'] = $_o_data['id'];
                    Db::name('pay')->where($_o_map)->setField('cpstatus', $_o_data['cpstatus']);

                    $_pay_cpmap['pay_id'] = $_o_data['id'];
                    $_data['cpstatus'] = $_o_data['cpstatus'];
                    $_data['params'] =  $_param;
                    $_data['status'] = PAYSTATUS_SUCCESS;
                    $_data['cnt'] = $i;
                    Db::name('pay_cpinfo')->where($_pay_cpmap)->update($_data);
                }
            }
        }

        return true;
    }

    /*
     * 返回接收页面
     */
    public function returnUrl() {
    }

    public function clientAjax($payway, $token, $status = 1) {
        $_rdata['paytype'] = $payway;
        $_rdata['order_id'] = Session::get('order_id', 'order');
        $_rdata['real_amount'] = Session::get('real_amount', 'order');
        $_rdata['token'] = $token;
        $_rdata['status'] = $status;

        return $_rdata;
    }


    /*支付类型是否为测试订单*/
     private function isTestPay($type) {
            if($type == "appletest"){
                return 1;
            }
            return 0;
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
    /**
     * 根据本地sdk的游戏账号查找后台配置的映射账号
     * @param  [type] $local_game_account [本地自动生成账号]
     * @return [type]                     [第三方导入的游戏账号]
     */
    public function gameaccount($local_game_account){
        if($local_game_account){
            $gamename=Db::name('members')->where('id',$local_game_account)->value('gameaccount');
            return $gamename?$gamename:$local_game_account;
        }else{
            return $local_game_account;
        }    
    }


    public function WebCheckSign(array $paydata = array()) {
        $sign =$paydata['sign'];
        $ext =$paydata['ext'];
        $ext =$paydata['ext'];
        $product_id =$paydata['product_id'];
        $product_price =$paydata['product_price'];
        $role_id =$paydata['role_id'];
        $server_id =$paydata['server_id'];
        $timestamp =$paydata['timestamp'];

        $_map['app_id'] = $paydata['app_id'];
        $_client_key = DB::name('game_client')->where($_map)->value('client_key');
        if($sign ==  md5(md5($ext.$product_id. $product_price .$role_id.$server_id.$timestamp).$_client_key)){
            return true;
        }
        return false;
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
        Session::set('gmremain', $_wallet_remain, 'user');
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
            $_order_data['real_amount'] = 0;
        }
        // 除去游戏币或平台币支付 后需要支付的金额
        // 去除游戏币计算折扣
        $this->setRate($_order_data, $_no_wallet_amount);
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
        /* 查看是否设定折扣,若无折扣版本则直接返回  */
        $_wallet_config = \think\Config::get('config.wallet');
        if (!$_wallet_config['sdkbenifit'] || 0 == \think\config::get('config.G_DISCONT_TYPE')) {
            $order_data['real_amount'] = $_no_wallet_amount;
            $order_data['rebate_cnt'] = 0;
            Session::set('benefit_type', 0, 'order');
            Session::set('mem_rate', 1, 'order');
            Session::set('mem_rebate', 0, 'order');
            Session::set('isfirst', 1, 'order');

            return;
        }
        $_rate_class = new \huosdk\rate\Rate($order_data['app_id']);
        $_rate_data = $_rate_class->getMemrate(
            $order_data['agent_id'],
            $order_data['app_id'],
            $order_data['mem_id'],
            $order_data['from']
        );
        Session::set('benefit_type', $_rate_data['benefit_type'], 'order');
        Session::set('mem_rate', $_rate_data['mem_rate'], 'order');
        Session::set('mem_rebate', $_rate_data['mem_rebate'], 'order');
        Session::set('isfirst', $_rate_data['isfirst'], 'order');
        $order_data['real_amount'] = round($_no_wallet_amount * $_rate_data['mem_rate'], 2);
        $order_data['rebate_cnt'] = round($_no_wallet_amount * $_rate_data['mem_rebate']);
        $order_data['rebate_cnt'] = sprintf("%.2f", substr(sprintf("%.3f", $order_data['rebate_cnt']), 0, -2));

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
        $_ext = urlencode(Session::get('ext', 'order'));
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
        $_pc_data['params'] = $_signstr."&sign=".$_sign.'&ext='.$_ext;
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
        $pw_id = Db::name('payway')->where($map)->value('id');
        if (empty($pw_id)) {
            return 0;
        } else {
            return $pw_id;
        }
    }

    /*
     * 更新支付方式
     */
    public function upPayway($order_id, $payway) {
        if (empty($payway) || empty($order_id)) {
            return false;
        }
        $_map['order_id'] = $order_id;
        $_rs = Db::name('pay')->where($_map)->setField('payway', $payway);
        if (false === $_rs) {
            return false;
        } else {
            return true;
        }
    }

    public function queryOrder($order_id) {
        if (empty($order_id)) {
            return false;
        }
        $_map['order_id'] = $order_id;
        $field = [
            'order_id',
            'status',
            'cpstatus'
        ];
        $_rdata = Db::name('pay')->field($field)->where($_map)->find();
        if (empty($_rdata)) {
            return false;
        }

        return $_rdata;
    }

    /**
     * 判断是否在时间范围内
     *
     * @param INT $start_time
     * @param INT $end_time
     *
     * @return bool true 表示要切换 false 表示不切换
     */
    public function switchTime($start_time, $end_time) {
        $_time = date('H:i:s');
        if (empty($start_time) && empty($end_time)) {
            return true;
        } else if (empty($start_time) && $_time > $end_time) {
            return false;
        } else if (empty($end_time) && $_time < $start_time) {
            return false;
        } else if ($_time > $end_time || $_time < $start_time) {
            return false;
        }

        return true;
    }

    /**
     * 判断IP是否是国内IP
     *
     * @param $is_domestic
     * @param $is_overseas
     *
     * @return bool true 表示要切换 false 表示不切换
     */
    public function switchIp($is_domestic, $is_overseas) {
        if (empty($is_domestic) && empty($is_overseas)) {
            return true;
        }
        if (2 == $is_domestic && 2 == $is_overseas) {
            return true;
        }
        $_ip = Session::get('ip', 'device');
        $_ip_class = new \huosdk\common\iplimit\Iplimit();
        $_rs = $_ip_class->setup($_ip); //true 国内  false 海外
        if (!$_rs && 1 == $is_overseas) {
            return true;
        } else if ($_rs && 1 == $is_domestic) {
            return true;
        }

        return false;
    }

    /**
     * 判断首充是否切换
     *
     * @param INT $app_id   游戏ID
     * @param INT $is_first 是否首冲切换 1是 0否
     *
     * @return bool true 表示要切换 false 表示不切换
     */
    public function switchFirst($app_id, $is_first) {
        if (0 == $is_first) {
            return true;
        }
        $_map = [
            'app_id' => $app_id,
            'mem_id' => $this->gameaccount(Session::get('id', 'user')),
            'status' => PAYSTATUS_SUCCESS
        ];
        $_cnt = Db::name('pay')->where($_map)->count();
        if (empty($_cnt) && 1 == $is_first) {
            /* 首次充值 */
            return false;
        }

        return true;
    }

    /**
     * 判断首充是否切换
     *
     * @param float $amount
     * @param float $set_money
     *
     * @return bool true 表示要切换 false 表示不切换
     *
     */
    public function switchMoney($amount, $set_money = 0.00) {
        if (empty($set_money)) {
            /* 没有设置金额 全部切换 */
            return true;
        }
        if ($amount < $set_money) {
            /* 金额小于设置金额 不切换 */
            return false;
        }

        return true;
    }

    /**
     * 判断版本是否切换
     *
     * @param string $version
     * @param string $DB_version
     *
     * @return bool true 表示要切换 false 表示不切换
     *
     */
    public function switchVersion($version, $DB_version) {
        if (empty($DB_version) || empty($version)) {
            /* 没有设置金额 全部切换 */
            return true;
        }
        if (in_array($version,explode(',',$DB_version))) {
            /* 版本号在数组内 不切换 */
            return false;
        }

        return true;
    }


    /*
     * 获取支付切换状态，1为切换，2为不切换
     * Lin
     * $version  是否需要切换的版本号
     */
    public function getPayswitch($app_id, $price,$version=null) {
        if (empty($app_id)) {
            return false;
        }
        $_pay_switch = 2;
        $condition['id'] = $app_id;
        $switch_value = Db::name('game')->where($condition)->value("pay_switch");
        if ($switch_value == 2 || empty($switch_value)) {//总开关关闭
            return $_pay_switch;
        }
        $_map['app_id'] = $app_id;
        $info = DB::name('game_pay_switch')->where($_map)->find();
        if (!empty($info)) {
            /*根据版本号判断是否需要切换*/
            $_rs = $this->switchVersion($version, $info['version']);
            if (false == $_rs) {
                return $_pay_switch;
            }
            /* 根据时间判断是否是否切换 */
            $_rs = $this->switchTime($info['start_time'], $info['end_time']);
            if (false == $_rs) {
                return $_pay_switch;
            }
            /* 根据IP段判断是否切换 */
            $_rs = $this->switchIp($info['is_domestic'], $info['is_overseas']);
            if (false == $_rs) {
                return $_pay_switch;
            }
            /* 根据首充是否切换 */
            $_rs = $this->switchFirst($app_id, $info['is_first']);
            if (false == $_rs) {
                return $_pay_switch;
            }
            /* 根据首充是否切换 */
            $_rs = $this->switchMoney($price, $info['price']);
            if (false == $_rs) {
                return $_pay_switch;
            }
            $_pay_switch = 1;

        } else { //只打开开关没设置智能切换
            $_pay_switch = 1;
        }

        return $_pay_switch;
    }
    
    public function getPayFunctionStr($app_id) {
        if (empty($app_id)) {
            return false;
        }
        $_map['app_id'] = $app_id;
        $info = DB::name('game_pay_switch')->where($_map)->find();
        return $info;
    }


    /**
     * 返回游戏跳转
     * http://doc.1tsdk.com/43?page_id=2129
     *
     * @return string
     */
    protected function getReturnToken() {
        $_apple_id = Session::get('apple_id', 'app');
        if (empty($_apple_id)){
        $_app_id = Session::get('app_id', 'app');
        $_client_id = Session::get('client_id', 'app');
        $_rt_data['order_id'] = Session::get('order_id', 'order');
        $_rt_data['back_type'] = 1; //默认关闭页面
        $_rt_data['money'] = Session::get('amount', 'order');
        $_rt_string = http_build_query($_rt_data);

        return urlencode('h'.DOCDOMAIN.$_app_id.$_client_id.'://queryOrder?'.$_rt_string);
        }else{
            $_rt_data['oi'] = Session::get('order_id', 'order');
            $_rt_data['bt'] = 1;  //默认关闭页面
            $_rt_string = http_build_query($_rt_data);
            return urlencode('h'.DOCDOMAIN.$_apple_id.'://qo?'.$_rt_string);
        }
    }

    /**
     * @param array $paydata c_pay 中的一条对应数据
     *
     * @return bool
     */
    public function insetRolemoney($paydata) {
        if (empty($paydata) || empty($paydata['id']) || 2 != $paydata['status']) {
            return false;
        }
        /* 1 从payext中查询role数据 */
        $_field = "server_id,server_name,role_id,role_name,role_level,role_vip,party_name,role_balence";
        $_map['pay_id'] = $paydata['id'];
        $_data = Db::name('pay_ext')->field($_field)->where($_map)->find();
        if (empty($_data)) {
            return false;
        }
        /* 2 金额赋值 */
        $_data['mem_id'] = $paydata['mem_id'];
        $_data['app_id'] = $paydata['app_id'];
        $_data['attach'] = '';
        $_data['type'] = 5;
        $_data['money'] = $paydata['amount'];
        $_data['rolelevel_ctime'] = 0;
        $_data['rolelevel_mtime'] = 0;
        $_data['create_time'] = time();
        /* 3 角色数据插入 */
        $_r_class = new \huosdk\log\Memrolelog('mg_role_log');
        return $_r_class->insertbyData($_data);
    }
}