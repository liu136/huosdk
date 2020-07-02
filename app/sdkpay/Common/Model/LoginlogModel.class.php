<?php
namespace Common\Model;
use Common\Model\CommonModel;
class LoginlogModel extends CommonModel{
    protected $trueTableName = 'l_loginlog';
    protected $dbName = 'db_league_data';	
	/*
	 * 获取用户登录数据
	 */
	public function getUserLogin($where, $limit){
		$info = M() -> table(C('DATA_DB_NAME').".".C('LDB_PREFIX')."loginlog l")
					-> where($where)
					-> join("INNER JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c on c.id=l.cid
						INNER JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g on g.id=l.appid 
						INNER JOIN ".C('DATA_DB_NAME').".".C('LDB_PREFIX')."user u on u.id=l.userid ")
					-> field("l.*,c.nickname,g.gamename,u.username")
					-> order("l.id desc")
					-> limit($limit)
					-> select();
		
		return $info;
	}
	

	/*
	 * 根据条件获取用户注册总数
	 */
	public function getUserLogincnt($where){
		$cnt = M() -> table(C('DATA_DB_NAME').".".C('LDB_PREFIX')."loginlog l") 
			-> where($where)
			-> join("INNER JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c on c.id=l.cid")
			-> join("INNER JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g on g.id=l.appid")
			-> join("INNER JOIN ".C('DATA_DB_NAME').".".C('LDB_PREFIX')."user u on u.id=l.userid")
			-> field("l.*,c.nickname,g.gamename,u.username")
			->count();
		return $cnt;
	}
	
}