<?php
namespace Common\Model;
use Common\Model\CommonModel;
class MessageModel extends CommonModel{
    protected $trueTableName = 'l_message';
    protected $dbName = 'db_league_auth';
    
	public function getMessagecnt($where){
		$cnt = $this->alias('a')
					-> where($where)
					-> join("left join ".C('AUTH_DB_NAME').".l_message_read r on a.id=r.mid")
					->count();
		return $cnt;
	}
	
	public function getMessagetime($where){
		$cnt = $this->alias('a')
					-> where($where)
					-> join("left join ".C('AUTH_DB_NAME').".l_message_read r on a.id=r.mid")
					-> order('r.status asc ,a.createtime desc')
					->find();
		return $cnt;
	}
	
	public function getMessagelist($where, $limit){
		$info = $this->alias('a')
					-> where($where)
					-> join("left join ".C('AUTH_DB_NAME').".l_message_read r on a.id=r.mid")
					-> field('a.title,a.type,a.createtime,r.log_id as id,r.status') 
					-> limit($limit)
					-> order('r.status asc ,a.createtime desc')
					-> select();
		return $info;
	}
	
	
	
	public function getMessageinfo($where){
		$info = $this-> where($where)-> find();
		return $info;
	}
	
	public function readMessageinfo($id){
		$log = M(C('AUTH_DB_NAME').'.message_read')-> where('log_id='.$id)-> find();
		$info = $this->where('id='.$log['mid'])->find();
		if($log['status']<1){
		$res = M(C('AUTH_DB_NAME').'.message_read')-> where('log_id='.$id)->setInc('status');
		}
		return $info;
	}
}
