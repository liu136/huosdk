<?php
/**
 * DB - A simple database class
 * 
 * @author Author: Vivek Wicky Aswal. (https://twitter.com/#!/VivekWickyAswal) @git https://github.com/indieteq/PHP-MySQL-PDO-Database-Class
 * @version 0.2ab
 */
require ("Switchlog.class.php");
require ("Response.class.php");

class DB {
    // @object, The PDO object
    private $pdo;
    
    // @object, PDO statement object
    private $sQuery;
    
    // @array, The database settings
    private $settings;
    
    // @bool , Connected to the database
    private $bConnected = false;
    
    // @object, Object for logging exceptions
    private $log;
    
    // @array, The parameters of the SQL query
    private $parameters;
    private $_dbConfig = array (
        'db_type' => DB_TYPE, // dbms
		'db_host' => DB_HOST, // 主机地址
		'db_user' => DB_USER, // 数据库用户
		'db_pwd' => DB_PWD, // 密码
		'db_name' => DB_DATABASE 
    ); // 数据库名
    
    /**
     * Default Constructor 1.
     * Instantiate Log class. 2. Connect to database. 3. Creates the parameter array.
     */
    public function __construct() {
        $this->log = new Switchlog();
		
        $this->Connect();
		
        $this->parameters = array ();
		
    }
    
    /**
     * This method makes connection to the database.
     * 1. Reads the database settings from a ini file. 2. Puts the ini content into the settings array. 3. Tries to connect to the database. 4. If connection failed, exception is displayed and a log file gets created.
     */
    private function Connect() {
        $dsn = $this->_dbConfig['db_type'] .
             ':dbname=' . $this->_dbConfig['db_name'] . ';host=' . $this->_dbConfig['db_host'] . '';
        try {
            // Read settings from INI file, set UTF8
            $this->pdo = new PDO(
                    $dsn, $this->_dbConfig['db_user'], $this->_dbConfig['db_pwd'], 
                    array (
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8" 
                    ));
            
            // We can now log any exceptions on Fatal error.
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Disable emulation of prepared statements, use REAL prepared statements instead.
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
            // Connection succeeded, set the boolean to true.
            $this->bConnected = true;
        } catch (PDOException $e) {
            // Write into log
            $this->ExceptionLog($e->getMessage());
            die();
        }
    }
    
    /*
     * You can use this little method if you want to close the PDO connection
     */
    public function CloseConnection() {
        // Set the PDO object to null to close the connection
        // http://www.php.net/manual/en/pdo.connections.php
        $this->pdo = null;
    }
    
    /**
     * Every method which needs to execute a SQL query uses this method.
     * 1. If not connected, connect to the database. 2. Prepare Query. 3. Parameterize Query. 4. Execute Query. 5. On exception : Write Exception into the log + SQL query. 6. Reset the Parameters.
     */
    private function Init($query, $parameters = "") {
        // Connect to database
        if (!$this->bConnected) {
            $this->Connect();
        }
        
        try {
            // Prepare query
            $this->sQuery = $this->pdo->prepare($query);

            // Add parameters to the parameter array
            $this->bindMore($parameters);
            
            // Bind parameters
            if (!empty($this->parameters)) {
                foreach ($this->parameters as $param) {
                    $parameters = explode("\x7F", $param);
                    $this->sQuery->bindParam($parameters[0], $parameters[1]);
                }
            }
            // Execute SQL
            $this->succes = $this->sQuery->execute();
            
        } catch (PDOException $e) {
            // Write into log and display Exception
            $this->ExceptionLog($e->getMessage(), $query);
            die();
        }
        
        // Reset the parameters
        $this->parameters = array ();
    }
    
    /**
     * 检查字符串是否是UTF8编码
     * 
     * @param string $string 字符串
     * @return Boolean
     */
    public function is_utf8($string) {
        return preg_match(
                '%^(?:
					         [\x09\x0A\x0D\x20-\x7E]            # ASCII
					       | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
					       |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
					       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
					       |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
					       |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
					       | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
					       |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
					    )*$%xs', 
                $string);
    }
    
    /**
     * @void Add the parameter to the parameter array
     * 
     * @param string $para
     * @param string $value
     */
    public function bind($para, $value) {
        if ($this->is_utf8($value)) {
            $str = $value;
        } else {
            $str = utf8_encode($value);
        }
        $this->parameters[sizeof($this->parameters)] = ":" . $para . "\x7F" . $str;
    }
    /**
     * @void Add more parameters to the parameter array
     * 
     * @param array $parray
     */
    public function bindMore($parray) {
        if (empty($this->parameters) && is_array($parray)) {
            $columns = array_keys($parray);
            foreach ($columns as $i => &$column) {
                $this->bind($column, $parray[$column]);
            }
        }
    }
    
    /**
     * If the SQL query contains a SELECT or SHOW statement it returns an array containing all of the result set row If the SQL statement is a DELETE, INSERT, or UPDATE statement it returns the number of affected rows
     * 
     * @param string $query
     * @param array $params
     * @param int $fetchmode
     * @return mixed
     */
    public function query($query, $params = null, $fetchmode = PDO::FETCH_ASSOC) {
        $query = trim($query);

        $this->Init($query, $params);

        $rawStatement = explode(" ", $query);
        
        // Which SQL statement is used
        $statement = strtolower($rawStatement[0]);
        
        if ($statement === 'select' || $statement === 'show') {
            return $this->sQuery->fetchAll($fetchmode);
        } elseif ($statement === 'insert' || $statement === 'update' || $statement === 'delete') {
            return $this->sQuery->rowCount();
        } else {
            return NULL;
        }
    }
    
    /**
     * Returns the last inserted id.
     * 
     * @return string
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Returns an array which represents a column from the result set
     * 
     * @param string $query
     * @param array $params
     * @return array
     */
    public function column($query, $params = null) {
        $this->Init($query, $params);
        $Columns = $this->sQuery->fetchAll(PDO::FETCH_NUM);
        
        $column = null;
        
        foreach ($Columns as $cells) {
            $column[] = $cells[0];
        }
        
        return $column;
    }
    /**
     * Returns an array which represents a row from the result set
     * 
     * @param string $query
     * @param array $params
     * @param int $fetchmode
     * @return array
     */
    public function row($query, $params = null, $fetchmode = PDO::FETCH_ASSOC) {
        $this->Init($query, $params);
        return $this->sQuery->fetch($fetchmode);
    }
    /**
     * Returns the value of one single field/column
     * 
     * @param string $query
     * @param array $params
     * @return string
     */
    public function single($query, $params = null) {
        $this->Init($query, $params);
        return $this->sQuery->fetchColumn();
    }
    /**
     * Writes the log and returns the exception
     * 
     * @param string $message
     * @param string $sql
     * @return string
     */
    private function ExceptionLog($message, $sql = "") {
        // $exception = 'Unhandled Exception. <br />';
        // $exception .= $message;
        // $exception .= "<br /> You can find the error back in the log.";
        if (!empty($sql)) {
            // Add the Raw SQL to the Log
            $message .= "\r\nRaw SQL : " . $sql;
        }
        // Write into log
        $this->log->write($message);
        
        // return $exception;
    }
     public function getDbtable($cidenty, $clientkey){
        $thisname = array();
        if(empty($cidenty) || empty($clientkey) ){
            return $thisname;
        }
        
        $sql = "select 2 as cid, 2 as dbname, paystatus,payway,logo ,float_img  from `".MNG_DB_NAME."`.`".LDB_PREFIX."client` where clientidentifier=:cidenty and clientkey=:clientkey";
        $this->bind("cidenty", $cidenty);
        $this->bind("clientkey", $clientkey);
        $data = $this->row($sql);
        
        if (!empty($data)){
            $data['dbname'] = 'db_sdk_' . $data['dbname'];
            return $data;
        }
        return $data;
    }
   

    public function getAppkey($appid){
        if (empty($appid) || 0 > $appid) {
            return NULL;
        }
		
        $sql = "select appkey from `".MNG_DB_NAME."`.`".LDB_PREFIX."game` where id=:appid ";
        $this->bind("appid", $appid);
        $appkey = $this->single($sql);
    
        return $appkey;
    }
    
    public function setUsername($thisname){
        $basenum = 10000;
        
        // 生成用户名
        $minsql = "select min(base) from `".$thisname."`.`c_members_base`";
        $min = $this->single($minsql);
		
        $cntsql = "select count(id) from `".$thisname."`.`c_members_base` where base=$min";
        $cnt = $this->single($cntsql) - 1;
        
        $limit = rand(0, $cnt);
		
        $upsql = "select id from `".$thisname."`.`c_members_base` where base=$min limit $limit,1";
        $uid = $this->single($upsql);
        
        $upsql = "UPDATE `".$thisname."`.`c_members_base` SET `base` = `base` + 1 WHERE `id` = $uid";
        $rs = $this->query($upsql);

        if(!empty($rs) && 0 < $rs){
            $username =  $basenum * $min + $limit;
        }
        
        return $username ;
    }
    
    public function getCpurl($appid, $cid = 0){
        if (!isset($appid) || 1 > $appid) {
            return NULL;
        }
        if(is_numeric($cid)){
            $sql = "select cpurl from `".MNG_DB_NAME."`.`".LDB_PREFIX."gamecpurl` where gid=:appid AND (cid=0 OR cid = ".$cid.") ORDER BY cid DESC";
            $this->bind("appid", $appid);
            $cpurl = $this->single($sql);
        }        
        return $cpurl;
    }
    
    /*
     * 校验id与clientidentifier
     */
    public function verifyIdenty($str){
        $ckey = Response::decrypt($str);
        $arr = explode('&', $ckey);
        $carr = explode('_', $arr[0]);
        $sign = $arr[1];
        $cid = $carr[0] - 61803;
        $cidenty = $carr[1];
        if (empty($cid) || empty($cidenty) || empty($sign)) {
            return $rdata;
        }
        if(is_numeric($cid) && $cid > 0){
            $rdata = array();
            $sql = "select clientidentifier, paystatus, clientkey, db_name dbname from `".MNG_DB_NAME."`.`".LDB_PREFIX."client` WHERE id=:cid ";
            $this->bind("cid", $cid);
            $data = $this->row($sql);
            if($data['clientidentifier'] !== $cidenty){
                return $rdata;
            }
            
            $verysign = md5('###'.$carr[0].'_'.$carr[1].'_'.$data['clientkey']);
            if ($verysign === $sign) {
                //
                if (0 != $data['paystatus']) {
                    $rdata['scid'] = 1;
                    $rdata['identy'] = 'daoxian';
                }else{                    
                    $rdata['scid'] = $cid;
                    $rdata['identy'] = $data['clientidentifier'];
                }
                $rdata['cid'] = $cid;
                $rdata['dbname'] = $data['dbname'];
                return $rdata;
            }
            return $rdata;
        }
        return $rdata;
    }
    
    /*
     * 
     */
    public function verifyIdentyTwo($str){
        $rdata = array();
        if (empty($str)){
            return $rdata;
        }
        $ckey = Response::decode($str,Response::fixedArr());
		
        $arr = explode('_', $ckey);
		
        $cid = $arr[0] - 61803;
        $cidenty = $arr[1];
        if (empty($cid) || empty($cidenty)) {
            return $rdata;
        }
        if(is_numeric($cid) && $cid > 0){
            $rdata = array();
            $sql = "select clientidentifier, paystatus, db_name dbname from `".MNG_DB_NAME."`.`".LDB_PREFIX."client` WHERE id=:cid ";
            $this->bind("cid", $cid);
            $data = $this->row($sql);
            if($data['clientidentifier'] !== $cidenty){
                return $rdata;
            }
            
            //
            if (0 != $data['paystatus']) {
                $rdata['scid'] = 1;
                $rdata['identy'] = 'daoxian';
            }else{
                $rdata['scid'] = $cid;
                $rdata['identy'] = $data['clientidentifier'];
            }
            $rdata['cid'] = $cid;
            $rdata['dbname'] = 'db_sdk_' . $data['dbname'];
            return $rdata;
        }
        return $rdata;
    }
    
    public function veryfyAlipay($str){
        $rdata = $this->verifyIdentyTwo($str);
        if (empty($rdata)) {
            return $rdata;
        }
        
        $sql = "select partner from `".MNG_DB_NAME."`.`".LDB_PREFIX."alipay` WHERE cid=:cid ";
        $this->bind("cid", $rdata['scid']);
        $rdata['partner'] = $this->single($sql);
        if(empty($rdata['partner'])){
            return array();
        }
        return $rdata;
    }

	/*
     * 渠道信息
     */
    public function clientinfo($cid){
      
        if (empty($cid)) {
            return $rdata;
        }
        if(is_numeric($cid) && $cid > 0){
            $rdata = array();
            $sql = "select clientidentifier, clientkey, db_name dbname from `".MNG_DB_NAME."`.`".LDB_PREFIX."client` WHERE id=:cid AND `status` = 2";
            $this->bind("cid", $cid);
            $data = $this->row($sql);
          
            $rdata['cid'] = $cid;
            $rdata['dbname'] = 'db_sdk_' . $data['dbname'];
            return $rdata;
        }
        return $rdata;
    }

	/*
     * 默认渠道信息
     */
    public function defaultClient(){
        $cid = 1;
        if (empty($cid)) {
            return $rdata;
        }
        if(is_numeric($cid) && $cid > 0){
            $rdata = array();
            $sql = "select clientidentifier, clientkey, db_name dbname, payway from `".MNG_DB_NAME."`.`".LDB_PREFIX."client` WHERE id=:cid AND `status` = 2";
            $this->bind("cid", $cid);
            $data = $this->row($sql);
          
            $rdata['cid'] = $cid;
            $rdata['dbname'] = 'db_sdk_' . $data['dbname'];
			$rdata['payway'] = $data['payway'];
            return $rdata;
        }
        return $rdata;
    }
	/**
	 **获取类型
	 **/
	 public function getPtbtype($cid){
        if (!isset($cid) || 1 > $cid) {
            return null;
        }
		
		if($cid == 1){
			return 2;
		}else{
			return 1;
		}
       
    }
	
	//支付宝信息
	public function getAlipay($cid){
        $sql = "select email,partner,rsa_private_key ,ios_rsa_private_key from `".MNG_DB_NAME."`.`".LDB_PREFIX."alipay` WHERE cid=:cid ";
        $this->bind("cid", $cid);
        $data = $this->row($sql);

        return $data;
    }

    public function getPayway($id){
        $sql = "select payname as a, disc as b, disc as c from `".MNG_DB_NAME."`.`".LDB_PREFIX."payway` WHERE id=:id";
        $this->bind("id", $id);
		
        $data = $this->row($sql);
		return $data;
    }
    /*
     * 插入分渠道数据函数
     */
    private function insertClpay($dbname, $arr){
        $paysql = "INSERT INTO `".$dbname."`.`c_pay`";
        $paysql .= " (`cid`,`orderid`,`amount`,`userid`,`roleid`,`paytype`,`productname`,`agentgame`,`serverid`,`appid`,`status`,`ip`,`imei`,`create_time`,`remark`,`regagent`,`product_id`,`isbox`)";
        $paysql .= " VALUES";
        $paysql .= " (:cid, :orderid,:amount,:userid,:roleid,:paytype,:productname,:agentgame,:serverid,:appid,:status,:ip,:imei,:create_time,:remark,:regagent,:product_id,:isbox)";
		
        return $this->query($paysql, $arr);
    }
    
    /*
     * 插入分渠道paycpinfo数据
     */
    private function insertClpaycp($dbname, $paycpdata){
        $sql_info = "  INSERT INTO `".$dbname."`.`c_paycpinfo`";
        $sql_info .= " ( `cid`,`orderid`, `fcallbackurl`, `params`, `create_time`, `update_time`)";
        $sql_info .= " VALUES";
        $sql_info .= " (:cid,:orderid, :cpurl, :params, :create_time, :update_time)";
        return $this->query($sql_info, $paycpdata);
    }
    
	
    /*
     * 支付创建函数
     */
    public function doPay($dbname, $cpurl, $arr){
        if(!empty($arr) && is_array($arr) && !empty($dbname)){
            if (!(isset($arr['orderid']) && isset($arr['amount']) && isset($arr['userid']) && isset($arr['roleid']) &&
                  isset($arr['paytype']) && isset($arr['productname']) && isset($arr['agentgame']) &&
                  isset($arr['serverid']) && isset($arr['appid']) && isset($arr['status']) && isset($arr['ip']) &&
                  isset($arr['imei']) && isset($arr['create_time']) && isset($arr['remark']) && isset($arr['regagent']) &&
                  isset($arr['cid']) && isset($arr['lm_username']) && isset($arr['switchflag'])) || empty($dbname) || empty($cpurl)) {
                $this->CloseConnection();
                Response::show("-2", array(), "参数错误");
                exit;
            }
			
            //总数据插入
             if (true) {
                $paydata = $arr;
				
				if(empty($arr['product_id'])){
					$paydata['product_id'] = '';
				}
				if(empty($arr['isbox'])){
					$paydata['isbox'] = 0;
				}
				
                unset($paydata['lm_username']);
                unset($paydata['switchflag']);
                
                //插入分渠道pay数据
                $this->insertClpay($dbname, $paydata);
                unset($paydata);
                
                $cpstr = "orderid=".urlencode($arr['orderid'])."&username=".urlencode($arr['lm_username'])."&appid=".urlencode($arr['appid'])."&roleid=".urlencode($arr['roleid']);
                $cpstr .= "&serverid=".urlencode($arr['serverid'])."&amount=".urlencode($arr['amount'])."&paytime=".urlencode($arr['create_time'])."&attach=".urlencode($arr['remark'])."&productname=".urlencode($arr['productname']);
                $appkey = $this->getAppkey($arr['appid']);
                
                $param = $cpstr."&appkey=".urlencode($appkey);
                $md5params = md5($param);
                $params = $cpstr . "&sign=".urlencode($md5params);
                
                $paycpdata['orderid'] = $arr['orderid'];
                $paycpdata['params'] = $params;
                $paycpdata['cpurl'] = $cpurl;
                $paycpdata['create_time'] = $arr['create_time'];
                $paycpdata['update_time'] = $arr['create_time'];
                $paycpdata['cid'] = $arr['cid'];
                
                //插入分渠道paycpinfo数据库
                $this->insertClpaycp($dbname, $paycpdata);
                $paycpdata['switchflag'] = $arr['switchflag'];
                unset($paycpdata);
                
                return TRUE;
             }else{
                Response::show("-2", array(), "L订单创建失败");
                exit;
			 }
        }
    }
	
	
    public function doPaynotify($orderid, $amount, $dbname, $paymark='default') {
		
        if(empty($orderid) || empty($amount) || empty($dbname)){
            return FALSE;
        }
        $trade['orderid'] = $orderid;
        $trade['paymark'] = $paymark;
        $time = time();
        $check_sql = "select amount,`status` from ".$dbname.".c_pay where orderid= :orderid";
        $this->bind("orderid",$orderid);
        $check_data = $this->row($check_sql);
        
        // 验证订单数额的一致性
        if (true) {
            $sql = "UPDATE ".$dbname.".c_pay SET status=1, paymark=:paymark WHERE orderid=:orderid";
            $rs = $this->query($sql, $trade);
             
            if ($rs) {
                //更新联盟数据
                $sql_info = "select fcallbackurl,params,status from ".$dbname.".c_paycpinfo where orderid=:orderid";
                $this->bind("orderid", $orderid);
                $paycpdata = $this->row($sql_info);
                $fcallbackurl = $paycpdata['fcallbackurl'];
                $params = $paycpdata['params'];
                $status = $paycpdata['status'];
				
                if ($status == 0 || $status == 2) {
                    $i = 0;
                    while (1) {
                        $cp_rs = Response::payback($fcallbackurl, $params, 'post');
                        if ($cp_rs > 0) {
                            $status = 1;
                            break;
                        }else{
                            $status = 2;
                            $i ++;
                            sleep(2);
                        }
        
                        if ($i == 3) {
                            $status = 2;
                            break;
                        }
                    }
                }
                //更新CP状态
                unset($trade['paymark']);
                $cpsql = "UPDATE ".$dbname.".c_paycpinfo SET `status`=".$status.", update_time=".$time." WHERE orderid=:orderid";
                $this->query($cpsql, $trade);
       
            }
        }
        return TRUE;
    }
	
	public function doSyReg($regurl, $params) {
		 
         $data = Response::syback($regurl, $params, 'post');
         
		 //$data = json_decode($cp_rs,true);

		 return $data;
         
	}

	public function doSyLogin($regurl, $params) {
		 
         $data = Response::syback($regurl, $params, 'post');
         
		 //$data = json_decode($cp_rs,true);

		 return $data;
         
	}
}
?>
