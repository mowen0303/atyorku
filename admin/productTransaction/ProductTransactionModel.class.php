<?php
namespace admin\productTransaction;
use \Model as Model;
use admin\transaction\TransactionModel as TransactionModel;
use \BasicTool as BasicTool;
use \Exception as Exception;


class ProductTransactionModel extends Model {

    private $sectionName;       // 产品表名
    private $daysOfPayment;     // 等待确认付款天数 (卖家拒绝退款后确认付款天数与其一致）
    private $daysOfRefund;      // 等待退款天数

    /**
     * ProductTransactionModel constructor.
     * @param $sectionName 产品表名
     * @param $daysOfPayment 等待确认付款天数
     * @param $daysOfRefund 等待退款天数
     */
    public function __construct($sectionName, $daysOfPayment, $daysOfRefund)
    {
        parent::__construct();
        $this->sectionName = $sectionName;
        $this->daysOfPayment = $daysOfPayment;
        $this->daysOfRefund = $daysOfRefund;
        $this->table = "product_transaction";
    }



    /**********************************/
    /*          Query Methods         */
    /**********************************/


    /**
     * Get a product_transaction by id
     * @param $id 产品交易ID
     * @param bool $extendSelect 外加的选择SQL，用逗号隔开 {交易表名: pt | 卖家交易表名: st | 买家交易表名: bt}
     * @param bool $extendFrom 外加的Join表SQL，用JOIN连接表名 ex: 'user u INNER JOIN user_class ON ...'
     * @return \一维关联数组
     */
    public function getTransactionById($id, $extendSelect=false, $extendFrom=false) {
        $id = intval($id);
        $select = "SELECT pt.*, st.amount AS seller_amount, st.user_id AS seller_id, st.section_id, st.pending AS seller_pending, st.description AS seller_description, bt.user_id AS buyer_id, bt.amount AS buyer_amount, bt.pending AS buyer_pending, bt.time AS purchased_time, bt.description AS buyer_description";
        if($extendSelect) {
            $select .= ",{$extendSelect}";
        }
        $from = "FROM (product_transaction pt INNER JOIN transaction bt ON pt.buyer_transaction_id=bt.id INNER JOIN transaction st ON pt.seller_transaction_id=st.id)";
        if($extendFrom) {
            $from .= " {$extendFrom}";
        }
        $where = "WHERE pt.id={$id}";

        $sql = "{$select} {$from} {$where}";
        return $this->sqltool->getRowBySql($sql);
    }


    /**
     * Get a list of transactions
     * @param int $pageSize
     * @param bool $query 外加的索引
     * @param bool $order 排序Sql
     * @param bool $extendSelect 外加的选择SQL，用逗号隔开 {交易表名: pt | 卖家交易表名: st | 买家交易表名: bt}
     * @param bool $extendFrom 外加的Join表SQL，用JOIN连接表名 ex: 'user u INNER JOIN user_class ON ...'
     * @return array
     */
    public function getListOfTransactions($pageSize=20, $query=false, $order=false, $extendSelect=false, $extendFrom=false){
        $select = "SELECT pt.*, st.amount AS seller_amount, st.user_id AS seller_id, st.section_id, st.pending AS seller_pending, st.description AS seller_description, bt.user_id AS buyer_id, bt.amount AS buyer_amount, bt.pending AS buyer_pending, bt.time AS purchased_time, bt.description AS buyer_description";
        if($extendSelect) {
            $select .= ",{$extendSelect}";
        }
        $from = "FROM (product_transaction pt INNER JOIN transaction bt ON pt.buyer_transaction_id=bt.id INNER JOIN transaction st ON pt.seller_transaction_id=st.id)";
        if($extendFrom) {
            $from .= " {$extendFrom}";
        }
        $where = $query ? " WHERE {$query}" : "";
        $sql = "{$select} {$from}";
        $countSql = "SELECT COUNT(*) {$from}";
        if($where){
            $sql .= $where;
            $countSql .= $where;
        }
        if($order){
            $sql .= " ORDER BY {$order}";
        }
        return $this->getListWithPage("product_transaction",$sql,$countSql,$pageSize);
    }


    /**
     * 获取一页指定买家的产品销售
     * @param $userId 买家ID
     * @param int $pageSize
     * @param bool $order 排序Sql
     * @return array
     */
    public function getListOfPurchasedTransactionsByUserId($userId, $pageSize=20, $order=false){
        $userId = intval($userId);
        $q = "bt.user_id={$userId}";
        return $this->getListOfTransactions($pageSize, $q, $order);
    }

    /**
     * 获取一页指定卖家的产品销售交易
     * @param $userId 卖家ID
     * @param int $pendingOption pending交易获取选项 {-1:全选, 0:仅限已完成的交易, 1:仅限未完成的交易}
     * @param int $pageSize
     * @param bool $order 排序Sql
     * @return array
     */
    public function getListOfSoldTransactionsByUserId($userId, $pendingOption=-1, $pageSize=20, $order=false){
        $userId = intval($userId);
        $q = "st.user_id={$userId}";
        if($pendingOption===0 || $pendingOption===1){
            $q .= " AND st.pending={$pendingOption}";
        }
        return $this->getListOfTransactions($pageSize, $q, $order);
    }




    /**********************************/
    /*        Command Methods         */
    /**********************************/


    /**
     * Buy a product
     * @param $buyer_user_id 买家ID
     * @param $seller_user_id 卖家ID
     * @param $amount 价格
     * @param $buyer_description 买家描述
     * @param $seller_description 卖家描述
     * @param $section_id 产品ID
     * @return bool
     */
    public function buy($buyer_user_id, $seller_user_id, $amount, $buyer_description, $seller_description, $section_id) {
        $transactionModel = new TransactionModel();
        $result = $transactionModel->buy($buyer_user_id, $seller_user_id, $amount, $buyer_description, $seller_description, $this->sectionName, $section_id, 0, 1);
        if($result){
            $buyerTransId = $result['buyer_transaction_id'];
            $sellerTransId = $result['seller_transaction_id'];
            $arr = [
                "buyer_transaction_id"=>$buyerTransId,
                "seller_transaction_id"=>$sellerTransId,
                "state"=>ProductTransactionState::WAITING_PAYMENT,
                "update_time"=>time()
            ];
            return $this->addRow($this->table, $arr);
        }

        return false;
    }

    /**
     * 买家收款
     * @param $id 产品交易ID
     * @param $reason 描述
     * @return bool
     */
    public function completeTransactionByBuyer($id, $reason="买家已确认付款") {
        return $this->completeTransaction($id,ProductTransactionAction::BUYER_ACTION,$reason);
    }

    /**
     * 自动收款
     * @param $id 产品交易ID
     * @param $reason 描述
     * @return bool
     */
    public function completeTransactionAutomatically($id, $reason="自动确认付款") {
        return $this->completeTransaction($id,ProductTransactionAction::AUTO_ACTION,$reason);
    }

    /**
     * 向卖家申请退款
     * @param $id 产品交易ID
     * @param $reason 描述
     * @return bool
     */
    public function applyRefundToSeller($id, $reason){
        try {
            $id = intval($id) or BasicTool::throwException("产品交易ID不能为空");
            $reason or BasicTool::throwException("产品交易原因不能为空");
            $reason = mysqli_real_escape_string($this->sqltool->mysqli,$reason);
            $result = $this->getTransactionById($id) or BasicTool::throwException("没有找到该产品交易");
            $state = $result['state'];
            $updateTime = intval($result['update_time']);
            if(!intval($result['seller_pending']) ||
                $state !== ProductTransactionState::WAITING_PAYMENT) {
                BasicTool::throwException("产品交易已无法向卖家申请退款");
            }

            $arr = [];
            $arr['update_time'] = time();
            $arr['state'] = ProductTransactionState::WAITING_SELLER_REFUND;
            $arr['buyer_response'] = $reason;
            $paymentTime = $updateTime + $this->daysOfPayment * 86400;
            time()<$paymentTime or BasicTool::throwException("产品交易确认收款期已过");
            $this->updateRowById($this->table, $id, $arr) or BasicTool::throwException($this->errorMsg);
            return true;
        } catch (Exception $e) {
            $this->errorMsg = $e->getMessage();
            return false;
        }
    }

    /**
     * 向管理员申请退款
     * @param $id 产品交易ID
     * @param $reason 描述
     * @return bool
     */
    public function applyRefundToAdmin($id, $reason){
        try {
            $id = intval($id) or BasicTool::throwException("产品交易ID不能为空");
            $reason or BasicTool::throwException("产品交易原因不能为空");
            $reason = mysqli_real_escape_string($this->sqltool->mysqli,$reason);
            $result = $this->getTransactionById($id) or BasicTool::throwException("没有找到该产品交易");
            $state = $result['state'];
            $updateTime = intval($result['update_time']);
            if(!intval($result['seller_pending']) ||
                $state !== ProductTransactionState::SELLER_REFUSED_REFUND) {
                BasicTool::throwException("产品交易无法向管理员申请退款");
            }

            $arr = [];
            $arr['update_time'] = time();
            $arr['state'] = ProductTransactionState::WAITING_ADMIN;
            $arr['buyer_response'] = $reason;
            $paymentTime = $updateTime + $this->daysOfPayment * 86400;
            time()<$paymentTime or BasicTool::throwException("产品交易向管理员申请退款已过期");
            $this->updateRowById($this->table, $id, $arr) or BasicTool::throwException($this->errorMsg);
            return true;
        } catch (Exception $e) {
            $this->errorMsg = $e->getMessage();
            return false;
        }
    }

    /**
     * 卖家退款
     * @param $id 产品交易ID
     * @param $reason 描述
     * @return bool
     */
    public function refundBySeller($id, $reason="买家已同意退款") {
        return $this->refundTransaction($id,ProductTransactionAction::SELLER_ACTION,$reason);
    }

    /**
     * 管理员退款
     * @param $id 产品交易ID
     * @param $reason 描述
     * @return bool
     */
    public function refundByAdmin($id, $reason) {
        return $this->refundTransaction($id,ProductTransactionAction::ADMIN_ACTION,$reason);
    }

    /**
     * 自动完成退款
     * @param $id 产品交易ID
     * @param $reason 描述
     * @return bool
     */
    public function refundAutomatically($id, $reason="卖家无响应，系统已同意自动退款") {
        return $this->refundTransaction($id,ProductTransactionAction::AUTO_ACTION,$reason);
    }

    /**
     * 管理员拒绝退款
     * @param $id 产品交易ID
     * @param $reason 描述
     * @return bool
     */
    public function refuseRefundByAdmin($id, $reason) {
        return $this->completeTransaction($id, ProductTransactionAction::ADMIN_ACTION, $reason);
    }

    /**
     * 卖家拒绝退款申请
     * @param $id 产品交易ID
     * @param $reason 描述
     * @return bool
     */
    public function refuseRefundBySeller($id, $reason) {
        try {
            $id = intval($id) or BasicTool::throwException("产品交易ID不能为空");
            $reason or BasicTool::throwException("产品交易原因不能为空");
            $reason = mysqli_real_escape_string($this->sqltool->mysqli,$reason);
            $result = $this->getTransactionById($id) or BasicTool::throwException("没有找到该产品交易");
            $state = $result['state'];
            $updateTime = intval($result['update_time']);
            if(!intval($result['seller_pending']) ||
                $state !== ProductTransactionState::WAITING_SELLER_REFUND) {
                BasicTool::throwException("产品交易已无法拒绝退款");
            }

            $arr = [];
            $arr['update_time'] = time();
            $arr['state'] = ProductTransactionState::SELLER_REFUSED_REFUND;
            $arr['seller_response'] = $reason;
            $refundTime = $updateTime + $this->daysOfRefund * 86400;
            time()<$refundTime or BasicTool::throwException("产品交易确认退款期已过");
            $this->updateRowById($this->table, $id, $arr) or BasicTool::throwException($this->errorMsg);
            return true;
        } catch (Exception $e) {
            $this->errorMsg = $e->getMessage();
            return false;
        }
    }



    /***************/
    /*   Private   */
    /***************/

    /**
     * 完成交易
     * @param $id 产品交易ID
     * @param $action 产品交易ACTION {BUYER_ACTION, ADMIN_ACTION, AUTO_ACTION}
     * @param $reason 描述
     * @return bool
     */
    private function completeTransaction($id, $action, $reason) {
        try {
            $id = intval($id) or BasicTool::throwException("产品交易ID不能为空");
            $reason or BasicTool::throwException("产品交易原因不能为空");
            $reason = mysqli_real_escape_string($this->sqltool->mysqli,$reason);
            $result = $this->getTransactionById($id) or BasicTool::throwException("没有找到该产品交易");
            $state = $result['state'];
            $updateTime = intval($result['update_time']);
            if(!intval($result['seller_pending']) ||
                $state == ProductTransactionState::COMPLETED ||
                $state == ProductTransactionState::REFUNDED) {
                BasicTool::throwException("产品交易已完成");
            }

            $arr = [];
            $arr['update_time'] = time();
            $arr['state'] = ProductTransactionState::COMPLETED;
            $paymentTime = $updateTime + $this->daysOfPayment * 86400;

            $transactionModel = new TransactionModel();

            switch ($action) {
                case ProductTransactionAction::BUYER_ACTION:
                    // buyer confirm purchase
                    if ($state != ProductTransactionState::WAITING_PAYMENT &&
                        $state != ProductTransactionState::SELLER_REFUSED_REFUND){
                        BasicTool::throwException("该产品状态下无此操作");
                    }
                    time()<$paymentTime or BasicTool::throwException("产品交易确认付款期已过");
                    break;
                case ProductTransactionAction::ADMIN_ACTION:
                    // admin reject refund application
                    if ($state != ProductTransactionState::WAITING_ADMIN) {
                        BasicTool::throwException("该产品状态下无此操作");
                    }
                    $arr['admin_response'] = $reason;
                    break;
                case ProductTransactionAction::AUTO_ACTION:
                    // automatically confirm purchase
                    if ($state != ProductTransactionState::WAITING_PAYMENT &&
                        $state != ProductTransactionState::SELLER_REFUSED_REFUND) {
                        BasicTool::throwException("该产品状态下无此操作");
                    }
                    time()>=$paymentTime or BasicTool::throwException("产品交易确认付款期还未到");
                    break;
                default:
                    BasicTool::throwException("无此操作");
                    break;
            }
            // change seller transaction pending to 0
            $transactionModel->setPending(intval($result['seller_transaction_id']),0) or BasicTool::throwException($transactionModel->errorMsg);
            // update product transaction
            $this->updateRowById($this->table, $id, $arr) or BasicTool::throwException($this->errorMsg);
            return true;
        } catch(Exception $e) {
            $this->errorMsg = $e->getMessage();
            return false;
        }
    }

    /**
     * 退款一个产品交易
     * @param $id 产品交易ID
     * @param $action ProductTransactionAction {SELLER_ACTION, ADMIN_ACTION, AUTO_ACTION}
     * @param $reason 描述
     * @return bool
     */
    private function refundTransaction($id, $action, $reason) {
        try {
            $id = intval($id) or BasicTool::throwException("产品交易ID不能为空");
            $reason or BasicTool::throwException("产品交易原因不能为空");
            $reason = mysqli_real_escape_string($this->sqltool->mysqli,$reason);
            $result = $this->getTransactionById($id) or BasicTool::throwException("没有找到该产品交易");
            $state = $result['state'];
            $updateTime = intval($result['update_time']);
            if(!intval($result['seller_pending']) ||
                $state == ProductTransactionState::COMPLETED ||
                $state == ProductTransactionState::REFUNDED) {
                BasicTool::throwException("产品交易已完成");
            }

            $arr = [];
            $arr['update_time'] = time();
            $arr['state'] = ProductTransactionState::REFUNDED;
            $refundTime = $updateTime + $this->daysOfRefund * 86400;

            $transactionModel = new TransactionModel();

            switch ($action) {
                case ProductTransactionAction::SELLER_ACTION:
                    // seller accept refund
                    if ($state != ProductTransactionState::WAITING_SELLER_REFUND) {
                        BasicTool::throwException("该产品状态下无此操作");
                    }
                    time()<$refundTime or BasicTool::throwException("产品交易确认退款期已过");
                    break;
                case ProductTransactionAction::ADMIN_ACTION:
                    // admin accept refund application
                    if($state != ProductTransactionState::WAITING_ADMIN){
                        BasicTool::throwException("该产品状态下无此操作");
                    }
                    $arr['admin_response'] = $reason;
                    break;
                case ProductTransactionAction::AUTO_ACTION:
                    // automatically confirm refund
                    if ($state != ProductTransactionState::WAITING_SELLER_REFUND){
                        BasicTool::throwException("该产品状态下无此操作");
                    }
                    time()>=$refundTime or BasicTool::throwException("产品交易确认退款期还未到");
                    break;
                default:
                    BasicTool::throwException("无此操作");
                    break;
            }
            // change seller transaction pending to 0
            $transactionModel->setPending(intval($result['seller_transaction_id']),0) or BasicTool::throwException($transactionModel->errorMsg);
            // make refund
            $buyerDescription = "退款: " . $result["seller_description"];
            $sellerDescription = "收到退款: " . $result["buyer_description"];
            $transactionModel->buy(intval($result['seller_id']),intval($result['buyer_id']),intval($result['seller_amount']),$buyerDescription, $sellerDescription, $this->sectionName, intval($result['section_id'])) or BasicTool::throwException($transactionModel->errorMsg);
            // update product transaction
            $this->updateRowById($this->table, $id, $arr) or BasicTool::throwException($this->errorMsg);
            return true;
        } catch(Exception $e) {
            $this->errorMsg = $e->getMessage();
            return false;
        }
    }

}

/**
 * Class ProductTransactionAction
 * a product transaction action enum class
 * @package admin\productTransaction
 */
abstract class ProductTransactionAction {
    const BUYER_ACTION = 1;
    const SELLER_ACTION = 2;
    const ADMIN_ACTION = 3;
    const AUTO_ACTION = 4;
}

abstract class ProductTransactionState {
    const WAITING_PAYMENT = "waiting_payment";
    const WAITING_SELLER_REFUND = "waiting_seller_refund";
    const SELLER_REFUSED_REFUND = "seller_refused_refund";
    const WAITING_ADMIN = "waiting_admin";
    const REFUNDED = "transaction_refunded";
    const COMPLETED = "transaction_complete";
}