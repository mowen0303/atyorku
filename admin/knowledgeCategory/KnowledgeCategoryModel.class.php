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
    public function deleteKnowledgeCategory($knowledge_category_id)
    {
        $sql = "SELECT COUNT(*) AS count FROM knowledge WHERE knowledge_category_id in ({$knowledge_category_id}) ";
        $count = $this->sqltool->getRowBySql($sql)["count"];
        if ($count == 0){
            $sql="DELETE FROM knowledge_category WHERE id in ({$knowledge_category_id})";
            $result = $this->sqltool->query($sql);
            if ($result)
                return true;
            else{
                $this->errorMsg = "删除失败";
                return false;
            }
        }
        else{
            $this->errorMsg = "删除失败,请删除分类下的所有考点";
            return false;
        }
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
}
?>