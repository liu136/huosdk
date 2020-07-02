<?php
// +---------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +---------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +---------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +---------------------------------------------------------------------

namespace Common\Lib;

/**
 * ThinkCMF权限认证类
 */
class iAuth{

    //默认配置
    protected $_config = array(
    );

    public function __construct() {
    }

    /**
      * 检查权限
      * @param name string|array  需要验证的规则列表,支持逗号分隔的权限规则或索引数组
      * @param uid  int           认证用户的id
      * @param relation string    如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
      * @return boolean           通过验证返回true;失败返回false
     */
    public function check($uid,$name,$relation='or') {
    	if(empty($uid)){
    		return false;
    	}
    	
        if (is_string($name)) {
            $name = strtolower($name);
            if (strpos($name, ',') !== false) {
                $name = explode(',', $name);
            } else {
                $name = array($name);
            }
        }
            
        $cid = sp_get_current_cid();
        $cmenumodel = D('Common/Menu');
        $namearr = explode('/', $name[0]);
        
        $where = array(
                "lower(app)"     => $namearr[0],
                "lower(model)"   => $namearr[1],
                "lower(action)"  => $namearr[2],
                "cid"            => array('IN', "0, $cid")
        );
        $cdata = $cmenumodel
        ->alias('c')
        ->field('type, cid')
        ->join("LEFT JOIN ".C('AUTH_DB_NAME').".l_client_cmenu cc ON cc.menu_id=c.id")
        ->where($where)
        ->find();
        
         /*
         * type=0 公共无权限菜单,所有渠道都能访问，无权限控制
         * type=1 公共有权限菜单,渠道拥有此菜单,不需要总后台授权,但有权限控制
         * type=2 菜单授权无权限菜单,渠道不拥有拥有此菜单,需要总后台授权,无权限控制
         * type=3 菜单授权有权限菜单,渠道需授权拥有此菜单,有权限控制
         */
//         if(!empty($cdata['type']) && empty($cdata['cid'])){
//             return false;
//         }
        
        if (0 == $cdata['type'] || 2 == $cdata['type']){
            return true;
        }
        
        if(sp_get_current_roletype()==1){
            return true;
        }
        
        $list = array(); //保存验证通过的规则名
        
        $role_user_model=M(C('AUTH_DB_NAME').'.clientrole_user','l_');
        
        $role_user_join = C('AUTH_DB_NAME').'.l_clientrole as b on a.role_id = b.id';

        $groups=$role_user_model->alias("a")->join($role_user_join)->where(array("user_id"=>$uid,"status"=>1))->getField("role_id",true);
       
        if(in_array(1, $groups)){
        	return true;
        }

        if(empty($groups)){
        	return false;
        }
        
        $auth_access_model=M(C('AUTH_DB_NAME').'.clientauth_access','l_');
        
        $join = C('AUTH_DB_NAME').'.l_clientauth_rule as b on a.rule_name =b.name';
        
        $rules=$auth_access_model->alias("a")->join($join)->where(array("a.role_id"=>array("in",$groups),"b.name"=>array("in",$name)))->select();
        
        foreach ($rules as $rule){
        	if (!empty($rule['condition'])) { //根据condition进行验证
        		$user = $this->getUserInfo($uid);//获取用户信息,一维数组
        	
        		$command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
        		//dump($command);//debug
        		@(eval('$condition=(' . $command . ');'));
        		if ($condition) {
        			$list[] = strtolower($rule['name']);
        		}
        	}else{
        		$list[] = strtolower($rule['name']);
        	}
        }
        
        if ($relation == 'or' and !empty($list)) {
            return true;
        }
        $diff = array_diff($name, $list);
        if ($relation == 'and' and empty($diff)) {
            return true;
        }
        return false;
    }
    
    /**
     * 获得用户资料
     */
    private function getUserInfo($uid) {
    	static $userinfo=array();
    	if(!isset($userinfo[$uid])){
    		$userinfo[$uid]= M(C('AUTH_DB_NAME').'.clientusers','l_')->where(array('id'=>$uid))->find();
    	}
    	return $userinfo[$uid];
    }

}
