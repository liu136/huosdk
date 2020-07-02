<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-17
 * Time: 21:25
 */
namespace app\index\model;
use think\Model;
use think\Db;
use think\Config;

class GameServer extends Model {

    // 获取开服信息
    public function getGameRunServers($limit=10) {
        $where['s.is_delete'] = 2;
        $where['g.status'] = 2;
        $field = "s.start_time, g.name, s.status, g.id app_id";
        $server_lists = Db::name('game_server')
            ->alias('s')
            ->field($field)
            ->join('__GAME__ g', 'g.id=s.app_id', 'INNER')
            ->where($where)
            ->whereTime('s.start_time', '>=', time())
            ->order('s.start_time asc')
            ->limit($limit)
            ->select();

        return $this->handleServerData($server_lists);
    }

    // 处理开服的数据
    public function handleServerData($data) {
        if (empty($data)) {
            return $data;
        } else {
            $start_time = strtotime(date('Y-m-d'));
            $end_time = $start_time+86400;
            $status = array('1'=>'即将开服', '2'=>'已开服');
            foreach ($data as $key=>$val) {
                if ($val['start_time']>$start_time && $val['start_time']<$end_time) {
                    $data[$key]['start_time'] = '今天' . date("H:i", $val['start_time']);
                    $data[$key]['run_time'] = '今天' . date("H:i", $val['start_time']);
                } else {
                    $data[$key]['start_time'] = date('m-d H:i', $val['start_time']);
                    $data[$key]['run_time'] = date("m-d H:i", $val['start_time']);
                }
                $data[$key]['status'] = $status[$val['status']];
            }

            return $data;
        }
    }

    // 获取游戏开服列表信息
    public function GameServerList($where=array(), $limit=10) {
        $app_id = request()->param('app_id');
        if (!empty($app_id)) {
            $where['s.app_id'] = $app_id;
        }
        $where['s.is_delete'] = 2;
        $field = "g.type,s.start_time, g.id app_id, g.name game_name";
        $field .= ", s.status, ifnull(i.yiosurl,'') ios_down_url, ifnull(i.androidurl,'') android_down_url";
        $field .= ", g.icon, s.ser_name server_name";
        $server_lists = Db::name('game_server')
            ->alias('s')
            ->field($field)
            ->join('__GAME__ g', 'g.id=s.app_id', 'INNER')
            ->join('__GAME_INFO__ i', 'i.app_id=g.id', 'LEFT')
            ->where($where)
            ->limit($limit)
            ->order('s.start_time desc')
            ->select();

        // 给游戏添加标签
        $game = new Game();
        foreach ($server_lists as $key=>$val) {
            $server_lists[$key]['game_type'] = $game->convertType($val['type']);
            $server_lists[$key]['platform'] = Config::get('brand_name');
            $server_lists[$key]['run_time'] = date("m-d H:i", $val['start_time']);
        }

        return $this->handleServerData($server_lists);
    }

    // 获取今日开服信息
    public function getTodayServerList() {
        $start_time = strtotime(date('Y-m-d', time()));
        $end_time = strtotime(date('Y-m-d', time()))+86400;
        $where['s.start_time'] = array('BETWEEN', $start_time.','.$end_time);

        $serverInfo['today_list'] = $this->GameServerList($where);
        return $serverInfo['today_list'];
    }

    // 获取即将开服信息
    public function getAboutServerList() {
        $end_time = strtotime(date('Y-m-d', time()))+86400;
        $where['s.start_time'] = array('GT', $end_time);

        $serverInfo['about_list'] = $this->GameServerList($where);
        return $serverInfo['about_list'];
    }

    // 获取已经开服信息
    public function getAfterServerList() {
        $start_time = strtotime(date('Y-m-d', time()));
        $where['s.start_time'] = array('LT', $start_time);

        $serverInfo['after_list'] = $this->GameServerList($where);
        return $serverInfo['after_list'];
    }

}