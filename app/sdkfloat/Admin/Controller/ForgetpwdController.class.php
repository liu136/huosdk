<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ForgetpwdController extends AdminbaseController{
	protected $member_model;
	
	function _initialize() {
		$client_ip = "";
        if (getenv('HTTP_CLIENT_IP')) {
           $client_ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
           $client_ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR')) {
           $client_ip = getenv('REMOTE_ADDR');
        } else {
           $client_ip = $_SERVER['REMOTE_ADDR'];
        }

		
		parent::_initialize();
		$this->member_model = M($this->dbname.'.members','c_');
	}
	
	//首页
	public function index(){
		
		$this->display();
	}
	
    public function forgetpwd(){
	  if(IS_POST){
         $action = I("action");
		 $username = I("username");

         if("findpwd" == $action){
             $user = $this->member_model->where(array("username"=>$username))->field("username,email,mobile")->find();

			 if(!empty($user['email'])){
                 $this -> assign('user',$user);
				 $this -> display();
				 exit;
			 }
		 }

	  }
	  $this -> assign('msg','该账户未绑定邮箱,请联系客服处理');   
	  $this -> display('forgetresult');
	}
   

	//找回密码邮箱提交函数
	public function forgetpwd_post(){
	
	  if(IS_POST){
	    $postemail = I('post.email');
	    $username = I('post.username');
	    
	    $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
	    if (!preg_match($pattern, $postemail) ){
	        $this->redirect('-1');
	    }
	    
	    //$cid = dm_get_current_cid();
	    $action = I('post.action');
	    
	    if(isset($action) && $action == "findpwd"){
			
	        //$clientkey = M(C('MNG_DB_NAME').'.client','l_')->where(array('id'=>$cid))->getField('clientkey');
			$newpwd = substr(md5(time()), 0, 6);

	        $unewpwd = dm_auth_code($newpwd,"ENCODE",C("AUTHCODE"));            
	        $user = base64_encode($newpwd);
	            
	        $subject = "密码找回";
	        $url = 'http://'.$_SERVER['SERVER_NAME'].U('Api/Checkemail/emailsafe',array('u'=>$user));
	        $message = "<div style='width:680px;padding:0 10px;margin:0 auto;'>
			    <div style='line-height:1.5;font-size:14px;margin-bottom:25px;color:#4d4d4d;'>
			    <strong style='display:block;margin-bottom:15px;'>亲爱的会员：".$username." 您好！</strong>
			    <p>您使用了本站提供的密码找回功能，密码为:<br></p>
			    </div>
			    <div style='margin-bottom:30px;'><strong style='display:block;margin-bottom:20px;font-size:14px;color:#f60;'>
		        {$newpwd}</strong><br/>
			
			    <div style='padding:10px 10px 0;border-top:1px solid #ccc;color:#999;margin-bottom:20px;line-height:1.3em;font-size:12px;'>
			    <p style='margin-bottom:15px;'>此为系统邮件，请勿回复<br/>
		     	   请保管好您的密码，避免游戏账户被他人盗用</p>
		   	    <p>Copyright 2004-2017 All Right Reserved</p>
			   </div>";  
	            
	            
	        $emailinfo = dm_send_email($this->dbname, $postemail, $subject, $message);
	        if(1 == $emailinfo['error']) {
				  $this->assign("邮件发送失败 <p>邮件错误信息:".$emailinfo['message']);
	        } else {
				 $user = $this->member_model->where(array("username"=>$username))->field("username,password,email")->find();

		         $oldpwd = $user['password'];
				 $data['update_time'] = time();
				 $data['password'] = $unewpwd;
                
				 $rs =  $this->member_model->where(array("username"=>$username,"password"=>$oldpwd))->save($data);

			     if($rs){
				      $this->assign('msg',"密码已发送至邮箱{$user['email']},请登陆邮箱查看密码.");
			     }
		    }
	        
	       $this -> display('forgetresult');
	    }
	       
	  }
	}


	public function ajaxForget(){
	  if(IS_POST){
         $type = I("type");
		 $username = I("username");
         
         if("findpwd" == $type){
             //不能为空
		    if (empty($username)) {
			    echo json_encode(array('success'=>false,'msg'=>'账号不能为空.'));
			    exit;
		    }

		    $user = $this->member_model->where(array("username"=>$username))->field("username,email,mobile")->find();
             
		    if(!empty($user)){
			   echo json_encode(array('success'=>true));
			   exit;
		    }else{
			   echo json_encode(array('success'=>false,'msg'=>'账号不存在,请重新输入！'));
			   exit;
		    }

		 }
		
		
	   }
	
	}
	
	
}