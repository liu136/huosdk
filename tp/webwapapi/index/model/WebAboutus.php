<?php
/**
 * User: wangchuang
 * Date: 2017-04-05
 * Time: 10:26
 */
namespace app\index\model;
use think\Model;
use think\Db;

class WebAboutus extends Model {

    // 获取关于我们的信息
    public function getAboutUsInfo() {

        $content = Db::name('web_aboutus')->where('title', '关于我们')->value('content');

        return $content;
    }

    // 获取联系我们
    public function getContactUsInfo() {
        $content = Db::name('web_aboutus')->where('title', '联系我们')->value('content');

        return $content;
    }

    // 获取商务合作
    public function getBusinessInfo() {
        $content = Db::name('web_aboutus')->where('title', '商务合作')->value('content');

        return $content;
    }
}