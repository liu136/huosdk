<?php
/**
 * 渠道页面
 * 
 * @author
 *
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class AgentController extends AdminbaseController {
	protected $users_model,$role_model,$role_user_model,$game_model,$where;
	
	function _initialize() {
		parent::_initialize();
		
		$this->users_model = M(C('AUTH_DB_NAME').".clientusers", C('LDB_PREFIX'));
		$this->role_model = M(C('AUTH_DB_NAME').".clientrole", C('LDB_PREFIX'));
		$this->role_user_model = M(C('AUTH_DB_NAME').".clientrole_user", C('LDB_PREFIX'));
		$this->game_model = M("$this->dbname.game", C('LDB_PREFIX'));
	}

    /**
     * 渠道列表
     * return void
     */
    public function index() {
        $where_ands = array();
        $roletype = sp_get_current_roletype();
        $adminid = sp_get_current_admin_id();
		
        //if ($roletype == 3){  //专员能看到自己子渠道的玩家数据
            $ids = $this->getAgentids($adminid);
			
			array_push($where_ands, "u.id in ($ids)");
       // }else if ($roletype == 4){
		//	array_push($where_ands, "u.id = $adminid");
        //}
        
        $this->_getclientroles();        
        $this->_agentindex($where_ands);
        $this->display();
    }

    //渠道数据
    public function dataindex() {
        $where_ands = array();
        $roletype = sp_get_current_roletype();
        $adminid = sp_get_current_admin_id();
		$where_ands = array();
		
		if ($roletype > 2) {
			 $ids = $this->getAgentids($adminid);
             $userwhere = " id in ($ids)";
             array_push($where_ands, "agentid in ($ids)");
        }
       // if ($roletype == 3){  //专员能看到自己子渠道的玩家数据
           
        //}else if ($roletype == 4){
       //     array_push($where_ands, "agentid = $adminid");
        //    $userwhere = " id = $adminid";
        //}
        
        $this->_getGames();
        $this->_getAgents($userwhere);
        $this->_getAgentdata($where_ands);
        $this->display();
    }
    
    public function retenindex() {
        $where_ands = array();
        $roletype = sp_get_current_roletype();
        $adminid = sp_get_current_admin_id();
		$where_ands = array();
		
		if ($roletype > 2) {
			$ids = $this->getAgentids($adminid);
			array_push($where_ands, "d.agentid in ($ids)");
            $userwhere = " id in ($ids)";
        }
        
        $this->_getAgentreten($where_ands);
        $this->_getAgents($userwhere);
        $this->display();
    }

    //渠道列表
    function _agentindex($where_ands=NULL){
        $fields = array(
                'roleid' => array(
                        "field" => "u.role_id",
                        "operator" => "="
                ),
                'agentnicename' => array(
                        "field" => "u.user_nicename",
                        "operator" => "like"
                ),
        );
        
        
        if (IS_POST) {
            foreach ($fields as $param => $val) {
                if (isset($_POST[$param]) && !empty($_POST[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $get = trim($_POST[$param]);
                    $_GET[$param] = $get;
        
                    if ($operator == "like") {
                        $get = "%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }else{
            foreach ($fields as $param => $val) {
                if (isset($_GET[$param]) && !empty($_GET[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $get = trim($_GET[$param]);
        
                    if ($operator == "like") {
                        $get = "%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }
		$adminid = sp_get_current_admin_id();
		
        $cid  = sp_get_current_cid();
		
	array_push($where_ands, "r.typeid = 3");
        array_push($where_ands, "u.id != $adminid");
       
        $where = join(" AND ", $where_ands);
	
        $count=$this->users_model
        ->alias('u')
        ->join("left join ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole r ON r.id = u.role_id")
        ->where($where)
        ->count();
        
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : $this->row;
        $page = $this->page($count, $rows);
        
        $field = "u.id id , r.name rolename, typeid, u.user_login,u.user_nicename, u.last_login_ip, u.last_login_time, u.user_email, u.mobile, u.user_status,u.parentid,pre.user_nicename as pres,u.level";
        $users = $this->users_model
        ->alias('u')
        ->field($field)
        ->join("left join ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole r ON r.id = u.role_id")
	->join("left join ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientusers pre ON pre.id = u.parentid")
        ->where($where)
        ->limit($page->firstRow . ',' . $page->listRows)
        ->select();
	$this->getcharactors();
		
        $this->assign("formget", $_GET);
        $this->assign("current_page", $page->GetCurrentPage());
        $this->assign("Page", $page->show('Admin'));
        $this->assign("users",$users);
    }


	function ban(){
        $id=intval($_GET['id']);
    	if ($id) {
    		$rst = $this->users_model->where(array("id"=>$id))->setField('user_status','0');
    		if ($rst) {
    			$this->success("账号禁用成功！", U("Agent/index"));
    		} else {
    			$this->error('账号禁用失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
    function cancelban(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = $this->users_model->where(array("id"=>$id))->setField('user_status','1');
    		if ($rst) {
    			$this->success("账号启用成功！", U("Agent/index"));
    		} else {
    			$this->error('账号启用失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
    function _getAgentdata($where_ands=NULL){
		$fields = array(
	        'start_time' => array(
	                "field" => "date",
	                "operator" => ">="
	        ),
	        'end_time' => array(
	                "field" => "date",
	                "operator" => "<="
	        ),
	        'agentid' => array(
	                "field" => "agentid",
	                "operator" => "="
	        ),
	        'parentid' => array(
	                "field" => "agentid",
	                "operator" => "in"
	        ), 
            'gid' => array(
                    "field" => "appid", 
                    "operator" => "=" 
             )
		);
		
		if (IS_POST) {
		    foreach ($fields as $param => $val) {
		        if (isset($_POST[$param]) && !empty($_POST[$param])) {
		            $operator = $val['operator'];
		            $field = $val['field'];
		            $get = trim($_POST[$param]);
		            $_GET[$param] = $get;
		            
					
		            if ($operator == "like") {
		                $get = "%$get%";
		            }
					if ('parentid' == $param) {
						$ids = $this->getAgentids($get);
			            array_push($where_ands, "$field $operator ($ids)");
					}else{
                       array_push($where_ands, "$field $operator '$get'");
					}
		            
		        }
		    }
		}else{
		    foreach ($fields as $param => $val) {
		        if (isset($_GET[$param]) && !empty($_GET[$param])) {
		            $operator = $val['operator'];
		            $field = $val['field'];
		            $get = trim($_GET[$param]);
		
		            if ($operator == "like") {
		                $get = "%$get%";
		            }

		            if ('parentid' == $param) {
						$ids = $this->getAgentids($get);
			            array_push($where_ands, "$field $operator ($ids)");
					}else{
                       array_push($where_ands, "$field $operator '$get'");
					}
		        }
		    }
		}
		
		if ('今日' == $_POST['date_time']) {
		    $_GET['start_time'] = date("Y-m-d");
		    $_GET['end_time'] = date("Y-m-d");
		    array_push($where_ands, "date='".$_GET['end_time']."'");
		} elseif ('七日' == $_POST['date_time']) {
		    $_GET['start_time']  = date("Y-m-d",strtotime("-6 day"));
		    $_GET['end_time']  = date("Y-m-d");
		    array_push($where_ands, "date>='".$_GET['start_time']."'");
		    array_push($where_ands, "date<='".$_GET['end_time']."'");
		} elseif ('当月' == $_POST['date_time']) {
		    $_GET['start_time'] = date("Y-m-01");
		    $_GET['end_time']  = date("Y-m-d");
		    array_push($where_ands, "date>='".$_GET['start_time']."'");
		    array_push($where_ands, "date<='".$_GET['end_time']."'");
		} elseif ('30天' == $_POST['date_time']) {
		    $_GET['start_time'] = date("Y-m-d",strtotime("-29 day"));
		    $_GET['end_time']  = date("Y-m-d");
		    array_push($where_ands, "date>='".$_GET['start_time']."'");
		    array_push($where_ands, "date<='".$_GET['end_time']."'");
		}
		
		$adminid = sp_get_current_admin_id();
		$www = ' 1 ';
		if($adminid == 487){
             $www .=" AND date >= '2016-08-18'";
		}
		array_push($where_ands, $www);
		$wheres = join(" AND ", $where_ands);
		
		//$model = M($this->dbname.'.dayagentgame', C('CDB_PREFIX'));
		//$count =  $model->where($wheres)->count();
		$where = $wheres ? ' where '.$wheres : '';
		$cquery = "select * from (SELECT date,agentid,appid,usercnt,summoney,paycnt,regpaycnt,sumregmoney,reg_cnt
                    from ".$this->dbname.".".C('CDB_PREFIX')."dayagentgame
                    ".$where."
                    UNION
                    select date,agentid,appid,usercnt,summoney,paycnt,regpaycnt,sumregmoney,reg_cnt
                    from ".$this->dbname.".".C('CDB_PREFIX')."tagentgamepayview
                    ".$where."
                    ORDER BY date desc)a ";
		$count = count(M()->query($cquery));
        
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : $this->row;
        $page = $this->page($count, $rows);
        
        //$sumfield = "SUM(`usercnt`) `usercnt`,SUM(`summoney`) `summoney`,SUM(`paycnt`) `paycnt`,SUM(`regpaycnt`) `regpaycnt`,SUM(`sumregmoney`) `sumregmoney`,SUM(`reg_cnt`) `reg_cnt` ";
		//$sumitems = $model->field($sumfield)->where($wheres)->select();
		$sumquery = "select SUM(`usercnt`) `usercnt`,SUM(`summoney`) `summoney`,SUM(`paycnt`) `paycnt`,SUM(`regpaycnt`) `regpaycnt`,SUM(`sumregmoney`) `sumregmoney`,SUM(`reg_cnt`) `reg_cnt` from (
                    SELECT `usercnt`,`summoney`,`paycnt`,`regpaycnt`,`sumregmoney`,`reg_cnt` 
                    FROM ".$this->dbname.".".C('CDB_PREFIX')."dayagentgame
                    ".$where."
                    UNION ALL 
                    select `usercnt`,`summoney`,`paycnt`,`regpaycnt`,`sumregmoney`,`reg_cnt` 
                    from ".$this->dbname.".".C('CDB_PREFIX')."tagentgamepayview
                    ".$where."
                    )a;";
        $sumitems = M()->query($sumquery);
		
        
		$limit = " limit " . $page->firstRow . ',' . $page->listRows;
		$query = "select * from (SELECT date,agentid,appid,usercnt,summoney,paycnt,regpaycnt,sumregmoney,reg_cnt
                    from ".$this->dbname.".".C('CDB_PREFIX')."dayagentgame
                    ".$where."
                    UNION
                    select date,agentid,appid,usercnt,summoney,paycnt,regpaycnt,sumregmoney,reg_cnt
                    from ".$this->dbname.".".C('CDB_PREFIX')."tagentgamepayview
                    ".$where."
                    ORDER BY date desc)a ".$limit;
		
		$items = M()->query($query);

        $this->assign("totalpays", $sumitems);
        $this->assign("pays", $items);
        $this->assign("formget", $_GET);        
        $this->assign("Page", $page->show('Admin'));
        $this->assign("current_page", $page->GetCurrentPage());
    }

	function _getAgentreten($where_ands){
		$fields = array(
		        'start_time' => array(
		                "field" => "date",
		                "operator" => ">"
		        ),
		        'end_time' => array(
		                "field" => "date",
		                "operator" => "<"
		        ),
		        'agentid' => array(
		                "field" => "d.agentid",
		                "operator" => "="
		        )
		);
		
		if (IS_POST) {
		    foreach ($fields as $param => $val) {
		        if (isset($_POST[$param]) && !empty($_POST[$param])) {
		            $operator = $val['operator'];
		            $field = $val['field'];
		            $get = trim($_POST[$param]);
		            $_GET[$param] = $get;
		
		            if ($operator == "like") {
		                $get = "%$get%";
		            }
		            array_push($where_ands, "$field $operator '$get'");
		        }
		    }
		}else{
		    foreach ($fields as $param => $val) {
		        if (isset($_GET[$param]) && !empty($_GET[$param])) {
		            $operator = $val['operator'];
		            $field = $val['field'];
		            $get = trim($_GET[$param]);
		
		            if ($operator == "like") {
		                $get = "%$get%";
		            }
		            array_push($where_ands, "$field $operator '$get'");
		        }
		    }
		}

		$where = join(" and ", $where_ands);
		$model = M($this->dbname.'.dayagentgame', C('CDB_PREFIX'));
        //$count =  $model->where($where)->count();
        $count = $model->alias('d')
        ->field($field)
        ->join("LEFT JOIN ".$this->dbname.".".C('CDB_PREFIX')."agentlist a on d.agentid = a.id")
        ->join("LEFT JOIN ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole r on a.roleid = r.id")
        ->where($where)
        ->count();
        
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : $this->row;
        $page = $this->page($count, $rows);
        
        $field = "d.`date`,d.`agentid`,d.`usercnt`,d.`day1`,d.`day2`,d.`day3`,d.`day4`,d.`day5`,d.`day6`,d.`day15`,d.`day30`,a.`roleid`,r.`typeid`";
        $items = $model->alias('d')
        ->field($field)
        ->join("LEFT JOIN ".$this->dbname.".".C('CDB_PREFIX')."agentlist a on d.agentid = a.id")
        ->join("LEFT JOIN ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole r on a.roleid = r.id")
        ->where($where)
        ->limit($page->firstRow . ',' . $page->listRows)
        ->order('`date` desc')
        ->select();
        
        /* $field = "`date`, `agentid`,`usercnt`,`day1`, `day2`, `day3`, `day4`, `day5`, `day6`, `day15`, `day30`";
		$items = $model
		->field($field)
		->where($where)
		->limit($page->firstRow . ',' . $page->listRows)
		->order('`date` desc')
		->select(); */
		
        
        $this->assign("pays", $items);
        $this->assign("formget", $_GET);
        $this->assign("Page", $page->show('Admin'));
        $this->assign("current_page", $page->GetCurrentPage());
	}
	
	
	public function batchedit(){
		//$roletype = sp_get_current_roletype();
        //$adminid = sp_get_current_admin_id();
        //$ids = $this->getAgentids($adminid);
        //$userwhere = " id in ($ids)";
       
        $this->_getGames(0,1);
        //$this->_getAgents($userwhere);
		
        $this->display();
    }
	
	public function noagent(){
		if (IS_POST) {
			$appid = I("appid");
		    $roletype = sp_get_current_roletype();
            $adminid = sp_get_current_admin_id();
            $ids = $this->getAgentids($adminid);
            $userwhere = " id in ($ids)";
	    
	        $cid  = sp_get_current_cid();
		    $where = " NOT EXISTS (SELECT cg.id FROM ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientusers_game cg WHERE cg.agentid = a.id AND FIND_IN_SET('".$appid."',cg.appids)) ";
		
	        $where .= " AND a.role_id > 1 AND a.cid = ".$cid;
	        if(!empty($userwhere)){
	           $where .= " AND ($userwhere)";
	        }

	        $agents = M(C('AUTH_DB_NAME').'.clientusers',C('LDB_PREFIX'))
		        ->alias('a') 
		        ->where($where)
				->getField("a.id,a.user_nicename agentname", true);
            
			
		    echo json_encode($agents);
		}
	}
	
	public function agentlist(){
		if (IS_POST) {
			$appid = I("appid");
			
		    $roletype = sp_get_current_roletype();
            $adminid = sp_get_current_admin_id();
            $ids = $this->getAgentids($adminid);
            $userwhere = " a.agentid in ($ids)";
	    
	        $cid  = sp_get_current_cid();
	        $where = " find_in_set('".$appid."',a.appids) AND a.cid = ".$cid;
	        if(!empty($userwhere)){
	          $where .= " AND ($userwhere)";
	        }
		
	        $agents = M(C('AUTH_DB_NAME').'.clientusers_game',C('LDB_PREFIX'))
		        ->alias('a')
		        ->join("INNER JOIN ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientusers cl on a.agentid = cl.id")
		        ->where($where)
				->getField("cl.id,cl.user_nicename agentname", true);

		    echo json_encode($agents);
		}
	}
	
	public function batchedit_post(){
		if (IS_POST) {
			$appid = I('appid');
            $to = I('to');
			$from = I('from');
			
			if($appid > 0 && count($to)>0){
				$agents = implode(",",$to);
				
				$ugamemodel = M(C('AUTH_DB_NAME')."."."clientusers_game",C('LDB_PREFIX'));
				$sql = "UPDATE ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientusers_game SET appids = CONCAT(appids,',".$appid."') WHERE agentid IN(".$agents.") AND !FIND_IN_SET('".$appid."',appids)";
				//print_r($sql);
				//exit;
				$tors = $ugamemodel ->execute($sql); 
				
				$agentlist = $ugamemodel->where("agentid IN(".$agents.")")->getField("agentid",true);
				
				$into_agent=array_diff($to,$agentlist);
				
				if(count($into_agent) > 0){
					$ugame_data['appids'] = $appid;
					$ugame_data['cid'] = sp_get_current_cid();
					$ugame_data['update_time'] = time();
					foreach($into_agent as $key=>$val){
						$ugame_data['agentid'] = $val;
					}
				    $urs = $ugamemodel->add($ugame_data);
				}
			}
			
			if($appid > 0 && count($from)>0){
				 $agents = implode(",",$from);
				 $ugamemodel = M(C('AUTH_DB_NAME')."."."clientusers_game",C('LDB_PREFIX'));
				 $sql = "UPDATE ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientusers_game SET appids = REPLACE(appids,'".$appid."','') WHERE agentid IN(".$agents.") AND FIND_IN_SET('".$appid."',appids)";
				  
				 $rs = $ugamemodel ->execute( $sql ); 
			}
			
			if($tors || $rs){
				$this->success("保存成功！");
				exit;
			}
			
		}
		$this->error("保存失败！");
		exit;
    }
}
