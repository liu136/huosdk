<?php

// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------


use Vendor\Pay\Pay;
use Vendor\Pay\PayVo;

require(dirname(dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'Sftwxpay/request.class.php');

class Sftwxpay extends Pay {

 
    protected $config     = array(
        'sub_merchant_id'   => '',
        'method'     => '',
		'rsa_private_key'=>''
    );

    public function check() {
        if (!$this->config['sub_merchant_id'] || !$this->config['method'] ) {
            E("微信设置有误！");
        }
        return true;
    }

    public function buildRequestForm(PayVo $vo) {
		
		$qdata['out_trade_no'] =$vo->getOrderNo();
		$amout=$vo->getFee();
		$qdata['body'] =$vo->getBody();
		$qdata['total_fee'] =$amout*100;
		$qdata['mch_create_ip'] ='127.0.0.1';
		$qdata['mch_app_id']='DaMaiassistant';
		$qdata['mch_app_name'] ='鑫游SDK';
		$qdata['device_info']='IOS_SDK';
		$qdata['notify_url'] =$this->config['notify_url'];
		$qdata['callback_url'] =$this->config['return_url']."/orderid/".$vo->getOrderNo();
		$req = new Request();
		//$rdata =$req->mytest();
	
		$rdata=$req->submitOrderInfo($qdata);
		
		
		
		if($rdata['code']== 'success'){
			//$sHtml = $this->_buildFormwft($param, $rdata['msg']);
			$payurl=$rdata['msg'];
			
			$sHtml="<script language='javascript' type='text/javascript'>window.location.href='{$payurl}';</script>";
		
			return $sHtml;
		}
		
	
        return $sHtml;
       
    }
	
	/**
     * 构造表单
     */
    public function _buildFormwft( $gateway,$charset = 'utf-8') {
		
		
		
		
        header("Content-type:text/html;charset={$charset}");


        $sHtml = $sHtml . "<script language='javascript' type='text/javascript'>window.location.href='{$gateway}';</script>";
        return $sHtml;
    }


	
	
    /**
     * 创建MD5签名
     * @param array $para
     * @return string
     */
    protected function createSign($params) {
        $arg = '';
        foreach ($params as $key => $value) {
            if ($value != "" && $key != "goods_name" && $key != "meta_option" 
			&& $key != "goods_num" && $key != "remark" && $key != "goods_note"
			&& $key != "is_phone" && $key != "is_frame") {
                $arg .= "{$key}={$value}&";
            }
        }
		
        return strtoupper(md5($arg . 'key=' . $this->config['sign_key']));
    }

    /**
     * 解析远程模拟提交后返回的信息
     * @param $str_text 要解析的字符串
     * @return 解析结果
     */
    protected function parseResponse($str_text) {
        //以“&”字符切割字符串
        $para_split = explode('&', $str_text);
        //把切割后的字符串数组变成变量与数值组合的数组
        foreach ($para_split as $item) {
            //获得第一个=字符的位置
            $nPos            = strpos($item, '=');
            //获得字符串长度
            $nLen            = strlen($item);
            //获得变量名
            $key             = substr($item, 0, $nPos);
            //获得数值
            $value           = substr($item, $nPos + 1, $nLen - $nPos - 1);
            //放入数组中
            $para_text[$key] = $value;
        }

        if (!empty($para_text['res_data'])) {

            //token从res_data中解析出来（也就是说res_data中已经包含token的内容）
            $doc                        = new \DOMDocument();
            $doc->loadXML($para_text['res_data']);
            $para_text['request_token'] = $doc->getElementsByTagName("request_token")->item(0)->nodeValue;
        }

        return $para_text;
    }

    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
    protected function getSignVeryfy($param, $sign) {
        //除去待签名参数数组中的空值和签名参数
        $param_filter = array();
        while (list ($key, $val) = each($param)) {
            if ($key == "sign" || $key == "sign_type" || $val == "") {
                continue;
            } else {
                $param_filter[$key] = $param[$key];
            }
        }

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = "";
        while (list ($key, $val) = each($param_filter)) {
            $prestr.=$key . "=" . $val . "&";
        }
        
        $prestr = $prestr .'key=' . $this->config['sign_key'];
        $mysgin = md5($prestr);

        if ($mysgin == $sign) {
            return true;
        } else {
            return false;
        }
    }

    public function verifyNotify($notify) {
		
		$ispost = $notify['ispost'];
		unset($notify['ispost']);
		$arr = $notify;
		
		$fp = fopen("/alidata/data/html/data/runtime/sdk/wap4_log.txt","a");
	    flock($fp, LOCK_EX) ;
	    fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n ---".json_encode($notify)."---\n");
	    flock($fp, LOCK_UN);
	    fclose($fp);
		
		if($ispost == '1'){
			$xml=$notify['xmldata'];
			$req = new Request();
			$info = array();
	
			$rdata=$req->callback($xml);
		
			return $rdata;
		}else{
			$orderid =$notify['orderid'];
			
			$req = new Request();
	
			$rcode=$req->queryOrder($orderid);
			
			if($rcode == 1){
				
				$info['status']       = 1;
				$info['recode']     = 1;
				$info['out_trade_no'] = $orderid;

				return $info;
			}
			
			return false;
		}
		
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
    protected function getResponse($notify_id) {
        $partner     = $this->config['partner'];
        $veryfy_url  = $this->verify_url . "?partner=" . $partner . "&notify_id=" . $notify_id;
        $responseTxt = $this->fsockOpen($veryfy_url);
        return $responseTxt;
    }

}