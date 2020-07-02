<?php
/**
 * Order.php UTF-8
 * 查询支付结果  http://doc.1tsdk.com/43?page_id=2269
 *
 * @date    : 2017/11/10 12:02
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : ouzhongfu <ozf@huosdk.com>
 * @version : HUOSDK 7.2
 */
namespace app\apple\controller\v1;

use app\common\controller\Baseapple;

class Order extends Baseapple {
    function _initialize() {
        parent::_initialize();
    }

    /**
     * 查询订单
     */
    public function queryOrder() {
        if (empty($this->rq_data['order_id'])) {
            return hs_api_responce('424', '订单号为空');
        }
        $_order_id = $this->rq_data['order_id'];
        /* 判断订单类型 尾号为2的订单为浮点红包充值订单  查询wallet_in 表 */
        $_end_str = substr($_order_id,-1);
        $_pay_class = new \huosdk\pay\Pay();
        if($_end_str == '2') {
            $_rdata = $_pay_class->getWalletInInfo($_order_id);
        }else{
            $_rdata = $_pay_class->queryOrder($_order_id);
        }

        if (false == $_rdata) {
            return hs_api_responce('424', '查询失败');
        }
        return $this->qoReturn($_rdata["order_id"],$_rdata["status"],$_rdata["cpstatus"]);
    }
}