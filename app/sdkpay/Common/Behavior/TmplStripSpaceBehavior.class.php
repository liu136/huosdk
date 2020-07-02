<?php
// +---------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +---------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.93damai.com All rights reserved.
// +---------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +---------------------------------------------------------------------
namespace Common\Behavior;
use Think\Behavior;

// 初始化钩子信息
class TmplStripSpaceBehavior extends Behavior {

    // 行为扩展的执行入口必须是run
    public function run(&$content){
        
		if(C('TOKEN_ON')) {
			
            list($tokenName,$tokenKey,$tokenValue)=$this->getToken();
		    
            $input_token = '<input type="hidden" name="'.$tokenName.'" value="'.$tokenKey.'_'.$tokenValue.'" />';
            $meta_token = '<meta name="'.$tokenName.'" content="'.$tokenKey.'_'.$tokenValue.'" />';
			
            if(strpos($content,'{__TOKEN__}')) {
                // 指定表单令牌隐藏域位置
                $content = str_replace('{__TOKEN__}',$input_token,$content);
            }elseif(preg_match('/<\/form(\s*)>/is',$content,$match)) {
                // 智能生成表单令牌隐藏域
                $content = str_replace($match[0],$input_token.$match[0],$content);
            }
			
            $content = str_ireplace('</head>',$meta_token.'</head>',$content);
        }else{
            $content = str_replace('{__TOKEN__}','',$content);
        }

        
		if(C('TMPL_STRIP_SPACE')) {
    		/* 去除html空格与换行 */
    		$find           = array('~>\s+<~','~>(\s+\n|\r)~');
    		$replace        = array('> <','>');
    		$content    = preg_replace($find, $replace, $content);
    	}
        
    }

	//获得token
    private function getToken(){
        $tokenName  = C('TOKEN_NAME',null,'__hash__');
        $tokenType  = C('TOKEN_TYPE',null,'md5');
        if(!isset($_SESSION[$tokenName])) {
            $_SESSION[$tokenName]  = array();
        }
        // 标识当前页面唯一性
        $tokenKey   =  md5($_SERVER['REQUEST_URI']);
        if(isset($_SESSION[$tokenName][$tokenKey])) {// 相同页面不重复生成session
            $tokenValue = $_SESSION[$tokenName][$tokenKey];
        }else{
            $tokenValue = is_callable($tokenType) ? $tokenType(microtime(true)) : md5(microtime(true));            
            $_SESSION[$tokenName][$tokenKey]   =  $tokenValue;
            if(IS_AJAX && C('TOKEN_RESET',null,true))
                header($tokenName.': '.$tokenKey.'_'.$tokenValue); //ajax需要获得这个header并替换页面中meta中的token值
        }
        return array($tokenName,$tokenKey,$tokenValue); 
    }
}