<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-18
 * Time: 14:47
 */
namespace app\index\model;
use think\Model;
use think\Db;

class WebLinks extends Model {

    // 获取友情链接
    public function getWebLinks() {
        $field = "link_url, link_name, link_target";
        $web_links = Db::name('webLinks')
            ->field($field)
            ->where('link_status', 1)
            ->select();

        return $web_links;
    }
}