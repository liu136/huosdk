<?php
namespace Common\Model;
use Common\Model\CommonModel;
class ClientgameModel extends CommonModel{
    protected $trueTableName = 'l_clientgame';
    protected $dbName = 'db_league_mng';

	public function getClientgameinfo($where, $limit){
		$info = M() -> table(C('MNG_DB_NAME').".".C('LDB_PREFIX')."clientgame cg")
					-> where($where)
					-> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c on c.id=cg.cid")
					-> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g on g.id=cg.gid")
			        -> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext ge on g.id=ge.gid")
					-> field("cg.*, c.nickname, c.clientname, g.gamename, g.appidenty,g.initial,ge.ghid ")
					-> order('cg.id desc')
					-> limit($limit)
					-> select();
		
		return $info;
	}
	
	//渠道注册数据
	public function getClientgamereg($where, $limit,$where_time){
		$info = M() -> table(C('MNG_DB_NAME').".".C('LDB_PREFIX')."clientgame cg")
					-> where($where)
					-> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c on c.id=cg.cid")
					-> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g on g.id=cg.gid")
					-> join("INNER JOIN (SELECT cid,appid,reg_time,COUNT(username) AS regcount,COUNT(DISTINCT imei) AS imeicount FROM 
						".C('DATA_DB_NAME').".".C('LDB_PREFIX')."user WHERE ".$where_time." GROUP BY cid,appid) u ON (u.cid = cg.cid AND u.appid = cg.gid)")
					-> field("c.nickname, g.gamename, u.regcount, u.imeicount,c.clevel")
					-> limit($limit)
					-> select();
		return $info;
	}


    //渠道注册总数据
	public function getClientgamesumreg($where, $where_time){
		/* $info = M() -> table(C('MNG_DB_NAME').".".C('LDB_PREFIX')."clientgame cg")
					-> where($where)
					-> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c on c.id=cg.cid")
					-> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g on g.id=cg.gid")
					-> join("LEFT JOIN ".C('DATA_DB_NAME').".".C('LDB_PREFIX')."user u ON (u.cid = cg.cid AND u.appid = cg.gid)")
					-> field("COUNT(DISTINCT u.lm_username) AS regcount,COUNT(DISTINCT u.imei) AS imeicount")
					-> select(); */
	    $info = M() -> table(C('MNG_DB_NAME').".".C('LDB_PREFIX')."clientgame cg")
	    -> where($where)
	    -> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c on c.id=cg.cid")
	    -> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g on g.id=cg.gid")
	    -> join("INNER JOIN (SELECT cid,appid,reg_time,COUNT(username) AS regcount,COUNT(DISTINCT imei) AS imeicount FROM
		".C('DATA_DB_NAME').".".C('LDB_PREFIX')."user WHERE ".$where_time." GROUP BY cid,appid) u ON (u.cid = cg.cid AND u.appid = cg.gid)")
	    -> field("sum(regcount) AS regcount,sum(imeicount) AS imeicount")
	    -> select();
		
		return $info;
	}


	/*
	 * 根据条件获取数量
	 */
	public function getClientgamecnt($where){
		$cnt = M() -> table(C('MNG_DB_NAME').".".C('LDB_PREFIX')."clientgame cg") -> where($where)->count();
		return $cnt;
	}

	/*
	 * 根据条件获取渠道注册数量
	 */
	public function getClientregcnt($where,$where_time){
		$cnt = M()	-> table(C('MNG_DB_NAME').".".C('LDB_PREFIX')."clientgame cg")
					-> where($where)
					-> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c on c.id=cg.cid")
	                -> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g on g.id=cg.gid")
	                -> join("INNER JOIN (SELECT cid,appid,reg_time,COUNT(username) AS regcount,COUNT(DISTINCT imei) AS imeicount FROM
						".C('DATA_DB_NAME').".".C('LDB_PREFIX')."user where ".$where_time." GROUP BY cid,appid) u ON (u.cid = cg.cid AND u.appid = cg.gid)")
					->count();
		return $cnt;
	}


	/*
	 * 根据条件获取渠道信息
	 */
	public function getClientlist($where){
		$list = M()	-> table(C('MNG_DB_NAME').".".C('LDB_PREFIX')."clientgame cg")
					-> where($where)
					-> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c on c.id=cg.cid")
					-> field("c.clientname, c.link_email, c.companyname")
					-> select();
		return $list;
	}
		
	public function saveData($data){
		if(empty($data)){
			return -1;
		}
	
		$rs = $this -> save($data);
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