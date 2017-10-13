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
    public function addAd($title, $description, $sponsor_name, $banner_url, $publish_time, $expiration_time, $ad_category_id, $ad_url)
    {
        $arr = [];
        $arr["title"] = $title;
        $arr["description"] = $description;
        $arr["sponsor_name"] = $sponsor_name;
        $arr["banner_url"] = $banner_url;
        $arr["publish_time"] = $publish_time;
        $arr["expiration_time"] = $expiration_time;
        $arr["ad_category_id"] = $ad_category_id;
        $arr["ad_url"] = $ad_url;
        $arr["view_count"] = 0;
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

        if ($flag == "effective") {
            $sql = "SELECT * FROM ad WHERE ad_category_id = {$ad_category_id} and {$currentTime}>publish_time and {$currentTime} <expiration_time ";
            $countSql = "SELECT COUNT(*) FROM ad WHERE ad_category_id = {$ad_category_id} and {$currentTime}>publish_time and {$currentTime} <expiration_time";
            return $this->getListWithPage("ad", $sql, $countSql, 20);
        }
        else{
            $sql = "SELECT * FROM ad WHERE ad_category_id = {$ad_category_id} and ({$currentTime} < publish_time or {$currentTime}>expiration_time)";
            $countSql = "SELECT count(*) FROM ad WHERE ad_category_id = {$ad_category_id} and ({$currentTime} < publish_time or {$currentTime}>expiration_time)";
            return $this->getListWithPage("ad", $sql, $countSql, 20);
        }
    }

    /**
     * 更改一则广告
     * @return bool
     */
    public function updateAd($id, $title, $description, $sponsor_name, $banner_url, $publish_time,$expiration_time, $ad_category_id, $ad_url)
    {
        $arr = [];
        $arr["title"] = $title;
        $arr["description"] = $description;
        $arr["sponsor_name"] = $sponsor_name;
        $arr["banner_url"] = $banner_url;
        $arr["publish_time"] = $publish_time;
        $arr["expiration_time"] = $expiration_time;
        $arr["ad_category_id"] = $ad_category_id;
        $arr["ad_url"] = $ad_url;
        $bool = $this->updateRowById("ad", $id, $arr);
        return $bool;
    }

    public function deleteAd($id)
    {
        $sql = "SELECT * FROM ad WHERE id = {$id[0]}";
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