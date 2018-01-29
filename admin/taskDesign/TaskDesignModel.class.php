<?php
namespace admin\taskDesign;   //-- 注意 --//
use admin\user\UserModel;
use admin\image\ImageModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;


class TaskDesignModel extends Model
{

    public function getTaskDesignById($id){
        $id = intval($id) or BasicTool::throwException("必须提供成就设计ID");
        $sql = "SELECT td.*, img.url AS icon_url FROM task_design td LEFT JOIN image img ON  td.icon_id=img.id WHERE td.id={$id}";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
     * 添加一个成就设计
     * @param $title
     * @param $bonus
     * @param $iconId
     * @param $book
     * @param $courseRating
     * @param $courseQuestion
     * @param $forum
     * @param $knowledge
     * @return bool
     * @throws Exception
     */
    public function addTaskDesign($title,$bonus,$iconId,$book,$courseRating,$courseQuestion,$forum,$knowledge)
    {
        // 权限验证
        $this->validateUser("无权限添加成就设计");

        // Parameter验证
        $title or BasicTool::throwException("需提供成就Title");
        $iconId or BasicTool::throwException("需提供图标URL");
        $bonus = intval($bonus);
        $bonus>0 or BasicTool::throwException("奖励必须大于0");

        $book = intval($book) ?: 0;
        $courseRating = intval($courseRating) ?: 0;
        $courseQuestion = intval($courseQuestion) ?: 0;
        $forum = intval($forum) ?: 0;
        $knowledge = intval($knowledge) ?: 0;

        //插入数据
        $arr["title"] = $title;
        $arr["bonus"] = $bonus;
        $arr["icon_id"] = $iconId;
        $arr["book"] = $book;
        $arr["course_rating"] = $courseRating;
        $arr["course_question"] = $courseQuestion;
        $arr["forum"] = $forum;
        $arr["knowledge"] = $knowledge;

        $bool = $this->addRow("task_design", $arr);
        if(!$bool) {BasicTool::throwException($this->errorMsg);}
        return $bool;
    }

    public function updateTaskDesign($id,$title,$bonus,$iconId,$book,$courseRating,$courseQuestion,$forum,$knowledge) {
        // 权限验证
        $this->validateUser("无权限添加成就设计");
        $id = intval($id) or BasicTool::throwException("id不能为空");
        $sql = "SELECT * FROM task_design WHERE id={$id}";
        $result = $this->sqltool->getRowBySql($sql) or BasicTool::throwException("该成就设计不存在");

        // Parameter验证
        $title or BasicTool::throwException("需提供成就Title");
        $iconId or BasicTool::throwException("需要上传图标");
        $bonus = intval($bonus);
        $bonus>0 or BasicTool::throwException("奖励必须大于0");

        $book = intval($book) ?: 0;
        $courseRating = intval($courseRating) ?: 0;
        $courseQuestion = intval($courseQuestion) ?: 0;
        $forum = intval($forum) ?: 0;
        $knowledge = intval($knowledge) ?: 0;

        //插入数据
        $arr["title"] = $title;
        $arr["bonus"] = $bonus;
        $arr["icon_id"] = $iconId;
        $arr["book"] = $book;
        $arr["course_rating"] = $courseRating;
        $arr["course_question"] = $courseQuestion;
        $arr["forum"] = $forum;
        $arr["knowledge"] = $knowledge;

        $bool = parent::updateRowById("task_design", $id, $arr);
        if(!$bool) {BasicTool::throwException($this->errorMsg);}
        return $bool;

    }

    /**
     * 获取一页成就设计
     * @param int $pageSize
     * @return array
     */
    public function getListOfTaskDesigns($pageSize=20) {
        $sql = "SELECT td.*, img.url AS icon_url FROM task_design td LEFT JOIN image img ON td.icon_id=img.id";
        $countSql = "SELECT COUNT(*) FROM task_design td LEFT JOIN image img ON td.icon_id=img.id";

        $arr = parent::getListWithPage("task_design", $sql, $countSql, $pageSize);
        return $arr;
    }

    /**
     * 删除一个成就设计
     * @param $id
     * @return bool|\mysqli_result
     * @throws Exception
     */
    public function deleteTaskDesignById($id) {
        $this->validateUser("无权限删除成就设计");
        $sql = "SELECT * FROM task_design WHERE id={$id}";
        $result = $this->sqltool->getRowBySql($sql) or BasicTool::throwException("该成就设计不存在");
        $iconId = $result['icon_id'];
        if($iconId){
            $imageModel = new ImageModel();
            $imageModel->deleteImageById($iconId) or BasicTool::throwException("删除失败");
        }

        $sql = "DELETE FROM task_design WHERE id in ({$id})";
        return $this->sqltool->query($sql);

    }

    private function validateUser($msg){
        // 权限验证
        $currentUser = new UserModel();
        if(!$currentUser->isUserHasAuthority("ADMIN") && !$currentUser->isUserHasAuthority("TASK_DESIGN")){
            BasicTool::throwException($msg?:"无权限");
        }
    }


}



?>