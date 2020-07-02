<?php

/**
 * 充值统计页面
 * 
 * @author
 *
 */
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class PayController extends AdminbaseController {
    
    protected $daypaymodel,$where,$orderwhere,$appidwhere,$isdiscount;
    
    function _initialize() {
        parent::_initialize();
        $this->daypaymodel = M($this->dbname.'.dayagentgame', C('CDB_PREFIX'));
        
        $roletype = sp_get_current_roletype();
		$uid = sp_get_current_admin_id();

        if ($roletype > 2) {
			$userids = $this->getAgentids($uid);
			$appids = $this->getAppids($uid);
            $this->where = "agentid in (".$userids.") ";
			//$this->appidwhere = "appid in (".$appids.") ";
            $this->orderwhere = "e.agentid in (".$userids.") ";
			
        }else{
            $this->where = "1 ";
			//$this->appidwhere = "1";
            $this->orderwhere = "1 ";
        }
		
		$this->isdiscount = 0;
		$cid = sp_get_current_cid();
		
		$this->assign('isdiscount',$this->isdiscount);  
    }
    
    public function index() {
        $this->_getAllgames();
        $this->_getpaydata();
        $this->display();
    }
    
    public function _payindex() {
        $this->display();
    }
    
    public function orderindex() {
		
		$where_ands = array();
        $roletype = sp_get_current_roletype();
        $adminid = sp_get_current_admin_id();
		$where_ands = array();
        if ($roletype > 2){  //专员能看到自己子渠道的玩家数据
            $ids = $this->getAgentids($adminid);
            $userwhere = " id in ($ids)";
            array_push($where_ands, "agentid in ($ids)");
        }
        
        $this->_getAgents($userwhere);
       
        $this->_getAllgames();
		
        $this->_payway();        
        $this->_paystatus();
		
        $this->_orderList();
        $this->display();
    }
    
    public function gameindex() {
        $this->_getAllgames();
        $this->_getgamedata();
        $this->display();
    } 

    /*
     * 充值记录表
     */
    function _getpaydata(){
        $paymodel = M($this->dbname.'.pay', C('CDB_PREFIX'));
		
		 $where_ands = array();
         $fields = array(
                'username' => array(
                        "field" => "m.username", 
                        "operator" => "=" 
                )
        );
        
         if (IS_POST) {
            foreach ($fields as $param => $val) {                
                if (isset($_POST[$param]) && !empty($_POST[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $get = trim($_POST[$param]);
                    $_GET[$param] = $get;   
                
                    if ($operator == "like") {
                        $get = "%$get%";
                    }                    
                    array_push($where_ands, "$field $operator '$get'");
                }
            }  
        }else{
            foreach ($fields as $param => $val) {
                if (isset($_GET[$param]) && !empty($_GET[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $get = trim($_GET[$param]);
 
                    if ($operator == "like") {
                        $get = "%$get%";
                    }                    
                    array_push($where_ands, "$field $operator '$get'");
                }
            }  
        }
		
		
		if($this->orderwhere != "1"){
			array_push($where_ands, $this->orderwhere);
		}
		
        $where = join(" and ", $where_ands); 
        
		$cquery = "select m.username,sum(a.amount) as amount,cu.user_nicename,max(a.amount) as mamount  from ".$this->dbname.".".C('CDB_PREFIX')."pay a
                   left join ".$this->dbname.".".C('CDB_PREFIX')."members m on a.userid=m.id
                   left join ".$this->dbname.".".C('LDB_PREFIX')."clientusers cu on cu.id = m.agentid
                   where ".$where." AND a.status = 1 group by a.userid";
        $citems = M()->query($cquery);
        $count=count($citems);
		
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $page = $this->page($count, $rows);
		
		$limit = " limit " . $page->firstRow . ',' . $page->listRows;
        $cquery = "select m.agentid,m.username,sum(a.amount) as amount,cu.user_nicename,max(a.amount) as mamount  from ".$this->dbname.".".C('CDB_PREFIX')."pay a
                   left join ".$this->dbname.".".C('CDB_PREFIX')."members m on a.userid=m.id
                   left join ".$this->dbname.".".C('LDB_PREFIX')."clientusers cu on cu.id = m.agentid
                   where ".$where." AND a.status = 1 group by a.userid order by SUM(a.amount) desc ".$limit;
		
        $items = M()->query($cquery);
		
        $this->assign("pays", $items);
        $this->assign("Page", $page->show('Admin'));
		$this->assign("formget", $_GET);
        $this->assign("current_page", $page->GetCurrentPage());
    }
    
    function _orderList() {
        $paymodel = M($this->dbname.'.pay', C('CDB_PREFIX'));
        
        $where_ands = array();
        
        $fields = array(
                'start_time' => array(
                        "field" => "a.create_time", 
                        "operator" => ">" 
                ), 
                'end_time' => array(
                        "field" => "a.create_time", 
                        "operator" => "<" 
                ), 
                'orderid' => array(
                        "field" => "a.orderid", 
                        "operator" => "=" 
                ), 
                'gid' => array(
                        "field" => "a.appid", 
                        "operator" => "=" 
                ), 
                'username' => array(
                        "field" => "e.username", 
                        "operator" => "=" 
                ), 
                'payway' => array(
                        "field" => "a.paytype", 
                        "operator" => "=" 
                ), 
                'paystatus' => array(
                        "field" => "a.status", 
                        "operator" => "=" 
                ),
                'agentname' => array(
                        "field" => "d.agentname",
                        "operator" => "="
                ),
                'agentnickname' => array(
                        "field" => "d.agentnicename",
                        "operator" => "="
                ),
	            'parentid' => array(
	                "field" => "d.parentid",
	                "operator" => "in"
	           ),
	            'serverid' => array(
	                "field" => "a.serverid",
	                "operator" => "="
	           )
        );
        
        if ('七日' == $_POST['submit']) {
            $_POST['start_time'] = date("Y-m-d",strtotime("-6 day"));
            $_POST['end_time'] = date("Y-m-d",time());
        }elseif ('本月' == $_POST['submit']) {    
            $_POST['start_time'] = date("Y-m-01");
            $_POST['end_time'] = date("Y-m-d",time());
        }
        
        if (IS_POST) {
            $_GET['gamename'] = I('gamename');
            foreach ($fields as $param => $val) {                
                if (isset($_POST[$param]) && !empty($_POST[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $get = trim($_POST[$param]);
                    $_GET[$param] = $get;   
                    
                    if ('start_time' == $param) {
                        $get = strtotime($get);
                    } else if ('end_time' == $param) {
                        $get .= " 23:59:59";
                        $get = strtotime($get);
                    } else if ('paystatus' == $param) {
                        $get = intval($get)-1;
                        if (3 == $get){                            
                            array_push($where_ands, "a.status = '1'");
                            array_push($where_ands, "c.status = '0'");
                            continue;
                        }
                    }
                    
					
                    if ($operator == "like") {
                        $get = "%$get%";
                    }                    
                    if ('parentid' == $param) {
						$ids = $this->getAgentids($get);
						$ids .= ','.$get.'';
						
			           array_push($where_ands, "($field $operator ($ids) or d.agentid = '$get')");
					}else{
                        array_push($where_ands, "$field $operator '$get'");
					}

                }
            }  
			
        }else{
            foreach ($fields as $param => $val) {
                if (isset($_GET[$param]) && !empty($_GET[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $get = trim($_GET[$param]);
                    
                    if ('start_time' == $param) {
                        $get = strtotime($get);
                    } else if ('end_time' == $param) {
                        $get .= " 23:59:59";
                        $get = strtotime($get);
                    } else if ('paystatus' == $param) {
                        $get = intval($get)-1;
                        if (3 == $get){                            
                            array_push($where_ands, "a.status = '1'");
                            array_push($where_ands, "c.status = '0'");
                            continue;
                        }
                    }

                    if ($operator == "like") {
                        $get = "%$get%";
                    }                    
                    
					if ('parentid' == $param) {
						$ids = $this->getAgentids($get);
						$ids .= ','.$get.'';
			            array_push($where_ands, "($field $operator ($ids) or d.agentid = '$get')");
					}else{
                       array_push($where_ands, "$field $operator '$get'");
					}
                    
                }
            }  
        }
        
        
        //array_push($where_ands, $this->where);
		
		if($this->orderwhere != "1"){
			array_push($where_ands, $this->orderwhere);
		}
        $where = join(" and ", $where_ands); 
		
        $count = $paymodel
        ->alias("a")
        ->join("inner join $this->dbname." . C('CDB_PREFIX') . "members e ON a.userid = e.id")
        ->join("left join $this->dbname." . C('CDB_PREFIX') . "paycpinfo c ON a.orderid = c.orderid")
        ->join("left join $this->dbname." . C('CDB_PREFIX') . "agentlist d ON a.regagent = d.agentgame")
        ->where($where)
        ->count();
        
		
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $page = $this->page($count, $rows);
        
        $field = "a.orderid, a.amount, e.username, a.roleid, a.paytype, a.productname, a.serverid,a.roleid,a.status,c.status cptatus, a.create_time, regagent";
        $field .= " ,d.agentname, cu.user_nicename as agentnicename, g.gamename,d.agentid,g.id,g.type";
		
        $items = $paymodel
        ->alias("a")
        ->field($field)
        ->where($where)
        ->join("inner join $this->dbname." . C('CDB_PREFIX') . "members e ON a.userid = e.id")
		->join("left join $this->dbname." . C('LDB_PREFIX') . "clientusers cu ON cu.id = e.agentid")
        ->join("left join $this->dbname." . C('CDB_PREFIX') . "paycpinfo c ON a.orderid = c.orderid")
        ->join("left join $this->dbname." . C('CDB_PREFIX') . "agentlist d ON a.regagent = d.agentgame")
	    ->join("left join $this->dbname." . C('LDB_PREFIX') . "game g ON a.appid = g.id")
        ->order("a.id DESC")
        ->limit($page->firstRow . ',' . $page->listRows)
        ->select(); 
        
        $paysum = $paymodel
        ->alias("a")
		->field("SUM(a.amount) as amount")
        ->join("inner join $this->dbname." . C('CDB_PREFIX') . "members e ON a.userid = e.id")
        ->join("left join $this->dbname." . C('CDB_PREFIX') . "paycpinfo c ON a.orderid = c.orderid")
        ->join("left join $this->dbname." . C('CDB_PREFIX') . "agentlist d ON a.regagent = d.agentgame")
        ->where($where." AND a.status = 1")
		->select();
       
		if ('导出xls' == $_POST['submit']) {
			$this->expPay($where);
		}
        
		$cid = sp_get_current_cid();
        
		$this->assign("paysum", $paysum[0]);	
        $this->assign("cid", $cid);
        $this->assign("orders", $items);
        $this->assign("formget", $_GET);
        $this->assign("Page", $page->show('Admin'));
        $this->assign("current_page", $page->GetCurrentPage());
    }
    
    function _getgamedata(){
       $model = M($this->dbname.'.dayagentgame', C('CDB_PREFIX'));

        $where = $this->where;
		$where_ands = array();
        
        $fields = array(
                'start_time' => array(
                        "field" => "date", 
                        "operator" => ">=" 
                ), 
                'end_time' => array(
                        "field" => "date", 
                        "operator" => "<=" 
                ), 
                'gid' => array(
                        "field" => "appid", 
                        "operator" => "=" 
                )
        );
        
        if ('今日' == $_POST['date_time']) {            
            $count = 1;
            $start = date("Y-m-d");
            $end  = date("Y-m-d");
        } elseif ('七日' == $_POST['date_time']) {
			
            $_POST['start_time'] = date("Y-m-d",strtotime("-6 day"));
            $_POST['end_time'] = date("Y-m-d",time());
            $count = 7;
            $start = date("Y-m-d",strtotime("-6 day"));
            $end  = date("Y-m-d");
        } elseif ('当月' == $_POST['date_time']) {
            $count = date('d');
			$_POST['start_time'] = date("Y-m-01");
            $_POST['end_time'] = date("Y-m-d",time());
            $start = date("Y-m-01");
            $end  = date("Y-m-d");
        } elseif ('30天' == $_POST['date_time']) {
            $count = 30;
			$_POST['start_time'] = date("Y-m-d",strtotime("-29 day"));
            $_POST['end_time'] = date("Y-m-d");
            $start = date("Y-m-d",strtotime("-29 day"));
            $end  = date("Y-m-d");
        }else{
            $start = date($_POST['start_time']);
            $end = date($_POST['end_time']);
        }
        
        if (IS_POST) {
            foreach ($fields as $param => $val) {                
                if (isset($_POST[$param]) && !empty($_POST[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $get = trim($_POST[$param]);
                    $_GET[$param] = $get;   
                   
                    if ($operator == "like") {
                        $get = "%$get%";
                    }                    
                    array_push($where_ands, "$field $operator '$get'");
                }
            }  
        }else{
            foreach ($fields as $param => $val) {                
                if (isset($_GET[$param]) && !empty($_GET[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $get = trim($_GET[$param]);
                    
                    if ($operator == "like") {
                        $get = "%$get%";
                    }                    
                    array_push($where_ands, "$field $operator '$get'");
                }
            }  
        }
		
        array_push($where_ands, $this->where);
 		if($this->appidwhere != "1"){
 			array_push($where_ands, $this->appidwhere);
 		}
        $where = join(" and ", $where_ands);
        $where_query = $where ? ' where '.$where : '';

        if($_POST['date_time'] == '导出exl'){
            $this->gamedata($where);
        }
        
        $bflagstart = true;
        $bflagend = true;
        if (isset($start) && !empty($start)) {
            $bflagstart = strtotime($start) <= mktime(0,0,0,date("m"),date("d"),date("Y"))? true : false;
        }  
        
        if (isset($end) && !empty($end)) {
             $bflagend = strtotime($end) >= mktime(0,0,0,date("m"),date("d"),date("Y"))? true : false;
        }
        
        if ($bflagstart){
			/* $field = "`date`, `appid`, SUM(`usercnt`) `usercnt`,SUM(`summoney`) `summoney`,SUM(`paycnt`) `paycnt`,SUM(`regpaycnt`) `regpaycnt`,SUM(`sumregmoney`) `sumregmoney`,SUM(`reg_cnt`) `reg_cnt`";
            $items = $model
            ->field($field)
            ->where($where)
            ->order("`date` desc")
            ->group("`date`, `appid`")
            ->select(); */
            
            $cquery = "select * from (SELECT date,appid,SUM(usercnt) usercnt,SUM(summoney) summoney,SUM(paycnt) paycnt,SUM(regpaycnt) regpaycnt,SUM(sumregmoney) sumregmoney,SUM(reg_cnt) reg_cnt
                    from ".$this->dbname.".".C('CDB_PREFIX')."dayagentgame
                    where ".$where."
                    GROUP BY date,appid
                    UNION
                    select date,appid,SUM(usercnt) usercnt,SUM(summoney) summoney,SUM(paycnt) paycnt,SUM(regpaycnt) regpaycnt,SUM(sumregmoney) sumregmoney,SUM(reg_cnt) reg_cnt
                    from ".$this->dbname.".".C('CDB_PREFIX')."tagentgamepayview
                    where ".$where."
                    GROUP BY date,appid
                    ORDER BY date desc)a ";
            $citems = M()->query($cquery);
            $count=count($citems);
        }else{
            $count = 1;
        }
        
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
		
        $page = $this->page($count, $rows);
        
        if(!empty($_GET['byorder'])){
            $order = $_GET['byorder'] == 1?"order by summoney asc,date desc":"order by summoney desc,date desc";
        }else{
            $order = "ORDER BY date desc";
        }
        if ($bflagstart) {  
            $sumfield = "date,SUM(`usercnt`) `usercnt`,SUM(`summoney`) `summoney`,SUM(`paycnt`) `paycnt`,SUM(`regpaycnt`) `regpaycnt`,SUM(`sumregmoney`) `sumregmoney`,SUM(`reg_cnt`) `reg_cnt`";

            $sql = "select ".$sumfield." from (SELECT date,appid,SUM(usercnt) usercnt,SUM(summoney) summoney,SUM(paycnt) paycnt,SUM(regpaycnt) regpaycnt,SUM(sumregmoney) sumregmoney,SUM(reg_cnt) reg_cnt
                    from ".$this->dbname.".".C('CDB_PREFIX')."dayagentgame
                    where ".$where."
                    GROUP BY date,appid
                    UNION
                    select date,appid,SUM(usercnt) usercnt,SUM(summoney) summoney,SUM(paycnt) paycnt,SUM(regpaycnt) regpaycnt,SUM(sumregmoney) sumregmoney,SUM(reg_cnt) reg_cnt 
                    from ".$this->dbname.".".C('CDB_PREFIX')."tagentgamepayview 
                    where ".$where."
                    GROUP BY date,appid )a";
            
            $sumitems = M()->query($sql);
			
            /*$field = "`date`, `appid`, SUM(`usercnt`) `usercnt`,SUM(`summoney`) `summoney`,SUM(`paycnt`) `paycnt`,SUM(`regpaycnt`) `regpaycnt`,SUM(`sumregmoney`) `sumregmoney`,SUM(`reg_cnt`) `reg_cnt`";
            $items = $model
            ->field($field)
            ->where($where)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("`date` desc")
            ->group("`date`, `appid`")
            ->select();*/
            //$where = $where ? ' where '.$where : '';
            $limit = " limit " . $page->firstRow . ',' . $page->listRows;
            $query = "select * from (SELECT date,appid,SUM(usercnt) usercnt,SUM(summoney) summoney,SUM(paycnt) paycnt,SUM(regpaycnt) regpaycnt,SUM(sumregmoney) sumregmoney,SUM(reg_cnt) reg_cnt
                    from ".$this->dbname.".".C('CDB_PREFIX')."dayagentgame
                    where ".$where."
                    GROUP BY date,appid
                    UNION
                    select date,appid,SUM(usercnt) usercnt,SUM(summoney) summoney,SUM(paycnt) paycnt,SUM(regpaycnt) regpaycnt,SUM(sumregmoney) sumregmoney,SUM(reg_cnt) reg_cnt 
                    from ".$this->dbname.".".C('CDB_PREFIX')."tagentgamepayview 
                    where ".$where."
                    GROUP BY date,appid )a 
                    ".$order." ".$limit;
//             echo $query;
            $items = M()->query($query);
        }
	    
        $this->assign("totalpays", $sumitems);
        $this->assign("pays", $items);
		$this->assign("formget", $_GET);
        $this->assign("Page", $page->show('Admin'));
        $this->assign("current_page", $page->GetCurrentPage());
    }
    
    function repairorder(){
        
        $orderid = trim(I("orderid"));
        $field = "fcallbackurl, params, status, payflag";
        $where['orderid'] = ':orderid';
        $bind[':orderid']  = array($orderid,\PDO::PARAM_STR);
        $cpinfo = M($this->dbname.'.paycpinfo', C('CDB_PREFIX'))->field($field)->where($where)->bind($bind)->select();
                
        //只有充值成功， 并且通知CP失败的订单才补单
        if ((1 == $cpinfo[0]['payflag']) && ((0 == $cpinfo[0]['status']) || (2 == $cpinfo[0]['status'])) ){
            if (0<$this->repair($orderid, $cpinfo[0]['fcallbackurl'], $cpinfo[0]['params'], $cpinfo[0]['status'])){
                $this->success('补单成功');
                exit;
            }else{
                $this->error('补单失败');
                exit;
            }
        }
        $this->error('不需要补单');
        exit;
    }
    
    function repair($orderid, $fcallbackurl, $params, $status){        
        if ($status == 0 || $status == 2) {
            $i = 0;
            while (1) {
                $cp_rs = $this->postLm($params);                
                if ($cp_rs['r'] > 0) {
                    $data['status'] = 1;
                    $data['update_time'] = time();                    
                    M($this->dbname.'.paycpinfo', C('CDB_PREFIX')) -> where('orderid='.$orderid) -> data($data)->save();                     
                    break;
                } else {
                    $i++;
                    sleep(2);
                }
                
                if ($i == 5) {
                    return 0;
                }
            }
        }
        
        return 1;
    }
    
    /**
     * 回调联盟数据
     * @date: 2015年10月15日下午7:40:20
     *
     * @param $postdata post数据
     * @return array
     * @since 1.0
     */
    function postLm($postdata) {
        $postdata = json_encode($postdata);
        $url =  C('LMWEBSITE'). "/index.php/AppInterface/Clientapi/clientapi";
        $cnt = 0;
        while (1) {
            $return_content = base64_decode($this->http_post_data($url, $postdata));
            parse_str($return_content, $rdata);
            if (0 < $rdata['r'] || 4 == $cnt) {
                break;
            }
            $cnt++;
        }
        return $rdata;
    }
    
    function _payway(){
        $cates=array(
                "0"=>"全部"
        );

		//$where =" status=0";
		$where =" 1 ";

        $payways = M(C('MNG_DB_NAME').".payway", C('LDB_PREFIX'))->where($where)->getField("payname,realname", true);      
        if($payways){
            $payways=$cates + $payways;
        }else{
            $payways=$cates;
        }
        $this->assign("payways",$payways);
    }
    

    
    function _paystatus(){
        $cates=array(
                "0"=>"全部",
                "1"=>"待支付",
                "2"=>"支付成功",                
                "4"=>"回调失败",
                "3"=>"支付失败",
        );
        $this->assign("paystatuss",$cates);
    }


	/**
     * 导出Excel
     */
    function expPay($where){//导出Excel
        $xlsName  = "Pay";
        $xlsCell  = array(
			array('orderid','订单号'),
			array('username','账号'),
			array('gamename','游戏'),
			array('amount','金额'),
			array('paytype','充值方式'),
			array('agentname','渠道号'),
			array('agentnicename','渠道名称'),
			array('create_time','充值时间'),
			array('status','状态'),
        );
        
		$field = "a.orderid, a.amount, e.username, a.roleid, a.paytype, a.productname, a.serverid,a.status,c.status cptatus, FROM_UNIXTIME(a.create_time,'%Y-%m-%d %H%i%s') as create_time, regagent";
        $field .= " ,d.agentname, d.agentnicename agentnicename, g.gamename,d.agentid";
        $xlsData = M($this->dbname.'.pay', C('CDB_PREFIX'))
        ->alias("a")
        ->field($field)
        ->where($where)
        ->join("left join $this->dbname." . C('CDB_PREFIX') . "members e ON a.userid = e.id")
        ->join("left join $this->dbname." . C('CDB_PREFIX') . "paycpinfo c ON a.orderid = c.orderid")
        ->join("left join $this->dbname." . C('CDB_PREFIX') . "agentlist d ON a.regagent = d.agentgame")
	    ->join("left join $this->dbname." . C('LDB_PREFIX') . "game g ON a.appid = g.id")
        ->order("a.id DESC")
        ->select();   
		
        foreach ($xlsData as $k => $v)
        {
            $xlsData[$k]['status']=$v['status']==1?'成功':'失败';
        }
        $this->exportExcel($xlsName,$xlsCell,$xlsData);
         
    }
    
    /**
     * 导出充值exl
     * */
    function exlpaydata($where){
        $xlsName = "PayData";
        
        $paymodel = $this->daypaymodel;
        $find = "sum(`reg_cnt`) `reg_cnt`,sum(`usercnt`) `usercnt`,sum(`paycnt`) `paycnt`,sum(`summoney`) `summoney`,sum(`sumregmoney`) `sumregmoney`,`date`";
        
        
        $sql = "select sum(`reg_cnt`) `reg_cnt`,sum(`usercnt`) `usercnt`,sum(`paycnt`) `paycnt`,sum(`summoney`) `summoney`,sum(`sumregmoney`) `sumregmoney`,'今日汇总' as `date` 
                from ".$this->dbname.".".C('CDB_PREFIX')."tagentgamepayview 
                where ".$where."
                union
                select ".$find." from ".$this->dbname.".".C('CDB_PREFIX')."dayagentgame 
                where ".$where." group by date order by date desc";
       
        $data = M()->query($sql);
        
        
        $total_regcnt = 0;
        $total_usercnt = 0;
        $total_paycnt = 0;
        $total_sumregmoney = 0;
        $total_summoney = 0;
        
        
        foreach($data as $k=>$v){
            $data[$k]['reg_cnt'] = number_format($v['reg_cnt']);
            $data[$k]['usercnt'] = number_format($v['usercnt']);
            $data[$k]['paycnt'] = number_format($v['paycnt']);
            $data[$k]['sumregmoney'] = number_format($v['sumregmoney']);
            $data[$k]['summoney'] = number_format($v['summoney']);
            $data[$k]['summoneyrate'] = number_format(($v['paycnt']/$v['usercnt']*100),2)."%";
            $data[$k]['reg_cntARPU'] = number_format(($v['sumregmoney']/$v['reg_cnt']),2);
            $data[$k]['usercntARPU'] = number_format(($v['summoney']/$v['usercnt']),2);
            $data[$k]['paycntARPU'] = number_format(($v['summoney']/$v['paycnt']),2);
            
            
            
            $total_regcnt += $v['reg_cnt'];
            $total_usercnt += $v['usercnt'];
            $total_paycnt += $v['paycnt'];
            $total_sumregmoney += $v['sumregmoney'];
            $total_summoney += $v['summoney'];
            
        }
        
        $totalData = array(
            'date'=>'总汇',
            'reg_cnt'=>number_format($total_regcnt),
            'usercnt'=>number_format($total_usercnt),
            'paycnt'=>number_format($total_paycnt),
            'sumregmoney'=>number_format($total_sumregmoney),
            'summoney'=>number_format($total_summoney),
            'summoneyrate'=>number_format(($total_paycnt/$total_usercnt*100),2)."%",
            'reg_cntARPU'=>number_format(($total_sumregmoney/$total_regcnt),2),
            'usercntARPU'=>number_format(($total_summoney/$total_usercnt),2),
            'paycntARPU'=>number_format(($total_summoney/$total_paycnt),2)
        );
        array_unshift($data,$totalData);
        
        $exlscell = array(
            array('date','日期'),
            array('reg_cnt','新增用户数'),
            array('usercnt','活跃用户数'),
            array('paycnt','付费用户数'),
            array('sumregmoney','新用户付费金额'),
            array('summoney','总付费金额'),
            array('summoneyrate','总付费率'),
            array('reg_cntARPU','注册APRU'),
            array('usercntARPU','活跃ARPU'),
            array('paycntARPU','付费ARPU')
        );
        $this->exportExcel($xlsName,$exlscell,$data);
    }
    
    /**
     * 导出游戏exl
     * */
    function gamedata($wheres){
        $xlsName = "GameData";
        
//        $sumfield = "'汇总' as date,appid,SUM(`usercnt`) `usercnt`,SUM(`summoney`) `summoney`,SUM(`paycnt`) `paycnt`,SUM(`regpaycnt`) `regpaycnt`,SUM(`sumregmoney`) `sumregmoney`,SUM(`reg_cnt`) `reg_cnt`";
       
        
        
//         $sql = "select ".$sumfield." FROM ".$this->dbname.".".C('CDB_PREFIX')."dayagentgame where ".$wheres."
//                 UNION
//                 select * from (SELECT date,appid,SUM(usercnt) usercnt,SUM(summoney) summoney,SUM(paycnt) paycnt,SUM(regpaycnt) regpaycnt,SUM(sumregmoney) sumregmoney,SUM(reg_cnt) reg_cnt
//                 from ".$this->dbname.".".C('CDB_PREFIX')."dayagentgame
//                 where ".$wheres."
//                 GROUP BY date,appid
//                 UNION
//                 select date,appid,SUM(usercnt) usercnt,SUM(summoney) summoney,SUM(paycnt) paycnt,SUM(regpaycnt) regpaycnt,SUM(sumregmoney) sumregmoney,SUM(reg_cnt) reg_cnt
//                 from ".$this->dbname.".".C('CDB_PREFIX')."tagentgamepayview
//                 where ".$wheres."
//                 GROUP BY date,appid
//                 )a ORDER BY date desc ";
        
        
        $sql = "select * from (SELECT date,appid,SUM(usercnt) usercnt,SUM(summoney) summoney,SUM(paycnt) paycnt,SUM(regpaycnt) regpaycnt,SUM(sumregmoney) sumregmoney,SUM(reg_cnt) reg_cnt
                    from ".$this->dbname.".".C('CDB_PREFIX')."dayagentgame
                    where ".$wheres."
                    GROUP BY date,appid
                    UNION
                    select date,appid,SUM(usercnt) usercnt,SUM(summoney) summoney,SUM(paycnt) paycnt,SUM(regpaycnt) regpaycnt,SUM(sumregmoney) sumregmoney,SUM(reg_cnt) reg_cnt
                    from ".$this->dbname.".".C('CDB_PREFIX')."tagentgamepayview
                    where ".$wheres."
                    GROUP BY date,appid )a ORDER BY a.date desc";
        
        
        $data = M()->query($sql);
        
        $cates=array(
    	        0=>"全部游戏"
    	);
	    $where['ratestatus'] = 3;
        $games = M($this->dbname.".game", C('LDB_PREFIX'))->where($where)->getField("id,gamename", true);
        if($games){
            $cates = $cates + $games;
        }
        
        
        
        $exlscell = array(
            array('date','日期'),
            array('game','游戏'),
            array('reg_cnt','新增用户数'),
            array('usercnt','活跃用户数'),
            array('paycnt','付费用户数'),
            array('sumregmoney','新用户付费金额'),
            array('summoney','总付费金额'),
            array('summoneyrate','总付费率'),
            array('reg_cntARPU','注册APRU'),
            array('usercntARPU','活跃ARPU'),
            array('paycntARPU','付费ARPU')
        );
        
        $total_regcnt = 0;
        $total_usercnt = 0;
        $total_paycnt = 0;
        $total_sumregmoney = 0;
        $total_summoney = 0;
        
        foreach($data as $k=>$v){
            
            $data[$k]['game'] = $cates[$v['appid']];
            $data[$k]['reg_cnt'] = number_format($v['reg_cnt']);
            $data[$k]['usercnt'] = number_format($v['usercnt']);
            $data[$k]['paycnt'] = number_format($v['paycnt']);
            $data[$k]['sumregmoney'] = number_format($v['sumregmoney']);
            $data[$k]['summoney'] = number_format($v['summoney']);
            $data[$k]['summoneyrate'] = number_format(($v['paycnt']/$v['usercnt']*100),2)."%";
            $data[$k]['reg_cntARPU'] = number_format(($v['sumregmoney']/$v['reg_cnt']),2);
            $data[$k]['usercntARPU'] = number_format(($v['summoney']/$v['usercnt']),2);
            $data[$k]['paycntARPU'] = number_format(($v['summoney']/$v['paycnt']),2);
            
            
            $total_regcnt += $v['reg_cnt'];
            $total_usercnt += $v['usercnt'];
            $total_paycnt += $v['paycnt'];
            $total_sumregmoney += $v['sumregmoney'];
            $total_summoney += $v['summoney'];
        }
        $totalData = array(
            'date'=>'总汇',
            'game'=>'--',
            'reg_cnt'=>number_format($total_regcnt),
            'usercnt'=>number_format($total_usercnt),
            'paycnt'=>number_format($total_paycnt),
            'sumregmoney'=>number_format($total_sumregmoney),
            'summoney'=>number_format($total_summoney),
            'summoneyrate'=>number_format(($total_paycnt/$total_usercnt*100),2)."%",
            'reg_cntARPU'=>number_format(($total_sumregmoney/$total_regcnt),2),
            'usercntARPU'=>number_format(($total_summoney/$total_usercnt),2),
            'paycntARPU'=>number_format(($total_summoney/$total_paycnt),2)
        );
        
        array_unshift($data,$totalData);
        
        $this->exportExcel($xlsName,$exlscell,$data);
    }
    
    function test_paydata(){
        $stime = $_REQUEST['start_time'];
        $etime = $_REQUEST['end_time'];
        $date_time = $_POST['date_time'];
        $searchwhere = ' and 1=1';
        $where = $this->where;
        if($date_time == '今日'){
            $stime = date("Y-m-d");
            $etime = date("Y-m-d");
            $count = 1;
        }else if($date_time == '七日'){
            $stime = date("Y-m-d",strtotime("-6 day"));
            $etime = date("Y-m-d");
            $count = 7;
        }else if($date_time == '当月'){
            $stime = date("Y-m-01");
            $etime = date("Y-m-d");
            $count = date("d");
        }else if($date_time == '30天'){
            $stime = date("Y-m-d",strtotime("-29 day"));
            $etime = date("Y-m-d");
            $count = 30;
        }
        
        if(IS_POST){
            $searchwhere = " and date>='$stime' and date<='$etime'";
            $where .= $searchwhere;
            $_GET['start_time'] = $stime;
            $_GET['end_time'] = $etime;
        }else{
            if(!empty($stime)){
                $searchwhere .= " and date>='$stime'";
            }
            if(!empty($etime)){
                $searchwhere .= " and date<='$etime'";
            }
           // $searchwhere = " and date >='$stime' and date <= '$etime'";
            $where .= $searchwhere;
            $_GET['start_time'] = $stime;
            $_GET['end_time'] = $etime;
        }
        
        $paymodel = $this->daypaymodel;
        $find = "sum(`reg_cnt`) `reg_cnt`,sum(`usercnt`) `usercnt`,sum(`paycnt`) `paycnt`,sum(`summoney`) `summoney`,sum(`sumregmoney`) `sumregmoney`,`date`";
        
        $item = $paymodel->field($find)->where($where)->order("date DESC")->group("date")->select();
        $count = isset($count) ? $count : count($item) ;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        
        $page = $this->page($count, $rows);
        
        $data = $paymodel->field($find)->where($where)->order("date DESC")->group("date")->limit($page->firstRow . ',' . $page->listRows)->select();
        
        $sumdata = $paymodel->field($find)->where($where)->select();
        
        $daymodel = M($this->dbname.'.tagentgamepayview', C('CDB_PREFIX'));
        $daydata = $daymodel->field($find)->where($where)->select();
        
//         echo $where."<br />";
//         print_r($_POST);
        $this->assign("pays", $data);
        $this->assign("totalpays", $sumdata);
        $this->assign('daysum',$daydata);
        $this->assign("Page", $page->show('Admin'));
		$this->assign("formget", $_GET);
        $this->assign("current_page", $page->GetCurrentPage());
        $this->display();
    }
    
    

   
}