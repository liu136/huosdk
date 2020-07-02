<?php
namespace Common\Model;
use Common\Model\CommonModel;
class ClientuserscoinModel extends CommonModel{
    protected $trueTableName = 'l_clientusers';
    protected $dbName = 'db_sdk_1';
    
    //获取子渠道账号信息
    public function get_clientusers($where,$limit){
        $field = 'cu.id,cu.cid,cu.user_login,cu.user_nicename,cu.coin_status,cu.coin_balance,cu.coin_pwd,cu.level,if(isnull(ca.allot_count),0,ca.allot_count) as allot_count,cr.typeid';
        $info = $this->alias('cu')->field($field)
        ->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."client c on cu.cid = c.id and cu.parentid = 0")
        ->join("inner join ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole cr on cr.id = cu.role_id and cr.typeid > 2")
        ->join("left join (select cid,sum(count) as allot_count from ".C('DATA_DB_NAME').".".C('LDB_PREFIX')."coin_allot group by cid) ca on cu.id = ca.cid")
        ->where($where)
        ->limit($limit)
        ->select();
        return $info;
    }
    
    //获取子渠道账号信息数量
    public function get_clientusers_count($where){
        $info = $this->alias('cu')
        ->join("inner join ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole cr on cr.id = cu.role_id and cr.typeid > 2")
        ->where($where)
        ->count('cu.id');
        return $info;
    }
    
    //获取子渠道账号下拉信息
    public function clientusers_list($where){
        $info = $this->alias('cu')
        ->join("inner join ".C('AUTH_DB_NAME').".".C('LDB_PREFIX')."clientrole cr on cr.id = cu.role_id and cr.typeid > 2")
        ->where($where)
        ->getField('cu.id,cu.user_nicename');
        return $info;
    }
    
    /* public function is_setCoinPwd($adminid){
        $info = $this->where('id = '.$adminid)->getField('coin_pwd');
        return $info ? true : false;
    } */
    
    
}