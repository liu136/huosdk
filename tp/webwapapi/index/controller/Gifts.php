<?php
/**
 *  礼包中心
 */
namespace app\index\controller;

use app\index\common\Base;
use app\index\model\Gift;
use app\index\model\Game;
use app\index\model\Members;
use app\index\model\Posts;

class Gifts extends Base {

    // 礼包中心
    public function index(Gift $gift) {
        $limitStr = $this->getPageString();
        $gift_data['gift_list'] = $gift->getGiftType($limitStr);
        $gift_data['gift_game_count'] = $gift->giftTypeCount();

        return $gift_data;
    }

    // 获取游戏的各种礼包
    public function gameGift(Gift $gift, Game $game) {
        // 获取游戏的信息
        $giftList['game_info'][] = $game->getGameinfo();

        // 获取游戏的礼包列表
        $giftList['gift_list'] = $gift->getGameGift();

        return $giftList;
    }

    // 获取游戏的礼包列表分页数据
    public function getGiftPageData(Gift $gift) {
        $limitStr = $this->getPageString();
        $giftList['gift_list'] = $gift->getGameGift($limitStr);
        $giftList['gift_list_count'] = count($gift->getGameGift());
        $giftList['list_row'] = 10;

        return $giftList;
    }

    // 礼包详情
    public function giftDetail(Gift $gift, Posts $posts, Game $game) {
        //20170525新增， 对应礼包人气+1
        $gift->incGifthits();
        // 获取礼包的信息
        $giftDetailInfo['gift_info'][] = $gift->getGiftDetail();

        // 获取相关礼包
        $giftDetailInfo['gift_relation'] = $gift->getGiftRelation();

        // 礼包的游戏的信息
        $giftDetailInfo['game_info'][] = $game->getGiftGameInfo();
        // 获取最新资讯
        $giftDetailInfo['game_news'] = $posts->NewPost();

        return $giftDetailInfo;
    }

    // 领取礼包
    public function getGiftCode(Members $members) {
        $giftInfo = $members->getGiftCode();

        return $giftInfo;
    }


}