<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";

$action = BasicTool::get('action');
$userModel = new \admin\user\UserModel();



//自动根据action的值调用函数
call_user_func(BasicTool::get('action'));

/**
 * adminLogin - 管理员登陆
 * [post]：http://www.atyorku.ca/admin/login/loginController.php?action=adminLogin
 * @param $username : string
 * @param $password : string
 * @return json
 */
function adminLogin(){
    login("admin");
}

/**
 * userLogin - 普通用户登录
 * [post]：http://www.atyorku.ca/admin/login/loginController.php?action=userLogin
 * @param $username : string
 * @param $password : string
 * @return json
 */
function userLogin() {
    login("user");
}

/**
 * logoutWithJson - 登出
 * [post]：http://www.atyorku.ca/admin/login/loginController.php?action=logoutWithJson
 * @return json
 */
function logoutWithJson(){
    global $userModel;
    $userModel->logout();
    BasicTool::echoJson(1,"已经退出");
}



function login($type) {

    global $userModel;

    try {
        $username = BasicTool::post('username', '请填写用户名');
        $password = BasicTool::post('password', '请填写密码');
        $userArray = $userModel->login($username, $password,$type) or BasicTool::throwException($userModel->errorMsg);
        BasicTool::echoJson(1, '登录成功',$userArray);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

//
function logout(){
    global $userModel;
    $userModel->logout();
    BasicTool::jumpTo('/admin/login/','parent');
    die("非法登录");
}




?>