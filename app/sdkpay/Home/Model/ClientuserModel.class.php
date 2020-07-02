<?php
namespace Common\Model;
use Common\Model\CommonModel;
class ClientuserModel extends CommonModel{
    protected $trueTableName = 'l_clientusers';
    protected $dbName = 'db_league_auth';
	    
	public function getClientuser($where, $limit){
		$info = $this -> where($where) -> limit($limit) -> select();
		return $info;
	}

	/*
	 * 根据条件获取数量
	 */
	public function getClientusercnt($where){
		$cnt = $this -> where($where)->count();
		return $cnt;
	}

	public function getUsername($where=NULL){
		$info = $this -> field('id, email') -> select();
		return $info;
	}
		

	public function saveData($data){
		if(empty($data)){
			return -1;
		}
	
		$rs = $this ->save($data);
		if($rs != false){
			return 1;
		}else{
			return 0;
		}
	}
	
	public function addData($data){
		if(empty($data)){
			return -1;
		}
				
		$rs = $this -> add($data);
		return $rs;
	}
}