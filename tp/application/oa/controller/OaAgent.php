<?php
/**
 * Created by PhpStorm.
 * User: Ksh
 * Date: 2017/6/26
 * Time: 11:12
 */

namespace app\oa\controller;

use huosdk\player\Member;
use think\Db;

class OaAgent extends Oacallback {
    function _initialize() {
        parent::_initialize();
        $this->checkParam();
        $pwd = '666666';
        if ($this->param['type'] == 'promoter') {
            $_rs = $this->oa2addSub(
                $this->param['legion_name'], $this->param['username'], $this->param['agent_name'], $pwd
            );
        } else {
            $_rs = $this->oa2AddAgent(
                $this->param['username'], $this->param['agent_name'], $pwd
            );
        }

        return $_rs;
    }

    public function index() {
    }

    function checkParam() {
        $this->checkType();
        $this->checkUsername();
        $this->agentName();
    }

    function checkType() {
    }

    function checkUsername() {
    }

    function agentName() {
    }

    public function oa2AddAgent($user_nicename, $user_login, $user_pass) {
        if (!$user_nicename) {
            hs_api_json('201', '渠道名称不能为空');
            exit;
        }
        if (!$user_pass) {
            hs_api_json('201', '密码不能为空');
            exit;
        }
        if (!$user_login) {
            hs_api_json('201', '帐号不能为空');
            exit;
        }
        //判断该添加渠道名称是否已经存在
        $checknicename = Db("users")->where(array("user_nicename" => $user_nicename))->find();
        if (!empty($checknicename)) {
            hs_api_json('201', '该渠道名称已被使用，请勿重复添加');
            exit;
        }
        //判断该添加账号是否已经存在
        $checkusername = Db("users")->where(array("user_login" => $user_login))->find();
        if (!empty($checkusername)) {
            hs_api_json('201', '该渠道账号已被使用，请勿重复添加');
            exit;
        }
        $agent_role_id = Db('role')->where(array("name" => "渠道专员"))->value("id");
        $data = array();
        $_member_class = new Member();
        $data['user_pass'] = $_member_class->userAuthPassword($user_pass);
        $data['user_login'] = $user_login;
        $data['user_nicename'] = $user_nicename;
        $data['create_time'] = date("Y-m-d H:i:s", time());
        $data['user_type'] = $agent_role_id;
        $data['user_status'] = 2;
        $data['ownerid'] = 1;
        $_rs = Db('users')->insert($data);
        if (false != $_rs) {
            hs_api_json('200', '创建一级渠道成功');
        }
        hs_api_json('201', '创建一级渠道失败');
    }

    public function oa2addSub($_legion_name, $user_nicename, $user_login, $user_pass) {
        if (!$_legion_name) {
            hs_api_json('201', '一级渠道名称不能为空');
            exit;
        }
        if (!$user_nicename) {
            hs_api_json('201', '二级渠道名称不能为空');
            exit;
        }
        if (!$user_pass) {
            hs_api_json('201', '密码不能为空');
            exit;
        }
        if (!$user_login) {
            hs_api_json('201', '帐号不能为空');
            exit;
        }
        $legion_role_id = Db::name('role')->where(array("name" => "渠道专员"))->value('id');
        $_legion_info = Db::name('users')->where('user_login', $_legion_name)->where('user_type', $legion_role_id)
                          ->find();
        if (empty($_legion_info)) {
            return hs_api_json('201', '军团长不存在');
        }
        //判断该添加渠道名称是否已经存在
        $checknicename = Db("users")->where(array("user_nicename" => $user_nicename))->find();
        if (!empty($checknicename)) {
            hs_api_json('201', '该渠道名称已被使用，请勿重复添加');
            exit;
        }
        //判断该添加账号是否已经存在
        $checkusername = Db("users")->where(array("user_login" => $user_login))->find();
        if (!empty($checkusername)) {
            hs_api_json('201', '该渠道账号已被使用，请勿重复添加');
            exit;
        }
        $agent_role_id = Db::name('role')->where(array("name" => "公会渠道"))->value('id');
        $data = array();
        $_member_class = new Member();
        $data['user_pass'] = $_member_class->userAuthPassword($user_pass);
        $data['user_login'] = $user_login;
        $data['user_nicename'] = $user_nicename;
        $data['create_time'] = date("Y-m-d H:i:s", time());
        $data['user_type'] = $agent_role_id;
        $data['user_status'] = 2;
        $data['ownerid'] = $_legion_info['id'];
        $_rs = Db('users')->insert($data);
        if (!empty($_rs)) {
            hs_api_json('200', '创建二级渠道成功');
        }
        hs_api_json('201', '创建二级渠道失败');
    }
}