<?php
/**
 * Userlogin.php UTF-8
 * 玩家登陆接口
 *
 * @date    : 2016年8月18日下午9:47:10
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : wuyonghong <wyh@huosdk.com>
 * @version : api 2.0
 */
namespace app\web\controller\v1;
use think\Session;
use app\common\controller\Baseweb;
use think\Db;
class Userlogin extends User {
    function _initialize() {
        parent::_initialize();
    }

    /*
     * 普通登陆
     */
    function login() {
        $_key_arr = array(
            'app_id',
            'client_id',
            'from',
           
           
            'username',
            'password'
        );

        $_rdata = $this->_login($_key_arr);

        $_float_is_show = 1; 
        $loginType=$_rdata['is_mobile']?"phone":"account";
        $_rdata['loginType']=$loginType;
        $_rdata["mem_id"]!= '' && $_rdata["mem_id"] != null && $this->SaveLoginData($_rdata,$_rdata['app_id']);
        return $this->LgReturn($_rdata["mem_id"], $_rdata["cp_user_token"], $_rdata["app_id"], "login", $_rdata["deviceType"], $loginType,isset( $_rdata['userlist'])? $_rdata['userlist']:'');
        //return $this->rgReturn($_rdata["mem_id"], $_rdata["cp_user_token"], $_rdata["agentgame"], $_float_is_show,"login");
    }

    /*获取缓存登陆*/
    function automaticLogin(){
        $_key_arr = array(
            'app_id',
            'from',
            'user_token',
        );
        $_data = $this->getParams($_key_arr);
        $_rdata=$this->getLoginData($_data["app_id"]);
  
        if(empty($_rdata)){
            return hs_player_responce('200', 'automaticLoginFail');
        }
        return $this->LgReturn($_rdata["mem_id"], $_rdata["cp_user_token"], $_data["app_id"], "login", $_data["deviceType"], $_rdata["loginType"] );
    }

    /*
     * 手机登陆
     */
    function loginMobile() {
        $_key_arr = array(
            'app_id',
            'client_id',
            'from',
         
            'userua',
            'mobile',
            'password',
            'smscode',
            'smstype'
        );
        /* 验证手机短信 */
        $this->checkMobile();
        $this->rq_data['username'] = $this->rq_data['mobile'];
        $_rdata = $this->_login($_key_arr);
        $_float_is_show = 1;
        return $this->rgReturn($_rdata["mem_id"], $_rdata["cp_user_token"], $_rdata["agentgame"], $_float_is_show);
    }

    /*
     * 第三方登陆
     */
    function loginOauth() {
        $_key_arr = array(
            'app_id',
            'client_id',
            'from',
            //'device_id',
            //'userua',
            'openid',
            'access_token',
            //'userfrom'
        );
        $_data = $this->getParams($_key_arr);

        if($_data['sign'] !=  md5(md5($_data['openid'].$_data['app_id'] .$_data['timestamp'] )."qqlogin123")){
            return hs_player_responce('411', 'sign 错误');
        }


        $_agentgame = $this->getVal($_data, 'agentgame', '');
        $_data['agentgame'] = $this->getAgentgame($_agentgame);
        $_data['ip'] = $this->request->ip();
        $_data['expires_date'] = 0;
        $_data['portrait'] = '';
        $_mem_info = $this->m_class->loginOauth($_data);
        if (-411 == $_mem_info['id']) {
            return hs_player_responce('411', '用户不存在');
        }
        if (-412 == $_mem_info['id']) {
            return hs_player_responce('412', '密码错误');
        }
        if (-3 == $_mem_info['id']) {
            return hs_player_responce('411', '用户已禁用');
        }
        if (0 > $_mem_info['id']) {
            return hs_player_responce(0 - $_mem_info['id'], '登陆失败');
        }
        $_flag = 0;
        if (!empty($_mem_info['flag'])) {
            $_flag = $_mem_info['flag'];
        }
        
        $_rdata = $this->getReturn($_mem_info, $_data['agentgame'], $_flag);
        $_rdata['loginType']="qq";//暂时定义为qq，目前只有qq的登陆
        $this->SaveLoginData($_rdata,$_data['app_id']);

       
        return $this->GotoH5Game($_rdata["mem_id"], $_rdata["cp_user_token"], $_data["app_id"], "login", $_data["deviceType"]);

    }

    /*
     * 登陆函数实体
     */
    private function _login($_key_arr) {
        $_data = $this->getParams($_key_arr);
       
        $_agentgame = $this->getVal($_data, 'agentgame', '');
        $_data['agentgame'] = $this->getAgentgame($_agentgame);
        /* Modified by wuyonghong BEGIN 2017-06-20 ISSUES:2738 手机登陆 */
        $_sms_class = new \huosdk\sms\Sms();
        $_is_mobile = $_sms_class->checkMoblie($_data['username']);

        if ($_is_mobile) {
            /* 获取字符串 */
            $_mem_info_array = $this->m_class->loginMobile($_data);
            if (empty($_mem_info_array['id'])) {
                if (1 < count($_mem_info_array)) {
                    $_user_list = [];
                    foreach ($_mem_info_array as $_val) {
                        $_list_data['username'] = $_val['username'];
                        $_user_list[] = $_list_data;
                    }
                    $_rdata['mem_id'] = null;
                    $_rdata['cp_user_token'] = '';
                    $_rdata['agentgame'] = null;
                    $_rdata['nickname'] = null;
                    $_rdata['portrait'] = null;
                    $_rdata['menu'] = null;
                    $_rdata['userlist'] = $_user_list;
                    $_rdata['deviceType']= $_data['deviceType'];
                    $_rdata['app_id']= $_data['app_id'];
                    $_rdata['is_mobile']=$_is_mobile;
                    return $_rdata;
                } else
                    if (1 == count($_mem_info_array)) {
                    /* 只有一个账号,直接登陆 */
                    $_mem_info = $_mem_info_array[0];
                } else {
                    $_mem_info['id'] = -411;
                }
            } else {
                $_mem_info = $_mem_info_array;
            }
        } else {
            $_map['username'] = $_data['username'];
            $_mem_info = $this->m_class->loginMem($_data);
        }
        if (-411 == $_mem_info['id']) {
            return hs_api_responce('411', '用户不存在');
        }
        /* END 2017-06-20 ISSUES:2738 */
        if (-412 == $_mem_info['id']) {
            return hs_api_responce('412', '密码错误');
        }
        if (-3 == $_mem_info['id']) {
            return hs_api_responce('411', '用户已禁用');
        }
        if (0 > $_mem_info['id']) {
            return hs_api_responce(0 - $_mem_info['id'], '用户名错误');
        }
        $_rdata = $this->getReturn($_mem_info, $_data['agentgame'], 0);
        $_rdata['deviceType']= $_data['deviceType'];
        $_rdata['app_id']= $_data['app_id'];
        $_rdata['is_mobile']=$_is_mobile;

        return $_rdata;
    }

    /*
     * 登出接口
     */
    function logout() {
        parent::logout();
        $_key_arr = array(
            'app_id',
            'client_id',
            'from',
            'user_token',
            'userua'
        );
        $_data =$this->getParams($_key_arr);
        $_map['id'] = $_data['app_id'];
        $GameUrl = DB::name('game')->where($_map)->value('gameurl');
        $_rdata['url'] =  $GameUrl;
         return hs_api_responce(200, '登出成功', $_rdata);
    }
}