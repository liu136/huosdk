<?php
/**
 * MessageController.class.php UTF-8
 * 消息控制中心
 *
 * @date    : 2016年10月8日下午3:06:32
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : wuyonghong <wyh@huosdk.com>
 * @version : H5 2.0
 */
namespace Mobile\Controller;

use Common\Controller\MobilebaseController;

class ToolsController extends MobilebaseController {
    function _initialize() {
        parent::_initialize();
        $this->assign('msgactive', 'active');
        $this->assign('title', '工具');
    }

    //工具首页
    public function index(){
        $this->display();
    }

    //家长监护
    public function parents_care(){

    }
}