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
    private function getKnowledgePointsByKnowledgeId($knowledge_id){
        $sql = "SELECT * FROM knowledge_point WHERE knowledge_id in ({$knowledge_id})";
        return $this->sqltool->getListBySql($sql);
    }

    /**根据课程,教授,年级和学期等条件查询一页的考试回忆录
     * @param int $user_id
     * @param bool $is_admin
     * @param int $course_code_id
     * @param int $prof_id
     * @param int $term_year
     * @param string $term_semester
     * @return array|false
     */
    function getKnowledgeByCourseCodeIdProfId($user_id,$is_admin,$course_code_id=0,$prof_id=0,$term_year=0,$term_semester=""){
        //查询knowledge表
        $condition = "true";
        if ($course_code_id) $condition .= " AND course_code_id in ({$course_code_id})";
        if ($prof_id) $condition .= " AND prof_id in ({$prof_id})";
        if ($term_year) $condition .= " AND term_year in ({$term_year})";
        if ($term_semester) $condition .= " AND term_semester in ('{$term_semester}')";
        $sql = "SELECT knowledge.*,professor.firstname AS prof_firstname, professor.lastname AS prof_lastname FROM (SELECT knowledge.*,course_code.title AS course_code_parent_title FROM (SELECT knowledge.*,course_code.parent_id AS course_code_parent_id, course_code.title AS course_code_child_title FROM (SELECT knowledge.*, user.alias,user.degree,user.img AS profile_img_url,user.gender,user.user_class_id,user.enroll_year,user.major FROM (SELECT knowledge.*, image.url AS img_url FROM (SELECT knowledge.*,transaction.section_id AS is_purchased FROM (SELECT * FROM knowledge WHERE {$condition}) AS knowledge LEFT JOIN transaction ON transaction.section_id = knowledge.id AND transaction.user_id in ({$user_id}) AND transaction.section_name='knowledge') AS knowledge LEFT JOIN image ON image.id = knowledge.img_id) AS knowledge INNER JOIN user ON user.id = knowledge.seller_user_id) AS knowledge INNER JOIN course_code ON course_code.id = knowledge.course_code_id) AS knowledge LEFT JOIN course_code ON course_code.id = knowledge.course_code_parent_id) AS knowledge INNER JOIN professor ON professor.id = knowledge.prof_id ORDER BY sort DESC, publish_time DESC";
        $countSql = "SELECT COUNT(*) FROM knowledge WHERE {$condition}";
        $knowledges = $this->getListWithPage("knowledge",$sql,$countSql,20);
        if (!$knowledges)
            return false;
        //收集knowledge_id,查询knowledge_point表
        $concat = "";
        foreach ($knowledges as $knowledge){
            $concat .= $knowledge["id"].",";
        }
        $concat = substr($concat, 0, -1);
        //如果当前用户是普通用户,每份未购买的考试回忆录只查询两条考点做为预览.每份已购买的考试回忆录查询所有考点.
        if (!$is_admin){
            $sql = "SELECT * FROM (SELECT knowledge_point.*, transaction.section_id FROM (SELECT * FROM knowledge_point WHERE knowledge_id in ({$concat})) AS knowledge_point LEFT JOIN transaction ON transaction.user_id in ({$user_id}) AND transaction.section_name = 'knowledge' AND section_id = knowledge_point.knowledge_id) AS t1 WHERE (SELECT COUNT(*) FROM (SELECT knowledge_point.*, transaction.section_id FROM (SELECT * FROM knowledge_point WHERE knowledge_id in ({$concat})) AS knowledge_point LEFT JOIN transaction ON transaction.section_name = 'knowledge' AND transaction.user_id in ({$user_id}) AND section_id = knowledge_point.knowledge_id) AS t2 WHERE t2.id <= t1.id AND t2.knowledge_id = t1.knowledge_id AND t1.section_id IS NULL) <= 2";
        }
        //如果当前用户是管理员,查询所有考点
        else{
            $sql = "SELECT * FROM knowledge_point WHERE knowledge_id in ({$concat})";
        }
        $knowledge_points = $this->sqltool->getListBySql($sql);
        //分配考点到对应的考试回忆录
        forEach($knowledges as $knowledge){
            $knowledge["knowledge_points"] = [];
            forEach($knowledge_points as $index => $knowledge_point){
                if($knowledge_point["knowledge_id"] == $knowledge["id"]){
                    $knowledge["knowledge_points"][] = $knowledge_point;
                    unset($knowledge_points[$index]);
                }
            }
        }
        return $knowledges;
    }
    function getKnowledgeById($knowledge_id){
        $sql = "SELECT knowledge.*,professor.firstname AS prof_firstname, professor.lastname AS prof_lastname FROM (SELECT knowledge.*,course_code.title AS course_code_parent_title FROM (SELECT knowledge.*,course_code.parent_id AS course_code_parent_id, course_code.title AS course_code_child_title FROM (SELECT knowledge.*, user.alias,user.degree,user.img AS profile_img_url,user.gender,user.user_class_id,user.enroll_year,user.major FROM (SELECT knowledge.*,image.url AS img_url FROM knowledge WHERE id = {$knowledge_id} LEFT JOIN image ON knowledge.img_id = image.id) AS knowledge INNER JOIN user ON user.id = knowledge.seller_user_id) AS knowledge INNER JOIN course_code ON course_code.id = knowledge.course_code_id) AS knowledge LEFT JOIN course_code ON course_code.id = knowledge.course_code_parent_id) AS knowledge INNER JOIN professor ON professor.id = knowledge.prof_id";
        return $this->sqltool->getRowBySql($sql);
    }

    function updateCountSold($id){
        $sql = "UPDATE knowledge SET count_sold = (SELECT COUNT(*)/2 FROM transaction WHERE section_name = 'knowledge' AND section_id in ({$id})) WHERE id in ({$id})";
        return $this->sqltool->query($sql);
    }

    function getInsertId() {
        return $this->sqltool->getInsertId();
    }
}

?>