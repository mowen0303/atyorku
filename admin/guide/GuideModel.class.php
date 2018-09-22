<?php

namespace admin\guide;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class GuideModel extends Model
{
    private static $type_enum = ['original','reproduced','video'];
    private static $videoVendor_enum = ['','youtube','tencent'];

    public function getListOfGuideClass($pageSize = 20)
    {
        $table = 'guide_class';
        $sql = "SELECT * FROM {$table} where is_del=0 order by guide_class_order";
        $countSql = null;
        return parent::getListWithPage($table, $sql, $countSql, $pageSize);   //-- 注意 --//
    }

    /**
     * @param bool $hideAll
     * @param bool $hideIds     1,2,3,4 或 1
     * @param int $pageSize
     */
    public function getListOfGuideClassVisible($hideAll=false,$hideIds=false,$pageSize = 40)
    {
        $condition="";
        if($hideIds){
            $condition.=" AND id not in ({$hideIds}) ";
        }
        $table = 'guide_class';
        $sql = "SELECT * FROM {$table} where is_del=0 AND visible=0 {$condition} ORDER BY guide_class_order";
        //die($sql);
        $countSql = "SELECT count(*) FROM {$table} where is_del=0 AND visible=0 {$condition} ORDER BY guide_class_order";
        $result = parent::getListWithPage($table, $sql, $countSql, $pageSize);   //-- 注意 --//
        if(!$hideAll){
            $arr = [];
            $arr['id'] = "0";
            $arr['title'] = "所有文章";
            $arr['is_del'] = "0";
            $arr['visible'] = "0";
            $arr['icon'] = "/admin/resource/img/icon/guideIcon/all8.png";
            $arr['guide_class_order'] = "1";
            $arr['description'] = "发过的所有文章都在这里了";
            $amount = 0;
            foreach($result as $row){
                foreach($row as $k => $v){
                    if($k == 'amount'){
                        $amount += $v;
                    }
                }
            }
            $arr['amount'] = "{$amount}";
            array_unshift($result,$arr);
        }
        return $result;
    }

    public function getRowOfGuideClassById($id)
    {
        // id|title|is_del|description|
        $sql = "SELECT g_c.* FROM `guide_class` AS g_c WHERE g_c.id in ({$id})";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
     * @param $guide_classId 0显示全部
     * @param int $pageSize
     * @return array
     * --------
     * id
     * title
     * time
     * user_id
     * view_no
     * cover
     * introduction
     * classTitle
     * imgOfUserHead
     * alias
     * --------
     */
    public function getListOfGuideByGuideClassId($guide_classId, $pageSize = 20, $showVisible = true, $showNotValid = false)
    {

        $statisticsModel = new StatisticsModel();
        $statisticsModel->countStatistics(2);
        $currentUser = new UserModel();
        $currentUser->addActivity();

        $table = 'guide';
        $condition = $guide_classId == 0 ? "" : "AND gc.id IN ({$guide_classId}) "; //0显示全部
        $condition .= $showVisible == true ? "AND gc.visible = 0 " : "";
        //$condition .= $showNotValid ? "AND g.valid = 0" : "";
        $order = $guide_classId == 0 ? "" : "g.guide_class_order DESC,";


        $sql = "SELECT g.id,title,time,user_id,view_no,cover,introduction,classTitle,guide_order,reproduced_source_url,reproduced_source_title,type,video_vendor,video_source_url, u.img AS imgOfUserHead,alias FROM (SELECT g.*, gc.title AS classTitle FROM guide AS g INNER JOIN guide_class AS gc ON g.guide_class_id = gc.id WHERE g.is_del = 0 {$condition}) AS g INNER JOIN user AS u ON g.user_id = u.id ORDER BY {$order} g.guide_order DESC ,g.time DESC";

        $countSql = "SELECT count(*) FROM (SELECT g.*, gc.title AS classTitle FROM guide AS g INNER JOIN guide_class AS gc ON g.guide_class_id = gc.id WHERE g.is_del = 0 {$condition}) AS g INNER JOIN user AS u ON g.user_id = u.id ORDER BY {$order} g.guide_order,time";
        $result = parent::getListWithPage($table, $sql, $countSql, $pageSize);

        $id = "";
        $idIndex = 0;

        foreach ($result as $k1 => $v1) {
            $result[$k1]["time"] = BasicTool::translateTime($result[$k1]["time"]);
            if( $result[$k1]["is_reproduced"]==1){
                $result[$k1]["alias"] = $result[$k1]["source_title"];
            }
            if ($idIndex < 3) {
                $id .= ($result[$k1]["id"] . ",");
                $idIndex++;
            }
        }

        //增加阅读量
        if ($id != "") {
            if ($_COOKIE["guideViewTime"] == null) {
                $id = substr($id, 0, -1);
                $this->countViewOfGuideById($id);
                setcookie("guideViewTime", time(), time() + 600, '/');
            }
        }

        return $result;
    }

    /**
     * @param $guideClassID
     * @param $userID
     * @param $title
     * @param $content
     * @param $introduction
     * @param $cover
     * @return bool|int
     */
    public function addGuide($guideClassID, $title, $content, $introduction, $userID, $cover, $order)
    {
        $arr['guide_class_id'] = $guideClassID; //14草稿箱
        $arr['user_id'] = $userID;
        $arr['title'] = $title ? $title : "";
        $arr['view_no'] = 0;
        $arr['content'] = $content ? $content : "";
        $arr['introduction'] = $introduction ? $introduction : "";
        $arr['guide_order'] = $order ? $order : 0;
        $arr['cover'] = $cover ? $cover : "";
        $arr['time'] = time();
        $arr['post_time'] = time();
        $arr['count_comments'] = 0;
        $arr['is_del'] = 0;
        $arr['type'] = 'original';
        $arr['reproduced_source_url'] = "";
        $arr['reproduced_source_title'] = "";
        $arr['video_source_url']="";
        $arr['video_vendor'] = '';
        if ($this->addRow('guide', $arr)) {
            return $this->idOfInsert;
        } else {
            $this->errorMsg = "新建文章出错";
            return false;
        }
    }

    public function updateGuide($guideID, $guideClassID, $title, $content, $introduction, $userID, $cover, $order, $classOrder, $type, $reproducedSourceTitle, $reproducedSourceUrl,$videoSourceUrl,$videoVendor)
    {
        $arr['guide_class_id'] = $guideClassID; //14草稿箱
        $arr['user_id'] = $userID;
        $arr['title'] = $title ?: "";
        $arr['content'] = $content ?: "";
        $arr['introduction'] = $introduction ?: "";
        $arr['guide_order'] = $order ?: 0;
        $arr['guide_class_order'] = $classOrder ?: 0;
        $arr['cover'] = $cover ? $cover : "";
        if (!in_array($type,self::$type_enum)){
            $this->errorMsg = "修改失败:type must equal to one of (original,reproduced,video)";
            return false;
        }
        $arr['type'] = $type;
        $arr["reproduced_source_title"] = $reproducedSourceTitle ?: "";
        $arr["reproduced_source_url"] = $reproducedSourceUrl ?: "";
        $arr["video_source_url"] = $videoSourceUrl?:"";
        if (!in_array($videoVendor,self::$videoVendor_enum)){
            $this->errorMsg = "修改失败:video_vender must equal to one of ('','youtube','tencent')";
            return false;
        }
        $arr['video_vendor'] = $videoVendor;
        $this->updateRowById('guide', $guideID, $arr);
        return true;
    }

    public function deleteGuideByIDs($id)
    {
        if(is_array($id)){
            foreach ($id as $currentID){
                BasicTool::delFile($_SERVER["DOCUMENT_ROOT"]."/uploads/guide2/{$currentID}",true);
            }
        }else{
            BasicTool::delFile($_SERVER["DOCUMENT_ROOT"]."/uploads/guide2/{$id}",true);
        }

        return $this->logicalDeleteByFieldIn('guide', 'id', $id);
    }

    public function countViewOfGuideById($guideId)
    {
        $sql = "UPDATE guide SET view_no = view_no+1 WHERE id in ($guideId)";
        $this->sqltool->query($sql);

    }

    public function getRowOfGuideById($id)
    {
        $statisticsModel = new StatisticsModel();
        $statisticsModel->countStatistics(2);
        $currentUser = new UserModel();
        $currentUser->addActivity();
        $sql = "SELECT G.*,GC.title AS category_title,GC.id as category_id,visible FROM (SELECT G.*,U.id AS uid,U.alias,img from `guide` as G LEFT JOIN `user` as U on G.user_id = U.id WHERE G.id in ({$id})) as G INNER JOIN guide_class AS GC ON G.guide_class_id = GC.id;";
        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }


    public function increaseCountNumber($id)
    {
        $sql = "UPDATE `guide` SET `view_no`=view_no+1 WHERE id in({$id})";
        return $this->sqltool->query($sql);
    }


    /**
     * 更新一分类下文章的总量
     * @param $classId
     * @return mixed
     */
    public function updateAmountOfArticleByClassId($classId)
    {
        $sql = "SELECT COUNT(*) AS amount FROM guide WHERE guide_class_id = {$classId} AND is_del = 0";
        $row = $this->sqltool->getRowBySql($sql);
        $amount = $row['amount'];

        $sql = "UPDATE guide_class SET amount = {$amount} WHERE id = $classId";
        return $this->sqltool->query($sql);
    }

    public function getClassIdByGuideId($guideId)
    {

        $sql = "SELECT guide_class_id FROM guide WHERE id = {$guideId}";
        $row = $this->sqltool->getRowBySql($sql);
        return $row['guide_class_id'];
    }


}


?>