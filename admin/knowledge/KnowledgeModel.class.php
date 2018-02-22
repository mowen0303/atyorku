<?php
namespace admin\knowledgeCategory;   //-- 注意 --//
use \Model as Model;
use \BasicTool as BasicTool;
class KnowledgeModel extends Model
{
    /**添加一个考点回忆
     * @param int $seller_user_id
     * @param int $knowledge_category_id
     * @param int $img_id
     * @param int $course_code_id
     * @param int $prof_id
     * @param float $price
     * @param string $description
     * @param string[] | NULL $knowledge_point_description
     * @param int $count_knowledge_points
     * @param string $term_year
     * @param string $term_semester
     * @param int $sort
     */
    function addKnowledge($seller_user_id,$knowledge_category_id,$img_id,$course_code_id,$prof_id,$price,$description,$knowledge_point_description, $count_knowledge_points,$term_year,$term_semester,$sort = 0){

    }

    /**添加一或多个考点到knowledge_point表里
     * @param $knowledge_id
     * @param $description
     */
    private function addKnowledgePoint($knowledge_id,$description){

    }


}
?>