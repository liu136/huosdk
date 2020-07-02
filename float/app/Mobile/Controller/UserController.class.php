<?php
/**
 * UserController.class.php UTF-8
 * 用户中心控制
 *
 * @date    : 2016年7月8日下午2:54:46
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : wuyonghong <wyh@1tsdk.com>
 * @version : 1.0
 */
namespace Mobile\Controller;

use Common\Controller\MobilebaseController;

class UserController extends MobilebaseController {
    protected $member_model;

    function _initialize() {
        parent::_initialize();
        $this->member_model = M('members');
        $this->assign('useractive', 'active');
    }

    // 浮点用户信息首页
    public function index() {
        $mem_id = sp_get_current_userid();
        $userdata = $this->member_model->where(array('id' => $mem_id))->find();
        $appid = $_SESSION['app']['app_id'];
        $yxb_cnt = M('gm_mem')
            ->where(array('mem_id' => $mem_id, "app_id" => $appid))
            ->getField("remain");
        //计算我的该游戏的礼包个数
        $gamegift = M('gift')->where(array("app_id" => $appid))->getField("id", true);
        $giftmap['mem_id'] = $mem_id;
        if (!empty($gamegift)) {
            $giftmap['gf_id'] = array("in", $gamegift);
            $giftnumber = M('gift_log')->where($giftmap)->count();
        } else {
            $giftnumber = 0;
        }
        $this->assign("giftnumber", $giftnumber);
// 		$ptb_sum = M('ptb_mem')
//         ->where(array('mem_id'=>$mem_id))
//         ->getField('remain');
// 		$this->assign("ptb_sum",$ptb_sum);
        $this->assign("gmremain", $yxb_cnt);
        $this->assign("title", '用户中心');
        $this->assign("userdata", $userdata);
        $this->display();
    }

    //H5 SDK 获取用户信息
    //用户绑定手机信息,身份证验证信息,礼包信息,客服信息,推荐信息(暂无)
    public function getuserinfo(){
        $appid = I('get.appid');
        if($appid){

            $time = time();
            $uid = sp_get_current_userid();
            $userdata = $this->member_model->where(array('id' => $uid))->find();
            //用户信息
            if(!$userdata){
                $this->error("非法请求");
            }
            $userinfo = array(
                'uid'=>$userdata['id'],
                'appid'=>$appid,
                'account'=>$userdata['username'],
                'bind_phone'=>$userdata['mobile']!=''?1:0,//1.绑定手机 0.未绑定手机
                'bind_mail'=>$userdata['mail']!=''?1:0,//1.绑定邮箱,0,未绑定邮箱
                'bind_real'=>2,//预留留实名认证,1:已实名,0:未实名,2:取消实名功能 $userdata->idcard
                );
            //所有礼包
            $allgift = $this->this_allgift_list($userinfo,$time);
            //用户礼包
            $mygift = $this->this_mygift_list($userinfo);
            //客服信息
            $contact_field = "qq,content";
            $contact = M('game_contact')->where('app_id = %d',0)->field($contact_field)->select();

            $json['userinfo']= $userinfo;
            $json['allgift']= $allgift;
            $json['mygift']= $mygift;
            $json['contact']= $contact;
            $json['status']= '1';

            $this->ajaxReturn($json);
        }
        $this->error("非法请求");

    }

    private function this_allgift_list($userinfo,$time){
        $field = "gf.id giftid, gf.app_id gameid,gf.title giftname,gf.total total";
        $field .= ",gf.remain remain,gf.content content";
        $field .= ",FROM_UNIXTIME(gf.start_time, '%Y-%m-%d %H:%i:%S') starttime,FROM_UNIXTIME(gf.end_time, '%Y-%m-%d %H:%i:%S') enttime";
        $where['gf.app_id'] = $userinfo['appid'];
        $where['gf.is_delete'] = 2;
        $where['gf.end_time'] = array(
            'GT',
            $time
        );
        $where['gf.remain'] = array(
            'GT',
            0
        );
        $final_data = array();
        $giftdata = M('gift')->alias('gf')->field($field)->where($where)->order("gf.start_time ASC")->select();
        foreach ($giftdata as $k => $v) {
            $gflog_info = $this->getUserGift($v['giftid'], $userinfo['uid']);
            if ($gflog_info && !empty($gflog_info['code'])) { // 不显示已领取的礼包
                // unset($giftdata[$k]);
            }else{
                $final_data[] = $v;
            }
        }
        return $final_data;
    }

    private function this_mygift_list($userinfo){
        $gfl_map['gf.app_id'] = $userinfo['appid'];
        $gfl_map['gfl.mem_id'] = $userinfo['uid'];
        $gfl_map['gf.is_delete'] = 2;
        $field = "gf.id giftid, gf.app_id gameid,gf.title giftname,gf.total total,gfl.code code";
        $field .= ",gf.remain remain,gf.content content";
        $field .= ",FROM_UNIXTIME(gf.start_time, '%Y-%m-%d %H:%i:%S') starttime,FROM_UNIXTIME(gf.end_time, '%Y-%m-%d %H:%i:%S') enttime";
        $joingift = "LEFT JOIN ".C('DB_PREFIX')."gift gf ON gf.id =gfl.gf_id";
        $gfl_model = M('gift_log');
        $giftdata = $gfl_model->alias('gfl')->field($field)->join($joingift)->where($gfl_map)
                              ->order(
                                  'gfl.id desc'
                              )->select();
        return $giftdata;
       
    }

    private function getUserGift($gf_id, $mem_id) {
        if (empty($gf_id) || empty($mem_id)) {
            return array();
        }
        $giftmap['mem_id'] = $mem_id;
        $giftmap['gf_id'] = $gf_id;
        $gflog_info = M('gift_log')->where($giftmap)->find();
        return $gflog_info;
    }

    //修改密码
    public function uppwd() {
        $this->assign("title", '修改密码');
        $this->display();
    }

    /*
     * 修改密码处理函数
     */
    public function uppwd_post() {
        if (IS_POST) {
            $action = I('post.action');
            if ('updatepwd' == $action) {
                $oldpwd = I('post.oldpwd');
                $newpwd = I('post.newpwd');
                $verifypwd = I('post.verifypwd');
                //不能为空
                if (empty($oldpwd)) {
                    $this->error("原密码不能为空.", '', true);
                    exit;
                }
                //密码不能为空
                if (empty($newpwd)) {
                    $this->error("新密码不能为空.", '', true);
                    exit;
                }
                //确认密码不能为空
                if (empty($newpwd)) {
                    $this->error("新密码不能为空.", '', true);
                    exit;
                }
                //用户名必须为数字字母组合, 长度在6-16位之间
                $checkExpressions = "/^[0-9A-Za-z-`=\\\[\];',.\/~!@#$%^&*()_+|{}:\"<>?]{6,16}$/";
                if (false == preg_match($checkExpressions, $newpwd)) {
                    $this->error("密码必须由6-16位的数字、字母、符号组成", '', true);
                    exit;
                }
                //新密码与确认密码不一致
                if ($newpwd != $verifypwd) {
                    $this->error("确认密码与新密码不一致", '', true);
                    exit;
                }
                $data['password'] = pw_auth_code($oldpwd);
                $mem_id = sp_get_current_userid();
                $userpwd = $this->member_model->where(array('id' => $mem_id))->getField('password');
                if ($data['password'] == $userpwd) {
                    $data['password'] = pw_auth_code($newpwd);
                    $data['update_time'] = time();
                    $rs = $this->member_model->where(array('id' => $mem_id))->save($data);
                    if (false != $rs) {
                        $this->success("修改密码成功", U('User/pwd_success'), true);
                        exit;
                    }
                } else {
                    $this->error("原密码错误", '', true);
                    exit;
                }
            }
        }
    }

    //操作成功跳转页面
    public function pwd_success() {
        $title = "密码修改";
        $msg = "密码修改成功";
        $this->ac_success($title, $msg);
    }

    //操作成功跳转页面
    public function pwd_error() {
        $title = "密码修改";
        $msg = "密码修改失败";
        $this->ac_error($title, $msg);
    }
}