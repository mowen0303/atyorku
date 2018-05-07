<?php
namespace admin\knowledgeCategory;   //-- 注意 --//
use \Model as Model;
class KnowledgeCategoryModel extends Model
{


    /**
     * 添加一分类
     * @param String $name
     * @return bool
     */
    public function addKnowledgeCategory($name){
        $arr = [];
        $arr["name"] = $name?$name:"";
        return $this->addRow("knowledge_category",$arr);
    }
    /**
     * 删除分类
     * @param int $knowledge_category_id
     * @return bool
     */
    public function deleteKnowledgeCategory($knowledge_category_ids)
    {
        foreach($knowledge_category_ids as $knowledge_category_id){
            $sql = "SELECT COUNT(*) AS count FROM knowledge WHERE knowledge_category_id in ({$knowledge_category_id}) ";
            $count = $this->sqltool->getRowBySql($sql)["count"];
            if ($count != 0){
                $this->errorMsg = "删除失败,请删除分类(ID={$knowledge_category_id})下的所有考试回忆录";
                return false;
            }
        }
        //删除
        $bool = $this->realDeleteByFieldIn("knowledge_category","id",$knowledge_category_ids);
            if (!$bool){
                $this->errorMsg = "删除失败，数据未受影响";
                return false;
            }
        return true;
    }

    /**
     * 更改分类
     * @param int $knowledge_category_id
     * @param String $name
     * @return bool
     */
    public function updateKnowledgeCategory($knowledge_category_id,$name){
        $arr = [];
        $arr["name"] = $name?$name:"";
        return $this->updateRowById("knowledge_category",$knowledge_category_id,$arr);
    }

    public function getKnowledgeCategories(){
        $sql = "SELECT * FROM knowledge_category";
        return $this->sqltool->getListBySql($sql);
    }
    public function getKnowledgeCategoryById($id){
        return $this->getRowById("knowledge_category",$id);
    }
    public function getKnowledgeCountByCategoryId($id){
        $sql = "SELECT count(*) AS count from knowledge WHERE knowledge_category_id in ({$id})";
        return $this->sqltool->getRowBySql($sql)["count"];
    }
}
?>