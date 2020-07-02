<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-27
 * Time: 15:47
 */
namespace Web\Controller;

use Common\Controller\AdminbaseController;

class MessageController extends AdminbaseController {
    //消息管理
    public function index(){
        redirect(U('Newapp/Message/index'));
        exit;
    }
}