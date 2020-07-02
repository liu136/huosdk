<?php

use Vendor\Pay\Pay;
use Vendor\Pay\PayVo;

require(dirname(dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'Wapalipay/lib/alipay_submit.class.php');
require(dirname(dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'Wapalipay/lib/alipay_notify.class.php');

class Alipay extends Pay {

    protected $gateway = 'https://mapi.alipay.com/gateway.do';
    protected $verify_url = 'http://notify.alipay.com/trade/notify_query.do';
    protected $config = array(
        'seller_id' => '',
        'partner' => ''
    );

    public function check() {
        if (!$this->config['seller_id'] || !$this->config['partner']) {
            E("支付宝设置有误！");
        }
        return true;
    }

    public function buildRequestForm(PayVo $vo) {
		$parameter = array(
		"service"       => $this->config['service'],
		"partner"       => $this->config['partner'],
		"seller_id"  => $this->config['seller_id'],
		"payment_type"	=> $this->config['payment_type'],
		"notify_url"	=> $this->config['notify_url'],
		"return_url"	=> $this->config['return_url'],
		"_input_charset"	=> trim(strtolower($this->config['input_charset'])),
		"out_trade_no"	=> $vo->getOrderNo(),
		"subject"	=> $vo->getBody(),
		"total_fee"	=> $vo->getFee(),
		"show_url"	=> $show_url,
		"app_pay"	=> "Y",//启用此参数能唤起钱包APP支付宝
		"body"	=> $vo->getBody(),
		
     );

     $alipaySubmit = new AlipaySubmit($this->config);
     $html_text = $alipaySubmit->buildRequestForm2($parameter,"get", "");
     echo $html_text;
    }


    /**
     * 针对notify_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
   public function verifyNotify($notify) {
		
		$ispost = $notify['ispost'];
		unset($notify['ispost']);
		$arr = $notify;
	    
		$alipayNotify = new AlipayNotify($this->config);
        if ($ispost) {
			$verify_result = $alipayNotify->verifyNotify();
		}else{
			$verify_result = $alipayNotify->verifyReturn();
		}
		
        //$verify_result = $alipayNotify->verifyNotify();
        if ($verify_result) {
			
            $info = array();
            if ($ispost) {
			   $out_trade_no = $notify['out_trade_no'];
	           //支付宝交易号
	           $trade_no = $notify['trade_no'];
	           //交易状态
	           $trade_status = $notify['trade_status'];

               $info['status']       = ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') ? 1 : 0;
               $info['trade_no']     = $trade_no;
			   $info['out_trade_no'] = $out_trade_no;
			   $info['total_amount'] = $notify['total_fee'];
			   $info['ispost'] = 1;
            }elseif ($ispost == false) {
                //支付状态
                $info['status']       = 2;
                $info['out_trade_no'] = htmlspecialchars($notify['out_trade_no']);
				$info['ispost'] = 0;
            } 
			
            return $info;
        } else {
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
