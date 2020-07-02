<?php

/**
 * 充值统计页面
 * 
 * @author
 *
 */
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class HelpController extends AdminbaseController {
    
   
    function _initialize() {   
        parent::_initialize();
    }
    
    public function index() {
        
        $this->display();
    }
	
	public function process() {
        
        $this->display();
    }
	
	public function faq() {
        
        $this->display();
    }
	
	public function course() {
        
        $this->display();
    }
    
}