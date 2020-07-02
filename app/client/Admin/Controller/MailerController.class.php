<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.93damai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------
/**
 * 邮箱配置
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class MailerController extends AdminbaseController {
    
	protected $email_model;

    function _initialize() {
        parent::_initialize();
		$this->email_model = M($this->dbname.'.email', C('CDB_PREFIX'));
    }

	//SMTP配置
    public function index() {
		$emails = $this->email_model->where(array("id"=>1))->find();

		$this->assign("emails", $emails);
    	$this->display();
    }
    
    //SMTP配置处理
    public function index_post() {
    	$_POST = array_map('trim', I('post.'));
    	if(in_array('', $_POST)) $this->error("不能留空！");
    	$data['adress'] = $_POST['address'];
    	$data['sender'] = $_POST['sender'];
    	$data['smtp'] = $_POST['smtp'];
    	$data['smtp_port'] = $_POST['smtp_port'];
    	$data['create_time'] = time();
    	$data['password'] = $_POST['password'];

		$email = $this->email_model->where(array("id"=>1))->find();
		if(empty($email)){
           $rst = $this->email_model->add($data);
		}else{
            $rst = $this->email_model->where(array("id"=>1))->save($data);
		}
    	
    	if ($rst) {
    		$this->success("保存成功！");
    	} else {
    		$this->error("保存失败！");
    	}
    }
    
}

