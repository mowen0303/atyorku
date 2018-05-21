<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));


/**
 * JSON -  卖家同意退款一个产品交易 [POST]
 * @param id 产品交易ID
 * @param section_name 产品交易表名
 * http://www.atyorku.ca/admin/productTransaction/productTransactionController.php?action=refundBySellerWithJson
 */
function refundBySellerWithJson() {
    refundBySeller('json');
}

/**
 * JSON -  自动完成所有符合自动付款的交易 [POST]
 * @param user_id 用户ID
 * @param section_name 产品交易表名
 * http://www.atyorku.ca/admin/productTransaction/productTransactionController.php?action=completeListOfTransactionsAutomaticallyWithJson
 */
function completeListOfTransactionsAutomaticallyWithJson() {
    autoCompleteProductTransactions('json');
}

/**
 * JSON -  自动完成所有符合自动付款的交易 [POST]
 * @param int|string id 产品交易ID
 * @param string section_name 产品交易表名
 * http://www.atyorku.ca/admin/productTransaction/productTransactionController.php?action=completePaymentByBuyerWithJson
 */
function completePaymentByBuyerWithJson() {
    completePaymentByBuyer('json');
}

/**
 * JSON -  自动完成所有符合自动退款申请 [POST]
 * @param int|string user_id 用户ID
 * @param string section_name 产品交易表名
 * http://www.atyorku.ca/admin/productTransaction/productTransactionController.php?action=refundListOfTransactionsAutomaticallyWithJson
 */
function refundListOfTransactionsAutomaticallyWithJson() {
    autoRefundProductTransactions('json');
}

/**
 * JSON -  卖家拒绝退款 [POST]
 * @param string section_name 产品交易表名
 * @param int|string id 产品交易ID
 * @param string reason 拒绝原因
 * http://www.atyorku.ca/admin/productTransaction/productTransactionController.php?action=refuseRefundBySellerWithJson
 */
function refuseRefundBySellerWithJson() {
    refuseRefundBySeller('json');
}

/**
 * JSON -  向卖家申请退款 [POST]
 * @param string section_name 产品交易表名
 * @param int|string id 产品交易ID
 * @param string reason 申请原因
 * http://www.atyorku.ca/admin/productTransaction/productTransactionController.php?action=applyRefundToSellerWithJson
 */
function applyRefundToSellerWithJson() {
    applyRefundToSeller('json');
}

/**
 * JSON -  向管理员申请退款 [POST]
 * @param string section_name 产品交易表名
 * @param int|string id 产品交易ID
 * @param string reason 申请原因
 * http://www.atyorku.ca/admin/productTransaction/productTransactionController.php?action=applyRefundToAdminWithJson
 */
function applyRefundToAdminWithJson() {
    applyRefundToAdmin('json');
}




/**
 * 自动完成所有符合自动付款的交易
 * @param string $echoType
 * @param string section_name 交易表名
 * @param int|string user_id 用户ID (optional ADMIN专用)
 */
function autoCompleteProductTransactions($echoType='normal'){
    global $currentUser;
    try {
        $sectionName = BasicTool::post("section_name","产品交易表名不能为空");
        $uid = BasicTool::post("user_id") ?: $currentUser->userId;
        $uid or BasicTool::throwException("请先登录");
        if($uid!==$currentUser->userId && !$currentUser->isUserHasAuthority('ADMIN')){
            BasicTool::throwException("无权限");
        }
        $productTransactionModel = getProductTransactionModel($sectionName);
        $result = $productTransactionModel->completeListOfTransactionsAutomatically($uid);
        if($result['fail']===0){
            if($echoType == "normal"){
                BasicTool::echoMessage($result['logs'], $_SERVER['HTTP_REFERER']);
            } else {
                BasicTool::echoJson(1, "完成", $result);
            }
        } else {
            if($echoType == "normal"){
                BasicTool::echoMessage($result['logs'], $_SERVER['HTTP_REFERER']);
            } else {
                BasicTool::echoJson(0, "失败", $result);
            }
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

/**
 * 自动完成所有符合自动退款申请
 * @param string $echoType
 * @param string section_name 交易表名
 * @param int|string user_id 用户ID【作为申请方和被申请方】 (optional ADMIN专用)
 */
function autoRefundProductTransactions($echoType='normal'){
    global $currentUser;
    try {
        $sectionName = BasicTool::post("section_name","产品交易表名不能为空");
        $uid = BasicTool::post("user_id") ?: $currentUser->userId;
        $uid or BasicTool::throwException("请先登录");
        if($uid!==$currentUser->userId && !$currentUser->isUserHasAuthority('ADMIN')){
            BasicTool::throwException("无权限");
        }
        $productTransactionModel = getProductTransactionModel($sectionName);
        $result = $productTransactionModel->refundListOfTransactionsAutomatically($uid);
        if($result['fail']===0){
            if($echoType == "normal"){
                BasicTool::echoMessage($result['logs'], $_SERVER['HTTP_REFERER']);
            } else {
                BasicTool::echoJson(1, "完成", $result);
            }
        } else {
            if($echoType == "normal"){
                BasicTool::echoMessage($result['logs'], $_SERVER['HTTP_REFERER']);
            } else {
                BasicTool::echoJson(0, "失败", $result);
            }
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}


/**
 * 买家确认付款
 * @param string $echoType
 * @param int|string id 产品交易ID
 * @param
 */
function completePaymentByBuyer($echoType='normal') {
    global $currentUser;
    try {
        $sectionName = BasicTool::post("section_name","产品交易表名不能为空");
        $productTransactionModel = getProductTransactionModel($sectionName);
        $id = BasicTool::post("id","产品交易ID不能为空");

        $productTransaction = $productTransactionModel->getTransactionById($id) or BasicTool::throwException("未找到该产品交易");
        if (!($currentUser->isUserHasAuthority('ADMIN'))) {
            $currentUser->userId == $productTransaction['buyer_id'] or BasicTool::throwException("无权限");
        }
        $result = $productTransactionModel->completeTransactionByBuyer($id) or BasicTool::throwException("确认付款失败");

        if ($echoType == "normal") {
            BasicTool::echoMessage("确认付款成功", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "确认付款成功");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}


/**
 * 卖家退款
 * @param string $echoType
 * @param int|string id 产品交易ID
 */
function refundBySeller($echoType='normal') {
    global $currentUser;
    try {
        $sectionName = BasicTool::post("section_name","产品交易表名不能为空");
        $productTransactionModel = getProductTransactionModel($sectionName);
        $id = BasicTool::post("id","产品交易ID不能为空");

        $productTransaction = $productTransactionModel->getTransactionById($id) or BasicTool::throwException("未找到该产品交易");
        if (!($currentUser->isUserHasAuthority('ADMIN'))) {
            $currentUser->userId == $productTransaction['seller_id'] or BasicTool::throwException("无权限");
        }
        $result = $productTransactionModel->refundBySeller($id) or BasicTool::throwException("退款失败");

        if ($echoType == "normal") {
            BasicTool::echoMessage("退款成功", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "退款成功");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

/**
 * 管理员执行退款
 * @param string $echoType
 * @param int|string id 产品交易ID
 * @param string reason 管理员退款原因
 */
function refundByAdmin($echoType='normal') {
    global $currentUser;
    try {
        $sectionName = BasicTool::post("section_name","产品交易表名不能为空");
        $productTransactionModel = getProductTransactionModel($sectionName);
        $id = BasicTool::post("id","产品交易ID不能为空");
        $reason = BasicTool::post("reason","产品交易退款原因不能为空");
        $productTransaction = $productTransactionModel->getTransactionById($id) or BasicTool::throwException("未找到该产品交易");
        $currentUser->isUserHasAuthority('ADMIN') or BasicTool::throwException("无权限");

        $result = $productTransactionModel->refundByAdmin($id, $reason) or BasicTool::throwException("退款失败");

        if ($echoType == "normal") {
            BasicTool::echoMessage("退款成功", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "退款成功");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

/**
 * 卖家拒绝退款
 * @param string $echoType
 * @param int|string id 产品交易ID
 * @param string reason 拒绝原因
 */
function refuseRefundBySeller($echoType='normal') {
    global $currentUser;
    try {
        $sectionName = BasicTool::post("section_name","产品交易表名不能为空");
        $productTransactionModel = getProductTransactionModel($sectionName);
        $id = BasicTool::post("id","产品交易ID不能为空");
        $reason = BasicTool::post("reason", "拒绝退款原因不能为空");

        $productTransaction = $productTransactionModel->getTransactionById($id) or BasicTool::throwException("未找到该产品交易");
        $currentUser->userId == $productTransaction['seller_id'] or BasicTool::throwException("无权限");
        $result = $productTransactionModel->refuseRefundBySeller($id, $reason) or BasicTool::throwException("拒绝退款失败");

        if ($echoType == "normal") {
            BasicTool::echoMessage("拒绝退款成功", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "拒绝退款成功");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}


/**
 * 向卖家申请退款
 * @param string $echoType
 * @param int|string id 产品交易ID
 * @param string reason 申请退款原因
 */
function applyRefundToSeller($echoType='normal') {
    global $currentUser;
    try {
        $sectionName = BasicTool::post("section_name","产品交易表名不能为空");
        $productTransactionModel = getProductTransactionModel($sectionName);
        $id = BasicTool::post("id","产品交易ID不能为空");
        $reason = BasicTool::post("reason", "申请退款原因不能为空");

        $productTransaction = $productTransactionModel->getTransactionById($id) or BasicTool::throwException("未找到该产品交易");
        $currentUser->userId == $productTransaction['buyer_id'] or BasicTool::throwException("无权限");
        $result = $productTransactionModel->applyRefundToSeller($id, $reason) or BasicTool::throwException("退款申请失败");

        if ($echoType == "normal") {
            BasicTool::echoMessage("退款申请成功", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "退款申请成功");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

/**
 * 向管理员申请退款
 * @param string $echoType
 * @param int|string id 产品交易ID
 * @param string reason 申请退款原因
 */
function applyRefundToAdmin($echoType='normal') {
    global $currentUser;
    try {
        $sectionName = BasicTool::post("section_name","产品交易表名不能为空");
        $productTransactionModel = getProductTransactionModel($sectionName);
        $id = BasicTool::post("id","产品交易ID不能为空");
        $reason = BasicTool::post("reason", "申请退款原因不能为空");

        $productTransaction = $productTransactionModel->getTransactionById($id) or BasicTool::throwException("未找到该产品交易");
        $currentUser->userId == $productTransaction['buyer_id'] or BasicTool::throwException("无权限");
        $result = $productTransactionModel->applyRefundToAdmin($id, $reason) or BasicTool::throwException("退款申请失败");

        if ($echoType == "normal") {
            BasicTool::echoMessage("退款申请成功", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "退款申请成功");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}


function getProductTransactionModel($sectionName) {
    return new \admin\productTransaction\ProductTransactionModel($sectionName);
}

