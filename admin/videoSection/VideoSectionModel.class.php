<?php
namespace admin\videoSection;   //-- 注意 --//
use admin\image\ImageModel;
use admin\video\VideoModel;
use admin\videoAlbum\VideoAlbumModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class VideoSectionModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = "video_section";
    }

    /**
     * 获取一行视频章节信息
     * @param int|string $id
     * @return string
     */
    public function getVideoSectionById($id) {
        $id = intval($id);
        $sql = "SELECT vs.*, va.user_id FROM video_section vs INNER JOIN video_album va ON vs.album_id = va.id WHERE vs.id = {$id}";
        return $this->sqltool->getRowBySql($sql);
    }


    /**
     * 获取一页视频章节
     * @param $albumId
     * @param int $pageSize
     * @param int $isDeleted
     * @param string $q
     * @param string $order
     * @return array
     */
    public function getListOfVideoSectionByVideoAlbumId(
        $albumId,
        $pageSize=20,
        $isDeleted=0,
        $q="",
        $order="sort DESC, publish_time DESC"
    ) {
        $albumId = intval($albumId);
        $where = "album_id={$albumId}";
        if ($isDeleted !== null) {
            $where .= " AND is_deleted = {$isDeleted}";
        }
        if ($q) {
            $where .= " AND " . $q;
        }
        $sql = "SELECT * FROM {$this->table} WHERE {$where}";
        $countSql = "SELECT COUNT(*) FROM {$this->table} WHERE {$where}";

        if($order){
            $sql .= " ORDER BY {$order}";
        }
        return $this->getListWithPage($this->table, $sql, $countSql, $pageSize);
    }

    /**
     * 添加一个视频章节
     * @param $title
     * @param $videoAlbumId
     * @return bool
     */
    public function addVideoSection(
        $title,
        $videoAlbumId
    ) {
        $arr = [];
        $arr["title"] = $title;
        $arr["album_id"] = $videoAlbumId;
        $arr["publish_time"] = time();
        $arr["update_time"] = $arr["publish_time"];
        $arr["sort"] = $this->getNextVideoSectionSortByAlbumId($videoAlbumId);

        return $this->addRow($this->table, $arr);
    }

    /**
     * 更改一个视频章节
     * @param $id
     * @param $title
     * @param $videoAlbumId
     * @return bool
     */
    public function updateVideoSectionById(
        $id,
        $title,
        $videoAlbumId
    ) {
        $result = $this->getVideoSectionById($id);
        if($result){
            $arr = [];
            $arr["id"] = $id;
            $arr["title"] = $title;
            $arr["album_id"] = $videoAlbumId;
            $arr["update_time"] = time();

            $bool = $this->updateRowById($this->table, $id, $arr);
            return $bool;
        }else{
            $this->errorMsg = VideoSectionError::ID_NOT_EXIST;
            return false;
        }
    }

    /**
     * 更新对应视频章节视频数量
     * @param int|string $id 视频专辑ID
     * @return bool|\mysqli_result
     */
    public function updateVideoSectionCount($id){
        $id = intval($id);
        $sql = "UPDATE {$this->table} SET count_video = (SELECT COUNT(*) FROM video WHERE section_id = {$id} AND NOT is_deleted) WHERE id in ({$id})";
        return $this->sqltool->query($sql);
    }

    /**
     * 逻辑删除一个视频章节
     * @param int|string $id 视频章节ID
     * @return bool
     */
    public function deleteVideoSectionById($id) {
        $id = intval($id);
        $result = $this->getVideoSectionById($id);
        if ($result && !$result['is_deleted']) {
            $arr = ["is_deleted"=>1];
            $bool = $this->updateRowById($this->table, $id, $arr);
            if ($bool) {
                // delete all section related videos
                $vm = new VideoModel();
                $vm->deleteListOfVideoBySectionId($id);

                // update album video count
                $vam = new VideoAlbumModel();
                $vam->updateVideoAlbumCount($result['album_id']);
            }
            return $bool;
        } else {
            $this->errorMsg = VideoSectionError::ID_NOT_EXIST;
        }
        return false;
    }

    /**
     * [ADMIN] 永久删除一个视频章节
     *
     * @param $id
     * @return bool|\mysqli_result
     */
    public function purgeVideoSectionById($id) {
        $id = intval($id);
        $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
        return $this->sqltool->query($sql);
    }

    /**
     * [ADMIN] 永久删除一个专辑里的所有章节
     * @param $albumId
     * @return bool|\mysqli_result
     */
    public function purgeListOfVideoSectionByAlbumId($albumId) {
        $albumId = intval($albumId);
        $sql = "DELETE FROM {$this->table} WHERE album_id = {$albumId}";
        return $this->sqltool->query($sql);
    }


    /**
     * Get next video section sort
     * @param $albumId
     * @return bool
     */
    private function getNextVideoSectionSortByAlbumId($albumId) {
        $albumId = intval($albumId);
        $sql = "SELECT MAX(sort)+1 AS sort FROM video_section WHERE album_id = {$albumId}";
        $result = $this->sqltool->getRowBySql($sql);
        return $result['sort'] || 1;
    }

}


/**========**/
/** Errors **/
/**========**/

abstract class VideoSectionError {
    const ID_NOT_EXIST = "Video Section Id doesn't exist.";
    const FAILED_TO_UPDATE = "Failed to update";
}

/**============**/
/** Validators **/
/**============**/

class VideoSectionValidator {

    /**
     * 验证标题
     * @param string $title
     * @return string
     * @throws Exception
     */
    public static function validateTitle($title) {
        $title = trim($title) or BasicTool::throwException("标题不能为空");
        (strlen($title)>0 && strlen($title)<=255) or BasicTool::throwException("标题长度不能超过255字节");
        return $title;
    }

}


?>
