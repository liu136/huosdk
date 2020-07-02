<?php
/**
* 游戏分包管理页面
*
* @author
*/

namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class SubiospackageController extends AdminbaseController {	
	protected $users_model,$role_model,$role_user_model,$game_model,$cadmin_model,$where, $client_model,$appidwhere;
	
	function _initialize() {
		parent::_initialize();
		$this->users_model = M(C('AUTH_DB_NAME').".clientusers", C('LDB_PREFIX'));
		$this->role_model = M(C('AUTH_DB_NAME').".clientrole", C('LDB_PREFIX'));
		$this->role_user_model = M(C('AUTH_DB_NAME').".clientrole_user", C('LDB_PREFIX'));
		$this->game_model = M(C('MNG_DB_NAME').".game", C('LDB_PREFIX'));
		$this->client_model = M(C('MNG_DB_NAME').".client", C('LDB_PREFIX'));
		$this->where = "1";
		$uid = sp_get_current_admin_id();
		$roletype = sp_get_current_roletype();
		if ($roletype > 2){
			$userids = $this->getAgentids($uid);
			$this->where = "agentid in (".$userids.") ";
			
			/*$appids = $this->getAppids($uid);
			$this->appidwhere = "appid in (".$appids.") "; */
		}else{
			 $this->appidwhere = "1";
			 $this->where = " 1 ";
		 }
		
	}
	
	public function index(){
	    $uid = sp_get_current_admin_id();
	    $roletype = sp_get_current_roletype();
	    if ($roletype > 2){
			$userids = $this->getAgentids($uid);
	        $userwhere = " id in (".$userids.") ";
	    }
	    $roletype = sp_get_current_roletype();
	    $this->assign('roletype',$roletype);
	    $where_ands = array();
	    $this->_getqudaoindex();
// 	    $this->_getUsers();

	    $this->_getAgents($userwhere);
		
	    $this->_getGames(2);
		
	    $this->_getroles();
		
	    $this->_getDownurl();
	    $this->display();
	}
	
	function _getDownurl(){
	    $downsite =  DAMAIQUDAOSITE."/Public/reg";
	    $this->assign('downurl', $downsite);
	}
	
	public function downurl(){
	    $id= intval(I("id"));
		$cid  = sp_get_current_cid();
		$field = "a.*,ge.ghid,ge.gid,g.initial";
        
		$agentmodel = M($this->dbname.'.agentlist', C('CDB_PREFIX'));
		$agents = $model->where($where)
		->alias("a") 
		->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g ON g.id = a.appid")
		->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext ge ON ge.gid = g.id")
		->field($field)
		->where(array("a.id"=>$id,"a.cid"=>$cid))
		->find();

		$this->_getDownurl();
	    $this->assign('agents', $agents);
	    $this->display();
	}
	
	/**
	 * 渠道游戏列表
	 * return void
	 */
    public function _getqudaoindex(){
        $where_ands = array();
		$fields = array(
		        'agent_id' => array(
		                "field" => "a.agentid",
		                "operator" => "="
		        ),
		        'appid' => array(
		                "field" => "a.appid",
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
        
		$adminid = sp_get_current_admin_id();
		
        $cid  = sp_get_current_cid();
		
		array_push($where_ands,  "g.type = 2");
		if($this->where != " 1 "){
			array_push($where_ands,  "a.".$this->where);
		}
		
		//if($this->appidwhere != "1"){
		//	array_push($where_ands,  "a.".$this->appidwhere);
		//}
		
		$where = join(" AND ", $where_ands);
		
		$model = M($this->dbname.'.agentlist',C('CDB_PREFIX'));
       
		$count=$model->alias("a")
		             ->join("INNER JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g ON g.id = a.appid")
		             ->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext ge ON ge.gid = g.id")
		             ->join("left join ".$this->dbname.".".C('CDB_PREFIX')."agentblack ab ON (ab.agentid = a.agentid AND ab.appid = a.appid)")
		             ->where($where)
					 ->count();

		$rows = isset($_POST['rows']) ? intval($_POST['rows']) : $this->row;
		$page = $this->page($count, $rows);
		
		$field = "a.*,ab.id AS abid,ge.ghid,ge.gid,g.initial";

		$agentgames = $model->where($where)
		->alias("a") 
		->join("INNER JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g ON g.id = a.appid")
		->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext ge ON ge.gid = g.id")
		->join("left join ".$this->dbname.".".C('CDB_PREFIX')."agentblack ab ON (ab.agentid = a.agentid AND ab.appid = a.appid)")
		->field($field)
		->where($where)
		->limit($page->firstRow . ',' . $page->listRows)
		->order('a.id DESC')
		->select();
       
		$cid = sp_get_current_cid();
		$this->assign('cid',$cid);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("agentgames",$agentgames);
		$this->assign("formget", $_GET);
    }
    
	function addagent(){
		$roles=$this->role_model->where("status=1 AND typeid = 4")->order("id desc")->select();		
		
		$this->assign("roles",$roles);
		$this->display();
	}

	/*
	 *添加渠道
	 */
	public function addagent_post(){
		if(IS_POST){
			$user_login = trim(I('user_login'));
			$agentname = trim(I('agentname'));
			$roleid = intval(I('role_id'));
			$mobile = trim(I('mobile'));
			$user_login = trim($_POST['user_login']);
			$agentname = trim($_POST['agentname']);
			$roleid = intval($_POST['role_id']);
			$mobile = trim($_POST['mobile']);
			if(empty($roleid) || $roleid <= 0){
				$this->error("请为此渠道选择角色！");
				exit;
			}
			
			$admin['user_login'] = $user_login;
			$pwd = trim(I('password'));
			$admin['user_pass'] = sp_password($pwd);
			$admin['user_nicename'] = $agentname;
			$admin['mobile'] = $mobile;
			$admin['create_time'] = Date('Y-m-d H:i:s');
			$admin['parentid'] = sp_get_current_admin_id();				
			$admin['role_id'] = $roleid;
		    if (empty($pwd) || empty($admin['user_login'])) {
				$this->error("请填写完整参数");
				exit;
		    }
				
		    $checkusername = $this->users_model->where(array("user_login"=>$admin['user_login']))->find();
		    
		    if ($checkusername) {
				$this->error("用户名已经存在");
		    }
			
			$admin['cid'] = sp_get_current_cid();
			if ($this->users_model->create($admin)) {
				$uid=$this->users_model->add();				
				if ($uid) {
					$ruser_data['role_id'] = $roleid;
					$ruser_data['user_id'] = $uid;					
					$rs = $this->role_user_model->add($ruser_data);
					if($rs){
					    //插入用户到渠道数据
					    $cdata['id'] = $uid;
					    $cdata['cid'] = sp_get_current_cid();
					    $cdata['user_login'] = $admin['user_login'];
					    $cdata['user_nicename'] = $admin['user_nicename'];
					    $cdata['role_id'] = $admin['role_id'];
					    $cdata['parentid'] = $admin['parentid'];	
					    $rs =  M($this->dbname.'.clientusers', C('CDB_PREFIX'))->add($cdata);
					}
					$this->success("添加账号成功！", U("Subpackage/qudaoindex"));
					exit;
				} else {
					$this->error("添加账号失败！");
					exit;
				}
			} else {
				$this->error("添加账号格式无效！");
				exit;
			}
			$this->error("数据传输失败！");
			exit;
			
		}
	}
	
	public function ajaxAgentgame(){
	    $data['appid'] = intval(I('post.appid'));
	    $data['agentid'] = intval(I('post.agent_id'));
	    $data['agexist'] = 1;
	    $data['status'] = 1;
	    $data['type'] = 4;
	    
	    if($data['appid']){
	        $where['appid'] = $data['appid'];
	    }
	    
	    if($data['agentid']){
	        $where['agentid'] = $data['agentid'];
	    }
	    
	    $cnt = M($this->dbname.'.agentlist',C('CDB_PREFIX'))->where($where)->count('id');
	    if(empty($cnt) && $data['appid'] && $data['agentid']){
	        $data['agexist'] = 0;
	        unset($where);
	        $where['id'] = $data['agentid'];
	        $where['cid'] = sp_get_current_cid();
	        $data['type'] = $this->users_model->where($where)->getfield('role_id');
	    }
	    
	    $this->ajaxReturn($data);
	}
	
	/**
	*修改渠道信息
	*/
	public function editagent(){
		$id= intval(I("id"));
		$agentmodel = M($this->dbname.'.agentlist', C('CDB_PREFIX'));
		$agent = $agentmodel->where(array("id"=>$id))->find();
		
		$games = $this->game_model->where(array("appid"=>$agent['appid']))->find();
		$roles=$this->role_model->where(array("id"=>$agent['roleid']))->find();
		$users=$this->users_model->where(array("id"=>$agent['agentid']))->find();
		
		$this->assign('id',$agent['id']);
		$this->assign('games',$games);
		$this->assign('agent',$agent);
		$this->assign("roles",$roles);
		$this->assign("users",$users);
		$this->display();
	}
 
	/**
	*保存修改信息
	*/
	public function editagent_post(){
		if (IS_POST) {
			$id=intval($_POST['id']);
			$data['cpa_price'] = I('cpa_price');		//CPA价格
			$data['rate'] = I('rate') ;						//分成比例
			
			$model = M($this->dbname.'.agentlist', C('CDB_PREFIX'));
			$rs = $model->where("id=%d",$id)->save($data);

			if ($rs) {
				$this->success("修改渠道成功！", U("Subpackage/index"));
				exit;
			} else {
				$this->error("修改渠道失败！");
				exit;
			}

		}

	}
	
	/*
	*打包
	*/
	public function subpackage(){
		$agent_id=intval(I('suagentid'));
		$appid=I('sugameid',0);
		$rate=I('rate');
		
		if($agent_id <= 0 || $appid <= 0 || (empty($rate))){
			$this->ajaxReturn(array('msg'=>'请填写完整参数.'),'JSON');
		}
		$clientid = sp_get_current_cid();

		$agentgamemodel = M($this->dbname.'.agentlist', C('CDB_PREFIX'));
    	$cnt = $agentgamemodel->where("agentid=%d AND appid=%d",$agent_id,$appid)->count();	
        
		
		if($cnt <= 0){
			$field = "id agentid, user_login agentname, user_nicename agentnicename, role_id roleid, parentid";
			$data = $this->users_model->field($field)->where("id=%d",$agent_id)->find();
			if (empty($data)){
			    $this->ajaxReturn(array('msg'=>'渠道参数错误！'),'JSON');
			}
			$data['appid'] = $appid;					//游戏
			$data['rate'] = $rate;						//分成比例
			$data['create_time'] = time();					//该包创建时间																
			$data['update_time'] = $data['create_time'];									
			$data['cid'] = 2;									

			$game = $this->game_model
			       ->alias("a")
				   ->field("a.gamename,ex.packageurl")
			       ->join("inner join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext ex on a.id=ex.gid")
			       ->where("a.id=%d",$data['appid'])
				   ->find();
		    if (empty($game)){
			    $this->ajaxReturn(array('msg'=>'游戏参数错误！'),'JSON');
			}
			$data['gamename'] = $game['gamename'];
			
			$initial = M(C('MNG_DB_NAME').'.game', C('LDB_PREFIX'))
			->where(array('id'=>$data['appid']))
			->getField('initial');
			
			$data['agentgame'] = $data['appid'].'-'.$initial.'-'.$data['cid'].'-'.$data['cid'].'-'.$data['agentid'];
			$lastid = $agentgamemodel->data($data)->add();
			if($lastid){
				$agentlist = $data;
				$agentlist['id'] = $lastid;
				
				//执行分包
				$this->ajaxReturn(array('success'=>true,'msg'=>'生成成功','data'=>$agentdata),'JSON');
				//$this->_subpackage($agentlist);
				exit;
			}else{
			    $this->ajaxReturn(array('msg'=>'添加渠道链接失败！'),'JSON');
				exit;
			}
		}
		$this->ajaxReturn(array('msg'=>'非法请求！'),'JSON');
	}
	
	function _subpackage($agentdata,$isajax = false){
	    if (empty($agentdata['id']) || empty($agentdata['cid']) ||  empty($agentdata['agentgame']) ||empty($agentdata['appid'])){
            $isajax ? $this->error('内部错误',U('Subpackage/index')) : $this->ajaxReturn(array('msg'=>'内部错误.'),'JSON');
            exit;
	    }
	   
	    $gamedata = M(C('MNG_DB_NAME').'.game', C('LDB_PREFIX'))
	    ->alias('g')
	    ->field("g.id,g.initial, ge.version, ge.ghid, ge.packageurl,g.type")
	    ->join(" INNER JOIN ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext ge ON g.id = ge.gid")
	    ->where(array('g.id'=>$agentdata['appid']))
	    ->find();
        
		$file_url = C('UPLOAD_BAG').$gamedata['id'].'_'.$gamedata['initial'].DIRECTORY_SEPARATOR.$gamedata['ghid'].DIRECTORY_SEPARATOR;
		
		//判断是否存在本地目录
	    if(!file_exists($file_url)){
		   mkdir($file_url,0777,true);
	    }
	
		$bag_url = $file_url.$gamedata['packageurl'];
		
		$updatedata['update_time'] = time();
        $newbag = $agentdata['agentgame'].'-'.$updatedata['update_time'].'.apk';
		$key = " --id=LTAI5CXm9Kqye5xG --key=zM2zEqhWQRrwHBRLioXKzhGWq0TEgv --host=oss-cn-shanghai.aliyuncs.com";
		
		//判断本地是否存在游戏母包,不存在则从服务器取出
	    if(!file_exists($bag_url)){
		   //删除本地目录下所有文件
		  
		   $dh=opendir($file_url);
	       while ($file=readdir($dh)) {
		   if($file!="." && $file!="..") {
			   $fullpath=$dir."/".$file;
			   if(!is_dir($fullpath)) {
				  unlink($fullpath);
			   } else {
				  deldir($fullpath);
			  }
		    }
	       }

	       closedir($dh);	
		 
		   $exstr = "python /var/OSS_Python/osscmd get oss://osshouhuiyao/sdkgame".DIRECTORY_SEPARATOR.$gamedata['id'].'_'.$gamedata['initial']
		   .DIRECTORY_SEPARATOR.$gamedata['ghid'].DIRECTORY_SEPARATOR.$gamedata['packageurl']." ".$bag_url.$key;
		   exec($exstr,$out,$status);
		   if($status){
			  $re_arr = array(
					"code" => 	-7,
					"name" => "OSS包下载失败"
			);
			return $re_arr;
		   }
	    }

	    //判断是否存在母包
	    if(!file_exists($bag_url)){
		  $return_arr = array(
				"code" => 	-5,
				"name" => "母包不存在"
		  );
	    }
		
		//本地拷贝文件,做分包
	    if (!copy($bag_url, $file_url.$newbag)) {
		   //无法创建文件,打包失败
		   $return_arr = array(
				"code" => 	-1,
				"name" => "拷贝分包失败！"
		   );
	    }
		
		
		//渠道子渠道信息
		$aginfofile = "META-INF/gamechannel_".$agentgame."_.json";
		$namelist = explode("-",$agentdata['agentgame']);
		
		$zip = new \ZipArchive;
		
		if ($zip->open($file_url.$newbag) === TRUE) {
			
			$zip->addFromString($aginfofile, json_encode(array('agent'=>$agentgame)));
           
			$size = 1;
			if(!empty($namelist[4])){
			   $size = intval($namelist[4]);
			   $re ="s";
			   while(strlen($re) < $size) { 
                  $re .= "n"; //从$s中随机产生一个字符 
               }
			   
			}

	        $aa = $re."s".$size;
            $zip->setArchiveComment($aa);
			$zip->close();
		
			//本地包拷贝到OSS中
			$putexstr = "python /var/OSS_Python/osscmd put ".$file_url.$newbag." oss://osshouhuiyao/sdkgame/".$gamedata['id'].'_'.$gamedata['initial'].'/'.$gamedata['ghid']."/".$newbag.$key;
			
			exec($putexstr,$putout,$putstatus);
			
			//删除本地拷贝包
			if(file_exists($file_url.$newbag)){
				unlink($file_url.$newbag);
				unlink($bag_url);
			}
			
			//如果OSS存在服务器老包,删除
			$rmexstr = "python /var/OSS_Python/osscmd rm oss://osshouhuiyao/sdkgame/".$gamedata['id'].'_'.$gamedata['initial'].'/'.$gamedata['ghid']."/".$agentdata['filename'].$key;
			exec($rmexstr,$rmout,$rmstatus);
			
			/*if(!empty($agentdata['filename'] )){
				unlink($file_url.$agentdata['filename']);
			}*/
			
			$return_arr = array(
					"code" => 1,
					"name" => $newbag
			);
			
		}
     
        if (0 < $return_arr['code'] ){
            $updatedata['filename'] = $return_arr['name'];
            $agentmodel = M($this->dbname.'.agentlist', C('CDB_PREFIX'));
            $rs = $agentmodel->where(array('id'=>$agentdata['id']))->save($updatedata);
            $isajax ? $this->success('分包成功',U('Subpackage/index')) : $this->ajaxReturn(array('success'=>true,'msg'=>'分包成功','data'=>$agentdata),'JSON');
            exit;
        }else if(-7 == $return_content){
            $isajax ? $this->error('code='.$return_filename.',OSS错误.',U('Subpackage/index')) : $this->ajaxReturn(array('msg'=>'code='.$return_filename.',OSS错误.'),'JSON');
            exit;
        }else if (-6 == $return_content){
            $isajax ? $this->error('code='.$return_content.',拒绝访问',U('Subpackage/index')) : $this->ajaxReturn(array('msg'=>'code='.$return_content.',拒绝访问'),'JSON');
            exit;
        }else if(-5 == $return_content){
            $isajax ? $this->error('code='.$return_content.',游戏原包不存在.',U('Subpackage/index')) : $this->ajaxReturn(array('msg'=>'code='.$return_content.',游戏原包不存在.'),'JSON');
            exit;
        }else if (-4 == $return_content){
            $isajax ? $this->error('code='.$return_content.',验证错误',U('Subpackage/index')) : $this->ajaxReturn(array('msg'=>'code='.$return_content.',验证错误'),'JSON');
            exit;
        }else if(-3 == $return_content){
            $isajax ? $this->error('code='.$return_content.',请求数据为空',U('Subpackage/index')) : $this->ajaxReturn(array('msg'=>'code='.$return_content.',请求数据为空'),'JSON');
            exit;
        }else if(-2== $return_content){
            $isajax ? $this->error('code='.$return_content.',分包失败',U('Subpackage/index')) : $this->ajaxReturn(array('msg'=>'code='.$return_content.',分包失败'),'JSON');
            exit;
        }else if(-1 == $return_content){
            $isajax ? $this->error('code='.$return_content.',无法创建文件,打包失败.',U('Subpackage/index')) : $this->ajaxReturn(array('msg'=>'code='.$return_content.',无法创建文件,打包失败.'),'JSON');
            exit;
        }else{
            $isajax ? $this->error('code='.$return_content.',请求数据失败.',U('Subpackage/index')) : $this->ajaxReturn(array('msg'=>'code='.$return_content.',请求数据失败.'),'JSON');
            exit;
        }
        $isajax ? $this->error('操作失败',U('Subpackage/index')) : $this->ajaxReturn(array('msg'=>'操作失败'),'JSON');
        exit;
	}
	
	//更新包
	public function updatepackage() {
		$agmodel = M($this->dbname.'.agentlist', C('CDB_PREFIX'));
		$agid= intval(I("id"));

		$agentlist = $agmodel->where("id=%d",$agid)->find();
		$this->_subpackage($agentlist,true);		
	}
	
	
	
	/**
	 *删除渠道
	 */
	public function delAgent(){
		$model = M($this->dbname.'.sdkgamelist', C('CDB_PREFIX'));
		$agent_id = I('id');

		$sdkgamelist = $model->field("filename")->where("id=".$agent_id)->select();
		$filename = $sdkgamelist[0]['filename'];
		$filenamearr = explode("_",$filename);
		$file = $filenamearr[0];

		$rs = $model -> where("id='".$agent_id."' ") ->delete();
		if($rs){
			$path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
			$path .= DIRECTORY_SEPARATOR."download".DIRECTORY_SEPARATOR."testsdkgame".DIRECTORY_SEPARATOR;
			$path .= $file."/".$filename;
			if (file_exists($path)) {
				unlink($path);
			}
		    
			echo json_encode(array('success'=>true,'msg'=>'删除渠道成功.'));
			exit;
		} else {
			echo json_encode(array('msg'=>'删除渠道失败.'));
			exit;
		}
		
	}


	
	/*
	 * 向下载服务器发送请求数据
	 */
	function http_post_data($url, $data_string) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json; charset=utf-8',
				'Content-Length: ' . strlen($data_string))
		);
		ob_start();
		curl_exec($ch);
		$return_content = ob_get_contents();
		ob_end_clean();
	
		$return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		return $return_content;
	}
		
			
}