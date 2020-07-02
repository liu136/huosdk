<?php

namespace Api\Controller;
use Think\Controller;
class CheckemailController extends Controller {
	
	public function emailsafe(){
	    header("Content-Type:text/html; charset=utf-8");
	    $user = I('get.u');
	    $userarr = explode(',',base64_decode($user));
	    if($user){
	        $cid = 2;
	       
	        $time = dm_auth_code($userarr[2], 'DECODE', C("AUTHCODE"));
	        if(time() - $time > 60*60*3){
	            echo '验证邮箱已过有效期,请重新申请';
	            exit;
	        }
	        $username = $userarr[0];
	        $password = $userarr[1];
	        $email = $userarr[3];	 
	        $dbname = "db_sdk_2";
	        $member_model = M($dbname.'.members','c_');
	        $field = "password";
	        $userdata = $member_model->field($field)->where(array('username'=>$username, 'flag'=>0))->find();

	        if($userdata['password'] == $password){
	           $rs =  $member_model->where(array('username'=>$username, 'flag'=>0))->setField('email',$email);
	           if (isset($rs) && $rs>0) {
	               $msg = "邮箱绑定成功.";
	           } elseif (isset($rs) && $rs==0){
	               $msg = "邮箱已绑定.";
	           }else{
	               $msg = "邮箱绑定失败.";
	           }
	        } else {
	            $msg = "验证失败,请重新申请或联系客服.";
	            	
	        }
	    }
        echo $msg;
        exit;
	}
	public function onemailsafe(){
		header("Content-Type:text/html; charset=utf-8");
	    $user = I('get.u');
	    $userarr = explode(',',base64_decode($user));
	    if($user){
	        
	        $time = dm_auth_code($userarr[2], "DECODE", C("AUTHCODE"));
	        if(time() - $time > 60*60*3){
	            echo '验证邮箱已过有效期,请重新申请';
	            exit;
	        }
	        $username = $userarr[0];
	        $password = $userarr[1];
	        $email = $userarr[3];	 
	        $dbname = "db_sdk_2";
	        $member_model = M($dbname.'.members','c_');
	        $field = "password";
	        $userdata = $member_model->field($field)->where(array('username'=>$username, 'flag'=>0))->find();

	        if($userdata['password'] == $password){
				
				
				$time = dm_auth_code(time(),'DECODE', C("AUTHCODE"));	             
	            $user = base64_encode($username.",".$password.",".$time.",".$email.",".$cid);
	            
	            $subject = "官方邮箱验证";
	            $url = 'http://'.$_SERVER['SERVER_NAME'].U('Api/Checkemail/endemailsafe',array('u'=>$user));
	            $message = "<div style='width:680px;padding:0 10px;margin:0 auto;'>
				<div style='line-height:1.5;font-size:14px;margin-bottom:25px;color:#4d4d4d;'>
				<strong style='display:block;margin-bottom:15px;'>亲爱的玩家：".$username." 您好！</strong>
			
				<p>您使用了本站提供的密码邮箱更换功能，如果您确认此密码邮箱更换功能是你启用的，请点击下面的链接,<br>
				该链接在3个小时内有效，请在有效时间内操作：
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
		   	    <p>Copyright 2004-2017 All Right Reserved</p>
		  		</div>";
				
				$dbname=getDbnamebyCid(2);
	            
	            $emailinfo = dm_send_email($dbname, $email, $subject, $message);
				
	            if(1 == $emailinfo['error']) {
	                $msg = "邮箱发送失败,请重新申请或联系客服.".$email.$emailinfo['message'];
	            } else {
	                $msg = "邮箱发送成功,请到新的邮箱中确认绑定邮箱.";
	            }
	
	        } else {
	            $msg = "验证失败,请重新申请或联系客服.";
	            	
	        }
	    }
        echo $msg;
        exit;
	}
	public function endemailsafe(){
		 header("Content-Type:text/html; charset=utf-8");
	    $user = I('get.u');
	    $userarr = explode(',',base64_decode($user));
	    if($user){
	        
	        $time = dm_auth_code($userarr[2], "DECODE", C("AUTHCODE"));
	        if(time() - $time > 60*60*3){
	            echo '验证邮箱已过有效期,请重新申请';
	            exit;
	        }
	        $username = $userarr[0];
	        $password = $userarr[1];
	        $email = $userarr[3];	 
	        $dbname = "db_sdk_2";
	        $member_model = M($dbname.'.members','c_');
	        $field = "password";
	        $userdata = $member_model->field($field)->where(array('username'=>$username, 'flag'=>0))->find();

	        if($userdata['password'] == $password){
	           $rs =  $member_model->where(array('username'=>$username, 'flag'=>0))->setField('email',$email);
	           if (isset($rs) && $rs>0) {
	               M(C('DATA_DB_NAME').'.user','l_')->where(array('username'=>$username, 'cid'=>$cid,'flag'=>0))->setField('email',$email);
	               $msg = "邮箱绑定成功.";
	           } elseif (isset($rs) && $rs==0){
	               $msg = "邮箱已绑定.";
	           }else{
	               $msg = "邮箱绑定失败.";
	           }
	        } else {
	            $msg = "验证失败,请重新申请或联系客服.";
	            	
	        }
	    }
        echo $msg;
        exit;
	}
}