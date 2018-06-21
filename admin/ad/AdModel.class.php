<?php
namespace admin\ad;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class AdModel extends Model
{

    /** 添加一则广告
     * @param String $title 广告标题
     * @param String $description 广告详情
     * @param String $sponsor_name 广告商
     * @param int $img_id_1
     * @param PHP时间戳 $publish_time 投放时间
     * @param PHP时间戳 $expiration_time 过期时间
     * @param int $ad_category_id 广告分类id
     * @param String $ad_url 广告链接
     * @param int $sort 排序时0或者1
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
        $arr["count_clicks"] = 0;
        $arr["count_exhibits"] = 0;
        $arr["sort"] = $sort? $sort : 0;
        $bool = $this->addRow("ad", $arr);
        if ($bool) {
            $sql = "UPDATE ad_category SET ads_count = (SELECT COUNT(*) from ad WHERE ad_category_id = {$ad_category_id}) WHERE id = {$ad_category_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }


    /**调出一分类下的广告
     * $adOption=1查询生效的广告
     * $adOption=0查询未生效的广告
     * @param int $ad_category_id 广告分类id
     * @param int $flag
     * @return array
     */
    public function getAdsByCategory($ad_category_id,$adOption=1,$addExhibitCount=true){
        $currentTime = time();
        $condition="";
        if($adOption==1){
            $condition .= "WHERE {$currentTime} > publish_time and {$currentTime} < expiration_time";
        }else{
            $condition .= "WHERE ({$currentTime} < publish_time or {$currentTime} > expiration_time)";
        }
        if($ad_category_id) $condition .= " AND ad_category_id = {$ad_category_id}";

        $sql = "SELECT ad.*,image.url AS img_url FROM (SELECT * FROM ad  {$condition} ) AS ad LEFT JOIN image ON ad.img_id_1 = image.id ORDER BY sort DESC, publish_time DESC";
        $countSql = "SELECT COUNT(*) FROM ad {$condition} ORDER BY sort DESC, publish_time DESC";
        $result = $this->getListWithPage("ad", $sql, $countSql, 20);
        if($addExhibitCount) $this->addExhibitCount($result);
        return $result;
    }

    private function addExhibitCount($ads){
        if (is_array($ads) && count($ads) > 0){
            $ids = "";
            foreach($ads as $ad){
                $ids .= $ad['id'].",";
            }
            $ids = substr($ids,0,-1);
            $sql = "UPDATE ad SET count_exhibits = count_exhibits + 1 WHERE id in ({$ids})";
            $this->sqltool->query($sql);
        }
    }

    public function getAd($id){
        $sql = "SELECT * from ad WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }

    /**
     * 更改一则广告
     * @param int $id 广告id
     * @param String $title 广告标题
     * @param String $description 广告详情
     * @param String $sponsor_name 广告商
     * @param int $img_id_1
     * @param PHP时间戳 $publish_time 投放时间
     * @param PHP时间戳 $expiration_time 过期时间
     * @param int $ad_category_id 广告分类id
     * @param String $ad_url 广告链接
     * @param int $sort 排序时0或者1
     * @return $bool
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

    /**删除广告
     * @param int|array $id 广告id
     * @return bool
     */
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


    /**添加点击量
     */
    public function addOnceView($id)
    {
        $sql = "UPDATE ad SET count_clicks = count_clicks + 1 WHERE id in ({$id})";
        return $this->sqltool->query($sql);
    }
}



?>