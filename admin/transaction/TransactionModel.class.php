<?php
namespace admin\transaction;   //-- 注意 --//

use \Model as Model;

class TransactionModel extends Model
{
    function getTransactionsByUserId($user_id){
        $sql = "SELECT * FROM transaction WHERE user_id = {$user_id} SORT BY time desc";
        $countSql = "SELECT COUNT(*) FROM transaction WHERE user_id = {$user_id} SORT BY time desc";
        return $this->getListWithPage("transaction",$sql,$countSql,30);
    }
    function addTransaction($user_id, $amount, $description)
    {
        $arr["user_id"] = $user_id;
        $arr["amount"] = $amount;
        $arr["description"] = $description;
        $arr["time"] = time();
        return $this->addRow("transaction", $arr);
    }

    /*
     * @return $credit or false if query failed
     */
    function getCredit($user_id)
    {
        $sql = "SELECT SUM(amount) AS credit FROM transaction WHERE user_id = {$user_id}";
        $result = $this->sqltool->getRowBySql($sql)["credit"];
        return $result;
    }
    /*到user表下更改credit
     * @return true/false
     */
    function setCredit($user_id,$credit){
        $sql = "UPDATE user SET credit = {$credit} WHERE id={$user_id}";
        return $this->sqltool->query($sql);
    }

    /*判断用户是否有足够的积分进行消耗
     *
     * @return true/false
     */
    function isCreditDeductible($user_id,$amount){
        $credit = $this->getCredit($user_id);
        return $credit - $amount >= 0;
    }

    /*给用户加积分
     *@para $amount is positive
     * @return true/false
     */
    function addCredit($user_id,$amount,$description){
        $bool = $this->addTransaction($user_id,$amount,$description);
        if ($bool){
            $this->setCredit($user_id,$this->getCredit($user_id));
        }
        return $bool;
    }

    function addCreditWithMultipleTransactions($user_ids,$amounts,$description){
        $time = time();
        $concat = "";
        for($i=0;$i<count($user_ids);$i++){
            $a = "({$user_ids[$i]},{$amounts[$i]},'{$description}',{$time}),";
            $concat = $concat.$a;
        }
        $concat = substr($concat, 0, -1);
        $sql = "INSERT INTO transaction (user_id,amount,description,time) VALUES {$concat}";
        $bool = $this->sqltool->query($sql);
        return $bool;
    }
    /*@param $amount is positive
     *@return true/false
     */
    function deductCredit($user_id,$amount,$description){
        $bool = $this->isCreditDeductible($user_id,$amount);
        if ($bool){
            $bool = $this->addTransaction($user_id,$amount*-1,$description);
        }
        if ($bool){
            $this->setCredit($user_id,$this->getCredit($user_id));
        }
        return $bool;
    }
    /*
     * @return true/false
     */
    function buy($buyer_user_id,$seller_user_id,$amount,$buyer_description,$seller_description){
        //判断买家积分是否足够消耗
        $bool = $this->isCreditDeductible($buyer_user_id,$amount);

        if ($bool){
            $time = time();
            $negative_amount = $amount * -1;
            $sql = "INSERT INTO transaction (user_id,amount,description,time) VALUES ({$buyer_user_id},{$negative_amount},'{$buyer_description}',{$time}),({$seller_user_id},{$amount},'{$seller_description}',{$time})";
            $bool = $this->sqltool->query($sql);
        }
        if ($bool){
            $this->setCredit($buyer_user_id,$this->getCredit($buyer_user_id));
            $this->setCredit($seller_user_id,$this->getCredit($seller_user_id));
        }
        return $bool;
    }
    function getTransactions(){
        $sql = "SELECT * FROM transaction";
        $countSql = "SELECT COUNT(*) FROM transaction";
        $result = $this->getListWithPage("transaction",$sql,$countSql,30);
        return $result;
    }
}

?>