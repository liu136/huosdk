<?php
/**
 * Update.php UTF-8
 * 游戏更新处理
 */
namespace huosdk\game;

use think\Log;
use think\Db;

class Update {
    private $app_id;
    private $agent_id;
   //private $version;

    /**
     * 自定义错误处理
     *
     * @param msg 输出的文件
     */
    private function _error($msg, $level = 'error') {
        $_info = 'game\Update Error:'.$msg;
        Log::record($_info, 'error');
    }

    /**
     * 构造函数
     *
     * @param $rsa_pri_path string rsa私钥地址
     */
    public function __construct($app_id = 0, $agent_id = 0) {
        if (!empty($app_id)) {
            $this->app_id = $app_id;
        }
        if (!empty($agent_id)) {
            $this->agent_id = $agent_id;
        }
    }

    /**
     * 获取最新版本信息
     *
     * @param $app_id int 游戏ID
     *
     * @return $status  0 关闭 1普通更新 2强制更新 
     */
    public function getupdatestatus($app_id,$agent_id,$version) {
        // if (empty($agent_id)) {
        //     $agent_id = $this->agent_id;
        // }
        if (empty($app_id) || empty($version)) {
            return false;
        }
        /* 获取更新信息 */
        $_map['app_id'] = $app_id;
        $_gv_info = Db::name('update_version')->where($_map)->limit(1)->find();
        if (empty($_gv_info)) {
            return false;
        }
        if($_gv_info['is_update']==1){   //本app_id开启更新
            if(str_replace('.','',$version) < str_replace('.','',$_gv_info['version']) && in_array($agent_id,explode(',',$_gv_info['update_agent']))){  //如果客户端传过来的版本小于后台填写版本且渠道在更新范围内，更新
                if($_gv_info['force_update']==1){    //，强更否则只是更新  
                    $status='2';
                 }else{
                    $status='1';
                 }   
            }else{
                $status='0';
            }
        }else{
            $status='0';  //关闭更新
        }
        return $status;
    }

    /**
     * 获取更新说明
     * @param $app_id int 游戏ID
     */
    public function getupdateinfo($app_id){       
        if(empty($app_id)){
            $info='';
        }else{
            $info_arr = Db::name('update_version')->where(['app_id' => $app_id])->limit(1)->find();
            $info=$info_arr['content'];
        }
        return $info;
    }

    /**
     * 获取更新url
     * 
     */
    public function getupdateurl($app_id,$agent_id,$is_ios=0){
        if (empty($agent_id)) {
            $agent_id = $this->agent_id;
        }
        $url='';
        if($is_ios==1){
            $url_arr=Db::name('update_version')->where(['app_id'=>$app_id])->limit(1)->find();
            $url=$url_arr['download_url'];
        }else{
            $_map['app_id']=$app_id;
            $_map['agent_id']=$agent_id;
            if($agent_id==0){
                $url_info=Db::name('game_version')->where(['app_id'=>$app_id])->limit(1)->find();
                $url=DOWNSITE.$url_info['packageurl'];
            }else{
                $url_info=Db::name('agent_game')->where($_map)->limit(1)->find();
                $url=DOWNSITE.$url_info['url'];
            }
        }
        return $url;
    }
}