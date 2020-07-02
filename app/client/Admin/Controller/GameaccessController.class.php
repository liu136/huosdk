<?php 
/*
**游戏管理
**/

namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class GameaccessController extends AdminbaseController {
	
	protected $game_model,$lmgame_model,$status_model,$client_model,$gametype_model,$gameclass_model;

    function _initialize() {
        parent::_initialize();
		
		{
		    if(2 < sp_get_current_roletype()){
		        $this->error('无权限访问');
		    }
		}
		
		$this->lmgame_model = M(C('MNG_DB_NAME').".game", C('LDB_PREFIX'));
        $this->game_model = M($this->dbname.".game", C('LDB_PREFIX'));
		$this->status_model = M(C('MNG_DB_NAME').".status", C('LDB_PREFIX'));
		$this->client_model = M(C('MNG_DB_NAME').".client", C('LDB_PREFIX'));
		$this->gametype_model = M(C('MNG_DB_NAME').".gametype", C('LDB_PREFIX'));
		$this->gameclass_model = M(C('MNG_DB_NAME').".gameclass", C('LDB_PREFIX'));
		$this->logoweb = DAMAIIMGSITE."/gamelogo/";
		
    }

	/**
	 * 接入游戏列表
	 */
	public function index(){
		$this -> _gameList($type);
		$this -> display();
	}

	public function _gameList($type){
		
		$name = trim(I('name'));
		$status = I('status',0);

		$where_ands = array();
        $fields = array(
                'name' => array(
                        "field" => "a.gamename", 
                        "operator" => "=" 
                ),
				'status' => array(
							"field" => "status", 
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
         
		$cid = sp_get_current_cid();
		array_push($where_ands, " a.cid = ".$cid);
		
		if($type > 0){
			array_push($where_ands, " a.type = ".$type);
		}
        $where = join(" and ", $where_ands); 
       
		$result = array();
		
		$result["total"] = $total = $this->lmgame_model->alias("a")->where($where)->count();
		$page = $this->page($result["total"], 10);
		$limit = $page->firstRow .",". $page->listRows;
		$field = "a.id, a.cid,a.appkey,a.gamename, a.status,CONCAT('".$this->logoweb."',g.icon) as logo,g.packageurl,a.type,a.initial,g.gid,g.ghid,a.create_time,cp.cpurl,a.paytype";
		$items = $this->lmgame_model
			          -> alias("a")
					  -> where($where)
					  -> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext g on a.id=g.gid")
					  -> join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."gamecpurl cp on a.id=cp.gid")
			          -> field($field)
					  -> limit($limit)
					  -> order('id desc')
					  -> select();
		//echo $items;
		
		$status_src = $this->status_model->where(array('typeid'=>1,'status'=>1))->order(' sequence asc')->select();
		
		$status_array=array();
		foreach ($status_src as $s){
			$sequence=$s['sequence'];
			$status_array["$sequence"]=$s;
		}
		
		$this->assign("downurl",DAMAIDOWNSITE."/sdkgame");
		$this->assign("games",$items);
		$this->assign("clientid",0);
		$this->assign("formget", $_GET);
		$this->assign("status_array",$status_array);
		$this->assign("Page", $page->show('Admin'));
		
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
	 * 游戏类型
	 * @date: 2016年3月3日下午2:31:40
	 * @since 1.0
	 */
	public function _cparatestatus(){	 
	    $cates=array(
	            "0"=>"全部"
	    );
	    $cparatestatus = M(C('MNG_DB_NAME').'.status',C('LDB_PREFIX'))->where("`status`=1 AND `typeid`=2 AND `sequence`>0")->order('sequence')->getField("sequence,statusname statusname");
	    if($cparatestatus){
	        $cparatestatus=$cates+$cparatestatus;
	    }else{
	        $cparatestatus=$cates;
	    }
	    $this->assign("cparatestatus",$cparatestatus);
	}
	
	public function _lgeamestatus(){	 
	    
	    $geamestatus = M(C('MNG_DB_NAME').'.status',C('LDB_PREFIX'))->where("`status`=1 AND `typeid`=1 AND `sequence`>0")->order('sequence')->getField("sequence,statusname statusname");
	  
	    $this->assign("geamestatus",$geamestatus);
	}

	/**
	 *游戏接入
	 */
	public function addGame() {
		$typelist = $this->gametype_model -> where(array("status"=>1)) ->select();
	
		$classlist = $this->gameclass_model -> where(array("status"=>1)) ->select();
		
		$this->_lgeamestatus();
		$this->assign("classlist",$classlist);
		$this->assign("typelist",$typelist);

		$this -> display();
	}

	/**
     * 创建游戏提交处理函数
     * @date: 2016年1月20日上午10:28:25
     * @param 无
     * @since 1.0
     * @author wuyonghong
     */
	public function addGame_post(){
		if(IS_POST){
			$time = time();
			
			//获取数据
            $gamedata['gamename'] = $this->_trimall(I('gamename'));
            $gamedata['type'] = I('gametype');
			$gamedata['status'] = I('status');
			$gamedata['iosurl'] = I('iosurl');
			
			$extdata['version'] = I('version');       //游戏版本
			$extdata['type'] = I('gametype');              //游戏运行平台
			$extdata['intro'] = I('intro');          //一句话简介
			$extdata['content'] = I('content');      //游戏简介
			$extdata['class'] = I('game_class');          //游戏分类
			
			if(empty($gamedata['type'])){
				$this->error("请选择游戏运行平台");
				exit;
			}
			
			if(empty($gamedata['gamename']) || 
			    empty($extdata['version'])  ||
				empty($extdata['intro'])   ||
			    empty($extdata['content'])  ||
			    empty($extdata['class']) ){
				$this->error("请填写完整参数");
				exit;
			}
			
			if(strlen($gamedata['gamename']) > 30){
			    $this->error("游戏名称超过30个字符");
			    exit;
			}
			
			$cid = sp_get_current_cid();
			$games = $this->lmgame_model->where(array('gamename'=>$gamedata['gamename'],'type'=>$gamedata['type']))->select();
			
			if(count($games) > 0){
				$this->error("游戏已存在");
				exit;
			}

			$logo = $_FILES['logo'];//游戏图标
			
			if (empty($logo['name'])) {
				$this->error("请上传游戏logo");
				exit;
			}

			//获取游戏名称拼音
			import('Vendor.Pin');
			$pin = new \Pin();
			
			$pinyin = $pin -> pinyin($gamedata['gamename']);
			$gamedata['appidenty'] = $pinyin;
			$gamedata['appkey'] = '';

			$initial = $pin -> pinyin($gamedata['gamename'],true);
			$gamedata['initial'] = $initial;
			
			$gamedata['create_time'] = $time;
			$gamedata['cid'] = sp_get_current_cid();
			
			if ($this->lmgame_model->create($gamedata)) {
				
				$model = M();
                $model->startTrans();
				
				$extdata['gid'] = $model->table(C('MNG_DB_NAME').".".C('LDB_PREFIX')."game")->add($gamedata);
                
				if ($extdata['gid']) {
					$gextmodel=M(C('MNG_DB_NAME').".game_ext", C('LDB_PREFIX'));
					$data['appkey'] = md5($extdata['gid'].md5($gamedata['appidenty']));
                    
					$rs = $model->table(C('MNG_DB_NAME').".".C('LDB_PREFIX')."game")->where("id = %d",$extdata['gid'])->save($data);
					
					$logoname = $this->uploadimg($logo, $extdata['gid'],"gamelogo/");
					
					$extdata['icon'] = $logoname;
					
					//插入游戏扩展
					$ext_rs = $model->table(C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext")->add($extdata);
                    if(!$ext_rs){
						$model->rollback();
						$this->error("游戏扩展添加失败！");
			            exit;
					}
					
                    if($ext_rs){
						$model -> commit();
						$this -> success("保存成功");
					    exit;
					}
					
				} 
			} 

			$this->error("添加失败！");
			exit;
		}
		$this->error("请求失败！");
		exit;
	}

	//游戏修改
	public function editgame(){
		$id = I('id');
		$clientid = 0;
		

		$field = "a.id, a.gamename, a.create_time, a.status,e.class,e.intro,CONCAT('".$this->logoweb."',e.icon) as logo,e.content,e.version,a.iosurl";
		$game= $this->lmgame_model
		->alias("a") 
		->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext e ON e.gid = a.id")
		->field($field)
		->where(array('a.id'=>$id))
		->find();
		
		$gtypemodel=M(C('MNG_DB_NAME').".gametype", C('LDB_PREFIX'));
		$typelist = $gtypemodel -> where(array("status"=>1)) ->select();

		$classmodel=M(C('MNG_DB_NAME').".gameclass", C('LDB_PREFIX'));
		$classlist = $classmodel -> where(array("status"=>1)) ->select();
		
		$this->_lgeamestatus();
		
		$this->assign("classlist",$classlist);
		$this->assign("typelist",$typelist);
		$this->assign("game",$game);
		
		$this->display();
	 }

	 /**
	 * 修改游戏信息
	 */
	public function editGame_post() {
		if(IS_POST){
			$time = time();

			$gamedata['gamename'] = $this->_trimall(I('gamename'));
			//获取数据
			$extdata['gid'] = I('id');              //游戏id
			$extdata['version'] = I('version');       //游戏版本
			//$extdata['type'] = I('game_type');              //游戏运行平台
			$extdata['intro'] = I('intro');          //一句话简介
			$extdata['content'] = I('content');      //游戏简介
			$extdata['class'] = I('game_class');          //游戏分类
			
			$gamedata['status'] = I('status');
			$gamedata['iosurl'] = I('iosurl');
			
			//$extdata['screenshots'] = I('post.screenshots');  //游戏截图

			if( empty($extdata['version'])  ||
			   empty($extdata['intro'])  ||
			   empty($extdata['content'])  ||
			   empty($extdata['class'])||
			   empty($gamedata['gamename'])){
				$this->error("请填写完整参数");
			}

			$logo = $_FILES['logo'];//游戏图标
			
			/*I('filename') ? $material = I('filename') : '';
			
			if(!empty($material)){
				$last_index = strripos($material,'.');
				$file_type = substr($material,$last_index+1);
				
				if((strtolower($file_type) != "zip")
					&& (strtolower($file_type) != "rar")){
					$this->error("素材包类型不符合");
				}
				$extdata['material'] = $material;
			}*/
			
			if ($extdata['gid'] > 0) {
					$gextmodel=M(C('MNG_DB_NAME').".game_ext", C('LDB_PREFIX'));
                    
					if (!empty($logo['name'])) {
						
						$gexts = $gextmodel->where(array("gid"=>$extdata['gid']))->find();
						$logoname = $this->uploadimg($logo, $extdata['gid'],"gamelogo/");
						
						$extdata['icon'] = $logoname;
					}
					
					$model = M();
                    $model->startTrans();
	                
					$rss = $model->table(C('MNG_DB_NAME').".".C('LDB_PREFIX')."game")->where("id = %d",$extdata['gid'])->save($gamedata);
					
					$rs = $model->table(C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext")->where("gid = %d",$extdata['gid'])->save($extdata);
					if($rs !== false){
						$model->commit();
						$this -> success("保存成功",U("Gameaccess/index"));
					}else {
						$model->rollback();
						$this->error("修改失败！");
					}
				   
			} else {
				$this->error($gextmodel->getError());
			}
		}
		
	} 

	 /**
	 * 添加游戏回调
	 */
	public function addurl(){
		$id = I("id");
        
		$where['id'] = $id;
		$game = $this->lmgame_model->where($where)->find();
		
		$this->assign("game",$game);
		$this -> display();
	}

	/**
	 * 渠道添加游戏回调
	 */
	public function addurl_post() {
		$id = I("id");
		$cpurl = I("cpurl");
		
		if(empty($cpurl)){
			$this->error("请填写回调");
			exit;
		}
		
		$cid = 0;
		$cpmodel = M(C('MNG_DB_NAME').".gamecpurl", C('LDB_PREFIX'));
		$cglist = $cpmodel->where("cid = %d AND gid=%d",$cid,$id)->select();

		if(count($cglist) <= 0){
			$urldata['gid'] = $id;
			$urldata['cid'] = $cid;
			$urldata['cpurl'] = $cpurl;
			$urldata['update_time'] = time();
			
			if($cpmodel->create($urldata)){
				if($cpmodel->add()){
					$this->success("添加成功！",U("Gameaccess/index"));
					exit();
				}
			}
			$this->error('请求失败.');
			exit;
		}else{
			$this->error('回调已存在.');
			exit;
		}
	}

	/**
	 * 修改游戏回调
	 */
	public function editurl(){
		$id = I("id");
      
		$where['a.id'] = $id;
		$field = "a.id,a.gamename,cp.cpurl";
		$game = $this->lmgame_model
			  ->alias("a") 
			  ->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."gamecpurl cp on a.id=cp.gid")
			  ->where($where)
			  ->find();

		$this->assign("game",$game);
		$this -> display();
	}

	/**
	 * 渠道修改游戏回调
	 */
	public function editurl_post() {
		$id = I("id");
		$cpurl = I("cpurl");
		
		if(empty($cpurl)){
			$this->error("请填写回调");
			exit;
		}
		
		$cid = 0;
		$cpmodel = M(C('MNG_DB_NAME').".gamecpurl", C('LDB_PREFIX'));
		$cglist = $cpmodel->where("cid = %d AND gid=%d",$cid,$id)->select();

		if(count($cglist) > 0){
			$urldata['cpurl'] = $cpurl;
			$urldata['update_time'] = time();
			
			if($cpmodel->create($urldata)){
				$rs = $cpmodel->where("cid = %d AND gid=%d",$cid,$id)->save();
				if($rs){
					$this->success("修改成功！",U("Gameaccess/index"));
					exit();
				}
			}
			$this->error('请求失败.');
			exit;
		}else{
			$this->error('获取信息失败.');
			exit;
		}
	}
	
	/**
	 * 游戏下架
	 */
	public function nogamestatus() {
		$id = intval(I("id"));
		
		if(empty($id)){
			$this->error("请求错误");
			exit;
		}
		
		$gamemodel = M(C('MNG_DB_NAME').".game", C('LDB_PREFIX'));
		$rs = $gamemodel->where(array("id"=>$id))->setField(array("status"=>5));

		if($rs){
			$this->success("下架成功！",U("Gameaccess/index"));
			exit();
		}else{
			$this->error('下架失败.');
			exit;
		}
	}
	
	/**
	 * 游戏上线
	 */
	public function gamestatus() {
		$id = intval(I("id"));
		
		if(empty($id)){
			$this->error("请求错误");
			exit;
		}
		
		$gamemodel = M(C('MNG_DB_NAME').".game", C('LDB_PREFIX'));
		$rs = $gamemodel->where(array("id"=>$id))->setField(array("status"=>3));

		if($rs){
			$this->success("上线成功！",U("Gameaccess/index"));
			exit();
		}else{
			$this->error('上线失败.');
			exit;
		}
	}
	
	
	/**
	 * IOS支付
	 */
	public function iospay(){
		//$this->_gameList(2);
		
		$name = trim(I('name'));
		//$status = I('status',0);

		$where_ands = array();
        $fields = array(
                'name' => array(
                        "field" => "g.gamename", 
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
         
	    array_push($where_ands, " g.type = 2 ");
		
        $where = join(" and ", $where_ands); 
       
		$result = array();
		
		$gamever_model = M(C('MNG_DB_NAME').".gameiospay", C('LDB_PREFIX'));
		
		$result["total"] = $total = $gamever_model
			                        ->alias("a") 
			                        ->join("inner join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g on a.gid=g.id")
									->where($where)
									->count();
			
		$page = $this->page($result["total"], 10);
		$limit = $page->firstRow .",". $page->listRows;
		$field = "a.id, g.gamename,a.versions, g.type,a.gid,a.create_time,a.paytype";
		$items = $gamever_model
			          -> alias("a") 
			          -> join("inner join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g on a.gid=g.id")
			          -> where($where)
			          -> field($field)
					  -> limit($limit)
					  -> order('g.id desc')
					  -> select();
			
		$this->assign("games",$items);
		$this->assign("formget", $_GET);
		$this->assign("Page", $page->show('Admin'));
		$this -> display();
	}
	
	/**
	 * IOS支付
	 */
	public function addiospay(){
		$this->_getGames(2);
		$this -> display();
	}
	
	/**
	 * IOS支付切换
	 */
	public function addiospay_post() {
		$appid = intval(I("appid"));
		$versions = I("versions");
		
		if(empty($appid) || empty($versions)){
			$this->error("请求错误");
			exit;
		}
		
		$gamever_model = M(C('MNG_DB_NAME').".gameiospay", C('LDB_PREFIX'));
		
		$game = $gamever_model
			  ->alias("a") 
			  ->join("inner join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g on a.gid=g.id")
			  ->where(array('a.gid'=>$appid,'a.versions'=>$versions))
			  ->find();
		
		if(!empty($game)){
			$this->error("该游戏版本已存在");
			exit;
		}
		$data['gid'] = $appid;
		$data['versions'] = $versions;
		$data['paytype'] = 2;
		$data['create_time'] = time();
		
		$rs = $gamever_model->add($data);
		if($rs){
			$this->success("添加成功！",U("Gameaccess/iospay"));
			exit();
		}else{
			$this->error('添加失败.');
			exit;
		}
	}
	
	/**
	 * IOS支付切换
	 */
	public function iospay_post() {
		$id = intval(I("id"));
		
		if(empty($id)){
			$this->error("请求错误");
			exit;
		}
		
		
		$gamever_model = M(C('MNG_DB_NAME').".gameiospay", C('LDB_PREFIX'));
		
		$game = $gamever_model->where(array("id"=>$id))->find();
		
		if($game['paytype'] == 2){
			$rs = $gamever_model->where(array("id"=>$id))->setField(array("paytype"=>1));
		}else{
			$rs = $gamever_model->where(array("id"=>$id))->setField(array("paytype"=>2));
		}
		
		if($rs){
			$this->success("切换成功！",U("Gameaccess/iospay"));
			exit();
		}else{
			$this->error('切换失败.');
			exit;
		}
	}
	
	public function deliospay() {
		$id = intval(I("id"));
		
		if(empty($id)){
			$this->error("请求错误");
			exit;
		}
		
		
		$gamever_model = M(C('MNG_DB_NAME').".gameiospay", C('LDB_PREFIX'));
		
		$rs = $gamever_model->where(array("id"=>$id))->delete();
		
		if($rs){
			$this->success("删除成功！",U("Gameaccess/iospay"));
			exit();
		}else{
			$this->error('删除失败.');
			exit;
		}
		
	}
	
	
	 /**
	 * 上传页面
	 */
	public function upload(){
		$id = I('id',0);
		
		$this->assign("appid",$id);
		$this -> display();
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

    public function _trimall($str){
	        $prefix = array(" ","　","\t","\n","\r");
            $suffix = array("","","","","");
            $str = str_replace($prefix,$suffix,$str);
           return $str;
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////
	/**
	**上传签名传递
	**/
	public function getSig(){
		$id = I('appid',0);
	    $clientid = sp_get_current_cid();
			
		$field = "a.*,e.*";
		$gameinfo=$this->lmgame_model
			->alias("a") 
			->join("inner join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext e ON e.gid = a.id")
			->field($field)
			->where("id = %d",array($id))
			->find();
			
		$ver = $gameinfo['version'];
		$packagename = $gameinfo['id'].'-0-0-0-'.time();

		$data['gid'] = $gameinfo['id'];
		$data['cid'] = $clientid;
		$data['version'] = $ver;
		$data['packageurl'] = $packagename.".apk";
		$data['size'] = $gameinfo['size'];
		$data['create_time'] = time();
		$data['update_time'] = time();

		$historymodel = M(C('MNG_DB_NAME').".gamehistory", C('LDB_PREFIX'));
		if($historymodel->create($data)){
			$ghid = $historymodel->add();
		}
			
		$filename = $gameinfo['id'].'_'.$gameinfo['initial'].'/'.$ghid.'/'.$packagename;
		$dir = C('GAMEDIR').'/';
			
		import('Vendor.ossdk.sdk');

		$id= C('OSS_ACCESS_ID');
		$key= C('OSS_ACCESS_KEY');
		$host = 'http://'.C('OSS_DOWN_BUCKET').".".C('OSS_ENDPOINT');
        
		$now = time();
		$expire = 30; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
		$end = $now + $expire;

		$dtStr = date("c", $end);
		
		$expiration = $dtStr;
		$pos = strpos($expiration, '+');
		$expiration = substr($expiration, 0, $pos);
		$expiration = $expiration."Z";
		
		
		$oss_sdk_service = new \ALIOSS($id, $key, $host);

		//最大文件大小.用户可以自己设置
		$condition = array(0=>'content-length-range', 1=>0, 2=>1048576000);
		$conditions[] = $condition; 

		//表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
		$start = array(0=>'starts-with', 1=>'$key', 2=>$dir);
		$conditions[] = $start; 

		//这里默认设置是２０２０年.注意了,可以根据自己的逻辑,设定expire 时间.达到让前端定时到后面取signature的逻辑
		$arr = array('expiration'=>$expiration,'conditions'=>$conditions);
		
		$policy = json_encode($arr);
		$base64_policy = base64_encode($policy);
		$string_to_sign = $base64_policy;
		$signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

		$response = array();
		$response['accessid'] = $id;
		$response['host'] = $host;
		$response['policy'] = $base64_policy;
		$response['signature'] = $signature;
		$response['expire'] = $end;
		$response['filename'] = $filename;
		$response['gettype'] = $type;
		//这个参数是设置用户上传指定的前缀
		$response['dir'] = $dir;
		$this->ajaxReturn($response,'JSON');
	}

	/**
	**上传回调
	**/
	public function uploadok(){
		if(IS_POST){
			$gid = I('post.appid',0);
			
            $clientid = sp_get_current_cid();

			$gdata['status'] = 3;
			   
			$this->lmgame_model->where("id = %d",$gid)->save($gdata);
               
			//更新上传包记录
			$history_model = M(C('MNG_DB_NAME').".gamehistory", C('LDB_PREFIX'));
			$hid = $history_model->where(array('gid'=>$gid))->max('id');
			$history = $history_model->where(array('id'=>$hid))->find();
			   
			$ext_model = M(C('MNG_DB_NAME').".game_ext", C('LDB_PREFIX'));
			$ext_model->where(array('gid'=>$gid))->save(array('ghid'=>$hid,'ghnewid'=>$hid,"packageurl"=>$history['packageurl']));
			   
	    }

	    $this->ajaxReturn(array('success'=>true,'msg'=>'上传成功'),'JSON');
	    exit;
		
	}
	
	public function uploadf(){
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
 
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
           exit; // finish preflight CORS requests here
        }
        if ( !empty($_REQUEST[ 'debug' ]) ) {
           $random = rand(0, intval($_REQUEST[ 'debug' ]) );
           if ( $random === 0 ) {
            header("HTTP/1.0 500 Internal Server Error");
            exit;
           }
        }
 
        // header("HTTP/1.0 500 Internal Server Error");
        // exit;
        // 5 minutes execution time
        @set_time_limit(5 * 60);
        // Uncomment this one to fake upload time
        // usleep(5000);
       // Settings
       // $targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
	   
	    $id = I('get.appid',0);
	    $clientid = sp_get_current_cid();
			
		$field = "a.*,e.*";
		$gameinfo=$this->lmgame_model
			->alias("a") 
			->join("inner join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext e ON e.gid = a.id")
			->field($field)
			->where("id = %d",array($id))
			->find();
			
		$ver = $gameinfo['version'];
		$packagename = $gameinfo['id'].'-0-0-0-'.time();

		$data['gid'] = $gameinfo['id'];
		$data['cid'] = $clientid;
		$data['version'] = $ver;
		$data['packageurl'] = $packagename.".apk";
		$data['size'] = $gameinfo['size'];
		$data['create_time'] = time();
		$data['update_time'] = time();

		$historymodel = M(C('MNG_DB_NAME').".gamehistory", C('LDB_PREFIX'));
		if($historymodel->create($data)){
			$ghid = $historymodel->add();
		}
			
		$file_url = C('UPLOAD_BAG')."/".$gameinfo['id'].'_'.$gameinfo['initial'].'/'.$ghid;
		
	    if (is_dir($file_url)){  
		   
	    }else{
		   $res=mkdir(iconv("UTF-8", "GBK", $file_url),0777,true); 
        }
	
        $targetDir = C('UPLOAD_BAG').DIRECTORY_SEPARATOR.'file_material_tmp';
        $uploadDir = $file_url;
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds
        // Create target dir
        if (!file_exists($targetDir)) {
          @mkdir($targetDir);
        }
        // Create target dir
        if (!file_exists($uploadDir)) {
          @mkdir($uploadDir);
        }
        // Get a file name
        if (isset($_REQUEST["name"])) {
           $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
           $fileName = $_FILES["file"]["name"];
        } else {
           $fileName = uniqid("file_");
        }
       $oldName = $fileName;
       $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
       // $uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
       // Chunking might be enabled
       $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
       $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;
       // Remove old temp files
    if ($cleanupTargetDir) {
      if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
      }
      while (($file = readdir($dir)) !== false) {
        $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
        // If temp file is current file proceed to the next
        if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
          continue;
        }
        // Remove temp file if it is older than the max age and is not the current file
        if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {
          @unlink($tmpfilePath);
        }
      }
      closedir($dir);
    }
 
    // Open temp file
    if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
      die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
    }
    if (!empty($_FILES)) {
      if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
      }
      // Read binary input stream and append it to temp file
      if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
      }
    } else {
      if (!$in = @fopen("php://input", "rb")) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
      }
    }
    while ($buff = fread($in, 4096)) {
      fwrite($out, $buff);
    }
    @fclose($out);
    @fclose($in);
    rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");
    $index = 0;
    $done = true;
    for( $index = 0; $index < $chunks; $index++ ) {
      if ( !file_exists("{$filePath}_{$index}.part") ) {
        $done = false;
        break;
      }
    }
 
    if ( $done ) {
      $pathInfo = pathinfo($fileName);
      $hashStr = substr(md5($pathInfo['basename']),8,16);
     // $hashName = time() . $hashStr . '.' .$pathInfo['extension'];
	  $hashName = $packagename.'.' .$pathInfo['extension'];
      $uploadPath = $uploadDir . DIRECTORY_SEPARATOR .$hashName;
 
      if (!$out = @fopen($uploadPath, "wb")) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
      }
      if ( flock($out, LOCK_EX) ) {
        for( $index = 0; $index < $chunks; $index++ ) {
          if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
            break;
          }
          while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
          }
          @fclose($in);
          @unlink("{$filePath}_{$index}.part");
        }
        flock($out, LOCK_UN);
      }
      @fclose($out);
      $response = [
        'success'=>true,
        'oldName'=>$oldName,
        'filePaht'=>$uploadPath,
        //'fileSize'=>$data['size'],
        'fileSuffixes'=>$pathInfo['extension']
        //'file_id'=>$data['id'],
        ];
 
      die(json_encode($response));
    }
 
   // Return Success JSON-RPC response
    die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
	}
}
