<?php
/**
* 礼包管理中心
*
* @author
*/
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class HelpController extends AdminbaseController {

	/**
	 * 礼包列表
	 */
	public function index(){
	    $data = dm_get_help($this->dbname);
	    $this->assign('data',$data);
		$this -> display();
	}
}