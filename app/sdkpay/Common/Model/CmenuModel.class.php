<?php

/* * 
 * 渠道菜单管理
 */
namespace Common\Model;
use Common\Model\CommonModel;
class CmenuModel extends CommonModel {
    protected $trueTableName = 'l_cmenu';
    protected $dbName = 'db_league_auth';
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('name', 'require', '菜单名称不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
        array('app', 'require', '应用不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
        array('model', 'require', '模块名称不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
        array('action', 'require', '方法名称不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
        array('app,model,action', 'checkAction', '同样的记录已经存在！', 1, 'callback', CommonModel:: MODEL_INSERT   ),
    	array('id,app,model,action', 'checkActionUpdate', '同样的记录已经存在！', 1, 'callback', CommonModel:: MODEL_UPDATE   ),
        array('parentid', 'checkParentid', '菜单只支持四级！', 1, 'callback', 1),
    );
    //自动完成
    protected $_auto = array(
            //array(填充字段,填充内容,填充条件,附加规则)
            array('is_cp', 0),
    );

    //验证菜单是否超出三级
    public function checkParentid($parentid) {
        $find = $this->where(array("id" => $parentid))->getField("parentid");
        if ($find) {
            $find2 = $this->where(array("id" => $find))->getField("parentid");
            if ($find2) {
                $find3 = $this->where(array("id" => $find2))->getField("parentid");
                if ($find3) {
                    return false;
                }
            }
        }
        return true;
    }

    //验证action是否重复添加
    public function checkAction($data) {
        //检查是否重复添加
        $find = $this->where($data)->find();
        if ($find) {
            return false;
        }
        return true;
    }
    
    //验证action是否重复添加
    public function checkActionUpdate($data) {
    	//检查是否重复添加
    	$id=$data['id'];
    	unset($data['id']);
    	$find = $this->field('id')->where($data)->find();
    	if (isset($find['id']) && $find['id']!=$id) {
    		return false;
    	}
    	return true;
    }

    /**
     * 按父ID查找菜单子项
     * @param integer $cid        渠道ID
     * @param integer $parentid   父菜单ID  
     * @param integer $with_self  是否包括他自己
     */
    public function admin_menu($cid, $iscp, $parentid, $with_self = false) {
        //父节点ID
        $parentid = (int) $parentid;
        $result = $this
        ->join("inner join ".C('AUTH_DB_NAME').".l_client_cmenu ON l_client_cmenu.menu_id=l_cmenu.id")
        ->where(array('parentid' => $parentid, 'status' => 1, 'cid' => $cid, 'is_cp'=>$iscp))
        ->order(array("listorder" => "ASC"))
        ->select();
        if ($with_self) {
            $result2[] = $this
            ->join("inner join l_client_cmenu ON l_client_cmenu.menu_id=l_cmenu.id")
            ->where(array('id' => $parentid, 'cid' => $cid))
            ->find();
            $result = array_merge($result2, $result);
        }
        //权限检查
        if (sp_get_current_usertype() == 1) {
            //如果是超级管理员 直接通过
            return $result;
        } 
        
        $array = array();
        foreach ($result as $v) {        	
            //方法
            $action = $v['action'];
            
            //public开头的通过
            if (preg_match('/^public_/', $action)) {
                $array[] = $v;
            } else {            	
                if (preg_match('/^ajax_([a-z]+)_/', $action, $_match)){                	
                	$action = $_match[1];
                }
                   
                $rule_name=strtolower($v['app']."/".$v['model']."/".$action);                
                if (sp_auth_check(sp_get_current_admin_id(),$rule_name)){
                	$array[] = $v;
                }                   
            }
        }
        return $array;
    }

    /**
     * 获取菜单 头部菜单导航
     * @param $parentid 菜单id
     */
    public function submenu($cid,$iscp, $parentid = '', $big_menu = false) {
        $array = $this->admin_menu($cid, $iscp, $parentid, 1);
        $numbers = count($array);
        if ($numbers == 1 && !$big_menu) {
            return '';
        }
        return $array;
    }

    /**
     * 菜单树状结构集合
     */
    public function menu_json($cid) {
        if (!is_numeric($cid) && $cid < 1){
            return NULL;
        }
        $data = $this->get_tree($cid,0, 0);
        return $data;
    }
    
    
    /**
     * 开发者菜单树状结构集合
     */
    public function devmenu_json($cid) {
        if (!is_numeric($cid) && $cid < 1){
            return NULL;
        }
        $data = $this->get_tree($cid, 1, 0);
        return $data;
    }

    //取得树形结构的菜单
    public function get_tree($cid, $iscp, $myid, $parent = "", $Level = 1) {        
        $ret = array();
        $data = $this->admin_menu($cid,$iscp, $myid);
        $Level++;
        if (is_array($data)) {
            foreach ($data as $a) {
                $id = $a['id'];
                $name = ucwords($a['app']);
                $model = ucwords($a['model']);
                $action = $a['action'];
                //附带参数
              	$fu = "";
                if ($a['data']) {
                    $fu = "?" . htmlspecialchars_decode($a['data']);
                }
                $array = array(
                    "icon" => $a['icon'],
                    "id" => $id . $name,
                    "name" => $a['name'],
                    "parent" => $parent,
                    "url" => U("{$name}/{$model}/{$action}{$fu}", array("menuid" => $id)),
                    'lang'=> strtoupper($name.'_'.$model.'_'.$action)
                ); 
                
                
                
                $ret[$id . $name] = $array;
                $child = $this->get_tree($cid,$iscp, $a['id'], $id, $Level);
                //由于后台管理界面只支持三层，超出的不层级的不显示
                if ($child && $Level <= 3) {
                    $ret[$id . $name]['items'] = $child;
                }
               
            }            
            return $ret;
        }
       
        return false;
    }

    /**
     * 更新缓存
     * @param type $data
     * @return type
     */
    public function menu_cache($data = null) {
        if (empty($data)) {
            $data = $this->where(array("is_cp"=>0))->select();
            F("Cmenu", $data);
        } else {
            F("Cmenu", $data);
        }
        return $data;
    }

    /**
     * 后台有更新/编辑则删除缓存
     * @param type $data
     */
    public function _before_write(&$data) {
        parent::_before_write($data);
        F("Cmenu", NULL);
    }

    //删除操作时删除缓存
    public function _after_delete($data, $options) {
        parent::_after_delete($data, $options);
        $this->_before_write($data);
    }
    
    public function menu($parentid, $with_self = false){
    	//父节点ID
    	$parentid = (int) $parentid;
    	$result = $this->where(array('parentid' => $parentid))->select();
    	if ($with_self) {
    		$result2[] = $this->where(array('id' => $parentid))->find();
    		$result = array_merge($result2, $result);
    	}
    	return $result;
    }
    /**
     * 得到某父级菜单所有子菜单，包括自己
     * @param number $parentid 
     */
    public function get_menu_tree($parentid=0){
    	$menus=$this->where(array("parentid"=>$parentid))->order(array("listorder"=>"ASC"))->select();
    	
    	if($menus){
    		foreach ($menus as $key=>$menu){
    			$children=$this->get_menu_tree($menu['id']);
    			if(!empty($children)){
    				$menus[$key]['children']=$children;
    			}
    			unset($menus[$key]['id']);
    			unset($menus[$key]['parentid']);
    		}
    		return $menus;
    	}else{
    		return $menus;
    	}
    	
    }

}

