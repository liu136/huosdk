<?php
/**
* 礼包管理中心
*
* @author
*/
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class GiftController extends AdminbaseController {
	
	protected $gift_model,$logoweb;
	
	function _initialize() {
		parent::_initialize();
		$this->gift_model = M($this->dbname.".gift",C('CDB_PREFIX'));
		$this->logoweb = DAMAIIMGSITE."/gift/";
	}

	
	public function index(){

	    $appid = $_SESSION['appid'];
	    $show = I('get.show');
        if (empty($show)){
            $show = 'giftlist';
        }
        
	    if($appid > 0){
	        $field = "a.gamename, b.icon, b.class ";
	        $gamedata = M(C('MNG_DB_NAME').'.game','l_')
	        ->alias('a')
	        ->field($field)
	        ->join('left join '.C('MNG_DB_NAME').'.'.C('LDB_PREFIX').'game_ext b ON  a.id = b.gid ')
	        ->where(array('a.id'=>$appid))
	        ->find();
	        $this->assign('game', $gamedata);
	    }
		
	    if($show == "mygift"){
	        $username = dm_get_current_user();
			
	        $userid = M($this->dbname.".members","c_")->where(array('username' => $username))->getField('id');
	        $field = "TRIM(b.code) as code,a.title, a.content,CONCAT('".$this->logoweb."',icon) as icon";
	        $giftdata = M($this->dbname.".giftinfo","c_")
	        ->alias('a')
	        ->field($field)
	        ->join("left join ".$this->dbname.".c_gift b ON a.id=b.infoid")
	        ->where(array('b.userid'=>$userid, 'a.appid'=>$appid))
	        ->order("update_time DESC")
	        ->select();
			
	        $this->assign('gifts', $giftdata);
	        $this->display('mygift');
	        exit;
	    } else {
	        $time = time();
	        $field = "id, title, appid, content, starttime, endtime, total, remain,CONCAT('".$this->logoweb."',icon) as icon";
	        $where['appid'] = $appid;
	        $where['endtime'] = array('GT',$time);
	        $where['remain'] = array('GT',0);
	        $where['isdelete'] = 0;
	        
	        $giftdata = M($this->dbname.".giftinfo","c_")
	        ->field($field)
	        ->where($where)
	        ->order("starttime ASC")
	        ->select();
			
	        $this->assign('gifts', $giftdata);
	        $this->display();
	    }
	}
	
	public function giftAjax(){
	    $data['b'] = 0;
	    $data['a'] = 7;
	    $infoid = intval(I('post.giftid'));
	    if (!empty($infoid)) {
	        $time = time();	    
	        $username = dm_get_current_user();
	        $userid = M($this->dbname.".members","c_")->where(array('username' => $username, 'flag'=>0))->getField('id');
	        $cnt = M($this->dbname.".gift","c_")->where(array('infoid'=>$infoid,'userid'=>$userid))->count('id');
	        
	        //未领取过礼包才能领取
	        if (0 == $cnt) {
	            $appid = $_SESSION['appid'];
	            $rs = M($this->dbname.".giftinfo","c_")->where(array('id'=>$infoid, 'appid'=>$appid))->setDec('remain');
	            if($rs>0){
	                $field = "code, id";
	                $giftdata = M($this->dbname.".gift","c_")->field($field)->where(array('infoid'=>$infoid,'userid'=>0))->find();
	                $rs = M($this->dbname.".gift","c_")->where(array('id'=>$giftdata['id']))->setField('userid',$userid);
	                if ($rs) {
	                    $data['b'] = M($this->dbname.".giftinfo","c_")->where(array('id'=>$infoid, 'appid'=>$appid))->getField('remain');
	                    $data['a'] = $giftdata['code'];
	                    $this->ajaxReturn($data);
	                }
	            }else{
	                $data['a'] = '3';
	            }
	        } else {
	            $data['a'] = '5';
	        }
	    }
	    $this->ajaxReturn($data);	    
	}
}