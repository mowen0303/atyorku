<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$userModel = new \admin\user\UserModel();
if(!$userModel->isAdminLogin()){
	$userModel->logout();
	BasicTool::jumpTo('/admin/login/','parent');
	die("非法登录");
}

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Login - AtYorkU</title>
    <link href="../resource/css/style.css" rel="stylesheet" type="text/css">
    <script src="/resource/js/jquery-2.1.4.js" type="text/javascript"></script>
    <script src="../resource/js/main.js" type="text/javascript"></script>
</head>

<body class="bodyLeftNav">
<!-- logo s -->
<header class="logoBox" title="AtYorkU CMS系统"></header>
<!-- logo e -->
<!-- nav s -->
<nav class="navBox">
	<section>
		<header class="navTit"><a href="/admin/welcome.php" target="mainFrame">首页</a></header>
	</section>
	<section>
		<header class="navTit">内容管理系统</header>
		<article class="navCon">
			<ul>
				<li><a href="/admin/forum/" target="mainFrame">新鲜事</a></li>
				<li><a href="/admin/guide/" target="mainFrame">资讯</a></li>
				<li><a href="/admin/course/" target="mainFrame">课评</a></li>
				<li><a href="/admin/user/index.php?s=listUser&isAdmin=0" target="mainFrame">用户</a></li>
				<li><a href="/admin/adCategory/" target="mainFrame">广告</a></li>
				<li><a href="/admin/book/ " target="mainFrame">二手书</a ></li>
				<li><a href="/admin/map/" target="mainFrame">地图</a ></li>
			</ul>
		</article>
	</section>
	<section>
		<header class="navTit">系统</header>
		<article class="navCon">
			<ul>
				<li><a href="/admin/msg/index.php" target="mainFrame">小纸条</a></li>
				<li><a href="/admin/user/index.php?s=listUser&isAdmin=1" target="mainFrame">管理员</a></li>
				<li><a href="/admin/user/index.php?s=listUserClass" target="mainFrame">权限配置</a></li>
				<li><a href="/admin/statistics/index.php?s=listStatistics" target="mainFrame">Dashboard</a></li>
			</ul>
		</article>
	</section>
</nav>
<!-- nav e -->
<footer class="userInfoBox">

	<p><a href="/admin/user/index.php?s=formUser&uid=<?php echo $userModel->userId?>" target="mainFrame"><?php echo $userModel->userName?></a></p>
	<p>权限 : <?php echo $userModel->authorityTitle?> <a href="/admin/login/loginController.php?action=logout" target="mainFrame">注销</a></p>
</footer>
</body>
</html>