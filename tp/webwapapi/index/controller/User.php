<?php

namespace app\index\controller;

use app\index\common\Base;
use app\index\model\Members;
use app\index\model\Gift;
use app\index\model\Coupon;
use app\index\model\Message;
use app\index\model\Slide;

class User extends Base {

    public function initialize() {

        parent::_initialize();
    }

    // 玩家登录
    public function pclogin(Members $members) {

        // 判断玩家是否登录成功
        $status = $members->login();

        return $status;
    }

    // 玩家注册
    public function pcregister(Members $members) {
        // 玩家注册
        $regRes = $members->register();

        return $regRes;
    }

    // 获取玩家的信息
    public function pcuserinfo(Members $members) {
        $regRes = $members->getMemberInfo();
        $regRes['data']['btpname'] = $this->btpname;

        return $regRes;
    }

    // 玩家退出
    public function pclogout(Members $members) {
        return $members->logout();
    }

    // 玩家存号箱
    public function gift(Gift $gift) {
        $data['list_row'] = 10;
        $limitStr = $this->getPageString($data['list_row']);
        $data['my_gifts'] = $gift->getMemGift($limitStr);
        $data['gift_count'] = $gift->memGiftCount();

        return $data;
    }

    // 获取玩家的代金券
    public function card(Coupon $coupon) {
        $limitStr = $this->getPageString();
        $data['my_cards'] = $coupon->getMemCard($limitStr);
        $data['card_count'] = $coupon->memCardCount();

        return $data;
    }

    // 玩家登录的状态
    public function loginStatus(Members $members) {
        return $members->loginStatus();
    }

    // 玩家的消息
    public function message(Message $message) {
        // 系统消息
        $data['system_message_data'] = $message->getSystemMessage();
        $data['system_message_count'] = $message->systemMessageCount();

        // 活动消息
        $data['activity_message_data'] = $message->getActivityMessage();
        $data['activity_message_count'] = $message->activityMessageCount();

        return $data;
    }

    // 获取系统消息的分页数据
    public function getSystemMessagePage(Message $message) {
        $data['list_row'] = 5;
        $limitStr = $this->getPageString($data['list_row']);
        $data['system_message'] = $message->getSystemMessage($limitStr);
        $data['message_count'] = $message->systemMessageCount();
        //$limitStr = $this->getPageString();
        //$data = $message->getSystemMessage($limitStr);

        return $data;
    }

    // 获取活动消息的分页数据
    public function getActivityMessagePage(Message $message) {
        $limitStr = $this->getPageString();
        $data = $message->getActivityMessage($limitStr);

        return $data;
    }

    // 获取登录背景图
    public function loginBackground(Slide $slide) {
        $background['login_background_image'] = $slide->loginBackground();

        return $background;
    }

    // 获取手机验证码或者是邮箱验证码
    public function getMobileCode() {
        $mobile = request()->param('mobile');

        if (isMobileNumber($mobile)) {
            $res = sendMsg($mobile);
        } else if (isEmail($mobile)) {
            // 发送邮件的方法
            $res = sendEmail($mobile);
        }

        return $res;
    }

    // 判断玩家账户是否存在
    public function nameIsExists(Members $members) {
        $username = request()->param('username');
        $userExists = $members->usernameIsExists($username);
        if ($userExists) {
            return $userExists;
        } else {
            return array('error_code'=>0, 'msg'=>'用户名不存在');
        }
    }

    // 修改密码
    public function editPwd(Members $members) {
        return $members->updatePwd();
    }

    // 修改用户信息
    public function editUserinfo(Members $members) {
        return $members->updateUserinfo();
    }

    // 验证原手机号
    public function verifyMobile(Members $members) {
        return $members->verifyMobileNumber();
    }

    // 绑定新手机号
    public function bindNewMobile(Members $members) {
        return $members->bindMobile();
    }

    // 验证验证码是否正确
    public function verifyMobileCode(Members $members) {
        return $members->verifyMobileCode();
    }

    // 验证原邮箱号
    public function verifyEmail(Members $members) {
        return $members->verifyEmailAccount();
    }

    // 绑定新邮箱号
    public function bindNewEmail(Members $members) {
        return $members->bindEmail();
    }
}