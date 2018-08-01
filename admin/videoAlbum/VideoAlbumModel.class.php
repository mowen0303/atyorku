<?php
namespace admin\videoAlbum;   //-- 注意 --//
use admin\image\ImageModel;
use admin\videoAlbumCategory\VideoAlbumCategoryModel;
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use admin\professor\ProfessorModel;
use admin\courseCode\CourseCodeModel;
use admin\institution\InstitutionModel;
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
        $videoAlbumSelect = "va.id, va.title, va.description, va.category_id, va.user_id AS user_id, va.institution_id, va.price, va.is_available, va.cover_img_id, va.publish_time, va.last_modified_time, va.count_video, va.count_participants, va.count_clicks, va.count_comments, va.sort";
        $videoAlbumCategorySelect = "vac.title AS video_album_category_title";
        $userSelect = "u.user_class_id, u.img, u.alias, u.gender, u.major, u.enroll_year, u.degree, uc.is_admin";
        $courseChildSelect = "c1.id AS course_code_child_id, c1.title AS course_code_child_title";
        $courseParentSelect = "c2.id AS course_code_parent_id, c2.title AS course_code_parent_title";
        $professorSelect = "p.id AS professor_id, CONCAT(p.firstname, ' ', p.lastname) AS prof_name";
        $imageSelect = "img.thumbnail_url AS thumbnail_url, img.height AS img_height, img.width AS img_width";

        $select = "SELECT {$videoAlbumSelect}, {$videoAlbumCategorySelect}, {$userSelect}, {$courseChildSelect}, {$courseParentSelect}, {$professorSelect}, {$imageSelect}";
        $from = "FROM {$this->table} va INNER JOIN video_album_category vac ON va.category_id=vac.id INNER JOIN user u ON va.user_id=u.id LEFT JOIN user_class uc ON u.user_class_id = uc.id LEFT JOIN course_code c1 ON va.course_code_id=c1.id LEFT JOIN course_code c2 ON c1.parent_id=c2.id LEFT JOIN professor p ON va.professor_id=p.id LEFT JOIN image img ON va.cover_img_id=img.id";
        $sql = "{$select} {$from} WHERE id = {$id}";

        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }

    /**
     * 获取一页课程专辑
     * SQL Table alias:
     *  video_album => va,
     *  video_album_category => vac,
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
     * @param string $order 排序
     * @return array
     */
    public function getListOfVideoAlbum($pageSize=20, $isDeleted=0, $isAvailable=null, $q="", $order="") {
        $videoAlbumSelect = "va.id, va.title, va.description, va.category_id, va.user_id AS user_id, va.institution_id, va.price, va.is_available, va.cover_img_id, va.publish_time, va.last_modified_time, va.count_video, va.count_participants, va.count_clicks, va.count_comments, va.sort";
        $videoAlbumCategorySelect = "vac.title AS video_album_category_title";
        $userSelect = "u.user_class_id, u.img, u.alias, u.gender, u.major, u.enroll_year, u.degree, uc.is_admin";
        $courseChildSelect = "c1.id AS course_code_child_id, c1.title AS course_code_child_title";
        $courseParentSelect = "c2.id AS course_code_parent_id, c2.title AS course_code_parent_title";
        $professorSelect = "p.id AS professor_id, CONCAT(p.firstname, ' ', p.lastname) AS prof_name";
        $imageSelect = "img.thumbnail_url AS thumbnail_url, img.height AS img_height, img.width AS img_width";

        $select = "SELECT {$videoAlbumSelect}, {$videoAlbumCategorySelect}, {$userSelect}, {$courseChildSelect}, {$courseParentSelect}, {$professorSelect}, {$imageSelect}";
        $from = "FROM {$this->table} va INNER JOIN video_album_category vac ON va.category_id=vac.id INNER JOIN user u ON va.user_id=u.id LEFT JOIN user_class uc ON u.user_class_id = uc.id LEFT JOIN course_code c1 ON va.course_code_id=c1.id LEFT JOIN course_code c2 ON c1.parent_id=c2.id LEFT JOIN professor p ON va.professor_id=p.id LEFT JOIN image img ON va.cover_img_id=img.id";

        $where = "";
        if ($isDeleted !== null) {
            $where .= "is_deleted = {$isDeleted}";
        }
        if ($isAvailable !== null) {
            if ($where) {
                $where .= " AND is_available = {$isAvailable}";
            } else {
                $where .= "is_available = {$isAvailable}";
            }
        }
        if ($q) {
            if ($where) {
                $where .= "{$q}";
            } else {
                $where .= " AND {$q}";
            }
        }

        $sql = "{$select} {$from}";
        $countSql = "SELECT COUNT(*) FROM {$this->table}";

        if($where){
            $sql .= " WHERE {$where}";
            $countSql .= " WHERE {$where}";
        }
        if($order){
            $sql .= " ORDER BY {$order}";
        }
        return $this->getListWithPage($this->table, $sql, $countSql, $pageSize);
    }


    /**
     * 添加一个课程专辑
     * @param $title
     * @param $description
     * @param $categoryId
     * @param $userId
     * @param $courseCodeId
     * @param $professorId
     * @param $institutionId
     * @param $price
     * @param int $coverImageId
     * @return bool
     */
    public function addVideoAlbum($title,
                                    $description,
                                    $categoryId,
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
        $arr["category_id"] = $categoryId;
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
            $vacm = new VideoAlbumCategoryModel();
            $vacm->updateVideoAlbumCategoryCount($categoryId);
        }
        return $bool;
    }


    /**
     * 更改一个课程专辑
     * @param $id
     * @param $title
     * @param $description
     * @param $categoryId
     * @param $userId
     * @param $courseCodeId
     * @param $professorId
     * @param $institutionId
     * @param $price
     * @param $isAvailable
     * @param int $coverImageId
     * @return bool
     */
    public function updateVideoAlbumById($id,
                                           $title,
                                           $description,
                                           $categoryId,
                                           $userId,
                                           $courseCodeId,
                                           $professorId,
                                           $institutionId,
                                           $price,
                                           $isAvailable,
                                           $coverImageId=0
    ) {
        $result = $this->getRawAlbumById($id);
        if($result){
            $oldCategoryId = $result["category_id"];
            $arr = [];
            $arr["id"] = $id;
            $arr["title"] = $title;
            $arr["description"] = $description;
            $arr["category_id"] = $categoryId;
            $arr["user_id"] = $userId;
            $arr["course_code_id"] = $courseCodeId;
            $arr["professor_id"] = $professorId;
            $arr["institution_id"] = $institutionId;
            $arr["price"] = $price;
            $arr["is_available"] = $isAvailable;
            $arr["cover_img_id"] = $coverImageId;

            $arr["last_modified_time"] = time();

            $bool = $this->updateRowById($this->table, $id, $arr);
            if ($bool && $oldCategoryId != $categoryId) {
                $vacm = new VideoAlbumCategoryModel();
                $vacm->updateVideoAlbumCategoryCount($categoryId);
                $vacm->updateVideoAlbumCategoryCount($oldCategoryId);
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
    public function deleteVideoAlbumById($id) {
        $id = intval($id);
        $result = $this->getRawAlbumById($id);
        if ($result && !intval($result["is_deleted"])) {
            $categoryId = $result["category_id"];
            $arr = ["is_deleted"=>1];
            $bool = $this->updateRowById($this->table, $id, $arr);
            if ($bool) {
                // update album category count
                $vacm = new VideoAlbumCategoryModel();
                $vacm->updateVideoAlbumCategoryCount($categoryId);
            }
            return true;
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
            $categoryId = $result['category_id'];
            $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
            $bool = $this->sqltool->query($sql);
            if ($bool && $result['is_deleted']) {
                // update album category count
                $vacm = new VideoAlbumCategoryModel();
                $vacm->updateVideoAlbumCategoryCount($categoryId);
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
    public function updateVideoAlbumCount($id){
        $id = intval($id);
        $sql = "UPDATE {$this->table} SET count_video = (SELECT COUNT(*) FROM video WHERE album_id = {$id} AND NOT is_deleted) WHERE id in ({$id})";
        return $this->sqltool->query($sql);
    }
}


/**========**/
/** Errors **/
/**========**/

abstract class VideoAlbumError {
    const ID_NOT_EXIST = "Video Album Id doesn't exist.";
    const FAILED_TO_UPDATE = "Failed to update";
}

/**============**/
/** Validators **/
/**============**/

class VideoAlbumValidator {

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
     * 验证是否上架
     * @param $isAvailable
     * @return int
     * @throws Exception
     */
    public static function validateIsAvailable($isAvailable) {
        $isAvailable !== null or BasicTool::throwException("可用状态不能为空");
        return intval($isAvailable == true);
    }

    /**
     * 验证机构
     * @param int|string $institutionId
     * @return int
     * @throws Exception
     */
    public static function validateInstitutionId($institutionId) {
        $institutionModel = new InstitutionModel();
        $institutionModel->getInstitution($institutionId) or BasicTool::throwException("未找到指定机构Id");
        return intval($institutionId);
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
     * 验证科目ID
     * @param string $parentCode
     * @param string $childCode
     * @return int
     * @throws Exception
     */
    public static function validateCourseId($parentCode,$childCode) {
        $parentCode = trim($parentCode) or BasicTool::throwException("所属学科大类不能为空");
        $childCode = trim($childCode) or BasicTool::throwException("所属学科课号不能为空");
        $courseCodeModel = new CourseCodeModel();
        $courseId = $courseCodeModel->getCourseIdByCourseCode($parentCode, $childCode) or BasicTool::throwException("未找到指定科目Id");
        return $courseId;
    }

    /**
     * 验证教授名称
     * @param string $profName
     * @return int
     * @throws Exception
     */
    public static function validateProfessorName($profName) {
        $profName = trim($profName);
        $profId = 0;
        if ($profName) {
            $professorModel = new ProfessorModel();
            $profId = $professorModel->getProfessorIdByFullName($profName) or BasicTool::throwException("教授名称格式错误");
        }
        return $profId;
    }


    /**
     * 验证课程专辑类别
     * @param int|string $videoAlbumCategoryId
     * @return int
     * @throws Exception
     */
    public static function validateVideoAlbumCategoryId($videoAlbumCategoryId) {
        $videoAlbumCategoryId = intval($videoAlbumCategoryId);
        $videoAlbumCategoryId > 0 or BasicTool::throwException("学习资料所属分类不能为空");
        $videoAlbumCategoryModel = new VideoAlbumCategoryModel();
        $videoAlbumCategoryModel->getVideoAlbumCategory($videoAlbumCategoryId) or BasicTool::throwException("课程专辑所属分类不存在");
        return $videoAlbumCategoryId;
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
