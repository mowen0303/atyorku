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
        $sql = "SELECT SUM(amount) AS credit FROM transaction WHERE user_id = {$user_id}";
        $result = $this->sqltool->getRowBySql($sql)["credit"];
        return (int)$result;
    }

    /**给用户添加积分
     * @param int $user_id 用户id
     * @param int $amount 积分
     * @param String $description 描述
     * @return bool
     */
    function addCredit($user_id, $amount, $description) {
        $amount = (float)$amount;
        if($amount < 0){
            $this->errorMsg = "积分值无效:{$amount}";
            return false;
        }else if($amount==0){
            return true;
        }
        if($this->addTransaction($user_id, $amount, $description)){
            return $this->setCredit($user_id, $this->getCredit($user_id));
        }else{
            $this->errorMsg = "添加积分记录失败";
            return false;
        }
    }

    function addCreditWithMultipleTransactions($user_ids, $amounts, $description) {
        $time = time();
        $concat = "";
        for ($i = 0; $i < count($user_ids); $i++) {
            $a = "({$user_ids[$i]},{$amounts[$i]},'{$description}',{$time}),";
            $concat = $concat . $a;
        }
        $concat = substr($concat, 0, -1);
        $sql = "INSERT INTO transaction (user_id,amount,description,time) VALUES {$concat}";
        $bool = $this->sqltool->query($sql);
        return $bool;
    }

    function systemAdjustCredit($userId,$creditSet){
        $credit = $creditSet['credit'];
        $description = $creditSet['description'];
        if($credit==0){
            $this->errorMsg = "积分值无效: {$credit}";
            return false;
        }
        return $this::addTransaction($userId,$credit,$description);
    }

    /**消耗用户的积分
     * @param int $user_id 用户id
     * @param int $amount 扣除的积分,positive
     * @param String $description 描述
     * @return bool
     */
    function deductCredit($user_id, $amount, $description) {
        $amount = (float)$amount;
        if($amount < 0){
            $this->errorMsg = "积分值无效:{$amount}";
            return false;
        }else if($amount==0){
            return true;
        }
        $credit = $this->getCredit($user_id);
        if($credit - $amount >= 0){
            $amount = -$amount;
            if($this->addTransaction($user_id, $amount, $description)){
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
     * @return bool|\mysqli_result
     */
    function buy($buyer_user_id, $seller_user_id, $amount, $buyer_description, $seller_description) {
        $amount = (float)$amount;
        if($amount<=0){
            $this->errorMsg = "积分值错误:{$amount}";
            return false;
        }
        //获取买家当前积分
        $buyerCredit = $this->getCredit($buyer_user_id);
        if($buyerCredit - $amount >= 0) {
            //如果卖家积分足够消费
            $time = time();
            $negative_amount = $amount * -1;
            $sql = "INSERT INTO transaction (user_id,amount,description,time) VALUES ({$buyer_user_id},{$negative_amount},'{$buyer_description}',{$time}),({$seller_user_id},{$amount},'{$seller_description}',{$time})";
            $bool = $this->sqltool->query($sql);
            if ($bool) {
                $this->setCredit($buyer_user_id, $this->getCredit($buyer_user_id));
                $this->setCredit($seller_user_id, $this->getCredit($seller_user_id));
                return true;
            }
        }else{
            $this->errorMsg = "购买失败:积分不足,你当前只有{$buyerCredit}点积分.";
            return false;
        }
    }

    function getTransactions() {
        $sql = "SELECT * FROM transaction";
        $countSql = "SELECT COUNT(*) FROM transaction";
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

    public function clearCredit($user_id,$reason){
        $amount = $this->getCredit($user_id);
        return $this->deductCredit($user_id,$amount,"积分清零: {$reason}");
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
     * @return bool
     */
    private function addTransaction($user_id, $amount, $description) {
        if ((int)$amount == 0) {
            $this->errorMsg = "积分值无效:{$amount}";
            return false;
        }
        $arr["user_id"] = $user_id;
        $arr["amount"] = $amount;
        $arr["description"] = $description ? $description : "";
        $arr["time"] = time();
        return $this->addRow("transaction", $arr);
    }
}

?>