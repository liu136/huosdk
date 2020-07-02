<?php
namespace Common\Model;
use Common\Model\CommonModel;
class AdminoperatelogModel extends CommonModel{
    protected $trueTableName = 'l_adminoperatelog';
    protected $dbName = 'db_league_mng';
    
    /**
     +----------------------------------------------------------
     * 保存数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param  array $data 数组数据 
     +----------------------------------------------------------
     * @return true/false
     +----------------------------------------------------------
     */
    public function saveData($data, $where=NULL){
    	$w = '1 ';
    	if(!empty($where)){
    		$w .= ' AND '.$where;
    	}
    	$rs = $this -> where($w) ->save($data);
    	 
    	return $rs;
    }    
}
?>