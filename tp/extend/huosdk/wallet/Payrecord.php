<?php
/**
 * Gm.php UTF-8
 * 游戏币处理函数
 *
 * @date    : 2016年11月16日下午5:02:28
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : wuyonghong <wyh@huosdk.com>
 * @version : HUOSDK 7.0
 * @modified: 2016年11月16日下午5:02:28
 */
namespace huosdk\wallet;

use think\Db;
use think\Log;
use think\Config;
use think\Session;

class Payrecord {
    private $mem_id;

    public function __construct() {
        if (!empty($mem_id)) {
            $this->mem_id = $mem_id;
        }
    }

    /**
     *
     * 自定义错误处理
     *
     * @param        $msg   输出的文件
     * @param string $level 输出级别
     */
    private function _error($msg, $level = 'error') {
        $_info = 'pay\Payrecord Error:'.$msg;
        Log::record($_info, 'error');
    }

    /**
     * @param     $mem_id
     * @param int $page
     * @param int $offset
     *
     * @return null
     */
    public function getConsumelist($mem_id, $page = 1, $offset = 10) {
        if (empty($mem_id)) {
            return null;
        }
        $_map['p.mem_id'] = $mem_id;
        $_map['p.payway'] = ['NEQ', '0'];
        $_count = Db::name('pay')->alias('p')->where($_map)->count();
        $_join = [
            [
                Config::get('database.prefix').'game g', 'g.id = p.app_id', 'LEFT'
            ],
            [
                Config::get('database.prefix').'payway pw',
                'p.payway =pw.payname',
                'LEFT'
            ]
        ];
        if ($_count > 0) {
            $_start = ($page - 1) * $offset;
            if ($_count > $offset) {
                $_start = rand(0, $_count - $offset);
            }
            $_field = [
                'p.app_id'                                              => 'gameid',
                'g.name'                                                => 'gamename',
                'pw.disc'                                               => 'paytype',
                "IFNULL(p.amount,0)"                                    => 'amount',
                "CASE p.status WHEN 1 THEN '待处理'
                 WHEN 2 THEN '成功' WHEN 3 THEN '失败' END" => 'status',
                "p.order_id"                                            => 'orderid',
                'FROM_UNIXTIME(p.create_time)'                          => 'pay_time'
            ];
            $_limit = $_start.','.$offset;
            $_order = " p.create_time DESC ";
            $_list = Db::name('pay')
                       ->alias('p')
                       ->field($_field)
                       ->join($_join)
                       ->where($_map)
                       ->order($_order)
                       ->limit($_limit)
                       ->select();
            if (empty($_list)) {
                $_rdata['list'] = null;
            } else {
                $_rdata['list'] = $_list;
            }
            $_rdata['count'] = $_count;
        } else {
            $_rdata = null;
        }
        if (empty($_rdata)) {
            return null;
        }

        return $_rdata;
    }

    /**
     * @param     $mem_id
     * @param int $page
     * @param int $offset
     *
     * @return null
     */
    public function getrechargelist($mem_id, $page = 1, $offset = 10) {
        if (empty($mem_id)) {
            return null;
        }
        $_map['p.mem_id'] = $mem_id;
        $_map['p.status'] = 2;
        $_count = Db::name('ptb_charge')->alias('p')->where($_map)->count();
        $_join = [
            [
                Config::get('database.prefix').'payway pw',
                'p.payway = pw.payname',
                'LEFT'
            ]
        ];
        if ($_count > 0) {
            $_start = ($page - 1) * $offset;
            if ($_count > $offset) {
                $_start = rand(0, $_count - $offset);
            }
            $_field = [
                'pw.disc'                                               => 'paytype',
                "IFNULL(p.money,0)"                                     => 'amount',
                "CASE p.status WHEN 1 THEN '待处理'
                 WHEN 2 THEN '成功' WHEN 3 THEN '失败' END" => 'status',
                "p.order_id"                                            => 'orderid',
                'FROM_UNIXTIME(p.create_time)'                          => 'pay_time'
            ];
            $_limit = $_start.','.$offset;
            $_order = " p.create_time DESC ";
            $_list = Db::name('ptb_charge')
                       ->alias('p')
                       ->field($_field)
                       ->join($_join)
                       ->where($_map)
                       ->order($_order)
                       ->limit($_limit)
                       ->select();
            if (empty($_list)) {
                $_rdata['list'] = null;
            } else {
                $_rdata['list'] = $_list;
            }
            $_rdata['count'] = $_count;
        } else {
            $_rdata = null;
        }
        if (empty($_rdata)) {
            return null;
        }

        return $_rdata;
    }

    public function getgmrechargelist($mem_id, $page = 1, $offset = 10) {
        if (empty($mem_id)) {
            return null;
        }
        $_map['gmc.mem_id'] = $mem_id;
        $_map['gmc.flag'] = 4;
        $_map['gmc.status'] = 2;
        $_count = Db::name('gm_charge')->alias('gmc')->where($_map)->count();
        $_join = [
            [
                Config::get('database.prefix').'payway pw',
                'gmc.payway = pw.payname',
                'LEFT'
            ],
            [
                Config::get('database.prefix').'game g',
                'gmc.app_id = g.id',
                'LEFT'
            ]
        ];
        if ($_count > 0) {
            $_start = ($page - 1) * $offset;
            if ($_count > $offset) {
                $_start = rand(0, $_count - $offset);
            }
            $_field = [
                'pw.disc'                                               => 'paytype',
                "IFNULL(gmc.money,0)"                                   => 'amount',
                "IFNULL(gmc.gm_cnt,0)"                                  => 'gm_num',
                "CASE gmc.status WHEN 1 THEN '待处理'
                 WHEN 2 THEN '成功' WHEN 3 THEN '失败' END" => 'status',
                "gmc.order_id"                                          => 'orderid',
                'FROM_UNIXTIME(gmc.create_time)'                        => 'pay_time',
                "g.name"                                                => "gamename",
                "g.id"                                                  => "gameid"
            ];
            $_limit = $_start.','.$offset;
            $_order = " gmc.create_time DESC ";
            $_list = Db::name('gm_charge')
                       ->alias('gmc')
                       ->field($_field)
                       ->join($_join)
                       ->where($_map)
                       ->order($_order)
                       ->limit($_limit)
                       ->select();
            if (empty($_list)) {
                $_rdata['list'] = null;
            } else {
                $_rdata['list'] = $_list;
            }
            $_rdata['count'] = $_count;
        } else {
            $_rdata = null;
        }
        if (empty($_rdata)) {
            return null;
        }

        return $_rdata;
    }
}