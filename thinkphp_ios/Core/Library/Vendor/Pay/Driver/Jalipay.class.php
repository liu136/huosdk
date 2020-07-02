<?php

use Vendor\Pay\Pay;
use Vendor\Pay\PayVo;

class Jalipay extends Pay {

    protected $gateway = 'http://139.196.143.88:8105/api/charge/request_pay';
   // protected $verify_url = 'http://notify.alipay.com/trade/notify_query.do';
    protected $config = array(
        'app_id' => '',
        'channel_code' => '',
		'app_key'=>''
    );

    public function check() {
        if (!$this->config['app_id'] || !$this->config['channel_code']) {
            E("支付宝设置有误！");
        }
        return true;
    }

    public function buildRequestForm(PayVo $vo) {
		
		$app_id = $this->config['app_id'];                                
        $channel_code = $this->config['channel_code'];   
        $app_key = $this->config['app_key']; 	
        $cp_order_id = $vo->getOrderNo();
        $app_package_name = "joy8899.com";
        $cp_notify_url  = $this->config['notify_url'];
        $sdk_version = "1";
        $sign = md5($app_id.$cp_order_id.$cp_notify_url.$channel_code.$sdk_version.$app_package_name.$app_key);    
		
		$parameter = array(
			"app_id"         => $this->config['app_id'],                              
			"cp_order_id"       => $cp_order_id,
			"cp_notify_url"	 => $this->config['notify_url'],
			"fee"            => $vo->getFee() * 100,
			"subject"	     => "充值",
			"pay_type"	     => "1",
			"notify_url"	 => $this->config['return_url']."/orderid/".$cp_order_id,
			"channel_code"	 => $this->config['channel_code'],
			"app_version"    => $sdk_version,
			"app_package_name"  => $app_package_name,
			"Imsi"	         => "test",
			"Imei"	         => "test",
			"client_ua"	     => "UA",
			"api_level"	     => "1",
			"sdk_version"	 => $sdk_version,
			"sign_msg"	     => $sign
		 );
		 
		$para = $this->getParam($parameter);
		
		$data = $this->getResponse2($para);
		
		if($data){
			$data = (array)json_decode($data);
			if($data['code'] == 0){
				$ext = (array)$data['ext'];
				$info = $ext['order_info'];
				header("Location:".$info);
		        exit;
			}else{
				echo json_encode(array('code'=>$data['code']));
			}
		}else{
            echo json_encode(array('status'=>500));
        }
        //return null;
    }
	
	protected function getParam($para) {
        $arg = "";
        while (list ($key, $val) = each($para)) {
            $arg.=$key . "=" . $val . "&";
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, -1);
		return $arg;
    }


    /**
     * 针对notify_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
   public function verifyNotify($notify) {
	    $ispost = $notify['ispost'];
		unset($notify['ispost']);
		$arr = $notify;
		
		$fp = fopen("/alidata/data/html/data/runtime/sdkpay/Logs/verifyNotify22_log.txt","a");
	    flock($fp, LOCK_EX) ;
	    fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".json_encode($_REQUEST)."\n");
	    flock($fp, LOCK_UN);
	    fclose($fp);
		
		$orderid = $_REQUEST['orderid'];
		
	    $cp_order_id=$_REQUEST['cp_order_id'];
		$correlator=$_REQUEST['correlator'];
		$fee=$_REQUEST['fee'];
		$result_code=$_REQUEST['result_code'];
		$cp_notify_url=$_REQUEST['cp_notify_url'];
		$result_sign=$_REQUEST['sign'];
		
		$sign = md5($correlator . $cp_order_id . $fee . $result_code . $this->config['app_key']);
	   
	    $fp = fopen("/alidata/data/html/data/runtime/sdkpay/Logs/verifyNotify23_log.txt","a");
	    flock($fp, LOCK_EX) ;
	    fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$sign."\n");
	    flock($fp, LOCK_UN);
	    fclose($fp);
		
        if ($result_sign == $sign)
        { 
            //echo "success";
            if($result_code == 0){
              /**
              * 在这里对数据进行处理
              */
			   $info = array();
               if (empty($orderid)) {
			      $out_trade_no = $cp_order_id;
	              //交易号
	              $trade_no = $correlator;
	              //交易状态
	              $trade_status = 1;

                  $info['status']       = $trade_status;
                  $info['trade_no']     = $trade_no;
			      $info['out_trade_no'] = $out_trade_no;
			      $info['total_amount'] = $fee / 100;
			      $info['ispost'] = 1;
                }else{
                   //支付状态
                  $info['status']       = 2;
                  $info['out_trade_no'] = htmlspecialchars($orderid);
				  $info['ispost'] = 0;
                } 
			
               return $info;
            }
        }else{
			return false;
		}
		
    }

    protected function setInfo($notify) {
        $info = array();
        //支付状态
        $info['status'] = ($notify['trade_status'] == 'TRADE_FINISHED' || $notify['trade_status'] == 'TRADE_SUCCESS') ? true : false;
        $info['money'] = $notify['total_fee'];
        $info['out_trade_no'] = $notify['out_trade_no'];
        $this->info = $info;
    }

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空 
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
    protected function getResponse2($Params) {
        $veryfy_url = $this->gateway . "?" . $Params;
        $responseTxt = $this->fsockOpen($veryfy_url);
        return $responseTxt;
    }
	
	protected function url_data($Url, $Params, $Method='post'){
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

		$res = curl_exec($ch);
		//$data = json_decode($Res,true);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		print_r($res);
		exit;
		if ($res == NULL) { 
		   $this->errInfo = "call http err :" . curl_errno($ch) . " - " . curl_error($ch) ;
		   curl_close($ch);
		   return false;
		} else if($this->responseCode  != "200") {
			$this->errInfo = "call http err httpcode=" . $this->responseCode  ;
			curl_close($ch);
			return false;
		}
		
		curl_close($ch);

		return $res;
	}

}
