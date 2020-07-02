<?php

/**
 * 充值统计页面
 * 
 * @author
 *
 */
namespace Pay\Controller;

use Common\Controller\AdminbaseController;

class PublicController extends AdminbaseController {
    
     protected $client_model;
	
     function _initialize() {
        //parent::_initialize();
		//$this->client_model = M(C('MNG_DB_NAME').".client", C('LDB_PREFIX'));
		
		//$ismobile = is_mobile_request();
		/*if(!$ismobile){
			echo "访问失败";
           exit;
		}*/
     }
	  
	  /**
	   **支付宝网关
	   **/
	  public function gateway() {
		  exit;
	  }
	  
	  /**
	   **支付宝授权回调
	   **/
	  public function authtoken() {
		  exit;
	  }
	  
	  public function logre($word){
		$fp = fopen("/alidata/data/html/data/runtime/sdkpay/Logs/Pay/al_log.txt","a");
	    flock($fp, LOCK_EX) ;
	    fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
	    flock($fp, LOCK_UN);
	    fclose($fp);
	  }
	  
	 /**
     * 支付结果返回
     */
    public function notify() {
		
        $apitype = I('get.apitype');
        $cid = intval(I('get.cid'));
		$dmmethod = I('get.dmmethod');
		$dbname = 'db_sdk_'.$cid;
		$typeid =I('get.typeid');
		
		
		
		$fp = fopen("/alidata/data/html/data/runtime/sdkpay/wapnotify_log.txt","a");
	    flock($fp, LOCK_EX) ;
	    fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".json_encode($_REQUEST)."\n");
	    flock($fp, LOCK_UN);
	    fclose($fp);
		
		$key_data = $this->payment($apitype,$cid);
	    $key_data['cid']  =  $cid;
        $pay = new \Vendor\Pay($apitype, $key_data);
		
		$fp = fopen("/alidata/data/html/data/runtime/sdkpay/wapnotify2_log.txt","a");
	    flock($fp, LOCK_EX) ;
	    fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".json_encode($key_data)."\n");
	    flock($fp, LOCK_UN);
	    fclose($fp);
		
        if (IS_POST && !empty($_POST)) {
            $notify = I('post.');
			$notify['ispost'] = 1;
        }else if (IS_GET && !empty($_GET)) {
            $notify = I('get.');
			$notify['ispost'] = 0;
			unset($notify['dmmethod']);
            unset($notify['apitype']);
			unset($notify['typeid']);
			
			unset($_GET['dmmethod']);
            unset($_GET['apitype']);
			unset($_GET['typeid']);
        } else {
             $xml = file_get_contents('php://input');
			
			if($xml){
				$notify['ispost'] = 1;
				$notify['xmldata']=$xml;
			}
			else{
				exit('Access Denied');
			}
        }
		
		unset($notify['cid']);
		unset($_GET['cid']);
		$info = $pay->verifyNotify($notify);
		
		$fp = fopen("/alidata/data/html/data/runtime/sdkpay/wapnotify3_log.txt","a");
	    flock($fp, LOCK_EX) ;
	    fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".json_encode($info)."\n");
	    flock($fp, LOCK_UN);
	    fclose($fp);
        //验证
        if ($info) {
            //获取订单信息
			
            if ($info['status']) {
				if ($dmmethod == "notify") {
					if($info['status'] == 1){
						$nrs = $this->doPaynotify($info['out_trade_no'], $info['total_amount'], $dbname, $info['trade_no']);
					    if($nrs){
							echo "success";
							exit;	
					    }
					}
					
                } else{
					
					$pay_info = M($dbname.".pay",C('CDB_PREFIX'))
			              ->where(array("orderid"=>$info['out_trade_no']))
			              ->field("`status`,baoming")
			              ->find();
					
					if($pay_info['status'] == 1){
						redirect(U("Pay/paysu", array('typeid' => $typeid)));
					}else{
						if($apitype == 'sftwxpay'){
							if($info['recode'] == 1){
								redirect(U("Pay/paysu", array('typeid' => $typeid)));
								exit;
							}
							redirect(U("Pay/payfa", array('typeid' => $typeid)));
							exit;
							
						}else{
							redirect(U("Pay/payfa", array('typeid' => $typeid)));
							exit;
						}
					}  
                }
            } else {
				redirect(U("Pay/payfa", array('typeid' => $typeid)));
				exit;
            }
        } else {
			
			redirect(U("Pay/payfa", array('typeid' => $typeid)));
			exit;
				
        }
    }
	
	
	public function arrayToXml($arr,$dom=0,$item=0){ 
		if (!$dom){ 
			$dom = new DOMDocument("1.0"); 
		} 
		if(!$item){ 
			$item = $dom->createElement("response"); 
			$dom->appendChild($item); 
		} 
		foreach ($arr as $key=>$val){ 
			$itemx = $dom->createElement(is_string($key)?$key:"item"); 
			$item->appendChild($itemx); 
			if (!is_array($val)){ 
				$text = $dom->createTextNode($val); 
				$itemx->appendChild($text); 
			 
			}else { 
				$this->arrayToXml($val,$dom,$itemx); 
			} 
		} 
		return $dom->saveXML(); 
	} 
	
	
    
   
}