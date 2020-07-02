<?php

// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------


use Vendor\Pay\Pay;
use Vendor\Pay\PayVo;

require(dirname(dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'Heepayh5/Tools.php');

class Heepayh5 extends Pay {

    protected $gateway    = 'https://pay.heepay.com/Payment/Index.aspx';
    protected $query_url = 'https://pay.heepay.com/Phone/SDK/PayQuery.aspx';
    protected $config     = array(
        'agent_id'   => '',
        'sign_key'     => ''
    );

    public function check() {
        if (!$this->config['agent_id'] || !$this->config['sign_key']) {
            E("汇付宝设置有误！");
        }
        return true;
    }

    public function buildRequestForm(PayVo $vo) {
		
	  $meta_option='{"s":"WAP","n":"多趣官网","id":"http://www.zwyouxi.com"}';
	  $meta_option=base64_encode(iconv("UTF-8","GB2312//IGNORE",$meta_option));
      
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
      $param = array(
            'version' => 1,
            'agent_id' => $this->config['agent_id'],
            'agent_bill_id' => $vo->getOrderNo(),
			'agent_bill_time' => date("YmdHis"),
			'pay_type' => 30,
            'pay_amt' => $vo->getFee(),
			'notify_url' => $this->config['notify_url'],
			'return_url' => $this->config['return_url'],
			'user_ip' => $client_ip,
			'is_phone' => 1,
			'is_frame' => 0,
            'goods_name' => urlencode($vo->getTitle()),
            'goods_num' => 1,
            'remark' => 'remark',
            'goods_note' => urlencode($vo->getBody()),
			'meta_option' => $meta_option
        );
        
        $param['sign'] = $this->createSign($param);

        $sHtml = $this->_buildForm($param, $this->gateway);

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
		
        if ($this->getSignVeryfy($notify,$notify['sign'])) {
            $info = array();
            
			$out_trade_no = $notify['agent_bill_id'];
	           //交易号
	        $trade_no = $notify['jnet_bill_no'];
	           //交易状态
	        $trade_status = $notify['result'];

            $info['status']       = $trade_status;
            $info['trade_no']     = $trade_no;
			$info['out_trade_no'] = $out_trade_no;
			$info['total_amount'] = $notify['pay_amt'];
			//$info['ispost'] = 1;
            return $info;
        } else {
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