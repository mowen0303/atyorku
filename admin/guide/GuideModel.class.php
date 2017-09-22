<?php
namespace admin\guide;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class GuideModel extends Model
{
    public function getListOfGuideClass( $pageSize = 20){
        $table = 'guide_class';
        $sql = "SELECT * FROM {$table} where is_del=0 order by guide_class_order";
        $countSql = null;
        return parent::getListWithPage($table,$sql,$countSql, $pageSize);   //-- 注意 --//
    }

    public function getListOfGuideClassVisible( $pageSize = 20){
        $table = 'guide_class';
        $sql = "SELECT * FROM {$table} where is_del=0 AND visible=0 ORDER BY guide_class_order";
        $countSql = null;
        return parent::getListWithPage($table,$sql,$countSql, $pageSize);   //-- 注意 --//
    }

    public function getRowOfGuideClassById($id){
        // id|title|is_del|description|
        $sql = "SELECT g_c.* FROM `guide_class` AS g_c WHERE g_c.id in ({$id})";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
     * @param $guide_classId 0显示全部
     * @param int $pageSize
     * @return array
     * --------
        id
        title
        time
        user_id
        view_no
        cover
        introduction
        classTitle
        imgOfUserHead
        alias
     * --------
     */
    public function getListOfGuideByGuideClassId($guide_classId,$pageSize = 20,$showVisible = true, $showNotValid = false){

        $statisticsModel = new StatisticsModel();
        $statisticsModel->countStatistics(2);
        $currentUser = new UserModel();
        $currentUser->addActivity();

        $table = 'guide';
        $condition = $guide_classId == 0 ? "" : "AND gc.id IN ({$guide_classId}) "; //0显示全部
        $condition .= $showVisible == true?"AND gc.visible = 0 ":"";
        //$condition .= $showNotValid ? "AND g.valid = 0" : "";

        $sql ="SELECT g.id,title,time,user_id,view_no,cover,introduction,classTitle,guide_order,u.img AS imgOfUserHead,alias FROM (SELECT g.*, gc.title AS classTitle FROM guide AS g INNER JOIN guide_class AS gc ON g.guide_class_id = gc.id WHERE g.is_del = 0 {$condition}) AS g INNER JOIN user AS u ON g.user_id = u.id ORDER BY g.guide_order DESC ,g.time DESC";

        $countSql = "SELECT count(*) FROM (SELECT g.*, gc.title AS classTitle FROM guide AS g INNER JOIN guide_class AS gc ON g.guide_class_id = gc.id WHERE g.is_del = 0 {$condition}) AS g INNER JOIN user AS u ON g.user_id = u.id ORDER BY g.guide_order,time";
        $result = parent::getListWithPage($table,$sql,$countSql, $pageSize);

        $id = "";
        $idIndex = 0;

        foreach($result as $k1 => $v1) {
            foreach($v1 as $k2 => $v2){
                if($k2=="time"){
                    $result[$k1][$k2] = BasicTool::translateTime($v2);
                }
                if($k2 == "id"){

                    if($idIndex<5){
                        $id .= ($v2.",");
                        $idIndex++;
                    }

                }

            }
        }

        //增加阅读量
        if($id != ""){
            if($_COOKIE["guideViewTime"] == null){
                $id = substr($id,0,-1);
                $this->countViewOfGuideById($id);
                setcookie("guideViewTime", time(), time()+600,'/');
            }
        }

        return $result;
    }

    public function countViewOfGuideById($guideId){
        $sql = "UPDATE guide SET view_no = view_no+1 WHERE id in ($guideId)";
        $this->sqltool->query($sql);

    }

    public function getRowOfGuideById($id){
        $statisticsModel = new StatisticsModel();
        $statisticsModel->countStatistics(2);
        $currentUser = new UserModel();
        $currentUser->addActivity();

        $sql = " select G.*,U.id AS uid,U.alias,img from `guide` as G LEFT JOIN `user` as U on G.user_id = U.id WHERE G.id in ({$id});";
        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }


    public function increaseCountNumber ($id){
        $sql = "UPDATE `guide` SET `view_no`=view_no+1 WHERE id in({$id})";
        return $this->sqltool->query($sql);
    }



    /**
     * 更新一分类下文章的总量
     * @param $classId
     * @return mixed
     */
    public function updateAmountOfArticleByClassId($classId){
        $sql = "SELECT COUNT(*) AS amount FROM guide WHERE guide_class_id = {$classId} AND is_del = 0";
        $row = $this->sqltool->getRowBySql($sql);
        $amount = $row['amount'];

        $sql = "UPDATE guide_class SET amount = {$amount} WHERE id = $classId";
        return $this->sqltool->query($sql);
    }

    public function getClassIdByGuideId($guideId){

        $sql = "SELECT guide_class_id FROM guide WHERE id = {$guideId}";
        $row = $this->sqltool->getRowBySql($sql);
        return $row['guide_class_id'];
    }







}


?>