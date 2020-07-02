<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class UserController extends AdminbaseController{
	protected $users_model,$role_model;
	
	function _initialize() {
		parent::_initialize();
		
		$this->role_model = M(C('MNG_DB_NAME').".clientrole", C('LDB_PREFIX'));
		$this->users_model = D("Common/Users");//M(C('MNG_DB_NAME').".clientusers", C('LDB_PREFIX'));
		
	}
	function index(){
		$this->adminCheck();
		
	    $cid = sp_get_current_cid();

		$where['u.cid'] = $cid;
		$where['u.role_id'] = array('NEQ',1);
		$where['cr.typeid'] = array('EQ',2);

		$count=$this->users_model
			        ->alias('u')
		            ->join("left join ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole cr on cr.id = u.role_id")
			        ->where($where)
			        ->count();

		$page = $this->page($count, $this->row);
		$users = $this->users_model
		->alias('u')
		->field('u.*')
		->join("left join ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole cr on cr.id = u.role_id")
		->where($where)
		->order("u.create_time DESC")
		->limit($page->firstRow . ',' . $page->listRows)
		->select();
		$this->assign("page", $page->show('Admin'));		
		$this->assign("users", $users);
		
		$this->_getRoles();
		$this->display();
	}
	
	function _getRoles(){
	    $cid = sp_get_current_cid();
	    $where['cid'] = $cid;

	    $roles=$this->role_model->where($where)->getField('id, name');
		
	    $this->assign("roles", $roles);
	}
	
	//管理员能添加全角色，除超级管理员
	function _getAdminRoles(){
	    $cid = sp_get_current_cid();
	    //$where['cid'] = array('in', "0, $cid");
	    $where['status'] = 1;
		$where['cid'] = array('in', "0, $cid");
	    $where['typeid'] = array('eq',2);
		
		$roles=$this->role_model->where($where)->order("id desc")->select();
		$this->assign("roles",$roles);
	}
	
	function adminCheck(){
		if(2 < sp_get_current_roletype()){
		        $this->error('无权限访问');
		}
	}
	
	function addAdmin(){
		$this->adminCheck();
	    $this->_getAdminRoles();
	    $this->display('User/add');
	}
	
	//渠道拥有的角色
	function _getAgentRoles(){
	    $cid = sp_get_current_cid();
		$roletype = sp_get_current_roletype();
	    $where['cid'] = array('in', "$cid");
		//$where['cid'] = array('eq', "0");
	    $where['status'] = 1;
		$where['typeid'] = 3;
        
	    $roles=$this->role_model->where($where)->order("id desc")->select();
		
	    $this->assign("roles",$roles);
	}
	
	function addAgent(){
		
		$adminid = sp_get_current_admin_id();
		$roletype = sp_get_current_roletype();
		
	    $this->_getAgentRoles();
		
		/*if($roletype <= 2){
			$this->_getUsergames(0);
		    $this->_getNousergames(0);
		}else{
			$this->_getUsergames(0);
		    $this->_getNousergames($adminid);
		}*/
		
		//$salesArr = M("$this->dbname.salesman", C('CDB_PREFIX'))->field("id,salename")->select();
		//$this->assign('salesArr',$salesArr);
		
		
	    $this->display('Agent/add');
	}

	
	function add_post(){
		if(IS_POST){
			$repwd = I('user_repass');
			$role_id = I('post.role_id');
			$to = I('to');
			//unset($_POST['role_id']);
// 			$this->_authPaypwd($repwd);
			if(!empty($role_id) && is_numeric($role_id)){
				////
				////
				$user_id = sp_get_current_admin_id();
				
				$model = M();
                $model->startTrans();
				
                $users = $model->table(C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientusers")->field("id,cid,lft,rgt")->where(array("id"=>$user_id))->find();
                
				$data['user_login'] = I('user_login');
				$data['user_nicename'] = trim(I('user_nicename'));
				$user_pass = trim(I('user_pass'));
				$data['user_pass']=sp_password($user_pass);
                $data['pay_pwd'] = $data['user_pass'];
				$data['user_email'] = I('user_email');
				$data['role_id'] = $role_id;
				$data['parentid'] = get_current_admin_id();
				$data['cid'] = sp_get_current_cid();
				
				$salesid = I('salesid');
				if($salesid){
				    $data['salesid'] = I('salesid');
				}
				//$data['salesid'] = I('salesid');
                
				if(empty($data['user_login']) || empty($user_pass)){
					$this->error("账号密码不能为空");
					exit;
				}else if(empty($data['user_email'])){
					$this->error("邮箱不能为空");
					exit;
				}
				
				$roletype = sp_get_current_roletype();
				$role_data = $this->role_model->field("typeid")->where(array("id"=>$roletype))->find();
	            if ($role_data['typeid'] > 2 && $roletype <= 2){
					$this->error("您没有权限创建该角色账号");
					exit;
				}
				
				$pre_user = $model->table(C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientusers")->field("id")->where(array("user_login"=>$data['user_login']))->find();
			    if(!empty($pre_user)){
					$this->error("账号已存在");
					exit;
				}

				//检查邮箱重复
				$chk_user_email = $model->table(C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientusers")->field("id")->where(array("user_email"=>$data['user_email']))->find();
				if(!empty($chk_user_email)){
					$this->error("邮箱已占用");
					exit;
				}
				
				 $result = $model->table(C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientusers")->add($data);
				 
			     if($result){
					
                      $rs = $model->table(C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole_user")->add(array("role_id"=>$role_id,"user_id"=>$result));
					  
                      if($rs){
						  
						    $role_data = $model->table(C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole")->where(array("id"=>$role_id))->find();
							
				            /*if($role_data['typeid'] > 2 && count($to) > 0){
					            $appids = implode(",",$to);
					            $ugame_data['agentid'] = $result;
					            $ugame_data['appids'] = $appids;
								$ugame_data['cid'] = sp_get_current_cid();
								$ugame_data['update_time'] = time();
								
								$ugame = $model->table(C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientusers_game")->where(array("agentid"=>$result))->find();
								if(empty($ugame)){
									$urs = $model->table(C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientusers_game")->add($ugame_data);
								}else{
									$urs = $model->table(C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientusers_game")->where(array('agentid'=>$result))->save($ugame_data);
								}
								
								if($urs != true){
									$model->rollback();
									$this->error("游戏分配失败");
									exit;
								}
				            }*/
							$this->insertLog(1, "添加账号-$result-".I('post.user_login'));
							
							$update_data = array('lft'=>0,'rgt'=>0);
							$rs = $model->table(C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientusers")->where(array("cid"=>$data['cid']))->setField($update_data);
							
							$rss = $model->table(C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientusers")->execute(" CALL sp_updateoneTree(".$data['cid'].")");
					        if($rs){
								$model->commit();
								
								$this->success("添加成功！", $succ_url);
							}
							exit;
							
					 }
				 }
                 $this->model->rollback();
				     
		   }
		}
		$this->error("添加失败！");
		exit;
	}
	
	function editadmin(){
		$this->adminCheck();
	    $this->_edit();
	    $this->_getAdminRoles();
	    $this->display('User/edit');
	}
	
	function editagent(){
	    $this->_edit();
	    $this->_getAgentRoles();
	    $this->display('Agent/edit');
	}
	
	function _edit(){
	    $id= intval(I("get.id"));
		$role_ids=$this->users_model->where(array("id"=>$id))->getField("role_id",true);
		$this->assign("role_ids",$role_ids);
	    	
	    $user=$this->users_model->where(array("id"=>$id))->find();
	    $this->assign($user);
	}
	
	function edit_post(){
		if (IS_POST) {
			$id = I('id');
			//$repwd = I('user_pass');
			$role_id = I('role_id');
			//unset($_POST['role_id']);
// 			$this->_authPaypwd($repwd);
			$chk_users = $this->users_model->where(array("id"=>$id))->find();
		
		    if($chk_users['role_id']==1){
				if($role_id != 1 || $user_login != $chk_users['user_login']){
                   $this->error("最高管理员的账号和角色不能编辑！");
				   exit;
				}
			    
		    }
			
			if(!empty($role_id) && is_numeric($role_id)){
				if ($this->users_model->create()) {
					$result=$this->users_model->save();
					if ($result!==false) {
					    $role_user_model=M(C('AUTH_DB_NAME').".clientrole_user", C('LDB_PREFIX'));
					    
						$uid=intval(I('post.id'));
						$role_user_model->where(array("user_id"=>$uid))->delete();
						$role_user_model->add(array("role_id"=>$role_id,"user_id"=>$uid));
						
						$admindata['id'] = $uid;
						$admindata['cid'] = sp_get_current_cid();
						$admindata['user_nicename'] = trim(I('post.user_nicename'));
						$admindata['role_id'] = $role_id;

						M($this->dbname.'.clientusers', C('CDB_PREFIX'))->save($admindata);
						
						$this->insertLog(2, "更新管理员信息-$uid-".$user_login);
						$this->success("保存成功！");
					} else {
						$this->error("保存失败！");
					}
				} else {
					$this->error($this->users_model->getError());
				}
			}else{
				$this->error("请为此用户指定角色！");
			}
		}
	}
	
	function editagentgame(){
	    $this->_editagentgame();
	    $this->display('Agent/editagentgame');
	}
	
	function _editagentgame(){
	    $id= intval(I("get.id"));
		
		$this->_getUsergames($id);
		$this->_getNousergames($id);
	    $user = $this->users_model->where(array("id"=>$id))->find();
	    $this->assign($user);
	}
	
	function editagentgame_post(){
		if (IS_POST) {
			$id = I('id');
            $to = I('to');
			
			$chk_users = $this->users_model->where(array("id"=>$id))->find();
			
			$role_data = $this->users_model->table(C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole")->where(array("id"=>$chk_users['role_id']))->find();
			
		    if($role_data['typeid'] > 2 && count($to) > 0){
					$appids = implode(",",$to);
					
					$ugame_data['appids'] = $appids;
					$ugame_data['cid'] = sp_get_current_cid();
					$ugame_data['update_time'] = time();
					
					$ugamemodel = M(C('AUTH_DB_NAME')."."."clientusers_game",C('LDB_PREFIX'));
					$ugame = $ugamemodel->where(array("agentid"=>$id))->find();
					if(empty($ugame)){
						$ugame_data['agentid'] = $id;
						$urs = $ugamemodel->add($ugame_data);
					}else{
						$userids = $this->getAgentids($id);
						$where['agentid'] = array("in",$userids);
						$urs = $ugamemodel->where($where)->save($ugame_data);
					}
								
					if($urs != false){
						$this->insertLog(2, "更新游戏权限-$uid-".$user_login);
						$this->success("保存成功！");
						exit;
					}
			}
		}
		$this->error("保存失败！");
		exit;
	}
	
	/**
	 *  删除
	 */
	function delete(){	    
		$id = intval(I("get.id"));
		$chk_users = $this->users_model
			              ->field("u.role_id,u.user_login,cr.typeid")
			              ->alias('u')
		                  ->join("left join ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole cr on cr.id = u.role_id")
			              ->where('u.id = '.$id)
			              ->find();
		
		if($chk_users['role_id']==1){
			$this->error("最高管理员不能删除！");
			exit;
		}

		if($chk_users['typeid'] != 2){
			$this->error("此账号不属于内部管理角色");
			exit;
		}

		$this->users_model->startTrans();
		
		$cid = sp_get_current_cid();
		$rs = $this->users_model->where(array('id'=>$id, 'cid'=>$cid))->delete();

		if($rs != false){
			$crs = $this->users_model->table(C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole_user")->where(array("user_id"=>$id))->delete();
            if($crs){
                $rs = $this->users_model->table($this->dbname.".".C('LDB_PREFIX')."clientusers")->where(array("id"=>$id))->delete();
                if($rs){
                    $this->insertLog(3, "删除管理员-$id-".$chk_users['user_login']);
					$this->users_model->commit();
			        $this->success("删除成功！");
				    exit;
				}
			}
		    $this->users_model->rollback();	
		}
		$this->error("删除失败！");
		exit;
	}
	
	function ban(){
        $id=intval($_GET['id']);
        
		
    	if ($id) {
			$item = $this->users_model
		    ->alias('cu')
		    ->field('cu.id,cr.typeid')
		    ->join("inner join ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole cr on cu.role_id = cr.id")
		    ->where("cu.id = $id")
		    ->find();
		    if($item['typeid'] > 2 || empty($item['id'])){
			   $this->error('不允许操作该角色',U('User/index'));
               exit;
		    }
		
    		$rst = $this->users_model->where(array("id"=>$id, "cid"=>$_SESSION['cid']))->setField('user_status','0');
		    
    		if ($rst) {
    		    $this->insertLog(2, "停用了管理员账号-".$id);
    			$this->success("管理员停用成功！", U("user/index"));
    		} else {
    			$this->error('管理员停用失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
    function cancelban(){
        $id=intval($_GET['id']);
    	if ($id) {
			
			$item = $this->users_model
		    ->alias('cu')
		    ->field('cu.id,cr.typeid')
		    ->join("inner join ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole cr on cu.role_id = cr.id")
		    ->where("cu.id = $id")
		    ->find();
			
		    if($item['typeid'] > 2 || empty($item['id'])){
			   $this->error('不允许操作该角色',U('User/index'));
               exit;
		    }
			
    		$rst = $this->users_model->where(array("id"=>$id,"cid"=>$_SESSION['cid']))->setField('user_status','1');
    		if ($rst) {
    		    $this->insertLog(2, "启用管理员账号-".$id);
    			$this->success("管理员启用成功！", U("user/index"));
    		} else {
    			$this->error('管理员启用失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
	
    function userinfo(){
        $id=get_current_admin_id();
        $user=$this->users_model->field("id,user_nicename,user_email,mobile")->where(array("id"=>$id, "cid"=>$_SESSION['cid']))->find();
        $this->assign($user);
        $this->display();
    }
    
    function userinfo_post(){
        if (IS_POST) {
            $_POST['id']=get_current_admin_id();
            $create_result=$this->users_model
            ->field("cid, user_login, last_login_ip,last_login_time,create_time,user_status,parentid,score,role_id",true)//排除相关字段
            ->create();
            if ($create_result) {
                if ($this->users_model->save()!==false) {
                    $this->insertLog(2, "更新自己用户信息");
                    $this->success("保存成功！");
                } else {
                    $this->error("保存失败！");
                }
            } else {
                $this->error($this->users_model->getError());
            }
        }
    }

    //账号使用平台币解除禁用
    function cancelban_status(){
    	$id = I('id');
		$cid = sp_get_current_cid();
        if(empty($id)){
            $this->error('非法操作',U('User/index'));
            exit;
        }
        $item = $this->users_model
		->alias('cu')
		->field('cu.id,cr.typeid')
		->join("inner join ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole cr on cu.role_id = cr.id")
		->where("cu.id = $id")
		->find();
		if($item['typeid'] > 2 || empty($item['id'])){
			$this->error('不允许操作该角色',U('User/index'));
            exit;
		}

        $res = $this->users_model->where("id = $id and cid = $cid")->setField('coin_status',1);
        $adminid = sp_get_current_admin_id();
        if($res){
            $pwdwrong_model = M(C('MNG_DB_NAME').".coinpwd_wrong",C('LDB_PREFIX'));
            $pwdwrong_model->where("adminid = ".$adminid)->setField(array('count'=>0,'date'=>0));//清除渠道密码错误记录
            $this->success('解除禁用平台币充值操作成功',U('User/index'));
            exit;
        }else{
            $this->error('解除禁用平台币充值操作失败，请重新尝试！',U('User/index'));
            exit;
        }
    }
	
	//账号使用平台币禁用
	function ban_status(){
		$id = I('id');
		$cid = sp_get_current_cid();
        if(empty($id)){
            $this->error('非法操作',U('User/index'));
            exit;
        }
		$item = $this->users_model
		->alias('cu')
		->field('cu.id,cr.typeid')
		->join("inner join ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole cr on cu.role_id = cr.id")
		->where("cu.id = $id")
		->find();
		if($item['typeid'] > 2 || empty($item['id'])){
			$this->error('不允许操作该角色',U('User/index'));
            exit;
		}

        $res = $this->users_model->where("id = $id and cid = $cid")->setField('coin_status',2);
        if($res){
            $this->success('禁用平台币充值操作成功',U('User/index'));
            exit;
        }else{
            $this->error('禁用平台币充值操作成功，请重新尝试！',U('User/index'));
            exit;
        }
	}

	//二级密码重置
    public function coinpwd_reset(){
        $id = I('id');
        $cid = sp_get_current_cid();
        if(empty($id)){
            $this->error('非法操作',U('User/index'));
            exit;
        }
        $item = $this->users_model
		->alias('cu')
		->field('cu.id,cr.typeid')
		->join("inner join ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole cr on cu.role_id = cr.id")
		->where("cu.id = $id")
		->find();
		if($item['typeid'] > 2 || empty($item['id'])){
			$this->error('不允许操作该角色',U('User/index'));
            exit;
		}

        $res = $this->users_model->where("id = $id and cid = $cid")->setField('coin_pwd',null);
        if($res){
            $this->success('重置二级密码成功！',U('User/index'));
        }else{
            $this->error('重置二级密码失败，请重试！',U('User/index'));
        }
    }
    
   
    
}