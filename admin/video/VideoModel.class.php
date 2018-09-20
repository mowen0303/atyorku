<?php
namespace admin\video;   //-- 注意 --//
use admin\image\ImageModel;
use admin\videoAlbum\VideoAlbumModel;
use admin\videoSection\VideoSectionModel;
use admin\productTransaction\ProductTransactionModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class VideoModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = "video";
    }

    const NUM_OF_WATCH_LIMITS = 5;

    /**
     * 获取一行视频
     * @param int|string $id 视频ID
     * @return \一维关联数组|bool
     */
    public function getVideoById($id) {
        $id = intval($id);
        $videoSelect = "v.*";
        $videoAlbumSelect = "va.user_id, va.institution_id";
        $videoSectionSelect = "vs.title AS section_title, vs.count_video, vs.sort AS section_sort, vs.publish_time AS section_publish_time, vs.update_time AS section_update_time";
        $imageSelect = "img.thumbnail_url AS thumbnail_url, img.height AS img_height, img.width AS img_width";
        $institutionSelect = "i.term_end_dates";
        $from = "video v INNER JOIN video_album va ON v.album_id = va.id INNER JOIN video_section vs ON v.section_id = vs.id INNER JOIN image img ON v.cover_img_id = img.id INNER JOIN institution i ON va.institution_id = i.id";
        $where = "v.id = {$id}";

        $sql = "SELECT {$videoSelect}, {$videoAlbumSelect}, {$videoSectionSelect}, {$imageSelect}, {$institutionSelect} FROM {$from} WHERE {$where}";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
     * 获取一页视频
     * @param int|string $albumId 视频专辑ID [optional]
     * @param int|string $sectionId 视频章节ID [optional]
     * @param int $status 视频审核状况 [optional] [null | -1 | 0 | 1]
     * @param int $isDeleted 是否被删除
     * @return array|bool
     */
    public function getListOfVideoByConditions($albumId=null, $sectionId=null, $status=null, $isDeleted=0) {
        $albumId = intval($albumId);
        $sectionId = intval($sectionId);
        $conditions = [];

        if ($albumId) {
            array_push($conditions, "v.album_id = {$albumId}");
        }
        if ($sectionId) {
            array_push($conditions, "v.section_id = {$sectionId}");
        }
        if ($status !== null) {
            $status = intval($status);
            if ($status < -1 || $status > 1) {
                $this->errorMsg = VideoError::INVALID_REVIEW_STATUS;
                return false;
            }
            array_push($conditions, "v.review_status = {$status}");
        }

        array_push($conditions, "v.is_deleted = {$isDeleted}");

        $where = implode(" AND ", $conditions);

        $result = $this->getListOfVideoBy($where);

        return $result;
    }


    /**
     * 添加一个视频
     * @param $url
     * @param $size
     * @param $length
     * @param $albumId
     * @param $sectionId
     * @param $instructorId
     * @param $price
     * @param $description
     * @param $title
     * @param $coverImgId
     * @return bool
     */
    public function addVideo(
        $url,
        $size,
        $length,
        $albumId,
        $sectionId,
        $instructorId,
        $price,
        $description,
        $title,
        $coverImgId
    ) {
        $arr = [];
        $arr['url'] = $url;
        $arr['size'] = $size;
        $arr['length'] = $length;
        $arr['album_id'] = $albumId;
        $arr['section_id'] = $sectionId;
        $arr['instructor_id'] = $instructorId;
        $arr['price'] = $price;
        $arr['description'] = $description;
        $arr['title'] = $title;
        $arr['cover_img_id'] = $coverImgId;
        $arr['sort'] = $this->getNextVideoSortBySectionId($sectionId) ?: 1;
        $arr['publish_time'] = time();
        $arr['update_time'] = $arr['publish_time'];

        $bool = $this->addRow($this->table, $arr);
        if ($bool) {
            $lsm = new VideoSectionModel();
            $lsm->updateVideoSectionCount($sectionId);
            $lam = new VideoAlbumModel();
            $lam->updateVideoAlbumCount($albumId);
        }
        return $bool;
    }


    /**
     * 修改一个视频
     * @param $id
     * @param $url
     * @param $size
     * @param $length
     * @param $albumId
     * @param $sectionId
     * @param $instructorId
     * @param $price
     * @param $description
     * @param $title
     * @param $coverImgId
     * @return bool
     */
    public function updateVideo(
        $id,
        $url,
        $size,
        $length,
        $albumId,
        $sectionId,
        $instructorId,
        $price,
        $description,
        $title,
        $coverImgId
    ) {
        $result = $this->getVideoById($id);
        if ($result) {
            $arr = [];
            $arr['url'] = $url;
            $arr['size'] = $size;
            $arr['length'] = $length;
            $arr['album_id'] = $albumId;
            $arr['section_id'] = $sectionId;
            $arr['instructor_id'] = $instructorId;
            $arr['price'] = $price;
            $arr['description'] = $description;
            $arr['title'] = $title;
            $arr['cover_img_id'] = $coverImgId;
            $arr['update_time'] = time();
            $bool = $this->updateRowById($this->table, $id, $arr);

            // Update section count if changes
            if ($bool && intval($result['section_id']) !== intval($sectionId)) {
                $lsm = new VideoSectionModel();
                $lsm->updateVideoSectionCount($sectionId);
                $lsm->updateVideoSectionCount($result['section_id']);
            }
            return $bool;
        } else {
            $this->errorMsg = VideoError::ID_NOT_EXIST;
            return false;
        }
    }


    /**
     * 逻辑删除一个视频
     * @param int|string $id 视频ID
     * @return bool
     */
    public function deleteVideoById($id) {
        $result = $this->getVideoById($id);
        if ($result && !$result['is_deleted']) {
            $arr = [];
            $arr["is_deleted"] = 1;
            $arr["update_time"] = time();
            $bool = $this->updateRowById($this->table, intval($id), $arr);
            if ($bool) {
                // update video count
                $vam = new VideoAlbumModel();
                $vam->updateVideoAlbumCount($result['album_id']);
                $vsm = new VideoSectionModel();
                $vsm->updateVideoSectionCount($result['section_id']);
            }
            return $bool;
        } else {
            $this->errorMsg = VideoError::ID_NOT_EXIST;
        }
        return false;
    }

    /**
     * 逻辑删除视频章节Id下的所有视频
     * @param int|string $sectionId 视频章节ID
     * @return bool|\mysqli_result
     */
    public function deleteListOfVideoBySectionId($sectionId) {
        $sectionId = intval($sectionId);
        $currentTime = time();
        $sql = "UPDATE {$this->table} SET is_deleted = 1, update_time = {$currentTime} WHERE section_id = {$sectionId}";
        return $this->sqltool->query($sql);
    }

    /**
     * 逻辑删除视频专辑Id下的所有视频
     * @param int|string $albumId 视频专辑ID
     * @return bool|\mysqli_result
     */
    public function deleteListOfVideoByAlbumId($albumId) {
        $albumId = intval($albumId);
        $currentTime = time();
        $sql = "UPDATE {$this->table} SET is_deleted = 1, update_time = {$currentTime} WHERE album_id = {$albumId}";
        return $this->sqltool->query($sql);
    }

    /**
     * [ADMIN] 永久删除一个视频
     *
     * @param $id
     * @return bool|\mysqli_result
     */
    public function purgeVideoById($id) {
        $id = intval($id);
        return $this->purgeVideo("id = {$id}");
    }

    /**
     * [ADMIN] 永久删除一个视频章节里的所有视频
     * @param $sectionId
     * @return bool|\mysqli_result
     */
    public function purgeListOfVideoBySectionId($sectionId) {
        $sectionId = intval($sectionId);
        return $this->purgeVideo("section_id = {$sectionId}");
    }

    /**
     * [ADMIN] 永久删除一个视频专辑里的所有视频
     * @param $albumId
     * @return bool|\mysqli_result
     */
    public function purgeListOfVideoByAlbumId($albumId) {
        $albumId = intval($albumId);
        return $this->purgeVideo("album_id = {$albumId}");
    }

    /**
     * 更新一个视频的审核状态
     * @param int|string $id video id
     * @param int $status 审核状态 [ -1 => 未通过, 0 => 未审核, 1 => 通过 ]
     * @return bool
     */
    public function updateReviewStatusById($id, $status) {
        $id = intval($id);
        $status = intval($status);
        if ($status === 0 || $status === -1 || $status === 1) {
            $arr = ["review_status"=>$status];
            return $this->updateRowById($this->table, $id, $arr);
        } else {
            $this->errorMsg = VideoError::INVALID_PARAMETERS . " [ review_status ]";
            return false;
        }
    }

    /**
     * Check if given user is authorized to watch given video id
     * @param $id
     * @param $userId
     * @return bool
     * @throws Exception
     */
    public function checkAuthentication($id, $userId) {
        $ptm = new ProductTransactionModel($this->table);
        $vLimit = VideoModel::NUM_OF_WATCH_LIMITS;
        $currentTime = time();
        $result = $ptm->getListOfPurchasedTransactionsBy(
            $userId,
            $id,
            $this->table,
            false,
            false,
            "pt.count_video_view < {$vLimit} AND pt.expiration_time > {$currentTime}"
        );
        return ($result && sizeof($result) > 0);
    }

    /**
     * 获取指定用户的指定一列视频ID的购买交易列表
     * @param int|array $vids 指定视频ID列表
     * @param $userId
     * @return array
     * @throws Exception
     */
    public function getListOfPurchasedVideoTransaction($vids, $userId) {
        $ptm = new ProductTransactionModel($this->table);
        $vLimit = VideoModel::NUM_OF_WATCH_LIMITS;
        $currentTime = time();
        $result = $ptm->getListOfPurchasedTransactionsBy(
            $userId,
            $vids,
            $this->table,
            false,
            false,
            "pt.count_video_view < {$vLimit} AND pt.expiration_time > {$currentTime}"
        );
        return $result;
    }

    /**==================**/
    /** Private function **/
    /**==================**/

    /**
     * 获取一页视频
     * @param string $q mysql WHERE 条件 (不含WHERE关键词)
     * @param int $pageSize
     * @param bool $showVideo
     * @return array
     */
    private function getListOfVideoBy($q="") {
        $videoSelect = "v.*";
        $videoAlbumSelect = "va.user_id, va.institution_id";
        $videoSectionSelect = "vs.title AS section_title, vs.count_video, vs.sort AS section_sort, vs.publish_time AS section_publish_time, vs.update_time AS section_update_time";
        $imageSelect = "img.thumbnail_url AS thumbnail_url, img.height AS img_height, img.width AS img_width";
        $institutionSelect = "i.term_end_dates";
        $from = "video v INNER JOIN video_album va ON v.album_id = va.id INNER JOIN video_section vs ON v.section_id = vs.id LEFT JOIN image img ON v.cover_img_id = img.id INNER JOIN institution i ON va.institution_id = i.id";
        $order = "vs.sort, vs.publish_time, v.sort, v.publish_time";

        $sql = "SELECT {$videoSelect}, {$videoAlbumSelect}, {$videoSectionSelect}, {$imageSelect}, {$institutionSelect} FROM {$from}";
        $countSql = "SELECT COUNT(*) FROM {$from}";
        if ($q) {
            $sql .= " WHERE {$q}";
            $countSql .= " WHERE {$q}";
        }
        $sql .= " ORDER BY {$order}";
        return $this->getListWithPage($this->table, $sql, $countSql, 10000);
    }

    /**
     * 永久删除视频
     * @param string $q mysql where 条件
     * @return bool|\mysqli_result
     */

    private function purgeVideo($q) {
        $sql = "DELETE FROM {$this->table}";
        if ($q) {
            $sql .= " WHERE {$q}";
        }
        return $this->sqltool->query($sql);
    }

    private function getNextVideoSortBySectionId($sectionId) {
        $sectionId = intval($sectionId);
        $sql = "SELECT MAX(sort)+1 AS sort FROM video WHERE section_id = {$sectionId}";
        $result = $this->sqltool->getRowBySql($sql);
        if ($result) {
            return $result['sort'];
        } else {
            return false;
        }
    }

}


/**========**/
/** Errors **/
/**========**/

abstract class VideoError {
    const ID_NOT_EXIST = "Video Album Id doesn't exist.";
    const FAILED_TO_UPDATE = "Failed to update";
    const INVALID_PARAMETERS = "Invalid parameter";
    const INVALID_REVIEW_STATUS = "Invalid review status";
    const FAILED_TO_AUTHORIZE = "Failed to authorize";
}

/**============**/
/** Validators **/
/**============**/

class VideoValidator {

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

    /**
     * 验证描述
     * @param string $description
     * @return string
     * @throws Exception
     */
    public static function validateDescription($description) {
        $description = trim($description) or BasicTool::throwException("描述不能为空");
        (strlen($description)>0 && strlen($description)<=255) or BasicTool::throwException("描述长度不能超过255字节");
        return $description;
    }

    /**
     * 验证讲师
     * @param int|string $instructorId
     * @return int
     * @throws Exception
     */
    public static function validateInstructorId($instructorId) {
        $userModel = new UserModel();
        // TODO: check if user is a valid/approved instructor
        $userModel->getRowById('user', $instructorId) or BasicTool::throwException("未找到指定讲师Id");
        return intval($instructorId);
    }

    /**
     * 验证URL
     * @param $url
     * @return mixed
     * @throws Exception
     */
    public static function validateUrl($url) {
        // TODO: check if url is on bokecc
        $url or BasicTool::throwException("url不能为空");
        return $url;
    }

    /**
     * 验证价钱
     * @param float|double|string $price
     * @return float|int
     * @throws Exception
     */
    public static function validatePrice($price) {
        if($price === null){ BasicTool::throwException("价格不能为空"); }
        $price = floatval($price);
        $price>=0 or BasicTool::throwException("价格不能为负数");
        $price<=99999999.99 or BasicTool::throwException("价格不能大于 $99,999,999.99");
        return floor($price*100)/100;
    }

    /**
     * 验证视频章节ID
     * @param int|string $videoSectionId
     * @return int
     * @throws Exception
     */
    public static function validateVideoSectionId($videoSectionId) {
        $videoSectionId = intval($videoSectionId);
        $videoSectionId > 0 or BasicTool::throwException("视频章节ID不能为空");
        $videoSectionModel = new VideoSectionModel();
        $videoSectionModel->isExistByFieldValue("video_section", "id", $videoSectionId) or BasicTool::throwException("视频章节ID不存在");
        return $videoSectionId;
    }


    /**
     * 验证视频专辑ID
     * @param int|string $videoAlbumId
     * @return int
     * @throws Exception
     */
    public static function validateVideoAlbumId($videoAlbumId) {
        $videoAlbumId = intval($videoAlbumId);
        $videoAlbumId > 0 or BasicTool::throwException("视频专辑ID不能为空");
        $videoAlbumModel = new VideoAlbumModel();
        $videoAlbumModel->isExistByFieldValue("video_album", "id", $videoAlbumId) or BasicTool::throwException("视频专辑ID不存在");
        return $videoAlbumId;
    }



    /**
     * 验证封面图
     * @param int|string $imageId
     * @param string $uploadFilePath
     * @throws Exception
     */
    public static function validateCoverImage($imageId, $uploadFilePath="imgFile") {
        $imageModel = new ImageModel();
        if(!intval($imageId) && !$imageModel->getNumOfUploadImages($uploadFilePath)) {
            BasicTool::throwException("封面图片不能为空");
        }
    }
}


?>
