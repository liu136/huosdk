<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-17
 * Time: 17:54
 */
namespace app\index\model;
use think\Model;
use think\Db;
use think\Session;

class Gift extends Model {

    // 获取首页的礼包
    public function fightGameGift($limit) {
        $where['g.is_delete'] = 2;
        $where['gt.remain'] = array('GT', 0);
        $where['gt.is_delete'] = 2;
        $where['g.is_app'] = 2;
        //$where['start_time'] = array('GT', $now_time);
        $where['gt.end_time'] = array('GT', time());

        //$where['gt.is_hot'] = 2;// 礼包中的热门
        $gift_list = Db::name('gift')
            ->alias('gt')
            ->field('gt.id,name, icon, gt.id gift_id,title gift_title')
            ->join('__GAME__ g', 'g.id=gt.app_id', 'inner')
            ->where($where)
            //->where('gt.end_time', 'LT', time())
            ->order('gt.hits_cnt desc,gt.is_rmd,gt.create_time desc')
            //->order('gt.is_rmd, gt.create_time desc')
            ->limit($limit)
            ->select();
        return $gift_list;
    }

    // 获取礼包中心的礼包分类
    public function getGiftType($limitStr='') {
        $where['g.is_delete'] = 2;
        $where['g.is_app'] = 2;
        $where['gt.is_delete'] = 2;

        // {"game_icon":"http://image.9game.cn/2017/3/9/16206597.png",
        // "game_name":"萌妖天团", "gift_type_num":"56","app_id":12},
        //$where['start_time'] = array('GT', $now_time);
        //$where['end_time'] = array('LT', $now_time);
        $field = 'count("app_id") gift_type_num, name, icon,title gift_title,app_id';
        $field .= ', remain, total, icon game_icon, name game_name';
        $gift_list = Db::name('gift')
               ->alias('gt')
               ->field($field)
               ->join('__GAME__ g', 'g.id=gt.app_id', 'inner')
               ->where($where)
               ->where('gt.end_time', 'GT', time())
               ->order('g.listorder desc,gt.create_time desc')
               ->group('name')
                ->limit($limitStr)
               ->select();

        foreach ($gift_list as $key=>$val) {
            $bfbData = $this->getBfb($val['remain'], $val['total']);
            $gift_list[$key]['remain_bfb'] = $bfbData;
        }

        return $gift_list;
    }

    // 获取礼包中心的条数
    public function giftTypeCount() {
        $where['g.is_delete'] = 2;
        $where['gt.is_delete'] = 2;
        $where['g.is_app'] = 2;

        $field = 'count("app_id") gift_cnt, name, icon,title gift_title,app_id';
        $field .= ', remain, total';
        $gift_list = Db::name('gift')
               ->alias('gt')
               ->field($field)
               ->join('__GAME__ g', 'g.id=gt.app_id', 'inner')
               ->where($where)
               ->where('gt.end_time', 'GT', time())
               ->order('gt.create_time desc')
               ->group('name')
               ->select();

        return count($gift_list);
    }

    // 获取百分比
    public function getBfb($remain, $total) {
        if (empty($total)) {
            return '0%';
        }
        return round(($remain/$total)*100, 0).'%';
    }

    // 获取某个游戏的各种礼包
    public function getGameGift($limitStr='') {
        $app_id = request()->param('app_id');
        $field = 'id gift_id, title gift_title, content gift_content,remain, total';
        $gameList = Db::name('gift')
            ->field($field)
            ->where('app_id', $app_id)
            //->where('remain', 'GT', 0)
            ->where('is_delete', 2)
            ->where('end_time', 'GT', time())
            //->where('start_time', 'LT', time())
            //->where('end_time', 'GT', time())
            ->order('is_hot desc, remain desc')
            ->limit($limitStr)
            ->select();

        foreach ($gameList as $key=>$val) {
            $gameList[$key]['remain_bfb'] = $this->getBfb($val['remain'], $val['total']);
        }
        return $gameList;
    }

    // 获取单个游戏的礼包种类数量
    public function SingleGameGiftTypeCount($app_id) {
        return Db::name('gift')
            ->where('app_id', $app_id)
            ->where('is_delete', 2)
            ->where('gt.end_time', 'GT', time())
            ->count();
    }

    // 获取礼包的详情
    public function getGiftDetail() {
        $gift_id = request()->param('gift_id');

        $giftData = $this->singleGiftData($gift_id);
        if (!empty($giftData)) {
            $remainBfb = $this->getBfb($giftData['remain'], $giftData['total']);
            $giftData['remain_bfb'] = $remainBfb;
            // 礼包的游戏数据
            $game = new Game();
            $gameData = $game->getSingGameInfo($giftData['app_id']);

            $gameData = array_merge($giftData, $gameData);

            return $gameData;
        } else {
            return array();
        }
    }

    // 获取礼包中心的相关礼包
    public function getGiftRelation() {
        $gift_id = request()->param('gift_id');
        $giftData = $this->singleGiftData($gift_id);
        // 获取相关礼包
        $giftRelation = $this->relationGift($giftData['app_id']);
        foreach ($giftRelation as $key=>$val) {
            $remain_bfb = $this->getBfb($val['remain'], $val['total']);
            $giftRelation[$key]['remain_bfb'] = $remain_bfb;
        }

        return $giftRelation;
    }

    // 获取单个礼包数据
    public function singleGiftData($gift_id) {
        $field = 'title gift_title,id gift_id';
        $field .= ', FROM_UNIXTIME(start_time, "%Y-%m-%d %H:%i:%S") start_time';
        $field .=', FROM_UNIXTIME(end_time, "%Y-%m-%d %H:%i:%S") end_time';
        $field .=', content gift_content, func remark, total, remain, app_id';
        $giftData = Db::name('gift')
             ->field($field)
             ->where('id', $gift_id)
             ->where('end_time', 'GT', time())
             ->find();

        return $giftData;
    }

    // 获取相关礼包
    public function relationGift($app_id) {
        $relationGift = Db::name('gift')
              ->alias('gt')
              ->field('gt.id as gift_id,g.icon game_icon, gt.title gift_title, remain, total')
              ->join('__GAME__ g', 'g.id=gt.app_id', 'INNER')
              ->where('app_id', $app_id)
              ->where('gt.end_time', 'GT', time())
              ->order('gt.is_hot desc, remain desc')
              ->select();

        return $relationGift;
    }

    // 获取个人礼包
    public function getMemGift($limitStr) {
        $id = Session::get('user.id');
        $field = "code gift_code,title gift_title,g.name game_name";
        $field .= ", g.icon game_icon, t.id gf_id";
        $memGift = Db::name('gift_code')
            ->alias('c')
            ->field($field)
            ->join('__GIFT__ t', 't.id=c.gf_id', 'inner')
            ->join('__GAME__ g', 'g.id=t.app_id')
            ->where('mem_id', $id)
            ->limit($limitStr)
            ->select();

        return $memGift;
    }

    // 获取玩家所有的礼包数
    public function memGiftCount() {
        $id = Session::get('user.id');
        $memGift = Db::name('gift_code')
            ->alias('c')
            ->join('__GIFT__ t', 't.id=c.gf_id', 'inner')
            ->join('__GAME__ g', 'g.id=t.app_id', 'inner')
            ->where('mem_id', $id)
            ->count();

        return $memGift;
    }

    // 获取游戏专区的礼包列表
    public function gamezoneGift($limitStr=10) {
        $now_time = time();
        $app_id = request()->param('app_id');
        $field = "id gift_id, title gift_name";
        $field .= ', FROM_UNIXTIME(start_time, "%Y-%m-%d") start_time';
        $field .=', FROM_UNIXTIME(end_time, "%Y-%m-%d") end_time';
        $field .=', content gift_content, total, remain, is_delete';
        $giftList['gift_info'] = Db::name('gift')
            ->alias('gt')
            ->field($field)
            ->where('app_id', $app_id)
            ->where('gt.end_time', 'GT', $now_time)
            ->limit($limitStr)
            ->select();

        $giftList['gift_count'] = Db::name('gift')
            ->alias('gt')
            ->field($field)
            ->where('app_id', $app_id)
            ->where('gt.end_time', 'GT', $now_time)
            ->count();

        foreach ($giftList['gift_info'] as $key=>$val) {
            $giftList['gift_info'][$key]['gift_type'] = '个人';
            $giftList['gift_info'][$key]['platform'] = '全平台';
            if (2 == $val['is_delete']) {
                $giftList['gift_info'][$key]['status'] = '正常';
            }
        }

        return $giftList;
    }

    // 获取搜索的礼包
    public function serchGift() {
        $keyword = request()->param('keyword');
        $where['gt.title'] = array('like', "%$keyword%");
        $field = "g.icon game_icon, gt.title gift_title, remain, total, gt.id gift_id";
        $gift_list = Db::name('gift')
            ->alias('gt')
            ->join('__GAME__ g', 'g.id=gt.app_id', 'inner')
            ->field($field)
            ->where($where)
            ->where('gt.end_time', 'GT', time())
            ->select();

        // 获取百分比
        foreach ($gift_list as $key => $val) {
            $remain_bfb = $this->getBfb($val['remain'], $val['total']);
            $gift_list[$key]['remain_bfb'] = $remain_bfb;
        }

        return $gift_list;
    }

    //对包人气+1
    public function incGifthits(){
        $gift_id = request()->param('gift_id');
        Db::name('gift')->where('id', $gift_id)->setInc('hits_cnt');
    }

}