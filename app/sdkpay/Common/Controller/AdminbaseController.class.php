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
	public function addpay($data,$dbname,$cpurl,$baoming){
		$lpaydata = $data;
		$model = M();
        $model->startTrans();
		
		$luser = $model->table("db_sdk_2.".C('CDB_PREFIX')."members")
			  ->where(array("lm_username"=>$data['lm_username']))
			  ->field("id")
			  ->find();
		
            unset($data['lm_username']);
            unset($data['switchflag']);
			$data['baoming'] = $baoming;
			$clrs = $model->table($dbname.".".C('CDB_PREFIX')."pay")->add($data);
			if($clrs > 0){
				unset($data['baoming']);
				
				$cpstr = "orderid=".urlencode($data['orderid'])."&username=".urlencode(strtolower($lpaydata['lm_username']))."&appid=".urlencode($data['appid'])."&roleid=".urlencode($data['roleid']);
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
					
						$model -> commit();
						return true;
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
     * 游戏回调地址
     */
    public function getCpurl($appid,$cid = 0){
	
        if (!isset($appid) || 1 > $appid) {
            return NULL;
        }
        if(is_numeric($cid)){
			$cpurl = M(C('MNG_DB_NAME').".gamecpurl",C('LDB_PREFIX'))
			     ->where(array('gid'=>$appid))
			     ->find();
		   
        }        
        return $cpurl['cpurl'];
    }
	
	/**
	**获取支付配置
	**/
	function payment($paytype,$cid){
		
		$model = M("db_sdk_2.payway",C('LDB_PREFIX'));
		
		/*$sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME ='".C('LDB_PREFIX').$paytype."'";
		
		$rs = $model->query($sql);
		
		if(!$rs){
			echo "支付方式未配置";
            exit;
		}*/
		
		$data = null;
        switch ($paytype)
        {
          case 'tenpay':
             $data = array(
                // 加密key，开通财付通账户后给予
               'key' => '',
               // 合作者ID，财付通有该配置，开通财付通账户后给予
               'partner' => ''
              );
             break;  
          case 'alipay':
             $data = array(
			      //合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串，查看地址：https://openhome.alipay.com/platform/keyManage.htm?keyType=partner
                  'partner' => "2017101009233939",
				  //收款支付宝账号，以2088开头由16位纯数字组成的字符串，一般情况下收款账号就是签约账号
				  'seller_id' => "chenxiang@128xy.com",
                  //商户的私钥,此处填写原始私钥去头去尾，RSA公私钥生成：https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.nBDxfy&treeId=58&articleId=103242&docType=1
		          'private_key' => "MIICXgIBAAKBgQDxUIHBcpQ0Kn2G2U6nGXEyvyMasZd5p8VWiWX963+0x9dgQZtNqGoVZJew2xc+c2PchSM5DRhY0ZxWRbMisMIGBVe/7CiIK4xyD8O/yCHcCbkJjne3C6a6pfXvxfFYscGobSBapEh/8OZLVR+eIcbv5OflVRLwFbN1wmcBlQOWAQIDAQABAoGBAM2b6vMIzX4lNg9P2NRHuUuj0CVOa+IcMOgq6dwQbB98puY9ADaK6NiRfS4TfxqW9t9OEVTq83O6JZrciGVmdelDq9LguTFGeYaeixG62RQuNWotu84Y+1knXfY8++8PNHEVrsCJW2G/wMZlnBSAOQJSCfWhQ5D4a5hYTB1oUMYBAkEA/6N8vEGWXVxhB/aKzR3Yf76S5AlG/RhyQkI5SAFnFbIHIUAeYZH2r89FqlT3nzqMCk7BBEHvFQIRV9Yy4bK3TQJBAPGn1fsuA47oj6xSx84TDjcLz0LUkDyT685lrUXQDbnTetA0V5V5KZ//FL+cwkEOW+GVcGdwrpKuFn+GJSmkR4UCQGSuK8ss/Z5paqGrPMFJ9uFg2hNLgBTgEuf7kvnD66iExAAZc52z0fct598MtbWVZmAM4kHeAd5BQTlZ2BJBw6UCQQCh+QP0+u+JnxmFwGqKFr2labX/LmiLIf6g9gfAzmYU0snzudGmr3KV+ixXDmQppM0zE64mtyFb0XHlAe3wzlrdAkEAvwYm1/9Tl4YYgFPJKSuLScCN0jXTYqHJb429mZr2b3fDyn6dEN0ZCqmSJ0MyBJiZ5TMPj1XOWIKkIE/VLczuJg==",
                  //支付宝的公钥，查看地址：https://openhome.alipay.com/platform/keyManage.htm?keyType=partner
				  'alipay_public_key' => "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCqkHJzKQTkBqYpPefT/EwDZV4ZCdY6JBTPN6kgMaG5ynqgGS7CT36cQSKrJwfucntTYXNX1v50MJdnrZV+A6IrISRxmRLsKZYHZn4ypOOOwbzqKM0LmuYIv4YikC5KRcpT+hsTNMHuY+EdvIRGi5O5ceeJ7MjfmPwh0vKju3hrKQIDAQAB",
				  //签名方式
		          'sign_type' => strtoupper('RSA'),
		          //字符编码格式 目前支持utf-8
		          'input_charset' => strtolower('utf-8'),
				  //ca证书路径地址，用于curl中ssl校验
                  //请保证cacert.pem文件在当前文件夹目录中
				  'cacert' => '/alidata/data/html/thinkphp/Core/Library/Vendor/Pay/Wapalipay/cacert.pem',
				  //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
				  'transport' => 'http',
				  // 支付类型 ，无需修改
				  'payment_type' => '1',
				  // 产品类型，无需修改
				  'service' => "alipay.wap.create.direct.pay.by.user"
             );
             break;
		  case 'aliwappay':
             $data = array(
						//应用ID,您的APPID。
					  'app_id' => "2017101009233939",
					  //商户私钥，您的原始格式RSA私钥
					  'merchant_private_key' => "MIICXgIBAAKBgQDxUIHBcpQ0Kn2G2U6nGXEyvyMasZd5p8VWiWX963+0x9dgQZtNqGoVZJew2xc+c2PchSM5DRhY0ZxWRbMisMIGBVe/7CiIK4xyD8O/yCHcCbkJjne3C6a6pfXvxfFYscGobSBapEh/8OZLVR+eIcbv5OflVRLwFbN1wmcBlQOWAQIDAQABAoGBAM2b6vMIzX4lNg9P2NRHuUuj0CVOa+IcMOgq6dwQbB98puY9ADaK6NiRfS4TfxqW9t9OEVTq83O6JZrciGVmdelDq9LguTFGeYaeixG62RQuNWotu84Y+1knXfY8++8PNHEVrsCJW2G/wMZlnBSAOQJSCfWhQ5D4a5hYTB1oUMYBAkEA/6N8vEGWXVxhB/aKzR3Yf76S5AlG/RhyQkI5SAFnFbIHIUAeYZH2r89FqlT3nzqMCk7BBEHvFQIRV9Yy4bK3TQJBAPGn1fsuA47oj6xSx84TDjcLz0LUkDyT685lrUXQDbnTetA0V5V5KZ//FL+cwkEOW+GVcGdwrpKuFn+GJSmkR4UCQGSuK8ss/Z5paqGrPMFJ9uFg2hNLgBTgEuf7kvnD66iExAAZc52z0fct598MtbWVZmAM4kHeAd5BQTlZ2BJBw6UCQQCh+QP0+u+JnxmFwGqKFr2labX/LmiLIf6g9gfAzmYU0snzudGmr3KV+ixXDmQppM0zE64mtyFb0XHlAe3wzlrdAkEAvwYm1/9Tl4YYgFPJKSuLScCN0jXTYqHJb429mZr2b3fDyn6dEN0ZCqmSJ0MyBJiZ5TMPj1XOWIKkIE/VLczuJg==",
					  
					  //编码格式
					  'charset' => "UTF-8",
					   //签名方式
					   'sign_type'=>"RSA",
					  //支付宝网关
					  'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
					  //支付宝公钥                
					  'alipay_public_key' => "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB"

				 );
				break;
		    case 'payecoh5':
             $data = array(
                 'merchant_id' => '',
                 'merchant_rsa_private_key' => '',
				 'payeco_url' => "https://testmobile.payeco.com",
				 'payeco_rsa_public_key'=> ''
             );
             break;
			case 'nowpay':
             $data = array(
                 'appId' => '',
                 'secure_key' => ''
             );
             break;
			case 'heepayh5':
              $data = array(
                 'agent_id' => '',
                 'sign_key' => ''
				 
              );
             break;
		   case 'yeepay':
             $data = array(
                 'key' => '',
                 'partner' => ''
             );
             break;
			case 'jalipay':
             $data = array(
                'app_id' => '',
				'channel_code' => '',
				'app_key'=>''
             );
             break;
			case 'jwxpay':
             $data = array(
                'app_id' => '',
				'channel_code' => '',
				'app_key'=>''
             );
             break;
			case 'wsfalipay':
				 $data = array(
					'usercode' => '',
					'compkey' => ''
				 );
				 break;
				case 'wsfwxpay':
				 $data = array(
					'usercode' => '',
					'compkey' => ''
				 );
             break;
			case 'sftwxpay':
				 $data = array(
					 'sub_merchant_id' => '1111',
					 'method' => '2222'
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
				$fp = fopen("/alidata/data/html/data/runtime/sdkpay/info_log.txt","a");
	            flock($fp, LOCK_EX) ;
	             fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$status."==".$time."\n");
	             flock($fp, LOCK_UN);
	             fclose($fp);
				M($dbname.".paycpinfo",C('CDB_PREFIX'))->where(array('orderid'=>$orderid))->save(array("status"=>$status,"update_time"=>$time));
				
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
		
		$qian=array(" ","　","\t","\n","\r");  
        $hou=array("","","","","");  
        $Res = str_replace($qian,$hou,$Res); 
        
		if (strtolower($Res) == "success") {
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