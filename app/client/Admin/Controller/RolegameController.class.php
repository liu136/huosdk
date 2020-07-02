<?php

/**
 * 玩家游戏数据统计页面
 * 
 * @author
 *
 */
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class RolegameController extends AdminbaseController {
    protected $daygamemodel,$where,$orderwhere,$appidwhere;
	function _initialize() {
        parent::_initialize();
        $this->daygamemodel = M($this->dbname.'.mgameinfo', C('CDB_PREFIX'));
        
        $roletype = sp_get_current_roletype();
		$uid = sp_get_current_admin_id();
        if ($roletype > 2) {
			$userids = $this->getAgentids($uid);
			$this->where = "e.agentid in (".$userids.") ";
			
		}else{
            $this->where = "1 ";
            $this->orderwhere = "1 ";
			$this->appidwhere = "1";
        }
    }
  
    public function index() {
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
                        "operator" => "like" 
                ), 
                'role' => array(
                        "field" => "a.role", 
                        "operator" => "like"
                ), 
                'gid' => array(
                        "field" => "a.appid", 
                        "operator" => "=" 
                ),
				'service' => array(
                        "field" => "a.service", 
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
					
                    array_push($where_arr, "$field $operator '$get'");
					
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
					
                    array_push($where_arr, "$field $operator '$get'");
                }
            }  
        }
		if($where != "1"){
			array_push($where_arr, $where);
		}
        $where = join(" and ", $where_arr);
		
		//$count=$this->daygamemodel->where($where,$where_arrs)->count();
		$field = "a.id, a.cid, e.username, a.appid, a.service,d.agentgame,d.agentnicename,d.agentid, a.role, a.grade,a.update_time,g.gamename";
		$count = $this->daygamemodel
		->alias("a")
        ->field($field)
		->where($where)
		->join("left join $this->dbname." . C('CDB_PREFIX') . "members e ON a.userid = e.id")
		->join("left join $this->dbname.l_game g ON a.appid = g.id")
		->join("left join $this->dbname." . C('CDB_PREFIX') . "agentlist d ON e.agentid = d.agentid")
		->count();
		
		$page = $this->page($count, 10);
		$field = "a.id, a.cid, e.username, a.appid, a.service, d.agentgame,d.agentnicename,d.agentid,a.role, a.grade,a.update_time,g.gamename";
		$rolegame = $this->daygamemodel
		->alias("a")
        ->field($field)
		->where($where)
		->join("left join $this->dbname." . C('CDB_PREFIX') . "members e ON a.userid = e.id")
		->join("left join $this->dbname.l_game g ON a.appid = g.id")
		->join("left join $this->dbname." . C('CDB_PREFIX') . "agentlist d ON e.agentid = d.agentid")
		->order("id DESC")
		->limit($page->firstRow . ',' . $page->listRows)
		->select();
       
		//游戏信息
		$this->_getGames();
		$this->assign("formget", $_GET);
		$this->assign("page", $page->show('Admin'));
		$this->assign("rolegame",$rolegame);
		$this->assign("current_page", $page->GetCurrentPage());
	}
}