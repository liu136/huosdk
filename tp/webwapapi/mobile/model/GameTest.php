<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-17
 * Time: 21:53
 */
namespace app\index\model;
use think\Model;
use think\Db;

class GameTest extends Model {

    // 获取开测列表
    public function getGameTestServers() {
        $where['s.is_delete'] = 2;
        $where['g.status'] = 2;
        $field = "s.start_time, g.name, s.status, g.id app_id";
        $field .= ", g.icon game_icon, testdesc server_name";
        $server_lists = Db::name('game_test')
              ->alias('s')
              ->field($field)
              ->join('__GAME__ g', 'g.id=s.app_id', 'INNER')
              ->where($where)
              ->whereTime('s.start_time', 'today')
              ->order('s.start_time asc')
              ->select();

        return $this->handleServerData($server_lists);
    }

    // 获取开测列表的数量
    public function getGameTestCount() {
        $where['s.is_delete'] = 2;
        $where['g.status'] = 2;
        $server_lists_count = Db::name('game_test')
            ->alias('s')
            ->join('__GAME__ g', 'g.id=s.app_id', 'INNER')
            ->where($where)
            ->whereTime('s.start_time', 'today')
            ->count();

        return $server_lists_count;
    }

    // 处理开服的数据
    public function handleServerData($data) {
        if (empty($data)) {
            return $data;
        } else {
            $start_time = strtotime(date('Y-m-d'));
            $end_time = $start_time+86400;
            $status = array('1'=>'删档内测', '2'=>'不删档内测');
            foreach ($data as $key=>$val) {
                if ($val['start_time']>$start_time && $val['start_time']<$end_time) {
                    $data[$key]['start_time'] = '今天' . date("H:i", $val['start_time']);
                    $data[$key]['run_time'] = '今天' . date("H:i", $val['start_time']);
                } else {
                    $data[$key]['start_time'] = date('m-d H:i', $val['start_time']);
                    $data[$key]['run_time'] = date('m-d H:i', $val['start_time']);
                }
                $data[$key]['status'] = $status[$val['status']];
            }

            return $data;
        }
    }

    // 获取游戏开服列表信息
    public function GameServerList($where, $limit=30,$orderstr = "") {
        $where['s.is_delete'] = 2;
        $field = "g.type, s.start_time, g.id app_id, g.name game_name";
        $field .=", s.status, ifnull(i.yiosurl,'') ios_down_url, ifnull(i.androidurl,'') android_url";
        $field .=", g.icon";
        $server_lists = Db::name('game_test')
              ->alias('s')
              ->field($field)
              ->join('__GAME__ g', 'g.id=s.app_id', 'inner')
              ->join('__GAME_INFO__ i', 'i.app_id=g.id', 'LEFT')
              ->where($where)
              ->order($orderstr)
              ->limit($limit)
              ->select();

        // 给游戏添加标签
        $game = new Game();
        foreach ($server_lists as $key=>$val) {
            $type_ids = explode(',', $val['type']);
            $type_id = array_shift($type_ids);
            $server_lists[$key]['game_type'] = $game->getTypeName($type_id);
            $server_lists[$key]['run_time'] = date("m-d H:i", $val['start_time']);
        }

        return $this->handleServerData($server_lists);
    }

    // 获取今日开测信息
    public function getTodayServerList() {
        $start_time = strtotime(date('Y-m-d', time()));
        $end_time = strtotime(date('Y-m-d', time()))+86400;
        $where['s.start_time'] = array('BETWEEN', $start_time.','.$end_time);

        return $this->GameServerList($where);
    }

    // 获取即将开测信息
    public function getAboutServerList() {
        $end_time = strtotime(date('Y-m-d', time()))+86400;
        $where['s.start_time'] = array('GT', $end_time);

        return $this->GameServerList($where);
    }

    // 获取已经开测信息
    public function getAfterServerList() {
        $start_time = strtotime(date('Y-m-d', time()));
        $where['s.start_time'] = array('LT', $start_time);

        return $this->GameServerList($where,30,"s.start_time desc");
    }

}