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
				<li><a href="/admin/adCategory/index.php" target="mainFrame">广告管理</a></li>
                <li><a href ="/admin/timetable/index.php" target="mainFrame">课程表</a></li>
				<li><a href ="/apps/event/event/index.php" target="mainFrame">活动管理</a></li>
                <li><a href ="/admin/knowledge/index.php" target="mainFrame">考试回忆录</a></li>
				<li><a href="/admin/forum/index.php" target="mainFrame">同学圈</a></li>
				<li><a href="/admin/guide/index.php" target="mainFrame">文章</a></li>
				<li><a href="/admin/book/index.php?s=listBook" target="mainFrame">二手书</a ></li>
				<li><a href="/admin/course/index.php" target="mainFrame">课评V1</a></li>
                <li><a href="/admin/courseRating/index.php" target="mainFrame">课评V2</a></li>
				<li><a href="/admin/courseQuestion/index.php" target="mainFrame">问答系统</a></li>
                <li><a href="/admin/professor/index.php?s=listProfessor" target="mainFrame">教授管理</a></li>
				<li><a href="/admin/user/index.php?s=listUser" target="mainFrame">用户管理</a></li>
				<li><a href="/admin/map/" target="mainFrame">地图管理</a ></li>
				<li><a href="/admin/courseCode/index.php?s=listCourseCode&parent_id=0" target="mainFrame">科目管理</a ></li>
                <li><a href="/admin/transaction/index.php" target="mainFrame">积分记录</a ></li>
                <li><a href="/admin/comment/index.php" target="mainFrame">评论管理</a></li>
				<li><a href="/admin/msg/index.php" target="mainFrame">小纸条</a></li>
                <li><a href="/admin/taskTransaction/index.php?s=listTaskTransaction" target="mainFrame">成就记录管理</a></li>
                <li><a href="/admin/taskDesign/index.php?s=listTaskDesign" target="mainFrame">成就设计管理</a></li>
                <li><a href="/admin/videoAlbum/index.php?s=listVideoAlbum" target="mainFrame">课程专辑管理</a></li>
			</ul>
		</article>
	</section>
	<section>
		<header class="navTit">系统</header>
		<article class="navCon">
			<ul>
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
