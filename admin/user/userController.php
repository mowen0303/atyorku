<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));


//管理员->添加或修改一个用户
function modify()
{

    global $currentUser;

    try {
        //判断当前用户是否有"用户修改","用户添加"权限
        $currentUser->isUserHasAuthority('USER_UPDATE') && $currentUser->isUserHasAuthority('USER_ADD') or BasicTool::throwException($currentUser->errorMsg);


        $flag = BasicTool::post('flag');

        $arr = [];
        $arr['alias'] = BasicTool::post('alias', false, 28);
        $arr['user_class_id'] = BasicTool::post('user_class_id', '所属用户组不能为空');
        $arr['gender'] = BasicTool::post('gender', '性别不能为空');
        $arr['blocktime'] = BasicTool::post('blocktime') == null ? "0" : BasicTool::post('blocktime');
        //$arr['setblocktime'] = BasicTool::post('setblocktime');
        $arr['blockreason'] = BasicTool::post('blockreason', false, 70);
        $arr['img'] = BasicTool::post('img');
        $arr['major'] = BasicTool::post('major', false, 30);
        $enrollYear = BasicTool::post('enroll_year', false, 30);
        if ($enrollYear == "") {
            $arr['enroll_year'] = 0;
        } else {
            $arr['enroll_year'] = strtotime(BasicTool::post('enroll_year', false, 30));
        }

        $arr['description'] = BasicTool::post('description', false, 90);
        $arr['wechat'] = BasicTool::post('wechat', false, 40);


        if ($flag == 'add') {
            //增加一个新用户
            $arr['name'] = BasicTool::post('name', '用户名不能为空', 80);
            if ($currentUser->isExistByFieldValue('user', 'name', $arr['name'])) {
                throw new Exception('用户名邮箱已存在');
            }
            $pwd = BasicTool::post('pwd', '密码不能为空');
            $arr['pwd'] = md5($pwd);
            $arr['registertime'] = time();

            $currentUser->addRow('user', $arr) or BasicTool::throwException($currentUser->errorMsg);
            BasicTool::echoMessage("新用户 {$arr['name']} 添加成功", "index.php?s=listUser&isAdmin=0");
        } elseif ($flag == 'update') {
            //修改一个用户
            $id = BasicTool::post('uid');


            if (!$currentUser->isUserHasAuthority('GOD')) {

                $targetUser = new \admin\user\UserModel($id);
                if ($targetUser->isAdmin) {
                    if ($arr['user_class_id'] < $currentUser->userClassId) {
                        BasicTool::throwException("禁止进入比自身级别高的管理员组");
                    }
                }
            }

            $setblocktime = BasicTool::post('setblocktime');

            if ($setblocktime > 0) {
                //设置禁言
                $arr['blocktime'] = time() + 3600 * 24 * $setblocktime;
                $arr['blockreason'] = BasicTool::post('blockreason', false, 70);
            } else if ($setblocktime == 0) {
                //解除禁言
                $arr['blocktime'] = 0;
            }

            //修改用户
            $currentUser->updateRowById('user', $id, $arr) or BasicTool::throwException($currentUser->errorMsg);
            BasicTool::echoMessage("修改成功");
        }
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage());
    }
}


//管理员->增加或修改用户分类
function modifyUserClass()
{
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

function modifyCredit()
{
    global $currentUser;
    try {
        $currentCredit = BasicTool::post('currentCredit', '当前点券值不能为空');
        $credit = BasicTool::post('credit', '点券值不能为空');
        $userId = BasicTool::post('userId', '用户ID不能为空');

        $currentCredit + $credit >= 0 or BasicTool::throwException("没有足够的点券用于消费");


        $currentUser->addCredit($credit, $userId) or BasicTool::throwException($currentUser->errorMsg);

        BasicTool::echoMessage("成功");

    } catch (Exception $e) {

        BasicTool::echoMessage($e->getMessage());

    }

}


//管理员->修改密码
function updatePwd()
{

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
function deleteUser()
{

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
function deleteClassOfUser()
{

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
function getListOfUserClassWithJson()
{

    global $currentUser;

    $result = $currentUser->getListOfUserClass();

    BasicTool::echoJson(1, "获取用户分类列表成功", $result);

}

/**
 * JSON - 获取一个用户信息(完整)
 * @param userId 用户id
 * http://www.atyorku.ca/admin/user/userController.php?action=getRowOfUserWithJson&userId=1
 */
function getRowOfUserWithJson()
{

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
function getRowOfUserBasicInfoWithJson()
{

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
 * JSON - 检测用户登录状态
 * @param username 用户名
 * @param password 密码
 * @param password2 确认密码
 * http://www.atyorku.ca/admin/user/userController.php?action=userIsLoginWithJson
 */
function userIsLoginWithJson()
{

    global $currentUser;

    if ($currentUser->isLogin()) {
        BasicTool::echoJson(1, "已经登录");
    } else {
        BasicTool::echoJson(0, "没登录");
    }


}


/**
 * 重发激活邮件
 * http://www.atyorku.ca/admin/user/userController.php?action=userSentActiveEmailWithJson
 * @throws Exception
 */
function userSentActiveEmailWithJson()
{

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

function userRegisterStateWithJson()
{

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
function updateAliasWithJson()
{

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
 * JSON - 激活账号
 * @param userId -POST
 * @param profile -POST 要修改的字段名
 * @param profileVal -POST 字段的值
 * http://www.atyorku.ca/admin/user/userController.php?action=activateAccount&email=mowen0303@gmail.com&code=7403a6e115abb8d4e0dfce29919fc8ae&id=5
 */
function activateAccount()
{

    global $currentUser;

    try {
        $email = BasicTool::get("email");
        $code = BasicTool::get("code");
        $id = BasicTool::get("id");

        $row = $currentUser->getRowById('user_code', $id);
        $email == $row['email'] && $code == $row['code'] && $row['is_valid'] == 1 or BasicTool::throwException("验证码已经失效");
        $currentUser->changeUserClassToNormal($email) or BasicTool::throwException($currentUser->errorMsg);

        $arr = [];
        $arr['is_valid'] = 0;
        $currentUser->updateRowById('user_code', $id, $arr);

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
function blockUserByUserId()
{
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


function topStudentToHTML()
{


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

function updateDevice()
{

    global $currentUser;

    $deviceToken = BasicTool::post('device');

    if ($currentUser->isLogin() && $deviceToken != null) {
        $id = $currentUser->userId;
        $arr = [];
        $arr['device'] = $deviceToken;
        if ($arr['device'] == "") {
            $arr['device'] = "0";
        }
        //修改用户
        $currentUser->updateRowById('user', $id, $arr);
        setcookie("cc_de", $deviceToken, time() + 3600 * 24 * 2, '/');
        setcookie("cc_dev", $deviceToken, time() + 3600 * 24 * 2, '/');
    }
    BasicTool::echoJson(1, "写入成功");
}

/**
 * 退出时删除设备号
 */
function logoutDevice()
{

    global $currentUser;
    //修改用户
    $currentUser->logoutDevice();
    BasicTool::echoJson(1, "退出成功");
}

/**
 * http://www.atyorku.ca/admin/user/userController.php?action=getListOfMsgReceive
 * 获取小纸条
 */
function getListOfMsgReceive()
{

    global $currentUser;
    $arr = $currentUser->getListOfMsgReceive();

    if ($arr) {
        BasicTool::echoJson(1, "成功", $arr);
    } else {
        BasicTool::echoJson(0, "没有更多消息了");
    }
}


/**
 * http://www.atyorku.ca/admin/user/userController.php?action=clearBadge
 * 重置badge
 */
function clearBadge()
{
    global $currentUser;
    $currentUser->clearBadge();
    BasicTool::echoJson(1, "成功");

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

    try {
        $username = BasicTool::post('username', '请填写用户名', 80);
        $password = BasicTool::post('password', '请填写密码');
        $currentUser->register($username, $password) or BasicTool::throwException($currentUser->errorMsg);
        $userInfo = $currentUser->login($username, $password) or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1, "注册成功", $userInfo);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * userUpdateHeadImgWithJson - 更新头像
 * [post] http://www.atyorku.ca/admin/user/userController.php?action=userUpdateHeadImgWithJson
 *
 */
function userUpdateHeadImgWithJson()
{
    global $currentUser;
    try {
        $imgDir = $currentUser->uploadImg("file", $currentUser->userId,80000,200) or BasicTool::throwException($currentUser->errorMsg);
        $arr = ["img" => $imgDir];
        $oldImg = $currentUser->getRowById("user", $currentUser->userId);
        $oldImg = $oldImg["img"];
        $currentUser->updateRowById("user", $currentUser->userId, $arr) or unlink($_SERVER["DOCUMENT_ROOT"] . $imgDir);
        if (stripos($oldImg, "default") == false) {
            unlink($_SERVER["DOCUMENT_ROOT"] . $oldImg);
        }
        $userArray = $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1, $imgDir,$userArray);
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

    try{
        $val = BasicTool::post('alias','请输入新的别名',36);
        $currentUser->updateAlias($val) or BasicTool::throwException($currentUser->errorMsg);
        $userArray = $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1,"修改成功",$userArray);
    } catch(Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
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

    try{
        $val = BasicTool::post('gender','输入性别',1);
        $currentUser->updateGender($val) or BasicTool::throwException($currentUser->errorMsg);
        $userArray = $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1,"修改成功",$userArray);
    }
    catch(Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
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

    try{
        $val = BasicTool::post('major',false,30);
        $currentUser->updateMajor($val) or BasicTool::throwException($currentUser->errorMsg);
        $userArray = $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1,"修改成功",$userArray);
    }
    catch(Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
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

    try{
        $val = BasicTool::post('wechat',false,40);
        $currentUser->updateWechat($val) or BasicTool::throwException($currentUser->errorMsg);
        $userArray = $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1,"修改成功",$userArray);
    }
    catch(Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
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

    try{
        $val = BasicTool::post('description',false,90);
        $currentUser->updateDescription($val) or BasicTool::throwException($currentUser->errorMsg);
        $userArray = $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1,"修改成功",$userArray);
    }
    catch(Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
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

    try{
        $val = BasicTool::post('enrollYear',false,90);
        $currentUser->updateEnrollYear($val) or BasicTool::throwException($currentUser->errorMsg);
        $userArray = $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1,"修改成功",$userArray);
    }
    catch(Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
    }
}

/**
 * updateDegreeWithJson - 入学年份
 * [post] http://www.atyorku.ca/admin/user/userController.php?action=updateDegreeWithJson
 * @param description : string
 * return json
 */
function updateDegreeWithJson() {

    global $currentUser;

    try{
        $val = BasicTool::post('degree',false,60);
        $currentUser->updateDegree($val) or BasicTool::throwException($currentUser->errorMsg);
        $userArray = $currentUser->updateCookie() or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1,"修改成功",$userArray);
    }
    catch(Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
    }
}

/**
 * updatePasswordWithJson - 更改密码
 * [post] http://www.atyorku.ca/admin/user/userController.php?action=updatePasswordWithJson
 * @param oldPassword : string
 * @param newPassword : string
 * return json
 */

function updatePasswordWithJson() {

    global $currentUser;

    try{
        $oldPassword = BasicTool::post('oldPassword',false);
        $newPassword = BasicTool::post('newPassword',false);
        $currentUser->login($currentUser->userName,$oldPassword) or BasicTool::throwException('原密码错误');
        $currentUser->updatePassword(md5($newPassword)) or BasicTool::throwException($currentUser->errorMsg);
        BasicTool::echoJson(1,"密码修改成功");
    }
    catch(Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
    }
}


?>