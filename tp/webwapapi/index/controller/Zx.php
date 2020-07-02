<?php
/**
 *  新闻资讯中心
 */
namespace app\index\controller;

use app\index\common\Base;
use app\index\model\Posts;
use app\index\model\Slide;
use app\index\model\Game;
use think\Db;

class Zx extends Base {

    // 获取文章信息
    public function index(Posts $posts, Slide $slide, Game $game) {
        // 获取限制条件
        $limitStr = $this->getPageString(8);

        // 获取活动资讯的数据和条数
        $postData['post_activity_data'] = array_values($posts->getPostData(2, $limitStr));
        $postData['post_activity_count'] = $posts->getPostsNum(2);

        // 获取游戏评测的数据和条数
        $postData['post_game_report_data'] = array_values($posts->getPostData(4, $limitStr));
        $postData['post_game_report_count'] = $posts->getPostsNum(4);

        // 获取游戏攻略的数据和条数
        $postData['post_stragety_data'] = array_values($posts->getPostData(3, $limitStr));
        $postData['post_stragety_count'] = $posts->getPostsNum(3, $limitStr);

        // 获取新游资讯的数据和条数
        $postData['post_game_new_data'] = array_values($posts->getPostData(1, $limitStr));
        $postData['post_game_new_count'] = $posts->getPostsNum(1, $limitStr);

        // 获取轮播图
        $postData['img_ads'] = $slide->getSinglePic();

        // 获取侧边栏的资讯标题
        $postData['new_game_post'] = $posts->getRigtNewPost();

        // 游戏周----取自热门游戏的数据
        $postData['hot_game_rank'] = $game->getHotDown();

        return $postData;
    }

    // 获取一篇文章的详情
    public function getPostDetail(Posts $posts, Slide $slide, Game $game) {
        // 获取文章的背景图片
        $post_data['background_img'] = $slide->getBackgroundPic();

        // 获取轮播图
        $postData['img_ads'] = $slide->getSinglePic();

        // 获取侧边栏的资讯标题
        $postData['new_game_post'] = $posts->getRigtNewPost();

        // 游戏周----取自热门游戏的数据
        $postData['hot_game_rank'] = $game->getHotDown();

        // 类似的文章
        $postData['similar_posts'] = $posts->getSimilarPosts();

        // 获取文章的详情内容
        $post_detail = $posts->getPostsDetail();

        $postData = array_merge($post_detail, $postData);

        return $postData;
    }

    // 获取资讯页面的推广图
    public function getZxImage(Slide $slide) {
        $image = $slide->getZxAds();

        return $image;
    }

    // 获取分页的数据
    public function getPagePostData(Posts $posts) {
        $type_id = request()->param('type_id');
        $limitStr = $this->getPageString(8);
        $data['post_page_data'] = $posts->getPostData($type_id, $limitStr);
        $data['post_data_count'] = $posts->getPostsNum($type_id);
        $data['list_row'] = 10;

        return $data;
    }
}