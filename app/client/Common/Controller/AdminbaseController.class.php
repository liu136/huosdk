<?php

/**
 * 后台Controller
 */
namespace Common\Controller;
use Common\Controller\AppframeController;

class AdminbaseController extends AppframeController {
    
    protected $dbname, $row;
    
	public function __construct() {
		
		$admintpl_path=C("SP_ADMIN_TMPL_PATH").C("SP_ADMIN_DEFAULT_THEME")."/";
		C("TMPL_ACTION_SUCCESS",$admintpl_path.C("SP_ADMIN_TMPL_ACTION_SUCCESS"));
		C("TMPL_ACTION_ERROR",$admintpl_path.C("SP_ADMIN_TMPL_ACTION_ERROR"));
		parent::__construct();
		$time=time();
		$this->assign("js_debug",APP_DEBUG?"?v=$time":"");
		
	}

    function _initialize(){
        parent::_initialize();
        $this->load_app_admin_menu_lang();
        if(strtolower(CONTROLLER_NAME) != 'checkcode'){
            if(isset($_SESSION['MNG_ID'])){
                $id=$_SESSION['MNG_ID'];
                $cid=$_SESSION['cid'];
                
        		$users_obj = M(C('MNG_DB_NAME').'.clientusers', C('LDB_PREFIX'));
        		$user=$users_obj->field('id, user_login, user_nicename,level')->where(array('id'=>$id, 'cid'=>$cid))->find();
				
        		if(!$this->check_access($id)){
        			$this->error("您没有访问权限！");
        		}
        	
        		$this->dbname = "db_sdk_2";
        		$this->row = 10;
				$this->getcharactors();
        		$this->assign("clientuser",$user);
        	}else{
				if(IS_AJAX){
    			    $this->error("您还没有登录！",U("public/login"));
    		    }else{
					$this->redirect("public/login");
    			   //header("Location:".U("public/login"));
    			   exit();
    		    }
                
        	}
        }
    }
    
	
    /**
     * 初始化后台菜单
     */
    public function initMenu() {
        $Menu = F("Menu");
        
        foreach($Menu as $key=>$value){
            unset($Menu[$key]);
        }
        
        if (!$Menu) {
            $Menu=D("Common/Menu")->menu_cache();
        }
		
        return $Menu;
    }

    /**
     * 消息提示
     * @param type $message
     * @param type $jumpUrl
     * @param type $ajax 
     */
    public function success($message = '', $jumpUrl = '', $ajax = false) {
        parent::success($message, $jumpUrl, $ajax);
    }

    /**
     * 模板显示
     * @param type $templateFile 指定要调用的模板文件
     * @param type $charset 输出编码
     * @param type $contentType 输出类型
     * @param string $content 输出内容
     * 此方法作用在于实现后台模板直接存放在各自项目目录下。例如Admin项目的后台模板，直接存放在Admin/Tpl/目录下
     */
    public function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
        parent::display($this->parseTemplate($templateFile), $charset, $contentType);
    }
    
    /**
     * 获取输出页面内容
     * 调用内置的模板引擎fetch方法，
     * @access protected
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $content 模板输出内容
     * @param string $prefix 模板缓存前缀*
     * @return string
     */
    public function fetch($templateFile='',$content='',$prefix=''){
        $templateFile = empty($content)?$this->parseTemplate($templateFile):'';
		return parent::fetch($templateFile,$content,$prefix);
    }
    
    /**
     * 自动定位模板文件
     * @access protected
     * @param string $template 模板文件规则
     * @return string
     */
    public function parseTemplate($template='') {
    	$tmpl_path=C("SP_ADMIN_TMPL_PATH");
    	define("SP_TMPL_PATH", $tmpl_path);
		// 获取当前主题名称
		$theme      =    C('SP_ADMIN_DEFAULT_THEME');
		
		if(is_file($template)) {
			// 获取当前主题的模版路径
			define('THEME_PATH',   $tmpl_path.$theme."/");
			return $template;
		}
		$depr       =   C('TMPL_FILE_DEPR');
		$template   =   str_replace(':', $depr, $template);
		
		// 获取当前模块
		$module   =  MODULE_NAME."/";
		if(strpos($template,'@')){ // 跨模块调用模版文件
			list($module,$template)  =   explode('@',$template);
		}
		// 获取当前主题的模版路径
		define('THEME_PATH',   $tmpl_path.$theme."/");
		
		// 分析模板文件规则
		if('' == $template) {
			// 如果模板文件名为空 按照默认规则定位
			$template = CONTROLLER_NAME . $depr . ACTION_NAME;
		}elseif(false === strpos($template, '/')){
			$template = CONTROLLER_NAME . $depr . $template;
		}
		
		C("TMPL_PARSE_STRING.__TMPL__",__ROOT__."/".THEME_PATH);
		
		C('SP_VIEW_PATH',$tmpl_path);
		C('DEFAULT_THEME',$theme);
		define("SP_CURRENT_THEME", $theme);
		
		$file = sp_add_template_file_suffix(THEME_PATH.$module.$template);
		$file= str_replace("//",'/',$file);
		if(!file_exists_case($file)) E(L('_TEMPLATE_NOT_EXIST_').':'.$file);
		return $file;
    }

    /**
     *  排序 排序字段为listorders数组 POST 排序字段为：listorder
     */
    protected function _listorders($model) {
        if (!is_object($model)) {
            return false;
        }
        $pk = $model->getPk(); //获取主键名称
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['listorder'] = $r;
            $model->where(array($pk => $key))->save($data);
        }
        return true;
    }

    /**
     * 后台分页
     * 
     */
    protected function page($total_size = 1, $page_size = 0, $current_page = 1, $listRows = 6, $pageParam = '', $pageLink = '', $static = FALSE) {
        if ($page_size == 0) {
            $page_size = C("PAGE_LISTROWS");
        }
        
        if (empty($pageParam)) {
            $pageParam = C("VAR_PAGE");
        }
        
        $Page = new \Page($total_size, $page_size, $current_page, $listRows, $pageParam, $pageLink, $static);
        $Page->SetPager('Admin', '{first}{prev}&nbsp;{liststart}{list}{listend}&nbsp;{next}{last}', array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
        return $Page;
    }

    private function check_access($uid){
    	//如果用户角色是1，则无需判断
    	if($uid == 1){
    		return true;
    	}
    	
    	$rule=MODULE_NAME.CONTROLLER_NAME.ACTION_NAME;
    	$no_need_check_rules=array("AdminIndexindex","AdminMainindex");
    	if(!in_array($rule,$no_need_check_rules) ){
    		return sp_auth_check($uid);
    	}else{
    		return true;
    	}
    }
	
	
	//支付密码
	public function pay_password($pw, $pre){
		$decor=md5($pre);
		$mi=md5($pw);
		return substr($decor,0,12).$mi.substr($decor,-4,4);
	}
	
	/**
	**搜索下拉游戏列表
	**/
	function _getGames($type = 0,$all = 0){
		$where = 1;
		if($type > 0){
			$where = " a.type = ".$type; 
		}
		$roletype = sp_get_current_roletype();
		$uid=get_current_admin_id();
		
		if($all == 0){
			$cates=array(
    	        0=>"全部游戏"
    	    );
		}elseif($all == 1){
			$cates=array(
    	        0=>"请选择游戏"
    	    );
		}
		
		$gamemodel = M(C("MNG_DB_NAME").".game", C('LDB_PREFIX'));
		
	    $games = $gamemodel
				  ->alias("a")
			      ->where($where)
				  ->getField("a.id,a.gamename,a.type", true);
        
		/*if($games){
               $cates = $cates + $games;
        }*/
		foreach($games as $key=>$val){
	        if($val['type'] == 1){
	            $cates[$key] = $val['gamename']."(Android)";
	        }else if($val['type'] == 2){
	            $cates[$key] = $val['gamename']."(IOS)";
	        }else{
	            $cates[$key] = $val['gamename'];
	        }
	    }
		
        $this->assign("games",$cates);
	}
	
	/**
	**搜索所有下拉游戏列表
	**/
	function _getAllgames($type = 0,$is_damai){
		$where = 1;
		if($type > 0){
			$where = " a.type = ".$type; 
		}
		
		$roletype = sp_get_current_roletype();
		$uid=get_current_admin_id();

    	$cates=array(
    	        0=>"全部游戏"
    	);
		
		$gamemodel = M(C("MNG_DB_NAME").".game", C('LDB_PREFIX'));
	
		$games = $gamemodel
				  ->alias("a")
				  ->where($where)
				   ->getField("a.id,a.gamename,a.type", true);
        
		/*if($games){
               $cates = $cates + $games;
        }*/
		foreach($games as $key=>$val){
	        if($val['type'] == 1){
	            $cates[$key] = $val['gamename']."(Android)";
	        }else if($val['type'] == 2){
	            $cates[$key] = $val['gamename']."(IOS)";
	        }else{
	            $cates[$key] = $val['gamename'];
	        }
	    }
		
        $this->assign("games",$cates);
	}
	
	/**
	**外部渠道游戏列表
	**/
	function _getUsergames($uid){
		$roletype = sp_get_current_roletype();
		
		$where['ratestatus'] = 3;
		$gamemodel = M($this->dbname.".game", C('CDB_PREFIX'));
		if($uid > 0){
				$gameuser = M(C("AUTH_DB_NAME")."."."clientusers_game", C('LDB_PREFIX'))->where(array('agentid'=>$uid))->find();
			    
				$appids_arr = array_values (array_unique (array_diff (split (",", $gameuser['appids']), array (""))));
			    $gameuser['appids'] = implode(',', $appids_arr);	
			
			    $ids = 0;
			    if(empty($gameuser['appids']) || $gameuser['appids'] == "" || empty($gameuser)){
				    $ids = 0;
			    }else{
				    $where['appid'] = array('in',$gameuser['appids']);
				    $games = $gamemodel->where($where)->getField("appid,gamename,type", true);
			    }
			
		}
		$cates=array();
		foreach($games as $key=>$val){
	        if($val['type'] == 1){
	            $cates[$key] = $val['gamename']."(Android)";
	        }else if($val['type'] == 2){
	            $cates[$key] = $val['gamename']."(IOS)";
	        }else{
	            $cates[$key] = $val['gamename'];
	        }
	    }
		
		/*else{
			$games = $gamemodel->where($where)->getField("appid,gamename", true);
		}*/
		
        $this->assign("usergames",$cates);
	}
	
	/**
	**外部渠道未获取游戏列表
	**/
	function _getNousergames($uid){
		
		
		$roletype = sp_get_current_roletype();
		
		$gamemodel = M($this->dbname.".game", C('CDB_PREFIX'));
		
		if($uid > 0){
			if($roletype <= 2){
				$gameuser = M(C("AUTH_DB_NAME")."."."clientusers_game", C('LDB_PREFIX'))->where(array('agentid'=>$uid))->find();
				
				$appids_arr = array_values (array_unique (array_diff (split (",", $gameuser['appids']), array (""))));
			    $gameuser['appids'] = implode(',', $appids_arr);	
				
				$ids = 0;
			    if(empty($gameuser['appids']) || $gameuser['appids'] == "" || empty($gameuser)){
				    $ids = 0;
			    }else{
				    $ids = $gameuser['appids'];
			    }
			    
			    $games = $gamemodel->where("appid not in (".$ids.")  AND ratestatus=3")->getField("appid,gamename,type", true);
				
			}else{
				
				$clientuser_model = M(C("AUTH_DB_NAME")."."."clientusers", C('LDB_PREFIX'));
				$parent = $clientuser_model
				       ->alias("a")
					   ->field("cu.id,cr.typeid")
					   ->join("inner join ".C("AUTH_DB_NAME").".".C('LDB_PREFIX')."clientusers cu on cu.id = a.parentid")
					   ->join("inner join ".C("AUTH_DB_NAME").".".C('LDB_PREFIX')."clientrole cr on cr.id = cu.role_id")
				       ->where(array("a.id"=>$uid))
					   ->find();
			    
				$ids = 0;
				if($parent['typeid'] > 2){
					$gameuser = M(C("AUTH_DB_NAME")."."."clientusers_game", C('LDB_PREFIX'))->where(array('agentid'=>$parent['id']))->find();
					
					$appids_arr = array_values (array_unique (array_diff (split (",", $gameuser['appids']), array (""))));
			        $gameuser['appids'] = implode(',', $appids_arr);	
					
					if(empty($gameuser['appids']) || $gameuser['appids'] == "" || empty($gameuser)){
				        $ids = 0;
			        }else{
						$ugameuser = M(C("AUTH_DB_NAME")."."."clientusers_game", C('LDB_PREFIX'))->where(array('agentid'=>$uid))->find();
						
						$u_array = array_values (array_unique (array_diff (split (",", $ugameuser['appids']), array (""))));

						if(count($u_array) > 0){
							$gu_array = explode(",",$gameuser['appids']); 
						    $result = array_intersect($u_array,$gu_array);
						}
				        $ids = empty($result) ? implode(",",$result) : $gameuser['appids'];
						$where['ratestatus'] = 3;
				        $where['appid'] = array('in',$gameuser['appids']);
			        }
					
				}else{
					$gameuser = M(C("AUTH_DB_NAME")."."."clientusers_game", C('LDB_PREFIX'))->where(array('agentid'=>$uid))->find();
					
					$appids_arr = array_values (array_unique (array_diff (split (",", $gameuser['appids']), array (""))));
			        $gameuser['appids'] = implode(',', $appids_arr);	
					
					if(empty($gameuser['appids']) || $gameuser['appids'] == "" || empty($gameuser)){
				        $ids = 0;
			        }else{
				        $ids = $gameuser['appids'];
						$where['appid'] = array('in',$gameuser['appids']);
					}
					
				}
				
			    $games = $gamemodel->where($where)->getField("appid,gamename,type", true);
			}
		}else{
			$where['ratestatus'] = 3;
			$games = $gamemodel->where($where)->getField("appid,gamename,type", true);
		}
		
        $cates=array();
		foreach($games as $key=>$val){
	        if($val['type'] == 1){
	            $cates[$key] = $val['gamename']."(Android)";
	        }else if($val['type'] == 2){
	            $cates[$key] = $val['gamename']."(IOS)";
	        }else{
	            $cates[$key] = $val['gamename'];
	        }
	    }
		
        $this->assign("nousergames",$cates);
	}
	
	function _getroles($typeid = NULL){
	    $cates=array(
	            0=>"全部"
	    );
	    
	    if (!empty($typeid) && is_numeric($typeid)){
	        $where['typeid'] = $typeid;
	    }
	    $cid = sp_get_current_cid();
	    $where['status'] = 1;
	    $where['cid'] = array('in', "0, $cid");
	    $roles = M(C('AUTH_DB_NAME').".clientrole", C('LDB_PREFIX'))->where($where)->getField("id,name", true);
	    if($roles){
	        $cates = $cates + $roles;
	    }
	    $this->assign("roles",$cates);
	}
	
	function _getclientroles(){
	    $typeid = 3;
	    
	    $cid = sp_get_current_cid();
	    $where['cid'] = array('in', "0, $cid");
	    $where['status'] = 1;
		$where['typeid'] = $typeid;
	    
	    $roles = M(C('AUTH_DB_NAME').".clientrole", C('LDB_PREFIX'))->where($where)->getField("id,name", true);
	    $roles[0] = "全部";
	    $this->assign("roles",$roles);
	}
	
	function getRoletype(){
	    $userid = $_SESSION["MNG_ID"];
	    $roleid = M(C('AUTH_DB_NAME').'.clientrole','l_')->where("user_id=$userid")->getField("role_id");
	    return $roleid;
	}
	
	/**
	 **根据当前账号节点获取下级所有账号id
	 **/
    function getAgentids($myid){
        $myid = intval($myid);
        if(empty($myid) || $myid < 1){
            return NULL;
        }
        
        $sonstr = $this->getSon($myid);
        if(!empty($sonstr)){
            //return $myid.','.$sonstr;
            return $sonstr;
        }else{
            return $myid;
        }
    }
    
    function getSon($uid){
        $uid = (int) $uid;
        $cid = sp_get_current_cid();

		$maindata = M(C('AUTH_DB_NAME').".clientusers", 'l_')
			    ->field("cid,lft,rgt")
			    ->where(array('id'=>$uid))
			    ->find();
	    
        $where['lft'] = array('EGT',$maindata['lft']);
		$where['rgt'] = array('ELT',$maindata['rgt']);
        $where['cid'] = array('EQ',$maindata['cid']);
        $result = M(C('AUTH_DB_NAME').".clientusers", 'l_')
		->where($where)
		->getField('id',true);
        $idstr = implode(',',$result);
        return $idstr;
    }

	/**
	 **根据当前账号获取该账号所有游戏
	 **/
    function getAppids($myid){
        $myid = intval($myid);
        if(empty($myid) || $myid < 1){
            return NULL;
        }
		$cid = sp_get_current_cid();
		
		$result = M(C('AUTH_DB_NAME').".clientusers_game", 'l_')
		->where(array("agentid"=>$myid,"cid"=>$cid))
		->find();
        
        if(!empty($result) && !empty($result['appids'])){
            return $result['appids'];
        }else{
            return 0;
        }
    }
    
    private function load_app_admin_menu_lang(){
    	if (C('LANG_SWITCH_ON',null,false)){
    		$admin_menu_lang_file=SPAPP.MODULE_NAME."/Lang/".LANG_SET."/admin_menu.php";
    		if(is_file($admin_menu_lang_file)){
    			$lang=include $admin_menu_lang_file;
    			L($lang);
    		}
    	}
    }
	
	function _getAgents($userwhere=NULL){
	    $cates=array(
            "0"=>"全部",
			"1"=>"官包",
	    );
	    
	    $cid  = sp_get_current_cid();
	    $where = "role_id > 1 AND cid = ".$cid;
		
		$roletype = sp_get_current_roletype();
        $adminid = sp_get_current_admin_id();
		if ($roletype > 2){  //专员能看到自己子渠道的玩家数据
            $ids = $this->getAgentids($adminid);
            $userwhere = " id in ($ids)";
            $where .= " AND ($userwhere)";
			
			$cates=array(
                 "0"=>"全部"
	         );
        }
		
	    if(!empty($userwhere)){
	       $where .= " AND ($userwhere)";
	    }
	    $agents = M(C('AUTH_DB_NAME').'.clientusers',C('LDB_PREFIX'))->where($where)->getField("id,user_login agentname", true);
		$uid = sp_get_current_admin_id();
		
	    if($agents){
			$agents = $cates + $agents;
	    }else{
	        $agents=$cates;
	    }
		
	    $this->assign("agents",$agents);
	}
	
	function _getAgentsUser($userwhere=NULL,$islist=false){
		if($islist){
			$cates=array(
	            "1"=>"官包"
	        );
		}else{
			$cates=array(
	            "0"=>"全部",
			    "1"=>"官包",
	        );
		}
	    
	     
	    $cid  = sp_get_current_cid();
	    $where = "role_id > 1 AND cid = ".$cid;
		
		$roletype = sp_get_current_roletype();
        $adminid = sp_get_current_admin_id();
		if ($roletype > 2){  //专员能看到自己子渠道的玩家数据
            $ids = $this->getAgentids($adminid);
            $userwhere = " id in ($ids)";
            $where .= " AND ($userwhere)";
			
			$cates=array(
	            "0"=>"全部"
	        );
        }
		
	    if(!empty($userwhere)){
	       $where .= " AND ($userwhere)";
	    }
	    $agents = M(C('AUTH_DB_NAME').'.clientusers',C('LDB_PREFIX'))->where($where)->getField("id,user_nicename agentname", true);
	    
	    if($agents){
	        $agents = $cates + $agents;
	    }else{
	        $agents=$cates;
	    }
	    $this->assign("agents",$agents);
	}

	function _getStatus($typeid = 0){
	    $cates=array(
	            "0"=>"全部"
	    );
	    if (1 != $typeid){
	        $where[''] = "role_id > 1";
	    }
	    
	    if(isset($typeid) && is_numeric($typeid)){
	        $where .= " and role_id=".$typeid;
	    }
	    $agents = M(C('AUTH_DB_NAME').'.clientusers',C('LDB_PREFIX'))->where($where)->getField("id,user_login agentname", true);
	
	    if($agents){
	        $agents = $cates + $agents;
	        //$agents=array_merge($cates,$agents);
	    }else{
	        $agents=$cates;
	    }
	
	    $this->assign("agents",$agents);
	}
	
	function _authPaypwd($repwd){
		if(empty($repwd)){
			$this->error("请输入二级密码！");
    		exit;
		}
		$user_obj = D("Common/Users");
		$uid=get_current_admin_id();
		$admin=$user_obj->where(array("id"=>$uid))->find();
			
		$repwd = sp_password($repwd);
			
		if($admin['pay_pwd'] != $repwd){
			$this->error("二级密码错误,操作失败！");
    		exit;
		}
	}
	
	
	function getcharactors(){
	   	$charactors = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		$this->assign("charactors", $charactors);
	}

	/**
	 * POST方式请求数据
	 * 
	 * @param $url 请求的地址 ;
	 * @param $data_string 数据
	 *
	 * @return 加密字符串
	 *
	 */
	function http_post_data($url, $data_string) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt(
				$ch, 
				CURLOPT_HTTPHEADER, 
				array(
						'Content-Type: application/json; charset=utf-8', 
						'Content-Length: ' . strlen($data_string) 
				));
		ob_start();
		curl_exec($ch);
		$return_content = ob_get_contents();
		ob_end_clean();
		
		$return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return $return_content;
	}
	
	public function checkcode() {	     
	    $length=4;
	    if (isset($_GET['length']) && intval($_GET['length'])){
	        $length = intval($_GET['length']);
	    }
	     
	    //设置验证码字符库
	    $code_set="";
	    if(isset($_GET['charset'])){
	        $code_set= trim($_GET['charset']);
	    }
	     
	    $use_noise=1;
	    if(isset($_GET['use_noise'])){
	        $use_noise= intval($_GET['use_noise']);
	    }
	     
	    $use_curve=1;
	    if(isset($_GET['use_curve'])){
	        $use_curve= intval($_GET['use_curve']);
	    }
	     
	    $font_size=25;
	    if (isset($_GET['font_size']) && intval($_GET['font_size'])){
	        $font_size = intval($_GET['font_size']);
	    }
	     
	    $width=0;
	    if (isset($_GET['width']) && intval($_GET['width'])){
	        $width = intval($_GET['width']);
	    }
	     
	    $height=0;
	
	    if (isset($_GET['height']) && intval($_GET['height'])){
	        $height = intval($_GET['height']);
	    }
	     
	    /* $background="";
	    	if (isset($_GET['background']) && trim(urldecode($_GET['background'])) && preg_match('/(^#[a-z0-9]{6}$)/im', trim(urldecode($_GET['background'])))){
	    	$background=trim(urldecode($_GET['background']));
	    	} */
	    //TODO ADD Backgroud param!
	     
	    $config = array(
	            'codeSet'   =>  !empty($code_set)?$code_set:"2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY",             // 验证码字符集合
	            'expire'    =>  1800,            // 验证码过期时间（s）
	            'useImgBg'  =>  false,           // 使用背景图片
	            'fontSize'  =>  !empty($font_size)?$font_size:25,              // 验证码字体大小(px)
	            'useCurve'  =>  $use_curve===0?false:true,           // 是否画混淆曲线
	            'useNoise'  =>  $use_noise===0?false:true,            // 是否添加杂点
	            'imageH'    =>  $height,               // 验证码图片高度
	            'imageW'    =>  $width,               // 验证码图片宽度
	            'length'    =>  !empty($length)?$length:4,               // 验证码位数
	            'bg'        =>  array(243, 251, 254),  // 背景颜色
	            'reset'     =>  true,           // 验证成功后是否重置
	    );
	    $Verify = new \Think\Verify($config);
	    $Verify->entry();
	}


	/**
     * 上传图片
     */
    public function uploadimg($up_info, $name,$fileurl = '') {
        $upload = new \Think\Upload(); // 实例化上传类
		
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->exts = array(
                'jpg', 
                'gif', 
                'png', 
                'jpeg' 
        ); // 设置附件上传类型
		
        $upload->rootPath = C('UPLOAD_IMG').$fileurl; // 设置附件上传根目录
        $upload->saveName = $name;
        $upload->autoSub = false;
        $upload->replace = true;
		
        // 上传单个文件
        $info = $upload->uploadOne($up_info);
        
        if (!$info) { // 上传错误提示错误信息
			print_r($upload->getError());
			$this->error($upload->getError());
            exit();
        } else { // 上传成功 获取上传文件信息
            return $info['savepath'] . $info['savename'];
        }
    }
	
	//上传APK
	public function uploadgame($rootpath,$up_info, $name){
		if (is_dir($rootpath)){  
		   
	    }else{
		    $res=mkdir(iconv("UTF-8", "GBK", $rootpath),0777,true); 
		}

		$upload = new \Think\Upload(); // 实例化上传类
		
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->exts = array(
                'apk'
        ); // 设置附件上传类型
        $upload->rootPath = $rootpath; // 设置附件上传根目录
        $upload->saveName = $name;
        $upload->autoSub = false;
        $upload->replace = true;
        // 上传单个文件
        $info = $upload->uploadOne($up_info);
        
        if (!$info) { // 上传错误提示错误信息
			print_r($upload->getError());
            exit();
        } else { // 上传成功 获取上传文件信息
            return $info['savepath'] . $info['savename'];
        }
	}

	/**
	 * 生成二维码
	 *
	 * @return null
	 */
	function qrcode($url)
	{	
	    Vendor('phpqrcode.phpqrcode');
        //生成二维码图片
        $object = new \QRcode();
        $level = 3;
        $size = 3;
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
		
	}

	/*
	**xls导出
	*/
	public function exportExcel($expTitle,$expCellName,$expTableData){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = $xlsTitle.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel");
		vendor("PHPExcel.IOFactory");
       
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        
        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
       // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));  
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]); 
        } 
          // Miscellaneous glyphs, UTF-8  
        for($i=0;$i<$dataNum;$i++){
          for($j=0;$j<$cellNum;$j++){
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
            if($expCellName[$j][0] == 'imei'){
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueExplicit($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]],\PHPExcel_Cell_DataType::TYPE_STRING);
            }
            
          }             
        }
		ob_clean();
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
		header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
        $objWriter->save('php://output'); 
        exit;   
    }
}