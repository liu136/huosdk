<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ProclamationController extends AdminbaseController{
	protected $game_model,$members_model, $daygamemodel, $where;
	
	function _initialize() {
		parent::_initialize();
		$this->agent_model = M($this->dbname.".agentlist", C('CDB_PREFIX'));
		$this->game_model = M($this->dbname.".game", C('CDB_PREFIX'));
		$this->pro_model = M($this->dbname.'.proclamation', C('CDB_PREFIX'));
        
        $roletype = sp_get_current_roletype();
		$uid = sp_get_current_admin_id();

        if ($roletype == 4) {
            $this->where = "agentid=".$uid;
        }else if($roletype == 3){
			$userids = $this->getAgentids($uid);
			$this->where = "agentid in (".$userids.") ";
		}else{
            $this->where = "1 ";
        }
	}
	
	function index(){
		 $where_ands = array();
		  
        $fields = array(
                'start_time' => array(
                        "field" => "a.create_time", 
                        "operator" => ">" 
                ), 
                'end_time' => array(
                        "field" => "a.create_time", 
                        "operator" => "<" 
                ), 
				'appid'=>array(
						"field" => "a.appid", 
                        "operator" => "=" 
				),
                'title' => array(
                        "field" => "a.title", 
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
                    
                    if ('start_time' == $param) {
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
		$where =" a.isdel='1' ";
		array_push($where_ands, $where);
        $where = join(" and ", $where_ands); 
		
        $count = $this->pro_model
        ->alias("a")    
        ->where($where)
        ->count();
		
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $page = $this->page($count, $rows);
		
		 $field = "a.id, a.appid,  a.title,a.content, a.popup, a.agentname,a.start_time,a.end_time,a.create_time ,g.gamename";
        
        $items = $this->pro_model
        ->alias("a")
        ->field($field)
        ->where($where)
	    ->join("left join $this->dbname." . C('CDB_PREFIX') . "game g ON a.appid = g.appid")
        ->order("a.id DESC")
        ->limit($page->firstRow . ',' . $page->listRows)
        ->select();   

        $this->assign("proclamations", $items);
        $this->assign("formget", $_GET);
        $this->assign("Page", $page->show('Admin'));
        $this->assign("current_page", $page->GetCurrentPage());
		$this->getgames();
	    $this->display();
	}
	function addproclamation(){
		$id = I('id');
		$where ="id='".$id."'";
		$pmation = $this->pro_model->where($where)->find();
		$this->assign("pmation",$pmation);
		$this->getgames();
		$this->display();
	}
	public function addagent(){
		
	
		$id=I('id');
		if(empty($id)){
			$this->error("未选择公告，请重新操作");
			exit;
		}
		$field = "a.id, a.appid,  a.title,a.content, a.popup, a.agentname,a.start_time,a.end_time,a.create_time ,g.gamename";
		$where ="a.id='".$id."'";
		 $pmation = $this->pro_model
        ->alias("a")
        ->field($field)
        ->where($where)
	    ->join("left join $this->dbname." . C('CDB_PREFIX') . "game g ON a.appid = g.appid")
        ->find();  
		
		$pid_array = explode(",", $pmation['agentname']);
		
	
		
		$where ="appid ='".$pmation['appid']."'";
		$field = "id ,agentnicename,agentgame,agentname,agentid";
		$agentlist=$this->agent_model ->field($field)->where($where)->select();
		foreach ($agentlist as $key => $val) {
				if(in_array($val['agentname'],$pid_array)) {
					$agentlist[$key]['ischeack'] ='1';
				}else{
					$agentlist[$key]['ischeack'] ='0';
				}
        }
	
		$this->assign("agentlist",$agentlist);
		$this->assign("pmation",$pmation);
		$this->display();
		
	}
	function getgames(){
		$game_src=$this->game_model->where("ratestatus=3")->order("id desc")->select();

		$games=array();
		foreach ($game_src as $g){
			$appid=$g['appid'];
			$games["$appid"]=$g;
		}
		$this->assign("games",$games);
	}
	public function addproc_post(){
		
		$id=I('pid');
		$appid=I('gameid');
		
		$proc_data['appid'] = I('appid');
		$proc_data['title'] = I('title');
		$proc_data['content'] = I('content');
		$proc_data['start_time'] = strtotime(I('start_time'));
		$proc_data['end_time'] = strtotime(I('end_time'));
		

		if(empty($proc_data['appid']) || empty($proc_data['title']) || empty($proc_data['content'])
				|| empty($proc_data['start_time']) || empty($proc_data['end_time'])){
				$this->error("请填写完数据后再提交");
				exit;
		}
		if(empty($id)){
			$proc_data['create_time'] = time();
			$rs=$this->pro_model->add($proc_data);
			if($rs){
				$this->success("添加成功!", U("Proclamation/index"));
				exit;
			}else{
				$this->error("添加失败");
				exit;
			}
		}else{
			if($appid != $proc_data['appid']){
					$proc_data['agentname'] = "";
			}
		
			$where="id = '".$id."'";
			$rs=$this->pro_model->where($where)->save($proc_data);
			
			if($rs){
				$this->success("更新成功!", U("Proclamation/index"));
				exit;
			}else{
				$this->error("更新失败");
				exit;
			}
		}
		
		
	}
	
	public function genprocl(){
		$id=I('id');
		$proc_data['popup'] ="1";
		$where="id = '".$id."'";
		$rs=$this->pro_model->where($where)->save($proc_data);
		
		if($rs){
			$this->success("生成成功!", U("Proclamation/index"));
			exit;
		}else{
			$this->error("公告已显示或生成失败");
			exit;
		}
	}
	public function delprocl(){
		$id=I('id');
		
		$where="id = '".$id."'";
		$rs=$this->pro_model->where($where)->delete();
		
		if($rs){
			$this->success("删除成功!", U("Proclamation/index"));
			exit;
		}else{
			$this->error("删除失败");
			exit;
		}
	}
	
	public function addagent_post(){
		$id=I('pid');
		$checkdata=I('agentname');
		$checkdata=implode(",",$checkdata);
		$proc_data['agentname'] =$checkdata;
		$where="id = '".$id."'";
		$rs=$this->pro_model->where($where)->save($proc_data);
		
		if($rs){
			$this->success("添加成功!", U("Proclamation/index"));
			exit;
		}else{
			$this->error("添加失败");
			exit;
		}
		exit;
	}
}