<?php
namespace admin\ad;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class AdModel extends Model
{

    /**
     * 添加一则广告
     * @return $bool
     */
    public function addAd($title, $description, $sponsor_name, $img_id_1, $publish_time, $expiration_time, $ad_category_id, $ad_url,$sort)
    {
        $arr = [];
        $arr["title"] = $title ? $title : "";
        $arr["description"] = $description ? $description : "";
        $arr["sponsor_name"] = $sponsor_name ? $sponsor_name : "";
        $arr["img_id_1"] = $img_id_1 ? $img_id_1 : 0;
        $arr["publish_time"] = $publish_time ? $publish_time : 0;
        $arr["expiration_time"] = $expiration_time ? $expiration_time : 0;
        $arr["ad_category_id"] = $ad_category_id ? $ad_category_id : 0;
        $arr["ad_url"] = $ad_url ? $ad_url : "";
        $arr["view_count"] = 0;
        $arr["sort"] = $sort? $sort : 0;
        $bool = $this->addRow("ad", $arr);
        if ($bool) {
            $sql = "UPDATE ad_category SET ads_count = (SELECT COUNT(*) from ad WHERE ad_category_id = {$ad_category_id}) WHERE id = {$ad_category_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }


    /*调出一页广告
     * 返回二维数组
     */
    public function getAdsByCategory($ad_category_id,$flag){
        $currentTime = time();
        if ($ad_category_id){
            $condition = "ad_category_id = {$ad_category_id} and";
        }
        else{
            $condition="";
        }
        if ($flag == 1) {
            $sql = "SELECT * FROM ad WHERE {$condition} {$currentTime}>publish_time and {$currentTime} <expiration_time ORDER BY sort DESC, publish_time DESC";
            $countSql = "SELECT COUNT(*) FROM ad WHERE {$condition} {$currentTime}>publish_time and {$currentTime} <expiration_time ORDER BY sort DESC, publish_time DESC";
            return $this->getListWithPage("ad", $sql, $countSql, 20);
        }
        else{
            $sql = "SELECT * FROM ad WHERE {$condition} ({$currentTime} < publish_time or {$currentTime}>expiration_time) ORDER BY sort DESC, publish_time DESC";
            $countSql = "SELECT count(*) FROM ad WHERE {$condition} ({$currentTime} < publish_time or {$currentTime}>expiration_time) ORDER BY sort DESC, publish_time DESC";
            return $this->getListWithPage("ad", $sql, $countSql, 20);
        }
    }

    public function getAd($id){
        $sql = "SELECT * from ad WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }

    /**
     * 更改一则广告
     * @return bool
     */
    public function updateAd($id, $title, $description, $sponsor_name, $img_id_1, $publish_time,$expiration_time, $ad_category_id, $ad_url,$sort)
    {
        $arr = [];
        $arr["title"] = $title ? $title : "";
        $arr["description"] = $description ? $description : "";
        $arr["sponsor_name"] = $sponsor_name ? $sponsor_name : "";
        $arr["img_id_1"] = $img_id_1 ? $img_id_1 : 0;
        $arr["publish_time"] = $publish_time ? $publish_time : 0;
        $arr["expiration_time"] = $expiration_time ? $expiration_time : 0;
        $arr["ad_category_id"] = $ad_category_id ? $ad_category_id : 0;
        $arr["ad_url"] = $ad_url ? $ad_url : "";
        $arr["sort"] = $sort ? $sort : 0;
        $bool = $this->updateRowById("ad", $id, $arr);
        return $bool;
    }

    public function deleteAd($id)
    {
        if (is_array($id))
            $sql = "SELECT * FROM ad WHERE id = {$id[0]}";
        else
            $sql = "SELECT * FROM ad WHERE id = {$id}";
        $ad_category_id = $this->sqltool->getRowBySql($sql)["ad_category_id"];

        $bool = $this->realDeleteByFieldIn("ad","id",$id,true);
        if ($bool) {
            $sql = "UPDATE ad_category SET ads_count = (SELECT COUNT(*) from ad WHERE ad_category_id = {$ad_category_id}) WHERE id = {$ad_category_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }

    /**
     * @更新阅读量
     */
    public function addAmountOfRead($AdId)
    {
        $sql = "UPDATE ad SET view_count = view_count + 1 WHERE id = " . $AdId;
        $this->sqltool->query($sql);
    }
}



?>