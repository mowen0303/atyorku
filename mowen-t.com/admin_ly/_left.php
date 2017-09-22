<?  include_once("./_head_BF.php"); ?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>流云后台管理程序</title>
<link href="css/all.css" rel="stylesheet" type="text/css">
<link href="css/manage.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<? include_once("../config/congif_jq.php")?>"></script>
<script type="text/javascript" src="js/action.js"></script>
</head>

<body class="leftBody">
<!--左侧框架 s -->
<div id="containerL">
  <div class="userInfo"> 你好, <a href="#">jiyu</a> ,欢迎登陆<br>
    <a href="login.php?out=out" target="_parent">退出</a> </div>
  <!--menu s -->
  <div class="menu"> 
    <!--s -->    
    <div class="menuTit"><span>作品</span></div>
    <div class="menuCon">
      <ul>
        <li><a href="opus_add.php" target="mainFrame">上传新作品</a></li>
        <li><a href="opus_manage.php?type=1&opusclass=0" target="mainFrame">作品管理-全部</a></li>
        <li><a href="opus_manage.php?type=1&opusclass=1" target="mainFrame">作品管理-摄影</a></li>
   		<li><a href="opus_manage.php?type=1&opusclass=2" target="mainFrame">作品管理-修片</a></li>
   		<li><a href="opus_manage.php?type=1&opusclass=3" target="mainFrame">作品管理-设计</a></li>        
        <li><a href="class.php?class=imgclass" target="mainFrame">分类管理</a></li>
        <li><a href="opus_manage.php?type=0" target="mainFrame">草稿箱</a></li>
      </ul>
    </div>
    <!--e -->     
    <!--s -->
    <div class="menuTit"><span>文章</span></div>
    <div class="menuCon">
      <ul>
        <li><a href="news_add.php" target="mainFrame">添加新文章</a></li>
        <li><a href="class.php?class=newsclass" target="mainFrame">分类管理</a></li>
        <li><a href="news_manage.php" target="mainFrame">全部分类</a></li>
        <?
        $query_class = $db->select("newsclass","*");	
		while($row_class = $db->fetch_array($query_class))
		{
    	?>
      	<li><a href="news_manage.php?class=<? echo $row_class['id'] ?>" target="mainFrame">文章管理 - <? echo $row_class['title']?></a></li>
      	<?
		 }	
		?>
        
      </ul>
    </div>
    <!--e --> 
    <!--s -->
    <div class="menuTit"><span>留言</span></div>
    <div class="menuCon">
      <ul>
        <li><a href="#" target="mainFrame">留言管理</a></li>
      </ul>
    </div>
    <!--e --> 
    <!--s -->
    <div class="menuTit"><span>扩展</span></div>
    <div class="menuCon">
      <ul>
        <li><a href="extend_userinfo.php" target="mainFrame">报名表</a></li>
      </ul>
    </div>
    <!--e -->
    <!--s -->
    <div class="menuTit"><span>用户</span></div>
    <div class="menuCon">
      <ul>        
        <li><a href="admin_pw.php" target="mainFrame">修改密码</a></li>   
        <li><a href="admin_info.php" target="mainFrame">用户信息设置</a></li>     
        <?
        	if($db->authority('2'))
			{			
		?>
        <li><a href="admin_manage.php" target="mainFrame">管理后台用户</a></li>
        <li><a href="admin_add.php" target="mainFrame">添加后台用户</a></li>        
        <?
        	}
		?>
      </ul>
    </div>
    <!--e --> 
    <!--s -->
    <div class="menuTit"><span>设置</span></div>
    <div class="menuCon">
      <ul>
        <li><a href="#" target="mainFrame">网站信息设置</a></li>
      </ul>
    </div>
    <!--e --> 
  </div>
  <!--menu e --> 
</div>
<!--左侧框架 e -->
</body>
</html>
