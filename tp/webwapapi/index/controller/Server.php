<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-18
 * Time: 17:24
 */
namespace app\index\controller;
use app\index\common\Base;
use app\index\model\GameServer;
use app\index\model\GameTest;

class Server extends Base {

    public function index(GameServer $gameServer, GameTest $gameTest) {
        // 获取开服信息
        $server_list['server_run_list'][0]['today_list'] = $gameServer->getTodayServerList();
        $server_list['server_run_list'][0]['about_list'] = $gameServer->getAboutServerList();
        $server_list['server_run_list'][0]['after_list'] = $gameServer->getAfterServerList();

        // 获取开测信息
        $server_list['server_test_list'][0]['today_list'] = $gameTest->getTodayServerList();
        $server_list['server_test_list'][0]['about_list'] = $gameTest->getAboutServerList();
        $server_list['server_test_list'][0]['after_list'] = $gameTest->getAfterServerList();

        return $server_list;
    }
}