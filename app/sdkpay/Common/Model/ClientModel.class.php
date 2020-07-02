<?php
namespace Common\Model;
use Common\Model\CommonModel;
class ClientModel extends CommonModel{
    protected $trueTableName = 'l_client';
    protected $dbName = 'db_league_mng';
    
	public function getClientcnt($where){
		$cnt = $this -> where($where)->count();
		return $cnt;
	}
	
	public function getClientinfo($where, $limit){
		$info = $this->alias('a')
					-> where($where)
					-> join("left join ".C('MNG_DB_NAME').".l_client_ext g on a.id=g.cid")
					-> field('id, clientname, clientkey,nickname,companyname, addr,url, down_url,linkman, link_email,link_tel, clevel,adminurl, qq, tel, wechat, email, create_time, regcnt, paycnt, totalmoney, a.payway, status,create_status,salesman,saleid') 
					-> limit($limit)
					-> order('id desc')
					-> select();
		return $info;
	}
	
	public function getSingleClient($cid){
		$where = 'id = '.$cid;
		$info = $this -> where($where) -> find();
		return $info;
	}
	
	public function getClientname($where=NULL){
		$info = $this -> field('id, clientname') -> select();
		return $info;
	}
	
	public function getNickname($where=NULL){
	    if($where) $where = " and $where";
		$info = $this -> field('id, nickname') -> where('create_status = 1 '.$where)-> select();
		return $info;
	}

	public function getClientpayinfo($where, $limit){
		$info = $this -> field('id,  id as did, create_time, clientname, nickname, paystatus, registerstatus')
					  -> where($where)
					  -> order("create_time desc")
					  -> limit($limit)
		              -> select();
		return $info;
	}
	
	public function getPayinfo($where, $limit){
		$info = $this -> field('id, create_time, clientname, nickname, paystatus, payway,id as cid')
					  -> where($where)
					  -> order("create_time desc")
					  -> limit($limit)
					  -> select();
		return $info;
	}
	
	public function getPayway($cid){
		$where = 'id = '.$cid;
		$info = $this -> field('id, payway')
					  -> where($where)
					  -> select();
		return $info[0];
	}
	
	/*
	 * 获取平台币信息  编号 1
	 */
	public function getPtbinfo($cid){
		$payModel = M('ptb');
		$where    = 'cid='.$cid;
		$payinfo = $payModel ->  where($where) -> select();
		return $payinfo[0];
	}

	/*
	 * 获取支付宝信息  编号 2
	 */
	public function getAlipayinfo($cid){
		$payModel = M(C('MNG_DB_NAME').'.alipay', C('LDB_PREFIX'));
		$where    = 'cid='.$cid;
		$payinfo = $payModel ->  where($where) -> select();
		return $payinfo[0];
	}
	
	/*
	 * 获取财付通信息  编号3
	 */
	public function getTenpayinfo($cid){
		$payModel = M('tenpay');
		$where    = 'cid='.$cid;
		$payinfo  = $payModel ->  where($where) -> select();
		return $payinfo[0];
	}

	/*
	 * 获取微信支付信息  编号4
	 */
	public function getWxpayinfo($cid){
		$payModel = M('wxpay');
		$where    = 'cid='.$cid;
		$payinfo  = $payModel ->  where($where) -> select();
		return $payinfo[0];
	}

	/*
	 * 获取短代支付 编号5
	 */
	public function getSmsinfo($cid){
		$payModel = M('sms');
		$where    = 'cid='.$cid;
		$payinfo  = $payModel ->  where($where) -> select();
		return $payinfo[0];
	}

	/*
	 * 获取易宝信息 编号 6
	 */
	public function getYeepayinfo($cid){
		$payModel = M('yeepay');
		$where    = 'cid='.$cid;
		$payinfo  = $payModel ->  where($where) -> select();
		return $payinfo[0];
	}
		
	/*
	 * 获取易联信息 编号 7
	 */
	public function getPayecoinfo($cid){
		$payModel = M(C('MNG_DB_NAME').'.payeco', C('LDB_PREFIX'));
		$where    = 'cid='.$cid;
		$payinfo  = $payModel ->  where($where) -> select();
		return $payinfo[0];
	}

	/*
	 * 获取TCL银联支付编号 8
	 */
	public function getTclpayinfo($cid){
		$payModel = M('tclpay');
		$where    = 'cid='.$cid;
		$payinfo  = $payModel ->  where($where) -> select();
		return $payinfo[0];
	}

	/*
	 * 获取易联信息 编号 9
	 */
	public function getNowpayinfo($cid){
		$payModel = M(C('MNG_DB_NAME').'.nowpay', C('LDB_PREFIX'));
		$where    = 'cid='.$cid;
		$payinfo  = $payModel ->  where($where) -> select();
		return $payinfo[0];
	}

	
	public function getClientpaycom($where){
		$payModel = M(C('MNG_DB_NAME').'.payway', C('LDB_PREFIX'));
		$field = "id,disc";
		$payinfo = $payModel ->  where($where) -> field($field) -> select();
		return $payinfo;
	}
	
	public function getSuminfo(){
		$sumModel = M(C('MNG_DB_NAME').'.client_ext', 'MNG_DB_NAME');
		$field = "sum(regcnt) sumregcnt, sum(paycnt) sumpaycnt, sum(totalmoney) summoney";
		$suminfo = $sumModel -> field($field) -> select();
				
		return $suminfo[0];
	}


	/*
	 * 获取后台邮箱信息
	 */
	public function getEmailinfo($cid){
		$emailModel = M(C('MNG_DB_NAME').'.clientemail', C('LDB_PREFIX'));
		$where    = 'cid='.$cid;
		$emailinfo  = $emailModel ->  where($where) -> select();
		return $emailinfo[0];
	}
	
	public function savaData($data){
		if(empty($data)){
			return false;
		}
			
		$rs = $this -> save($data);
		
		if($rs != false){
			return true;
		}else{
			return false;
		}
	}
	
	public function addData($data){
		if(empty($data)){
			return 0;
		}
	
		$rs = $this -> add($data);
	
		if($rs){
			return $rs;
		}else{
			return 0;
		}
	}
	
	public function getPayname($id){
		$payModel = M(C('MNG_DB_NAME').'.payway', C('LDB_PREFIX'));
		$where = 'id='.$id;
		$field = "id, name, disc";
		$payinfo = $payModel ->  where($where) -> field($field) -> select();
		return $payinfo[0];
	}
}
