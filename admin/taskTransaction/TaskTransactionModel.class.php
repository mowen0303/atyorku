<?php
namespace admin\taskTransaction;   //-- 注意 --//
use admin\user\UserModel;
use admin\taskDesign\TaskDesignModel;
use admin\transaction\TransactionModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;


class TaskTransactionModel extends Model
{
    /**
     * Add a task transaction to a given user id.
     * @param $type     type of task transaction (the table name such as: [ book, course_rating, course_question, forum, knowledge ]
     * @param $userId   the user id to be assigned
     * @param $itemId   the item id that triggered this transaction, which matches the $type.  e.g. $type='book' -> $itemId='5'
     * @param $op       operation type: 'add' or 'delete'
     * @return bool
     * @throws Exception
     */
    public function addTaskTransaction($type,$userId,$itemId,$op)
    {
        // 权限验证
        $currentUser = new UserModel();
        if($currentUser->userId!==$userId && !$currentUser->isUserHasAuthority("ADMIN")){
            BasicTool::throwException("无权限添加成就交易");
        }

        $op = $this->validateOp($op);

        //插入数据
        $arr["task_type"] = $type;
        $arr["user_id"] = $userId;
        $arr["item_id"] = $itemId;
        $arr["op"] = $op;
        $arr["time"] = time();
        $bool = $this->addRow("task_transaction", $arr);
        if(!$bool) {BasicTool::throwException($this->errorMsg);}
        return $bool;
    }

    /**
     * Get List of Task transactions by a given user id.
     * @param $userId
     * @param int $pageSize
     * @return array
     * @throws Exception
     */
    public function getListOfTaskTransactionsByUserId($userId, $pageSize=20) {
        // 权限验证
        $currentUser = new UserModel();
        if($currentUser->userId !== intval($userId) && !$currentUser->isUserHasAuthority("ADMIN")){
            BasicTool::throwException("无权限查看成就交易");
        }

        $q = "t.user_id={$userId}";
        return $this->getListOfTaskTransactions($q, $pageSize);
    }

    /**
     * get summary of task transactions by user id
     * @param $userId given user id
     * @return array (structure: [[each task_type name]=>['total'=>'total count','add'=>'add count','delete'=>'delete count']])
     * @throws Exception
     */
    public function getSummaryOfTaskTransactionsByUserId($userId) {
        // 权限验证
        $currentUser = new UserModel();
        if($currentUser->userId!==intval($userId) && !$currentUser->isUserHasAuthority("ADMIN")){
            BasicTool::throwException("无权限查看他人成就总结");
        }

        $sql = "SELECT t.task_type, COUNT(*) AS type_count, SUM(op) AS total FROM (SELECT * FROM task_transaction WHERE user_id={$userId}) t GROUP BY t.task_type";

        $result = $this->sqltool->getListBySql($sql);
        $arr = [];
        if($result){
            foreach($result as $v) {
                $tp = $v['task_type'];
                $tc = intval($v['type_count']);
                $total = intval($v['total']);
                $add = ($tc+$total)/2;
                $delete = $tc - $add;
                $arr[$tp] = array('total'=>$total,'add'=>$add, 'delete'=>$delete);
            }
        }
        return $arr;
    }

    /**
     * 返回指定用户可以获取的所有成就设计
     * @param $userId
     * @return array
     * @throws Exception
     */
    public function getListOfAvailableTaskDesignsByUserId($userId){
        $result = $this->getSummaryOfTaskTransactionsByUserId($userId) or BasicTool::throwException("获取用户成就失败");

        // current user task summary
        $book = $result['book']['total'] ?: 0;
        $courseRating = $result['course_rating']['total'] ?: 0;
        $courseQuestion = $result['course_question']['total'] ?: 0;
        $forum = $result['forum']['total'] ?: 0;
        $knowledge = $result['knowledge']['total'] ?: 0;

        $select = "SELECT td.*, img.url AS icon_url FROM task_design td LEFT JOIN image img ON td.icon_id=img.id";
        $received = "SELECT task_design_id FROM task_received WHERE user_id={$userId}";
        $achieved = "book<={$book} AND course_rating<={$courseRating} AND course_question<={$courseQuestion} AND forum<={$forum} AND knowledge<={$knowledge}";
        $sql = "{$select} WHERE {$achieved} AND td.id NOT IN ({$received})";
        return $this->sqltool->getListBySql($sql);
    }


    /**
     * 指定一个用户获得一个成就设计
     * @param $userId
     * @param $taskId
     * @return bool
     * @throws Exception
     */
    public function obtainTaskDesign($userId,$taskId){
        $userId = intval($userId);
        $taskId = intval($taskId);
        $result = $this->getListOfAvailableTaskDesignsByUserId($userId);
        $bool = false;
        $task = null;
        if($result){
            foreach ($result as $v) {
                if($v['id'] && intval($v['id'])===intval($taskId)){
                    $bool = true;
                    $task = $v;
                    break;
                }
            }
        }

        if($bool) {
            $arr = [];
            $arr['user_id'] = intval($userId);
            $arr['task_design_id'] = intval($taskId);
            $arr['time'] = time();
            $bool = parent::addRow("task_received",$arr);
            $id = $this->idOfInsert;
            if($bool){
                $transactionModel = new TransactionModel();
                $transactionModel->addCredit($userId,$task['bonus'],"成就奖励: ".$task['title'], 'task_received',$id);
            }
        }

        return $bool;
    }

    /**
     * @param $userId
     * @return array
     * @throws Exception
     */
    public function getListOfReceivedTaskDesignByUserId($userId){
        $userId = intval($userId) or BasicTool::throwException("请先登录");
        $currentUser = new UserModel();
        if($currentUser->userId!==intval($userId) && !$currentUser->isUserHasAuthority("ADMIN")){
            BasicTool::throwException("无权限查看他人成就总结");
        }
        $sql = "SELECT td.*, img.url AS icon_url FROM task_received tr INNER JOIN task_design td ON tr.task_design_id=td.id LEFT JOIN image img ON td.icon_id=img.id WHERE tr.user_id={$userId}";
        return $this->sqltool->getListBySql($sql);
    }


    /********** Administration operations ***********/


    /**
     * Get List of Task transactions (ADMIN use only)
     * @param bool $query
     * @param int $pageSize
     * @return array
     */
    public function getListOfTaskTransactions($query=false, $pageSize=20) {
        $select = "SELECT t.*, u.name AS user_name, u.user_class_id, u.img AS user_img, u.alias AS user_alise, u.gender AS user_gender, u.major AS user_major, u.enroll_year AS user_enroll_year, u.degree AS user_degree, uc.is_admin";
        $from = "FROM (task_transaction t INNER JOIN `user` u ON t.user_id = u.id LEFT JOIN user_class uc ON u.user_class_id = uc.id)";
        $sql = "{$select} {$from}";
        $countSql = "SELECT COUNT(*) {$from}";
        if ($query) {
            $sql = "{$sql} WHERE ({$query})";
            $countSql = "{$countSql} WHERE ({$query})";
        }
        $sql = "{$sql} ORDER BY `time` DESC";
        $arr = parent::getListWithPage($this->table, $sql, $countSql, $pageSize);

        // Format publish time
        foreach ($arr as $k => $v) {
            $t = $v["time"];
            $op = intval($v["op"]);
            if($t) $arr[$k]["time"] = BasicTool::translateTime($t);
            if($op) $arr[$k]["op"] = $this->translateOp($op);

        }
        return $arr;
    }


    /**
     * 删除一条成就交易
     * @param $id
     * @return bool|\mysqli_result
     * @throws Exception
     */
    public function deleteTaskTransactionById($id){
        $sql = "SELECT * FROM task_transaction WHERE id={$id}";
        $result = $this->sqltool->getRowBySql($sql) or BasicTool::throwException("该成就交易不存在");
        $sql = "DELETE FROM task_transaction WHERE id in ({$id})";
        return $this->sqltool->query($sql);
    }


    /**
     * Validate a given task operation
     * @param $op
     * @return int
     * @throws Exception
     */
    private function validateOp($op){
        if($op === "add") return 1;
        else if($op === "delete") return -1;
        BasicTool::throwException("无效成就交易操作");
    }

    private function translateOp($op){
        if ($op === 1) return "添加";
        else if($op === -1) return "删除";
        else return "未知";
    }


}



?>