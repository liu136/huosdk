<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------
/**
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class PublicController extends AdminbaseController {
    function _initialize() {
		
    }
    
    // 后台登陆界面
    public function login() {
		
        if (isset($_SESSION['MNG_ID'])) { // 已经登录
            $this->redirect("Index/index");
        } else {
			
            $this->display(":login");
        }
    }
    
    public function logout() {
		$cid  = sp_get_current_cid();
        session('MNG_ID', null);
        redirect(DAMAIQUDAOSITE);
		exit;
    }
    
    public function dologin() {
		
        $name = I("post.username");
        if (empty($name)) {
            $this->error(L('USERNAME_OR_EMAIL_EMPTY'), U('public/login'));
        }
        $pass = I("post.password");
        if (empty($pass)) {
            $this->error(L('PASSWORD_REQUIRED'), U('public/login'));
        }
        $verify = I("post.verify");
        if (empty($verify)) {
            $this->error(L('CAPTCHA_REQUIRED'), U('public/login'));
        }
        // 验证码
        if (!sp_check_verify_code()) {
            $this->error(L('CAPTCHA_NOT_RIGHT'), U('public/login'));
        } else {
			
            $usermodel = M(C('MNG_DB_NAME').'.clientusers', C('LDB_PREFIX'));
            $where['user_login'] = $name;
            $result = $usermodel->where($where)->find();
			
            if ($result) {
				
                if($result['user_pass'] == sp_password($pass)){
                    $crolemodel = M(C('AUTH_DB_NAME').'.clientrole', C('LDB_PREFIX'));					
                    $where = " id=".$result['role_id']." AND (cid = 0 or cid=1)";
                    $role_type = $crolemodel->where($where)->getField("typeid");
                    if ($result["role_id"] != 1 && (empty($role_type) || empty($result['user_status']))) {
                        $this->error(L('USE_DISABLED'),U('public/login'));
                    }
                    
                    $_SESSION['roletype'] = $role_type;
                 
                    // 登入成功页面跳转
                    $_SESSION["MNG_ID"] = $result["id"];
                    $_SESSION['cid'] = 1;                    
                    
                    $result['last_login_ip'] = get_client_ip();
                    $result['last_login_time'] = date("Y-m-d H:i:s");
                    $usermodel->save($result);
                    setcookie("clientusername", $name, time() + 30 * 24 * 3600, "/");
                    $this->redirect("index/index");
                } else {
                    $this->error(L('PASSWORD_NOT_RIGHT'), U('public/login'));
                }
            } else {
                $this->error(L('USERNAME_NOT_EXIST'), U('public/login'));
            }
        }
    }
	
	
	/**
	**注册
	**/
    public function reg() {
		$key = I("get.key");
		
		$str= base64_decode($key);
		
		$str_array = explode("_",$str);
		$agent = $str_array[1];
		$appid = $str_array[0];
		
		if($agent < 0 ){
			$agent = 0;
		}
		
		if($appid < 0 ){
			$appid = 1;
		}
		$gamemodel = M(C('MNG_DB_NAME').'.game', C('LDB_PREFIX'));
		$game = $gamemodel
		        ->where(array("id"=>$appid))
				->field('gamename,iosurl')
				->find();
		
		$this->assign("game", $game);
		$this->assign("appid", $appid);
		$this->assign("agent", $agent);
        $this->display(":reg");
    }
	
	public function doreg(){

		$usermodel = M(C('MNG_DB_NAME').".members", C('CDB_PREFIX'));
		
		$userdata['username'] = I("post.myUsername");
		$agent = I("post.agent");
		$appid = I("post.appid");
		
		$pwd = I("post.myPwd");

		if(empty($userdata['username']) || empty($pwd)){
			echo "FAIL4";
			exit;
		}
		
		$pattern = "/^[a-z0-9]+$/i";
        if (!preg_match($pattern, $userdata['username']) || (strlen($userdata['username']) > 16) || (strlen($userdata['username']) < 6)) {
		   echo "FAIL0";
		   exit;
		}else if (!preg_match($pattern, $pwd) || (strlen($pwd) > 16) || (strlen($pwd) < 6)) {
		   echo "FAIL0";
		   exit;
		}
        // 验证码
        if (!sp_check_verify_code()) {
            echo "FAIL5";
			exit;
        }
		
		$user = $usermodel->where(array('username'=>$userdata['username']))->find();
		if($user){
			echo "FAIL2";
			exit;
		}
        
		$userdata['password'] =  sp_auth_code($pwd,C("AUTHCODE"));
		$userdata['reg_time'] = time();
		$userdata['device'] = 4;
		$userdata['nickname'] = $userdata['username'];
		$userdata['cid'] = 2;
		$userdata['lm_username'] = $userdata['username'];
		$userdata['agentid'] = $agent;
		$userdata['ip'] = get_client_ip();
		$userdata['last_login_time'] = time();
		$userdata['appid'] = $appid;

		if($usermodel->create()){
			$uid = $usermodel->add($userdata);
			
		    echo "SUCCESS";
			exit;
		}else{
			echo "FAIL5";
			exit;
		   
		}
	}
   
    
}