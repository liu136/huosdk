<?php 
/*
**游戏管理
**/

namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class GameController extends AdminbaseController {
	
	protected $game_model,$lmgame_model,$status_model,$client_model,$logoweb;

    function _initialize() {
        parent::_initialize();
		{
		    if(2 < sp_get_current_roletype()){
		        $this->error('无权限访问');
		    }
		}
		$this->lmgame_model = D("Common/Game");
        $this->game_model = M($this->dbname.".game", C('CDB_PREFIX'));
		$this->status_model = M(C('MNG_DB_NAME').".status", C('LDB_PREFIX'));
		$this->client_model = M(C('MNG_DB_NAME').".client", C('LDB_PREFIX'));
		$this->logoweb = DAMAIIMGSITE."/".C('IMGDIR')."/gamelogo/";
		
    }

	/**
	 * 游戏列表
	 */
	public function index(){
		$type = 1;
		$this -> _gList($type);
		$this -> _cparatestatus();
		$this -> _cparatestatus(1);
		$this -> display();
	}

	/**
	 * CPA列表
	 */
	public function cpaindex(){	
		$type = 2;
		$this -> _gList($type);
		$this -> _cparatestatus();
		$this -> display();
	}
	
	/**
	 * 游戏列表
	 */
	public function _gList($type){	    
		$where_ands = array();
		if ($type == 1){
		    array_push($where_ands, "ratestatus > 0");
		}else{
		    array_push($where_ands, "cpastatus > 0");
		}
		
	    $fields = array(
            'gamename' => array(
                    "field" => "a.gamename",
                    "operator" => "="
            ),
            'ratestatus' => array(
                    "field" => "a.ratestatus",
                    "operator" => "="
            ),
            'cpastatus' => array(
                    "field" => "a.cpastatus",
                    "operator" => "="
            )
	    );
	    
	    if (IS_POST) {
	        $_POST['cid'] = sp_get_current_cid();
	        foreach ($fields as $param => $val) {
	            if (isset($_POST[$param])&& !empty($_POST[$param])) {
	                $operator = $val['operator'];
	                $field = $val['field'];
	                $get = trim(I("post.$param"));
	                $_GET[$param] = $get;
	    
	                if ($operator == "like") {
	                    $get = "%$get%";
	                }
	                array_push($where_ands, "$field $operator '$get'");
	            }
	        }
	    }else{
	        $_GET['cid'] = sp_get_current_cid();
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
	    
	    $where= join(" AND ", $where_ands);

		 if($_GET['split']==1){
			$order='s.rate desc,a.id DESC';
		}else if($_GET['Split']==2){
			$order='s.rate asc,a.id DESC';
		}else{
			$order='a.id DESC';
		}
		
		if($_GET['split']){
			if($_GET['split']==1){
				$_GET['split']=2;
			}else{
				$_GET['split']=1;
			}
			$arr=$_GET;
		}else{
			$_GET['split']=1;
			$arr=$_GET;
		
		}
		$url=u('Game/index',$arr);
		
	    $count= $this->game_model
		->alias("a") 
	    ->where($where)
	    ->count();
		
	    $rows = $this->row;
	    $page = $this->page($count, $rows);
		
		$field = "a.appid, a.gamename, s.rate,CONCAT('".$this->logoweb."',a.icon) as logo,a.type,scpa.servicestatus as severstatus,g.status as gamestatus,
		s.status as ratestatus,a.cpastatus, a.ratefilename,g.initial,ge.gid,ge.ghid,scpa.rate_desc";
        
		$cid = sp_get_current_cid();

	    $items=$this->game_model
		->alias("a") 
		->join("INNER JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g ON g.id = a.appid")
		->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext ge ON ge.gid = g.id")
		->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client_serverorder s ON (s.gid = g.id AND s.cid = $cid)")
		->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."serverorder scpa ON (scpa.gid = g.id)")
	    ->field($field)
	    ->where($where)
	    ->limit($page->firstRow . ',' . $page->listRows)
	    ->order($order)
		->select();
	  
		$this->assign("games",$items);
		$this->assign("downurl",DAMAIDOWNSITE."/sdkgame");
		$this->assign("formget", $_GET);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("split", $url);
	}

	/**
	 * 大麦游戏列表
	 */
	public function lmgame(){
	    $type = 1;
		$this -> _lmList($type);
		$this -> _gameclass();
		$this -> display();
	}

	/**
	 * 游戏详情
	 */
	public function gameinfo(){	
		$id = I('id');
		$clientid = 0;
		
		$field = "a.id, a.gamename, a.create_time, a.cid, a.status,e.class,CONCAT('".$this->logoweb."',e.icon) as logo,e.content,e.version,scps.rate,scpa.cpaprice";
		
		$game=$this->lmgame_model
		->alias("a") 
		->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext e ON e.gid = a.id")
		->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."serverorder scps ON (scps.gid = a.id AND scps.typeid = 1)")
		->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."serverorder scpa ON (scpa.gid = a.id AND scpa.typeid = 2)")
		->field($field)
		->where(array('a.id'=>$id))
		->find();
		
		$gtypemodel=M(C('MNG_DB_NAME').".gametype", C('LDB_PREFIX'));
		$typelist = $gtypemodel -> where(array("status"=>1)) ->select();

		$classmodel=M(C('MNG_DB_NAME').".gameclass", C('LDB_PREFIX'));
		$classlist = $classmodel -> where(array("status"=>1)) ->select();
		
		$this->assign("classlist",$classlist);
		$this->assign("typelist",$typelist);
		$this->assign("game",$game);

		$this -> display();
	}
	
	/**
	* 游戏类型
	* @date: 2016年3月3日下午2:31:40
	* @since 1.0
	*/	
	public function _gameclass(){
	    $cates=array(
	            "0"=>"全部"
	    );
	    $classes = M(C('MNG_DB_NAME').'.gameclass',C('LDB_PREFIX'))->where(array('status'=>1))->getField("id,classname class");
	    if($classes){
	        $classes=array_merge($cates,$classes);
	    }else{
	        $classes=$cates;
	    }
	    $this->assign("gameclass",$classes);
	}
	
	/**
	 * 服务状态
	 * @date: 2016年3月3日下午2:31:40
	 * @since 1.0
	 */
	public function _cparatestatus($type = 0){	 
	    $cates=array(
	            "0"=>"全部"
	    );
		if($type == 0){
			$cparatestatus = M(C('MNG_DB_NAME').'.status',C('LDB_PREFIX'))->where("`status`=1 AND `typeid`=2 AND `sequence`>0")->order('sequence')->getField("sequence,statusname statusname");
		}else if($type == 1){
			$cparatestatus = M(C('MNG_DB_NAME').'.status',C('LDB_PREFIX'))->where("`status`=1 AND `typeid`=".$type." AND `sequence`>0")->order('sequence')->getField("sequence,statusname statusname");
		}
	    
	    if($cparatestatus){
	        $cparatestatus=$cates+$cparatestatus;
	    }else{
	        $cparatestatus=$cates;
	    }
		if($type == 0){
			$this->assign("cparatestatus",$cparatestatus);
		}else if($type == 1){
			$this->assign("gamesstatus",$cparatestatus);
		}
	    
	}

    /**
	 * 游戏列表
	 */
	public function _lmList($type = NULL){
	    $where_ands = array();	    
	    array_push($where_ands, "(s.gid>61839 AND (g.status BETWEEN 2 AND 3) AND ge.ghid>0)");

		array_push($where_ands, "g.is_show = 1");
	    if (1==$type || 2==$type){
	        array_push($where_ands, " s.typeid=$type ");
	    }
	    $fields = array(
	            'gamename' => array(
	                    "field" => "g.gamename",
	                    "operator" => "like"
	            ),
	            'gclass' => array(
	                    "field" => "ge.class",
	                    "operator" => "="
	            )	            
	    );
	    
	    if (IS_POST) {
	        foreach ($fields as $param => $val) {
	            if (isset($_POST[$param])&& !empty($_POST[$param])) {
	                $operator = $val['operator'];
	                $field = $val['field'];
	                $get = trim(I("post.$param"));
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
	    
	    $where= join(" AND ", $where_ands);
		
	    $model = M(C('MNG_DB_NAME').'.serverorder',C('LDB_PREFIX'));
	    
	    $count = $model->alias('s')
                	   ->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g ON s.gid = g.id")
                	   ->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext ge ON ge.gid = s.gid")
                	   ->where($where)
                	   ->count();
		
	    $rows = $this->row;
	    $page = $this->page($count, $rows);
		
		$field = "g.id, g.gamename, g.create_time, s.cid, ge.ghid ,ge.ghnewid,g.status,s.servicestatus,g.type,s.cpaprice,s.rate,ge.class, CONCAT('".$this->logoweb."',ge.icon) as logo,s.rate_desc";
	    $items=$model->alias('s')
	    ->field($field)
	    ->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g ON s.gid = g.id")
        ->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext ge ON ge.gid = s.gid")
	    ->where($where)
	    ->limit($page->firstRow . ',' . $page->listRows)
	    ->order("s.create_time DESC")
		->select();

		$this->assign("games",$items);
		$this->assign("formget", $_GET);
		$this->assign("Page", $page->show('Admin'));
	}
	
	//联运协议
	public function contractinfo() {
		$type = I('type');
		$id = I('id');
		
		$this->assign("id",$id);
		$this->assign("type",$type);
		$this -> display();
	}
	
	//添加联运游戏
	public function lmgameAdd() {
		$appid = intval(I('post.id'));
		$type = intval(I('post.type'));		
		$contratstatus = intval(I('post.contratstatus'));
		
		//必须同意合同才能操作
		if (1 != $contratstatus) {
		    $this->error("请同意合同");
		}
		
        if (!empty($appid) && $appid > 0){
			$cid = sp_get_current_cid();
			
			/* 1 先判断是否已经引入过此游戏  */
			$csomodel = M(C('MNG_DB_NAME').'.client_serverorder',C('LDB_PREFIX'));
			$where = array(
			        'cid'=>$cid,
			        'gid'=>$appid,
			        'typeid'=>$type
			);
			
			$field = "id, status";
			$sodata = $csomodel->field($field)->where($where)->find();
			
			/* 2.1 引入过游戏则提示现在游戏的状态  */
			if(!empty($sodata)){
			    /* 1待签署 2待审核 3签署成功 4审核不通过*/
			    if (3 == $sodata['status']){
			        $this->success("该游戏已申请通过", U('Game/index'));
			    } elseif (4 == $sodata['status']){
			        $this->success("审核不通过,请联系管理员", U('Game/index'));
			    } else {
			        $this->success("申请中，等待审核", U('Game/index'));
			    }
			}else{
			    /* 2.2 未引入过游戏则插入游戏  */
			    /* 3 游戏库中获取获取此游戏信息 */
			    $lmsomodel = M(C('MNG_DB_NAME').'.serverorder',C('LDB_PREFIX'));
			    $gamefield = "g.id appid, g.gamename, ge.class, ge.icon, s.rate, s.c_rate, s.cpaprice, s.starttime,s.endtime";
			    $lmsowhere = array(			            
                        's.gid'=>$appid,
			            's.typeid'=>$type
			    );
			    $gamedata = $lmsomodel->alias('s')
			    ->field($gamefield)
			    ->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g ON s.gid = g.id")
			    ->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext ge ON ge.gid = s.gid")
			    ->where($lmsowhere)
			    ->find();
				
			    /* 获取信息为空  信息错误 */
			    if(empty($gamedata)){
			        $this->error('游戏游戏错误', U('Game/lmgame'));
			    }
			    
			    /* 还未放出的游戏不能引入 */
  			    if ($gamedata['start_time'] > $time){
  			        $this->error('游戏无法引入', U('Game/lmgame'));
  			    }
			    
			    $time = time();	
			    $gamedata['create_time'] = $time;
			    $gamedata['ratestatus'] = 2;
				$gamedata['cid'] = $cid;
			    
			    $sodata['start_time'] = $time;
			    $sodata['end_time'] = $gamedata['endtime'];			    
			    $sodata['status'] = $gamedata['ratestatus'];
				
			    /* 4 渠道库中插入游戏信息 */
			    if (1 == $type){
			        /* 分成比例 */
			        if (-1 == bccomp($gamedata['rate'], $gamedata['c_rate'], 4) ){
			            $gamedata['rate'] = $gamedata['c_rate'];
			        }
			        $sodata['rate'] = $gamedata['rate'];
			        unset($gamedata['cpaprice']);
			        unset($gamedata['c_rate']);
			    }else if(2 == $type){
			        $sodata['cpaprice'] = $gamedata['cpaprice'];
			        unset($gamedata['rate']);
			    }else{
			        $this->error("非法输入");
			    }
				
			    /* 5渠道接入游戏服务单 */
			    $gamers = $this->game_model->add($gamedata);
			    if ($gamers) {
			        $sodata['cid'] = $cid;
			        $sodata['gid'] = $appid;
			        $sodata['typeid'] = $type;			        
			        $sodata['create_time'] = $time;
					
			        $csomodel = M(C('MNG_DB_NAME').'.client_serverorder',C('LDB_PREFIX'));
		            $rs = $csomodel->add($sodata);
		            if ($rs){
						$lm_game = M(C('MNG_DB_NAME').".clientgame", C('LDB_PREFIX'));
						$lmdata['cid'] = $sodata['cid'];
						$lmdata['gid'] = $sodata['gid'];
						$lmdata['ratestatus'] = $gamedata['ratestatus'];
						$lmdata['rate'] = $gamedata['rate'];
						$lmdata['create_time'] = $gamedata['create_time'];

						$lm_game->add($lmdata);
                       
		                $this->insertLog(1, "引入游戏$appid");
		                $this->success('引入游戏成功，等待审核.',U('Game/index'));
			        }else{
			            $this->error('引入失败.');
			        }
			    }
			}
	   }
	}

	/**
	 **游戏下拉列表
	 **/
	 public function gameCombobox(){
		$gamelist1[0]['appid'] = 0;
		$gamelist1[0]['name'] = '请选择游戏名称'; 

		$gamemodel = $this->game_model;
		$gamelist2 = $gamemodel->field("appid,name")->select();
		
		$gamelist = array_merge($gamelist1,$gamelist2);
		echo json_encode($gamelist);
	 }


	 /**
	 **服务单详情
	 **/
	 public function contractdetail(){
		$gid = I("gid");
		$typeid = I("typeid");
		$cid = sp_get_current_cid();
		
		$server_model =  M(C('MNG_DB_NAME').".client_serverorder", C('LDB_PREFIX'));

		$server = $server_model
		->alias("cs") 
		->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g ON g.id = cs.gid")
		->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c ON c.id = cs.cid")
		->field("cs.*,g.gamename,c.companyname,c.addr,c.linkman,c.link_tel")
		->where(array('cs.gid'=>$gid,'cs.cid'=>$cid,"cs.typeid"=>$typeid))
		->find();
		
// 		if(empty($server)){
// 			$this->error('未查询到服务单.');
// 		}
		
// 		$finance_model = M(C('MNG_DB_NAME').".finance", C('LDB_PREFIX'));
// 		$finance = $finance_model->where(array('cid'=>1))->find();//获取财务信息
		$this->assign("damaconmpany",DAMACOMPANY);
		$this->assign("server",$server);
		$this->assign("finance",$finance);
		$this -> display();
	 } 
	 
	 /**
	  * 推广中心的游戏列表
	  * 
	  * */
	 public function extgamelist(){
	     $count = $this->game_model->field("id")->count();
	     $page = $this->page($count, 20);
	     $gameList = $this->game_model->field("id,gamename")->limit($page->firstRow . ',' . $page->listRows)->select();
	     //dump($gameList);
	     $this->assign('gameList',$gameList);
	     $this->assign("page", $page->show('Admin'));
	     $this->display();
	     
	 }
	 
	 
	 
	 
	 
	 
	 
}
