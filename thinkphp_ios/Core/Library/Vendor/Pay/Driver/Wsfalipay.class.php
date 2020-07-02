<?php

use Vendor\Pay\Pay;
use Vendor\Pay\PayVo;

class Wsfalipay extends Pay {

    protected $gateway = 'http://pay.paywap.cn/form/pay';
   // protected $verify_url = 'http://notify.alipay.com/trade/notify_query.do';
    protected $config = array(
        'usercode' => '',
        'compkey' => ''
    );

    public function check() {
        if (!$this->config['usercode'] || !$this->config['compkey']) {
            E("支付宝设置有误！");
        }
        return true;
    }

    public function buildRequestForm(PayVo $vo) {
		$client_ip = "";
		if (getenv('HTTP_CLIENT_IP')) {
			$client_ip = getenv('HTTP_CLIENT_IP');
		} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
			$client_ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('REMOTE_ADDR')) {
			$client_ip = getenv('REMOTE_ADDR');
		} else {
			$client_ip = $_SERVER['REMOTE_ADDR'];
		}
	  
	    $client_ip = str_replace(".","_",$client_ip);
	  
		$p1_usercode = $this->config['usercode'];                                //旺实富分配的商户号
        $compkey = $this->config['compkey'];              //旺实富分配的密钥
        $p2_order = $vo->getOrderNo();
        $p3_money = $vo->getFee();
        $p4_returnurl  = $this->config['return_url'];
        $p5_notifyurl  = $this->config['notify_url'];
        $p6_ordertime = date('Y-m-d H:i:s');
        $p7_sign = strtoupper(md5($p1_usercode."&".$p2_order."&".$p3_money."&".$p4_returnurl."&".$p5_notifyurl."&".$p6_ordertime.$compkey));    
		
		$parameter = array(
			"p1_usercode"    => $this->config['usercode'],                                //旺实富分配的商户号
			"p2_order"       => $vo->getOrderNo(),
			"p3_money"       => $p3_money,
			"p4_returnurl"	 => $this->config['return_url'],
			"p5_notifyurl"	 => $this->config['notify_url'],
			"p6_ordertime"	 => date('Y-m-d H:i:s'),
			"p7_sign"	     => $p7_sign,
			"p9_paymethod"	 => "4",
			"p14_customname"	=> "test",
			"p17_customip"	=> $client_ip,
			"p25_terminal"	=> "2",
			"p26_iswappay"	=> "3",
			
		 );
		 
        $sHtml = $this->_buildForm($parameter, $this->gateway);
        return $sHtml;
    }


    /**
     * 针对notify_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
   public function verifyNotify($notify) {
	    $ispost = $notify['ispost'];
		unset($notify['ispost']);
		$arr = $notify;
		
	    $p7_paychannelnum=$_POST['p7_paychannelnum'];
	    if(empty($p7_paychannelnum))
	    {
		  $p7_paychannelnum="";
		}
		$signmsg = $this->config['compkey'];//支付秘钥
		$md5info_paramet = $_REQUEST['p1_usercode']."&".$_REQUEST['p2_order']."&".$_REQUEST['p3_money']."&".$_REQUEST['p4_status']."&".$_REQUEST['p5_payorder']."&".$_REQUEST['p6_paymethod']."&".$_REQUEST['p7_paychannelnum']."&".$_REQUEST['p8_charset']."&".$_REQUEST['p9_signtype']."&".$signmsg;
		$md5info_tem= strtoupper(md5($md5info_paramet));
		$requestsign=$_REQUEST['p10_sign'];
        if ($md5info_tem == $_REQUEST['p10_sign'])
        { 
            echo "success";
		    $tradeStatus = $_REQUEST['p4_status'];
            if($tradeStatus){
              /**
              * 在这里对数据进行处理
              */
			   $info = array();
               if ($ispost) {
			      $out_trade_no = $_REQUEST['p2_order'];
	              //交易号
	              $trade_no = $_REQUEST['p5_payorder'];
	              //交易状态
	              $trade_status = $tradeStatus;

                  $info['status']       = $tradeStatus;
                  $info['trade_no']     = $trade_no;
			      $info['out_trade_no'] = $out_trade_no;
			      $info['total_amount'] = $_REQUEST['p3_money'];
			      $info['ispost'] = 1;
                }elseif ($ispost == false) {
                   //支付状态
                  $info['status']       = 2;
                  $info['out_trade_no'] = htmlspecialchars($_REQUEST['p2_order']);
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
    protected function getResponse2($notify_id) {
        $partner = $this->config['partner'];
        $veryfy_url = $this->verify_url . "?partner=" . $partner . "&notify_id=" . $notify_id;
        $responseTxt = $this->fsockOpen($veryfy_url);
        return $responseTxt;
    }

}
