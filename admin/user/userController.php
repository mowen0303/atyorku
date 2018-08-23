<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$currentUser = new \admin\user\UserModel();
$institutionModel = new \admin\institution\InstitutionModel();
call_user_func(BasicTool::get('action'));


//管理员->增加或修改用户分类
function modifyUserClass() {
    global $currentUser;
    try {
        //判断用户是否有"上帝"权限
        $currentUser->isUserHasAuthority('GOD') or BasicTool::throwException("无权操作");

        $flag = BasicTool::post('flag');
        $id = BasicTool::post('id');


        $arr = [];
        $arr['title'] = BasicTool::post('title', '类别描述不能为空');
        $arr['authority'] = array_sum(BasicTool::post('authority'));
        $arr['is_admin'] = BasicTool::post('is_admin', '管理员分组不能为空');

        if ($flag == 'add') {
            //增加一个新用户分类
            $currentUser->addRow('user_class', $arr) or BasicTool::throwException($currentUser->errorMsg);
            BasicTool::echoMessage("{$arr['title']} 添加成功", "index.php?s=listUserClass");

        } elseif ($flag == 'update') {
            //修改用户分类
            $currentUser->updateRowById('user_class', $id, $arr) or BasicTool::throwException($currentUser->errorMsg);
            BasicTool::echoMessage("修改成功", "index.php?s=listUserClass");

        }
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage());
    }
}

function modifyCredit() {
    global $currentUser;
    try {
        $transactionModel = new \admin\transaction\TransactionModel();
        $userId = BasicTool::post('userId', '用户ID不能为空');
        $credit = BasicTool::post('credit', '点券值不能为空');
        $reason = BasicTool::post('reason', '请输入理由');
        $alias = $currentUser->aliasName;
        if ($credit > 0) {
            $transactionModel->addCredit($userId, $credit, "【{$reason}】您的积分增加了{$credit}点。（管理员：{$alias}）","manualAdjust",$userId) or BasicTool::throwException("操作失败");
        } else {
            $transactionModel->deductCredit($userId, $credit, "【{$reason}】您的积分减少了{$credit}点。（管理员：{$alias}）","manualAdjust",$userId) or BasicTool::throwException("操作失败");;
        }
        BasicTool::echoMessage("成功");
    } catch (Exception $e) {

        BasicTool::echoMessage($e->getMessage());

    }

}


//管理员->修改密码
function updatePwd() {

    global $currentUser;

    try {
        $id = BasicTool::post('uid', '用户id不能为空');

        $pwd = BasicTool::post('pwd', '密码不能为空');
        $pwd2 = BasicTool::post('pwd2', '密码不能为空');

        if ($pwd != $pwd2) {
            throw new Exception('两次密码不一样');
        }

        strlen($pwd) >= 6 or BasicTool::throwException("至少6个字符");


        //判断用户是否有权修改
        $currentUser->isAuthorityToManageUserByTargetUserId($id) or BasicTool::throwException($currentUser->errorMsg);

        if (!$currentUser->isUserHasAuthority("USER_UPDATE")) {
            $pwdOfOld = BasicTool::post("pwdOfOld", "请输入原密码");
            $row = $currentUser->getRowById('user', $currentUser->userId);
            $row['pwd'] == md5($pwdOfOld) or BasicTool::throwException("原密码错误");
        }

        $arr = [];
        $arr['pwd'] = md5($pwd);
        //修改用户
        if ($currentUser->updateRowById('user', $id, $arr)) {
            BasicTool::echoJson(1, "修改成功");
        } else {
            throw new Exception("新密码与旧密码相同");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

//管理员->删除用户
function deleteUser() {

    global $currentUser;

    try {
        //判断是否有"删除用户"权限
        $currentUser->isUserHasAuthority("USER_DELETE") or BasicTool::throwException($currentUser->errorMsg);

        $id = BasicTool::post('id');
        $isAdmin = BasicTool::post('isAdmin');

        //检查要删除的每一个用户是不是管理员
        foreach ($isAdmin as $userType) {
            if ($userType == 1) {
                $currentUser->isUserHasAuthority("GOD") or BasicTool::throwException($currentUser->errorMsg);
            }
        }
        //删除用户
        $currentUser->logicalDeleteByFieldIn('user', 'id', $id) or BasicTool::throwException($currentUser->errorMsg);

        BasicTool::echoMessage("删除成功");

    } catch (Exception $e) {

        BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
    }

}

//管理员->删除用户
function deleteClassOfUser() {

    global $currentUser;

    try {
        //判断是否有"删除用户分组"权限
        $currentUser->isUserHasAuthority('GOD') or BasicTool::throwException("你无权操作");

        $idOfClassArr = BasicTool::post('$idOfClassArr');

        $currentUser->realDeleteByFieldIn('user_class', 'id', $idOfClassArr) or BasicTool::throwException($currentUser->errorMsg);

        BasicTool::echoMessage("删除成功");

    } catch (Exception $e) {

        BasicTool::echoMessage($e->getMessage());
    }

}


/**
 * JSON - 获取用户列表
 * http://www.atyorku.ca/admin/user/userController.php?action=getListOfUserClassWithJson
 */
function getListOfUserClassWithJson() {

    global $currentUser;

    $result = $currentUser->getListOfUserClass();

    BasicTool::echoJson(1, "获取用户分类列表成功", $result);

}

/**
 * JSON - 获取一个用户信息(完整)
 * @param userId 用户id
 * http://www.atyorku.ca/admin/user/userController.php?action=getRowOfUserWithJson&userId=1
 */
function getRowOfUserWithJson() {

    global $currentUser;

    try {
        $userId = BasicTool::get('userId', "请输入用户ID");

        //判断是否有权查看别人的详细信息
        if (!($currentUser->isUserHasAuthority('ADMIN'))) {
            $currentUser->userId == $userId or BasicTool::throwException("只有管理员才能查看详细用户信息");
        }
        $arr = $currentUser->getProfileOfUserById($userId);

        if ($arr) {
            BasicTool::echoJson(1, "获取用111户信息成功", $arr);
        } else {
            throw new Exception("没有此用户");
        }
    } catch (Exception $e) {
        BasicTool::echoJson($e->getCode(), $e->getMessage());
    }
}

/**
 * JSON - 获取一个用户信息(基本)
 * @param userId 用户id
 * http://www.atyorku.ca/admin/user/userController.php?action=getRowOfUserBasicInfoWithJson&userId=1
 */
function getRowOfUserBasicInfoWithJson() {

    global $currentUser;

    try {
        $userId = BasicTool::get('userId', "请输入用户ID");
        $arr = $currentUser->getProfileOfUserById($userId, true);

        if ($arr) {
            BasicTool::echoJson(1, "获取用111户信息成功", $arr);
        } else {
            throw new Exception("没有此用户");
        }
    } catch (Exception $e) {
        BasicTool::echoJson($e->getCode(), $e->getMessage());
    }

}


/**
 * 重发激活邮件
 * http://www.atyorku.ca/admin/user/userController.php?action=userSentActiveEmailWithJson
 * @throws Exception
 */
function userSentActiveEmailWithJson() {

    global $currentUser;


    try {

        $currentUser->userId != false or BasicTool::throwException("请先登录");
        $currentUser->userClassId == 6 or BasicTool::throwException("此账号已是激活状态,不需要再次激活");
        $timeGap = time() - $_COOKIE['emailTime'];
        $timeGap > 400 or BasicTool::throwException("激活邮件刚刚已经发送成功，下一封请在" . (400 - $timeGap) . "秒后重试（若激活遇到问题，请联系管理员微信号：jiyu55 ）");

        $code = md5(rand(999, 999999));
        $arr2 = [];
        $arr2['email'] = $currentUser->userName;
        $arr2['code'] = $code;
        $arr2['is_valid'] = "1";
        $currentUser->addRow('user_code', $arr2) or BasicTool::throwException("激活码配置出错（若激活遇到问题，请联系管理员微信号：jiyu55 ）");
        $id = $currentUser->idOfInsert;
        $mailBody = '<p>AtYorkU账号注册成功,请点击下面链接进行激活:</p><p><a href="http://www.atyorku.ca/admin/user/userController.php?action=activateAccount&email=' . $currentUser->userName . '&code=' . $code . '&id=' . $id . '" target="_blank">http://www.atyorku.ca/admin/user/userController.php?action=activateAccount&email=' . $currentUser->userName . '&code=' . $code . '&id=' . $id . '</a></p>';

        if (BasicTool::mailTo($currentUser->userName, "AtYorkU账号激活邮件", $mailBody)) {
            $msg = "邮件发送成功！若5分钟后仍未收到邮件，请检查邮件垃圾箱。（若激活遇到问题，请联系管理员微信号：jiyu55）";
            setcookie('emailTime', time(), time() + 400, '/');


        } else {
            $msg = "邮件发送失败! 当前邮件服务器压力过大,请稍后重试。（若激活遇到问题，请联系管理员微信号：jiyu55）";
        }
        BasicTool::echoJson(1, $msg);


    } catch (Exception $e) {

        BasicTool::echoJson(0, $e->getMessage());

    }
}

function userRegisterStateWithJson() {

    try {

        //BasicTool::throwException("目前软件正在限量技术内测阶段,暂不开放注册.");

        BasicTool::echoJson(1, "开放注册");


    } catch (Exception $e) {

        BasicTool::echoJson(0, $e->getMessage());

    }
}

/**
 * x-x-x-x-x-x-x - 接口准备停用 x-x-x-x-x-x-x-x
 *
 * JSON - 更新个人信息
 * @param userId -POST
 * @param profile -POST 要修改的字段名
 * @param profileVal -POST 字段的值
 * http://www.atyorku.ca/admin/user/userController.php?action=updateAliasWithJson
 */
function updateAliasWithJson() {

    global $currentUser;

    try {
        $userId = BasicTool::post('userId', "请输入用户ID");
        $profile = BasicTool::post('profile', "请输入修改项");
        $currentUser->userId == $userId or BasicTool::throwException("无权修改他人信息");

        $r = false;
        switch ($profile) {
            case "alias":
                $r = 36;
                break;
            case "blockreason":
                $r = 70;
                break;
            case "major":
                $r = 30;
                break;
            case "wechat":
                $r = 40;
                break;
            case "description":
                $r = 90;
                break;
        }

        if ($profile == "enroll_year") {
            if (BasicTool::post('profileVal') > time() || BasicTool::post('profileVal') < (time() - 3600 * 24 * 365 * 8)) {
                BasicTool::throwException("请选择正确的入学时间");
            }
        }

        $arr = [];
        $arr[$profile] = BasicTool::post('profileVal', '请输入一个修改项的值', $r);
        $currentUser->updateRowById('user', $userId, $arr) or BasicTool::throwException($currentUser->errorMsg);
        $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1, "修改成功");

    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }


}

/**
 * 激活用户
 * http://www.atyorku.ca/admin/user/userController.php?action=activateAccount&email=mowen0303@gmail.com&code=7403a6e115abb8d4e0dfce29919fc8ae&id=5
 */
function activateAccount() {
    global $currentUser;
    try {
        $email = BasicTool::get("email", "email不能为空");
        $code = BasicTool::get("code", "code不能为空");
        $id = BasicTool::get("id", "id不能为空");
        $userCodeModel = new \admin\userCode\UserCodeModel();
        $userCodeModel->validateUserCode($id, $email, $code);
        $currentUser->changeUserClassToNormal($email) or BasicTool::throwException($currentUser->errorMsg);
        $currentUser->updateCookie($email);
        BasicTool::echoWapMessage("恭喜,AtYorkU账号激活成功", 0);
    } catch (Exception $e) {
        BasicTool::echoWapMessage($e->getMessage(), 0);
    }
}


/**
 * http://www.atyorku.ca/admin/user/userController.php?action=blockUserByUserId
 * 禁言一个用户, 解禁(days = 0)
 */
function blockUserByUserId() {
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('USER_UPDATE') or BasicTool::throwException("你无权操作");
        $userId = BasicTool::post('userId', "id不能为空");
        $days = BasicTool::post('days', "id不能为空");
        $reason = BasicTool::post('reason', "id不能为空");


        $targetUser = new \admin\user\UserModel($userId);

        if ($targetUser->isAdmin == true) {
            $currentUser->isUserHasAuthority("GOD") or BasicTool::throwException("无权禁言其他管理员");
        }

//        $targetUser->

        $currentUser->blockUser($userId, $days, $reason) or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1, "成功");
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


function topStudentToHTML() {
    try {
        ob_start();
        include($_SERVER['DOCUMENT_ROOT'] . "/apps/topStudentList/index.php");
        //把本页输出到一个变量中
        $temp = ob_get_contents();
        ob_end_clean();
        //写入文件
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/apps/topStudentList/index.html", "w");
        fwrite($fp, $temp) or BasicTool::throwException("写文件错误");
        BasicTool::echoMessage("成功");
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage());
    }
}

/**
 * 如果cookie中没有设备号,则更新当前手机设备号
 * @throws Exception
 */

function updateDevice() {
    global $currentUser;
    try {
        $deviceToken = BasicTool::post('device',"Token无效");
        $deviceType = BasicTool::post('device_type',"设备类型无效") ;
        $currentUser->isLogin() or BasicTool::throwException("未登录");
        $currentUser->updateDevice($currentUser->userId,$deviceType,$deviceToken);
        $userArray = $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1, "修改成功", $userArray);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * 退出时删除设备号
 */
function logoutDevice() {

    global $currentUser;
    //修改用户
    $currentUser->logoutDevice();
    BasicTool::echoJson(1, "退出成功");
}


/**
 * ---- v2 弃用 -----
 * http://www.atyorku.ca/admin/user/userController.php?action=clearBadge
 * 重置badge
 */
function clearBadge() {
    global $currentUser;
    try {
        $currentUser->clearBadge();
        BasicTool::echoJson(1, "成功");
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


/**
 * --------------------------------------------------------
 * --------------------------------------------------------
 *  -------------------- 2.0 已审查 -----------------------
 * --------------------------------------------------------
 * --------------------------------------------------------
 */

/**
 * userRegisterWithJson - 用户注册
 * [post] http://www.atyorku.ca/admin/user/userController.php?action=userRegisterWithJson
 * @param username : string
 * @param password : string
 * return json
 */
function userRegisterWithJson() {
    global $currentUser;
    global $institutionModel;
    try {
        //不能为空,需要验证的字段
        $name = BasicTool::post('username', '请填写用户名', 80);
        $pwd = BasicTool::post('password', '请填写密码');
        $institutionId = 1; // TODO: remove hard code
//        $institutionId = BasicTool::post('institution_id', '请选择学校');
//        $institutionModel->getInstitution($institutionId) or BasicTool::throwException("学校没找到");
        BasicTool::checkFormatOfEmail($name) or BasicTool::throwException("邮箱格式不正确");
        !$currentUser->isExistByFieldValue('user', 'name', $name) or BasicTool::throwException("用户名邮箱已存在");
        strlen($pwd) >= 6 or BasicTool::throwException("密码最少6个字符");
        $alias = explode('@', $name)[0];
        //可以为空的字段
        $user_class_id = $currentUser->enableEmailVerify ? 6 : 7; //普通用户
        $degree = BasicTool::post('degree');
        $major = BasicTool::post('major');
        $wechat = BasicTool::post('wechat');
        $description = BasicTool::post('description');
        $currentUser->register($user_class_id, $institutionId, $name, $pwd, $degree, $alias, $major, $wechat, $description) or BasicTool::throwException("注册失败");
        $userInfo = $currentUser->login($name, $pwd) or BasicTool::throwException($currentUser->errorMsg);
        $msg = "注册成功";
        if ($currentUser->enableEmailVerify) {
            //邮箱验证
            $userCodeModel = new \admin\userCode\UserCodeModel();
            $url = $userCodeModel->generateUserCode($name, "activateAccount");
            $mailBody = "<p>亲爱的用户:</p><p>您AtYorkU的账户已经注册成功,请点击下面链接进行激活:</p><p>{$url}</p>";
            if (BasicTool::mailTo($name, "AtYorkU 账号激活邮件", $mailBody)) {
                $msg = "注册成功，为了保证账号正常使用，请尽快到邮箱激活账号";
            } else {
                $msg = "注册成功，可以登录了! (当前邮件服务器压力过大，激活邮件发送失败，请稍登录账号后重新发送)";
            }
        }
        BasicTool::echoJson(1, $msg, $userInfo);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

//管理员直接添加用户
function addUser() {
    global $currentUser;
    global $institutionModel;
    try {
        $currentUser->isUserHasAuthority("ADMIN") && $currentUser->isUserHasAuthority("USER_ADD") or BasicTool::throwException("权限不足");
        //不能为空,需要验证的字段
        $name = BasicTool::post('username', '请填写用户名', 80);
        $pwd = BasicTool::post('password', '请填写密码');
        $institutionId = 1; // TODO: remove hard code
//        $institutionId = BasicTool::post('institution_id', '请选择学校');
//        $institutionModel->getInstitution($institutionId) or BasicTool::throwException("学校没找到");
        BasicTool::checkFormatOfEmail($name) or BasicTool::throwException("邮箱格式不正确");
        !$currentUser->isExistByFieldValue('user', 'name', $name) or BasicTool::throwException("用户名邮箱已存在");
        strlen($pwd) >= 6 or BasicTool::throwException("密码最少6个字符");
        $alias = explode('@', $name)[0];
        //可以为空的字段
        $user_class_id = BasicTool::post('user_class_id');
        $degree = BasicTool::post('degree');
        $major = BasicTool::post('major');
        $wechat = BasicTool::post('wechat');
        $description = BasicTool::post('description');
        $currentUser->register($user_class_id, $institutionId, $name, $pwd, $degree, $alias, $major, $wechat, $description) or BasicTool::throwException($currentUser->errorMsg);
        echo "11111";
        $userInfo = $currentUser->login($name, $pwd) or BasicTool::throwException($currentUser->errorMsg);
        echo "2222";
        var_dump($userInfo);
        BasicTool::echoMessage("注册成功");
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage());
    }
}

//管理员->添加或修改一个用户
function updateUser() {
    global $currentUser;
    global $institutionModel;
    try {
        //判断当前用户是否有"用户修改","用户添加"权限
        $currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('USER_UPDATE') or BasicTool::throwException($currentUser->errorMsg);

        $alias = BasicTool::post('alias', false, 28);
        $user_class_id = BasicTool::post('user_class_id', '所属用户组不能为空');
        $gender = BasicTool::post('gender', '性别不能为空');
        $institutionId = 1; // TODO: remove hard code
//        $institutionId = BasicTool::post('institution_id', '学校不能为空');
//        $institutionModel->getInstitution($institutionId) or BasicTool::throwException("学校没找到");
        $blocktime = BasicTool::post('setblocktime');
        $blockreason = BasicTool::post('blockreason', false, 70);
        $major = BasicTool::post('major', false, 30);
        $enroll_year = strtotime(BasicTool::post('enroll_year', false, 30));
        $description = BasicTool::post('description', false, 90);
        $wechat = BasicTool::post('wechat', false, 40);

        //修改一个用户
        $targetUserId = BasicTool::post('uid');
        if (!$currentUser->isUserHasAuthority('GOD')) {
            $targetUser = new \admin\user\UserModel($targetUserId);
            if ($targetUser->isAdmin) {
                if ($user_class_id < $currentUser->userClassId) {
                    BasicTool::throwException("禁止进入比自身级别高的管理员组");
                }
            }
        }
        if ($blocktime > 0) {
            //设置禁言
            $blocktime = time() + 3600 * 24 * $blocktime;
        } else if ($blocktime == 0) {
            //解除禁言
            $blocktime = 0;
        }
        //修改用户
        $currentUser->updateUserByAdmin($targetUserId, $institutionId, $alias, $user_class_id, $gender, $blocktime, $blockreason, $major, $enroll_year, $description, $wechat);
        BasicTool::echoMessage("修改成功");
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage());
    }
}

/**
 * retrievePasswordByEmailWithJson - 通过用户名在邮箱找回密码
 * [post] http://www.atyorku.ca/admin/user/userController.php?action=retrievePasswordByEmailWithJson
 * @param email : string
 * return json
 */
function retrievePasswordByEmailWithJson() {
    try {
        $name = BasicTool::post('email', "Email不能为空");
        BasicTool::checkFormatOfEmail($name) or BasicTool::throwException("邮箱格式不正确");
        $userCodeModel = new \admin\userCode\UserCodeModel();
        $url = $userCodeModel->generateUserCode($name, "getNewPasswordByEmail");
        $time = date("Y-m-d H:i:s");
        $mailBody = "<p>亲爱的用户:</p><p>您在{$time}进行了密码找回操作</p><p>请点击下面连接，获取新的临时密码，:</p><p>{$url}</p>";
        BasicTool::mailTo($name, "AtYorkU 密码找回", $mailBody) or BasicTool::throwException("密码找回失败,请联系官方微信客服号: atyorku666");
        BasicTool::echoJson(1, "密码找回成功,请到{$name}的邮箱中查询新密码,若5分钟内未收到邮件,请检查你的垃圾邮件. 若持续收不到邮件,请联系官方客服微信号: atyorku666");
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * 通过邮箱验证码生成临时密码
 */
function getNewPasswordByEmail() {
    global $currentUser;
    try {
        $email = BasicTool::get("email", "email不能为空");
        $code = BasicTool::get("code", "code不能为空");
        $id = BasicTool::get("id", "id不能为空");
        $userCodeModel = new \admin\userCode\UserCodeModel();
        $userCodeModel->validateUserCode($id, $email, $code);
        $newPwd = $currentUser->changePasswordRandomly($email) or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoWapMessage("你的新密码为 {$newPwd} ,请牢记并尽快修改.", 0);
    } catch (Exception $e) {
        BasicTool::echoWapMessage($e->getMessage(), 0);
    }
}


/**
 * userUpdateHeadImgWithJson - 更新头像
 * [post] http://www.atyorku.ca/admin/user/userController.php?action=userUpdateHeadImgWithJson
 */
function userUpdateHeadImgWithJson() {
    global $currentUser;
    try {
        $imgDir = $currentUser->uploadImg("file", $currentUser->userId, 80000, 200) or BasicTool::throwException($currentUser->errorMsg);
        $arr = ["img" => $imgDir];
        $oldImg = $currentUser->getRowById("user", $currentUser->userId);
        $oldImg = $oldImg["img"];
        $currentUser->updateRowById("user", $currentUser->userId, $arr) or unlink($_SERVER["DOCUMENT_ROOT"] . $imgDir);
        if (stripos($oldImg, "default") == false) {
            unlink($_SERVER["DOCUMENT_ROOT"] . $oldImg);
        }
        $userArray = $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1, $imgDir, $userArray);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * updateNicknameWithJson - 更新用户别名
 * [post] http://www.atyorku.ca/admin/user/userController.php?action=updateNicknameWithJson
 * @param alias : string
 * return json
 */
function updateNicknameWithJson() {

    global $currentUser;

    try {
        $val = BasicTool::post('alias', '请输入新的别名', 36);
        $currentUser->updateAlias($val) or BasicTool::throwException($currentUser->errorMsg);
        $userArray = $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1, "修改成功", $userArray);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * updateGenderWithJson - 更新性别
 * [post] http://www.atyorku.ca/admin/user/userController.php?action=updateGenderWithJson
 * @param gender : string
 * return json
 */
function updateGenderWithJson() {

    global $currentUser;

    try {
        $val = BasicTool::post('gender', '输入性别', 1);
        $currentUser->updateGender($val) or BasicTool::throwException($currentUser->errorMsg);
        $userArray = $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1, "修改成功", $userArray);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * updateMajorWithJson - 更新专业
 * [post] http://www.atyorku.ca/admin/user/userController.php?action=updateMajorWithJson
 * @param major : string
 * return json
 */
function updateMajorWithJson() {

    global $currentUser;

    try {
        $val = BasicTool::post('major', false, 30);
        $currentUser->updateMajor($val) or BasicTool::throwException($currentUser->errorMsg);
        $userArray = $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1, "修改成功", $userArray);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * updateWechatWithJson - 更新微信
 * [post] http://www.atyorku.ca/admin/user/userController.php?action=updateWechatWithJson
 * @param wechat : string
 * return json
 */
function updateWechatWithJson() {

    global $currentUser;

    try {
        $val = BasicTool::post('wechat', false, 40);
        $currentUser->updateWechat($val) or BasicTool::throwException($currentUser->errorMsg);
        $userArray = $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1, "修改成功", $userArray);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * updateDescriptionWithJson - 更新个人签名
 * [post] http://www.atyorku.ca/admin/user/userController.php?action=updateDescriptionWithJson
 * @param description : string
 * return json
 */
function updateDescriptionWithJson() {

    global $currentUser;

    try {
        $val = BasicTool::post('description', false, 90);
        $currentUser->updateDescription($val) or BasicTool::throwException($currentUser->errorMsg);
        $userArray = $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1, "修改成功", $userArray);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * updateEnrollYearWithJson - 入学年份
 * [post] http://www.atyorku.ca/admin/user/userController.php?action=updateEnrollYearWithJson
 * @param description : string
 * return json
 */
function updateEnrollYearWithJson() {

    global $currentUser;

    try {
        $val = BasicTool::post('enrollYear', false, 90);
        $currentUser->updateEnrollYear($val) or BasicTool::throwException($currentUser->errorMsg);
        $userArray = $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1, "修改成功", $userArray);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * 入学年份
 * [post] http://www.atyorku.ca/admin/user/userController.php?action=updateDegreeWithJson
 * @param description : string
 * return json
 */
function updateDegreeWithJson() {

    global $currentUser;

    try {
        $val = BasicTool::post('degree', false, 60);
        $currentUser->updateDegree($val) or BasicTool::throwException($currentUser->errorMsg);
        $userArray = $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1, "修改成功", $userArray);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * 更改密码
 * [post] http://www.atyorku.ca/admin/user/userController.php?action=updatePasswordWithJson
 * @param oldPassword : string
 * @param newPassword : string
 * return json
 */
function updatePasswordWithJson() {
    global $currentUser;
    try {
        $oldPassword = BasicTool::post('oldPassword', false);
        $newPassword = BasicTool::post('newPassword', false);
        $currentUser->login($currentUser->userName, $oldPassword) or BasicTool::throwException('原密码错误');
        $currentUser->updatePassword(md5($newPassword)) or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1, "密码修改成功");
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * 获取badge
 * [post] http://www.atyorku.ca/admin/user/userController.php?action=getBadgeWithJson
 * return json
 */
function getBadgeWithJson() {
    global $currentUser;
    try {
        $badge = $currentUser->getBadge();
        BasicTool::echoJson(1, "获取成功", $badge);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


/**
 * 获取当前用户的小纸条,并清空badge
 * [post] http://www.atyorku.ca/admin/user/userController.php?action=getListOfMsgReceive&page=1
 * @param oldPassword : string
 * @param newPassword : string
 * return json
 */
function getListOfMsgReceive() {
    global $currentUser;
    try {
        $arr = $currentUser->getListOfMsgReceive() or BasicTool::throwException("没有消息");
        $currentUser->clearBadge();
        BasicTool::echoJson(1, "获取成功", $arr);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * 领取每日积分
 * [GET] http://www.atyorku.ca/admin/user/userController.php?action=getDailyCredit
 * return json
 */
function getDailyCredit(){
    global $currentUser;
    try {
        $result = $currentUser->getDailyCredit() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1, $result[0], $result[1]);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * 更新用户cookie,并返回用户数据
 * http://www.atyorku.ca/admin/user/userController.php?action=updateCookieWithJson
 */
function updateCookieWithJson() {
    global $currentUser;
    try {
        $row = $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1, 'cookie更新成功',$row);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


?>
