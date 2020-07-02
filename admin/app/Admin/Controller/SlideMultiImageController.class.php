<?php
namespace Admin\Controller;
class SlideMultiImageController extends SlideBaseController {
    function _initialize() {
        parent::_initialize();
        $this->slidecat_type_id = 1;
        $this->cat_name = $_GET['cat_name'];
        $this->assign('catname', $this->cat_name);
    }

    function index() {
        $this->_auto_config();
        $this->_status();
        $this->_game();
        $this->_cates();
        $where = "sc.cat_type_id = $this->slidecat_type_id  ";
        $cid = 0;
        if (isset($_POST['cid']) && $_POST['cid'] != "") {
            $cid = $_POST['cid'];
            $where .= " AND sl.slide_cid=$cid";
        }

        if (!empty($this->cat_name)) {
            $where .= " AND sc.cat_idname='$this->cat_name'";
        }
        $this->assign("slide_cid", $cid);
        $slides = $this->slide_model
            ->alias("sl")
            ->field("sl.*,sc.cat_name")
            ->join("LEFT JOIN ".C("DB_PREFIX")."slide_cat sc ON sc.cid=sl.slide_cid")
            ->where($where)
            ->order("sl.listorder ASC")
            ->select();

        $add_show = 1;
        if (!empty($this->cat_name)) {
            $count = count($slides);
            switch ($this->cat_name)
            {
                case "indexrecmd":
                    if($count >= 6){
                        $add_show = 0;
                    }
                    break;
                case "gameslide":
                    if($count >= 3){
                        $add_show = 0;
                    }
                    break;
                case "welfareslide":
                    if($count >= 3){
                        $add_show = 0;
                    }
                    break;
                default:
                    break;
            }
        }
        $this->formatTargetObject($slides);
        $this->assign('slides', $slides);
        $this->assign('addshow', $add_show);
        $this->display();
    }

    function add() {
        $where = "cat_status!=0 AND sc.cat_type_id = $this->slidecat_type_id  ";

        if (!empty($this->cat_name)) {
            $where .= " AND sc.cat_idname='$this->cat_name'";
        }

        $this->setSelectAreas();
        $this->_game(true, '', 2, '', 2);
        $categorys = $this->slidecat_model
            ->alias("sc")
            ->field("cid,cat_name")
            ->join("LEFT JOIN ".C("DB_PREFIX")."slide sl ON sc.cid=sl.slide_cid")
            ->where($where)
            ->select();
        $this->assign("categorys", $categorys);
        $this->display();
    }

    function add_post() {
        if (IS_POST) {
            if ($this->slide_model->create()) {
                $_POST['slide_pic'] = sp_asset_relative_url($_POST['slide_pic']);
                if ($this->slide_model->add() !== false) {
                    $this->success("添加成功！", U("slide/index",array('cat_name'=>$this->cat_name)));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($this->slide_model->getError());
            }
        }
    }

    function edit() {
        $this->_game(true, '', 2, '', 2);

        $where = "cat_status!=0 AND sc.cat_type_id = $this->slidecat_type_id  ";
        if (!empty($this->cat_name)) {
            $where .= " AND sc.cat_idname='$this->cat_name'";
        }

        $categorys = $this->slidecat_model
            ->alias("sc")
            ->field("cid,cat_name")
            ->join("LEFT JOIN ".C("DB_PREFIX")."slide sl ON sc.cid=sl.slide_cid")
            ->where($where)
            ->limit('0,1')
            ->select();

        $id = intval(I("get.id"));
        $slide = $this->slide_model
            ->alias("sl")
            ->field("sl.*,sc.cat_idname")
            ->where("slide_id=$id")
            ->join("LEFT JOIN ".C("DB_PREFIX")."slide_cat sc ON sc.cid=sl.slide_cid")
            ->find();
        //允许修改标题
        $slide['can_edit_title'] = 1;
        $init_data = ['indexrecmd', 'gameslide', 'welfareslide', 'zxads', 'webpic'];
        if (in_array($slide['cat_idname'], $init_data)) { //以上不允许修改标题
            $slide['can_edit_title'] = 0;
        }
        switch ($slide['slide_name']) {
            case '焦点图1':
                $pic_size = '300*400';
                break;
            case '焦点图2':
                $pic_size = '300*400';
                break;
            case '焦点图3':
                $pic_size = '600*400';
                break;
            case '焦点图4':
                $pic_size = '300*400';
                break;
            case '焦点图5':
                $pic_size = '600*400';
                break;
            case '焦点图6':
                $pic_size = '300*400';
                break;
            case 'pc-新游轮播图':
                $pic_size = '1920*400';
                break;
            case 'pc-公益服轮播图':
                $pic_size = '1920*400';
                break;
            default:
                $pic_size = '720*360';
                break;
        }
        $slide['pic_size'] = $pic_size;
        $this->setSelectAreas($slide['type_id'], $slide['target_id']);
        $this->assign($slide);
        $this->assign("categorys", $categorys);
        $this->display();
    }

    function imagedesc() {
        if (IS_POST) {
            $cid = I("cid");
            $catdesc = $this->slidecat_model
                ->where("cid = $cid")
                ->getField("cat_desc");

            $this->ajaxReturn(array("error" => "0", "msg" => $catdesc));
            exit;
        }
        $this->ajaxReturn(array("error" => "0", "msg" => "(宽高比=2:1 推荐720*360)"));
    }

    function edit_post() {
        if (IS_POST) {
            $_POST['target_id'] = $_POST['app_id'];
            unset($_POST['app_id']);
            if ($this->slide_model->create()) {
                $_POST['slide_pic'] = sp_asset_relative_url($_POST['slide_pic']);
                if ($this->slide_model->save() !== false) {

                    $this->success("保存成功！", U("slide/index"));
                } else {
                    $this->error("保存失败！");
                }
            } else {
                $this->error($this->slide_model->getError());
            }
        }
    }

    //自动加载图片的配置
    public function _auto_config() {
        $init_data = ['indexrecmd', 'gameslide', 'welfareslide', 'zxads', 'webpic'];
        $init_data_name = [
            'indexrecmd'   => '首页焦点图',
            'gameslide'    => 'PC-新游轮播图',
            'welfareslide' => 'PC-公益服轮播图',
            'zxads'        => '资讯广告图',
            'webpic'       => '官网图片设置',
        ];
        $init_data_type = [
            'indexrecmd'   => 1,
            'gameslide'    => 1,
            'welfareslide' => 1,
            'zxads'        => 3,
            'webpic'       => 3,
        ];
        $condition = [
            'cat_idname' => ['in', $init_data]
        ];
        $list = M('slide_cat')->field('cat_idname')->where($condition)->select();
        if (!empty($list)) {
            $data = [];
            foreach ($list as $value) {
                $data[$value['cat_idname']] = 1;
            }
            foreach ($init_data as $key => $value) {
                if (isset($data[$value])) {
                    unset($init_data[$key]);
                }
            }
        }
        if (!empty($init_data) && count($init_data) > 0) {
            foreach ($init_data as $init_value) {
                $add_data = [
                    'cat_name'    => $init_data_name[$init_value],
                    'cat_idname'  => $init_value,
                    'cat_remark'  => $init_value,
                    'cat_status'  => 1,
                    'cat_type_id' => $init_data_type[$init_value],
                ];
                $c_id = M('slide_cat')->add($add_data);
                if ($init_value == 'indexrecmd') { //首页焦点图需要自动加载
                    $c_data = [];
                    for ($i = 1; $i <= 6; $i++) {
                        $c_data[] = [
                            'slide_cid'  => $c_id,
                            'slide_name' => '焦点图'.$i,
                            'slide_des'  => '首页焦点图',
                            'listorder'  => $i,
                            'type_id'    => 2
                        ];
                    }
                    M('slide')->addAll($c_data);
                }
                if ($init_value == 'webpic') {//官网图片设置需要自动加载
                    $c_data = [
                        [
                            'slide_cid'  => $c_id,
                            'slide_name' => '头部logo',
                            'slide_des'  => '头部logo',
                            'type_id'    => 6
                        ],
                        [
                            'slide_cid'  => $c_id,
                            'slide_name' => '文网文logo',
                            'slide_des'  => '文网文logo',
                            'type_id'    => 6
                        ],
                        [
                            'slide_cid'  => $c_id,
                            'slide_name' => '微信公众号二维码',
                            'slide_des'  => '微信公众号二维码',
                            'type_id'    => 6
                        ],
                        [
                            'slide_cid'  => $c_id,
                            'slide_name' => '资讯广告图',
                            'slide_des'  => '资讯广告图',
                            'type_id'    => 6
                        ],
                        [
                            'slide_cid'  => $c_id,
                            'slide_name' => '登陆背景图',
                            'slide_des'  => '登陆背景图',
                            'type_id'    => 6
                        ],
                        [
                            'slide_cid'  => $c_id,
                            'slide_name' => '安卓app',
                            'slide_des'  => '安卓app',
                            'type_id'    => 7
                        ],
                        [
                            'slide_cid'  => $c_id,
                            'slide_name' => '游戏币充值',
                            'slide_des'  => '游戏币充值',
                            'type_id'    => 7
                        ],
                        [
                            'slide_cid'  => $c_id,
                            'slide_name' => '礼包',
                            'slide_des'  => '礼包',
                            'type_id'    => 7
                        ],
                        [
                            'slide_cid'  => $c_id,
                            'slide_name' => '苹果app',
                            'slide_des'  => '苹果app',
                            'type_id'    => 7
                        ],
                    ];
                    M('slide')->addAll($c_data);
                }
            }
        }
    }
}