<?php
namespace Common\Model;
use Common\Model\CommonModel;
class UserModel extends CommonModel{
    protected $trueTableName = 'l_user';
    protected $dbName = 'db_league_data';	
	/*
	 * 获取用户注册数据
	 */
	public function getUserReg($where, $limit){
		
	    $sql = "select cid,appid,reg_time,username,imei,deviceinfo,nickname,gamename,paysum,paycount,logcount,login_time from 
		        (select u.cid,u.appid,u.reg_time,u.username,u.imei,u.deviceinfo,c.nickname,g.gamename,SUM(d.summoney) as paysum,SUM(d.paycnt) as paycount,SUM(d.logincnt) as logcount,MAX(d.date) as login_time,c.saleid
		         FROM ".C('DATA_DB_NAME').".".C('LDB_PREFIX')."dayuser d
		          RIGHT JOIN ".C('DATA_DB_NAME').".".C('LDB_PREFIX')."user u ON u.id=d.userid
                  LEFT JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c ON c.id = u.cid
                  LEFT JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g ON g.id=u.appid";
		$sql .= " GROUP BY d.userid )a where ".$where." order by reg_time desc limit ".$limit;
		
		$info = M() ->query($sql);
		/*$info = M() -> table(C('DATA_DB_NAME').".".C('LDB_PREFIX')."user u")
					-> where($where)
					-> join("INNER JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c on c.id=u.cid ")
					-> join("INNER JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g on g.id=u.appid")
					-> join("INNER JOIN (SELECT userid,cid,MAX(login_time) AS login_time,COUNT(userid) AS logcount FROM ".C('DATA_DB_NAME').".".C('LDB_PREFIX')."loginlog GROUP BY userid,FROM_UNIXTIME( login_time, '%Y%m%d' )) l ON (l.userid = u.id AND  l.cid = u.cid)")
					-> join("INNER JOIN (SELECT userid,cid,SUM(amount) AS paysum,COUNT(lm_username) AS paycount FROM ".C('DATA_DB_NAME').".".C('LDB_PREFIX')."paylog WHERE STATUS=1 GROUP BY lm_username) p ON (p.userid=u.id AND  p.cid = u.cid)")
					-> field("u.reg_time,u.username,u.imei,u.deviceinfo,c.nickname, g.gamename,p.paysum,p.paycount,l.login_time,l.logcount")
					-> order("u.id desc")
					-> limit($limit)
					-> select();
		*/
		return $info;
	}

	/*
	 * 根据条件获取用户注册总数
	 */
	public function getUserRegcnt($where){
		
		/*$cnt = M() -> table(C('DATA_DB_NAME').".".C('LDB_PREFIX')."user u")
					-> where($where)
					-> join("INNER JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c on c.id=u.cid ")
					-> join("INNER JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g on g.id=u.appid")
					-> join("INNER JOIN (SELECT userid,cid,MAX(login_time) AS login_time,COUNT(userid) AS logcount FROM ".C('DATA_DB_NAME').".".C('LDB_PREFIX')."loginlog GROUP BY userid,FROM_UNIXTIME( login_time, '%Y%m%d' )) l ON (l.userid = u.id AND  l.cid = u.cid)")
					-> join("INNER JOIN (SELECT userid,cid,SUM(amount) AS paysum,COUNT(lm_username) AS paycount FROM ".C('DATA_DB_NAME').".".C('LDB_PREFIX')."paylog WHERE STATUS=1 GROUP BY lm_username) p ON (p.userid=u.id AND  p.cid = u.cid)")
					-> field("u.reg_time,u.username,u.imei,u.deviceinfo,c.nickname, g.gamename,p.paysum,p.paycount,l.login_time,l.logcount")
					-> count();*/
	    $sql = "select count(username) as rcount from (select u.cid,u.appid,u.reg_time,u.username,u.imei,u.deviceinfo,c.nickname,g.gamename,SUM(d.summoney) as paysum,SUM(d.paycnt) as paycount,SUM(d.logincnt) as logcount,MAX(d.date) as login_time,c.saleid
		          FROM ".C('DATA_DB_NAME').".".C('LDB_PREFIX')."dayuser d
		          RIGHT JOIN ".C('DATA_DB_NAME').".".C('LDB_PREFIX')."user u ON u.id=d.userid
                  LEFT JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c ON c.id = u.cid
                  LEFT JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g ON g.id=u.appid";
		$sql .= " GROUP BY d.userid )a where ".$where;
		
		$info = M() ->query($sql);
		return $info[0]['rcount'];
	}
	
}