<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class AnalysisController extends AdminbaseController{
	protected $game_model,$members_model, $daygamemodel,$agentlist_model, $where,$userwhere,$agentwhere;
	
	function _initialize() {
		
		parent::_initialize();
        $this->members_model = M($this->dbname.".members", C('CDB_PREFIX'));
		$this->game_model = M(C('MNG_DB_NAME').".game", C('LDB_PREFIX'));
		$this->daygamemodel = M($this->dbname.'.mgameinfo', C('CDB_PREFIX'));
		$this->agentlist_model = M($this->dbname.'.agentlist', C('CDB_PREFIX'));
        
        $roletype = sp_get_current_roletype();
		$uid = sp_get_current_admin_id();
        
		if ($roletype > 2) {
			$userids = $this->getAgentids($uid);
            $this->where = "agentid in (".$userids.") ";
            
		    $agents = $this->agentlist_model->where($this->where)->field('agentgame')->select();
			$agent_str = array();
			foreach($agents as $key=>$val){
               array_push($agent_str,$val['agentgame']);
			}
		    $agentgames = "'".implode("','",$agent_str)."'";
        
			$this->userwhere = " (parentid=$uid OR id=$uid) ";
		    $this->agentwhere = " a.agentgame in ($agentgames) ";
			
        }else{
            $this->agentwhere = "1 ";
			$this->where = "1";
        }
		
	}
	
	function newuser(){
		$username = I('username');
		$start_time = I('start_time');
		$end_time = I('end_time');
		$agent_id = I('agent_id');
		
		$uid = sp_get_current_admin_id();
		$roletype = sp_get_current_roletype();
		$model = $this->agentlist_model;
	    //if($agent_id){
	   //       $agent_role = $model->where('agentid = '.$agent_id)->getField('roleid');
	   // }
		
		$where = $this->agentwhere;
		$where_arr = array();
		if (!empty($username) && $username != '') {
			$where .= " AND a.username like '%s'";
			$sqlname = "%{$username}%";
			$_GET['username'] = $username;
			array_push($where_arr,$sqlname);
		}
		
		if(!empty($agent_id) && $agent_id != ''){
		    $where .= " AND ag.agentid = '%d'";
		    $_GET['agent_id'] = $agent_id;
		    array_push($where_arr,$agent_id);
		}

	   if (!empty($start_time) && $start_time != '') {
		    $tmp = $start_time . " 00:00:00";
		    $start = strtotime($tmp);
		    $where .= " AND a.reg_time >= '%s'";
		    $_GET['start_time'] = $start_time;
		    array_push($where_arr,$start);
		}
		
		if (!empty($end_time) && $end_time != '') {
		    $tmp = $end_time . " 23:59:59";
		    $end = strtotime($tmp);
		    $where .= " AND a.reg_time <= '%s'";
		    $_GET['end_time'] = $end_time;
		    array_push($where_arr,$end);
		}
        
		if($this->where != "1"){
			$where .= " AND ag.".$this->where;
		}
		
		$count=$this->members_model
		->alias('a')
		->field('a.*,ag.agentid,ag.agentname,ag.agentnicename,g.gamename')
		->join("left join ".$this->dbname.".". C('CDB_PREFIX') . "agentlist ag ON ag.agentgame = a.agentgame")
		->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX') . "game g ON g.id = a.appid")
		->where($where,$where_arr)
		->count();
		
		$page = $this->page($count, 20);
		
		$members = $this->members_model
		->alias('a')
		->join("left join ".$this->dbname.".". C('CDB_PREFIX') . "agentlist ag ON ag.agentgame = a.agentgame")
		->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX') . "game g ON g.id = a.appid")
		->field('a.*,ag.agentid,ag.agentname,ag.agentnicename,g.gamename')
		->where($where,$where_arr)
		->order("a.reg_time DESC")
		->limit($page->firstRow . ',' . $page->listRows)
		->select();
    
		$this->_getAgentsUser($this->userwhere);
		
		$this->assign("page", $page->show('Admin'));
		$this->assign("members",$members);
// 		$this->assign("games",$games);
		$this->assign("formget", $_GET);
		$this->display();
		
	}

    
	function userpay(){
        $this->_getGames();
        $this->_getgamedata();
        $this->display();
	}
	
	function _getgamedata(){
       $model = M($this->dbname.'.dayagentgame', C('CDB_PREFIX'));

        $where = $this->where;
		$where_ands = array();
        
        $fields = array(
                'start_time' => array(
                        "field" => "date", 
                        "operator" => ">=" 
                ), 
                'end_time' => array(
                        "field" => "date", 
                        "operator" => "<=" 
                ), 
                'gid' => array(
                        "field" => "appid", 
                        "operator" => "=" 
                )
        );
        
        if ('今日' == $_POST['date_time']) {            
            $count = 1;
            $start = date("Y-m-d");
            $end  = date("Y-m-d");
        } elseif ('七日' == $_POST['date_time']) {
			
            $_POST['start_time'] = date("Y-m-d",strtotime("-6 day"));
            $_POST['end_time'] = date("Y-m-d",time());
            $count = 7;
            $start = date("Y-m-d",strtotime("-6 day"));
            $end  = date("Y-m-d");
        } elseif ('当月' == $_POST['date_time']) {
            $count = date('d');
			$_POST['start_time'] = date("Y-m-01");
            $_POST['end_time'] = date("Y-m-d",time());
            $start = date("Y-m-01");
            $end  = date("Y-m-d");
        } elseif ('30天' == $_POST['date_time']) {
            $count = 30;
			$_POST['start_time'] = date("Y-m-d",strtotime("-29 day"));
            $_POST['end_time'] = date("Y-m-d");
            $start = date("Y-m-d",strtotime("-29 day"));
            $end  = date("Y-m-d");
        }else{
            $start = date($_POST['start_time']);
            $end = date($_POST['end_time']);
        }
        
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
		
        array_push($where_ands, $this->where);
        $where = join(" and ", $where_ands);
        $where_query = $where ? ' where '.$where : '';

        if($_POST['date_time'] == '导出exl'){
            $this->gamedata($where);
        }
        
        $bflagstart = true;
        $bflagend = true;
        if (isset($start) && !empty($start)) {
            $bflagstart = strtotime($start) <= mktime(0,0,0,date("m"),date("d"),date("Y"))? true : false;
        }  
        
        if (isset($end) && !empty($end)) {
             $bflagend = strtotime($end) >= mktime(0,0,0,date("m"),date("d"),date("Y"))? true : false;
        }
        
        if ($bflagstart){
		
            
            $cquery = "select * from (SELECT date,appid,SUM(usercnt) usercnt,SUM(summoney) summoney,SUM(paycnt) paycnt,SUM(regpaycnt) regpaycnt,SUM(sumregmoney) sumregmoney,SUM(reg_cnt) reg_cnt
                    from ".$this->dbname.".".C('CDB_PREFIX')."dayagentgame
                    where ".$where."
                    GROUP BY date,appid
                    UNION
                    select date,appid,SUM(usercnt) usercnt,SUM(summoney) summoney,SUM(paycnt) paycnt,SUM(regpaycnt) regpaycnt,SUM(sumregmoney) sumregmoney,SUM(reg_cnt) reg_cnt
                    from ".$this->dbname.".".C('CDB_PREFIX')."tagentgamepayview
                    where ".$where."
                    GROUP BY date,appid
                    ORDER BY date desc)a ";
            $citems = M()->query($cquery);
            $count=count($citems);
        }else{
            $count = 1;
        }
        
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
		
        $page = $this->page($count, $rows);
        
        if(!empty($_GET['byorder'])){
            $order = $_GET['byorder'] == 1?"order by summoney asc,date desc":"order by summoney desc,date desc";
        }else{
            $order = "ORDER BY date desc";
        }
        if ($bflagstart) {  
            $sumfield = "date,SUM(`usercnt`) `usercnt`,SUM(`summoney`) `summoney`,SUM(`paycnt`) `paycnt`,SUM(`regpaycnt`) `regpaycnt`,SUM(`sumregmoney`) `sumregmoney`,SUM(`reg_cnt`) `reg_cnt`";

            $sql = "select ".$sumfield." from (SELECT date,appid,SUM(usercnt) usercnt,SUM(summoney) summoney,SUM(paycnt) paycnt,SUM(regpaycnt) regpaycnt,SUM(sumregmoney) sumregmoney,SUM(reg_cnt) reg_cnt
                    from ".$this->dbname.".".C('CDB_PREFIX')."dayagentgame
                    where ".$where."
                    GROUP BY date,appid
                    UNION
                    select date,appid,SUM(usercnt) usercnt,SUM(summoney) summoney,SUM(paycnt) paycnt,SUM(regpaycnt) regpaycnt,SUM(sumregmoney) sumregmoney,SUM(reg_cnt) reg_cnt 
                    from ".$this->dbname.".".C('CDB_PREFIX')."tagentgamepayview 
                    where ".$where."
                    GROUP BY date,appid )a";
            
            $sumitems = M()->query($sql);
			
            $limit = " limit " . $page->firstRow . ',' . $page->listRows;
            $query = "select * from (SELECT date,appid,SUM(usercnt) usercnt,SUM(summoney) summoney,SUM(paycnt) paycnt,SUM(regpaycnt) regpaycnt,SUM(sumregmoney) sumregmoney,SUM(reg_cnt) reg_cnt
                    from ".$this->dbname.".".C('CDB_PREFIX')."dayagentgame
                    where ".$where."
                    GROUP BY date,appid
                    UNION
                    select date,appid,SUM(usercnt) usercnt,SUM(summoney) summoney,SUM(paycnt) paycnt,SUM(regpaycnt) regpaycnt,SUM(sumregmoney) sumregmoney,SUM(reg_cnt) reg_cnt 
                    from ".$this->dbname.".".C('CDB_PREFIX')."tagentgamepayview 
                    where ".$where."
                    GROUP BY date,appid )a 
                    ".$order." ".$limit;
            $items = M()->query($query);
        }
	    
        $this->assign("totalpays", $sumitems);
        $this->assign("pays", $items);
		$this->assign("formget", $_GET);
        $this->assign("Page", $page->show('Admin'));
        $this->assign("current_page", $page->GetCurrentPage());
    }
	
	function userdevice(){
		$username = I('username');
		$start_time = I('start_time');
		$end_time = I('end_time');
		$uid = sp_get_current_admin_id();
		$roletype = sp_get_current_roletype();
		$model = M($this->dbname.'.agentlist', C('CDB_PREFIX'));

		$where = $this->agentwhere;
        
		 $where_ands = array();
		 $fields = array(
                'username' => array(
                        "field" => "m.username", 
                        "operator" => "like" 
                ),
			    'start_time' => array(
                        "field" => "a.reg_time", 
                        "operator" => ">" 
                ), 
                'end_time' => array(
                        "field" => "a.reg_time", 
                        "operator" => "<" 
                )
        );

		 if (IS_POST) {
            foreach ($fields as $param => $val) {                
                if (isset($_POST[$param]) && !empty($_POST[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $get = trim($_POST[$param]);
                    $_GET[$param] = $get;   
                   
				    if ('start_time' == $param) {
                        $get .= " 00:00:00";
                        $get = strtotime($get);
                    } else if ('end_time' == $param) {
                        $get .= " 23:59:59";
                        $get = strtotime($get);
                    } 

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
                    
					if ('start_time' == $param) {
                        $get .= " 00:00:00";
                        $get = strtotime($get);
                    } else if ('end_time' == $param) {
                        $get .= " 23:59:59";
                        $get = strtotime($get);
                    } 

                    if ($operator == "like") {
                        $get = "%$get%";
                    }                    
                    array_push($where_ands, "$field $operator '$get'");
                }
            }  
        }
        
		
		array_push($where_ands, $where);
        $where = join(" AND ", $where_ands); 
        
		$login_model = M($this->dbname.".logininfo", C('CDB_PREFIX'));
        $Model = new \Think\Model();
        $rs = $Model->query("SELECT COUNT(*) as count FROM (SELECT a.id FROM ".$this->dbname.".".C('CDB_PREFIX')."logininfo a 
		left join $this->dbname." . C('CDB_PREFIX') . "members m ON a.userid = m.id where ".$where." GROUP BY a.userid,a.appid) a");
        
		$count = $rs[0]['count'];

		$page = $this->page($count, 20);

		$field = "a.appid, g.gamename,m.username, a.login_time, m.appid as reggid";
		$members = $login_model
			->alias("a")
			->join("left join $this->dbname." . C('CDB_PREFIX') . "members m ON a.userid = m.id")
			->join("left join ".C('MNG_DB_NAME') .".". C('LDB_PREFIX') . "game g ON g.id = a.appid")
			->field($field)
		    ->where($where)
		    ->group(" a.userid,a.appid")
			->order(" a.userid desc")
		    ->limit($page->firstRow . ',' . $page->listRows)
		    ->select();
		//游戏信息
		//$this->_getGames();
		
		$this->assign('formget',$_GET);
		$this->assign("page", $page->show('Admin'));
		$this->assign("members",$members);
	    $this->display();
	}
	
	/////
	
	/////
	function restindex(){
	    $this->display();
	}
	
	function payindex(){
	    $this->display();
	}

	 public function usergame() {
		$this->_getGames();
		$this->_getRolegamedata();
        $this->display();
    }
     /*
     * 玩家游戏数据记录表
     */
    function _getRolegamedata(){
		
		$username = I('username');
		$role = I('role');
		$gid=I('gid');
		
		$model = $this->daygamemodel;
		
		$where = $this->where;
		
		$where_arr = array();
		
		 $fields = array(
                'username' => array(
                        "field" => "e.username", 
                        "operator" => "=" 
                ), 
                'role' => array(
                        "field" => "a.role", 
                        "operator" => "=" 
                ), 
                'gid' => array(
                        "field" => "a.appid", 
                        "operator" => "=" 
                 ),
				'service' => array(
                        "field" => "a.service", 
                        "operator" => "=" 
                ),
				'start_time' => array(
                        "field" => "e.reg_time", 
                        "operator" => ">" 
                ), 
                'end_time' => array(
                        "field" => "e.reg_time", 
                        "operator" => "<" 
                )
				
        );
		if (IS_POST) {
            foreach ($fields as $param => $val) {                
                if (isset($_POST[$param]) && !empty($_POST[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $get = trim($_POST[$param]);
					$_GET[$param] = $get;   
						if ('start_time' == $param) {
                        $get .= " 00:00:00";
                        $get = strtotime($get);
                    } else if ('end_time' == $param) {
                        $get .= " 23:59:59";
                        $get = strtotime($get);
                    } 
                  
                   
                    array_push($where_arr, "$field $operator '$get'");
                }
            }  
        }else{
            foreach ($fields as $param => $val) {                
                if (isset($_GET[$param]) && !empty($_GET[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $get = trim($_GET[$param]);
					$_GET[$param] = $get;   
                    if ('start_time' == $param) {
                        $get .= " 00:00:00";
                        $get = strtotime($get);
                    } else if ('end_time' == $param) {
                        $get .= " 23:59:59";
                        $get = strtotime($get);
                    } 
				  
                    array_push($where_arr, "$field $operator '$get'");
                }
            }  
        }
		
		array_push($where_arr, $where);
        $where = join(" and ", $where_arr);
		
		//$count=$this->daygamemodel->where($where,$where_arrs)->count();
		$field = "a.id, a.cid, e.username,e.reg_time, a.appid, a.service, a.role, a.grade,a.update_time,g.gamename";
		$count = $this->daygamemodel
		->alias("a")
		->where($where)
		->join("left join $this->dbname." . C('CDB_PREFIX') . "members e ON a.userid = e.id")
		->join("left join ".C('MNG_DB_NAME')."." . C('LDB_PREFIX') . "game g ON a.appid = g.id")
		->join("left join $this->dbname." . C('CDB_PREFIX') . "agentlist d ON e.agentgame = d.agentgame")
		->count();
	 
		$page = $this->page($count, 15);

		$rolegame = $this->daygamemodel
		->alias("a")
        ->field($field)
		->where($where)
		->join("left join $this->dbname." . C('CDB_PREFIX') . "members e ON a.userid = e.id")
		->join("left join ".C('MNG_DB_NAME')."." . C('LDB_PREFIX') . "game g ON a.appid = g.id")
		->join("left join $this->dbname." . C('CDB_PREFIX') . "agentlist d ON e.agentgame = d.agentgame")
		->order("a.id DESC")
		->limit($page->firstRow . ',' . $page->listRows)
		->select();
		
        
        if ('导出xls' == $_POST['submit']) {
	        $this->expUsergame($where);
	    }
		
		$this->assign("formget", $_GET);
		
		$this->assign("page", $page->show('Admin'));
		$this->assign("rolegame",$rolegame);
		$this->assign("current_page", $page->GetCurrentPage());
	}
	
	
	function edit(){
		$id= intval(I("get.id"));

		$member = $this->members_model->where("id=%d",$id)->find();
		$this->assign($member);
		
		$this->display();
	}
	
	function edit_post(){
		if (IS_POST) {
			$id = I("id");
			if(!empty($id) && $id > 0){
				$password = I('password');
				
				if (empty($password)) {
					$this->error("密码不能为空");
					exit();
				}
				$data['password'] =  $this->auth_code($password,'ENCODE','');
				
				if ($this->members_model->create($data)) {
					$rs = $this->members_model->where("id = %d",$id)->save();
					if($rs){
						$this->success("修改成功！", U("Member/index"));
						exit();
					}
				}
				$this->error("修改失败");
				exit();
			}else{
				$this->error("未找到玩家账号");
			}
			
		}
	}
	
	function ban(){
        $id=intval($_GET['id']);
    	if ($id) {
    		$rst = $this->members_model->where(array("id"=>$id))->setField('flag','1');
    		if ($rst) {
    			$this->success("账号冻结成功！", U("Member/index"));
    		} else {
    			$this->error('账号冻结失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
    function cancelban(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = $this->members_model->where(array("id"=>$id))->setField('flag','0');
    		if ($rst) {
    			$this->success("账号解封成功！", U("Member/index"));
    		} else {
    			$this->error('账号解封失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }

	function setptb(){
        $mid = intval($_GET['id']);
        
		$user = $this->members_model->field("id,username")->where(array("id"=>$mid))->find();

		$model = M($this->dbname.".ptbuser", C('CDB_PREFIX'));
    	$uptb = $model->field("amount")->where("userid=%d",$mid)->find();
		$user['ptb'] = $uptb['amount'];

		$this->assign("user",$user);
	    $this->display();
	}

	/**
     * 平台币手充
     */
	 function saveptb(){
		$action = I('action');
		if (isset($action) && isset($action) == 'add') {
    		$userid = I('id');
    		$ptb = I('ptb',0);
    		
    		if (empty($userid) || $ptb == 0) {
				$this->error('请填写完整参数');
    			exit;
    		}
			
			$member = $this->members_model;
    		$us = $member->where("id=%d",$userid)->find();

			if (count($us) <= 0) {
				$this->error('用户名不存在');
    			exit;
    		}

            if($us['appid'] != 62009 && $us['appid'] != 62010 && $us['appid'] != 62055){
                $this->error('发放游戏不正确');
    			exit;
			}

    		$model = M($this->dbname.".ptbuser", C('CDB_PREFIX'));
    		$check = $model->field("id")->where("userid=%d",$userid)->find();
    		if ($check) {
				$rs1 = $model->where("userid=%d",$userid)->setInc('totalcnt',$ptb); // 用户平台币加3
				$rs1 = $model->where("userid=%d",$userid)->setInc('amount',$ptb); // 用户平台币加3
				$rs2 = $model->where("userid=%d",$userid)->setField('updatetime',time());
    			$rs = $rs1;
    		} else {
				$data['userid'] = $userid;
				$data['totalcnt'] = $ptb;
				$data['amount'] = $ptb;
				$data['createtime'] = time();
    			$rs = $model->data($data)->add();
    		}
			
    		if ($rs) {
    			$this->success("发放成功！", U("Member/index"));
    		} else {
				$this->error('发放失败！');
    		}
    		exit;
    	}
    		 
	 }

	
	/**
     * 
     * 密码加密解密
     * @param $string     密码
     * @param $operation  DECODE 为解密，其他为加密
     * @param $key		     密钥
     * @param $expiry
     */
	function auth_code($string, $operation = 'DECODE', $key = '', $expiry = 0) {
		$cid = sp_get_current_cid();
		$model = M(C('MNG_DB_NAME').'.client',C('LDB_PREFIX'));
		$client = $model->where(array("id"=>$cid))->find();

		$clientkey = $client['clientkey'];
		
		$ckey_length = 0;
		
		$key = md5($key ? $key : $clientkey);
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(
				md5(microtime()), 
				-$ckey_length)) : '';
		
		$cryptkey = $keya . md5($keya . $keyc);
		$key_length = strlen($cryptkey);
		
		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf(
				'%010d', 
				$expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
		$string_length = strlen($string);
		
		$result = '';
		$box = range(0, 255);
		//$box = 100;
		
		$rndkey = array();
		for ($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}
		
		for ($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}
		
		for ($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}
		
		if ($operation == 'DECODE') {
			if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(
					md5(substr($result, 26) . $keyb), 
					0, 
					16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			return $keyc . str_replace('=', '', base64_encode($result));
		}
	}
		
	function _getuserdata(){
	    $roletype = sp_get_current_roletype();
        $adminid = sp_get_current_admin_id();
	    $where_ands = array();
	  
	    $ids = $this->getAgentids($adminid);
	    array_push($where_ands, "a.agentid in ($ids)");
	    
		/*else{
	        $where = " p.status = 1 ";
	    }*/
        $fields = array(
                'username' => array(
                        "field" => "m.username", 
                        "operator" => "like" 
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
        
        //array_push($where_ands, " p.status = 1 ");
		array_push($where_ands, " 1 ");
        $where = join(" AND ", $where_ands); 
        
        $query = "SELECT count(DISTINCT m.id) count
                FROM ".$this->dbname.".". C('CDB_PREFIX') . "members m 
                LEFT JOIN ".$this->dbname.".". C('CDB_PREFIX') . "pay p ON (p.userid = m.id AND p.status = 1 )
                LEFT JOIN ".$this->dbname.".". C('CDB_PREFIX') . "agentlist a ON m.agentgame = a.agentgame
                WHERE ( ".$where.");";
        $count_res = M()->query($query);
        $count = $count_res[0]['count'];
        
	    $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
	    $page = $this->page($count, $rows);
	    
	    
	    $field = 'm.`id`,m.`username`,m.`last_login_time`,COUNT(p.userid) AS paycou,SUM(p.amount) AS amount,MAX(p.create_time) as creat_time';
	    $items = $this->members_model
	    ->alias('m')
	    ->field($field)
	    ->join("left join ".$this->dbname.".". C('CDB_PREFIX') . "pay p ON (p.userid = m.id AND p.status = 1 )")
	    ->join("left join ".$this->dbname.".". C('CDB_PREFIX') . "agentlist a ON m.agentgame = a.agentgame")
	    ->where($where)
	    ->group('m.username')
	    ->order('amount desc')
	    ->limit($page->firstRow . ',' . $page->listRows)
	    ->select();
		
	    $this->assign("members", $items);
	    $this->assign("formget", $_GET);
	    $this->assign("Page", $page->show('Admin'));
	    $this->assign("current_page", $page->GetCurrentPage());	    
	}
	
	/**
	 * 玩家列表，导出Excel
	 */
	function expUser($where,$where_arr){//导出Excel
	    $xlsName  = "UserList";
	    $xlsCell  = array(
	        array('username','账号'),
	        array('ip','最后登录IP'),
	        array('last_login_time','最后登录时间'),
	        array('imei','注册IMEI码'),
	        array('gamename','注册游戏'),
	        array('agentnicename','注册渠道'),
	        array('reg_time','注册时间'),
	        array('flag','状态'),
	    );
	    
	    $xlsData = $this->members_model
		->alias('a')
		->join("left join ".$this->dbname.".". C('CDB_PREFIX') . "agentlist ag ON ag.agentgame = a.agentgame")
		->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX') . "game g ON g.id = a.appid")
		->field('a.*,ag.agentid,ag.agentname,ag.agentnicename,g.gamename')
		->where($where,$where_arr)
		->order("a.reg_time DESC")
		->select();
	
	    foreach ($xlsData as $k => $v)
	    {
	        $xlsData[$k]['last_login_time'] = date('Y-m-d  H:i:s',$v['last_login_time']);
	        $xlsData[$k]['reg_time'] = date('Y-m-d  H:i:s',$v['reg_time']);
	        $xlsData[$k]['flag'] = $v['flag'] == 1 ? '冻结' : '正常';
	    }
	    $this->exportExcel($xlsName,$xlsCell,$xlsData);
	     
	}
	
	/**
	 * 玩家游戏数据列表，导出Excel
	 */
	function expUsergame($where){//导出Excel
	    $xlsName  = "UsergameList";
	    $xlsCell  = array(
	        array('username','账号'),
	        array('update_time','最后登录时间'),
	        array('gamename','游戏名称'),
	        array('service','游戏区服'),
	        array('role','游戏角色'),
	        array('grade','游戏等级'),
	    );
	    
	    $field = "a.id, a.cid, e.username,e.reg_time, a.appid, a.service, a.role, a.grade,a.update_time,g.gamename";
	    $xlsData = $this->daygamemodel
		->alias("a")
        ->field($field)
		->where($where)
		->join("left join $this->dbname." . C('CDB_PREFIX') . "members e ON a.userid = e.id")
		->join("left join ".C('MNG_DB_NAME')."." . C('LDB_PREFIX') . "game g ON a.appid = g.id")
		->join("left join $this->dbname." . C('CDB_PREFIX') . "agentlist d ON e.agentgame = d.agentgame")
		->order("a.id DESC")
		->select();
	
	    foreach ($xlsData as $k => $v)
	    {
	        $xlsData[$k]['update_time'] = date('Y-m-d  H:i:s',$v['update_time']);
	        $xlsData[$k]['flag'] = $v['flag'] == 1 ? '冻结' : '正常';
	    }
	    $this->exportExcel($xlsName,$xlsCell,$xlsData);
	
	}
	
}