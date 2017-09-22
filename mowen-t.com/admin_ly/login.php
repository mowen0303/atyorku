<?

include_once("global_admin.php");
//退出登陆，如果地址栏有out参数时


if(!empty($_GET['out']))
{
	$db->user_logout();
}

//判断是否设置session，如已设置，则跳入main
if($db->user_login_tf() == true)
{
	$db->jump("main.php");
	exit();
}


//点击登陆按钮时，检查用户名密码是否正确
if(!empty($_POST['enter']))
{
	$db->user_login($_POST['name'],$_POST['pw'],$_POST['code']);	
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>登陆-流云后台管理程序</title>
<link href="css/all.css" rel="stylesheet" type="text/css">
<link href="css/login.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<? include_once("../config/congif_jq.php")?>"></script>
<script type="text/javascript" src="js/code.js"></script>
</head>

<body>
<div class="loginBg"> 
  <!--container s -->
  <div id="container">
    <div class="loginLogo" title="流云后台管理系统"></div>
    <!--用户登陆框 S -->
    <form method="post">
      <div class="fm loginFm">
      <? $db->echoMsg("","errorTxt","0"); //系统信息提示 ?>
        <dl>
          <dt>用户名</dt>
          <dd>
            <input type="text" name="name" />
          </dd>
        </dl>
        <dl>
          <dt>密&nbsp;&nbsp;码</dt>
          <dd>
            <input type="password" name="pw" />
          </dd>
        </dl>
        <dl class="code">
          <dt>验证码</dt>
          <dd>
            <input name="code" class="code" type="text" />
            <img class="codeimg" src="_code.php" onClick="randcode();" title="看不清，换一张"></dd>
        </dl>
        <div class="fmBtn">
          <input name="enter" type="submit" class="resBtn" value="登陆" />
        </div>
      </div>
    </form>
    <!--用户登陆框 E --> 
  </div>
  <!--container e --> 
</div>
</body>
</html>