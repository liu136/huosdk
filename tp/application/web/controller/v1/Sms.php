<?php
/**
 * Sms.php UTF-8
 * 短信接口
 *
 * @date    : 2016年8月18日下午9:47:10
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : wuyonghong <wyh@huosdk.com>
 * @version : api 2.0
 */
namespace app\web\controller\v1;

use app\common\controller\Baseweb;

class Sms extends Baseweb {
    function _initialize() {
        parent::_initialize();
    }

    /*
     * 短信发送
     */
    function send() {
        $_key_arr = array(
            'app_id',
            'client_id',
            'from',
       
            'userua',
            'mobile',
            'smstype'
        );
        $_param_data = $this->getParam($_key_arr);
        $_mobile = $_param_data['mobile'];
        $_smstype = $_param_data['smstype'];
        $_username = get_val($this->rq_data, 'username', '');
        /* 非注册发送验证码 需要有效用户名 */
         if (1 != $_smstype && empty($_username)) {
            $_username = \think\Session::get('username', 'user');
            if (empty($_username)) {
                $_map['mobile'] = $_mobile;
                $_username = \think\Db::name("members")->where($_map)->value('username');
                if (empty($_username)) {
                    return hs_api_responce(413, '手机号输入错误');
                }
            }
        }
        $_sms_class = new \huosdk\sms\Sms();
        $_data = $_sms_class->send($_mobile, $_smstype);
        $_rdata['ag'] = $this->getVal($_param_data, 'agentgame', '');
        $_rdata['ag'] = $this->getAgentgame($_rdata['ag']);
        return hs_api_responce($_data['code'], $_data['msg'], $_rdata);
    }

    //h5 修改密码 发送短信
    function send_update_password() {
        $_key_arr = array(
            'app_id',
            'client_id',
            'from',
            'userua',
            'mobile',
            'smstype'
        );
        $_param_data = $this->getParam($_key_arr);
        $_mobile = $_param_data['mobile'];
        $_smstype = $_param_data['smstype'];
        /* 非注册发送验证码 需要有效用户名 */
        if (3 != $_smstype && empty($_mobile)) {
            $map['bindmobile'] = $_mobile;
            $userdata = \think\Db::name("members")->where($map)->find();
            if (empty($userdata)) {
                return hs_api_responce(413, "请输入正确的用户名");
            }
            if (3 == $userdata['status']) {
                return hs_api_responce(413, "改用户账号已被冻结");
            }
            if(empty($userdata['mobile'])) {
                return hs_api_responce(413, "该账号未绑定手机");
            }
         
        }
        $_sms_class = new \huosdk\sms\Sms();
        $_data = $_sms_class->send($_mobile, $_smstype);
        $_rdata['ag'] = $this->getVal($_param_data, 'agentgame', '');
        $_rdata['ag'] = $this->getAgentgame($_rdata['ag']);
        return hs_api_responce($_data['code'], $_data['msg'], $_rdata);
    }

    //h5 绑定手机 发送短信
    /* 
        1.先验证是否是存在绑定手机(绑定手机),
        2.
            a.存在绑定手机则验证绑定手机号是否相同,相同发送验证码
            b.不存在绑定手机则向该手机号发送验证码

    */
    function send_sms_bindmobile() {

        $_param_data = $this->getParam();
        $appid = $_param_data['app_id'];
        $mobile = $_param_data['mobile'];
        $smstype = $_param_data['smstype'];
        $session = $this->getLoginData($appid);
        $mem_id = $session['mem_id'];
        if(empty($mem_id)){
            return hs_api_responce(413, "登录信息已失效");
        }
        $map['id'] = $mem_id;
        $userdata =  \think\Db::name("members")->where($map)->find();
        if (empty($userdata)) {
            return hs_api_responce(413, "请输入正确的用户名");
        }
        if (3 == $userdata['status']) {
            return hs_api_responce(413, "改用户账号已被冻结");
        }

        if($userdata['mobile']==$mobile){
            return hs_api_responce(413, "已绑定该手机号");
        }
        
        $_sms_class = new \huosdk\sms\Sms();
        $_data = $_sms_class->send($mobile, $smstype);
        $_rdata['ag'] = $this->getVal($_param_data, 'agentgame', '');
        $_rdata['ag'] = $this->getAgentgame($_rdata['ag']);
        return hs_api_responce($_data['code'], $_data['msg'], $_rdata);
        
    }
}