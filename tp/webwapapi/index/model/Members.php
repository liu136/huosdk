<?php 
namespace app\index\model;

use think\image\Exception;
use think\Model;
use think\Db;
use think\Session;

class Members extends Model
{
    public function initialize() {

        parent::initialize();
    }

    // 获取玩家的信息
    public function getMemberInfo($id=1) {
        $id = Session::get('user.id');
        if (empty($id)) {
            return array('error_code'=>1, 'data'=>array());
        } else {
            //$member = Members::get($id);
            $field = "id, username, nickname, sex,ifnull(email,'') as email, qq, ifnull(mobile,'') as mobile";
            $field .= ", address, portrait head_img";
            $member = Db::name('members')
                ->alias('m')
                ->field($field)
                ->join('__MEM_INFO__ i', 'i.mem_id = m.id', 'LEFT')
                ->where('id', $id)
                ->find();

            return array('error_code'=>0, 'data'=>$member);
        }
    }

    // 玩家登录
    public function login() {
        $username = request()->param('username');
        $password = request()->param('password');

        $_is_mobile = isMobileNumber($username);

        if($_is_mobile){
            $userinfo = $this->loginMobile(array("username"=>$username,"password"=>$password));
        }else{
            $userinfo = $this->checkPwd($username, $password);
        }


        $loginRes = $this->checkUsernameStatus($userinfo);

        if (0 == $loginRes['error_code']) {
            Session::set('user', $userinfo);
            // 记录登陆记录
            $this->loginLog($userinfo);
            return $loginRes;
        } else {
            return $loginRes;
            //return array('error_code'=>'1', 'msg'=>'登录失败');
        }

    }

    public function loginMobile($data = []) {

        $_map['bindmobile'] = $data['username'];
        $_map['password'] = member_password($data['password']);

        $_mem_info = Db::name('members')->where($_map)->find();;

        return $_mem_info;
    }

    // 判断玩家的账户和密码是否正确
    public function checkPwd($username, $password) {
        $where['username'] = $username;
        $where['password'] = member_password($password);
        $userinfo = Db::name('members')
                      ->where($where)
                      ->find();

        return $userinfo;
    }

    // 判断玩家的账户状态
    public function checkUsernameStatus($userinfo) {
        if (empty($userinfo)) {
            return array(
                'error_code'=>"1",
                'msg'=>'玩家的账户或密码不正确'
            );
        } else if ("3" == $userinfo['status']) {
            return array('error_code'=>"2", 'msg'=>'玩家的账户被冻结');
        } else if ("2" == $userinfo['status']) {
            return array('error_code'=>"0", 'msg'=>'登陆成功');
        }
    }

    // 记录登陆记录
    public function loginLog($userinfo) {
        $data['mem_id'] = $userinfo['id'];
        $data['app_id'] = 0;
        $data['agentgame'] = 'default';
        $data['login_time'] = time();
        $data['from'] = 4; //1为安卓，2为H5，3为苹果，4 WEB
        $data['reg_time'] = $userinfo['reg_time'];
        $data['flag'] = 0;
        $data['login_ip'] = request()->ip();
        $res = Db::name('loginLog')->insertGetId($data);

        return $res;
    }

    // 玩家注册
    public function register() {

        if (empty(Session::get('sms_time'))) {
            Session::set('sms_time',time());
        }

        $username = request()->param('username');


        // 判断用户名是否存在
        $userExists = $this->usernameIsExists($username);
        if ($userExists) {
            return array('error_code'=>8, 'msg'=>'用户名已经存在');
        }

        /* BEGIN 2017-06-22 添加手机号登陆 已注册的手机号 不能再次注册 */
        $_is_mobile = isMobileNumber($username);
        if($_is_mobile){
            $_check_mobile_map['bindmobile'] = $username;
            $_cnt = Db::name('members')->where($_check_mobile_map)->count();
            if ($_cnt > 0){
                return array('error_code'=>8, 'msg'=>'用户名已经存在');
            }
        }

        $password = request()->param('password');
        $mobileCode = request()->param('mobile_code');
        $realName = request()->param('real_name');
        $identityCode = request()->param('identity_code');

        // 验证手机短信验证码
        $checkRes = $this->checkMobileCode($mobileCode);
        if ($checkRes) {
            return $checkRes;
        }
        $regRes = $this->regAction($username, $password, $realName, $identityCode);

        if ($regRes) {
            Session::set('user', $regRes);
            return array('error_code'=>0, 'msg'=>'注册成功');
        }

        return array('error_code'=>7, 'msg'=>'注册失败');
    }

    // 验证手机验证码是否正确
    public function checkMobileCode($mobileCode) {
        // 判断验证码是否在有效期内
        if ((Session::get('sms_time')+1200) < time()) {
            return array('error_code'=>'1', 'msg'=>'验证码已经过期');
        } else if (Session::get('sms_code') != $mobileCode) {
            return array('error_code'=>'2', 'msg'=>'验证码不正确');
        }
    }

    // 注册玩家账户的方法
    protected function regAction($username, $password, $truename, $idcard) {
        if (empty($username)) {
            return array('error_code'=>'3', 'msg'=>'用户名不能为空');
        }
        if (empty($password)) {
            return array('error_code'=>'4', 'msg'=>'用户密码为空');
        }
        if (empty($truename)) {
            return array('error_code'=>'5', 'msg'=>'真实姓名不能为空');
        }
        if (empty($idcard)) {
            return array('error_code'=>'6', 'msg'=>'身份证不能为空');
        }
        $data['username'] = $username;
        $data['nickname'] = $username;
        $data['password'] = member_password($password);
        $data['pay_pwd'] = member_password($password);
        $data['from'] = 1;// android
        $data['reg_time'] = time();
        $data['regist_ip'] = request()->ip();
        $data['truename'] = $truename;
        $data['idcard'] = $idcard;

        $data = array_merge($data, $this->checkUsername($username));
        $data['id'] = Db::name('members')->insertGetId($data);

        return $data;
    }

    // 判断玩家是手机号还是邮箱
    public function checkUsername($username) {
        if (isEmail($username)) {
            $data['email'] = $username;
            $data['bindemail'] = $username;
        } else if (isMobileNumber($username)){
            $data['mobile'] = $username;
            $data['bindmobile'] = $username;
        } else {
            $data = array();
        }

        return $data;
    }

    // 退出登录
    public function logout() {
        Session::clear(null);
        return array('error_code'=>0, 'msg'=>'退出成功');
    }

    // 判断玩家是否正常的登录
    public function loginStatus() {
        if (empty(Session::get('user.id'))) {
            return array('error'=>1, 'msg'=>'玩家未登陆');
        } else {
            return array('error'=>0, 'msg'=>'玩家已登陆');
        }
    }

    // 玩家领取礼包
    public function getGiftCode() {
        $mem_id = Session::get('user.id');

        $members = new Members();
        $loginInfo = $members->loginStatus();
        if (0 == $loginInfo['error']) {
            $gift_id = request()->param('gift_id');
			
			// 判断玩家是否已经领取过礼包
			$map['gf_id'] = $gift_id;
            $map['mem_id'] = $mem_id;
			$isGetGift = Db::name('gift_code')->where($map)->find();
			if (!empty($isGetGift)) {
				return array('error'=>4, 'msg'=>'您已经领取过礼包');
			}
			
            $where['gf_id'] = $gift_id;
            $where['mem_id'] = 0;
            $giftInfo = Db::name('gift_code')
                          ->where($where)
                          ->find();

            if (!empty($giftInfo)) {
                $giftInfo['mem_id'] = $mem_id;
                $giftInfo['update_time'] = time();

                $res = Db::name('gift_code')->update($giftInfo);

                if ($res) {
                    // 领取成功之后减少礼包余量
                    Db::name('gift')->where('id', $gift_id)->setDec('remain');
                    Db::name('gift')->where('id', $gift_id)
                        ->setField('update_time', time());
                    return array('error'=>0, 'msg'=>'领取成功');
                } else {
                    return array('error'=>1, 'msg'=>'领取失败');
                }
            } else {
                return array('error'=>2, 'msg'=>'礼包不足');
            }
        } else {
            return $loginInfo;
        }
    }

    // 判断玩家是否存在
    public function usernameIsExists($username) {

        $userinfo = Db::name('members')
            ->where('username', $username)
            ->find();

        if (!empty($userinfo)) {
            return array('error_code'=>8, 'msg'=>'用户名已经存在');
        }
    }

    // 更新密码
    public function updatePwd() {
        $username = Session::get('user.username');
        if (empty($username)) {
            return array('error'=>1, 'msg'=>'玩家未登录');
        }
        $oldPwd = request()->param('oldpwd');
        $newPwd = request()->param('newpwd');
        // 判断玩家的密码是否正确
        $res = Db::name('members')
                 ->where('username', $username)
                 ->where('password', member_password($oldPwd))
                 ->find();

        if (empty($res)) {
            return array('error'=>1, 'msg'=>'密码不正确');
        } else {
            $res['password'] = member_password($newPwd);
            $res['update_time'] = time();
            $update_res = Db::name('members')->update($res);

            if ($update_res) {
                return array('error'=>0, 'msg'=>'密码更新成功');
            } else {
                return array('error'=>2, 'msg'=>'密码更新失败');
            }
        }
    }

    // 验证手机号码是否正确
    public function verifyMobileNumber() {
        $mobile = Session::get('user.mobile');

        if (empty($mobile)) {
            return array('status'=>11, 'msg'=>'玩家未绑定手机号码');
        }
        $msg = sendMsg($mobile);

        return $msg;
    }

    // 绑定新手机号
    public function bindMobile() {
        $mobile = request()->param('mobile');
        $code = request()->param('code');
        if (Session::get('sms_code')==$code) {
            $id = Session::get('user.id');
            $data['id'] = $id;
            $data['mobile'] = $mobile;
            $data['bindmobile'] = $mobile;
            $data['update_time'] = time();
            $res = Db::name('members')->update($data);

            if ($res) {
                return array('status'=>1, 'msg'=>'绑定成功');
            } else {
                return array('status'=>2, 'msg'=>'绑定失败');
            }
        } else {
            return array('status'=>3, 'msg'=>'验证码不正确');
        }
    }

    // 更新玩家的信息
    public function updateUserinfo() {
        try {
            $id = Session::get('user.id');
            if (empty($id)) {
                return array('error'=>1, 'msg'=>'玩家未登录');
            }
            $data['id'] = $id;
            $data['nickname'] = request()->param('nickname');
            $data['update_time'] = time();
            $res1 = Db::name('members')->update($data);

            $ext_data['mem_id'] = $id;
            $ext_data['address'] = request()->param('address');
            $ext_data['update_time'] = time();
            $ext_data['qq'] = request()->param('qq');
            $ext_data['sex'] = request()->param('sex');

            if(empty($ext_data['sex']) || $ext_data['sex'] == "null"){
                $ext_data['sex'] = 3;
            }
            $mem_info = Db::name('mem_info')
                ->where('mem_id', $id)
                ->find();
            if (empty($mem_info)) {
                $res2 = Db::name('mem_info')->insert($ext_data);
            } else {
                $res2 = Db::name('mem_info')->update($ext_data);
            }


            if ($res1 && $res2) {
                return array('error'=>0, 'msg'=>'更新玩家信息成功');
            } else {
                return array('error'=>2, 'msg'=>'更新玩家信息失败');
            }
        } catch (Exception $e) {
            return array('error'=>3, 'msg'=>$e->getMessage());
        }

    }

    // 验证手机号码是否正确
    public function verifyMobileCode() {
        $code = request()->param('mobilecode');
        if (empty($code)) {
            return array('error'=>2, 'msg'=>'验证码不能为空');
        }
        if ($code == Session::get('sms_code')) {
            return array('error'=>0, 'msg'=>'验证码正确');
        } else {
            return array('error'=>1, 'msg'=>'验证码不正确');
        }
    }

    // 验证原来的邮箱账户
    public function verifyEmailAccount() {
        $email = Session::get('user.email');

        if (empty($email)) {
            return array('status'=>11, 'msg'=>'玩家未绑定手机号码');
        }
        $msg = sendEmail($email);

        return $msg;
    }

    // 绑定新邮箱
    public function bindEmail() {
        $email = request()->param('email');
        $code = request()->param('code');
        if (Session::get('sms_code')==$code) {
            $id = Session::get('user.id');
            $data['id'] = $id;
            $data['email'] = $email;
            $data['bindemail'] = $email;
            $data['update_time'] = time();
            $res = Db::name('members')->update($data);

            if ($res) {
                return array('status'=>1, 'msg'=>'绑定成功');
            } else {
                return array('status'=>2, 'msg'=>'绑定失败');
            }
        } else {
            return array('status'=>3, 'msg'=>'验证码不正确');
        }
    }
}