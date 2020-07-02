<?php
/**
* 客服管理
*
* @author wuyonghong
*/
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class ContactController extends AdminbaseController {
	
	function _initialize() {
        parent::_initialize();
	}
	/**
	 * 联系方式页面
	 */
	public function index(){
		$contact_model = M($this->dbname.".contact", C('CDB_PREFIX'));
		$list = $contact_model->where("id = %d ",1)->select();
		
		$this->assign("contact",$list[0]);
		$this -> display();
	}
	
	/**
	 * 修改联系信息
	 */
	public function editContact(){
		if(I('action') == 'contact'){
			$data['id'] = 1;
			$data['qq'] = I('qq');
			$data['tel'] = I('tel');
			$data['email'] = I('email');
			
			$contact_model = M($this->dbname.".contact", C('CDB_PREFIX'));
			$list = $contact_model->where("id = %d ",$data['id'])->select();
			if(count($list) > 0){
				$rs = $contact_model-> save($data);
			}else{
				$rs = $contact_model-> add($data);
			}

			// 获取图标文件名
            $logo = $_FILES['logo'];
			$float_img=$_FILES['float_img'];
			
            if ($logo['name'] != '' || $float_img['name'] != '') {
				$cid = sp_get_current_cid();
				
				if($logo['name'] != ''){
                   $logoname = $this->uploadimg($logo, "client_".$cid);	
				  
				   $cdata['logo'] = $logoname;
				}
				
				if($float_img['name'] != ''){
                   $float_imgname = $this->uploadimg($float_img, "client_float_".$cid);	
				
				   $cdata['float_img'] = $float_imgname;
				}

				$cdata['update_time'] = time();
				
				$client_model = M(C('MNG_DB_NAME').".client", C('LDB_PREFIX'));
                $rs = $client_model->where(array('id'=>$cid))->save($cdata);
            }
            
			if ($rs > 0 || !empty($logoname)) {
				$this->success("更新成功");
				exit;
			} else {
				$this->error("更新失败");
    		    exit;
			}
			exit;
		}
		
	}

}
	
?>