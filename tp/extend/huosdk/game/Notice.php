<?php
namespace huosdk\game;

use think\Config;
use think\Db;
use think\Log;

class Notice {
    private $app_id;
    private $client_id;

    /**
     * 自定义错误处理
     *
     * @param msg 输出的文件
     */
    private function _error($msg, $level = 'error') {
        $_info = 'game\Game Error:'.$msg;
        Log::record($_info, 'error');
    }

    /**
     * 构造函数
     *
     * @param $rsa_pri_path string rsa私钥地址
     */
    public function __construct($app_id = 0, $client_id = 0) {
        if (!empty($app_id)) {
            $this->app_id = $app_id;
        }
        if (!empty($client_id)) {
            $this->client_id = $client_id;
        }
    }

    public function getNoticeList($app_id = 0) {
        $_map['status'] = 1;
        $_map['app_id'] = $app_id;
        $list = DB::name("game_affiche")->where($_map)->order("listorder desc,id desc")->select();
        return $list;
    }

     public function getNotice($app_id = 0) {
        $_map['status'] = 1;
        $_map['app_id'] = $app_id;
        $list = DB::name("game_affiche")->where($_map)->order("update_time desc")->limit(1)->find();
        if($list){
            return $list;
        }
        return array("status"=>0);
        
    }

    public function getNoticeRecord($notice_id = 0) {
        $_map['id'] = $notice_id;
        $record = DB::name("game_affiche")->where($_map)->order("listorder desc,id desc")->find();
        return $record;
    }
}