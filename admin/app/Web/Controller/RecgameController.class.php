<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-25
 * Time: 10:46
 */
namespace Web\Controller;

use Common\Controller\AdminbaseController;

class RecgameController extends AdminbaseController {
    //推荐设置
    public function index(){
        redirect(U('IosApp/Recgame/index'));
        exit;
    }
}