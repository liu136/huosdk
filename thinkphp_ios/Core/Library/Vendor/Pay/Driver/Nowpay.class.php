<?php

use Vendor\Pay\Pay;
use Vendor\Pay\PayVo;

require(dirname(dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'Nowpay/conf/Config.php');
require(dirname(dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'Nowpay/services/Services.php');

require(dirname(dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'Nowpay/utils/Log.php');

class Nowpay extends Pay {

    protected $gateway = '';
    protected $verify_url = '';
    protected $config = array(
        'appId' => '',
        'secure_key' => ''
    );

    public function check() {
        if (!$this->config['appId'] || !$this->config['secure_key']) {
            E("现在支付设置有误！");
        }
        return true;
    }

    public function buildRequestForm(PayVo $vo) {
	        $req=array();
            
            $req["mhtOrderName"]=$vo->getBody();
            $req["mhtOrderAmt"]=$vo->getFee();
            $req["mhtOrderDetail"]=$vo->getBody();
            $req["funcode"]=Config::TRADE_FUNCODE;
            $req["appId"]=$this->config['appId'];//应用ID
            $req["mhtOrderNo"]=$vo->getOrderNo();
            $req["mhtOrderType"]=Config::TRADE_TYPE;
            $req["mhtCurrencyType"]=Config::TRADE_CURRENCYTYPE;
            $req["mhtOrderStartTime"]=date("YmdHis");
            $req["notifyUrl"]=$this->config['notify_url'];
            $req["frontNotifyUrl"]=$this->config['return_url'];
            $req["mhtCharset"]=Config::TRADE_CHARSET;
            $req["deviceType"]=Config::TRADE_DEVICE_TYPE;
			$req["payChannelType"]=Config::TRADE_PAYCHANNELTYPE;
            $req["mhtReserved"]="test";
            $req["mhtSignature"]=Services::buildSignature($req,$this->config['secure_key']);
            $req["mhtSignType"]=Config::TRADE_SIGN_TYPE;
            
            
            $req_str=Services::trade($req);
			$text = $this->getResponse2(Config::TRADE_URL."?".$req_str);
			print_r($text);
			exit;
            //header("Location:".Config::TRADE_URL."?".$req_str);
    }


    /**
     * 针对notify_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
   public function verifyNotify($notify) {
		
		$ispost = $notify['ispost'];
		unset($notify['ispost']);
		$arr = $notify;
	    
		$request=file_get_contents('php://input');
        Log::outLog("网银通知接口", $request);
        parse_str($request,$request_form);
        if (Services::verifySignature($request_form)){
            $tradeStatus=$request_form['tradeStatus'];
            echo "success=Y";
            if($tradeStatus!=""&&$tradeStatus=="A001"){
              /**
              * 在这里对数据进行处理
              */
			   $info = array();
               if ($ispost) {
			      $out_trade_no = $request_form['mhtOrderNo'];
	              //交易号
	              $trade_no = $request_form['nowPayOrderNo'];
	              //交易状态
	              $trade_status = $request_form['tradeStatus'];

                  $info['status']       = ($trade_status == 'A001') ? 1 : 0;
                  $info['trade_no']     = $trade_no;
			      $info['out_trade_no'] = $out_trade_no;
			      $info['total_amount'] = $request_form['mhtOrderAmt'];
			      $info['ispost'] = 1;
                }elseif ($ispost == false) {
                   //支付状态
                  $info['status']       = 2;
                  $info['out_trade_no'] = htmlspecialchars($request_form['mhtOrderNo']);
				  $info['ispost'] = 0;
                } 
			
               return $info;
            }
           //支付失败
        }else {
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
    protected function getResponse2($veryfy_url) {
        $responseTxt = $this->fsockOpen($veryfy_url);
        return $responseTxt;
    }
	
	/*public function url_data($Url, $Params, $Method='post'){
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
		$data = json_decode($Res,true);

		curl_close($Curl);//关闭curl
		
		return $data;
	}*/

}
