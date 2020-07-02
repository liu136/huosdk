<?php

/**
 * 后台Controller
 */
namespace Common\Controller;
use Common\Controller\AppframeController;

class AdminbaseController extends AppframeController {
	
	public function __construct() {
		parent::__construct();
		Header("Access-Control-Allow-Origin: * ");

		Header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
		$time=time();
		$this->assign("js_debug",APP_DEBUG?"?v=$time":"");
	}

    function _initialize(){
        parent::_initialize();
        
    	/*if(isset($_SESSION['ADMIN_ID'])){
    		$users_obj= M("admin");
    		$id=$_SESSION['ADMIN_ID'];
    		$user=$users_obj->where("id=$id")->find();
    		if(!$this->check_access($id)){
    			$this->error("您没有访问权限！");
    			exit();
    		}
			
    		$this->assign("admin",$user);
    	}else{
    		//$this->error("您还没有登录！",U("admin/public/login"));
    		if(IS_AJAX){
    			$this->error("您还没有登录！",U("admin/public/login"));
    		}else{
    			header("Location:".U("admin/public/login"));
    			exit();
    		}
    		
    	}*/
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
    	$tmpl_path=C("SP_TMPL_PATH");
    	define("SP_TMPL_PATH", $tmpl_path);
		// 获取当前主题名称
		$theme      =    C('SP_DEFAULT_THEME');
		
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
	 **插入充值记录
	 */
	public function addpay($data,$dbname,$cpurl){
		$lpaydata = $data;
		$model = M();
        $model->startTrans();
		
		$luser = $model->table(C('DATA_DB_NAME').".".C('LDB_PREFIX')."user")
			  ->where(array("lm_username"=>$data['lm_username']))
			  ->field("id")
			  ->find();
		
		$lpaydata['userid'] = $luser['id'];
		$lpaydata['payway'] = $data['paytype'];
		$lpaydata['regagentgame'] = $data['regagent'];
		unset($lpaydata['paytype']);
		unset($lpaydata['regagent']);
		$rs = $model->table(C('DATA_DB_NAME').".".C('LDB_PREFIX')."paylog")->add($lpaydata);
		if($rs > 0){
            unset($data['lm_username']);
            unset($data['switchflag']);
			
			$clrs = $model->table($dbname.".".C('CDB_PREFIX')."pay")->add($data);
			if($clrs > 0){
				$cpstr = "orderid=".urlencode($data['orderid'])."&username=".urlencode($lpaydata['lm_username'])."&appid=".urlencode($data['appid'])."&roleid=".urlencode($data['roleid']);
                $cpstr .= "&serverid=".urlencode($data['serverid'])."&amount=".urlencode($data['amount'])."&paytime=".urlencode($data['create_time'])."&attach=".urlencode($data['remark'])
				        ."&productname=".urlencode($data['productname']);
				
                $appkey = $this->getAppkey($data['appid']);
                
                $param = $cpstr."&appkey=".urlencode($appkey);
                $md5params = md5($param);
                $params = $cpstr . "&sign=".urlencode($md5params);
				
				$paycpdata['orderid'] = $data['orderid'];
                $paycpdata['params'] = $params;
                $paycpdata['cpurl'] = $cpurl;
                $paycpdata['create_time'] = $data['create_time'];
                $paycpdata['update_time'] = $data['create_time'];
                $paycpdata['cid'] = $data['cid'];
                
                //插入分渠道paycpinfo数据库
                $cpinfo_rs = $this->insertClpaycp($model,$dbname, $paycpdata);
				if($cpinfo_rs > 0){
					$paycpdata['switchflag'] = $lpaydata['switchflag'];
				    // 插入联盟paycpinfo数据
				    $lcpinfo_rs = $this->insertLepaycp($model,$paycpdata);
                    unset($paycpdata);
					if($lcpinfo_rs > 0){
						$model -> commit();
						return true;
					}
				}else{
					$model->rollback();
					unset($data);
					unset($lpaydata);
					return false;
				}
			}else{
				$model->rollback();
				unset($data);
			    unset($lpaydata);
				return false;
			}
		}else{
			$model->rollback();
			unset($data);
			unset($lpaydata);
			
			return false;
		}
	}
	
	/**
	 **游戏key
	 **/
    private function getAppkey($appid){
		if (empty($appid) || 0 > $appid) {
            return NULL;
        }
		
		$appkey = M(C('MNG_DB_NAME').".game",C('LDB_PREFIX'))
		        ->where(array("id"=>$appid))
				->getField('appkey');
        
        return $appkey;
	}
	
	/*
     * 插入分渠道paycpinfo数据
     */
    private function insertClpaycp($model,$dbname, $paycpdata){
		$paycpdata['fcallbackurl'] = $paycpdata['cpurl'];
		unset($paycpdata['cpurl']);
		
		$rs = $model->table($dbname.".".C('CDB_PREFIX')."paycpinfo")->add($paycpdata);
        
        return $rs;
	}
	
	/*
     * 插入联盟paycpinfo数据
     */
    private function insertLepaycp($model,$paycpdata){
		
		$rs = $model->table(C('DATA_DB_NAME').".".C('LDB_PREFIX')."paycpinfo")->add($paycpdata);
	
        return $rs;
    }
	
	/*
     * 游戏回调地址
     */
    public function getCpurl($appid,$cid = 0){
	
        if (!isset($appid) || 1 > $appid) {
            return NULL;
        }
        if(is_numeric($cid)){
			$cpurl = M(C('MNG_DB_NAME').".gamecpurl",C('LDB_PREFIX'))
			     ->where(array('appid'=>$appid))
			     ->find();
        }        
        return $cpurl['cpurl'];
    }
	
	/**
	**获取支付配置
	**/
	function payment($paytype,$cid){
		
		$model = M(C('MNG_DB_NAME').".payway",C('LDB_PREFIX'));
		
		$sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME ='".C('LDB_PREFIX').$paytype."'";
		
		$rs = $model->query($sql);
		
		if(!$rs){
			echo "支付方式未配置";
            exit;
		}
		
		$payway = M(C('MNG_DB_NAME').".".$paytype,C('LDB_PREFIX'))
			     ->where(array("cid"=>$cid))
			     ->find();
        
		$data = null;
        switch ($paytype)
        {
          case 'tenpay':
             $data = array(
                // 加密key，开通财付通账户后给予
               'key' => 'e82573dc7e6136ba414f2e2affbe39fa',
               // 合作者ID，财付通有该配置，开通财付通账户后给予
               'partner' => '1900000113'
              );
             break;  
          case 'alipay':
             $data = array(
                  'email' => $payway['email'],
				  'app_id' => $payway['appid'],
                  'merchant_private_key' => $payway['rsa_private_key'],
                  'partner' => $payway['partner']
             );
             break;
		  case 'aliwappay':
             $data = array(
                  'email' => $payway['email'],
				  'app_id' => $payway['appid'],
                  //商户私钥，您的原始格式RSA私钥
		          'merchant_private_key' => $payway["rsa_private_key"],
                  'partner' => $payway['partner'],
				   //编码格式
		          'charset' => "UTF-8",
		          //支付宝网关
		          'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",
		          //支付宝公钥
		          'alipay_public_key' => "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDIgHnOn7LLILlKETd6BFRJ0GqgS2Y3mn1wMQmyh9zEyWlz5p1zrahRahbXAfCfSqshSNfqOmAQzSHRVjCqjsAw1jyqrXaPdKBmr90DIpIxmIyKXv4GGAkPyJ/6FTFY99uhpiq0qadD/uSzQsefWo0aTvP/65zi3eof7TcZ32oWpwIDAQAB"
		
             );
             break;
		   case 'payecoh5':
             $data = array(
                 'merchant_id' => '502053000005',
                 'merchant_rsa_private_key' => 'MIICdQIBADANBgkqhkiG9w0BAQEFAASCAl8wggJbAgEAAoGBAIbwV4LhFfAn5LMw+To0kR2EWYY3spVyERtRUzMEuwR0iRSSBkek++XFcEV+vgnz1dMtg6u+7nR+is+l+tUxorDrg0A4PIaRJZMqrImQhnfL282ujpESb+imYM8f72/MayzwYe9xQ7StmKpDkxhx/oTFeclw+b64mibjGjuWwPXxAgMBAAECgYBCZdEp3Yfl/DtU0SxRr7wYQh+rI40EbHRudL3zxMghkRZCwPfGGTC6B0UPbSYlz43Ps/2ubOz49atoMcwTS7E5liqNmbwqijEktHWt9zSmw2YlM1erL2rxoBenLUKVEgKCikctbeR/2+/hmGREzwMhLPiT+1InEIaU7WgeWJHggQJBAL+cCIz3bumZSHoeaiu5NrD1bPcGLYOQBVH/sFndS9/Nd1MJEMBEmZrFDxNr00bAMFeFGAeJwjUfU71W+VAme/0CQQC0SP1VtfmxG8PBLN93U0WdHY+zbfs41hl6SIxppyy3JVN1AYyJGSlsP2YHpwGylEEfUblyR6PV3jwYIcrP59IFAkAnriSHLOanMbs0rv/FtkGBPBIoxfq++CBh7tWShqWj32UKqSHy70HwL0cD+pxyVnKsbT+gsAKsBaTN3SkcVBvxAkA5Abdxicg5k5DznW/P+HnTs4xD7Wv5zeFihFw58E24X8oi/mlk1Jr/ipCFrO5hfHWXJK1iEHsi3lHcQ5sw4JnNAkB4jAVswdJPkEw2q+N2dVUo0yadmxnBBPVBOrlgSo+xSmYLGgeaiDX14S/tSiO4Xb+/FoqrcCSJbc3N0vVqdRxQ',
				 'payeco_url' => "https://testmobile.payeco.com",
				 'payeco_rsa_public_key'=> 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCRxin1FRmBtwYfwK6XKVVXP0FIcF4HZptHgHu+UuON3Jh6WPXc9fNLdsw5Hcmz3F5mYWYq1/WSRxislOl0U59cEPaef86PqBUW9SWxwdmYKB1MlAn5O9M1vgczBl/YqHvuRzfkIaPqSRew11bJWTjnpkcD0H+22kCGqxtYKmv7kwIDAQAB'
             );
             break;
		   case 'yeepay':
             $data = array(
                 'key' => '69cl522AV6q613Ii4W6u8K6XuW8vM1N6bFgyv769220IuYe9u37N4y7rI4Pl',
                 'partner' => '10001126856'
             );
             break;
          default:
            $data = null;
        }
        
		if(empty($data)){
			echo "未找到配置";
			exit;
		}
        return $data;	
		
	}
	
	/**
	 **充值回调
	 **/
	public function doPaynotify($orderid, $amount, $dbname, $paymark='default') {
		
        if(empty($orderid) || empty($amount) || empty($dbname)){
            return FALSE;
        }
        $trade['orderid'] = $orderid;
        $trade['paymark'] = $paymark;
        $time = time();
		
		$model = M($dbname.".pay",C('CDB_PREFIX'));
		$check_data = $model
			     ->where(array('orderid'=>$orderid))
				 ->field("amount,`status`")
			     ->find();
		
        // 验证订单数额的一致性
        if ((bccomp($amount, $check_data['amount'], 2) == 0) && 1 != $check_data['status']) {
			 $up_data['status'] = 1;
			 $up_data['paymark'] = $trade['paymark'];
			 
			 $rs = $model
			     ->where(array('orderid'=>$orderid))
				 ->save($up_data);
             
            // 通知联盟回调CP
            if ($rs) {
				unset($up_data);
				$up_data['status'] = 1;
			    $up_data['paymark'] = $trade['paymark'];
				
                //更新联盟数据
				$lrs = M(C('DATA_DB_NAME').".paylog",C('LDB_PREFIX'))
			         ->where(array('orderid'=>$orderid))
				     ->save($up_data);
				 
				$paycpdata = M($dbname.".paycpinfo",C('CDB_PREFIX'))
			         ->where(array('orderid'=>$orderid))
					 ->field("fcallbackurl,params,`status`")
				     ->find();
					 
                $fcallbackurl = $paycpdata['fcallbackurl'];
                $params = $paycpdata['params'];
                $status = $paycpdata['status'];
				
                if ($status == 0 || $status == 2) {
                    $i = 0;
                    while (1) {
                        $cp_rs = $this->payback($fcallbackurl, $params, 'post');
                        if ($cp_rs > 0) {
                            $status = 1;
                            break;
                        }else{
                            $status = 2;
                            $i ++;
                            sleep(2);
                        }
        
                        if ($i == 3) {
                            $status = 2;
                            break;
                        }
                    }
                }
                //更新CP状态
                unset($trade['paymark']);
				M($dbname.".paycpinfo",C('CDB_PREFIX'))->where(array('orderid'=>$orderid))->save(array("`status`"=>$status,"update_time"=>$time));
				
				M(C('DATA_DB_NAME').".paycpinfo",C('LDB_PREFIX'))->where(array('orderid'=>$orderid))->save(array("`status`"=>$status,"update_time"=>$time));
            }else{
				return FALSE;
			}
			
        }
        return TRUE;
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
   protected function page($Total_Size = 1, $Page_Size = 0, $Current_Page = 1, $listRows = 6, $PageParam = '', $PageLink = '', $Static = FALSE) {
        import('Page');
        if ($Page_Size == 0) {
            $Page_Size = C("PAGE_LISTROWS");
        }
        if (empty($PageParam)) {
            $PageParam = C("VAR_PAGE");
        }
        $Page = new \Page($Total_Size, $Page_Size, $Current_Page, $listRows, $PageParam, $PageLink, $Static);
        $Page->SetPager('Admin', '{first}{prev}&nbsp;{liststart}{list}{listend}&nbsp;{next}{last}', array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
        return $Page;
    }
    
	public static function payback($Url, $Params, $Method='post'){
		$rs = 0;
		$Curl = curl_init();//初始化curl

		if ('get' == $Method){//以GET方式发送请求
			curl_setopt($Curl, CURLOPT_URL, "$Url?$Params");
		}else{//以POST方式发送请求
			curl_setopt($Curl, CURLOPT_URL, $Url);
			curl_setopt($Curl, CURLOPT_POST, 1);//post提交方式
			curl_setopt($Curl, CURLOPT_POSTFIELDS, $Params);//设置传送的参数
		}

		curl_setopt($Curl, CURLOPT_HEADER, false);//设置header
		curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);//要求结果为字符串且输出到屏幕上
		//curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 3);//设置等待时间

		$Res = curl_exec($Curl);//运行curl
		
		if ( $Res == 'success') {
			$rs = 1;
		} else {
			$rs = 0;
		}
		curl_close($Curl);//关闭curl
		
		return $rs;
	}
	
	/*
	 * 向服务器发送请求数据
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