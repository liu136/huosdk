<?php
namespace Common\Model;
use Common\Model\CommonModel;
class MessageModel extends CommonModel{
    protected $trueTableName = 'l_message';
    protected $dbName = 'db_league_auth';
    
	public function getMessagecnt($where){
		$cnt = $this -> where($where)->count();
		return $cnt;
	}
	
	public function getMessagelist($where, $limit){
		$info = $this-> where($where)
					-> field('id,title,type,status,createtime') 
					-> limit($limit)
					-> order('createtime desc')
					-> select();
		return $info;
	}
	
	public function addData($data){
		if(empty($data)){
			return -1;
		}
		
		$rs = $this -> add($data);
		return $rs;
	}
	
	public function getMessageinfo($where){
		$info = $this-> where($where)-> find();
		return $info;
	}
	
	public function saveData( $data){
		if(empty($data)){
			return -1;
		}
		
		$rs = $this -> save($data);
		
		if($rs){
			return 1;
		}else{
			return 0;
		}
	}
}
