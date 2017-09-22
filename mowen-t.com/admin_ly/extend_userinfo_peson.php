<?  include_once("./_head_BF.php"); ?>
<?
	$id=$_GET['id'];
	$row = $db->fetch_array($db->select("extend_userinfo","*","id='$id'"));
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>无标题文档</title>
<link href="../css/register.css" rel="stylesheet" type="text/css">
<style>
.fm dl dd {font-size:16px; line-height:30px;color:#fff;font-family:"微软雅黑";}
</style>
</head>


<body>
<div id="container">
  <form method="post" name="regform" onsubmit="return validate_form(this);">
    <div class="fmBoxTit">编号:<? echo $row['id']?></div>
    <div class="fm imgListFm fmBoxCon">
      <div class="dlBox">
        <div id="errorinfo"></div>
        <dl>
          <dt>昵称：</dt>
          <dd>
            <? echo $row['nickname']?>
          </dd>
        </dl>
        <dl>
          <dt>性别：</dt>
          <dd class="spanDl">
            <? echo $row['gender']?>
          </dd>
        </dl>
        <dl>
          <dt>群内职业：</dt>
          <dd>
            <? echo $row['work']?>
          </dd>
        </dl>
        <dl>
          <dt>真实姓名：</dt>
          <dd>
            <? echo $row['name']?>
          </dd>
        </dl>
        <dl>
          <dt>手机：</dt>
          <dd>
            <? echo $row['phone']?>
          </dd>
        </dl>
        <dl>
          <dt>固话：</dt>
          <dd>
            <? echo $row['call']?>
          </dd>
        </dl>
        <dl>
          <dt>QQ：</dt>
          <dd>
            <? echo $row['qq']?>
          </dd>
        </dl>
        <dl>
          <dt>出生日期：</dt>
          <dd><? echo $row['date']?></dd>
        </dl>
        <dl>
          <dt>住址：</dt>
          <dd>
            <? echo $row['dress']?>
          </dd>
        </dl>
        <dl>
          <dt>个人网站：</dt>
          <dd>
            <a href="<? echo $row['page']?>" target="_blank"><? echo $row['page']?></a>
          </dd>
        </dl>
    </div>
    </div>
  </form>
</div>
</body>
</html>
