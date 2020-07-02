<?php
/**
 * Userreg.php UTF-8
 * 玩家注册接口
 *
 * @date    : 2016年8月18日下午9:47:10
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : wuyonghong <wyh@huosdk.com>
 * @version : api 2.0
 */
namespace app\apple\controller\v1;

class Userreg extends User {
    function _initialize() {
        parent::_initialize();
    }

    /*
     * 一键注册
     */
    function regOne() {
        $_key_arr = array('app_id', 'client_id', 'from', 'device_id', 'userua');
        $_data = $this->getParams($_key_arr);
        $_username = $this->m_class->genUsername();
        $_agentgame = $this->getVal($_data, 'agentgame', '');
        $_rdata['username'] = $_username;
//         $_rdata['password'] = $this->m_class->authPwd(hs_random(8));
        //$_rdata['password'] = hs_random(8);
        $_rdata['password'] = "";
        $_rdata['agentgame'] = $this->getAgentgame($_agentgame);
    }

    /*
     * 普通注册
     */
    function register() {
        $_key_arr = array('app_id', 'client_id', 'from', 'device_id', 'userua', 'username', 'password');
        /* Added by wuyonghong BEGIN 2017-06-20 ISSUES:2738 添加手机号登陆 不能使用手机号直接注册用户名 */
        $_sms_class = new \huosdk\sms\Sms();
        $_is_mobile = $_sms_class->checkMoblie($this->rq_data['username']);
        if ($_is_mobile) {
            return hs_api_responce('411', '手机号请使用手机注册');
        }
        /* END 2017-06-20 ISSUES:2738 */
        $_rdata = $this->_reg($_key_arr);
        $_float_is_show = 1;
        return $this->rgReturn($_rdata["mem_id"], $_rdata["cp_user_token"], $_rdata["agentgame"], $_float_is_show);
    }

    /*
     * 手机注册
     */
    function regMobile() {
        $_key_arr = array('app_id', 'client_id', 'from', 'device_id', 'userua', 'mobile', 'password',
                          'smstype', 'smscode');
        /* 验证手机短信 */
        $this->checkMobile();
        $this->rq_data['username'] = $this->rq_data['mobile'];
        $_rdata = $this->_reg($_key_arr);
        $_float_is_show = 1;
        return $this->rgReturn($_rdata["mem_id"], $_rdata["cp_user_token"], $_rdata["agentgame"], $_float_is_show);
    }

    /*
     * 注册函数实体
     */
    private function _reg($_key_arr) {
        $_data = $this->getParams($_key_arr);
        $_agentgame = $this->getVal($_data, 'agentgame', '');
        $_data['agentgame'] = $this->getAgentgame($_agentgame);
        $_data['ip'] = $this->request->ip();
        $_mem_info = $this->m_class->regMem($_data);
        if (-1 == $_mem_info['id']) {
            return hs_api_responce('411', '用户名不合法或密码不合法');
        }
        if (-3 == $_mem_info['id']) {
            return hs_api_responce('411', '用户名已存在');
        }
        $_rdata = $this->getReturn($_mem_info, $_data['agentgame'], 1);
        return $_rdata;
    }
}