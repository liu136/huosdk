<?php

/**
 * 充值统计页面
 * 
 * @author
 *
 */
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class ChargeController extends AdminbaseController {
    
    protected $daypaymodel,$where,$orderwhere;
    
    function _initialize() {
        parent::_initialize();
       
    }
 
    /*
     * 首充发放
     */
	public function firstcharge() {
        $this -> _getGames();
        $this->display();
    } 
    
	/*
     * 首充发放
     */
	public function firstcharge_post() {
        if (IS_POST) {
            $gid = I("gid");
			$data['username'] = I("username");
			$data['service'] = I("service");
			$data['role'] = I("role");
			$data['money'] = I("amount");
			$data['paytime'] = time();
             
			$cid = sp_get_current_cid();
			list($usec, $sec) = explode(" ", microtime());
            // 取微秒前3位+再两位随机数+渠道ID后四位
            $data['orderid'] = $sec . substr($usec, 2, 3) . rand(10, 99) . sprintf("%04d", $cid % 10000);

			if(empty($gid) || empty($data['service']) || empty($data['role']) || empty($data['username'])){
                $this->error('请填写完整信息');
				exit;
			}
            
			$is_number = '/^[0-9]*[1-9][0-9]*$/';
			
			if(!preg_match($is_number, $data['amount']) || $data['amount'] <= 0 || $data['amount'] > 10){
			   	$this->error('金额必须为整数,且不能大于10');
				exit;
			}
			$user = M($this->dbname.".members", C('CDB_PREFIX'))->where(array('username'=>$data['username']))->find();

			if(empty($user)){
               $this->error('账号不存在');
			   exit;
			}
            
			$url_model = M(C('MNG_DB_NAME').".gamepayurl", C('LDB_PREFIX'));
			$where['gid'] = $gid;
			$url_data = $url_model->where($where)->find();
            
			if(empty($url_data)){
                $this->error('没有配置直充地址');
				exit;
			}

			$gamemodel = M(C('MNG_DB_NAME').".game", C('LDB_PREFIX'));
			$gdata = $gamemodel->where(array('id'=>$gid))->find();
            
			if(empty($gdata)){
                $this->error('游戏不存在');
				exit;
			}
            
			$str = 'username='.$data['username'].'&server='.$data['service'].'&role='.$data['role'].'&money'.$data['money'].'&paytime='.$data['paytime'].'&orderid='.$data['orderid'].'&appkey='.$gdata['appkey'];
			$data['sign'] = md5(urlencode($str));
            
			$paydata['params'] = json_encode($data);
			$paydata['orderid'] = $data['orderid'];
			$paydata['appid'] = $gid;
			$paydata['cid'] = $cid;
			$paydata['userid'] = $user['id'];
			$paydata['lm_username'] = $cid."_".$data['username'];
			$paydata['amount'] = $data['money'];
			$paydata['role'] = $data['role'];
			$paydata['server'] = $data['server'];
			$paydata['status'] = 0;
			$paydata['agentgame'] = '';
			$paydata['create_time'] = $data['paytime'];
           
			$pay_model = M(C('DATA_DB_NAME').".firstpay", C('LDB_PREFIX'));
			$rs = $pay_model->add($paydata);
			unset($paydata);
            if($rs){
                $content = $this->http_post_data($url_data['payurl'], json_encode($data));
				$cdata = json_decode($content);
                 
				if($cdata['code'] == 1){
					 $pdata['status'] = '1';
					 $pdata['remark'] = $cdata['data']['orderid'];
					 
                     $rs = $pay_model->where(array('id'=>$rs))->save($pdata);

                     $this->success('充值成功');
				}else{
					 $pdata['status'] = '-1';
					 $pdata['remark'] = $cdata['msg'];

                     $rs = $pay_model->where(array('id'=>$rs))->save($pdata);
                     $this->error('充值失败,msg:'.$cdata['msg']);
				}
			   
                exit;
			}
			
            $this->error('充值失败');
			exit;
		}
        
    } 


	function _getGames(){
    	$cates=array(
    	        0=>"请选择游戏"
    	);
	    $where['ratestatus'] = 3;
        $games = M($this->dbname.".game", C('CDB_PREFIX'))->where($where)->getField("appid,gamename", true);
        if($games){
            $cates = $cates + $games;
        }
        
        $this->assign("games",$cates);
	}


	function getServer(){
		$appid = I("post.appid");

		$list1[0]['id'] = 0;
		$list1[0]['service'] = '请选择区服'; 
        
		$where['appid'] = $appid;
		$list2 = M($this->dbname.".mgameinfo", C('CDB_PREFIX'))->where($where)->field("id,service")->group('service')->select();
		
		$list = array_merge($list1,$list2);
		echo json_encode($list);
	}

	function getRole(){
		$appid = I("post.appid");
		$server = I("post.server");

    	$list1[0]['id'] = 0;
		$list1[0]['role'] = '请选择角色'; 
        
		$where['appid'] = $appid;
		$where['service'] = $server;
		$list2 = M($this->dbname.".mgameinfo", C('CDB_PREFIX'))->where($where)->field("id,role")->select();
		
		$list = array_merge($list1,$list2);
		echo json_encode($list);
	}
   
}