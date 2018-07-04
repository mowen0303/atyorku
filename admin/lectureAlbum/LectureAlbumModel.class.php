<?php
namespace admin\lectureAlbum;   //-- 注意 --//
use admin\lectureAlbumCategory\LectureAlbumCategoryModel;
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class LectureAlbumModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = "lecture_album";
    }

    /**
     * 添加一个课程专辑
     * @param $title
     * @param $categoryId
     * @param $userId
     * @param $courseCodeId
     * @param $professorId
     * @param $year
     * @param $term
     * @param $price
     * @param $expiration
     * @param string $description
     * @param int $coverImageId
     * @param int $isAvailable
     * @return bool
     */
    public function addLectureAlbum($title, $categoryId, $userId, $courseCodeId, $professorId, $year, $term, $price, $expiration, $description="", $coverImageId=0, $isAvailable=1)
    {
        $arr = [];
        $arr["title"] = $title;
        $arr["category_id"] = $categoryId;
        $arr["user_id"] = $userId;
        $arr["course_code_id"] = $courseCodeId;
        $arr["professor_id"] = $professorId;
        $arr["year"] = $year;
        $arr["term"] = $term;
        $arr["price"] = $price;
        $arr["expiration"] = $expiration;
        $arr["description"] = $description;
        $arr["is_available"] = $isAvailable;
        $arr["cover_img_id"] = $coverImageId;

        return $this->addRow($this->table, $arr);
    }

    /**
     * 查询一个课程专辑,返回一维键值数组
     *
     * @param $id
     * @return \一维关联数组
     */
    public function getLectureAlbum($id)
    {
        $sql = "SELECT * from {$this->table} WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }

    /**
     * 获取一页课程专辑
     * SQL Table alias:
     *  lecture_album => la,
     *  lecture_album_category => lac,
     *  user => u,
     *  user_class => uc,
     *  course_code (child) => c1,
     *  course_code (parent) => c2,
     *  professor => p,
     *  image => img
     * @param string $q 检索
     * @param string $order 排序
     * @param int $pageSize
     * @return array
     */
    public function getListOfLectureAlbums($q="",$order="",$pageSize=20) {
      $sql = "SELECT la.id, la.title, la.description, la.category_id, la.user_id AS userId, la.professor_id, la.year, la.term, la.price, la.expiration, la.is_available, la.cover_img_id, la.num_lectures, lac.title AS category_title, u.user_class_id,u.img,u.alias,u.gender,u.major,u.enroll_year,u.degree,uc.is_admin, c1.id AS course_code_child_id, c1.title AS course_code_child_title, c2.id AS course_code_parent_id, c2.title AS course_code_parent_title, CONCAT(p.firstname, ' ', p.lastname) AS prof_name, img.thumbnail_url AS thumbnail_url, img.height AS img_height, img.width AS img_width FROM {$this->table} la INNER JOIN lecture_album_category lac ON la.category_id=lac.id INNER JOIN user u ON la.user_id=u.id LEFT JOIN user_class uc ON u.user_class_id = uc.id LEFT JOIN course_code c1 ON la.course_code_id=c1.id LEFT JOIN course_code c2 ON c1.parent_id=c2.id LEFT JOIN professor p ON la.professor_id=p.id LEFT JOIN image img ON la.cover_img_id==img.id";
      $countSql = "SELECT COUNT(*) FROM {$this->table}";
      if($q){
          $sql .= " WHERE {$q}";
          $countSql .= " WHERE {$q}";
      }
      if($order){
          $sql .= " ORDER BY {$order}";
      }
      return $this->getListWithPage($this->table, $sql, $countSql, $pageSize);
    }


    /**
     * 更改一个课程专辑
     * @param $id
     * @param $title
     * @param $categoryId
     * @param $userId
     * @param $courseCodeId
     * @param $professorId
     * @param $year
     * @param $term
     * @param $price
     * @param $expiration
     * @param string $description
     * @param int $coverImageId
     * @param int $isAvailable
     * @return bool
     */
    public function updateLectureAlbumById($id, $title, $categoryId, $userId, $courseCodeId, $professorId, $year, $term, $price, $expiration, $description="", $coverImageId=0, $isAvailable=1)
    {
        $result = $this->getAlbumByIdWithNoJoin($id);
        if($result){
            $arr = [];
            $arr["title"] = $title;
            $arr["category_id"] = $categoryId;
            $arr["user_id"] = $userId;
            $arr["course_code_id"] = $courseCodeId;
            $arr["professor_id"] = $professorId;
            $arr["year"] = $year;
            $arr["term"] = $term;
            $arr["price"] = $price;
            $arr["expiration"] = $expiration;
            $arr["description"] = $description;
            $arr["is_available"] = $isAvailable;
            $arr["cover_img_id"] = $coverImageId;
            return $this->updateRowById($this->table, $id, $arr);
        }else{
            $this->errorMsg = LectureAlbumError::ID_NOT_EXIST;
            return false;
        }
    }


    /**
     * 逻辑删除一个课程专辑
     * @param int|string $id 课程专辑ID
     * @return bool
     */
    public function deleteLectureAlbumLogicallyById($id) {
        $result = $this->getAlbumByIdWithNoJoin($id);
        if ($result && !$result['is_deleted']) {
            $categoryId = $result["category_id"];
            $sql = "UPDATE {$this->table} SET is_deleted=1 WHERE id in ({$id})";
            $bool = $this->sqltool->query($sql);
            if ($bool) {
                // update category album count
                $lectureAlbumCategoryModel = new LectureAlbumCategoryModel();
                $lectureAlbumCategoryModel->updateLectureAlbumCategoryCount($categoryId);
            }
            return true;
        } else {
            $this->errorMsg = LectureAlbumError::ID_NOT_EXIST;
        }
        return false;
    }

    /**
     * 删除一个课程专辑
     * @param $id
     * @return bool|\mysqli_result
     */
    public function deleteLectureAlbumById($id)
    {
        $result = $this->getAlbumByIdWithNoJoin($id);
        if($result){
            $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
            return $this->sqltool->query($sql);
        } else {
            $this->errorMsg = LectureAlbumError::ID_NOT_EXIST;
        }
        return false;
    }


    /**
     * 更新对应课程专辑数量
     * @param int|string $id 课程专辑ID
     * @return bool|\mysqli_result
     */
    public function updateLectureAlbumCount($id){
        $id = intval($id);
        $sql = "UPDATE {$this->table} SET count = (SELECT COUNT(*) FROM lecture_album WHERE category_id in ({$id}) AND NOT is_deleted AND is_available) WHERE id in ({$id})";
        return $this->sqltool->query($sql);
    }



    private function getAlbumByIdWithNoJoin($id){
        return $this->getRowById($this->table, intval($id));
    }
}

abstract class LectureAlbumError {
    const ID_NOT_EXIST = "Lecture Album Id doesn't exist.";
    const FAILED_TO_UPDATE = "Failed to update";
}

?>
