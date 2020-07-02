<?php
namespace Common\Model;
use Common\Model\CommonModel;

class PaylogModel extends CommonModel{
    protected $trueTableName = 'l_paylog';
    protected $dbName = 'db_league_data';	
	/*
	 * 获取用户充值数据
	 */
	public function getUserPaylog($where, $limit){
		$info = M() -> table(C('DATA_DB_NAME').'.'.C('LDB_PREFIX').'paylog p')
					-> where($where)
					-> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c on c.id=p.cid ")
					-> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g on g.id=p.appid ")
					-> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."payway pw on pw.payname = p.payway ")
					-> field("p.*, c.clientname, g.gamename, pw.realname,c.nickname")
					-> order("p.id desc")
					-> limit($limit)
					-> select();
		
		return $info;
	}

	/*
	 * 获取渠道充值数据
	 */
	public function getClientPaylog($where, $limit){
		$info = M() -> table(C('DATA_DB_NAME').".".C('LDB_PREFIX')."paylog p")
					-> where($where)
					-> join("LEFT JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c ON c.id=p.cid")
					-> join("LEFT JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g ON g.id=p.appid")
					-> field("SUM(p.amount) AS paysum,COUNT(DISTINCT p.lm_username) AS paycount, c.clientname, c.nickname,g.gamename,c.clevel")
					-> group("p.cid,p.appid")
					-> limit($limit)
					-> select();
		return $info;
	}

	/*
	 * 获取渠道充值总数据
	 */
	public function getClientPaysumlog($where){
	    $where = $where ? "where ".$where : '';
	    $sql = "SELECT SUM(a.paysum) AS paysum,sum(a.paycount) AS paycount
	            from(
                SELECT SUM(p.amount) AS paysum,COUNT(DISTINCT p.lm_username) AS paycount
            	FROM ".C('DATA_DB_NAME').".l_paylog p
            	INNER JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c ON c.id=p.cid
            	".$where."
            	GROUP BY p.cid,p.appid
                )a";
	    $info = M()->query($sql);
		return $info;
	}

	/*
	 * 根据条件获取用户充值总数
	 */
	public function getUserPaylogcnt($where){
		$cnt = M() -> table(C('DATA_DB_NAME').".".C('LDB_PREFIX')."paylog p") 
		-> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c on c.id=p.cid ")
		-> where($where)
		-> count();
		return $cnt;
	}

	/*
	 * 根据条件获取渠道充值数据
	 */
	public function getClientPaylogcnt($where){
		$cntlist = M() 
		-> table(C('DATA_DB_NAME').".".C('LDB_PREFIX')."paylog p") 
		-> join("LEFT JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c ON c.id=p.cid")
		-> where($where)-> group("p.cid,p.appid") 
		-> select();
		$cnt = count($cntlist);
		return $cnt;
	}
	
	/*
	 * 获取渠道充值数据详情
	 */
	public function getChannelClientPay($where,$limit,$cid){
	    $limit = $limit ? 'limit '.$limit : '';
	    $where = $where ? 'where '.$where : '';
	    $cparentid = M('db_sdk_'.$cid.".clientusers",C('CDB_PREFIX'))->where('parentid = 0')->getField('id');
	    
	    $sql = "select n.user_nicename,sum(n.paysum) as paysum,sum(n.paycount) as paycount,n.agentid,n.appid,n.agentgame,n.gamename,n.parentid,ms.rate,mc.nickname,mc.saleid 
	            from (SELECT c.id,c.user_nicename,sum(p.amount) as paysum,count(DISTINCT p.userid) as paycount,a.agentid,p.appid,ag.gamename,a.agentgame,a.parentid
                from db_sdk_".$cid.".".C('CDB_PREFIX')."clientusers c
                LEFT JOIN db_sdk_".$cid.".".C('CDB_PREFIX')."agentlist a on (c.id = a.agentid and c.parentid = ".$cparentid.")
                left join db_sdk_".$cid.".".C('CDB_PREFIX')."agentlist ag on (ag.parentid = a.agentid and a.appid=ag.appid)
                inner JOIN db_sdk_".$cid.".".C('CDB_PREFIX')."pay p on (ag.agentgame = p.agentgame and p.`status` = 1 )
	            ".$where."
                group by a.agentid
                UNION 
                SELECT c.id,c.user_nicename,sum(p.amount) as paysum,count(DISTINCT p.userid) as paycount,a.agentid,p.appid,a.gamename,a.agentgame,a.parentid
                from db_sdk_".$cid.".".C('CDB_PREFIX')."clientusers c
                inner JOIN db_sdk_".$cid.".".C('CDB_PREFIX')."agentlist a on (c.id = a.agentid and a.parentid = ".$cparentid.")
                inner JOIN db_sdk_".$cid.".".C('CDB_PREFIX')."pay p on (a.agentgame = p.agentgame and p.`status` = 1 ) 
	            ".$where."
                group by a.agentid) n
                inner JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game mg on mg.id = n.appid
                inner JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."serverorder ms on ms.gid = n.appid
                inner JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client mc on mc.id = mg.cid 
                group by n.agentid,n.appid ".$limit;
	    
	    $info = M() -> query($sql);
	    return $info;
	}
	
	/*
	 * 根据条件获取渠道充值数据详情
	 */
	public function getChannelClientPaycnt($where,$cid){
	    $where = $where ? 'where '.$where : '';
	    $cparentid = M('db_sdk_'.$cid.".clientusers",C('CDB_PREFIX'))->where('parentid = 0')->getField('id');
	    
	    $sql = "select n.user_nicename,sum(n.paysum) as paysum,sum(n.paycount) as paycount,n.agentid,n.appid,n.agentgame,n.parentid,ms.rate,mc.nickname,mc.saleid 
	           from (
	            SELECT c.id,c.user_nicename,sum(p.amount) as paysum,count(DISTINCT p.userid) as paycount,a.agentid,p.appid,ag.gamename,a.agentgame,a.parentid
                from db_sdk_".$cid.".".C('CDB_PREFIX')."clientusers c
                left join db_sdk_".$cid.".".C('CDB_PREFIX')."agentlist a on (c.id = a.agentid and c.parentid = ".$cparentid.")
                left join db_sdk_".$cid.".".C('CDB_PREFIX')."agentlist ag on (ag.parentid = a.agentid and a.appid=ag.appid)
                inner JOIN db_sdk_".$cid.".".C('CDB_PREFIX')."pay p on (ag.agentgame = p.agentgame and p.`status` = 1 )
	            ".$where."
                group by a.agentid
                UNION 
                SELECT c.id,c.user_nicename,sum(p.amount) as paysum,count(DISTINCT p.userid) as paycount,a.agentid,p.appid,a.gamename,a.agentgame,a.parentid
                from db_sdk_".$cid.".c_clientusers c
                inner JOIN db_sdk_".$cid.".".C('CDB_PREFIX')."agentlist a on (c.id = a.agentid and a.parentid = ".$cparentid.")
                inner JOIN db_sdk_".$cid.".".C('CDB_PREFIX')."pay p on (a.agentgame = p.agentgame and p.`status` = 1 )
	            ".$where."
                group by a.agentid) n
                inner JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game mg on mg.id = n.appid
                inner JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."serverorder ms on ms.gid = n.appid
                inner JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client mc on mc.id = mg.cid 
                group by n.agentid,n.appid ";
	    
	    $cntlist = M() -> query($sql);
	    $cnt = count($cntlist);
	    return $cnt;
	}
	
	/*
	 * 获取渠道充值数据，显示对应业务员
	 */
	public function getClientSalesPaylog($where, $limit){
	    $info = M() -> table(C('DATA_DB_NAME').".".C('LDB_PREFIX')."paylog p")
	    -> where($where)
	    -> join("LEFT JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c ON c.id=p.cid")
	    -> join("LEFT JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g ON g.id=p.appid")
	    -> field("p.cid,SUM(p.amount) AS paysum,COUNT(DISTINCT p.lm_username) AS paycount, c.clientname, c.nickname,g.gamename,c.saleid")
	    -> group("p.cid,p.appid")
	    -> limit($limit)
	    -> select();
	    return $info;
	}
	
	/*
	 * 根据条件获取渠道充值数据，显示对应业务员
	 */
	public function getClientSalesPaylogcnt($where){
	    $cntlist = M() -> table(C('DATA_DB_NAME').".".C('LDB_PREFIX')."paylog p") 
	    -> where($where) 
	    -> join("LEFT JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c ON c.id=p.cid")
	    -> group("p.cid,p.appid")
	    -> select();
	    $cnt = count($cntlist);
	    return $cnt;
	}
	
	/*
	 * 获取渠道充值汇总数据
	 */
	public function getClientSalesPaysumlog($where){
	    $where = $where ? "where ".$where : '';
	    $sql = "SELECT SUM(a.paysum) AS paysum,sum(a.paycount) AS paycount 
	            from(
                SELECT SUM(p.amount) AS paysum,COUNT(DISTINCT p.lm_username) AS paycount 
            	FROM ".C('DATA_DB_NAME').".l_paylog p 
            	INNER JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c ON c.id=p.cid 
            	".$where."
            	GROUP BY p.cid,p.appid
                )a";
	    $info = M()->query($sql);
	    return $info;
	}
}