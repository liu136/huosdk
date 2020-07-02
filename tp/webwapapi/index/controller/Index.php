<?php
/**
 *  @date 2017年3月7日15:48:07
 *  @author wangchuang
 */
namespace app\index\controller;

use app\index\common\Base;
use app\index\model\Slide;
use app\index\model\Game;
use app\index\model\Gift;
use app\index\model\Posts;
use app\index\model\GameServer;
use app\index\model\GameTest;
use app\index\model\GameRecmd;
use app\index\model\WebLinks;

class Index extends Base
{
    // 展示首页
    public function index() {
        $this->redirect(WEBSITE."/index.html");
        exit;
    }

    // 获取首页的数据
    public function getIndexData(Slide $slide, Game $game, Gift $gift,
                                 Posts $posts, GameServer $gameServer,
                                 GameTest $gameTest, GameRecmd $gameRecmd,
                                 WebLinks $webLinks) {
        // 获取首页展示的推广图
        $index_data['spread_show_game'] = $slide->spreadShowGame();

        // 获取新游戏
        $index_data['new_game_list'] = $game->getGameList(array("g.is_new" =>2));
        // 获取热门游戏
        $index_data['hot_game_list'] = $game->getGameList(array("g.is_hot" =>2));
        // 获取公益服游戏

        $index_data['public_game_list'] = $game->getGameList(array("g.is_welfare" =>2));

        // 抢礼包
        $index_data['fight_game_gift'] = $gift->fightGameGift(5);

        // 今日新闻
        $index_data['today_new_posts'] = $posts->postLists();

        // 今日活动
        $index_data['today_activity_posts'] = $posts->postLists(2);

        // 新游戏排行榜
        $index_data['new_game_rank'] = $game->getNewGameRank(10);

        // 游戏评测
        $index_data['game_common_post'] = $posts->getGameTestPosts(5);

        // 开服列表
        $index_data['run_server_list'] = $gameServer->getGameRunServers(7);

        // 开测列表
        $index_data['test_server_list'] = $gameTest->getGameTestServers(7);

        // 手机游戏推荐
        $index_data['recommond_game_list'] = $gameRecmd->getRecommondGameList();

        // 手机下载排行榜
        $index_data['down_game_rank'] = $game->getDownGameRank();

        // 游戏分类
        $index_data['game_classify'] = $game->getGameClassify();

        // 获取友情链接
        $index_data['link_url'] = $webLinks->getWebLinks();

        // 获取首页背景和页脚广告图
        $index_data['index_image'] = $slide->bgSlide();
        $index_data['indexbgurl'] = $slide->bgUrl();

        return $index_data;
    }
	
	// 获取SEO的信息
	public function getIndexSeo() {
		$seotitle = $this->getSeo();
		return $seotitle;
	}
	
}
