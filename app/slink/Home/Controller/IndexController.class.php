<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        //sleep(3);
        $para = base64_decode(I('i'));
        $para_arr = explode(',', $para);
        $id = $para_arr[0];

		if(empty($id)){
            exit;
        }
		
           $this_dbname = "db_sdk_2";
           $model = M($this_dbname.'.agentlist',C('CDB_PREFIX'));
           $field = "a.*,ge.ghid,ge.gid,g.initial";
           $agentgames = $model
                   ->alias("a")
                   ->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game g ON g.id = a.appid")
                   ->join("left join ".C('MNG_DB_NAME').".".C('LDB_PREFIX')."game_ext ge ON ge.gid = g.id")
                   ->field($field)
                   ->where("a.id = ".$id)
                   ->find();
        
            $downurl  = DAMAIDOWNSITE."/".C('GAMEDIR')."/";
            $downurl .= $agentgames['gid']."_".$agentgames['initial']."/".$agentgames['ghid']."/".$agentgames['filename'];
        
            echo "<script>window.location.href='".$downurl."'</script>";
            exit;
    
    }
}