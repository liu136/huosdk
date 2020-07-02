<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-18
 * Time: 16:04
 */
namespace app\index\controller;
use app\index\common\Base;
use app\index\model\Slide;
use app\index\model\Game;

class PublicBenefit extends Base {

    // 获取公益服信息
    public function index(Slide $slide, Game $game) {
        // 获取轮播图信息
        $benefit_game['game_slides'] = $slide->welfareSlide();

        // 获取热门游戏
        $limitStr = $this->getPageString();
        $benefit_game['game_icon'] = $game->getPublicGames('公益服', $limitStr);
        $benefit_game['game_list_count'] = $game->getPublicGamesCount();
        $benefit_game['list_row'] = 10;
        return $benefit_game;
    }

}