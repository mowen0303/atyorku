<?php
namespace admin\videoAlbumTagVideoAlbum;   //-- 注意 --//
use admin\videoAlbumTag\VideoAlbumTagModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class VideoAlbumTagVideoAlbumModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = "video_album_tag_video_album";
    }

    public function getListOfVideoAlbumTagByVideoAlbumId($albumId)
    {
        $albumId = intval($albumId);
        $sql = "SELECT tag_id FROM {$this->table} WHERE album_id = {$albumId}";
        $tagIds = [];
        $result = $this->sqltool->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($tagIds, $row['tag_id']);
            }
            $result->free();
            return $tagIds;
        }
        return false;
    }

    /**
     * 永久删除指定视频专辑的所有tag和其tag对应的专辑数
     * @param $albumId
     * @return bool|\mysqli_result
     */
    public function purgeAllVideoAlbumTagByVideoAlbumId($albumId) {
        $albumId = intval($albumId);
        $tags = $this->getListOfVideoAlbumTagByVideoAlbumId($albumId);
        if ($tags) {
            $sql = "DELETE FROM {$this->table} WHERE album_id = {$albumId}";
            $bool = $this->sqltool->query($sql);
            $tagModel = new VideoAlbumTagModel();
            $tagModel->updateVideoAlbumTagCount($tags);
            return $bool;
        }
    }

    /**
     * 更新一个专辑的对应Tag和其tag对应的专辑数
     * @param int|array $tagId 专辑新的Tag
     * @param int $albumId
     * @return bool
     */
    public function updateVideoAlbumTagByVideoAlbumId($tagId, $albumId) {
        // 获取新添加和新删除的Tag
        $oldTagIds = $this->getListOfVideoAlbumTagByVideoAlbumId($albumId);
        if (!is_array($oldTagIds)) {
            $this->errorMsg = 'Fail to get tags';
            return false;
        }
        $deletedTagIds = [];
        $addedTagIds = [];

        if (is_array($tagId)) {
            $deletedTagIds = array_diff($oldTagIds, $tagId);
            $addedTagIds = array_diff($tagId, $oldTagIds);
        } else {
            $deletedTagIds = array_diff($oldTagIds, [$tagId]);
            if (!in_array($tagId, $oldTagIds)) {
                $addedTagIds = $tagId;
            }
        }
        if ($deletedTagIds) {
            $bool = $this->purgeVideoAlbumTagVideoAlbum($deletedTagIds, $albumId);
            if (!$bool) {
                $this->errorMsg = 'Fail to delete old album tags';
                return false;
            }
        }
        if ($addedTagIds) {
            $bool = $this->addVideoAlbumTagVideoAlbum($addedTagIds, $albumId);
            if (!$bool) {
                $this->errorMsg = 'Fail to add new album tags';
                return false;
            }
        }

        $tagModel = new VideoAlbumTagModel();
        $tagModel->updateVideoAlbumTagCount(array_merge($deletedTagIds, $addedTagIds));
        return true;
    }

    /**=============**/
    /**   Private   **/
    /**=============**/

    /**
     * 添加一个视频专辑类别视频专辑
     *
     * @param int|array $tagId
     * @param int $albumId
     * @return bool
     */
    private function addVideoAlbumTagVideoAlbum($tagId, $albumId)
    {
        if (is_array($tagId)) {
            // Add Multiple tags
            $tagId = array_unique(array_filter($tagId, function($id){return intval($id);}));
            if (!$tagId) {
                $this->errorMsg = "No Valid tags";
                return false;
            }
            $tagSql = implode(",", array_map(function($id) use ($albumId) {return "({$id}, {$albumId})";}, $tagId));
            $sql = "INSERT INTO {$this->table} (tag_id, album_id) VALUES {$tagSql}";
            return $this->sqltool->query($sql);
        } else {
            // add single tag
            $arr = [];
            $arr["tag_id"] = $tagId;
            $arr["album_id"] = $albumId;
            return $this->addRow($this->table, $arr);
        }
    }

    /**
     * 永久删除一个视频专辑Tag视频专辑
     * @param int|array $tagId
     * @param int $albumId
     * @return bool|\mysqli_result
     */
    private function purgeVideoAlbumTagVideoAlbum($tagId, $albumId)
    {
        $tagSql = "";
        $albumId = intval($albumId);
        if (is_array($tagId)) {
            // Add Multiple tags
            $tagId = array_unique(array_filter($tagId, function($id){return intval($id);}));
            if (!$tagId) {
                $this->errorMsg = "No Valid tags";
                return false;
            }
            $tagSql = implode(",", $tagId);
        } else {
            // add single tag
            $tagSql = intval($tagId);
        }
        $sql = "DELETE FROM {$this->table} WHERE album_id = {$albumId} AND tag_id IN ({$tagSql})";
        return $this->sqltool->query($sql);
    }
}



?>
