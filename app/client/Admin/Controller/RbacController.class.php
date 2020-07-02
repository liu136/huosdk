<?php

/* * 
 * 系统权限配置，用户角色管理
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class RbacController extends AdminbaseController {

    protected $role_model, $auth_access_model,$menu_model;

    function _initialize() {
        parent::_initialize();
		
		{
		    if(2 < sp_get_current_roletype()){
		        $this->error('无权限访问');
		    }
		}
		
        $this->role_model = D("Common/Role");
		$this->menu_model = M(C('AUTH_DB_NAME').".cmenu",C('LDB_PREFIX'));
    }

    /**
     * 角色管理，有add添加，edit编辑，delete删除
     */
    public function index() {
        $cid = sp_get_current_cid();
        $where['cid'] = array('IN', "$cid");
        //$where['status'] = 1;
        $data = $this->role_model->where($where)
        ->order(array("listorder" => "asc", "id" => "desc"))->select();        
        $this->assign("roles", $data);
        $this->display();
    }

    /**
     * 添加角色
     */
    public function roleadd() {
        $this->display();
    }
    
    /**
     * 添加角色
     */
    public function roleadd_post() {
    	if (IS_POST) {
    		if ($this->role_model->create()) {
    			if (($id = $this->role_model->add())!==false) {
    			    $this->insertLog(1, "添加角色-$id-". I('post.name'));
    				$this->success("添加角色成功",U("rbac/index"));
    			} else {
    				$this->error("添加失败！");
    			}
    		} else {
    			$this->error($this->role_model->getError());
    		}
    	}
    }

    /**
     * 删除角色
     */
    public function roledelete() {
        $typeid = intval(I("get.typeid"));
        if ($typeid == 1) {
            $this->error("超级管理员角色不能被删除！");
        }        
        $id = intval(I("get.id"));        
        $role_user_model=M(C('AUTH_DB_NAME').".clientrole_user",'l_');
        $count=$role_user_model->where(array("role_id"=>$id))->count();
        if($count){
        	$this->error("该角色已经有用户,请先删除用户！");
        }else{
            $rolename = $this->role_model->where()->getField('name');
        	$status = $this->role_model->delete($id);
        	if ($status!==false) {
        	    $this->insertLog(3, "删除角色-$id-".I('get.name'));
        		$this->success("删除成功！", U('Rbac/index'));
        	} else {
        		$this->error("删除失败！");
        	}
        }
        
    }

    /**
     * 编辑角色
     */
    public function roleedit() {
        $id = intval(I("get.id"));
        if ($id == 0) {
            $id = intval(I("post.id"));
        }
        
        $typeid = intval(I("get.typeid"));
        if ($typeid == 0) {
            $typeid = intval(I("post.typeid"));
        }
        
        if ($id < 50) {
            $this->error("系统默认角色不能被修改！");
        }
        
        if ($typeid == 1) {
            $this->error("超级管理员角色不能被修改！");
        }
        
        $data = $this->role_model->where(array("id" => $id))->find();
        if (!$data) {
        	$this->error("该角色不存在！");
        }
        $this->assign("data", $data);
        $this->display();
    }
    
    /**
     * 编辑角色
     */
    public function roleedit_post() {
    	$id = intval(I("get.id"));
    	if ($id == 0) {
    		$id = intval(I("post.id"));
    	}
    	
        if ($id < 50) {
            $this->error("系统默认角色不能被修改！");
        }
        
    	if (IS_POST) {
    		$data = $this->role_model->create();
    		if ($data) {
    			if ($this->role_model->save($data)!==false) {
    			    $this->insertLog(2, "修改角色-$id-".I('post.name'));
    				$this->success("修改成功！", U('Rbac/index'));
    			} else {
    				$this->error("修改失败！");
    			}
    		} else {
    			$this->error($this->role_model->getError());
    		}
    	}
    }

    /**
     * 角色授权
     */
    public function authorize() {
        $this->auth_access_model = M(C('AUTH_DB_NAME').".clientauth_access",C('LDB_PREFIX'));
        //角色ID
        $roleid = intval(I("get.id"));
        if (!$roleid) {
        	$this->error("参数错误！");
        }
        import("Tree");
        $menu = new \Tree();
        $menu->icon = array('│ ', '├─ ', '└─ ');
        $menu->nbsp = '&nbsp;&nbsp;&nbsp;';
        //$result = $this->initMenu();
		$cid = 0;
		
		$where['cm.cid'] = $cid;
		
		$clientrole_model = M(C("AUTH_DB_NAME")."."."clientrole", C('LDB_PREFIX'));
		$role = $clientrole_model
				       ->where(array("id"=>$roleid))
					   ->find();
		if($role['typeid'] > 2){
			$where['a.grade'] = array("neq",1);
		}
		
		$result = $this->menu_model
			      ->alias("a")
			      ->join("INNER JOIN ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."client_cmenu cm ON a.id = cm.menu_id")
			      ->where($where)
			      ->order(array("listorder" => "ASC"))->select();
		
        $newmenus=array();
        $priv_data=$this->auth_access_model->where(array("role_id"=>$roleid))->getField("rule_name",true);//获取权限表数据
        foreach ($result as $m){
        	$newmenus[$m['id']]=$m;
        }
        
        foreach ($result as $n => $t) {
        	$result[$n]['checked'] = ($this->_is_checked($t, $roleid, $priv_data)) ? ' checked' : '';
        	$result[$n]['level'] = $this->_get_level($t['id'], $newmenus);
        	$result[$n]['parentid_node'] = ($t['parentid']) ? ' class="child-of-node-' . $t['parentid'] . '"' : '';
        }
        $str = "<tr id='node-\$id' \$parentid_node>
                       <td style='padding-left:30px;'>\$spacer<input type='checkbox' name='menuid[]' value='\$id' level='\$level' \$checked onclick='javascript:checknode(this);'> \$name</td>
	    			</tr>";
        $menu->init($result);
        $categorys = $menu->get_tree(0, $str);
        
        $this->assign("categorys", $categorys);
        $this->assign("roleid", $roleid);
        $this->display();
    }
    
    /**
     * 角色授权
     */
    public function authorize_post() {
    	$this->auth_access_model = M(C('AUTH_DB_NAME').".clientauth_access",'l_');
    	if (IS_POST) {
    		$roleid = intval(I("post.roleid"));
    		
    		if(!$roleid){
    			$this->error("需要授权的角色不存在！");
    		}
    		if (is_array($_POST['menuid']) && count($_POST['menuid'])>0) {
				$cid = sp_get_current_cid();
				$clientrole_model = M(C("AUTH_DB_NAME")."."."clientrole", C('LDB_PREFIX'));
                $role = $clientrole_model
				       ->where(array("id"=>$roleid))
					   ->find();
		        
    			$menu_model = M(C('AUTH_DB_NAME').".cmenu",'l_');
    			$auth_rule_model = M(C('AUTH_DB_NAME').".clientauth_rule",'l_');
    			$this->auth_access_model->where(array("role_id"=>$roleid,'type'=>'admin_url'))->delete();
    			foreach ($_POST['menuid'] as $menuid) {
					$where['id'] = $menuid;
					$where['cid'] = $cid;
					if($role['typeid'] > 2){
			           $where['grade'] = array("neq",1);
		            }    	
				
    				$menu=$menu_model->where($where)->field("app,model,action")->find();
					
    				if($menu){
    					$app=$menu['app'];
    					$model=$menu['model'];
    					$action=$menu['action'];
    					$name=strtolower("$app/$model/$action");
    					$this->auth_access_model->add(array("role_id"=>$roleid,"rule_name"=>$name,'type'=>'admin_url'));
    				}
    			}
                $this->insertLog(1, "角色-$roleid 授权成功");
    			$this->success("授权成功！", U("Rbac/index"));
    		}else{
    			//当没有数据时，清除当前角色授权
    			$this->auth_access_model->where(array("role_id" => $roleid))->delete();
    			$this->error("没有接收到数据，执行清除授权成功！");
    		}
    	}
    }
    /**
     *  检查指定菜单是否有权限
     * @param array $menu menu表中数组
     * @param int $roleid 需要检查的角色ID
     */
    private function _is_checked($menu, $roleid, $priv_data) {
    	
    	$app=$menu['app'];
    	$model=$menu['model'];
    	$action=$menu['action'];
    	$name=strtolower("$app/$model/$action");
    	if($priv_data){
	    	if (in_array($name, $priv_data)) {
	    		return true;
	    	} else {
	    		return false;
	    	}
    	}else{
    		return false;
    	}
    	
    }

    /**
     * 获取菜单深度
     * @param $id
     * @param $array
     * @param $i
     */
    protected function _get_level($id, $array = array(), $i = 0) {
    	if ($array[$id]['parentid']==0 || empty($array[$array[$id]['parentid']]) || $array[$id]['parentid']==$id){
    		return  $i;
    	}else{
    		$i++;
    		return $this->_get_level($array[$id]['parentid'],$array,$i);
    	}
    }
    
    public function member(){
    	//TODO 添加角色成员管理
    	
    }

}

