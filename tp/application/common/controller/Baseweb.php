<?php
/**
 * Baseweb .php UTF-8
 * h5 接口入口
 *
 * @date    : 2017/11/8 18:20
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : wuyonghong <wyh@huosdk.com>
 * @version : HUOSDK 8.0
 */

namespace app\common\controller;

use think\Db;
use huosdk\common\HuoSession;
use think\Cache;
use think\Config;


class Baseweb extends Base {
    protected $apple_id;
    protected $se_class;

    protected function _initialize() {
        parent::_initialize();
        $this->se_class = new HuoSession();
        $this->getParam();
        //$this->verifyParam();
        $this->row = 10;
    }

    /**
     * 加密字符串
     * @param string $data 字符串
     * @param string $key 加密key
     * @param string $iv 加密向量
     * @return string
     */
    function encrypt($data, $key, $iv)
    {
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
        return base64_encode($encrypted);
    }

    /**
     * 解密字符串
     * @param string $data 字符串
     * @param string $key 加密key
     * @param string $iv 加密向量
     * @return object
     */
    function decrypt($data)
    {
        $data = str_replace(' ','+', $data);
        $h5_data=  openssl_decrypt( $data  ,"aes-128-cbc", Config::get('domain.H5_DESRYPT_KEY'),OPENSSL_ZERO_PADDING, Config::get('domain.H5_IV')) ;
        return json_decode(base64_decode( $h5_data), true);
    }



    public function getParam() {
        
        $_url_param = $this->request->param();


        if( isset($_url_param['h5_data'])){
             $_rq_data = $this->decrypt($_url_param['h5_data']);
        }else{
            $_params = $this->request->getContent();
            $_rq_data = json_decode($_params, true);
        }
        

        
        $_device_id = isset($_rq_data["dd"]) ? $_rq_data["dd"] : (isset($_rq_data["device_id"])?$_rq_data["device_id"]:"");
        $_device_info = isset($_rq_data["df"]) ? $_rq_data["df"] : (isset($_rq_data["device_info"])?$_rq_data["device_info"]:"");
        $_apple_id = isset($_rq_data["apple_id"]) ? $_rq_data["apple_id"] : 0;
        $_agentgame = isset($_rq_data["ag"]) ? $_rq_data["ag"] : (isset($_rq_data["agentgame"])?$_rq_data["agentgame"]:"");
        $_timestamp = isset($_rq_data["tt"]) ? $_rq_data["tt"] : (isset($_rq_data["timestamp"])?$_rq_data["timestamp"]:"");
        $_userua = isset($_rq_data["ua"]) ? $_rq_data["ua"] : (isset($_rq_data["userua"])?$_rq_data["userua"]:"");
        $_user_token = isset($_rq_data["ut"]) ? $_rq_data["ut"] :(isset($_rq_data["user_token"])?$_rq_data["user_token"]:"");
        $_username = isset($_rq_data["uname"]) ? $_rq_data["uname"] : (isset($_rq_data["userua"])?$_rq_data["userua"]:"");
        $_password = isset($_rq_data["pwd"]) ? $_rq_data["pwd"] : (isset($_rq_data["userua"])?$_rq_data["userua"]:"");
        $_sign = isset($_rq_data["sign"]) ? $_rq_data["sign"] : "";
        $_mobile = isset($_rq_data["mb"]) ? $_rq_data["mb"] :(isset($_rq_data["mobile"])?$_rq_data["mobile"]:"");
        $_mobile_old = isset($_rq_data["mbd"]) ? $_rq_data["mbd"] :(isset($_rq_data["mobile_old"])?$_rq_data["mobile_old"]:"");
        $_sms_type = isset($_rq_data["st"]) ? $_rq_data["st"] : (isset($_rq_data["sms_type"])?$_rq_data["sms_type"]:"");
        $_sms_code = isset($_rq_data["sc"]) ? $_rq_data["sc"] : (isset($_rq_data["sms_code"])?$_rq_data["sms_code"]:"");
        $_roleinfo = array();
        $_roleinfo['role_type'] = isset($_rq_data["ri"]["rt"]) ? $_rq_data["ri"]["rt"] : (isset($_rq_data['roleinfo']["role_type"])?$_rq_data['roleinfo']["role_type"]:"");
        $_roleinfo['server_id'] = isset($_rq_data["ri"]["sd"]) ? $_rq_data["ri"]["sd"] : (isset($_rq_data['roleinfo']["server_id"])?$_rq_data['roleinfo']["server_id"]:"");
        $_roleinfo['server_name'] = isset($_rq_data["ri"]["sn"]) ? $_rq_data["ri"]["sn"] : (isset($_rq_data['roleinfo']["server_name"])?$_rq_data['roleinfo']["server_name"]:"");
        $_roleinfo['role_id'] = isset($_rq_data["ri"]["rd"]) ? $_rq_data["ri"]["rd"] : (isset($_rq_data['roleinfo']["role_id"])?$_rq_data['roleinfo']["role_id"]:"");
        $_roleinfo['role_name'] = isset($_rq_data["ri"]["rn"]) ? $_rq_data["ri"]["rn"] : (isset($_rq_data['roleinfo']["role_name"])?$_rq_data['roleinfo']["role_name"]:"");
        $_roleinfo['party_name'] = isset($_rq_data["ri"]["pn"]) ? $_rq_data["ri"]["pn"] : (isset($_rq_data['roleinfo']["party_name"])?$_rq_data['roleinfo']["party_name"]:"");
        $_roleinfo['role_level'] = isset($_rq_data["ri"]["rl"]) ? $_rq_data["ri"]["rl"] : (isset($_rq_data['roleinfo']["role_level"])?$_rq_data['roleinfo']["role_level"]:"");
        $_roleinfo['role_vip'] = isset($_rq_data["ri"]["rv"]) ? $_rq_data["ri"]["rv"] : (isset($_rq_data['roleinfo']["role_vip"])?$_rq_data['roleinfo']["role_vip"]:"");
        $_roleinfo['role_balence'] = isset($_rq_data["ri"]["rb"]) ? $_rq_data["ri"]["rb"] : (isset($_rq_data['roleinfo']["role_balence"])?$_rq_data['roleinfo']["role_balence"]:"");
        $_roleinfo['rolelevel_ctime'] = isset($_rq_data["ri"]["rct"]) ? $_rq_data["ri"]["rct"] : (isset($_rq_data['roleinfo']["rolelevel_ctime"])?$_rq_data['roleinfo']["rolelevel_ctime"]:"");
        $_roleinfo['rolelevel_mtime'] = isset($_rq_data["ri"]["rmt"]) ? $_rq_data["ri"]["rmt"] : (isset($_rq_data['roleinfo']["rolelevel_mtime"])?$_rq_data['roleinfo']["rolelevel_mtime"]:"");
        $_openid = isset($_rq_data["oid"]) ? $_rq_data["oid"] : (isset($_rq_data['openid'])?$_rq_data['openid']:"");
        $_access_token = isset($_rq_data["acst"]) ? $_rq_data["acst"] : (isset($_rq_data['access_token'])?$_rq_data['access_token']:"");

        /*订单信息*/
        $_order_info = array();
        $_order_info['cp_order_id'] = isset($_rq_data["oi"]["coi"]) ? $_rq_data["oi"]["coi"] :  (isset($_rq_data['orderinfo']['cp_order_id'])?$_rq_data['orderinfo']['cp_order_id']:"");
        $_order_info['product_price'] = isset($_rq_data["oi"]["pp"]) ? $_rq_data["oi"]["pp"] :  (isset($_rq_data['orderinfo']['product_price'])?$_rq_data['orderinfo']['product_price']:0);
        $_order_info['product_count'] = isset($_rq_data["oi"]["pc"]) ? $_rq_data["oi"]["pc"] : (isset($_rq_data['orderinfo']['product_count'])?$_rq_data['orderinfo']['product_count']:1);
        $_order_info['product_id'] = isset($_rq_data["oi"]["pi"]) ? $_rq_data["oi"]["pi"] : (isset($_rq_data['orderinfo']['product_id'])?$_rq_data['orderinfo']['product_id']:"");
        $_order_info['product_name'] = isset($_rq_data["oi"]["pn"]) ? $_rq_data["oi"]["pn"] : (isset($_rq_data['orderinfo']['product_name'])?$_rq_data['orderinfo']['product_name']:"");
        $_order_info['product_desc'] = isset($_rq_data["oi"]["pd"]) ? $_rq_data["oi"]["pd"] : (isset($_rq_data['orderinfo']['product_desc'])?$_rq_data['orderinfo']['product_desc']:"");
        $_order_info['exchange_rate'] = isset($_rq_data["oi"]["rt"]) ? $_rq_data["oi"]["rt"] : (isset($_rq_data['orderinfo']['exchange_rate'])?$_rq_data['orderinfo']['exchange_rate']:0);
        $_order_info['currency_name'] = isset($_rq_data["oi"]["crn"]) ? $_rq_data["oi"]["crn"] : (isset($_rq_data['orderinfo']['currency_name'])?$_rq_data['orderinfo']['currency_name']:"");
        $_order_info['ext'] = isset($_rq_data["oi"]["ext"]) ? $_rq_data["oi"]["ext"] : (isset($_rq_data['orderinfo']['ext'])?$_rq_data['orderinfo']['ext']:"");
        $_order_id = isset($_rq_data["oi"]) ? $_rq_data["oi"] : (isset($_rq_data['order_id'])?$_rq_data['order_id']:"");
        $_trans_id = isset($_rq_data["ti"]) ? $_rq_data["ti"] : (isset($_rq_data['trans_id '])?$_rq_data['trans_id ']:"");
        $_is_sandbox = isset($_rq_data["sb"]) ? $_rq_data["sb"] : (isset($_rq_data['is_sandbox'])?$_rq_data['is_sandbox']:"");
        $_appverifystr = isset($_rq_data["apf"]) ? $_rq_data["apf"] : (isset($_rq_data['appverifystr'])?$_rq_data['appverifystr']:"");
        $_pay_token = isset($_rq_data["pt"]) ? $_rq_data["pt"] : (isset($_rq_data['pay_token'])?$_rq_data['pay_token']:"");

        $_Devicetype = isset($_rq_data["df"]) ? $_rq_data["df"] : (isset($_rq_data['device_type'])?$_rq_data['device_type']:"");

        $_acd = isset($_rq_data["acd"]) ? $_rq_data["acd"] : "";
        $_aid = isset($_rq_data["aid"]) ? $_rq_data["aid"] : "";
        $_akd = isset($_rq_data["akd"]) ? $_rq_data["akd"] : "";
        $_isRmSession = isset($_rq_data["rs"]) ? $_rq_data["rs"] : "0";
        $_deviceType =  isset($_rq_data['deviceType'])?$_rq_data['deviceType']:"" ;

        $_userfrom = 1;
        if(isset($_rq_data["app_id"])){
            /*根据apple_id获取游戏信息*/
            $_game_where["game_id"] = $_rq_data["app_id"];
        }else{
             /*根据apple_id获取游戏信息*/
            $_game_where["apple_id"] = $_apple_id;
        }
       

        $_game_data = Db::name("game")->where($_game_where)->find();
        $_ip = request()->ip();
        if (empty($_game_data)) {
            return false;
        }
        $_client_id = Db::name("game_client")->where(array("app_id" => $_game_data["id"]))->value("id");
        $_device = array(
            "device_id"   => $_device_id,
            "deviceinfo" => $_device_info,
            "userua"      => $_userua,
        );
        $app_id=isset($_rq_data["app_id"])?$_rq_data["app_id"]:$_game_data["id"];
        /*请求参数映射表*/
        $this->rq_data = array(
            "acd"             => $_acd, 
            "aid"             => $_aid,
            "deviceType"      => $_deviceType, 
            "isRmSession"     =>$_isRmSession, //清理session
            "akd"             => $_akd, 
            "device"          => $_device,      //设备信息
            "from"            => 4,               //来源
            "app_id"          => $_game_data["id"], //游戏id
            "client_id"       => $_client_id,       //client_id
            "ip"              => $_ip,              //请求ip
            "open_cnt"        => 1,
            "apple_id"        => $_apple_id,    //苹果id
            "deviceType"     =>$_Devicetype,
            "agentgame"       => $_agentgame,          //玩家所属渠道agentgame 默认为’’
            "timestamp"       => $_timestamp,          //客户端时间戳 timestamp
            "device_id"       => $_device_id,          //玩家设备ID IOS为idfa
            "userua"          => $_userua,          //玩家设备UA
            "deviceinfo"     => $_device_info,          //玩家设备信息 包括手机号码,用户系统版本,双竖线隔开
            "sign"            => $_sign,
            "user_token"      => $_user_token,          //链接的TOKEN
            "username"        => $_username,       //用户名，注册用户名
            "password"        => $_password,         //密码，注册密码
            "mobile"          => $_mobile,         //玩家注册手机号
            "mobile_old"      => $_mobile_old,      //玩家注册原手机号
            "smstype"         => $_sms_type,         //短信类型 1 注册 2 登陆 3 修改密码 4 信息变更
            "smscode"         => $_sms_code,         //短信校验码
            "roleinfo"        => $_roleinfo,         //角色信息
            "role_type"       => $_roleinfo['role_type'],
            "server_id"       => $_roleinfo['server_id'],
            "server_name"     => $_roleinfo['server_name'],
            "role_id"         => $_roleinfo['role_id'],
            "role_name"       => $_roleinfo['role_name'],
            "party_name"      => $_roleinfo['party_name'],
            "role_level"      => $_roleinfo['role_level'],
            "role_vip"        => $_roleinfo['role_vip'],
            "role_balence"    => $_roleinfo['role_balence'],
            "rolelevel_ctime" => $_roleinfo['rolelevel_ctime'],
            "rolelevel_mtime" => $_roleinfo['rolelevel_mtime'],
            "openid"          => $_openid,               //第三方openid
            "access_token"    => $_access_token,         //第三方登陆token
            "access_token"    => $_access_token,         //第三方登陆token
            "userfrom"        => $_userfrom,         //第三方登陆token
            "cp_order_id"     => $_order_info['cp_order_id'],
            //游戏传入的外部订单号。服务器会根据这个订单号生成对应的平台订单号，请保证每笔订单传入的订单号的唯一性。
            "product_price"   => $_order_info['product_price'],         //商品价格(元);建议传入整数,可以保留两位小数
            "product_count"   => $_order_info['product_count'],         //商品数量(除非游戏需要支持一次购买多份商品),默认为1
            "product_id"      => $_order_info['product_id'],         //商品ID
            "product_name"    => $_order_info['product_name'],         //商品名称
            "product_desc"    => $_order_info['product_desc'],         //商品描述(不传则使用PRODUCT_NAME) 默认为OI.PRODUCT_NAME
            "exchange_rate"   => $_order_info['exchange_rate'],         //虚拟币兑换比例（例如100，表示1元购买100虚拟币） 默认为0
            "currency_name"   => $_order_info['currency_name'],         //虚拟币名称（如金币、元宝）默认为”” 	CURRENCY_NAME
            "ext"             => $_order_info['ext'],         //CP自定义扩展字段 透传信息 默认为”” 	EXT
            "orderinfo"       => $_order_info,                //订单信息
            "order_id"        => $_order_id,                //订单信息
            "trans_id"        => $_trans_id,                //订单信息
            "is_sandbox"      => $_is_sandbox,                //订单信息
            "appverifystr"    => $_appverifystr,                //订单信息
            "paytoken"       => $_pay_token,                //订单信息
        );
    

      $this->se_class->setSession($this->rq_data);
      \think\Session::set('apple_id', $_apple_id, "app");
      return $this->rq_data;
    }


    //保存登陆数据。主要用于缓存登陆
    public function SaveLoginData($data,$appid){
        \think\Session::set( $appid.'_Oauth_login', $data, 'Oauth_login');
    }

    //获取缓存登陆数据
    public function getLoginData($appid,$is_clear=false){
        $data = \think\Session::get($appid.'_Oauth_login','Oauth_login' );
        if($is_clear && $data['loginType'] == "qq"){
             \think\Session::delete($appid.'_Oauth_login','Oauth_login' );//清理 qq登陆缓存
        } 
        return  $data;
    }



    /**
     * 返回信息处理
     *
     * @param int    $check      是否切换用户    0 不更换 1 重新初始化  
     * @param string $user_token 玩家此次连接token
     * @param string $agent_game 所属渠道    玩家游戏渠道编号
     * @param string $type       回调数据类型
     *
     * @return $this
     */
    public function initReturn($user_token = '', $agent_game = '',$callback='init',$game_url='',$app_id='') {
        $uid=$this->getLoginData($app_id);
        $check=(!empty($uid))?"1":"0";
        $_rdata['change'] = $check;     //是否自动登录
        $_rdata['ut'] = $user_token;
        $_rdata['agent_game'] = $agent_game;
        $_rdata['app_id'] = $app_id;
        $_rdata['game_url'] = $game_url;
        $_rdata['callback_type'] =$callback;

        return hs_api_responce(200, 'success', $_rdata);
    }

    public function initH5Pay($_pay_class ,$_payway){
            if($_payway == "wxpay"){
                //$_payinfo = $_pay_class->clientPay(true,'gotoh5weixin') ;
               $_payinfo = $_pay_class->mobilePay("","gotoh5weixin");
            }else{
                $_payinfo = $_pay_class->mobilePay();
            }
            return $_payinfo;
    }

    /**
     * 玩家普通注册信息返回
     *
     * @param $mem_id          原mem_id    用户在平台的用户ID
     * @param $cp_user_token   原cp_user_token    CP用user_token
     * @param $agent_game      原agent_game    渠道游戏标识
     * @param $float_is_show   浮点是否显示 float_is_show    1 不显示 2 显示
     *
     * @return $this
     */
    public function rgReturn($mem_id = 0, $cp_user_token = '', $agent_game = '', $float_is_show = 1,$callback='') {
        $_rdata['mid'] = $mem_id;     //是否切换用户
        $_rdata['cptoken'] = $cp_user_token;
        $_rdata['ag'] = $agent_game;
        $_rdata['sh'] = $float_is_show;
         $_rdata['callback_type'] =$callback;
        return hs_api_responce(200, 'success', $_rdata);
    }

 /**
     * 玩家web 登陆信息返回
     *
     * @param $mem_id          原mem_id    用户在平台的用户ID
     * @param $cp_user_token   原cp_user_token    CP用user_token
     *
     * @return $this
     */
    public function LgReturn($mem_id = 0, $cp_user_token = '', $app_id = '',$callback='',$deviceType='',$LoginType='account',$userlist='') {
        $_map['app_id'] = $app_id ;
        $_game['id']= $app_id ;
        $_client_key = DB::name('game_client')->where($_map)->value('client_key');
        $game_url = DB::name('game')->where($_game)->value('gameurl');
        $time=time();
        $sign=md5(md5($mem_id.$deviceType.$app_id. $time). $_client_key);
        $_rdata['userid'] = $mem_id; 
        $_rdata['deviceType'] = $deviceType;   
        $_rdata['appid'] = $app_id;      
        $_rdata['cptoken'] = $cp_user_token;
        $_rdata['time'] = $time;
        $_rdata['callback_type'] =$callback;
        $_rdata['game_url'] =$game_url;
        $_rdata['LoginType'] =$LoginType;
        $_rdata['sign'] = $sign;
        $_rdata['userlist'] = $userlist;
        return hs_api_responce(200, 'success', $_rdata);
    }


    /**
     * 支付返回函数
     *
     * @param string $order_id    平台订单号
     * @param int    $pay_switch  是否切换支付 pay_switch    1 不切换 2 切换支付
     * @param string $pay_token   pay_token    支付token
     * @param string $class_name  class_name    要调用的类名
     * @param string $method_name method_name    要调用的类名
    * @param string $method_name Instace_name    要调用的类名
     * @param string $method_name str    混淆的字符
     *
     * @return $this
     */
    public function payReturn($order_id = '', $pay_switch = 1, $pay_token = '', $class_name = '', $method_name = '', $Instace_name = '',$str='') {
        $_rdata[$str.'oi'] = base64_encode($order_id);     //是否切换用户
        $_rdata[$str.'cc'] = 3;    // 	1 不切换 2 切换支付
        $_rdata[$str.'pt'] = $pay_token;    //支付token
        $_rdata[$str.'cn'] = base64_encode($class_name);    //要调用的类名
        $_rdata[$str.'mn'] = base64_encode($method_name);    //要调用的方法名
        $_rdata[$str.'in'] = base64_encode($Instace_name);    //要调用的方法名
        return hs_api_responce(200, '预下单成功', $_rdata);
    }

    /**
     * 验单信息返回
     * @param string $order_id
     * @param int    $status
     * @param int    $cpstatus
     *
     * @return $this
     */
    public function qoReturn($order_id = '', $status = 1, $cpstatus = 1) {
        $_rdata['oi'] = $order_id;     //平台订单号
        $_rdata['st'] = $status;       //玩家支付状态 1 待支付 2 支付成功 3 支付失败
        $_rdata['cst'] = $cpstatus;    //原cpstatus 	通知游戏状态 1 待通知 2 通知成功 3 通知失败
        return hs_api_responce(200, '查询成功', $_rdata);
    }

    protected function getAgentgame($agentname) {
        if (!empty($agentname) || 'default' != $agentname) {
            return $agentname;
        }
        /* 若第一次打开，且agentgame为空或default, 通过设备确认agent_id */
        $_opent_cnt = Session::get('open_cnt', 'device');
        if (1 == $_opent_cnt) {
            /* 获取渠道 */
            $_dl_class = new \huosdk\log\Devicelog();
            $_device_id = Session::get('device_id', 'device');
            $_app_id = Session::get('app_id', 'device');
            return $_dl_class->getAgentgame($_device_id, $_app_id);
        }
    }

    function checkMobileFormat($data) {
        $checkExpressions = "/^1[34578]\d{9}$/";
        if (false == preg_match($checkExpressions, $data)) {
            return false;
        }

        return true;
    }
}