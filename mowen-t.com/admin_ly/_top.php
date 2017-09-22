<?
include_once("global_admin.php");
$db->user_login_check();
?>
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

<body>
<div id="containerAll"> 
  <!--左侧框架 s -->
  <div id="containerL">
    <div class="userInfo"> 你好, <a href="#"><? echo $_SESSION['uname'] ?></a> ,欢迎登陆<br>
      <a href="login.php?out=out">退出</a> </div>
    <!--menu s -->
    <div class="menu"> 
      <!--s -->
      <div class="menuTit"><span>摄影作品</span></div>
      <div class="menuCon">
        <ul>
          <li><a href="#">上传新作品</a></li>
          <li><a href="#">作品管理</a></li>
          <li><a href="#">作品分类管理</a></li>
        </ul>
      </div>
      <!--e --> 
      <!--s -->
      <div class="menuTit"><span>文章</span></div>
      <div class="menuCon">
        <ul>
          <li><a href="#">添加新文章</a></li>
          <li><a href="#">文章管理</a></li>
          <li><a href="#">文章分类管理</a></li>
        </ul>
      </div>
      <!--e --> 
      <!--s -->
      <div class="menuTit"><span>留言</span></div>
      <div class="menuCon">
        <ul>
          <li><a href="#">留言管理</a></li>
        </ul>
      </div>
      <!--e --> 
      <!--s -->
      <div class="menuTit"><span>用户</span></div>
      <div class="menuCon">
        <ul>
          <li><a href="#">用户信息设置</a></li>
          <li><a href="#">修改密码</a></li>
          <li><a href="#">添加新用户</a></li>
        </ul>
      </div>
      <!--e --> 
      <!--s -->
      <div class="menuTit"><span>设置</span></div>
      <div class="menuCon">
        <ul>
          <li><a href="#">网站信息设置</a></li>
        </ul>
      </div>
      <!--e --> 
    </div>
    <!--menu e --> 
  </div>
  <!--左侧框架 e --> 
  <!--右侧框架 s -->
  <div id="containerR">
    <div id="containerRin"> 
      <!--conBox s -->
      <div class="conBox">