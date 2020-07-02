<?php
namespace Common\Model;
use Common\Model\CommonModel;
class PayModel extends CommonModel{
    protected $trueTableName = 'l_payway';
    protected $dbName = 'db_league_mng';
	
	public function getPayId($payname){
		if(empty($payname)){
			return NULL;
		}
		
		$where = 'payname='.$payname;
		$info = $this -> field('id')
					  -> where($where)
					  -> select();
		return $info[0]['id'];
	}

	public function getPayWay($where){
		$info = $this -> where($where) -> select();
		return $info;
	}
	
	public function savePaydata($table, $data){
		if(empty($data)){
			return -1;
		}
		
		$paymodel = M(C('MNG_DB_NAME').".".$table,C('LDB_PREFIX'));
		$rs = $paymodel -> save($data);
		
		if($rs){
			return 1;
		}else{
			return 0;
		}
	}
	
	public function addPaydata($table, $data){
		if(empty($data)){
			return -1;
		}
	
		$paymodel = M(C('MNG_DB_NAME').".".$table,C('LDB_PREFIX'));
		$rs = $paymodel -> add($data);
		return $rs;
	}
	
	public function checkPayway($table,$where){
		$paymodel = M(C('MNG_DB_NAME').".".$table,C('LDB_PREFIX'));
		$cnt = $paymodel -> where($where) ->select();
		return $cnt;
	}

	public function checkHistory($table,$cid){
		
		$paymodel = M(C('MNG_DB_NAME').".".$table,C('LDB_PREFIX'));

		$sql = "select * from ".C('MNG_DB_NAME').".l_".$table." a WHERE 
				NOT EXISTS (SELECT 1 FROM ".C('MNG_DB_NAME').".l_".$table." b WHERE a.cid=b.cid AND a.create_time<b.create_time AND b.cid=%d) AND a.cid=%d";
		
		$info = $paymodel->query($sql,$cid,$cid);

		return $info;
	}
	
	public function addPayhistory($table, $data){
		if(empty($data)){
			return -1;
		}
	
		$paymodel = M(C('MNG_DB_NAME').".".$table,C('LDB_PREFIX'));
		$rs = $paymodel -> add($data);
		return $rs;
	}

	public function getPaywaydesc($where){
		$payinfo = $this ->  where($where) -> select();
		return $payinfo;
	}
}
?>