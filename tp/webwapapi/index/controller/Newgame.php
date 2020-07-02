<?php
/**
 *  新游控制器
 * @date 2017年3月21日14:30:36
 * @author wangchuang
 */
namespace app\index\controller;

use app\index\common\Base;
use app\index\model\GameServer;
use app\index\model\Posts;
use app\index\model\Slide;
use app\index\model\Game;
use app\index\model\GameTest;
use app\index\model\Gift;

class NewGame extends Base {

    public function index(Slide $slide, Posts $posts, Game $game,
                          GameTest $gameTest) {
        // 获取轮播图信息
        $newGameData['game_slides'] = $slide->newGameSlide();

        // 获取新游资讯
        $newGameData['new_game_zx'] = $posts->newGamePost(5);

        // 新游期待榜
        $newGameData['new_game_rank'] = $game->getNewGameHope();

        // 新游入库
        $newGameData['new_game_list'] = $game->newAddGame();

        // 开测表
        $newGameData['server_test_list'] = $gameTest->getGameTestServers();
        $newGameData['server_test_count'] = $gameTest->getGameTestCount();

        return $newGameData;
    }

    // 获取游戏专区信息
    public function gameZone(Game $game, GameServer $gameServer,
                             Gift $gift, Posts $posts) {
        // 获取游戏的信息
        $data['game_info'] = $game->getGameZoneInfo();

        // 获取开服信息
        $data['server_list'] = $gameServer->GameServerList();

        // 获取礼包列表
        $limitStr = $this->getPageString(10);
        $giftList = $gift->gamezoneGift($limitStr);
        $data = array_merge($data, $giftList);

        // 获取新闻资讯
        $data['post_news'] = $posts->gamezonePost(1, $limitStr);
        //$data['post_news_count'] = $posts->gamezonePostCount(1);

        // 获取策略资讯
        $data['post_stragetys'] = $posts->gamezonePost(3, $limitStr);
        //$data['post_stragetys_count'] = $posts->gamezonePostCount(3);

        // 猜你喜欢
        $data['game_like'] = $game->memLikeGame();

        // 获取游戏评论信息
        $data['game_comment'] = $game->gameComments();
        $data['member_comment'] = $game->memGameComments();

        return $data;
    }
}
