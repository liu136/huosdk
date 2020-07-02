<?php

/**
 * 充值统计页面
 * 
 * @author
 *
 */
namespace Pay\Controller;

use Common\Controller\AdminbaseController;

class PayController extends AdminbaseController {
    
     protected $client_model;
	
    function _initialize() {
        parent::_initialize();
		$this->client_model = M(C('MNG_DB_NAME').".client", C('LDB_PREFIX'));
		
		$ismobile = is_mobile_request();
		if(!$ismobile){
			echo "访问失败aaa";
           exit;
		}
    }
	
	public function pricelist() {
		$data['amount'] = !empty(I("post.amount")) ? I("post.amount") : 0; // 交易金额
        $data['username'] = !empty(I("post.username")) ? I("post.username") : ''; // 用户名
        $data['roleid'] = !empty(I("post.roleid")) ? I("post.roleid") : ''; // 用户角色
        $data['serverid'] = !empty(I("post.serverid")) ? I("post.serverid") : ''; // 服务器
        $data['imei'] = !empty(I("post.imei")) ? I("post.imei") : ''; // 其他支付身份信息
        $data['appid'] = !empty(I("post.appid")) ? intval(I("post.appid")) : 0; // appid
        $data['agentgame'] = !empty(I("post.agentgame")) ? I("post.agentgame") : '';  // 渠道
        $data['productname'] = !empty(I("post.productname")) ? I("post.productname") : ''; // 商品名称
		$data['orderdesc'] = !empty(I("post.orderdesc")) ? I("post.orderdesc") : '';
        $data['attach'] = !empty(I("post.attach")) ? I("post.attach") : ''; // CP方的扩展参数
		$data['reg_time'] = !empty(I("post.reg_time")) ? I("post.reg_time") : 0;
		$data['paystatus'] = I("paystatus"); // 
		$data['type'] = I('type');
		
		$data['baoming'] = I('baoming');
			
		$data['cid'] = intval(I('cid'));
		
		$pricelist = array(
		    "5000"=>"充值5000元获得50000元宝",
			"2000"=>"充值2000元获得20000元宝",
			"1000"=>"充值1000元获得10000元宝",
			"648"=>"充值648元获得6480元宝"
		);
		
		$this->assign("pricelist",$pricelist);
		$this->assign("pay",$data);
		$this->display();
	}
	
	public function fromsub() {
		
		$data['amount'] = !empty(I("post.amount")) ? I("post.amount") : 0; // 交易金额
        $data['username'] = !empty(I("post.username")) ? I("post.username") : ''; // 用户名
        $data['roleid'] = !empty(I("post.roleid")) ? I("post.roleid") : ''; // 用户角色
        $data['serverid'] = !empty(I("post.serverid")) ? I("post.serverid") : ''; // 服务器
        $data['imei'] = !empty(I("post.imei")) ? I("post.imei") : ''; // 其他支付身份信息
        $data['appid'] = !empty(I("post.appid")) ? intval(I("post.appid")) : 0; // appid
        $data['agentgame'] = !empty(I("post.agentgame")) ? I("post.agentgame") : '';  // 渠道
        $data['productname'] = !empty(I("post.productname")) ? I("post.productname") : ''; // 商品名称
		$data['orderdesc'] = !empty(I("post.orderdesc")) ? I("post.orderdesc") : '';
        $data['attach'] = !empty(I("post.attach")) ? I("post.attach") : ''; // CP方的扩展参数
		$data['reg_time'] = !empty(I("post.reg_time")) ? I("post.reg_time") : 0;
		$data['paystatus'] = I("paystatus"); // 
		$data['type'] = I('type');
		
		$data['baoming'] = I('baoming');
			
		$data['cid'] = intval(I('cid'));
	
		$data['cidenty'] = C('CIDENTY'); // 渠道标识
        $data['clientkey'] = C('CLIENTKEY'); // 渠道标识符
       
		// 校验渠道key
        if (empty($data['cidenty']) || empty($data['clientkey'])) {
           echo "标识不能为空";
           exit;
        } else {
			
			$dbname = $this->client_model
			->where(array("clientidentifier"=>$data['cidenty'],"clientkey"=>$data['clientkey'],"`status`"=>2))
			->field("2 as cid, id as dbname, paystatus,payway")->find();
            
            if (empty($dbname)) {
               echo "渠道错误";
               exit;
            }
			$dbname['dbname'] = 'db_sdk_2' ;
        }
		
		$user = M($dbname['dbname'].".members",C('CDB_PREFIX'))
		      ->where(array("username"=>$data['username']))
			  ->field("id")
			  ->find();
	    
		if(empty($user)){
			echo "账号不存在";
			exit;
		}
		
		$data['token'] = md5(md5("username=".$data['username']."&amount=".$data['amount']."&appid=".$data['appid']."&userid=".$user['id']."&paytime=".$data['reg_time']).C("PAYCODE"));
		
		$sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".U("Pay/Pay_post")."' method='POST'>";
		while (list ($key, $val) = each ($data)) {
			if (false === $this->checkEmpty($val)) {
				$val = str_replace("'","&apos;",$val);
				$sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
			}
        }

		//submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<input type='submit' value='' style='display:none;''></form>";
		
		$sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
		
		echo $sHtml;
		exit;
		
	}
	
	/**
	 * 校验$value是否非空
	 *  if not set ,return true;
	 *    if is null , return true;
	 **/
	public function checkEmpty($value) {
		if (!isset($value))
			return true;
		if ($value === null)
			return true;
		if (trim($value) === "")
			return true;

		return false;
	}
    
    public function index() {
		
		$urldata = file_get_contents("php://input");
        $post_data=json_decode($urldata);
		
		$post_data = (array)$post_data;
		
		if(empty($post_data) && empty($_POST)){
			echo "参数不能为空";
			exit;
		}
		/**
		$data['amount'] = !empty($post_data['a']) ? $post_data['a'] : 0; // 交易金额
        $data['username'] = !empty($post_data['b']) ? $post_data['b'] : ''; // 用户名
		$data['appid'] = !empty($post_data['c']) ? intval($post_data['c']) : 0; // appid
        $data['roleid'] = !empty($post_data['d']) ? $post_data['d'] : ''; // 用户角色
        $data['serverid'] = !empty($post_data['e']) ? $post_data['e'] : ''; // 服务器
		$data['agentgame'] = !empty($post_data['f']) ? $post_data['f'] : 'default';  // 渠道
        $data['imei'] = !empty($post_data['g']) ? $post_data['g'] : ''; // 其他支付身份信息
		$data['productname'] = !empty($post_data['h']) ? $post_data['h'] : ''; // 商品名称
		$data['orderdesc'] = !empty($post_data['j']) ? $post_data['j'] : ''; // 商品名称
        $data['attach'] = !empty($post_data['k']) ? $post_data['k'] : ''; // CP方的扩展参数
		
		$data['baoming'] = !empty($post_data['l']) ? $post_data['l'] : ''; // 包名，用于支付返回唤起游戏
		
        $data['cidenty'] = C('CIDENTY'); // 渠道标识
        $data['clientkey'] = C('CLIENTKEY'); // 渠道标识符
        //$data['token'] = !empty(I("post.z")) ? I("post.z") : ''; // 
		**/
		
		if($post_data){
			$data['amount'] = !empty($post_data['a']) ? $post_data['a'] : 0; // 交易金额
			$data['username'] = !empty($post_data['b']) ? $post_data['b'] : ''; // 用户名
			$data['appid'] = !empty($post_data['c']) ? intval($post_data['c']) : 0; // appid
			$data['roleid'] = !empty($post_data['d']) ? $post_data['d'] : ''; // 用户角色
			$data['serverid'] = !empty($post_data['e']) ? $post_data['e'] : ''; // 服务器
			$data['agentgame'] = !empty($post_data['f']) ? $post_data['f'] : 'default';  // 渠道
			$data['imei'] = !empty($post_data['g']) ? $post_data['g'] : ''; // 其他支付身份信息
			$data['productname'] = !empty($post_data['h']) ? $post_data['h'] : ''; // 商品名称
			$data['orderdesc'] = !empty($post_data['j']) ? $post_data['j'] : ''; // 商品名称
			$data['attach'] = !empty($post_data['k']) ? $post_data['k'] : ''; // CP方的扩展参数
			$data['baoming'] = !empty($post_data['l']) ? $post_data['l'] : ''; // 包名，用于支付返回唤起游戏
			$data['typeid'] = 2;
			 $data['cidenty'] = C('CIDENTY'); // 渠道标识
			$data['clientkey'] = C('CLIENTKEY'); // 渠道标识符
		}
		else{
			$data['amount'] = !empty($_POST['money']) ? $_POST['money'] : 1; // 交易金额
			$data['username'] = !empty($_POST['username']) ? $_POST['username'] : '2720000'; // 用户名
			$data['appid'] = !empty($_POST['appid']) ? intval($_POST['appid']) : '1'; // appid
			$data['roleid'] = !empty($_POST['roleid']) ? $_POST['roleid'] : 'aaa'; // 用户角色
			$data['serverid'] = !empty($_POST['serverid']) ? $_POST['serverid'] : 'aaa'; // 服务器
			$data['agentgame'] = !empty($_POST['agentid']) ? $_POST['agentid'] : 'aaa';  // 渠道
			$data['imei'] = !empty($_POST['imeil']) ? $_POST['imeil'] : 'aaa'; // 其他支付身份信息
			$data['productname'] = !empty($_POST['productname']) ? $_POST['productname'] : 'bbb'; // 商品名称
			$data['orderdesc'] = !empty($_POST['productdesc']) ? $_POST['productdesc'] : 'bbb'; // 商品名称
			$data['attach'] = !empty($_POST['attach']) ? $_POST['attach'] : 'ccc'; // CP方的扩展参数
			$data['typeid'] = 1;
			$data['cidenty'] = C('CIDENTY'); // 渠道标识
			$data['clientkey'] = C('CLIENTKEY'); // 渠道标识符
		}
		
		$data['amount'] = intval($data['amount']);
		
		if($data['amount'] <= 0){
			echo "金额错误";
            exit;
		}
		// 校验渠道key
        if (empty($data['cidenty']) || empty($data['clientkey'])) {
           echo "标识不能为空";
           exit;
        } else {
			
			$dbname = $this->client_model
			->where(array("clientidentifier"=>$data['cidenty'],"clientkey"=>$data['clientkey'],"`status`"=>2))
			->field("2 as cid, id as dbname, paystatus,payway")->find();
            
            if (empty($dbname)) {
               echo "渠道错误";
               exit;
            }
			$dbname['dbname'] = 'db_sdk_2' ;
        }

		// 用户名不能为空
        if (empty($data['username'])) {
            echo "用户名不能为空";
			exit;
        }

        // 游戏渠道标识不能为空
        if (empty($data['agentgame'])) {
            echo "游戏渠道标识不能为空";
			exit;
        }

        // appid不能为空
        if (empty($data['appid'])) {
           echo "游戏ID不能为空";
			exit;
        }

        // 服务器id不能为空
        if (empty($data['serverid'])) {
            echo "区服不能为空";
			exit;
        }

        // 角色id不能为空
        if (empty($data['roleid'])) {
            echo "角色不能为空";
			exit;
        }
		
		//
        if (empty($data['orderdesc'])) {
            echo "订单描述不能为空";
			exit;
        }
		
		$user = M($dbname['dbname'].".members",C('CDB_PREFIX'))
		      ->where(array("username"=>$data['username']))
			  ->field("id")
			  ->find();
	    
		if(empty($user)){
			echo "账号不存在";
			exit;
		}
		
		//回调不能为空
        $cpurl = $this->getCpurl($data['appid'], $dbname['cid']);
        if (empty($cpurl)) {
            echo "未配置回调";
			exit;
        }
		
		$payarr = explode(',', $dbname['payway']);
	    //$payarr = explode(',', "2,17");
        

        $payarr = array_unique($payarr);

        foreach ($payarr as $key => $val) {
			$paydata = M(C('MNG_DB_NAME').".payway",C('LDB_PREFIX'))
			->where(array("id"=>$val))
			->field("id,payname,disc")
			->find();

		    if($paydata){
			   $payinfo[$key] = $paydata;
		    }		
        }

		$data['paystatus'] = $dbname['paystatus'];
		$data['reg_time'] = time();
        $data['paytoken'] = md5(md5("username=".$data['username']."&amount=".$data['amount']."&appid=".$data['appid']."&userid=".$user['id']."&paytime=".$data['reg_time']).C("PAYCODE"));
		
		$data['cid'] = $dbname['cid'];
		$this->assign("pay",$data);
		$this->assign("payway",$payinfo);
        $this->display();
    }
	
	
	function Pay_post(){
		if (IS_POST) {
			//if($this->client_model->autoCheckToken($_POST)){
				
				$data['amount'] = !empty(I("post.amount")) ? I("post.amount") : 0; // 交易金额
                $data['username'] = !empty(I("post.username")) ? I("post.username") : ''; // 用户名
                $data['roleid'] = !empty(I("post.roleid")) ? I("post.roleid") : ''; // 用户角色
                $data['serverid'] = !empty(I("post.serverid")) ? I("post.serverid") : ''; // 服务器
                $data['imei'] = !empty(I("post.imei")) ? I("post.imei") : ''; // 其他支付身份信息
                $data['appid'] = !empty(I("post.appid")) ? intval(I("post.appid")) : 0; // appid
                $data['agentgame'] = !empty(I("post.agentgame")) ? I("post.agentgame") : '';  // 渠道
                $data['productname'] = !empty(I("post.productname")) ? I("post.productname") : ''; // 商品名称
				$data['orderdesc'] = !empty(I("post.orderdesc")) ? I("post.orderdesc") : '';
                $data['attach'] = !empty(I("post.attach")) ? I("post.attach") : ''; // CP方的扩展参数
			    $data['reg_time'] = !empty(I("post.reg_time")) ? I("post.reg_time") : 0;
			    $data['paystatus'] = I("paystatus"); // 
			    $data['typeid'] = !empty(I("post.typeid")) ? I("post.typeid") : 0;
				$baoming = I('baoming');
				
			    $token = I('token');
			    $cid = intval(I('cid'));
		 	    $dbname = 'db_sdk_'.$cid;
			    $user = M($dbname.'.members',C('CDB_PREFIX'))
		               ->where(array("username"=>$data['username']))
			           ->field("id,agentid")
			           ->find();
			
			    $pay_time = $data['reg_time'];
			    $paytoken = md5(md5("username=".$data['username']."&amount=".$data['amount']."&appid=".$data['appid']."&userid=".$user['id']."&paytime=".$pay_time).C("PAYCODE"));
			
			    if($token != $paytoken){
				    echo "签名失败";
				    exit;
			    }
			    
                $regagent = $user['agentid'];
                $lm_username =$data['username'];
			
                $order_no = sp_get_orderid($cid);
		        $userip = get_client_ip(); // 用户支付时使用的网络终端IP
		
                //页面上通过表单选择在线支付类型
                $type = intval(I('type'));
		        $paytype_data = M(C('MNG_DB_NAME').".payway",C('LDB_PREFIX'))
			              ->where(array("id"=>$type))
			              ->field("payname")
			              ->find();
						  
			    $paytype = $paytype_data['payname'];
			
                $paydata['orderid'] = $order_no;
                $paydata['amount'] = $data['amount'];
                $paydata['userid'] = $user['id'];
                $paydata['roleid'] = $data['roleid'];
                $paydata['paytype'] = $paytype;
                $paydata['productname'] = $data['productname'];
                $paydata['agentgame'] = $data['agentgame'];
                $paydata['serverid'] = $data['serverid'];
                $paydata['appid'] = $data['appid'];
                $paydata['status'] = 0;
                $paydata['ip'] = $userip;
                $paydata['imei'] = $data['imei'];
                $paydata['create_time'] = $pay_time;
                $paydata['remark'] = $data['attach'];
                $paydata['regagent'] = $regagent;
                $paydata['cid'] = $cid;
                $paydata['lm_username'] = $lm_username;
                $paydata['switchflag'] = $data['paystatus'];
		    
			    //回调不能为空
                $cpurl = $this->getCpurl($data['appid'], $cid);
                if (empty($cpurl)) {
                   echo "未配置回调";
			       exit;
                }
			    
			    $payrs = $this->addpay($paydata,$dbname,$cpurl,$baoming);
			    if(!$payrs){
				   echo "支付创建失败";
				   exit;
			    }

		        $body = $data['orderdesc'];
		        $goods = '扩展参数';
		        $money = $data['amount'];
                
		        $key_data = $this->payment($paytype,$cid);
			    $key_data['cid']  =  $cid;
				$key_data['typeid']  =  $data['typeid'];
		        $pay = new \Vendor\Pay($paytype, $key_data);
                
		        $vo = new \Vendor\Pay\PayVo();
                $vo->setBody($body)
                   ->setFee($money) //支付金额
                   ->setOrderNo($order_no)
                   ->setTitle("wap充值")
                   ->setCallback("Pay/User/order")
                   ->setUrl(U("Pay/User/order"))
                   ->setParam(array('order_id' => $goods));
			
                echo $pay->buildRequestForm($vo);
            //}else{
			//	echo "请勿重复提交";
           //     exit;
			//}
		}
	    echo "请求失败";
        exit;
			
	}
	
	 
	public function paysu() {
		$typeid =I('get.typeid');
		$this->assign("typeid",$typeid);
		$this->display();
		exit;
	}
	
	public function payfa() {
		$typeid =I('get.typeid');
		$this->assign("typeid",$typeid);
		$this->display();
		exit;
	}
	  
	
   
}