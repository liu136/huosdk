<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class BindingController extends AdminbaseController{
	protected $member_model;
	
	function _initialize() {
		parent::_initialize();
		$this->member_model = M($this->dbname.'.members',C('CDB_PREFIX'));
	}
	
	//用户邮箱绑定界面
	public function index(){
	    $username = dm_get_current_user();
	    $email = $this->member_model->where(array('username'=>$username))->getField('email');
	    
	    $update = I('post.upemail');
	    //已绑定邮箱
	    if(!empty($email) && $update !="update"){
	        $this->assign('username',$username);
	        $this->assign('email',$email);
	        //进入已绑定邮箱界面
	        $this->display('emailbinding');
	        exit;
	    }
	    
	    //未绑定邮箱
	    $this->display('noemail');
	}
	
	/*
	 * 用户更换邮箱流程
	 * 1、 验证原邮箱
	 * 2、绑定现邮箱
	 * 3、绑定成功
	 */
	public function updatemail(){
	    $update = I('post.upemail');
		
	    if ($update == "one" ){
		
	        $this->display('emailcheckold_f');
	        exit;
	    } elseif ($update == "second" ){
	        $this->display('emailupdate_s');
	        exit;
	    }else{
	        $this->display('emailsuccess_t');
	        exit;
	    }
	     
	    //跳回用户中心
	    $this->redirect(U('User/index'));
	}
	
	
	
	
	//绑定邮箱提交函数
	public function email_one_post(){
	    $postemail = I('post.email');
	    $postpwd = I('post.pwd');
	    
	    $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
	    if (!preg_match($pattern, $postemail) || empty($postpwd)){
	        $this->redirect('-1');
	    }
	    
	    $username = dm_get_current_user();
	    $cid = dm_get_current_cid();
	    $action = I('post.action');
	    
	    if(isset($action) && $action == "safeemail"){
	        //$clientkey = M(C('MNG_DB_NAME').'.client','l_')->where(array('id'=>$cid))->getField('clientkey');
	        $postpwd = dm_auth_code($postpwd,"ENCODE",C("AUTHCODE"));
	        $userpwd = $this->member_model->where(array('username'=>$username))->getField('password'); 
	        if($userpwd == $postpwd){
	            $time = dm_auth_code(time(),"ENCODE",C("AUTHCODE"));	             
	            $user = base64_encode($username.",".$userpwd.",".$time.",".$postemail.",".$cid);
	            
	            $subject = "官方邮箱验证";
	            $url = 'http://'.$_SERVER['SERVER_NAME'].U('Api/Checkemail/onemailsafe',array('u'=>$user));
	            $message = "<div style='width:680px;padding:0 10px;margin:0 auto;'>
				<div style='line-height:1.5;font-size:14px;margin-bottom:25px;color:#4d4d4d;'>
				<strong style='display:block;margin-bottom:15px;'>亲爱的玩家：".$username." 您好！</strong>
			
				<p>您使用了本站提供的密码邮箱更换功能，如果您确认此密码邮箱更换功能是你启用的，请点击下面的链接,<br>
				该链接在3个小时内有效，请在有效时间内操作：我们将发送邮件到您更换的邮箱中进行验证！
				</p>
				</div>
				<div style='margin-bottom:30px;'><strong style='display:block;margin-bottom:20px;font-size:14px;'>
				<a target='_blank' style='color:#f60;' href='".$url."'>确认绑定邮箱</a></strong>
			    <p style='color:#666;'><small style='display:block;font-size:12px;margin-bottom:5px;'>
				如果上述文字点击无效，请把下面网页地址复制到浏览器地址栏中打开：
				</small><span style='color:#666;'>".$url."</span></p></div></div>
				<div style='padding:10px 10px 0;border-top:1px solid #ccc;color:#999;margin-bottom:20px;line-height:1.3em;font-size:12px;'>
		    	<p style='margin-bottom:15px;'>此为系统邮件，请勿回复<br  />
		     	请保管好您的邮箱，避免游戏账户被他人盗用</p>
		   	    <p>Copyright 2004-2015 All Right Reserved</p>
		  		</div>";
	            $email = $this->member_model->where(array('username'=>$username))->getField('email');
	            
	            $emailinfo = dm_send_email($this->dbname, $email, $subject, $message);
	            if(1 == $emailinfo['error']) {
	                $this->error("邮件发送失败 <p>邮件错误信息:".$emailinfo['message']);
	            } else {
	                $this->assign('msg',"邮件发送成功,请登陆原绑定邮箱".$email."中查看邮件.");
	                $this->display('emailupdate_s');
	                exit();
	            }
	        } else {
	            $this->assign('msg',"更换邮箱失败,密码输入错误.");
	            $this->display('index');
	            exit();
	        }
	        $this->display('index');
	    }
	}
	//绑定邮箱提交函数
	public function usermail_post(){
	    $postemail = I('post.email');
	    $postpwd = I('post.pwd');
	    
	    $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
	    if (!preg_match($pattern, $postemail) || empty($postpwd)){
	        $this->redirect('-1');
	    }
	    
	    $username = dm_get_current_user();
	    $cid = dm_get_current_cid();
	    $action = I('post.action');
	    
	    if(isset($action) && $action == "safeemail"){
	        //$clientkey = M(C('MNG_DB_NAME').'.client','l_')->where(array('id'=>$cid))->getField('clientkey');
	        $postpwd = dm_auth_code($postpwd,"ENCODE",C("AUTHCODE"));
	        $userpwd = $this->member_model->where(array('username'=>$username))->getField('password'); 
	        if($userpwd == $postpwd){
	            $time = dm_auth_code(time(),"ENCODE",C("AUTHCODE"));	             
	            $user = base64_encode($username.",".$userpwd.",".$time.",".$postemail.",".$cid);
	            
	            $subject = "官方邮箱验证";
	            $url = 'http://'.$_SERVER['SERVER_NAME'].U('Api/Checkemail/emailsafe',array('u'=>$user));
	            $message = "<div style='width:680px;padding:0 10px;margin:0 auto;'>
				<div style='line-height:1.5;font-size:14px;margin-bottom:25px;color:#4d4d4d;'>
				<strong style='display:block;margin-bottom:15px;'>亲爱的玩家：".$username." 您好！</strong>
				<p>您使用了本站提供的密码邮箱绑定功能，如果您确认此密码邮箱绑定功能是你启用的，请点击下面的链接,<br>
				该链接在3个小时内有效，请在有效时间内操作：
				</p>
				</div>
				<div style='margin-bottom:30px;'><strong style='display:block;margin-bottom:20px;font-size:14px;'>
				<a target='_blank' style='color:#f60;' href='".$url."'>确认发送邮件,更换邮箱</a></strong>
			    <p style='color:#666;'><small style='display:block;font-size:12px;margin-bottom:5px;'>
				如果上述文字点击无效，请把下面网页地址复制到浏览器地址栏中打开：
				</small><span style='color:#666;'>".$url."</span></p></div></div>
				<div style='padding:10px 10px 0;border-top:1px solid #ccc;color:#999;margin-bottom:20px;line-height:1.3em;font-size:12px;'>
		    	<p style='margin-bottom:15px;'>此为系统邮件，请勿回复<br  />
		     	请保管好您的邮箱，避免游戏账户被他人盗用</p>
		   	    <p>Copyright 2004-2015 All Right Reserved</p>
		  		</div>";
	            
				
	            
	            $emailinfo = dm_send_email($this->dbname, $postemail, $subject, $message);
	            if(1 == $emailinfo['error']) {
	                $this->error("邮件发送失败 <p>邮件错误信息:".$emailinfo['message']);
	            } else {
	                $this->assign('msg',"邮件发送成功,请登陆邮箱查看.");
	                $this->display('emailupdate_s');
	                exit();
	            }
	        } else {
	            $this->assign('msg',"绑定失败,密码输入错误.");
	            $this->display('index');
	            exit();
	        }
	        $this->display('index');
	    }
	}
	
	/*
	 * 一个邮箱能绑定多个账号
	 * 
	 */
	public function ajaxEmail(){
	    $email = I('post.email');
        $pwd = I('post.pwd');
        $oneamil=I('post.onemail');
	    $data['status'] = 0;
	    $data['msg'] = '';
	    
	    //不能为空
	    if (empty($email)) {
	        $data['msg']  = '邮箱不能为空.';
	        $this->ajaxReturn($data);
	    }
	    
	    //不能为空
	    if (empty($pwd)) {
	        $data['msg']  = '密码不能为空.';
	        $this->ajaxReturn($data);
	    }
	    $cid = dm_get_current_cid();
	    
	    //$clientkey = M(C('MNG_DB_NAME').'.client','l_')->where(array('id'=>$cid))->getField('clientkey');
	    $pwd = dm_auth_code($pwd, 'ENCODE', C("AUTHCODE"));

	    $username = dm_get_current_user();
	    $field = "password,email";
	    $userdata = $this->member_model->field($field)->where(array('username'=>$username))->find();

	    if ($pwd != $userdata['password']) {
	        $data['msg']  = '密码错误，请重新填写.';
	        $this->ajaxReturn($data);
	    }
	    
	    if(!empty($userdata['email']) && empty($oneamil)){
			
	        $data['msg']  = '该用户已绑定邮箱.';
	        $this->ajaxReturn($data);
	    }
	    
	    $data['status'] = 1;
	    $this->ajaxReturn($data);
	}
	
}