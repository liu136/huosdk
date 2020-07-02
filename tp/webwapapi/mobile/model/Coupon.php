<?php

namespace app\index\model;

use think\Model;

class Coupon extends Model {

    public function getMemCard($limitStr) {
        $id = Session::get('user.id');
        $field = "code gift_code,title gift_title,g.name game_name";
        $field .= ", g.icon game_icon, t.id gf_id";
        $memCard = Db::name('gift_code')
                     ->alias('c')
                     ->field($field)
                     ->join('__GIFT__ t', 't.id=c.gf_id', 'LEFT')
                     ->join('__GAME__ g', 'g.id=t.app_id')
                     ->where('mem_id', $id)
                     ->limit($limitStr)
                     ->select();

        return $memCard;
    }

    // 获取个人礼包
    public function getMemGift($limitStr) {
        $id = Session::get('user.id');
        $field = "code gift_code,title gift_title,g.name game_name";
        $field .= ", g.icon game_icon, t.id gf_id";
        $memGift = Db::name('gift_code')
                     ->alias('c')
                     ->field($field)
                     ->join('__GIFT__ t', 't.id=c.gf_id', 'LEFT')
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
                     ->join('__GIFT__ t', 't.id=c.gf_id', 'LEFT')
                     ->join('__GAME__ g', 'g.id=t.app_id')
                     ->where('mem_id', $id)
                     ->count();

        return $memGift;
    }
}