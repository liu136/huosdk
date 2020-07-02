<?php
namespace Common\Model;
use Common\Model\CommonModel;

class GameModel extends CommonModel{
    protected $trueTableName = 'l_game';
    protected $dbName = 'db_league_mng';
	public function saveData($data){
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

	/*
	 * 添加回调地址
	 */
	public function addUrldata($data){
		if(empty($data)){
			return -1;
		}
		
		$urlmodel = M(C('MNG_DB_NAME').'.'.C('LDB_PREFIX').'gamecpurl');
		$rs = $urlmodel -> add($data);
		if($rs){
			return 1;
		}else{
			return 0;
		}
	}	
	/*
	 * 保存回调地址
	 */
	public function saveUrldata($data){
		if(empty($data)){
			return -1;
		}
		$urlmodel = M(C('MNG_DB_NAME').'.'.C('LDB_PREFIX').'gamecpurl');
		$rs = $urlmodel -> save($data);
		if($rs){
			return 1;
		}else{
			return 0;
		}
	}
	
	/*
	 * check回调地址是否存在
	 */
	public function checkurl($gid){
		if(empty($gid)){
			return -1;
		}
		
		$where = "gid=".$gid;
		$urlmodel = M(C('MNG_DB_NAME').'.'.C('LDB_PREFIX').'gamecpurl');
		$rs = $urlmodel -> where($where) -> count();
		return $rs;
	}

	/*
	 * 条件查询回调
	 */
	public function seachurl($where){
		
		$urlmodel = M(C('MNG_DB_NAME').'.'.C('LDB_PREFIX').'gamecpurl');
		$rs = $urlmodel -> where($where) -> select();
		return $rs;
	}
	
	/*
	 * 获取回调地址
	 */
	public function getCpurlinfo($where, $limit){
		$info = M() -> table(C('MNG_DB_NAME').".".C('LDB_PREFIX')."game a")
					-> where($where)
					-> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."gamecpurl g on a.id=g.gid")
					-> field("a.id, g.cid,a.gamename, g.cpurl, g.update_time")
					-> limit($limit)
					-> select();
		return $info;
	}
	
	public function getGameinfo($where, $limit){
		$info = M() -> table(C('MNG_DB_NAME').".".C('LDB_PREFIX')."game a")
					-> where($where)
					-> field("a.*,cp.*")
					-> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext g on a.id=g.gid")
					-> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."gamecpurl cp on a.id=cp.gid")
					-> limit($limit)
					-> order('id desc')
					-> select();
		
		return $info;
	}
	

	/*
	 * 条件游戏版本
	 */
	public function seachver($where){
		
		$vermodel = M(C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_version");
		$rs = $vermodel 
			-> where($where) 
			-> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g on l_game_version.gid=g.id") 
			-> field("l_game_version.*,g.gamename")
			-> select();
		return $rs;
	}
	/*
	 * 添加游戏版本
	 */
	public function addVerdata($data){
		if(empty($data)){
			return -1;
		}
		
		$vermodel = M(C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_version");
		$rs = $vermodel -> add($data);
		if($rs){
			return 1;
		}else{
			return 0;
		}
	}	
	/*
	 * 保存游戏版本
	 */
	public function saveVerdata($data){
		if(empty($data)){
			return -1;
		}
		$vermodel =  M(C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_version");
		$rs = $vermodel -> save($data);
		if($rs){
			return 1;
		}else{
			return 0;
		}
	}

	
	/*
	 * 根据条件获取游戏数量
	 */
	public function getGamecnt($where){
		$cnt = $this -> where($where)->count();
		return $cnt;
	}
	
	/*
	 * 获取游戏ID 与 NAME 对应值
	 */
	public function  getGamename($where = NULL){
	    if(empty($where)){
	        $info = $this -> field('id, gamename') -> select();
	    }else{
	        $info = M(C('MNG_DB_NAME').".clientgame",C('LDB_PREFIX'))
    	    -> alias('cg')
    	    -> field('g.id, g.gamename')
    	    -> join("inner join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g ON cg.gid = g.id and cg.ratestatus = 3")
    	    -> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c ON c.id = cg.cid")
    	    -> group('g.id')
    	    -> where($where)
    	    -> select();
	    }
		
		return $info;
	}

	/*
	 * 根据条件获取游戏版本
	 */
	public function getVersioncnt($where){
		$model =  M(C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_version");
		$cnt = $model -> where($where)->count();
		return $cnt;
	}

	public function getVersionfo($where, $limit){
		$info =  M(C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_version")
				-> where($where)
				-> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g on l_game_version.gid=g.id")
				-> field("l_game_version.*, g.gamename,g.id as sid")
				-> limit($limit)
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
}