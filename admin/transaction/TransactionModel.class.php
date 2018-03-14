<?php
namespace admin\transaction;   //-- 注意 --//

use \Model as Model;

class TransactionModel extends Model {
    /**查询某个用户的积分记录
     * @param int $user_id
     * @return array
     */
    function getTransactionsByUserId($user_id) {
        $sql = "SELECT * FROM transaction WHERE user_id IN ({$user_id}) ORDER BY time desc";
        $countSql = "SELECT COUNT(*) FROM transaction WHERE user_id IN ({$user_id}) ORDER BY time desc";
        return $this->getListWithPage("transaction", $sql, $countSql, 30);
    }

    /**查询某个用户的积分
     * @param int $user_id 用户id
     * @return mixed
     */
    function getCredit($user_id) {
        $sql = "SELECT SUM(amount) AS credit FROM transaction WHERE user_id = {$user_id} AND pending = 0";
        $result = $this->sqltool->getRowBySql($sql)["credit"];
        return (float)$result;
    }

    /**给用户添加积分
     * @param int $user_id 用户id
     * @param int $amount 积分
     * @param String $description 描述
     * @param String $section_name 表名
     * @param int $section_id 表里的id
     * @param int $pending 0 or 1
     * @return bool
     */
    function addCredit($user_id, $amount, $description,$section_name,$section_id,$pending=0) {
        $amount = (float)$amount;
        if($amount < 0){
            $this->errorMsg = "积分值无效:{$amount}";
            return false;
        }else if($amount==0){
            return true;
        }
        if($this->addTransaction($user_id, $amount, $description,$section_name,$section_id,$pending)){
            return $this->setCredit($user_id, $this->getCredit($user_id));
        }else{
            $this->errorMsg = "添加积分记录失败";
            return false;
        }
    }

    function addCreditWithMultipleTransactions($user_ids, $ids, $amounts, $description) {
        $time = time();
        $concat = "";
        for ($i = 0; $i < count($user_ids); $i++) {
            $a = "({$user_ids[$i]},{$amounts[$i]},'{$description}',{$time},'course_question',{$ids[$i]},0),";
            $concat = $concat . $a;
        }
        $concat = substr($concat, 0, -1);
        $sql = "INSERT INTO transaction (user_id,amount,description,time,section_name,section_id,pending) VALUES {$concat}";
        $bool = $this->sqltool->query($sql);
        return $bool;
    }

    function systemAdjustCredit($userId,$creditSet,$section_name,$section_id){
        $credit = $creditSet['credit'];
        $description = $creditSet['description'];
        if($credit==0){
            $this->errorMsg = "积分值无效: {$credit}";
            return false;
        }
        if($this::addTransaction($userId,$credit,$description,$section_name,$section_id,0)){
            return $this->setCredit($userId, $this->getCredit($userId));
        }else{
            $this->errorMsg = "添加积分记录失败[systemAdjustCredit]";
            return false;
        }
    }

    /**消耗用户的积分
     * @param int $user_id 用户id
     * @param int $amount 扣除的积分,positive
     * @param String $description 描述
     * @param String $section_name 表名
     * @param int $section_id 表里的id
     * @param int $pending 0 or 1
     * @return bool
     */
    function deductCredit($user_id, $amount, $description,$section_name,$section_id,$pending = 0) {
        $amount = (float)abs($amount);
        if($amount==0){
            return true;
        }
        $credit = $this->getCredit($user_id);
        if($credit - $amount >= 0){
            $amount = -$amount;
            if($this->addTransaction($user_id, $amount, $description,$section_name,$section_id,$pending)){
                return $this->setCredit($user_id, $this->getCredit($user_id));
            }else{
                $this->errorMsg = "积分记录添加失败";
                return false;
            }
        }else{
            $this->errorMsg = "积分不足,你的当前积分:{$credit}";
            return false;
        }
    }

    /**购买
     * @param $buyer_user_id
     * @param $seller_user_id
     * @param $amount
     * @param $buyer_description
     * @param $seller_description
     * @param String $section_name 表名
     * @param int $section_id 表里的id
     * @param int $buyer_pending
     * @param int $seller_pending
     * @return bool|array
     */
    function buy($buyer_user_id, $seller_user_id, $amount, $buyer_description, $seller_description,$section_name,$section_id,$buyer_pending = 0,$seller_pending = 0) {
        $result = [];
        $amount = (float)$amount;
        if($buyer_user_id == $seller_user_id){
            $this->errorMsg = "购买失败,buyer_id = seller_id";
            return false;
        }
        if($amount<=0){
            $this->errorMsg = "积分值错误:{$amount}";
            return false;
        }
        if ($buyer_pending != 0 && $buyer_pending != 1){
            $this->errorMsg ="买家pending无效:{$buyer_pending}";
        }
        if ($seller_pending != 0 && $seller_pending != 1){
            $this->errorMsg = "卖家pending无效:{$seller_pending}";
        }
        //获取买家当前积分
        $buyerCredit = $this->getCredit($buyer_user_id);
        if($buyerCredit - $amount >= 0) {
            //如果卖家积分足够消费
            $time = time();
            $negative_amount = $amount * -1;
            $sql = "INSERT INTO transaction (user_id,amount,description,pending,time,section_name,section_id) VALUES ({$buyer_user_id},{$negative_amount},'{$buyer_description}',{$buyer_pending},{$time},'{$section_name}',{$section_id}),({$seller_user_id},{$amount},'{$seller_description}',{$seller_pending},{$time},'{$section_name}',{$section_id})";
            $bool = $this->sqltool->query($sql);
            if ($bool) {
                $result["buyer_transaction_id"] = $this->getInsertId();
                $result["seller_transaction_id"] = $result["buyer_transaction_id"] + 1;
                $this->setCredit($buyer_user_id, $this->getCredit($buyer_user_id));
                $this->setCredit($seller_user_id, $this->getCredit($seller_user_id));
                return $result;
            }
            else{
                $this->errorMsg = "网络异常,交易失败";
                return false;
            }
        }else{
            $this->errorMsg = "购买失败:积分不足,你当前只有{$buyerCredit}点积分.";
            return false;
        }
    }

    function getTransactions($uid = 0) {
        $condition = " WHERE ";
        if($uid != 0){
            $condition .= "user_id in ({$uid}) ";
        }else{
            $condition .= "true ";
        }
        $sql = "SELECT * FROM transaction {$condition} ORDER BY id DESC";
        $countSql = "SELECT COUNT(*) FROM transaction {$condition} ORDER BY id DESC";
        $result = $this->getListWithPage("transaction", $sql, $countSql, 30);
        return $result;
    }

    /**判断用户是否有足够积分进行消耗
     * @param int $user_id 用户id
     * @param int $amount 扣除的积分
     * @return bool
     */
    public function isCreditDeductible($user_id, $amount) {
        $amount = (float)$amount;
        if($amount < 0){
            $this->errorMsg = "积分值无效:{$amount}";
            return false;
        }else if($amount==0){
            return true;
        }
        $credit = $this->getCredit($user_id);
        if($credit - $amount >= 0){
            return true;
        }else{
            $this->errorMsg = "积分不足, 你的当前积分为[{$credit}]点,需要消耗的积分为[{$amount}]点";
            return false;
        }
    }

    public function clearCredit($user_id,$reason,$section_name,$section_id){
        $amount = $this->getCredit($user_id);
        return $this->deductCredit($user_id,$amount,"积分清零: {$reason}",$section_name,$section_id,0);
    }

    /**把积分写进user表
     * @param int $user_id 用户id
     * @param int $credit 用户当前积分
     * @return bool|\mysqli_result
     */
    private function setCredit($user_id, $credit) {
        $sql = "UPDATE user SET credit = {$credit} WHERE id={$user_id}";
        return $this->sqltool->query($sql);
    }

    /**添加一个积分记录
     * @param int $user_id 用户id
     * @param int $amount 积分
     * @param String $description 描述
     * @param String $section_name 表名
     * @param int $section_id 表里的id
     * @param int $pending 0 or 1
     * @return bool
     */
    private function addTransaction($user_id, $amount, $description,$section_name,$section_id,$pending = 0) {
        if ($amount == 0) {
            $this->errorMsg = "积分值无效:{$amount}";
            return false;
        }
        if ($pending != 0 && $pending != 1){
            $this->errorMsg = "Pending无效:{$pending}";
            return false;
        }
        $arr["user_id"] = $user_id;
        $arr["amount"] = $amount;
        $arr["description"] = $description ? $description : "";
        $arr["section_name"] = $section_name ? $section_name : "";
        $arr["section_id"] = $section_id;
        $arr["pending"] = $pending;
        $arr["time"] = time();
        return $this->addRow("transaction", $arr);
    }

    /**篡改指定的transaction的pending值
     * @param int $transaction_id
     * @param int $pending 0 or 1
     * @return bool
     */
    function setPending($transaction_id,$pending){
        if ($pending != 0 && $pending != 1){
            $this->errorMsg = "pending无效:{$pending}";
            return false;
        }
        $sql = "UPDATE transaction SET pending = {$pending} WHERE id = {$transaction_id}";
        return $this->sqltool->query($sql);
    }
    function getInsertId() {
        return $this->sqltool->getInsertId();
    }
    function isPurchased($user_id,$section_name,$section_id){
        $sql = "SELECT COUNT(*) AS count FROM transaction WHERE user_id in ({$user_id}) AND section_name in ({$section_name}) AND section_id in ({$section_id})";
        $count = $this->sqltool->getRowBySql($sql);
        if ($count)
            return true;
        else
            return false;
    }
}

?>
