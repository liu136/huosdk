<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-25
 * Time: 11:00
 */
namespace Web\Controller;

use Common\Controller\AdminbaseController;

class HotkeyController extends AdminbaseController {
    // 获取热词
    public function index() {
        $data = M('options')->where(array('option_name' => 'hot_search_key'))->getField('option_value');
        $items = json_decode($data, true);
        $this->assign('items', $items);
        $this->display();
    }

    // 添加热词
    public function add() {
        $this->display();
    }

    public function addPost() {
        $val = I('post.');
        $data['option_value'] = json_encode($val);
        $data['option_name'] = 'hot_search_key';
        $info = M('options')->where(array('option_name' => $data['option_name']))->find();
        if (empty($info)) {
            $res = M('options')->data($data)->add();
        } else {
            $data['option_id'] = $info['option_id'];
            $res = M('options')->data($data)->save();
        }
        if ($res) {
            $this->success('添加成功', U('Hotkey/index'));
        }
    }

    // 修改热词
    public function edit() {
        $data = M('options')->where(array('option_name' => 'hot_search_key'))->getField('option_value');
        $items = json_decode($data, true);
        $this->assign('items', $items);
        $this->display();
    }

    // 提交修改
    public function editPost() {
        $val = I('post.');
        $data['option_value'] = json_encode($val);
        $data['option_name'] = 'hot_search_key';
        $info = M('options')->where(array('option_name' => $data['option_name']))->find();
        if (empty($info)) {
            $res = M('options')->data($data)->add();
        } else {
            $data['option_id'] = $info['option_id'];
            $res = M('options')->data($data)->save();
        }
        if ($res) {
            $this->success('修改成功', U('Web/Hotkey/edit'));
        } else {
            $this->success('亲，您还未修改哦', U('Web/Hotkey/edit'));
        }
    }
}