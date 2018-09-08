<?php
namespace admin\productTransaction;
use \Model as Model;
use admin\transaction\TransactionModel as TransactionModel;
use \BasicTool as BasicTool;
use \Exception as Exception;
use admin\productTransaction\ProductTransactionSectionConfig as SectionConfig;


class ProductTransactionModel extends Model {

    private $sectionName;       // 产品表名
    private $daysOfPayment;     // 等待确认付款天数 (卖家拒绝退款后确认付款天数与其一致）
    private $daysOfRefund;      // 等待退款天数

    /**
     * ProductTransactionModel constructor.
     * @param string $sectionName 产品表名
     * @throws Exception
     */
    public function __construct($sectionName)
    {
        parent::__construct();

        $sectionInfo = SectionConfig::$sections[$sectionName];
        if(!$sectionInfo) BasicTool::throwException("Invalid section name");
        $this->sectionName = $sectionName;
        $this->daysOfPayment = $sectionInfo['daysOfPayment'];
        $this->daysOfRefund = $sectionInfo['daysOfRefund'];
        $this->table = "product_transaction";
    }



    /**********************************/
    /*          Query Methods         */
    /**********************************/


    /**
     * Get a product_transaction by id
     * @param int|string $id 产品交易ID
     * @param bool $extendSelect 外加的选择SQL，用逗号隔开 {交易表名: pt | 卖家交易表名: st | 买家交易表名: bt}
     * @param bool $extendFrom 外加的Join表SQL，用JOIN连接表名 ex: 'user u INNER JOIN user_class ON ...'
     * @return array|\一维关联数组
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
     * @param bool $extendSelect 外加的选择SQL，用逗号隔开 {交易表名: pt | 卖家交易表名: st | 买家交易表名: bt | 买家表名: buyer | 卖家表名: seller}
     * @param bool $extendFrom 外加的Join表SQL，用JOIN连接表名 ex: 'user u INNER JOIN user_class ON ...'
     * @param bool $usersDetail 是否query 卖家买家详细信息
     * @return array
     */
    public function getListOfTransactions($pageSize=20, $query=false, $order=false, $extendSelect=false, $extendFrom=false, $usersDetail=false){
        $productTransactionSelect = "pt.*";
        $sellerTransactionSelect = "st.amount AS seller_amount, st.user_id AS seller_id, st.section_id, st.pending AS seller_pending, st.description AS seller_description";
        $buyerTransactionSelect = "bt.user_id AS buyer_id, bt.amount AS buyer_amount, bt.pending AS buyer_pending, bt.time AS purchased_time, bt.description AS buyer_description, bt.time AS transaction_time";

        $select = "SELECT {$productTransactionSelect}, {$sellerTransactionSelect}, {$buyerTransactionSelect}";
        $from = "FROM (product_transaction pt INNER JOIN transaction bt ON pt.buyer_transaction_id=bt.id INNER JOIN transaction st ON pt.seller_transaction_id=st.id)";

        if($usersDetail) {
            $sellerSelect = "seller.user_class_id AS seller_class_id,seller.img AS seller_img,seller.alias AS seller_alias,seller.gender AS seller_gender,seller.major AS seller_major,seller.enroll_year AS seller_enroll_year,seller.degree AS seller_degree";
            $buyerSelect = "buyer.user_class_id AS buyer_class_id,buyer.img AS buyer_img,buyer.alias AS buyer_alias,buyer.gender AS buyer_gender,buyer.major AS buyer_major,buyer.enroll_year AS buyer_enroll_year,buyer.degree AS buyer_degree";
            $select .= ", {$sellerSelect}, {$buyerSelect}";
            $from .= " INNER JOIN user buyer ON bt.user_id=buyer.id INNER JOIN user seller ON st.user_id=seller.id";
        }

        if($extendSelect) {
            $select .= ", {$extendSelect}";
        }

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

        $results = $this->getListWithPage("product_transaction",$sql,$countSql,$pageSize);

        if($results){
            foreach($results as &$result) {
                $result['state_description'] = ProductTransactionState::translateState($result['state']);
                $result['transaction_time'] = BasicTool::translateTime($result['transaction_time']);
                $result['update_time'] = BasicTool::translateTime($result['update_time']);
                if ($result['expiration_time']) {
                    $result['expiration_time'] = BasicTool::translateTime($result['expiration_time']);
                }
            }
        }
        return $results;
    }

    /**
     * Get a list of purchased product transactions
     * @param int|string $userId buyer id
     * @param int|string $sectionId section row id
     * @param string $sectionName section table name
     * @param bool $extendSelect 外加的选择SQL，用逗号隔开 {交易表名: pt | 买家交易表名: bt}
     * @param bool $extendFrom 外加的Join表SQL，用JOIN连接表名 ex: 'user u INNER JOIN user_class ON ...'
     * @param bool $extendWhere 外加的判断SQL
     * @return array
     */
    public function getListOfPurchasedTransactionsBy($userId, $sectionId, $sectionName, $extendSelect=false, $extendFrom=false, $extendWhere=false) {
        $userId = intval($userId);
        $sectionId = intval($sectionId);

        $select = "pt.*, bt.user_id AS buyer_id, bt.amount, bt.description, bt.section_name, bt.section_id, bt.pending";
        $from = "{$this->table} pt INNER JOIN transaction bt ON pt.buyer_transaction_id = bt.id";
        $where = "bt.user_id = {$userId} AND bt.section_name = '{$sectionName}' AND bt.section_id = {$sectionId}";

        if($extendSelect) {
            $select .= ",{$extendSelect}";
        }
        if($extendFrom) {
            $from .= " {$extendFrom}";
        }
        if ($extendWhere) {
            $where .= " AND ({$extendWhere})";
        }

        $sql = "SELECT {$select} FROM {$from} WHERE {$where}";
        return $this->sqltool->getListBySql($sql);
    }


    /**
     * 获取一页指定买家的产品销售
     * @param int|string $userId 买家ID
     * @param int $pageSize
     * @param bool $order 排序Sql
     * @param bool $extendQuery 外加索引Condition SQL
     * @param bool $extendSelect 外加的选择SQL，用逗号隔开 {交易表名: pt | 卖家交易表名: st | 买家交易表名: bt | 买家表名: buyer | 卖家表名: seller}
     * @param bool $extendFrom 外加的Join表SQL，用JOIN连接表名 ex: 'user u INNER JOIN user_class ON ...'
     * @param bool $usersDetail 是否query 卖家买家详细信息
     * @return array
     */
    public function getListOfPurchasedTransactionsByUserId($userId, $pageSize=20, $order=false, $extendQuery=false, $extendSelect=false, $extendFrom=false, $usersDetail=false){
        $userId = intval($userId);
        $q = "bt.user_id={$userId}";
        if($extendQuery){
            $q .= " AND (${extendQuery})";
        }
        return $this->getListOfTransactions($pageSize, $q, $order, $extendSelect, $extendFrom, $usersDetail);
    }

    /**
     * 获取一页指定卖家的产品销售交易
     * @param int|string $userId 卖家ID
     * @param int $pendingOption pending交易获取选项 {-1:全选, 0:仅限已完成的交易, 1:仅限未完成的交易}
     * @param int $pageSize
     * @param bool $order 排序Sql
     * @param bool $extendQuery 外加索引Condition SQL
     * @param bool $extendSelect 外加的选择SQL，用逗号隔开 {交易表名: pt | 卖家交易表名: st | 买家交易表名: bt | 买家表名: buyer | 卖家表名: seller}
     * @param bool $extendFrom 外加的Join表SQL，用JOIN连接表名 ex: 'user u INNER JOIN user_class ON ...'
     * @param bool $usersDetail 是否query 卖家买家详细信息
     * @return array
     */
    public function getListOfSoldTransactionsByUserId($userId, $pendingOption=-1, $pageSize=20, $order=false, $extendQuery=false, $extendSelect=false, $extendFrom=false, $usersDetail=false){
        $userId = intval($userId);
        $q = "st.user_id={$userId}";
        if($pendingOption===0 || $pendingOption===1){
            $q .= " AND st.pending={$pendingOption}";
        }
        if($extendQuery){
            $q .= " AND (${extendQuery})";
        }
        return $this->getListOfTransactions($pageSize, $q, $order, $extendSelect, $extendFrom, $usersDetail);
    }




    /**********************************/
    /*        Command Methods         */
    /**********************************/


    /**
     * Buy a product
     * @param int $buyerUserId 买家ID
     * @param int $sellerUserId 卖家ID
     * @param int $amount 价格
     * @param string $buyerDescription 买家描述
     * @param string $sellerDescription 卖家描述
     * @param int $sectionId 产品ID
     * @return bool
     */
    public function buy($buyerUserId, $sellerUserId, $amount, $buyerDescription, $sellerDescription, $sectionId, $expirationTime=0) {
        try {
            $transactionModel = new TransactionModel();
            $result = $transactionModel->buy($buyerUserId, $sellerUserId, $amount, $buyerDescription, $sellerDescription, $this->sectionName, $sectionId, 0, 1);
            if(!$result) BasicTool::throwException($transactionModel->errorMsg);
            $buyerTransId = $result['buyer_transaction_id'];
            $sellerTransId = $result['seller_transaction_id'];
            $arr = [
                "buyer_transaction_id" => $buyerTransId,
                "seller_transaction_id" => $sellerTransId,
                "state" => ProductTransactionState::WAITING_PAYMENT,
                "update_time" => time(),
                "expiration_time" => $expirationTime
            ];
            return $this->addRow($this->table, $arr);
        } catch (Exception $e) {
            $this->errorMsg = $e->getMessage();
            return false;
        }
    }

    /**
     * 买家收款
     * @param int|string $id 产品交易ID
     * @param string $reason 描述
     * @return bool
     */
    public function completeTransactionByBuyer($id, $reason="买家已确认付款") {
        return $this->completeTransaction($id,ProductTransactionAction::BUYER_ACTION,$reason);
    }

    /**
     * 自动收款
     * @param int|string $id 产品交易ID
     * @param string $reason 描述
     * @return bool
     */
    public function completeTransactionAutomatically($id, $reason="自动确认付款") {
        return $this->completeTransaction($id,ProductTransactionAction::AUTO_ACTION,$reason);
    }

    /**
     * 自动收款全部交易【满足自动收款条件作为买家和卖家】
     * @param int|string $uid 用户ID
     * @return array
     */
    public function completeListOfTransactionsAutomatically($uid) {
        $q = "pt.state IN ('" . ProductTransactionState::WAITING_PAYMENT . "','" . ProductTransactionState::SELLER_REFUSED_REFUND . "') AND pt.update_time<=" . strval($this->getAutoPaymentEarliestTimeFromNow()) . " AND (bt.user_id=" . strval($uid) . " OR st.user_id=" . strval($uid) . ")";
        $results = $this->getListOfTransactions(99999, $q);
        $log = ["success"=>0,"fail"=>0,"logs"=>["自动付款LOG"]];
        if($results) {
            foreach ($results as $result) {
                $bool = $this->completeTransactionAutomatically($result['id']);
                if($bool){
                    array_push($log['logs'], "[SUCCESS] Product Transaction Id=".$result['id']);
                    $log['success']++;
                } else {
                    array_push($log['logs'], "[FAIL] Product Transaction Id=".$result['id']." Failed. Error: ".$this->errorMsg);
                    $log['fail']++;
                }
            }
        } else {
            array_push($log['logs'], "[N/A] No Product Transaction can be auto completed.");
        }

        return $log;
    }

    /**
     * 自动退款全部交易【满足自动退款条件作为买家和卖家】
     * @param int|string $uid 用户ID
     * @return array
     */
    public function refundListOfTransactionsAutomatically($uid) {
        $q = "pt.state='" . ProductTransactionState::WAITING_SELLER_REFUND . "' AND pt.update_time<=" . strval($this->getAutoRefundEarliestTimeFromNow()) . " AND (bt.user_id=" . strval($uid) . " OR st.user_id=" . strval($uid) . ")";
        $results = $this->getListOfTransactions(99999, $q);
        $log = ["success"=>0,"fail"=>0,"logs"=>["自动退款LOG"]];
        if($results) {
            foreach ($results as $result) {
                $bool = $this->refundAutomatically($result['id']);
                if($bool){
                    array_push($log['logs'], "[SUCCESS] Product Transaction Id=".$result['id']);
                    $log['success']++;
                } else {
                    array_push($log['logs'], "[FAIL] Product Transaction Id=".$result['id']." Failed. Error: ".$this->errorMsg);
                    $log['fail']++;
                }
            }
        } else {
            array_push($log['logs'], "[N/A] No Product Transaction can be auto refunded.");
        }

        return $log;
    }



    /**
     * 向卖家申请退款
     * @param int|string $id 产品交易ID
     * @param string $reason 描述
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
            $paymentTime = $this->getAutoPaymentTime($updateTime);
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
     * @param int|string $id 产品交易ID
     * @param string $reason 描述
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
            $paymentTime = $this->getAutoPaymentTime($updateTime);
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
     * @param int|string $id 产品交易ID
     * @param string $reason 描述
     * @return bool
     */
    public function refundBySeller($id, $reason="买家已同意退款") {
        return $this->refundTransaction($id,ProductTransactionAction::SELLER_ACTION,$reason);
    }

    /**
     * 管理员退款
     * @param int|string $id 产品交易ID
     * @param string $reason 描述
     * @return bool
     */
    public function refundByAdmin($id, $reason) {
        return $this->refundTransaction($id,ProductTransactionAction::ADMIN_ACTION,$reason);
    }

    /**
     * 自动完成退款
     * @param int|string $id 产品交易ID
     * @param string $reason 描述
     * @return bool
     */
    public function refundAutomatically($id, $reason="卖家无响应，系统已同意自动退款") {
        return $this->refundTransaction($id,ProductTransactionAction::AUTO_ACTION,$reason);
    }

    /**
     * 管理员拒绝退款
     * @param int|string $id 产品交易ID
     * @param string $reason 描述
     * @return bool
     */
    public function refuseRefundByAdmin($id, $reason) {
        return $this->completeTransaction($id, ProductTransactionAction::ADMIN_ACTION, $reason);
    }

    /**
     * 卖家拒绝退款申请
     * @param int|string $id 产品交易ID
     * @param string $reason 描述
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
            $refundTime = $this->getAutoRefundTime($updateTime);
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


    private function getAutoPaymentTime($updateTime) {
        return $updateTime + $this->daysOfPayment * 86400;
    }

    private function getAutoRefundTime($updateTime) {
        return $updateTime + $this->daysOfRefund * 86400;
    }

    private function getAutoPaymentEarliestTimeFromNow() {
        return time() - $this->daysOfPayment * 86400;
    }

    private function getAutoRefundEarliestTimeFromNow() {
        return time() - $this->daysOfRefund * 86400;
    }

    /**
     * 完成交易
     * @param int|string $id 产品交易ID
     * @param int $action 产品交易ACTION {BUYER_ACTION, ADMIN_ACTION, AUTO_ACTION}
     * @param string $reason 描述
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
            $paymentTime = $this->getAutoPaymentTime($updateTime);

            $transactionModel = new TransactionModel();

            switch ($action) {
                case ProductTransactionAction::BUYER_ACTION:
                    // buyer confirm purchase
                    if ($state != ProductTransactionState::WAITING_PAYMENT &&
                        $state != ProductTransactionState::SELLER_REFUSED_REFUND){
                        BasicTool::throwException("该产品状态下无此操作");
                    }
//                    time()<$paymentTime or BasicTool::throwException("产品交易确认付款期已过");
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
     * @param int|string $id 产品交易ID
     * @param int $action ProductTransactionAction {SELLER_ACTION, ADMIN_ACTION, AUTO_ACTION}
     * @param string $reason 描述
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
            $refundTime = $this->getAutoRefundTime($updateTime);

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

    /**
     * 翻译ProductTransactionState
     * @param $state
     * @return string
     */
    public static function translateState($state){
        switch ($state) {
            case ProductTransactionState::WAITING_PAYMENT:
                return "等待买家确认付款";
            case ProductTransactionState::WAITING_SELLER_REFUND:
                return "等待卖家退款";
            case ProductTransactionState::SELLER_REFUSED_REFUND:
                return "卖家拒绝退款";
            case ProductTransactionState::WAITING_ADMIN:
                return "等待小姐姐介入";
            case ProductTransactionState::REFUNDED:
                return "卖家已退款";
            case ProductTransactionState::COMPLETED:
                return "交易完成";
            default:
                return "Invalid ProductTransactionState";
        }
    }
}