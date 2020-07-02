<?php
namespace app\index\model;

use think\Model;
use think\Db;
use think\Session;

class Message extends Model {

    public function index($limitStr='', $id=0) {
        $field = "title message_title, message message_content";
        $field .= ", send_time create_time";
        $message = Db::name('message')
            ->field($field)
            ->where('mem_id', $id)
            ->where('is_delete', 2)
            ->limit($limitStr)
            ->order('send_time desc')
            ->select();

        foreach ($message as $key=>$val) {
            $message[$key]['message_content'] = $val['message_content'];
            $message[$key]['create_time'] = date('Y-m-d', $val['create_time']);
        }

        return $message;
    }

    // 获取条数
    public function messageCount($id=0) {
        $count = Db::name('message')
            ->where('mem_id', $id)
            ->where('is_delete', 2)
            ->count();

        return $count;
    }

    // 获取系统消息
    public function getSystemMessage($limitStr='') {
        return $this->index($limitStr, 0);
    }

    // 获取系统消息的条数
    public function systemMessageCount() {
        return $this->messageCount(0);
    }

    // 获取活动消息
    public function getActivityMessage($limitStr='') {
        $id = Session::get('user.id');
        return $this->index($limitStr, $id);
    }

    // 获取活动消息的条数
    public function activityMessageCount() {
        $id = Session::get('user.id');
        return $this->messageCount($id);
    }
}