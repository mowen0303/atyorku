<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$userModel = new \admin\user\UserModel();
if($userModel->isLogin()){
    if(!($userModel->isUserHasAuthority('EVENT_ADMIN')||$userModel->isUserHasAuthority('EVENT_ADMIN'))){
        $userModel->logout();
        BasicTool::echoMessage("无权限进入此页面");
    }
}else{
    BasicTool::jumpTo('/apps/login.php');
    die("没登录");
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Login - AtYorkU</title>
    <link href="/admin/resource/css/style.css" rel="stylesheet" type="text/css">
    <script src="/resource/js/jquery-2.1.4.js" type="text/javascript"></script>
    <script src="/admin/resource/js/main.js" type="text/javascript"></script>
    <script src="/admin/resource/js/component.js" type="text/javascript"></script>

</head>

<body class="bodyMain">
