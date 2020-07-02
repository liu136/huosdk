<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class UserController extends AdminbaseController{
	protected $member_model;
	
	function _initialize() {
		parent::_initialize();
		$this->member_model = M($this->dbname.'.members','c_');
	}
	
	//浮点用户信息首页
	public function index(){
		
	    $username = dm_get_current_user();
	    $field = "email, mobile, username, nickname, score, level";
        $userdata = $this->member_model->field($field)->where(array('username'=>$username, 'flag'=>0))->find();
        
        if(empty($userdata['email']) && empty($userdata['mobile'])){
            $strshow = "未设置";
        }else{
            $strshow = "已设置";
        }

        $this->assign("users", $userdata);
		$this->assign('strshow',$strshow);
		$this->display();
	}
	
	//实名验证
	public function realname(){
		
		$this->display();
	}
	
	public function uppwd(){
	    $this->display();
	}
	
	/*
	 * 修改密码处理函数
	 */
	public function uppwd_post(){
	    $oldpwd = I('post.oldpwd');
	    $newpwd = I('post.newpwd');
	    $unewpwd = I('post.unewpwd');
	    $action = I('post.action');
	    
	    $state = 0;
	    if ($unewpwd != $newpwd) {
	        $this->assign('state', $state);
	        $this->assign('msg', '两次输入密码不一致!');
	        $this->display('pwdresult');
	        exit;
	    }

	    if(isset($action) && $action == 'updatepwd'){
	        $cid = dm_get_current_cid();
			
	        //$clientkey = M(C('MNG_DB_NAME').'.client','l_')->where(array('id'=>$cid))->getField('clientkey');
			
	        $oldpwd = dm_auth_code($oldpwd, 'ENCODE', C("AUTHCODE"));
	        
	        $username = dm_get_current_user();
	        $userpwd = $this->member_model->where(array('username'=>$username))->getField('password');

	        $msg = "修改密码失败!";
	        if ($oldpwd == $userpwd) {
	            $data['password'] = dm_auth_code($newpwd, 'ENCODE', C("AUTHCODE"));
	            $data['update_time'] = time();

	            $rs =  $this->member_model->where(array('username'=>$username))->setField($data);
	            if (isset($rs) && $rs>0) {
	                $state = 1;
	                $msg = "修改密码成功!";
	            }
	        }
	        
	        $this->assign('state', $state);
	        $this->assign('msg', $msg);
	        $this->display('pwdresult');
	    }
	}
	
	/*
	 * 验证原密码
	 */
	public function ajaxPwd(){
	    $oldpwd = I('post.oldpwd');
	    $newpwd = I('post.newpwd');
	    $unewpwd = I('post.unewpwd');
	    $type = I('post.type');
	
	    $data['status'] = 0;
	    $data['msg'] = '';
	     
	    //不能为空
	    
	    if (empty($oldpwd)) {
	        $data['msg']  = '原密码为空';
	        $this->ajaxReturn($data);
	    }
	    if (empty($newpwd)) {
	        $data['msg']  = '新密码为空';
	        $this->ajaxReturn($data);
	    }
	    
	    if (empty($type)) {
	        $data['msg']  = '网络错误';
	        $this->ajaxReturn($data);
	    }
	    
	    if ($unewpwd != $newpwd) {
	        $data['msg']  = '两次输入密码不一致!';
	        $this->ajaxReturn($data);
	    }
	    
	    //$cid = dm_get_current_cid();	     
	    //$clientkey = M(C('MNG_DB_NAME').'.client','l_')->where(array('id'=>$cid))->getField('clientkey');
	    $oldpwd = dm_auth_code($oldpwd, 'ENCODE', C("AUTHCODE"));
		
	    $username = dm_get_current_user();
	    $userpwd = $this->member_model->where(array('username'=>$username))->getField('password');
	
	    if ($oldpwd != $userpwd) {
	        $data['msg']  = '原密码错误!';
	        $this->ajaxReturn($data);
	    }
	     
	    $data['status'] = 1;
	    $this->ajaxReturn($data);
	}
}