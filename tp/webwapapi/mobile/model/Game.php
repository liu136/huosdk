<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-17
 * Time: 14:30
 */
namespace app\index\model;
use think\Model;
use think\Db;
use think\Config;

class Game extends Model {

    // 获取搜索热词
    public function getName() {
		$data = Db::name('options')->where('option_name', 'hot_search_key')->value('option_value');

		if (empty($data)) {
			$search_name = Db::name('game')
            ->field('id app_id, name game_name')
            ->order('listorder desc, create_time desc')
            ->limit(4)
            ->select();
		} else {
			$hot_key = json_decode($data, true);
			$new_key = array_values($hot_key);
            $search_name = array();
			foreach ($new_key as $key=>$val) {
				$app_id = Db::name('game')->where('name', 'like', "%$val%")->value('id');
				$search_name[$key]['app_id'] = $app_id ?: 0;
				$search_name[$key]['game_name'] = $val;
				if($search_name[$key]['app_id'] == 0){
				    unset($search_name[$key]);
                }
			}
            $search_name = array_values($search_name);
		}

        return $search_name;
    }

    // 获取热门游戏
    public function getHotGame() {
        $hot_games = Db::name('game')
            ->field('name,id app_id')
            ->where('is_hot', 2)
            ->order('listorder desc,create_time desc')
            ->limit(9)
            ->select();

        return $hot_games;
    }

    /**
     * @access 获取某个标签的游戏列表
     * @param string $gtype  游戏标签
     * @param int    $limit  限制数量
     *
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getGameList($_map=array(), $limit=20) {
        //$type_id = $this->getGameTypeId($gtype);
        //$where['t.type_id'] = array('eq', $type_id);
        //$where['g.is_new'] = 2;
        $where['g.status'] = 2;
        $game_list = Db::name('game')
            ->alias('g')
            ->field("name, icon, id app_id")
            //->join('__GAME_GT__ t', 'g.id=t.app_id' ,'left')
            ->where($where)
            ->where($_map)
            ->group("g.id")
            ->order('g.listorder desc, g.create_time desc')
            ->limit($limit)
            ->select();

        return $game_list;
    }

    // 获取游戏类型的ID
    public function getGameTypeId($gtype='新游') {
        $type_id = Db::name('game_type')->where('name', $gtype)->value('id');
        if (empty($type_id)) {
            $data['name'] = $gtype;
            $type_id = Db::name('game_type')->insertGetId($data);
        }

        return $type_id;
    }

    // 获取新游戏
    public function getNewGameRank($limit = 10) {
        // 获取最新游戏
        $where['g.is_new'] = 2;
        //$type_id = $this->getGameTypeId('新游');
       // $where['gg.type_id'] = $type_id;
        $where['g.status'] = 2;
        //20170525要求热门
        //$where['g.is_hot'] = 2;
        $where['g.is_app'] = 2;

        if (request()->param('classify_name')) {
            $map = $this->searchWhere();
            $where = array_merge($where, $map);
        }

        $field = "g.id app_id, g.name,g.icon, gt.name game_type, g.listorder hot_num";
        $field .= ", ifnull(i.yiosurl,'') ios_url, ifnull(i.androidurl,'') android_url";
        $game_lists = Db::name('game_gt')
            ->alias('t')
            ->field($field)
            ->join('__GAME__ g', 't.app_id=g.id', 'inner')
            ->join('__GAME_INFO__ i', 'i.app_id=g.id', 'LEFT')
            ->join('__GAME_TYPE__ gt', 't.type_id=gt.id', 'LEFT')
            ->join('__GAME_GT__ gg', 'gg.app_id=t.app_id', 'LEFT')
            ->where($where)
            //->where("gg.type_id",$type_id)
            ->order('g.listorder desc, g.create_time desc')
            ->group('g.id')
            ->limit($limit)
            ->select();

        return $game_lists;
    }

    // 获取新游戏的条数
    public function getNewGameRankCount() {
        // 获取最新游戏
        //$type_id = $this->getGameTypeId('最新');

        $where['g.status'] = 2;
        $where['g.is_new'] = 2;
        $where['g.is_app'] = 2;
        if (request()->param('classify_name')) {
            $map = $this->searchWhere();
            $where = array_merge($where, $map);
        }

        $field = "g.id app_id, g.icon, g.name, gt.name game_type, g.listorder hot_num";
        $field .= ", ifnull(i.yiosurl,'') ios_url, ifnull(i.androidurl,'') android_url";
        $game_lists = Db::name('game_gt')
            ->alias('t')
            ->field($field)
            ->join('__GAME__ g', 't.app_id=g.id', 'LEFT')
            ->join('__GAME_INFO__ i', 'i.app_id=g.id', 'LEFT')
            ->join('__GAME_TYPE__ gt', 't.type_id=gt.id', 'LEFT')
            ->join('__GAME_GT__ gg', 'gg.app_id=t.app_id', 'LEFT')
            ->where($where)
            //->where('gg.type_id', $type_id)
            ->order('g.listorder desc,g.is_hot desc, g.create_time desc')
            ->group('g.id')
            ->select();

        $game_lists_count = count($game_lists);
        return $game_lists_count;
    }

    // 获取找游戏中的热门游戏
    public function getHotGameRank($limit) {
        // 获取最新游戏
        //$type_id = $this->getGameTypeId('最热');
        $where['g.is_hot'] = 2;
        //$where['g.status'] = 2;
        $where['g.is_app'] = 2;
        if (request()->param('classify_name')) {
            $map = $this->searchWhere();
            $where = array_merge($where, $map);
        }
        $field = "g.id app_id, g.name, g.icon, gt.name game_type, g.listorder hot_num";
        $field .= ", ifnull(i.yiosurl,'') ios_url, ifnull(i.androidurl,'') android_url";
        $game_lists = Db::name('game_gt')
            ->alias('t')
            ->field($field)
            ->join('__GAME__ g', 't.app_id=g.id', 'LEFT')
            ->join('__GAME_INFO__ i', 'i.app_id=g.id', 'LEFT')
            ->join('__GAME_TYPE__ gt', 't.type_id=gt.id', 'LEFT')
            ->join('__GAME_GT__ gg', 'gg.app_id=t.app_id', 'LEFT')
            ->where($where)
            //->where('gg.type_id', $type_id)
            ->order('g.listorder desc,g.is_hot desc, g.create_time desc')
            ->group('g.id')
            ->limit($limit)
            ->select();

        return $game_lists;
    }

    // 获取热门游戏的条数
    public function getHotGameRankCount() {
        // 获取最热游戏
        //$type_id = $this->getGameTypeId('最热');
        //$where['g.status'] = 2;
        $where['g.is_hot'] = 2;
        $where['g.is_app'] = 2;
        if (request()->param('classify_name')) {
            $map = $this->searchWhere();
            $where = array_merge($where, $map);
        }
        $field = "g.id app_id, g.name, g.icon, gt.name game_type, g.listorder hot_num";
        $field .= ", ifnull(i.yiosurl,'') ios_url, ifnull(i.androidurl,'') android_url";
        $game_lists = Db::name('game_gt')
            ->alias('t')
            ->field($field)
            ->join('__GAME__ g', 't.app_id=g.id', 'LEFT')
            ->join('__GAME_INFO__ i', 'i.app_id=g.id', 'LEFT')
            ->join('__GAME_TYPE__ gt', 't.type_id=gt.id', 'LEFT')
            ->join('__GAME_GT__ gg', 'gg.app_id=t.app_id', 'LEFT')
            ->where($where)
            //->where('gg.type_id', $type_id)
            ->order('g.listorder desc,g.is_hot desc, g.create_time desc')
            ->group('g.id')
            ->select();

        $game_lists_count = count($game_lists);
        return $game_lists_count;
    }

    // 下载排行榜
    public function getDownGameRank() {
        $where['g.status'] = 2;
        $where['g.is_delete'] = 2;
        $field = "g.id app_id, g.name, g.type , g.listorder hot_num";
        $field .= ", ifnull(i.yiosurl,'') ios_url, ifnull(i.androidurl,'') android_url";
        $field .= ", if(g.status=2,'上线','下线') status";
        $game_down_list = Db::name('game')
            ->alias('g')
            ->field($field)
            ->join('__GAME_INFO__ i', 'i.app_id=g.id', 'LEFT')
            ->join('__GAME_GT__ t', 't.app_id=g.id', 'LEFT')
            ->join('__GAME_TYPE__ gt', 't.type_id=gt.id', 'LEFT')
            ->join('__GAME_EXT__ ext', 'ext.app_id=g.id', 'LEFT')
            //->join('__GAME_EXT__ e', 'e.app_id=g.id', 'LEFT')
            ->where($where)
            ->group('g.id')
            ->order('down_cnt DESC,g.id DESC')
            ->limit(10)
            ->select();

        foreach ($game_down_list as $key=>$val) {

            $game_down_list[$key]['game_type'] = $this->gameSignTag($val['type']);
        }

        return $game_down_list;
    }

    // 获取游戏分类
    public function getGameClassify() {
        $where['g.status'] = 2;
        $field = "gt.name game_type, g.icon, g.id app_id, g.name";
        $field .= ", ifnull(i.yiosurl,'') ios_url, ifnull(i.androidurl,'') android_url";
        $game_classify = Db::name('game')
            ->alias('g')
            ->field($field)
            ->join('__GAME_INFO__ i', 'i.app_id=g.id', 'LEFT')
            ->join('__GAME_GT__ t', 't.app_id=g.id', 'LEFT')
            ->join('__GAME_TYPE__ gt', 't.type_id=gt.id', 'RIGHT')
            ->where($where)
            ->order('gt.listorder desc, gt.id desc')
            ->select();

        // 获取所有的游戏的分类
        $game_types = Db::name('game_type')->column('name');

        $game_classify_new = array();
        foreach ($game_classify as $key=>$val) {
            foreach ($game_types as $k=>$v) {
                if ($val['game_type'] == $v) {
                    $game_classify_new[$k]['classify'] = $v;
                    $game_classify_new[$k]['games'][] = $val;
                    if (count($game_classify_new[$k]['games']) >= 5) {
                        unset($game_types[$k]);
                    }
                }
            }

            //if (count($game_classify_new) >= 4) {
            //    break;
            //}
        }

        $game_classify_new = array_slice($game_classify_new,0,4);
        return array_values($game_classify_new);
    }

    // 获取游戏的来源分类
    public function gamePlatfromClass() {
        /*return Db::name('game_class')
            ->field("realname platform_name")
            ->where('status', 2)
            ->select();*/
        return array(
            array("platform_name"=>'Android'),
            array("platform_name"=>'IOS'),
            array("platform_name"=>'H5')
        );
    }

    // 获取游戏分类
    public function getGameType() {
        return Db::name('game_type')
            ->field("name classify_name")
            ->where('parentid', 0)
            ->select();
    }

    // 获取游戏的特性
    public function getGameFeature() {
        return Db::name('game_type')
            ->field("name feature_name")
            ->where('parentid', 'neq', 0)
            ->select();

    }

    // 获取公益服游戏
    public function getPublicGames($gtype = '公益服', $limit = 25) {

        $field = "gt.name game_type, g.icon game_icon, g.id app_id";
        $field .= ", g.name game_name, ifnull(i.yiosurl,'') ios_down_url, ifnull(i.androidurl,'') android_down_url";

        $where['g.status'] = 2;
        $where['g.is_welfare'] = 2;
        $game_list = Db::name('game')
                       ->alias('g')
                       ->field($field)
                       ->join('__GAME_GT__ t', 'g.id=t.app_id' ,'INNER')
                       ->join('__GAME_INFO__ i', 'i.app_id=g.id', 'LEFT')
                       ->join('__GAME_TYPE__ gt', 't.type_id=gt.id', 'RIGHT')
                       ->where($where)
                       ->group('g.id')
                       ->order('g.listorder desc, g.create_time desc')
                       ->select();

        return $game_list;
    }

    // 获取公益服游戏总数
    public function getPublicGamesCount($gtype = '公益服') {
        //$type_id = $this->getGameTypeId($gtype);
        $where['g.status'] = 2;
        $where['g.is_welfare'] = 2;
        $game_list = Db::name('game')
                       ->alias('g')
                       ->join('__GAME_GT__ t', 'g.id=t.app_id' ,'LEFT')
                       ->join('__GAME_INFO__ i', 'i.app_id=g.id', 'LEFT')
                       ->join('__GAME_TYPE__ gt', 't.type_id=gt.id', 'RIGHT')
                       ->group('g.id')
                       ->where($where)
                       ->count();

        return $game_list;
    }

    // 获取公益服游戏
    public function getPublicGame($gtype = '公益服', $limit = 25) {
        $type_id = $this->getGameTypeId($gtype);
        $field = "gt.name game_type, g.icon game_icon, g.id app_id";
        $field .= ", g.name game_name, ifnull(i.yiosurl,'') ios_url, ifnull(i.androidurl,'') android_url";
        $where['t.type_id'] = array('eq', $type_id);
        $where['g.status'] = 2;
        $game_list = Db::name('game')
            ->alias('g')
            ->field($field)
            ->join('__GAME_GT__ t', 'g.id=t.app_id' ,'LEFT')
            ->join('__GAME_INFO__ i', 'i.app_id=g.id', 'LEFT')
            ->join('__GAME_TYPE__ gt', 't.type_id=gt.id', 'RIGHT')
            ->where($where)
            ->order('g.listorder desc, g.create_time desc')
            ->select();

        return $game_list;
    }

    // 获取公益服游戏总数
    public function getPublicGameCount($gtype = '公益服') {
        $type_id = $this->getGameTypeId($gtype);
        $where['t.type_id'] = array('eq', $type_id);
        $where['g.status'] = 2;
        $game_list = Db::name('game')
               ->alias('g')
               ->join('__GAME_GT__ t', 'g.id=t.app_id' ,'LEFT')
               ->join('__GAME_INFO__ i', 'i.app_id=g.id', 'LEFT')
               ->join('__GAME_TYPE__ gt', 't.type_id=gt.id', 'RIGHT')
               ->where($where)
               ->count();

        return $game_list;
    }

    // 处理对应的游戏type
    public function convertType($type) {
        if (empty($type)) {
            return 0;
        } else {
            $game_type = $this->gameSignTag($type);
        }

        return $game_type;
    }

    // 获取游戏的多个标签
    public function gameMulTag($type_ids) {
        $game_type = Db::name('game_type')
            ->where('id', 'in', $type_ids)
            ->limit(4)
            ->order('id desc')
            ->column('name');

        return $game_type;
    }

    // 获取单个标签
    public function gameSignTag($type) {
        $type_ids = explode(',', $type);
        $type_id = array_pop($type_ids);
        $game_type = $this->getTypeName($type_id);

        return $game_type;
    }

    // 获取游戏对应的标签
    public function getTypeName($type_id) {
        return Db::name('game_type')->where('id', $type_id)->value('name');
    }

    // 获取热门下载的数据,按照下载量来排序的
    public function getHotDown($limit=10) {
        $field = "gt.name game_types, g.icon game_icon, g.id app_id";
        $field .= ", g.name game_name, ifnull(i.yiosurl,'') ios_url, ifnull(i.androidurl,'') android_url";
        $field .= ", e.down_cnt, i.size game_size";
        $down_game = Db::name('game')
            ->alias('g')
            ->field($field)
            ->join('__GAME_EXT__ e ', 'e.app_id=g.id', 'LEFT')
            ->join('__GAME_GT__ t', 'g.id=t.app_id' ,'LEFT')
            ->join('__GAME_INFO__ i', 'i.app_id=g.id', 'LEFT')
            ->join('__GAME_CLIENT__ c', 'c.app_id=g.id', 'LEFT')
            ->join('__GAME_TYPE__ gt', 't.type_id=gt.id', 'RIGHT')
            ->limit($limit)
            ->group('g.id')
            ->order('e.down_cnt desc')
            ->select();

        return $down_game;
    }

    // 获取游戏的信息
    public function getGameInfo() {
        $app_id = request()->param('app_id');
        $gameInfo = $this->getSingGameInfo($app_id);

        // 获取下载地址
        $gameDownUrl = $this->getDownUrl($app_id);
        $gameInfo = array_merge($gameInfo, $gameDownUrl);

        // 获取游戏的礼包种类数
        $gift = new Gift();
        $gameInfo['gift_counts'] = $gift->SingleGameGiftTypeCount($app_id);

        return $gameInfo;
    }

    // 获取单个游戏的信息
    public function getSingGameInfo($app_id) {
        $field = 'name game_name, icon game_icon, id app_id';
        $gameInfo = Db::name('game')
              ->field($field)
              ->where('id', $app_id)
              ->find();

        return $gameInfo;
    }

    // 获取单个游戏的Android和ios下载地址
    public function getDownUrl($app_id) {
        $field = "ifnull(yiosurl,'') ios_url, ifnull(androidurl,'') android_url, description game_desc";
        $gameDownUrl = Db::name('game_info')
            ->field($field)
            ->where('app_id', $app_id)
            ->find();

        return $gameDownUrl;
    }

    // 获取某个类型的游戏ID
    public function getTypeIds($type_id) {
        $app_ids = Db::name('game_gt')
            ->where('type_id', 'in', $type_id)
            ->column('app_id');

        return $app_ids;
    }

    // 获取新游中心中的新游期待榜中游戏
    public function getNewGameHope($limit = 10) {
        $field = "g.id app_id, g.name game_name, g.type, g.icon game_icon,ifnull(i.score,'9.5') game_score";
        $field .= ", ifnull(i.yiosurl,'') ios_url, ifnull(i.androidurl,'') android_url";
        //$type_id = $this->getGameTypeId();
       // $app_ids = $this->getTypeIds($type_id);
        //if (empty($app_ids)) {
       //     return array();
       // }
        $where['g.is_new'] = 2;
        //$where['g.id'] = array('in', $app_ids);
        $gameLists = Db::name('game')
            ->alias('g')
            ->field($field)
            ->join('__GAME_INFO__ i', 'i.app_id=g.id', 'LEFT')
            ->where($where)
            ->order('i.score desc')
            ->group('g.id')
            ->limit($limit)
            ->select();

        // 添加标签和评分
        foreach ($gameLists as $key=>$val) {
            $gameLists[$key]['game_type'] = $this->gameSignTag($val['type']);
            //$gameLists[$key]['game_score'] = 9.5;
        }
        return $gameLists;
    }

    // 获取新游入库的游戏
    public function newAddGame() {
        return Db::name('game')
            ->field('id app_id, name game_name, icon game_icon')
            ->where('status', 'eq', 2)
            ->where('is_app', 'eq', 2)
            ->where('id', 'GT', 100)
            ->limit(10)
            ->order("update_time desc")
            ->select();
    }

    // 获取游戏专区的信息
    public function getGameZoneInfo() {
        $app_id = request()->param('app_id');

        // 游戏的人气值加1
        $this->addListorder($app_id);

        $field_game = 'name game_name, icon game_icon, id app_id, type, `status`';
        $gameInfo = Db::name('game')
                      ->field($field_game)
                      ->where('id', $app_id)
                      ->find();

        $gameInfo['type'] = $this->gameMulTag($gameInfo['type']);
        $field_info = "ifnull(yiosurl,'') ios_url, ifnull(androidurl,'') android_url";
        $field_info .= ", description game_desc, image, size";
        $gameDownUrl = Db::name('game_info')
                         ->field($field_info)
                         ->where('app_id', $app_id)
                         ->find();

        if (empty($gameDownUrl)) {
            $gameDownUrl = array();
        } else {
            $gameDownUrl['game_shots'] = $this->handleGameImage($gameDownUrl['image']);
        }

        $gameInfo['down_cnt'] = Db::name('game_ext')
                ->where('app_id', $app_id)
                ->value('down_cnt');

        $gameInfo['version'] = Db::name('game_client')
            ->where('app_id', $app_id)
            ->value('version');

        $gameInfo['developers'] = Config::get('developers');
        $gameInfo = array_merge($gameInfo, $gameDownUrl);


        $gameInfo['status'] = ($gameInfo['status'] == 2) ? '上线' : '接入中';
        return $gameInfo;
    }

    // 获取游戏的人气值
    public function addListorder($app_id) {
        $res = Db::name('game')->where('id', $app_id)->setInc('listorder');
    }

    // 处理游戏的图片
    private function handleGameImage($data, $default_img=array()) {
        if (empty($data)) {
            return $data;
        } else {
            $game_img = json_decode($data, true);

            if (empty($game_img)) {
                return $default_img;
            } else {
                foreach ($game_img as $key=>$val) {
                    $images[] = $game_img[$key]['url'];
                }
            }

            return $images;
        }
    }

    // 猜你喜欢
    public function memLikeGame($limit=8) {
        $app_id = request()->param('app_id');
        $type = Db::name('game')
            ->where('id', 'eq', $app_id)
            ->value('type');

        $app_ids = $this->getTypeIds($type);
        $field = "id app_id, name game_name, icon game_icon";
        $game_like = Db::name('game')
            ->field($field)
            ->where('id', 'in', $app_ids)
            ->where('id', 'neq', $app_id)
            ->where('status', 2)
            ->limit($limit)
            ->select();

        return $game_like;
    }

    /**
     * 待完善这个评论的显示
     * @return array
     */
    public function gameComments() {
        $data['game_comment'] = array(
            "game_avg_socre"=>"3.2",
            "comment_star"=>[
                "1"=>"56%",
                "2"=>"0%",
                "3"=>"0%",
                "4"=>"0%",
                "5"=>"44%",
            ],
            "game_cates"=>["画面超赞","运行流畅","动作带感","音效很棒"],
        );

        return $data;
    }

    // 获取玩家的游戏评论
    public function memGameComments() {
        $app_id = request()->param('app_id');
        $field = "gc.create_time comment_time, gc.content comment_content";
        $field .= "m.username user_name, m.portrait head_img";

        $memberComments = Db::name('game_comments')
            ->alias('gc')
            ->join('__MEMBERS__ m', 'gc.mem_id=m.id', 'LEFT')
            ->where('gc.app_id', $app_id)
            ->select();

        return $memberComments;
    }

    // 获取礼包详情中的游戏信息
    public function getGiftGameInfo() {
        $gift_id = request()->param('gift_id');
        $app_id = Db::name('gift')->where('id', $gift_id)->value('app_id');
        $gameInfo = $this->getSingGameInfo($app_id);
        $gameUrl = $this->getDownUrl($app_id);

        $gameInfo = array_merge($gameInfo, $gameUrl);

        return $gameInfo;
    }

    // 获取搜索的筛选条件
    public function searchWhere() {
        $platform_name = request()->param('platform_name');
        $classify_name = request()->param('classify_name');
        $feature_name = request()->param('feature_name');
        /*if ($platform_name !== '全部') {
            $where['platform_name'] = 1;
        }*/
        if ($classify_name !== '全部') {
            $parentid = Db::name("game_type")
                          ->where("name", $classify_name)->value('id');

            if($feature_name === '全部'){
                $game_type = Db::name('game_type')
                               ->where(array("parentid"=>$parentid))
                               ->whereOr(array("id"=>$parentid))
                               ->column("id");

                if(count($game_type) > 0){
                    $types = implode(",",$game_type);
                    $where['gt.id'] = array("IN", $types);
                }

            }else{
                $where['gt.parentid'] = $parentid;
            }
        }
        if ($feature_name !== '全部') {
            //$where['gt.id'] = Db::name("game_type")
            //    ->where("name", $feature_name)->value('id');
			//$where['gt.parentid'] = isset($where['gt.id'])? $where['gt.id']:0;
			$where['gt.id'] = Db::name("game_type")
				->where("name", $feature_name)->value('id');
        }

        return empty($where) ? array() : $where;
    }

    // 获取搜索的游戏结果
    public function searchGame() {
        $keyword = request()->param('keyword');
        if (empty($keyword)) {
            return array();
        }
        $where['g.name'] = array('like', "%$keyword%");
        $field = "g.id app_id, g.icon game_icon, g.name game_name, g.type";
        $field .= ", g.listorder click_num";
        $game_lists = Db::name('game')
                ->alias('g')
                ->field($field)
                ->where($where)
                ->order('g.listorder desc,g.create_time desc')
                ->select();

        // 获取标签
        foreach ($game_lists as $key=>$val) {
            $tags = $this->gameMulTag($val['type']);
            $game_lists[$key]['game_type'][] = array_pop($tags);
            //$game_lists[$key]['game_type'] = $this->gameMulTag($val['type']);
        }

        return $game_lists;
    }

}