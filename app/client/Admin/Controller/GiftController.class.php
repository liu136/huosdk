<?php
/**
* 礼包管理中心
*
* @author
*/
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class GiftController extends AdminbaseController {
	
	protected $game_model,$logoweb;

	function _initialize() {
		parent::_initialize();
		//游戏管理只有授权管理员才能登陆
		{
		    if(2 < sp_get_current_roletype()){
		        $this->error('无权限访问');
		    }
		}
		$this->game_model = M($this->dbname.".game", C('LDB_PREFIX'));
		$this->logoweb = DAMAIIMGSITE."/gift/";
	}

	/**
	 * 礼包列表
	 */
	public function giftList(){
		$this->_giftNewList();
		$this -> display();
	}
	/**
	**礼包列表
	*/
	function _giftNewList(){
		$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
		$title = I('title');
		$gameid = I('appid');
		$page = 1;
		$offset = ($page-1)*$rows;
		
		$result = array();
		$where = " isdelete = 0 ";
		
		$where_arr = array();
		if (isset($title) && $title != '') {
			$where .= " and title='%s'";
			array_push($where_arr,$title);
			$this->assign('title',$title);
		}
		
		if (isset($gameid) && $gameid >0) {
			$where .= " and appid=%d";
			array_push($where_arr,$gameid);

			$this->assign('appid',$gameid);
		}
		
		$giftinfo = M($this->dbname.'.giftinfo', C('CDB_PREFIX'));
		$result["total"] = $giftinfo->where($where,$where_arr)->count();
		
		$page = $this->page($result["total"], $rows);
		
		$field = "id,appid,title,content,endtime,isdelete,create_time,total,starttime,CONCAT('".$this->logoweb."',icon) as icon";

		$giftlist = $giftinfo->field($field)->where($where,$where_arr)->order("id DESC")->limit($page->firstRow . ',' . $page->listRows)->select();
			
		$this->_getGames();
		
		$this->assign('giftlist', $giftlist);
		$this->assign("page", $page->show('Admin'));
	}
	
	
	/**
	 * 
	 * 删除礼包
	 */
	public function delGift() {
		$gift_id = I('id');
		
		if($gift_id != ''){
			$info = M($this->dbname.'.giftinfo', C('CDB_PREFIX'));
			
			$data['isdelete'] = 1;
			//伪删除信息
			$rs = $info -> where("id=%d",$gift_id)->data($data)->save();
			
			if ($rs) {
				$this->success("删除成功", U("Gift/giftList"));
				exit;
			} else {
				$this->error("删除失败");
				exit;
			}
		}
	}
	
	public function addGift(){
	    //联运并通过审核的游戏才能添加礼包 
		$this->_getGames();
		$this->display();
	}

	/**
	 * 添加礼包
	 */
	public function addgift_post(){
			//获取数据
			$libao_data['appid'] = I('appid');
			$libao_data['title'] = I('title');
			$libao_data['content'] = I('content');
			$libao_data['starttime'] = strtotime(I('starttime'));
			$libao_data['endtime'] = strtotime(I('endtime'));
			$libao_data['create_time'] = time();
			
			$icon = $_FILES['icon'];//图标
			
			if (empty($icon['name'])) {
				$this->error("请上传icon");
				exit;
			}

			if(empty($libao_data['appid']) || empty($libao_data['title']) || empty($libao_data['content'])
				|| empty($libao_data['starttime']) || empty($libao_data['endtime'])){
				$this->error("请填写完数据后再提交");
				exit;
			}
			
			if($libao_data['starttime']>=$libao_data['endtime']){
				$this->error("兑换日期开始时间不能大于结束时间");
			}
			
			//插入数据
			$libaoinfo = M($this->dbname.'.giftinfo', C('CDB_PREFIX'));
			$code = I('code');
			$codearr = explode("\n", trim($code));
			array_filter($codearr);
			$total = count($codearr);
			$libao_data['total'] = $total;
			$libao_data['remain'] = $total;
			
			if($libaoinfo->create($libao_data)){ 
			   
				$lastInsId = $libaoinfo->add();
				if($lastInsId){
					 $iconname = $this->uploadimg($icon, $lastInsId,"gift/");
				     $extdata['icon'] = $iconname;
					 if(!empty($extdata['icon'])){
						$ext_rs = $libaoinfo->where(array("id"=>$lastInsId))->save($extdata);
					 }
				}
				$libao = M($this->dbname.'.gift', C('CDB_PREFIX'));
				foreach ($codearr as $val) {
					$dataList[] = array('infoid'=>$lastInsId,'code'=>$val);
				}
				
				if(count($dataList) > 0){
					$libao->addAll($dataList);
				}else{
					$this->error("请填写礼包码");
					exit;
				}
				$this->success("添加成功!", U("Gift/giftList"));
				exit;
			} else { 
				$this->error("添加失败");
				exit;
			} 
		
	}

	public function editGift(){
		$id= intval(I("get.id"));

		$this->_getGames();
		
		$infomodel = M($this->dbname.'.giftinfo', C('CDB_PREFIX'));
		$giftlist = $infomodel->where("id = %d",$id)->select();
		
		$libao = M($this->dbname.".gift", C('CDB_PREFIX'));
		foreach ($giftlist as $key => $val) {
			$codestr = "";

			$list = $libao->field("code")->where("infoid='".$val['id']."'")->select();
			
			foreach ($list as $k=>$v) {
				$codestr .= $v['code']."\n";
			}

			$giftlist[$key]['code'] = $codestr;	
		}
		
		$this -> assign($giftlist[0]);
		$this -> display();
	}

	/**
	 * 修改礼包
	 */
	public function editgift_post(){
			$libao_id = I('id');
			
			//获取数据
			$libao_data['appid'] = I('appid');
			$libao_data['title'] = I('title');
			$libao_data['content'] =  I('content');
			$libao_data['starttime'] = strtotime(I('starttime'));
			$libao_data['endtime'] = strtotime(I('endtime'));
			
			$libao_data['create_time'] = time();
			
			if($libao_data['starttime']>=$libao_data['endtime']){
				$this->error("兑换日期开始时间不能大于结束时间");
			}
			
			$icon = $_FILES['icon'];//图标
			
			/*
			 * 判断是插入还是修改保存
			 */
			if($libao_id != ''){
				//修改数据
				$libaoinfo = M($this->dbname.'.giftinfo', C('CDB_PREFIX'));
				$code = I('code');
			    $codearr = explode("\n", trim($code));
				array_filter($codearr);
			    $total = count($codearr);
				
				if (!empty($icon['name'])) {
					$iconname = $this->uploadimg($icon, $libao_id,"gift/");
					$libao_data['icon'] = $iconname;
				}
				
				if($libaoinfo->create($libao_data))
				{
					
					///
		            $giftlist = $libaoinfo->where("id = %d",$libao_id)->select();
		
		            $libao = M($this->dbname.".gift", C('CDB_PREFIX'));
		            foreach ($giftlist as $key => $val) {
			            $codestr = "";

			            $list = $libao->field("code")->where("infoid='".$val['id']."'")->select();
			
			            foreach ($list as $k=>$v) {
				           $codestr .= $v['code'];
			            }
                        
						if(empty($codestr) && $codestr != ""){
							
						}
			            $giftlist[$key]['code'] = $codestr;	
		            }
					
					$oldcount = count($giftlist);
					
				    foreach ($codearr as $key =>$val) {
					   if($oldcount > $key){
						   if($giftlist[$key]['code'] != $val){
						       $rs = $libao->where(array('infoid'=>$libao_id,'code'=>$giftlist[$key]['code']))->setField('code',$val);
					       }
					   }else{
						   $dataList[] = array('infoid'=>$libao_id,'code'=>$val);
					   }
					   
				    }
					
					
					if(count($dataList) > 0){
						$total = $total-$oldcount;
			           
					    $libaoinfo->where(array('id'=>$libao_id))->setInc('total',$total);
					    $libaoinfo->where(array('id'=>$libao_id))->setInc('remain',$total);
						
					    $libao->addAll($dataList);
				    }
				    ////
					
					$update = $libaoinfo -> where("id = %d ",$libao_id)->save();//update
					
					if($update){
						$this->success("更新成功!", U("Gift/giftList"));
						exit;
					}
				}
				$this->error("修改失败");
				exit;
			}
	}
}