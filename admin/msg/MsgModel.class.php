<?php
namespace admin\msg;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class MsgModel extends Model
{

    public function getListOfMsg(){
        $table = 'msg';
        $sql = "SELECT M.*,user.img,user.alias,user.gender FROM (SELECT * FROM {$table}) AS M INNER JOIN user ON sender_id = user.id ORDER BY id DESC";
        $countSql = "SELECT COUNT(*) FROM {$table} ORDER BY id DESC";
        $result =   parent::getListWithPage($table,$sql,$countSql,40);
        foreach($result as $k1 => $v1) {
            foreach($v1 as $k2 => $v2){
                if($k2=="time"){
                    $result[$k1][$k2] = BasicTool::translateTime($v2);
                }
            }
        }
        return $result;
    }

    public function getListOfAlert(){
        $table = 'msg';
        $sql = "SELECT M.*,user.img,user.alias,user.gender FROM (SELECT * FROM {$table} WHERE alert = 1) AS M INNER JOIN user ON sender_id = user.id ORDER BY id DESC";
        $countSql = "SELECT COUNT(*) FROM {$table} WHERE alert = 1 ORDER BY id DESC";
        $result =   parent::getListWithPage($table,$sql,$countSql,40);
        foreach($result as $k1 => $v1) {
            foreach($v1 as $k2 => $v2){
                if($k2=="time"){
                    $result[$k1][$k2] = BasicTool::translateTime($v2);
                }
            }
        }
        return $result;
    }


}


?>