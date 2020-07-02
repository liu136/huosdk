<?php
namespace Common\Model;
use Common\Model\CommonModel;
class ClientemailModel extends CommonModel{
    protected $trueTableName = 'l_clientemail';
    protected $dbName = 'db_league_mng';

	public function getClientemail($where, $limit){
		$info = M() -> table(C('MNG_DB_NAME').'.'.C('LDB_PREFIX').'_clientemail ce')
					-> where($where)
					-> join("left join ".C('MNG_DB_NAME').'.'.C('LDB_PREFIX')."client c on c.id=ce.cid")
					-> field("ce.id as sid, ce.*, c.nickname, c.clientname")
					-> limit($limit)
					-> select();
		return $info;
	}

	/*
	 * 根据条件获取数量
	 */
	public function getClientemailcnt($where){
		$cnt = $this -> where($where)->count();
		return $cnt;
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