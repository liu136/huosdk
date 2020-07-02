<?php
/**
 * 客服联系信息管理中心
 *
 * @author
 */
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class ContactController extends AdminbaseController {
    /**
     * 联系方式页面
     */
    public function index() {
        $contactmodel = M('game_contact');
        $list = $contactmodel->where("app_id = %d ", 0)->find();
        $this->assign("contact", $list);
        $this->display();
    }

    /**
     * 修改联系信息
     */
    public function editContact() {
        if (I('action') == 'contact') {
            $data['app_id'] = 0;
            $data['qq'] = I('qq');
            $data['tel'] = I('tel');
            $data['email'] = I('email');
            $data['qqgroup'] = I('qqgroup');
            $data['qqgroupkey'] = I('qqgroupkey');
            $data['service_time'] = I('sertime/s', '');
            $data['wx'] = I('wx', '');
            $data['content'] = I('content', '');
            /* if(empty($data['qq']) || empty($data['qqgroup']) || empty($data['tel']) || empty($data['email'])){
                echo json_encode(array('msg'=>'参数缺失.'));
                exit;
            } */
            $contactmodel = M('game_contact');
            $list = $contactmodel->where("app_id = %d ", $data['app_id'])->select();
            if (count($list) > 0) {
                $rs = $contactmodel->save($data);
            } else {
                $rs = $contactmodel->add($data);
            }
            if ($rs !== false) {
                $this->assign("contact", $data);
                $this->success("更新成功.");
            } else {
                $this->error("更新联系方式失败.");
            }
            exit;
        }
    }

    //H5客服列表,跟据appid进行分类
    public function custom_server_list() {
        $contactmodel = M('game_contact');
        $list = $contactmodel->where("app_id != %d ", 0)->select();
        $this->assign("contact", $list);
        $this->display();
    }
    public function custom_server_list_edit($id) {
        $contactmodel = M('game_contact');
        $list = $contactmodel->where("app_id = %d ", $id)->select();
        $this->assign("contact", $list);
        $this->display();
    }
    public function custom_server_list_edit_submit() {
        if (I('action') == 'contact') {
            $data['app_id'] = I('app_id');
            $data['qq'] = I('qq');
            $data['tel'] = I('tel');
            $data['email'] = I('email');
            $data['qqgroup'] = I('qqgroup');
            $data['qqgroupkey'] = I('qqgroupkey');
            $data['service_time'] = I('sertime/s', '');
            $data['wx'] = I('wx', '');
            $data['content'] = I('content', '');
            /* if(empty($data['qq']) || empty($data['qqgroup']) || empty($data['tel']) || empty($data['email'])){
                echo json_encode(array('msg'=>'参数缺失.'));
                exit;
            } */
            $contactmodel = M('game_contact');
            $list = $contactmodel->where("app_id = %d ", $data['app_id'])->select();
            if (count($list) > 0) {
                $rs = $contactmodel->save($data);
            } else {
                $rs = $contactmodel->add($data);
            }
            if ($rs !== false) {
                $this->assign("contact", $data);
                $this->success("更新成功.");
            } else {
                $this->error("更新联系方式失败.");
            }
            exit;
        }
    }


}

?>