<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$userModel = new \admin\user\UserModel();
if ($userModel->isLogin()&&$userModel->isUserHasAuthority('EVENT_ADMIN')) {BasicTool::jumpTo("/apps/event/event");}
?>
<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta charset="utf-8">
    <title>Admin Login - AtYorkU</title>
    <link href="/admin/resource/css/style.css" rel="stylesheet" type="text/css">
    <script src="/resource/js/jquery-2.1.4.js" type="text/javascript"></script>
    <script src="/admin/resource/js/main.js" type="text/javascript"></script>
    <script>
        $(document).ready(function () {
            //-------------------------------------------------------------------------------------------------------------

            if($("#userName").length>0){inputMessage($("#userName"),"用户名");}
            if($("#password").length>0){inputMessage($("#password"),"密码");}
            //-------------------------------------------------------------------------------------------------------------
            $("#login").click(function () {
                var $errorBox = $(".errorBox").slideUp();
                var username = $("#userName").val();
                var password = $("#password").val();
                $.ajax({
                    url:"/admin/login/loginController.php?action=userLogin",
                    type:"POST",
                    contentType:"application/x-www-form-urlencoded",
                    dataType:"json",
                    data:{'username': username, 'password': password},
                    success:function(json){
                        if (json.code == 1) {
                            window.location='/apps/event/event';
                        }else {
                            $errorBox.slideDown().children("em").html(json.message).siblings("i").click(function () {
                                $(this).parent().slideUp();
                            });
                        }
                    }
                });

            })
            //-------------------------------------------------------------------------------------------------------------
        })
    </script>
</head>

<body class="bodyLogin">
<div class="containerLogin">
    <!-- logo s -->
    <header class="logoBox"></header>
    <!-- logo e -->
    <!-- login form s -->
    <article class="loginFormBox">
        <div class="errorBox"><em></em><i class="icon-font icon-font-close"></i></div>
        <div><input id="userName" class="input" type="text" value=""></div>
        <div><input id="password" class="input" type="password" value=""></div>
        <div><input id="login" class="button" type="button" value="登录"></div>
    </article>
    <!-- login form e -->
</div>
</body>
</html>