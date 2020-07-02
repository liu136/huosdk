<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-23
 * Time: 16:17
 */
namespace Web\Controller;

use Common\Controller\AdminbaseController;

class SlideController extends AdminbaseController {
    // 添加轮播图
    public function index() {
        redirect(U('Admin/SlideMultiImage/index'));
        exit;
    }

    //列表
    public function picList() {
        $m_slide = M("slide");
        $condition = [
            'type_id' => ['in', [6, 7]]
        ];
        $items = $m_slide->where($condition)->select();
        if (empty($items)) { //项目新部署时，自动加载配置项
            $m_slide_cat = M(slideCat);
            $data = [
                'cat_name'    => '官网图片设置',
                'cat_idname'  => 'webpic',
                'cat_remark'  => '官网图片设置',
                'cat_type_id' => 3
            ];
            if (!$slide_cid = $m_slide_cat->data($data)->add()) {
                $this->error("加载失败，请联系开发商！ErrorCode：0001");
            }
            $data = [
                [
                    'slide_cid'  => $slide_cid,
                    'slide_name' => '头部LOGO',
                    'type_id'    => 6,
                    'recommend_size' => '225*70'
                ],
                [
                    'slide_cid'  => $slide_cid,
                    'slide_name' => '文网文logo',
                    'type_id'    => 6,
                    'recommend_size' => '100*100'
                ],
                [
                    'slide_cid'  => $slide_cid,
                    'slide_name' => '微信公众号二维码',
                    'type_id'    => 6,
                    'recommend_size' => '100*100'
                ],
                [
                    'slide_cid'  => $slide_cid,
                    'slide_name' => '资讯广告图',
                    'type_id'    => 6,
                    'recommend_size' => '300*200'
                ],
                [
                    'slide_cid'  => $slide_cid,
                    'slide_name' => '登陆背景图',
                    'type_id'    => 6,
                    'recommend_size' => '1190*500'
                ],
                [
                    'slide_cid'  => $slide_cid,
                    'slide_name' => '首页背景图',
                    'type_id'    => 6,
                    'recommend_size' => '1920*519'
                ],
                [
                    'slide_cid'  => $slide_cid,
                    'slide_name' => 'app',
                    'type_id'    => 7,
                    'recommend_size' => '48*48'
                ],
                [
                    'slide_cid'  => $slide_cid,
                    'slide_name' => C('PTBNAME').'充值',
                    'type_id'    => 7,
                    'recommend_size' => '48*48'
                ],
                [
                    'slide_cid'  => $slide_cid,
                    'slide_name' => '礼包',
                    'type_id'    => 7,
                    'recommend_size' => '48*48'
                ],
                [
                    'slide_cid'  => $slide_cid,
                    'slide_name' => '苹果app',
                    'type_id'    => 7,
                    'recommend_size' => '48*48'
                ],
            ];
            if (!$m_slide->addAll($data)) {
                $this->error("加载失败，请联系开发商！ErrorCode：0002");
            }
        }

        $items = $m_slide->where($condition)->order('type_id')->select();
        $this->assign("items", $items);
        $status = [
            1 => '隐藏',
            2 => '显示'
        ];
        $this->assign("status", $status);
        $this->display();
    }

    //修改新增界面
    public function edit() {
        $m_slide = M("slide");
        $condition = [
            'slide_id' => I('id')
        ];
        if (I('slide_name')) {
            $condition = [
                'slide_name' => I('slide_name')
            ];
        }
        $item = $m_slide->where($condition)->find();

        switch ($item["slide_name"]) {
            case '首页背景图':
                $pic_size = '1920*519';
                break;
            case '头部logo':
                $pic_size = '357*105';
                break;
            case '文网文logo':
                $pic_size = '428*424';
                break;
            case '微信公众号二维码':
                $pic_size = '209*210';
                break;
            case '登录背景图':
                $pic_size = '1600*600';
                break;
            default:
                $pic_size = '720*360';
                break;
        }

        $item['pic_size'] = $pic_size;
        if($item['type'] == 7){
            $item['pic_size'] = '52*52';
        }

        $this->assign("item", $item);
        $this->display();
    }

    //新增、
    public function logoSetting_do() {
        if (IS_POST) {
            $m_slide = D("Common/Slide");
            $condition = [
                'slide_id' => I('slide_id')
            ];
            $record = $m_slide->where($condition)->find();
//            $cat_record = M('slide_cat')->where(['cat_idname' => 'webpic'])->find();
//            $_POST['slide_cid'] = $cat_record['id'];
            if (empty($record)) {
                if (empty($_POST['slide_id'])) {
                    unset($_POST['slide_id']);
                }
                if ($m_slide->create()) {
                    if ($m_slide->add() !== false) {
                        $this->success("添加成功！", U("slide/logoSetting"));
                    } else {
                        $this->error("添加失败！");
                    }
                } else {
                    $this->error($m_slide->getError());
                }
            } else {
                if ($record['type_id'] == 6) {
                    unset($_POST['slide_name']);
                }
                if ($m_slide->create()) {
                    $_POST['slide_pic'] = sp_asset_relative_url($_POST['slide_pic']);
                    if ($m_slide->save() !== false) {
                        $this->success("保存成功！", U("slide/logoSetting"));
                    } else {
                        $this->error("保存失败！");
                    }
                } else {
                    $this->error($m_slide->getError());
                }
            }
        }
    }

    //首页焦点图
    public function indexrecmd() {
        redirect(U('Admin/SlideMultiImage/index').'?cat_name=indexrecmd');
        exit;
    }

    //PC新游轮播图
    public function gameslide() {
        redirect(U('Admin/SlideMultiImage/index').'?cat_name=gameslide');
        exit;
    }

    //PC公益服轮播图
    public function welfareslide() {
        redirect(U('Admin/SlideMultiImage/index').'?cat_name=welfareslide');
        exit;
    }

    //资讯广告图
    public function news_adv() {
        redirect(U('Web/Slide/edit/slide_name/资讯广告图'));
        exit;
    }
}