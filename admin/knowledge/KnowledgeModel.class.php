<?php
namespace admin\knowledge;   //-- 注意 --//
use \Model as Model;
class KnowledgeModel extends Model
{
    /**添加一个考试回忆录
     * 图片版 $img_id>0  $knowledge_point_description = NULL, 文字版 $img_id=0 $knowledge_point_descript=String[]
     * 文字版 传进来的 $count_knowledge_points为0
     * @param int $seller_user_id
     * @param int $knowledge_category_id
     * @param int $img_id
     * @param int $course_code_id
     * @param int $prof_id
     * @param float $price
     * @param string $description
     * @param string[] | NULL $knowledge_point_description
     * @param int $count_knowledge_points
     * @param int $term_year
     * @param string $term_semester
     * @param int $sort
     * @return int|false $insert_id
     */
    function addKnowledge($seller_user_id,$knowledge_category_id,$img_id,$course_code_id,$prof_id,$price,$description,$knowledge_point_description, $count_knowledge_points,$term_year,$term_semester,$sort = 0){
        $arr = [];
        $arr["seller_user_id"] = $seller_user_id;
        $arr["knowledge_category_id"] = $knowledge_category_id;
        $arr["img_id"] = $img_id;
        $arr["course_code_id"] = $course_code_id;
        $arr["prof_id"] = $prof_id;
        $arr["price"] = $price;
        $arr["description"] = $description?$description:"";
        $arr["count_knowledge_points"] = $count_knowledge_points? $count_knowledge_points : count($knowledge_point_description);
        $arr["count_views"] = 0;
        $arr["count_sold"] = 0;
        $arr["publish_time"] = time();
        $arr["term_year"] = $term_year;
        $arr["term_semester"] = $term_semester?$term_semester:"";
        $arr["sort"] = $sort;
        $insert_id = $this->addRow("knowledge",$arr)?$this->getInsertId() : false;
        if ($insert_id){
            if (!$img_id){
                //文字版
                if (!$this->addKnowledgePoint($insert_id,$knowledge_point_description)){
                    $this->errorMsg = "添加考点失败";
                    //删除刚添加的考试回忆录
                    $this->deleteKnowledgeById($insert_id);
                    return false;
                }
            }
            return $insert_id;
        }
        else{
            $this->errorMsg = "添加失败";
            return false;
        }
    }

    /**添加一或多个考点到knowledge_point表,用于文字版
     * @param $knowledge_id
     * @param string[] $description
     * @return bool
     */
    private function addKnowledgePoint($knowledge_id,$description){
        $concat = "";
        foreach ($description as $d) {
            $a = "({$knowledge_id},'{$d}'),";
            $concat = $concat . $a;
        }
        $concat = substr($concat, 0, -1);
        $sql = "INSERT INTO knowledge_point (knowledge_id,description) VALUES {$concat}";
        return $this->sqltool->query($sql);
    }

    /**更改一个考试回忆录,用于CMS
     * @param int $id knowledge_id
     * @param int $knowledge_category_id 分类ID
     * @param string $description
     * @param int[] $knowledge_point_id 考点id
     * @param string[] $knowledge_point_description 考点描述
     * @param int $count_knowledge_points
     * @param int $img_id
     * @param float $price
     * @param int $term_year
     * @param string $term_semester
     * @param int $sort
     * @return bool
     */
    function updateKnowledgeById($id,$knowledge_category_id,$description,$knowledge_point_id,$knowledge_point_description,$count_knowledge_points,$img_id,$price,$term_year,$term_semester,$sort=0){
        $arr = [];
        $arr["knowledge_category_id"] = $knowledge_category_id;
        $arr["img_id"] = $img_id;
        $arr["price"] = $price;
        $arr["description"] = $description?$description:"";
        $arr["count_knowledge_points"] = $count_knowledge_points? $count_knowledge_points : count($knowledge_point_description);
        $arr["term_year"] = $term_year;
        $arr["term_semester"] = $term_semester?$term_semester:"";
        $arr["sort"] = $sort;
        if (!$img_id){
            //文字版,更改考点，插入新考点
            if ($this->updateKnowledgePointById($knowledge_point_id,$id,$knowledge_point_description)){
                $this->errorMsg = "更改考点失败";
                return false;
            }
        }
        if(!$this->updateRowById("knowledge",$id,$arr)){
            $this->errorMsg = "更改失败";
            return false;
        }
        return true;
    }

    /**修改考点,用于文字版
     * 新考点的knowledge_point_id = 0
     * @param int[] $knowledge_point_id
     * @param int $knowledge_id
     * @param string[] $knowledge_point_description
     * @return bool
     */
    private function updateKnowledgePointById($knowledge_point_id,$knowledge_id,$knowledge_point_description){
        $knowledge_point_description_for_insert = [];
        $existing_knowledge_point_ids = [];
        $bool = true;
        //把knowledge_point表里已存在的考点的id储存进数组里
        foreach ($this->getKnowledgePointsByKnowledgeId($knowledge_id) as $knowledge_point){
            $existing_knowledge_point_ids[] = $knowledge_point["id"];
        }
        for ($i=0; $i < count($knowledge_point_id);$i++){
            if(in_array($knowledge_point_id[$i],$existing_knowledge_point_ids)){
                //考点id已存在数据表里,执行update
                $arr=[];
                $arr["description"] = $knowledge_point_description[$i];
                $arr["knowledge_id"] = $knowledge_id;
                $this->updateRowById("knowledge_point",$knowledge_point_id[$i],$arr);
            }
            else{
                $knowledge_point_description_for_insert[] = $knowledge_point_description[$i];
            }
        }
        if (count($knowledge_point_description_for_insert) > 0)
            $bool = $this->addKnowledgePoint($knowledge_id,$knowledge_point_description_for_insert);
        return $bool;
    }

    /**删除一个或多个考试回忆录
     * @param int|int[] $id
     * @return bool
     */
    function deleteKnowledgeById($id){
           return $this->realDeleteByFieldIn("knowledge","id",$id);
    }

    function getKnowledgeByCourseCodeIdProfId($course_code_id=0,$prof_id=0,$term_year=0,$term_semester=""){
        $course_code_condition = $course_code_id ? "course_code_id = {$course_code_id}" :"";
        $prof_condition = $prof_id ? "AND prof_id = {$prof_id}" : "";
        $term_year_condition = $term_year ? "AND term_year = {$term_year}" : "";
        $term_semester_condition = $term_semester ? "AND term_semester = '{$term_semester}'" : "";
        $sql = "(SELECT * FROM knowledge WHERE {$course_code_condition} {$prof_condition} {$term_year_condition} {$term_semester_condition}) AS ";

    }

    private function getKnowledgePointsByKnowledgeId($knowledge_id){
        $sql = "SELECT * FROM knowledge_point WHERE knowledge_id in ({$knowledge_id})";
        return $this->sqltool->getListBySql($sql);
    }
    function getInsertId() {
        return $this->sqltool->getInsertId();
    }
}

?>