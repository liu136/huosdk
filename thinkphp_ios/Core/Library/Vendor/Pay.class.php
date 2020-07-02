<?php

/**
 * 通用支付接口类
 * @author
 */
namespace Vendor;
 
class Pay {

    /**
     * 支付驱动实例
     * @var Object
     */
    private $payer;

    /**
     * 配置参数
     * @var type 
     */
    private $config;

    /**
     * 构造方法，用于构造实例
     * @param string $driver 要使用的支付驱动
     * @param array  $config 配置
     */
    public function __construct($driver, $config = array()) {
        /* 配置 */
        $pos = strrpos($driver, '\\');
        $pos = $pos === false ? 0 : $pos + 1;
        $apitype = strtolower(substr($driver, $pos));
		if($config['cid'] < 0){
			echo "渠道不存在";
			exit;
		}
        $this->config['notify_url'] = U("Pay/Public/notify@p.128xy.com", array('apitype' => $apitype, 'dmmethod' => 'notify','cid' => $config['cid'],'typeid' => $config['typeid']), false);
        $this->config['return_url'] = U("Pay/Public/notify@p.128xy.com", array('apitype' => $apitype, 'dmmethod' => 'return','cid' => $config['cid'],'typeid' => $config['typeid']), false);
        
        $config = array_merge($this->config, $config);
      
        /* 设置支付驱动 */
		
		require_once(dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'Pay/Driver/'.ucfirst($driver).'.class.php');
        $class = strpos($driver, '\\') ? $driver : '\\' . ucfirst(strtolower($driver));
		 
        $this->setDriver($class, $config);
    }

    public function buildRequestForm(Pay\PayVo $vo) {
        $this->payer->check();
		
        if (true) {
            return $this->payer->buildRequestForm($vo);
        } else {
            E(M("Pay")->getDbError());
        }
    }

    /**
     * 设置支付驱动
     * @param string $class 驱动类名称
     */
    private function setDriver($class, $config) {
        $this->payer = new $class($config);
        if (!$this->payer) {
            E("不存在支付驱动：{$class}");
        }
    }

    public function __call($method, $arguments) {
        if (method_exists($this, $method)) {
            return call_user_func_array(array(&$this, $method), $arguments);
        } elseif (!empty($this->payer) && $this->payer instanceof Pay\Pay && method_exists($this->payer, $method)) {
            return call_user_func_array(array(&$this->payer, $method), $arguments);
        }
    }

}
