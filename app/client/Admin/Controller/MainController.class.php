<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class MainController extends AdminbaseController {
	
	protected $users_model,$agentgamepaymodel,$daypaymodel,$where,$userwhere,$appidwhere;

	function _initialize() {
		
		parent::_initialize();
		
		$this->agentgamepaymodel = M($this->dbname.'.dayagentgame', C('CDB_PREFIX'));
		$this->daypaymodel = M($this->dbname.'.daypay', C('CDB_PREFIX'));
		$this->users_model = M($this->dbname.'.clientusers', C('LDB_PREFIX'));
		$this->tagentgamepaymodel = M($this->dbname.'.tagentgamepayview', C('CDB_PREFIX'));
        
		$this->where = " 1 ";
		$this->userwhere = " 1 ";
		$this->appidwhere = "1";
		$uid = sp_get_current_admin_id();
		$roletype = sp_get_current_roletype();
		
	   if ($roletype > 2) {
			$userids = $this->getAgentids($uid);
			//$appids = $this->getAppids($uid);
			
			$this->where = " agentid in (".$userids.") ";
			$this->userwhere = " l_clientusers.id in (".$userids.") ";
			
			//$appids = $this->getAppids($uid);
			//$this->appidwhere = "appid in (".$appids.") "; 
			
        }
		
		$this->daypaymodel = M($this->dbname.'.dayagentgame', C('CDB_PREFIX'));
		//}
	}
    public function index(){
		$this->_dayreg();
		
    	//$this->_yesterdayreg();
		$this->_regcnt();
		
		$this->_yesterdaylogin();
		$this->_yesterdaypayuser();
		$this->_yesterdaypay();
		$this->_yesterdaypay();
		$this->_paymoney();
		
		$this->_monthregcou();
		
		$this->_lastmonthmoney();
		$this->_monthmoney();
		$this->_activeusers();
		$this->_moneycount();
		
		$this->_yesterdaygames();
		
		$this->_gamescnt();
		
		$this->_yesterdayagent();
		$this->_agentcnt();
		$this->_packagecnt();
		$this->_paylist();
		
		$this->_userloginlist();
		$this->_userreglist();
		$this->_userpaylist();
		
    	$this->display();
    }

	/*
	 *昨日今日注册
	 */ 
	function _dayreg(){
		//今日注册
        $model = M($this->dbname.'.tagentgamepayview', C('CDB_PREFIX'));
		$dayreg = $model->where($this->where)->sum('reg_cnt');
		$dayreg = empty($dayreg) ? 0 : $dayreg;
        
		//昨日注册
		$where = $this->where." AND `date`= (CURDATE() - INTERVAL 1 DAY)";
        $yesterdayreg = $this->agentgamepaymodel->where($where)
						 ->sum('reg_cnt');
		$yesterdayreg = empty($yesterdayreg) ? 0 : $yesterdayreg;
        
		//上升和降幅
		if($dayreg == 0 || $yesterdayreg == 0){
		    $regrate['isratio'] = 1;
		    $regrate['ratio'] = '-';
		}else{
		    $ratio = ($yesterdayreg - $dayreg) / $yesterdayreg * 100;
		    $ratio = number_format($ratio,2);
		    $regrate['isratio'] = $ratio ? $ratio : 0;
		    $regrate['ratio'] = $ratio < 0 ? ($ratio * -1).'%' : $ratio.'%';
		}
		
		$this->assign("dayreg",$dayreg);
		$this->assign("regrate",$regrate);
	}
	
	//历史注册
	function _regcnt(){
		$where = $this->appidwhere=="1" ? $this->where : $this->where." AND ".$this->appidwhere;
		
        $regcnt = $this->daypaymodel
        ->where($where)
		->sum('reg_cnt');
		$regcnt = empty($regcnt) ? 0 : $regcnt;
		$this->assign("regcnt",$regcnt);
	}
	
	//昨日登陆
	function _yesterdaylogin(){
		$where = $this->appidwhere=="1" ? $this->where : $this->where." AND ".$this->appidwhere;
		
	    $where = $where." AND `date`= (CURDATE() - INTERVAL 1 DAY)";
	    $yesterdaylogin = $this->agentgamepaymodel->where($where)
						   ->sum('usercnt');
		$yesterdaylogin = empty($yesterdaylogin) ? 0 : $yesterdaylogin;
		$this->assign("yesterdaylogin",$yesterdaylogin);
	}
	
	//昨日付费用户
	function _yesterdaypayuser(){
		$where = $this->appidwhere=="1" ? $this->where : $this->where." AND ".$this->appidwhere;
		
	    $where = $where." AND `date`= (CURDATE() - INTERVAL 1 DAY)";
	     $yesterdaypayuser = $this->agentgamepaymodel->where($where)
							  ->sum('paycnt');
		 $yesterdaypayuser = empty($yesterdaypayuser) ? 0 : $yesterdaypayuser;
		$this->assign("yesterdaypayuser",$yesterdaypayuser);
	}
	
	//昨日流水
	function _yesterdaypay(){
		$where = $this->appidwhere=="1" ? $this->where : $this->where." AND ".$this->appidwhere;
		
	    $where = $where." AND `date`= (CURDATE() - INTERVAL 1 DAY)";
	    $yesterdaypay = $this->agentgamepaymodel->where($where)
					->sum('summoney');
		$yesterdaypay = empty($yesterdaypay) ? 0 : $yesterdaypay;
		$this->assign("yesterdaypay",$yesterdaypay);
	}

	//历史流水
	function _paymoney(){
		$where = $this->appidwhere=="1" ? $this->where : $this->where." AND ".$this->appidwhere;
		
	    $paymoney = $this->daypaymodel->where($where)
						->sum('summoney');
		
	    $paymoney += $this->tagentgamepaymodel->where($where)
						->sum('summoney');//当天数据
		
		$this->assign("paymoney",number_format($paymoney,2));
	}
	
	//上月总收入
	function _lastmonthmoney(){
	    $lastd = date("t",strtotime("last month"));
	    $lastym = date("Y-m",strtotime("last month"));
	    $lastmonthmoney = $this->daypaymodel
        ->where("date >= '".$lastym."-01' and date <= '".$lastym."-".$lastd."'")
        ->sum('summoney');
	    $this->assign('lastmonthmoney',number_format($lastmonthmoney,2));
	}
	
	//本月累计收入
	function _monthmoney(){
	    $nowd = date("t");
	    $nowym = date("Y-m");
	    $lastd = date("t",strtotime("last month"));
	    $lastym = date("Y-m",strtotime("last month"));
	    
	    $where = $this->appidwhere=="1" ? $this->where : $this->where." AND ".$this->appidwhere;
		
	    $lastmonthmoney = $this->daypaymodel
	    ->where("date >= '".$lastym."-01' and date <= '".$lastym."-".$lastd."' and ".$where)
	    ->sum('summoney');
	    
	    $monthsum = $this->daypaymodel
        ->where("date >= '".$nowym."-01' and date <= '".$nowym."-".$nowd."' and ".$where)
        ->sum('summoney');
	    
	    $monthsum += $this->tagentgamepaymodel
	                   ->where("date >= '".$nowym."-01' and date <= '".$nowym."-".$nowd."' and ".$where)
	                   ->sum('summoney');
	    
	    $monthmoney['sum'] = number_format($monthsum,2);
	    
 	    $ratio = round(($monthsum - $lastmonthmoney) / $lastmonthmoney * 100);
 	    $monthmoney['isratio'] = $ratio ? $ratio : 0;
 	    $monthmoney['ratio'] = $ratio < 0 ? $ratio * -1 : $ratio;
 	    $monthmoney['draw'] = $monthmoney['ratio'] > 100 ? '100%' : $monthmoney['ratio'].'%';
	    
	    $this->assign('monthmoney',$monthmoney);
	}
	
	//本月累积注册用户
	function _monthregcou(){
	    $lastnuix_start = mktime(0,0,0,date('m',strtotime('last month')),1,date('Y'));
	    $lastnuix_end = mktime(23,59,59,date('m',strtotime('last month')),date('t',strtotime('last month')),date('Y'));
	    
	    $year = date("Y");
	    $month = date("m");
	    $allday = date("t");
	    $unix_start = strtotime($year."-".$month."-1");
	    $unix_end = strtotime($year."-".$month."-".$allday);
	    
		$where = $this->where ==" 1 " ? $this->where : " a.".$this->where;
		
	    $monthregcou['count'] = M($this->dbname.'.members', C('CDB_PREFIX'))
	    ->alias('m')
	    ->join("left join ".$this->dbname.".".C('CDB_PREFIX')."agentlist a on (m.agentid = a.agentid)")
	    ->where("reg_time >= '".$unix_start."' and reg_time <= '".$unix_end."' and ".$where)->count('m.id');
	   
	    $lastcount = M($this->dbname.'.members', C('CDB_PREFIX'))
	    ->alias('m')
	    ->join("left join ".$this->dbname.".".C('CDB_PREFIX')."agentlist a on m.agentid = a.agentid")
	    ->where("reg_time >= '".$lastnuix_start."' and reg_time <= '".$lastnuix_end."' and ".$where)->count('m.id');
	    
	    $ratio = round(($monthregcou['count'] - $lastcount) / $lastcount * 100);
	    $monthregcou['isratio'] = $ratio ? $ratio : 0;
	    $monthregcou['ratio'] = $ratio < 0 ? $ratio * -1 : $ratio;
	    $monthregcou['draw'] = $ratio > 100 ? '100%' : $monthregcou['ratio'].'%';
	    
	    $this->assign('monthregcou',$monthregcou);
	}
	
	//活跃用户
	function _activeusers(){
	    $lastdate = date('Y-m-d',strtotime('last day'));
        
		$where = $this->where;
		
	    $lastusercount = M($this->dbname.'.dayuser', C('CDB_PREFIX'))->where("date = '".$lastdate."' and ".$where)->count('id');
	    $activeusers['count'] = M($this->dbname.'.todaypayuserview', C('CDB_PREFIX'))->where($where)->count('userid');
	    if($lastusercount == 0 || $activeusers['count'] == 0){
	        $activeusers['isratio'] = 1;
	        $activeusers['ratio'] = '-';
	    }else{
	        $ratio = round(($activeusers['count'] - $lastusercount) / $lastusercount *100);
	        $ratio = number_format($ratio,2);
	        $activeusers['isratio'] = $ratio ? $ratio : 0;
	        $activeusers['ratio'] = $ratio < 0 ? ($ratio * -1).'%' : $ratio.'%';
	    }
	    $this->assign('activeusers',$activeusers);
	}
	
	//充值统计
	function _moneycount(){
		$where = $this->appidwhere=="1" ? $this->where : $this->where." AND tv.".$this->appidwhere;
		
	    $moneycount = M($this->dbname.'.tagentgamepayview',C('CDB_PREFIX'))->alias('tv')
	    ->field('tv.date,tv.appid,g.gamename,sum(tv.summoney) money')
	    ->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g ON tv.appid = g.id")
	    ->where($where)
	    ->group('g.gamename')
	    ->order('money desc')
	    ->limit(6)
	    ->select();
	    $this->assign('moneycount',$moneycount);
	}
	
	//昨日游戏数
	function _yesterdaygames(){
		$time = strtotime(date('Y-m-d')." 00:00:00");		
	    $gamemodel = M($this->dbname.".game", C('LDB_PREFIX'));
		$yesterdaygames = $gamemodel ->where(" create_time >= %d",$time) ->count();
		
		$this->assign("yesterdaygames",$yesterdaygames);
	}

	//总游戏数
	function _gamescnt(){
	    $gamemodel = M($this->dbname.".game", C('LDB_PREFIX'));
		$gamescnt = $gamemodel ->count();
		$this->assign("gamescnt",$gamescnt);
	}
	
	//获取用户不是最真实的
	function _yesterdayagent(){
		$yesterdayagent = $this->users_model
			->where($this->userwhere." AND role_id>1 AND date(create_time) = curdate()")
			->count();
		$this->assign("yesterdayagent",$yesterdayagent);
	}

	//总渠道数
	function _agentcnt(){
		//$where = " role_type = 2";
		$agentcnt = $this->users_model
			->where($this->userwhere)
			->count();
		$this->assign("agentcnt",$agentcnt);
	}

	//总分包数
	function _packagecnt(){
		$where = $this->appidwhere=="1" ? $this->where : $this->where." AND ".$this->appidwhere;
		
		$packagemodel = M($this->dbname.".agentlist", C('CDB_PREFIX'));
		$packagecnt = $packagemodel->where("$where")->count();
		$this->assign("packagecnt",$packagecnt);
	}

	//用户流水
	function _paylist(){
		$moth = date("Y-m");
		
		$where = $this->appidwhere=="1" ? $this->where : $this->where." AND ".$this->appidwhere;
		
		$list = $this->daypaymodel->field("date,summoney")->where("$where AND  date > '%s'",$moth)->order("date")->select();
		$item = array();
		foreach ($list as $key=>$val){
			 $item[$val['date']] = $val['summoney'];
		}
		
		$paydata = $this->_tabledata($item);
		$this->assign("paydata",$paydata);
	}

	//活跃用户
	function _userloginlist(){
		$moth = date("Y-m");
		$where = $this->appidwhere=="1" ? $this->where : $this->where." AND ".$this->appidwhere;
		
		$where .= " AND "." date > '%s'";
		$list = $this->daypaymodel->field("date,usercnt")->where($where,$moth)->order("date")->select();
		$item = array();
		foreach ($list as $key=>$val){
			 $item[$val['date']] = $val['usercnt'];
		}
		
		$ulogindata = $this->_tabledata($item);
		$this->assign("ulogindata",$ulogindata);
	}

	//新增用户
	function _userreglist(){
		$moth = date("Y-m");
		$where = $this->appidwhere=="1" ? $this->where : $this->where." AND ".$this->appidwhere;
		
		$where .= " AND "." date > '%s'";
		$list = $this->daypaymodel->field("date,reg_cnt")->where($where,$moth)->order("date")->select();
		
		$item = array();
		foreach ($list as $key=>$val){
			 $item[$val['date']] = $val['reg_cnt'];
		}
		
		$uregindata = $this->_tabledata($item);
		$this->assign("uregindata",$uregindata);
	}

	//付费用户
	function _userpaylist(){
		$moth = date("Y-m");
		$where = $this->appidwhere=="1" ? $this->where : $this->where." AND ".$this->appidwhere;
		
		$where .= " AND "." date > '%s'";
		$list = $this->daypaymodel->field("date,paycnt")->where($where,$moth)->order("date")->select();
		
		$item = array();
		foreach ($list as $key=>$val){
			 $item[$val['date']] = $val['paycnt'];
		}
		
		$upaydata = $this->_tabledata($item);
		$this->assign("upaydata",$upaydata);
	}


	//图形分析数据转换
    function _tabledata($item){
        $list = array();
        
		$year = date('Y');
		$moth = 'a'.date('m');
		$moth = str_replace('a0','',$moth);
		$j = date("t"); //获取当前月份天数
		$start_time = strtotime(date('Y-m-01'));  //获取本月第一天时间戳
		$number = 1;
		for($i=0;$i<$j;$i++){
			 $day = date('Y-m-d',$start_time+$i*86400);

			 $str = strtotime($day) * 1000;
			 if($item[$day] > 0 && !empty($item[$day])){
				$arr = array($str,floatval($item[$day]));
			 }else{
				$arr = array($str,0);
			 }
			 array_push($list,$arr);
			 $number = $number+1;
		}
		return json_encode($list);
	}


}