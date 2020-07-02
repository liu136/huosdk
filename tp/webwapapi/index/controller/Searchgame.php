<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-18
 * Time: 15:00
 */
namespace app\index\controller;
use app\index\common\Base;
use app\index\model\Game;
use app\index\model\Gift;
use app\index\model\Posts;

class SearchGame extends base {

    // 获取找游戏的信息
    public function index(Game $game) {
        // 获取游戏来源分类
        $search_game['platform'] = $game->gamePlatfromClass();
        $search_game['classify'] = $game->getGameType();
        $search_game['feature'] = $game->getGameFeature();

        // 获取新游戏
        $limit_str = $this->getPageString(25);
        $search_game['new_games'] = $game->getNewGameRank($limit_str);

        // 获取热门游戏
        $search_game['hot_games'] = $game->getHotGameRank($limit_str);

        // 获取分页总条数
        $search_game['new_game_count'] = $game->getNewGameRankCount();
        $search_game['hot_game_count'] = $game->getHotGameRankCount();

        return $search_game;
    }

    // 获取热门的分页数据
    public function hotGamePageData(Game $game) {
        $search_game['list_row'] = 25;
        $limitStr = $this->getPageString($search_game['list_row']);
        $search_game['hot_games'] = $game->getHotGameRank($limitStr);
        $search_game['hot_game_count'] = $game->getHotGameRankCount();

        return $search_game;
    }

    // 获取最新的分页数据
    public function newGamePageData(Game $game) {
        $search_game['list_row'] = 25;
        $limitStr = $this->getPageString($search_game['list_row']);
        $search_game['new_games'] = $game->getNewGameRank($limitStr);
        $search_game['new_game_count'] = $game->getNewGameRankCount();

        return $search_game;
    }

    // 获取搜索结果
    public function searchResult(Game $game, Gift $gift, Posts $post) {
        // 获取游戏的搜索结果
        $data['game_list'] = $game->searchGame();

        // 获取礼包搜索结果
        $data['gift_list'] = $gift->serchGift();

        // 获取文章搜索结果
        $data['post_list'] = $post->searchPost();

        return $data;
    }
}
