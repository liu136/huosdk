<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-17
 * Time: 20:32
 */
namespace app\index\model;
use think\Model;
use think\Db;
use think\Config;

class Posts extends Model {

    // 查询数据
    public function postData($type=1, $field=true, $limitStr='20', $where=array(),$orderstr = "") {
        if($orderstr == ""){
            $orderstr = 'is_top desc, post_modified desc';
        }
        $where['post_type'] = $type;// 文章类型  0-所有 1-新闻 2-活动 3-攻略 4-评测
        $where['post_status'] = 2;
        $today_new_posts = Db::name('posts')
             ->alias('r')
             ->field($field)
             ->join('__GAME__ g', 'r.app_id=g.id', 'INNER')
//->whereTime('post_date', 'd')
             ->where($where)
             ->order($orderstr)
             ->limit($limitStr)
             ->select();
			 
        foreach ($today_new_posts as $key=>$val) {
            if (isset($val['post_content'])) {
                $today_new_posts[$key]['post_content'] = strip_tags($val['post_content']);
            }
        }
        return $today_new_posts;
    }

    // 今日新闻
    public function postLists($type=1, $limit=10) {
        $field = 'post_title,FROM_UNIXTIME(post_modified, "%Y-%m-%d") post_date, r.id post_id';
        $today_new_posts = $this->postData($type, $field, $limit);

        return $today_new_posts;
    }

    // 获取游戏评测文章
    public function getGameTestPosts($type=1) {
        $where['post_type'] = $type;// 文章类型  0-所有 1-新闻 2-活动 3-攻略 4-公告 5-评测
        $where['post_status'] = 2;
        $field = "r.smeta, r.post_title, r.id post_id,i.score post_score";
        $field .= ", FROM_UNIXTIME(post_modified, '%Y-%m-%d') post_date";
        $game_common_post = Db::name('posts')
             ->alias('r')
             ->field($field)
             ->join('__GAME__ g', 'r.app_id=g.id', 'LEFT')
             ->join('__GAME_INFO__ i', 'i.app_id=g.id', 'LEFT')
             //->whereTime('post_date', 'd')
             ->where($where)
             ->order('is_top desc,post_modified desc')
             ->limit(6)
             ->select();

        return $this->handlePosts($game_common_post);
    }

    // 处理文章的内容
    public function handlePosts($data, $default_img='') {
        if (empty($data)) {
            return $data;
        } else {
            foreach ($data as $key=>$val) {
                if(empty($data[$key]['post_score'])){
                    $data[$key]['post_score'] = number_format(rand(91, 99)/10,1); // 文章评分数
                }

                $post_img = json_decode($data[$key]['smeta'], true);

                if (empty($post_img)) {
                    $data[$key]['post_thumb'] = $default_img;
                } else {
                    $data[$key]['post_thumb'] = $post_img['thumb'];
                }

            }

            return $data;
        }
    }

    // 获取资讯中心的文章资讯
    public function getPostData($type=2,$limit='') {
        $where = array(); // 搜索条件
        $field = 'smeta, post_title,FROM_UNIXTIME(post_modified, "%Y-%m-%d") post_date, r.id post_id, post_content, post_type type_id';
        $postList = $this->postData($type, $field, $limit, $where);
        return $this->handlePosts($postList);
    }

    // 获取资讯的条数
    public function getPostsNum($type=1, $where=array()) {
        return Db::name('posts')
          //->whereTime('post_date', 'd')
          ->where('post_type', $type)
          ->count();
    }

    // 获取资讯中的新游资讯
    public function getNewPost() {
        $field = "r.id post_id, post_title";
        return $this->postData(1, $field, 15);
    }

    // 获取资讯中的新游资讯
    public function getRigtNewPost() {
        $field = "r.id post_id, post_title";
        return $this->postData(1, $field, 10,array(),"is_top desc, post_date desc");
    }

    // 获取单条文章的详情内容
    public function getPostsDetail() {
        $post_id = request()->param('post_id');
        $field = "post_title, post_content, post_author";
        $field .= ", FROM_UNIXTIME(post_modified, '%Y-%m-%d') post_modify";
        $postDetail = Db::name('posts')
            ->field($field)
            ->where('id', $post_id)
            ->find();
        $postDetail['post_author'] = Db::name('users')
            ->where('id', $postDetail['post_author'])->value('user_login');
        $postDetail['post_from'] = Config::get('brand_name');
        //$postDetail['post_content'] = strip_tags($postDetail['post_content']);
        return $postDetail;
    }

    // 获取类似的文章
    public function getSimilarPosts($limit=10) {
        $post_id = request()->param('post_id');
        $data = Db::name('posts')->field('app_id, post_type')->where('id', $post_id)->find();

        $where['app_id'] = $data['app_id']; // 文章的ID
        $where['id'] = array('neq', $post_id); // 文章的ID
        $where['post_status'] = 2;
        $map['post_type'] = $data['post_type']; // 文章的类型
        $field = "post_title, id post_id";
        $similar_post = Db::name('posts')
            ->alias('p')
            ->field($field)
            ->where($where)
            ->whereOr($map)
            ->limit($limit)
            ->order('post_modified desc')
            ->select();

        return $similar_post;
    }

    // 获取最新资讯
    public function NewPost() {
        $field = "post_title, r.id post_id";
        $postData = $this->postData(1, $field, 10);

        return $postData;
    }

    // 获取新游中心中的新游资讯
    public function newGamePost($limit) {
        $game = new Game();
        //$type_id = $game->getGameTypeId();

        // 获取新游的游戏类型ID
        //$app_ids = $game->getTypeIds($type_id);

        // 获取新游的资讯
        $field = "FROM_UNIXTIME(post_modified, '%Y-%m-%d %H:%i:%S') post_modify";
        $field .= ", post_content,post_title, smeta, r.id post_id";

        //if (empty($app_ids)) {
        //    return array();
        //} else {
        $where['is_new'] = 2;
        $postData = $this->postData(1, $field, $limit, $where);
        //}
		
        return $this->handlePosts($postData);
    }

    // 获取游戏专区的资讯
    public function gamezonePost($type, $limitStr) {
        $field = "id post_id, post_title";
        $field .= ", FROM_UNIXTIME(post_modified, '%Y-%m-%d %H:%i:%S') post_modify";
        $app_id = request()->param('app_id');
        $post_news = Db::name('posts')
            ->field($field)
            ->where('app_id', $app_id)
            ->where('post_type', $type)
            ->where('post_status', 2)
            ->limit($limitStr)
            ->select();

        return $post_news;
    }

    // 获取游戏专区的资讯的条数
    public function gamezonePostCount($type) {
        $field = "id post_id, title post_title";
        $field .= ", FROM_UNIXTIME(post_modified, '%Y-%m-%d %H:%i:%S') post_modify";
        $app_id = request()->param('app_id');
        $post_news_count = Db::name('posts')
            ->field($field)
            ->where('app_id', $app_id)
            ->where('post_type', $type)
            ->where('post_status', 2)
            ->count();

        return $post_news_count;
    }

    // 获取文章搜索结果
    public function searchPost() {
        $keyword = request()->param('keyword');
        if (empty($keyword)) {
            return array();
        }
        $where['post_title'] = array('like', "%$keyword%");
        $where['post_status'] = 2;
        $field = "smeta, post_title, id post_id";
        $field .= ", FROM_UNIXTIME(post_modified, '%Y-%m-%d') post_modified";

        $post_list = Db::name('posts')
            ->field($field)
            ->where($where)
            ->select();
        $data = $this->handlePosts($post_list);

        return $data;
    }
}