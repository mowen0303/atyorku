<?php
namespace admin\videoAlbum;   //-- 注意 --//
use admin\image\ImageModel;
use admin\videoAlbumTag\VideoAlbumTagModel;
use admin\professor\ProfessorModel;
use admin\courseCode\CourseCodeModel;
use admin\institution\InstitutionModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;


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
     * @param int|string $videoAlbumTagId
     * @return int
     * @throws Exception
     */
    public static function validateVideoAlbumTagId($videoAlbumTagId) {
        $videoAlbumTagId = intval($videoAlbumTagId);
        $videoAlbumTagId > 0 or BasicTool::throwException("学习资料所属分类不能为空");
        $videoAlbumTagModel = new VideoAlbumTagModel();
        $videoAlbumTagModel->getVideoAlbumTag($videoAlbumTagId) or BasicTool::throwException("课程专辑所属分类不存在");
        return $videoAlbumTagId;
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
