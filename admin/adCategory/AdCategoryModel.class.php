<?php
namespace admin\adCategory;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class AdCategoryModel extends Model {

    /** 添加一则广告分类
     * @param String $size
     * @param String $title
     * @param String $description
     * @return bool
     */
    public function addAdCategory($size,$title,$description){
        $arr = [];
        $arr["size"] = $size ? $size : "";
        $arr["description"] = $description ? $description : "";
        $arr["title"] = $title ? $title : "";
        $arr["ads_count"] = 0;
        $bool = $this->addRow("ad_category",$arr);
        return $bool;
    }

    //查询一则广告
    public function getAdCategory($id){
        $sql = "SELECT * from ad_category WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }

    //调出所有广告分类
    public function getAdCategories(){
        $sql = "SELECT * FROM ad_category";
        return $this->sqltool->getListBySql($sql);
    }

    /**更改一个广告分类
     * @param int $id 分类id
     * @param String $size
     * @param String $title
     * @param String $description
     * @return $bool
     */
    public function updateAdCategory($id,$size,$title,$description){
        $arr = [];
        $arr["size"] = $size ? $size : "";
        $arr["title"] = $title ? $title : "";
        $arr["description"] = $description ? $description : "";
        return $this->updateRowById("ad_category",$id,$arr);
    }

    /*删除一个分类，确保该分类下没有任何广告
     * @param int $id 分类id
     * @return $bool
     */
    public function deleteAdCategory($id){
        $sql = "SELECT ads_count FROM ad_category WHERE id = {$id}";
        $ads_count = $this->sqltool->getRowBySql($sql)["ads_count"];
        if($ads_count == 0){
            $sql = "DELETE FROM ad_category WHERE id = {$id}";
            return $this->sqltool->query($sql);
        }
        else{
            BasicTool::throwException("请删除分类下的所有广告");
        }
    }
}



?>