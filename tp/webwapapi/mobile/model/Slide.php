<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-17
 * Time: 16:29
 */
namespace app\index\model;
use think\Model;
use think\Db;

class Slide extends Model {

    // 获取首页的推广图
    public function spreadShowGame() {
        $slide_cid = $this->getSlideCid('首页焦点图', 'indexrecmd', 1);
        $slide_data = $this->getShowGame($slide_cid);

        return $slide_data;
    }

    // 获取新游轮播图
    public function newGameSlide($type=1, $limit=4) {
        $slide_cid = $this->getSlideCid('PC-新游轮播图', 'gameslide', $type);
        $slide_data = $this->getShowGame($slide_cid);

        return $slide_data;
    }

    // 获取新游轮播图
    public function welfareSlide($type=1, $limit=4) {
        $slide_cid = $this->getSlideCid('PC-公益服轮播图', 'welfareslide', $type);
        $slide_data = $this->getShowGame($slide_cid);

        return $slide_data;
    }

    // 获取图片数据
    public function getShowGame($slide_cid,$type=2, $limit=6) {
        $field = "ifnull(g.id,0) app_id,g.name, publicity game_desc,slide_pic game_shot";

        $spread_show_game = Db::name('slide')
            ->alias('s')
            ->field($field)
            ->join('__GAME__ g','s.target_id = g.id', 'left')
            ->join('__GAME_INFO__ i','i.app_id = s.target_id', 'left')
            ->join('__SLIDE_CAT__ c','c.cid = s.slide_cid', 'left')
            ->where('s.type_id',$type) //'1 url 2 game 3 gift 4 代金卷',,
            ->where('slide_cid', $slide_cid)
            ->where('slide_status', 2)
			->order('s.slide_name asc')
            ->limit($limit)
            ->select();
			
        return $spread_show_game;
    }

    // 获取PC轮播图的cid
    public function getSlideCid($cat_name='PC轮播图', $cat_idname='gameslide', $cat_slide_id=1) {
        $slide_info = Db::name('slide_cat')
            ->where('cat_name', $cat_name)
            ->find();

        if (empty($slide_info)) {
            $slide_info['cat_name'] = $cat_name;
            $slide_info['cat_idname'] = $cat_idname;
            $slide_info['cat_remark'] = $cat_name;
            $slide_info['cat_status'] = 1;
            $slide_info['cat_type_id'] = $cat_slide_id;
            $slide_info['cid'] = Db::name('slide_cat')->insertGetId ($slide_info);
        }

        return $slide_info['cid'];
    }

    // 获取单张资讯广告图
    public function getSinglePic() {
        $cid = $this->getSlideCid('资讯广告图', 'zxads', 3);
        return Db::name('slide')->where(array('slide_cid'=>$cid))->value('slide_pic');
    }

    // 获取单张资讯背景图
    public function getBackgroundPic() {
        $cid = $this->getSlideCid('资讯背景图', 'zxbackground', 3);
        return Db::name('slide')->where(array('slide_cid'=>$cid))->value('slide_pic');
    }


    // 获取登录背景
    public function loginBackground() {
        $backgroundImg = Db::name('slide')
                           ->where('slide_name', '登录背景图')
                           ->find();
        if (empty($backgroundImg)) {
            $data['slide_name'] = '登录背景图';
            $data['slide_status'] = 2;
            $data['type_id'] = 6;
            $data['slide_cid'] = Db::name('slide_cat')
                ->where('cat_idname', 'webpic')
                ->value('cid');
            $id = Db::name('slide')->insertGetId($data);

            $backgroundImg = Db::name('slide')
                               ->where('slide_name', '登录背景图')
                               ->find();
        }

        return $backgroundImg['slide_pic'];
    }

    // 获取资讯推广图
    public function getZxAds() {
        $backgroundImg = Db::name('slide')
                           ->where('slide_name', '资讯广告图')
                           ->find();

        if (empty($backgroundImg)) {
            $data['slide_name'] = '资讯广告图';
            $data['slide_status'] = 2;
            $data['type_id'] = 1;
            $id = Db::name('slide')->insertGetId($data);

            $backgroundImg = Db::name('slide')
                   ->where('slide_name', '资讯广告图')
                   ->find();
        }

        return $backgroundImg['slide_pic'];
    }

}