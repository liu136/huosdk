<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-18
 * Time: 9:19
 */
namespace app\index\model;
use think\Model;
use think\Db;

class GameRecmd extends Model {
    // 获取手机游戏推荐
    public function getRecommondGameList($limit=15) {
        $where['r.is_delete'] = 2;
        $where['r.status'] = 2;
        $where['g.is_app'] = 2;

        $field = "g.name, g.icon, g.id app_id, ifnull(i.yiosurl,'') iosurl, ifnull(i.androidurl,'') android_url";
        $recmd_game_lists = Db::name('game_recmd')
            ->alias('r')
            ->field($field)
            ->join('__GAME__ g', 'r.app_id=g.id', 'INNER')
            ->join('__GAME_INFO__ i', 'i.app_id=g.id', 'LEFT')
            ->where($where)
            ->order('g.listorder desc,r.create_time desc')
            ->limit($limit)
            ->select();

        return $recmd_game_lists;
    }
}