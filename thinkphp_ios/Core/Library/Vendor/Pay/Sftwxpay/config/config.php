<?php
class Config{
    private $cfg = array(
		//接口请求地址，固定不变，无需修改
        'url'=>'https://pay.swiftpass.cn/pay/gateway',
		//测试商户号，商户需改为自己的
        'mchId'=>'105520043680',
		//测试密钥，商户需改为自己的
        'key'=>'14254dffc4c23f5bb8f2c6b5ffe9eadf',
		//版本号默认2.0
        'version'=>'2.0'
       );
    
    public function C($cfgName){
        return $this->cfg[$cfgName];
    }
}
?>