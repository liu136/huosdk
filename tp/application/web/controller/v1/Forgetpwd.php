<?php
/**
 * ForgetpwdController.class.php UTF-8
 * 找回密码
 *
 * @date    : 2016年9月7日下午3:26:39
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : wuyonghong <wyh@huosdk.com>
 * @version : float 2.0
 */
namespace app\web\controller\v1;

 
use think\Db;
use think\Session;
use think\Cache;
use huosdk\common\Simplesec;
use think\Config;

class Forgetpwd  extends User {
    function _initialize() {
        parent::_initialize();
    }

 
    public function update() {
        $_key_arr = array('app_id', 
                        'client_id', 
                        'from',  
                        'userua', 
                        'smstype',
                        'smscode');
        $_data = $this->getParams($_key_arr);
        $this->checkMobile();
        $map['bindmobile'] =$_data['mobile'];
        $userdata = \think\Db::name("members")->where($map)->find();
        if (empty($userdata)) {
            return hs_api_responce('411', '请输入正确的用户名');
        }
        if (3 == $userdata['status']) {
            return hs_api_responce('412', '改用户账号已被冻结');
        }
        if (empty($userdata['mobile'])) {
            return hs_api_responce('413', '该账号未绑定手机');
        }
 
        $_up_data['password'] =  $this->m_class->authPwd($_data['password']);
        $_up_data['update_time'] = time();
        $_mem_map['id'] = $userdata['id'];
        $rs = \think\Db::name('members')->where($_mem_map)->update($_up_data);
        if ($rs) {
            $_ss_class = new Simplesec();
            $cp_user_token = $_ss_class->encode(session_id(), Config::get('config.CPAUTHCODE'));
            return $this->rgReturn($userdata['id'],$cp_user_token , '', 1,'update');
        } else {
            return hs_api_responce('500', '服务器内部错误');
        }


    }

     
}