<?php
namespace Sdk\Controller;

use Common\Controller\AdminbaseController;

class VersionController extends AdminbaseController {
    function _initialize() {
        parent::_initialize();
    }

    public function index() {
        $hs_ui_filter_obj = new \Huosdk\UI\Filter();
        $this->assign("app_select", $hs_ui_filter_obj->app_select());
        $this->assign("agent_select", $hs_ui_filter_obj->agent_select());
        $where = array();
        $hs_where_obj = new \Huosdk\Where();
        $hs_where_obj->get_simple($where, "app_id", "gg.app_id");
        $hs_where_obj->get_simple($where, "agent_id", "gg.agent_id");
        $where["gg.is_delete"] = 2;
        $model = M('agent_game');
        $app_appid = C("APP_APPID");
        $where['_string'] = "gg.url IS NOT NULL AND (gg.app_id != $app_appid )";
//        $where['gg.app_id'] = array("neq",C("APP_APPID"));
        $count = $model
            ->field("gg.*,u.user_nicename as agent_name,g.name as game_name")
            ->alias("gg")
            ->where($where)
            ->join("LEFT JOIN ".C("DB_PREFIX")."game g ON g.id=gg.app_id")
            ->join("LEFT JOIN ".C("DB_PREFIX")."users u ON u.id=gg.agent_id")
            ->count();
        $page = $this->page($count, 20);
        $items = $model
            ->field("gg.*,u.user_nicename as agent_name,g.name as game_name")
            ->alias("gg")
            ->where($where)
            ->join("LEFT JOIN ".C("DB_PREFIX")."game g ON g.id=gg.app_id")
            ->join("LEFT JOIN ".C("DB_PREFIX")."users u ON u.id=gg.agent_id")
            ->limit($page->firstRow, $page->listRows)
            ->order("gg.id desc")
            ->select();
        \Huosdk\Data\FormatRecords::package_generate_status($items);
        $this->assign("items", $items);
        $this->assign("formget", $_GET);
        $this->_GetVersion();
        $this->_Getdownloadurl();
        $this->_Getgamename();
        $this->assign("page", $page->show('Admin'));
        $this->display();
    }
    //设置强更参数
    public function set_update_switch_condition() {
        $appid = I('app_id');
        $item = [];
        if ($appid <= 0) {
            $this->error("数据传输失败，请刷新页面重试");
        }
            $item = M('agent_game')
            ->field("gg.*,u.user_nicename as agent_name")
            ->alias("gg")
            ->where(['app_id' => $appid])
            ->join("LEFT JOIN ".C("DB_PREFIX")."users u ON u.id=gg.agent_id")
            ->where(['app_id' => $appid])
            ->select();
        $appname=M('game')->where(['id'=>$appid])->getField('name');
        $this->assign("item", array_chunk($item,8));
        $this->assign("appid", $appid);
        $this->_GetVersion();
        $this->assign("appname",$appname);
        $this->display("Version/edit");
    }
    /**
     * 更新开启
     */
    public function Isupdate(){
        $app_id=I('app_id');
        $status=I('status');
        if ($app_id <= 0 ) {
            $this->ajaxReturn(array("error" => "2", "msg" => '数据传送失败'));
        }
        $res = M('update_version')->where(['app_id' => $app_id])->save(['is_update'=>$status]);
        if($res){
             $this->ajaxReturn(array("error" => "0", "msg" => "更新设置成功"));
        }else{
            $this->ajaxReturn(array("error" => "1", "msg" => '更新设置失败！！！'));   
        }

    }
    /**
     **设置强更切换内容
     **/
    public function set_update_switch_do() {
        $appid = I('appid', 0);
        if ($appid <= 0) {
            $this->error("数据传输失败，请刷新页面重试");
        }
        //$is_update = I('is_update');
        $force_update = I('force_update');
        $version = I('version', '');
        $content = I('content', '');
        $agent_id = I('agent_id', '');  //强更渠道id，数组
        
        if (empty($version)) {
            $this->error("请输入版本号");
        }
        if (empty($content)) {
            $this->error("请输入更新内容");
        }
        if (empty($agent_id) && !is_array($agent_id)) {
            $this->error("请选择更新渠道");
        }else{
            foreach ($agent_id as $value) {
                if(isset($update_agent)){
                    $update_agent.=','.$value;
                }else{
                    $update_agent=$value;
                }
            }
        }
        $param=[
            'app_id'=> $appid,
            'update_agent'=>$update_agent,
            //'is_update'=>$is_update,
            'force_update'=>$force_update,
            'content'=>$content,
            'version'=>$version
        ];
        $id = M('update_version')->where(['app_id' => $appid])->getField("id");
        if (!empty($id) && $id > 0) {
            $res = M('update_version')->where(['id' => $id])->save($param);
        } else {
            $res = M('update_version')->add($param);
        }
        if ($res) {
            $this->success("设置成功");
        } else {
            $this->error("设置没有变动，请重试");
        }
    }
    //获取更新版本app_id下的版本信息
    public function _GetVersion(){
        $data=[];
        $app_id=I('app_id',0);
        if($app_id){
            $version=M('update_version')->where(['app_id'=>$app_id])->select();
            foreach ($version as  $val) {
                $data['is_update']=$val['is_update'];
                $data['force_update']=$val['force_update'];
                $data['version']=$val['version'];
                $data['content']=$val['content'];
                $data['update_agent_id']=explode(',',$val['update_agent']);
                // foreach (explode(',',$val['update_agent']) as  $v) {
                //     $data[$v]['is_update']=$val['is_update'];
                //     $data[$v]['force_update']=$val['force_update'];
                //     $data[$v]['version']=$val['version'];

                // }
            }
        }
        $this->assign("data", $data);
        $this->assign("app_id",$app_id);
    }
    //获取更新更新下载地址
    public function _Getdownloadurl(){
        $url=[];
        $is_ios=false;
        $app_id=I('app_id',0);
        if($app_id){
            //判断表中是否有下载地址，ios需手动加入
            $ios_url=M('update_version')
            ->field("gg.*,g.classify as game_type")
            ->alias("gg")
            ->where(['app_id'=>$app_id])
            ->join("LEFT JOIN ".C("DB_PREFIX")."game g ON g.id=gg.app_id")
            ->select();
            if ($ios_url[0]['game_type']==401) {  //401表示appstore类型游戏
               $url['ios']=$ios_url[0]['download_url'];
               $is_ios=true;
            }else{
                $url_info=M('game_version')->where(['app_id'=>$app_id])->limit(1)->find();
                $url[0]=DOWNSITE.$url_info['packageurl'];  //官包地址
                $data_url=M('agent_game')->where(['app_id'=>$app_id])->order('agent_id')->select();
                foreach ($data_url as $v) {
                   $url[$v['agent_id']]=DOWNSITE.$v['url'];
                }
            }
        }
        $this->assign("url",$url);
        $this->assign("is_ios",$is_ios); //bool
    }
    //根据app_id获得游戏名称
    public function _Getgamename(){
        $app_id=I('app_id',0);
        $name='';
        if($app_id>0){
            $name=M('game')->where(['id'=>$app_id])->getField("name");
            $update_status=M('update_version')->where(['app_id'=>$app_id])->getField('is_update');
        }
        $this->assign('update_status',$update_status);
        $this->assign("game_name",$name);
        
    }

    //刷新分包时间
    public function Updatetime(){
        $app_id=I("app_id");
        $str=''; 
        if(empty($app_id)){
            $this->error('请选择游戏');
        }
        //搜索当前app_id下的渠道包并且是已经出包的进行刷新
        $data=M('agent_game')->where(array('app_id'=>$app_id,'status'=>2))->select();
        $hs_package_obj = new \Huosdk\Package();
        foreach ($data as  $v) {
            $result = $hs_package_obj->pack($v['id']);
            if ($result['error'] == 1) {
                $str.=','.$v['agent_id'];
            }
        }
        if(empty($str)){
            $this->ajaxReturn(array("error" => "0", "msg" => "所有渠道更新成功"));
        }else{
            $this->ajaxReturn(array("error" => "1", "msg" => '渠道id：'.$str.'更新失败！！！'));   
        }
    }

     /**
     * 添加ios更新url
     */
    public function addurl() {
        $appid = I("app_id");
        $update_info =M('update_version')->where(['app_id'=>$appid])->find();
         if ($update_info) {
            $this->assign("games", $update_info);
        } else {
            $this->error("请生成参数对接后,再添加回调");
        }
        $this->assign("games", $update_info);
        $this->_Getgamename();
        $this->display();
    }

     public function addurl_post() {
        $appid = I("app_id");
        $download_url = I("post.download_url", "", "trim");
        if (empty($download_url)) {
            $this->error("请填写回调地址");
        }
        $checkExpressions = '|^http://|';
        $httpsExpressions = '|^https://|';
        if (false == preg_match($checkExpressions, $download_url) && false == preg_match($httpsExpressions, $download_url)) {
            $this->error("请输入正确的回调地址http://或者https://开头");
        }
        $g_data['app_id'] = $appid;
        $g_data['download_url'] = $download_url;
        $rs = M('update_version')->where(array('app_id' => $appid))->save($g_data);
        if ($rs) {              
            $this->success("添加成功！", U("Version/index"));
        } else {
            $this->error("数据写入失败！！！");
        }
    }

    // /**
    //  * 修改游戏回调
    //  */
    public function editurl() {
        $appid = I("app_id");
        $games = M('update_version')->where(['app_id'=>$appid])->find();
        $this->assign("games", $games);
        $this->_Getgamename();
        $this->display();
    }

    // /**
    //  * 修改游戏回调
    //  */
    public function editurl_post() {
        $appid = I("app_id");
        $download_url = I("post.download_url", "", "trim");
        if (empty($download_url)) {
            $this->error("请填写下载地址");
        }
        $checkExpressions = '|^http://|';
        if (false == preg_match($checkExpressions, $download_url)) {
            $this->error("请输入正确的回调地址http://开头");
        }
        $g_data['id'] = $appid;
        $g_data['download_url'] = $download_url;
        $rs =M('update_version')->where(array('app_id' => $appid))->save($g_data);
        if ($rs) {
            $this->success("修改成功！", U("Version/index"));
        } else {
            $this->error("数据写入失败！！！！");
        }
    }
}