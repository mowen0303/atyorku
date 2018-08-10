<?php
namespace admin\videoAlbum;   //-- 注意 --//
use admin\videoAlbumTag\VideoAlbumTagModel;
use admin\videoAlbumTagVideoAlbum\VideoAlbumTagVideoAlbumModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class VideoAlbumModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = "video_album";
    }

    /**
     * 获取一行原始课程专辑信息
     * @param int|string $id
     * @return \一维关联数组
     */
    public function getRawAlbumById($id){
        return $this->getRowById($this->table, intval($id));
    }

    /**
     * 查询一个课程专辑,返回一维键值数组
     *
     * @param $id
     * @return \一维关联数组
     */
    public function getVideoAlbumById($id)
    {
        $videoAlbumSelect = "va.id, va.title, va.description, va.user_id AS user_id, va.institution_id, va.price, va.is_available, va.cover_img_id, va.publish_time, va.last_modified_time, va.count_video, va.count_participants, va.count_clicks, va.count_comments, va.sort";
        $videoAlbumTagSelect = "tag.title AS video_album_tag_title, tag.count_album";
        $userSelect = "u.user_class_id, u.img, u.alias, u.gender, u.major, u.enroll_year, u.degree, uc.is_admin";
        $courseChildSelect = "c1.id AS course_code_child_id, c1.title AS course_code_child_title";
        $courseParentSelect = "c2.id AS course_code_parent_id, c2.title AS course_code_parent_title";
        $professorSelect = "p.id AS professor_id, CONCAT(p.firstname, ' ', p.lastname) AS prof_name";
        $imageSelect = "img.thumbnail_url AS thumbnail_url, img.height AS img_height, img.width AS img_width";
        $institutionSelect = "i.title AS institution_title";

        $select = "{$videoAlbumSelect}, {$videoAlbumTagSelect}, {$userSelect}, {$courseChildSelect}, {$courseParentSelect}, {$professorSelect}, {$imageSelect}, {$institutionSelect}";
        $from = "{$this->table} va INNER JOIN video_album_tag_video_album ta ON va.id = ta.album_id INNER JOIN video_album_tag tag ON tag.id = ta.tag_id INNER JOIN user u ON va.user_id=u.id INNER JOIN institution i ON va.institution_id = i.id LEFT JOIN user_class uc ON u.user_class_id = uc.id LEFT JOIN course_code c1 ON va.course_code_id=c1.id LEFT JOIN course_code c2 ON c1.parent_id=c2.id LEFT JOIN professor p ON va.professor_id=p.id LEFT JOIN image img ON va.cover_img_id=img.id";
        $sql = "SELECT {$select} FROM {$from} WHERE va.id = {$id}";

        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }

    /**
     * 获取一页课程专辑
     * SQL Table alias:
     *  video_album => va,
     *  video_album_tag => tag,
     *  user => u,
     *  user_class => uc,
     *  course_code (child) => c1,
     *  course_code (parent) => c2,
     *  professor => p,
     *  image => img
     * @param int $pageSize
     * @param int $isDeleted
     * @param int $isAvailable
     * @param string $q 检索
     * @param string $additionalSelect 附加 SELECT mysql 语句 (开始不要提供逗号)
     * @param string $additionalFrom 附加 FROM mysql 语句 (需要提供JOIN类型： 'INNER JOIN <Table> <alias table> ON ...')
     * @param string $order 排序
     * @return array|bool
     */
    public function getListOfVideoAlbum($pageSize=20, $isDeleted=0, $isAvailable=null, $q="", $additionalSelect="", $additionalFrom="", $order="") {
        $videoAlbumSelect = "va.id, va.title, va.description, va.user_id AS user_id, va.institution_id, va.price, va.is_available, va.cover_img_id, va.publish_time, va.last_modified_time, va.count_video, va.count_participants, va.count_clicks, va.count_comments, va.sort";
        $videoAlbumTagSelect = "tag.title AS video_album_tag_title, tag.count_album";
        $userSelect = "u.user_class_id, u.img, u.alias, u.gender, u.major, u.enroll_year, u.degree, uc.is_admin";
        $courseChildSelect = "c1.id AS course_code_child_id, c1.title AS course_code_child_title";
        $courseParentSelect = "c2.id AS course_code_parent_id, c2.title AS course_code_parent_title";
        $professorSelect = "p.id AS professor_id, CONCAT(p.firstname, ' ', p.lastname) AS prof_name";
        $imageSelect = "img.thumbnail_url AS thumbnail_url, img.height AS img_height, img.width AS img_width";
        $institutionSelect = "i.title AS institution_title";

        // 获取当前用户的学校ID
        $currentUser = new UserModel();
        $institutionId = $currentUser->institutionId;
        if (!$institutionId) {
            $this->errorMsg = "请先登录";
            return false;
        }

        $select = "{$videoAlbumSelect}, {$videoAlbumTagSelect}, {$userSelect}, {$courseChildSelect}, {$courseParentSelect}, {$professorSelect}, {$imageSelect}, {$institutionSelect}";
        $from = "{$this->table} va INNER JOIN video_album_tag_video_album ta ON va.id = ta.album_id INNER JOIN video_album_tag tag ON tag.id = ta.tag_id INNER JOIN user u ON va.user_id=u.id INNER JOIN institution i ON va.institution_id = i.id LEFT JOIN user_class uc ON u.user_class_id = uc.id LEFT JOIN course_code c1 ON va.course_code_id=c1.id LEFT JOIN course_code c2 ON c1.parent_id=c2.id LEFT JOIN professor p ON va.professor_id=p.id LEFT JOIN image img ON va.cover_img_id=img.id";

        if ($additionalSelect) {
            $select .= ", {$additionalSelect}";
        }
        if ($additionalFrom) {
            $from .= " {$additionalFrom}";
        }

        // build WHERE statement
        $conditions = [];
        array_push($conditions, "va.institution_id = {$institutionId}");
        if ($isDeleted !== null) {
            array_push($conditions, "va.is_deleted = {$isDeleted}");
        }
        if ($isAvailable !== null) {
            array_push($conditions, "va.is_available = {$isAvailable}");
        }
        if ($q) {
            array_push($conditions, $q);
        }
        $where = implode(' AND ', $conditions);

        $sql = "SELECT {$select} FROM {$from}";
        $countSql = "SELECT COUNT(*) FROM {$from}";

        if ($where) {
            $sql .= " WHERE {$where}";
            $countSql .= " WHERE {$where}";
        }
        if ($order) {
            $sql .= " ORDER BY {$order}";
        }

        return $this->getListWithPage($this->table, $sql, $countSql, $pageSize);
    }


    /**
     * 添加一个课程专辑
     * @param string $title
     * @param string $description
     * @param int|array $tagId
     * @param int $userId
     * @param int $courseCodeId
     * @param int $professorId
     * @param int $institutionId
     * @param float $price
     * @param int $coverImageId
     * @return bool
     */
    public function addVideoAlbum(
        $title,
        $description,
        $tagId,
        $userId,
        $courseCodeId,
        $professorId,
        $institutionId,
        $price,
        $coverImageId=0
    ) {
        $arr = [];
        $arr["title"] = $title;
        $arr["description"] = $description;
        $arr["user_id"] = $userId;
        $arr["course_code_id"] = $courseCodeId;
        $arr["professor_id"] = $professorId;
        $arr["institution_id"] = $institutionId;
        $arr["price"] = $price;
        $arr["cover_img_id"] = $coverImageId;

        $arr["publish_time"] = time();
        $arr["last_modified_time"] = $arr["publish_time"];

        $bool = $this->addRow($this->table, $arr);
        $id = $this->idOfInsert;
        if ($bool && $id>0) {
            $ta = new VideoAlbumTagVideoAlbumModel();
            $bool = $ta->updateVideoAlbumTagByVideoAlbumId($tagId, $id);
            if (!$bool) {
                $this->errorMsg = $ta->errorMsg;
            }
        }
        return $bool;
    }


    /**
     * 更改一个课程专辑
     * @param int $id
     * @param string $title
     * @param string $description
     * @param int|array $tagId
     * @param int $userId
     * @param int $courseCodeId
     * @param int $professorId
     * @param int $institutionId
     * @param float $price
     * @param int $coverImageId
     * @return bool
     */
    public function updateVideoAlbumById(
        $id,
        $title,
        $description,
        $tagId,
        $userId,
        $courseCodeId,
        $professorId,
        $institutionId,
        $price,
        $coverImageId=0
    ) {
        $result = $this->getRawAlbumById($id);
        if($result){
            $arr = [];
            $arr["id"] = $id;
            $arr["title"] = $title;
            $arr["description"] = $description;
            $arr["user_id"] = $userId;
            $arr["course_code_id"] = $courseCodeId;
            $arr["professor_id"] = $professorId;
            $arr["institution_id"] = $institutionId;
            $arr["price"] = $price;
            $arr["cover_img_id"] = $coverImageId;
            $arr["last_modified_time"] = time();

            $bool = $this->updateRowById($this->table, $id, $arr);
            if ($bool) {
                $ta = new VideoAlbumTagVideoAlbumModel();
                $bool = $ta->updateVideoAlbumTagByVideoAlbumId($tagId, $id);
                if (!$bool) {
                    $this->errorMsg = $ta->errorMsg;
                }
            }
            return $bool;
        }else{
            $this->errorMsg = VideoAlbumError::ID_NOT_EXIST;
            return false;
        }
    }


    /**
     * 逻辑删除一个课程专辑
     * @param int|string $id 课程专辑ID
     * @return bool
     */
    public function deleteVideoAlbumById($id)
    {
        $id = intval($id);
        $result = $this->getRawAlbumById($id);
        if ($result && !intval($result["is_deleted"])) {
            $arr = ["is_deleted"=>1];
            $bool = $this->updateRowById($this->table, $id, $arr);
            if ($bool) {
                $ta = new VideoAlbumTagVideoAlbumModel();
                $ta->updateVideoAlbumTagByVideoAlbumId([], $id);
            }
            return $bool;
        } else {
            $this->errorMsg = VideoAlbumError::ID_NOT_EXIST;
        }
        return false;
    }

    /**
     * [ADMIN] 永久删除一个课程专辑
     *
     * @param $id
     * @return bool|\mysqli_result
     */
    public function purgeVideoAlbumById($id)
    {
        $result = $this->getRawAlbumById($id);
        if($result){
            $ta = new VideoAlbumTagVideoAlbumModel();
            $tags = $ta->getListOfVideoAlbumTagByVideoAlbumId($id);

            $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
            $bool = $this->sqltool->query($sql);
            if ($bool && is_array($tags) && sizeof($tags)>0) {
                $videoAlbumTagModel = new VideoAlbumTagModel();
                $videoAlbumTagModel->updateVideoAlbumTagCount($tags);
            }
            return $bool;
        } else {
            $this->errorMsg = VideoAlbumError::ID_NOT_EXIST;
        }
        return false;
    }

    /**
     * 更新对应课程专辑视频数量
     * @param int|string $id 课程专辑ID
     * @return bool|\mysqli_result
     */
    public function updateVideoAlbumCount($id)
    {
        $id = intval($id);
        $sql = "UPDATE {$this->table} SET count_video = (SELECT COUNT(*) FROM video WHERE album_id = {$id} AND NOT is_deleted) WHERE id in ({$id})";
        return $this->sqltool->query($sql);
    }


    /**
     * 更新视频专辑的Availability, 同时更新专辑tag count
     * @param $id
     * @param $isAvailable
     * @return bool|\mysqli_result
     */
    public function updateAvailabilityByAlbumId($id, $isAvailable) {
        $id = intval($id);
        $isAvailable = intval($isAvailable);
        $result = $this->getRawAlbumById($id);
        if ($result) {
            if ($isAvailable === intval($result['is_available'])) {
                $this->errorMsg = "Availability not changed.";
                return false;
            }
            if ($isAvailable === 1) {
                // TODO: check if album has at least one free video
            }
            $sql = "UPDATE {$this->table} SET is_available = {$isAvailable} WHERE id = {$id}";
            $bool = $this->sqltool->query($sql);
            if ($bool && $isAvailable === 1) {
                $ta = new VideoAlbumTagVideoAlbumModel();
                $tags = $ta->getListOfVideoAlbumTagByVideoAlbumId($id);
                if (is_array($tags)) {
                    $videoAlbumTagModel = new VideoAlbumTagModel();
                    $videoAlbumTagModel->updateVideoAlbumTagCount($tags);
                }
            }
            return $bool;
        } else {
            $this->errorMsg = VideoAlbumError::ID_NOT_EXIST;
            return false;
        }
    }
}


/**========**/
/** Errors **/
/**========**/

abstract class VideoAlbumError {
    const ID_NOT_EXIST = "Video Album Id doesn't exist.";
    const FAILED_TO_UPDATE = "Failed to update";
}

?>
