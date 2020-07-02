<?php
/**
 * System.php UTF-8
 * 苹果初始化
 *
 * @date    : 2017/11/8 18:38
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : wuyonghong <wyh@huosdk.com>
 * @version : HUOSDK 8.0
 */
namespace app\web\controller\v1;

use app\common\controller\Baseweb;
use huosdk\game\Version;
use huosdk\log\Devicelog;
use huosdk\log\Gamelog;
use think\Db;
use think\Session;
use huosdk\common\Simplesec;
use think\Config;

class System extends Baseweb {
    function _initialize() {
        parent::_initialize();
    }

    /**
     * http://doc.1tsdk.com/43?page_id=2242
     *
     * 初始化接口
     *
     * @return $this
     */
    function open() {
        $_key_arr = array(
            'app_id', 'client_id', 'from'
        );
        $_data = $this->getParams($_key_arr);
        $_agentgame = $this->getVal($_data, 'agentgame', '');
        $_gol_data['mem_id'] = 0;
        $_gol_data['ver_id'] = $this->getVal($_data, 'client_id', 0);
        $_gol_data['app_id'] = $this->getVal($_data, 'app_id', 0);
        $_gol_data['agentname'] = $_agentgame;
        $_gol_data['agent_id'] = $this->getAgentid($_gol_data['agentname']);
        $_gol_data['device_id'] = $this->getVal($_data, 'device_id', '');
        $_gol_data['idfa'] = $this->getVal($_data, 'idfa', '');
        $_gol_data['idfv'] = $this->getVal($_data, 'idfv', '');
        $_gol_data['mac'] = $this->getVal($_data, 'mac', '');
        $_gol_data['deviceinfo'] = $this->getVal($_data, 'deviceinfo', '');
        $_gol_data['userua'] = $this->getVal($_data, 'userua', '');
        $_gol_data['local_ip'] = $this->getVal($_data, 'local_ip', '');
        $_gol_data['ipaddrid'] = $this->getVal($_data, 'ipaddrid', 0);
        $_gol_data['create_time'] = time();
        $_gol_data['ip'] = $this->request->ip();
        $_ss_class = new Simplesec();
        $_user_token = $_ss_class->encode(session_id(), Config::get('config.HSAUTHCODE'));
        $_gol_data['open_cnt'] = $this->getVal($_data, 'open_cnt', 0);
        /* 安装打开日志*/
        $_temp_map = [];
        $_temp_map['app_id'] = $_gol_data['app_id'];
        $_temp_map['agent_id'] = $_gol_data['agent_id'];
        $_temp_map['deviceid'] = $_gol_data['device_id'];
        $_temp_map['network'] = 'tpplayer';
        //插入打开记录
        $_gl_class = new Gamelog('game_openlog');
        $_rs = $_gl_class->insert($_gol_data);
        if (!$_rs) {
            return hs_player_responce('1000', '服务器内部错误');
        }
        $_data['open_cnt'] = 1;
        if (empty($_agentgame) && 1 == $_data['open_cnt']) {
            //调起异步操作 计算agent_game wuyonghongtest
            //computeAg();
            /* 获取渠道 */
            $_dl_class = new Devicelog();
            $_agentgame = $_dl_class->getAgentgame($_gol_data['device_id'], $_gol_data['app_id']);
        }
        Session::set('open_cnt', $this->rq_data['open_cnt'], 'device');
         
        $acd =$this->getVal($_data, 'acd', '');
        $akd =$this->getVal($_data, 'akd', '0000000');
        $_map['id'] = $_gol_data['app_id'] ;
        $gameurl = DB::name('game')->where($_map)->value('gameurl');
        if( empty( $gameurl)){
            return hs_player_responce('1001', '初始化失败.');
        }

        
       
        return $this->initReturn( $_user_token, $_agentgame,"init", $gameurl, $_gol_data['app_id']);
    }
}