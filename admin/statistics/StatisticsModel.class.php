<?php
namespace admin\statistics;   //-- 注意 --//
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class StatisticsModel extends Model
{

    /**
     *
     * @param $type 1论坛 2资讯 3课评
     * @return bool
     */
    public function countStatistics($type,$recursion=true){

//        $timeOfMidnight = strtotime(date("Y-m-d"));
        $date = date("Y-m-d",time());



        if($recursion ==true ){

            if($_COOKIE["visitTime"] == false){
                self::countStatistics(4,false);
                $time = strtotime($date)+86400;
                setcookie("visitTime", time(), $time,'/');
            }
        }

        $sql = "SELECT date FROM statistics WHERE date = '{$date}' AND type = '{$type}'";
        $this->sqltool->query($sql);
        if($this->sqltool->getAffectedRows()>0){
            //已存在
            $sql = "UPDATE statistics SET amount_view = amount_view + 1 WHERE date = '$date' AND type = '{$type}'";

        }else{
            $sql = "INSERT INTO `statistics`(`date`, `type`, `amount_view`) VALUES ('{$date}',$type,1)";
        }

        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows()>0){
            return true;
        }else{
            return false;
        }





    }


    public function getListOfStatic($pageSize = 31){
        $table = 'forum';
        $sql = "SELECT * FROM statistics ORDER BY id DESC";
        $countSql = "SELECT count(*) FROM statistics ORDER BY id DESC";
        return parent::getListWithPage($table,$sql,$countSql,$pageSize);
    }


    public function countVisitorToday(){

        $date = date("Y-m-d",time());
        if($_COOKIE['visitTime'] > time()){

        }



        $sql = "SELECT date FROM statistics_user WHERE date = '{$date}'";
        $this->sqltool->query($sql);
        if($this->sqltool->getAffectedRows()>0){
            //已存在
            $sql = "UPDATE statistics_user SET amount_visitor = amount_visitor + 1 WHERE date = '$date'";

        }else{
            $sql = "INSERT INTO `statistics_user`(`date`, `amount_visitor`) VALUES ('{$date}',1)";
        }

        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows()>0){
            return true;
        }else{
            return false;
        }

    }





}