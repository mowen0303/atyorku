<?php
namespace admin\videoAlbumTag;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class VideoAlbumTagModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = "video_album_tag";
    }

    /**
     * 添加一个视频专辑类别
     *
     * @param $title
     * @param int $coverImageId
     * @return bool
     */
    public function addVideoAlbumTag($title, $coverImageId=0)
    {
        $arr = [];
        $arr["title"] = $title;
        $arr["cover_img_id"] = $coverImageId;
        return $this->addRow($this->table, $arr);
    }

    /**
     * 查询一个视频专辑类别,返回一维键值数组
     *
     * @param $id
     * @return \一维关联数组
     */
    public function getVideoAlbumTag($id)
    {
        $select = "tag.*, img.thumbnail_url AS thumbnail_url, img.height AS img_height, img.width AS img_width";
        $from = "{$this->table} tag LEFT JOIN image img ON tag.cover_img_id=img.id";
        $sql = "SELECT {$select} FROM {$from} WHERE tag.id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }

    /**
     * 获取全部视频专辑类别
     * @param int $pageSize
     * @return array
     */
    public function getListOfVideoAlbumTag($pageSize=30) {
        $select = "tag.*, img.thumbnail_url AS thumbnail_url, img.height AS img_height, img.width AS img_width";
        $from = "{$this->table} tag LEFT JOIN image img ON tag.cover_img_id=img.id";
        $sql = "SELECT {$select} FROM {$from}";
        $countSql = "SELECT COUNT(*) FROM {$from}";
        return $this->getListWithPage($this->table, $sql, $countSql, $pageSize);
    }

    /**
     * 更改一个视频专辑类别名称
     * @param $id
     * @param $title
     * @param int $coverImageId
     * @return bool
     */
    public function updateVideoAlbumTag($id, $title, $coverImageId=0)
    {
        $arr = [];
        $arr["title"] = $title;
        $arr["cover_img_id"] = $coverImageId;
        return $this->updateRowById($this->table, $id, $arr);
    }

    /**
     * 删除一个视频专辑类别
     * @param $id
     * @return bool|\mysqli_result
     */
    public function deleteVideoAlbumTag($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
        return $this->sqltool->query($sql);
    }

    /**
     * 查看是否视频专辑类别名称已存在
     *
     * @param string $title 新类别名称
     * @param bool $currentId 当前类别id，用于更新类别名称，新建类别无需提供
     * @return bool
     */
    public function isExistOfVideoAlbumTagTitle($title,$currentId=false){
        $sql = "SELECT COUNT(*) as amount FROM {$this->table} WHERE title = '{$title}'" . (($currentId)?" AND id<>{$currentId}":"");
        $row = $this->sqltool->getRowBySql($sql);
        if($row['amount']>0){
            return true;
        }
        return false;
    }

    /**
     * 更新对应视频专辑类别专辑数量
     * @param int|array $tagId 视频专辑Tag ID
     * @return bool|\mysqli_result
     */
    public function updateVideoAlbumTagCount($tagId) {
        if (is_array($tagId)) {
            // update Multiple tags count
            $tagId = array_unique(array_filter($tagId, function($id){return intval($id);}));
            if (!$tagId) {
                $this->errorMsg = "No Valid tags";
                return false;
            }

            // Query tag counts
            $tagSql = implode(",", $tagId);
            $sql = "SELECT ta.tag_id, COUNT(*) AS c FROM video_album_tag_video_album ta INNER JOIN video_album va ON ta.album_id = va.id WHERE ta.tag_id IN ({$tagSql}) AND NOT va.is_deleted AND va.is_available GROUP BY ta.tag_id";
            $result = $this->sqltool->query($sql);
            if ($result) {
                $tagCounts = [];
                while ($row = $result->fetch_assoc()) {
                    $tagCounts[$row['tag_id']] = $row['c'];
                }
                $result->free();

                // add 0 to the tag ids that doesn't have any row
                $caseSql = "";
                foreach($tagId as $value) {
                    if (!$tagCounts[$value]) {
                        $tagCounts[$value] = 0;
                    }
                    $caseSql .= "WHEN '{$value}' THEN '{$tagCounts[$value]}' ";
                }

                $sql = "UPDATE {$this->table} SET count_album = CASE id {$caseSql}END WHERE id IN ({$tagSql})";
                return $this->sqltool->query($sql);
            } else {
                $this->errorMsg = "Fail to get video album tag counts";
                return false;
            }
        } else {
            // update single tag count
            $tagId = intval($tagId);
            $sql = "UPDATE {$this->table} SET count_album = (SELECT COUNT(*) FROM video_album_tag_video_album ta INNER JOIN video_album va ON ta.album_id = va.id WHERE ta.tag_id = {$tagId} AND NOT va.is_deleted AND va.is_available) WHERE id = {$tagId}";
            return $this->sqltool->query($sql);
        }
    }
}



?>
