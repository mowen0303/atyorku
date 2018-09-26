<?php
namespace admin\device;   //-- 注意 --//
use \Model as Model;

class DeviceModel extends Model {

    private static $table_name = "employee_kpi";
    private static $pk = "id";
    private static $c_time = "created_time";
    private static $lu_time = "last_updated_time";
    private static $main_uid = "main_user_id";
    private static $uids = "user_ids";
    private static $pageSize = 30;
    private static $count_accounts = "count_accounts";
    private static $count_guides = "count_guides";
    private static $count_forums = "count_forums";
    private static $count_comments = "count_comments";

    public static function convertArrayToString($arr){
        if (is_array($arr)){
            $concat = "";
            foreach ($arr as $item){
                $concat .= $item.",";
            }
            return $concat ? substr($concat, 0, -1) : "";
        }else{
            return false;
        }
    }

    public static function convertUIdsStringToArray($uid_string){
        if (!$uid_string)
            return [];
        $result = explode(",",$uid_string);
        $bool = true;
        foreach ($result as $item){
            $str = (string) $item;
            $bool = $bool && ctype_digit($str);
        }
        if ($bool)
            return $result;
        else
            return false;
    }
}
?>
